<?php
// Use real Symfony dependencies; isolate all Guardian state from the host project.
$autoload=$argv[1]??__DIR__.'/../vendor/autoload.php';require $autoload;
spl_autoload_register(function($c){$prefix='OmegaGuardian\\ProfilerShield\\';if(strpos($c,$prefix)===0){$p=__DIR__.'/../src/'.str_replace('\\','/',substr($c,strlen($prefix))).'.php';if(is_file($p))require $p;}},true,true);
use OmegaGuardian\ProfilerShield\Storage\FileStore;
use OmegaGuardian\ProfilerShield\Security\Settings;
use OmegaGuardian\ProfilerShield\Security\Policy;
use OmegaGuardian\ProfilerShield\Security\Sessions;
use OmegaGuardian\ProfilerShield\Security\PublicErrorRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
$_SERVER['GUARDIAN_ENCRYPTION_KEY']=bin2hex(random_bytes(32));unset($_SERVER['GUARDIAN_STORAGE_DIR'],$_ENV['GUARDIAN_STORAGE_DIR']);putenv('GUARDIAN_STORAGE_DIR');
$dir=sys_get_temp_dir().'/guardian-matrix-'.bin2hex(random_bytes(8));$store=new FileStore($dir);$store->initialize();$cfg=new Settings($store);$policy=new Policy($cfg,$store);$sessions=new Sessions($store,$cfg);$n=0;$fail=[];$coverage=[];
function check($ok,$label){global $n,$fail;$n++;if(!$ok)$fail[]=$label;}
function reject($data,$label){global $cfg;$before=$cfg->all();try{$cfg->set($data);check(false,$label);}catch(InvalidArgumentException $e){check($cfg->all()===$before,$label.' atomic');}catch(Throwable $e){check(false,$label.' unexpected '.get_class($e));}}
function resetCfg(){global $cfg;$cfg->set(Settings::defaults());}
foreach(Settings::defaults() as $key=>$value){$coverage[]=$key;resetCfg();
 if(is_bool($value)){foreach([true,false] as $v){$cfg->set([$key=>$v]);check($cfg->all()[$key]===$v,$key.' bool');}foreach([0,1,'1',null,[]] as $v)reject([$key=>$v],$key.' type');}
 elseif(is_int($value)){foreach([1,720] as $v){$cfg->set([$key=>$v]);check($cfg->all()[$key]===$v,$key.' boundary');}foreach([0,-1,721,1.5,'2',null,[]] as $v)reject([$key=>$v],$key.' reject');}
 elseif($key==='login_rules'||$key==='password_policy'){foreach([null,[],true,1,'bad'] as $v)reject([$key=>$v],$key.' invalid structure');}
 elseif($key!=='weekdays'){foreach([null,[],true,1,str_repeat('a',513)] as $v)reject([$key=>$v],$key.' type');}
}
foreach(['preset'=>['solo','team','strict'],'audit_level'=>['minimal','standard','advanced'],'error_mode'=>['native','generic','custom'],'error_reference'=>['none','random','custom']] as $key=>$values){foreach($values as $v){resetCfg();$cfg->set([$key=>$v]);check($cfg->all()[$key]===$v,$key.' enum');}reject([$key=>'invalid'],$key.' bad enum');}
foreach(['solo','team','strict'] as $v){resetCfg();$cfg->preset($v);check($cfg->all()['preset']===$v,'preset '.$v);}reject(['unknown'=>1],'unknown key');
foreach(['UTC','Europe/Paris','Asia/Kathmandu','Pacific/Chatham','America/New_York'] as $v){resetCfg();$cfg->set(['timezone'=>$v]);check($cfg->all()['timezone']===$v,'timezone '.$v);}reject(['timezone'=>'Mars/Unknown'],'timezone invalid');
foreach(['start','end'] as $k){foreach(['00:00','23:59','09:30'] as $v){resetCfg();$cfg->set([$k=>$v]);check($cfg->all()[$k]===$v,$k.' time');}foreach(['24:00','9:00','12:60','12:00:00',"09:00\n"] as $v)reject([$k=>$v],$k.' invalid time');}
foreach([[],[0],[8],[1.5],[true],['1x'],[[]]] as $days){resetCfg();if($days===[]){$cfg->set(['weekdays'=>[]]);check($cfg->all()['weekdays']===[],'empty weekdays allowed while disabled');}else reject(['weekdays'=>$days],'invalid weekdays '.json_encode($days));}
resetCfg();$cfg->set(['weekdays'=>[1,1,7]]);check($cfg->all()['weekdays']===[1,7],'days unique');
reject(['schedule_enabled'=>true,'weekdays'=>[]],'enabled empty days');reject(['schedule_enabled'=>true,'start'=>'18:00','end'=>'09:00'],'overnight rejected');reject(['schedule_enabled'=>true,'start'=>'09:00','end'=>'09:00'],'zero interval');reject(['portal_closed_outside_hours'=>true,'emergency_enabled'=>true],'portal emergency conflict');
foreach(['','192.0.2.1','192.0.2.0/24','0.0.0.0/0','2001:db8::/32','::/0','192.0.2.0/24, 2001:db8::/32'] as $v){resetCfg();$cfg->set(['ip_allowlist'=>$v]);check(true,'CIDR valid');}
foreach(['nope','192.0.2.1/33','::/129','192.0.2.0/-1','192.0.2.0/','::/x'] as $v)reject(['ip_allowlist'=>$v],'CIDR bad');
foreach(['error_reference_text'=>60,'error_custom_text'=>360] as $k=>$max){resetCfg();$cfg->set([$k=>str_repeat('a',$max)]);check(true,$k.' max');reject([$k=>str_repeat('a',$max+1)],$k.' too long');reject([$k=>"bad\0text"],$k.' nul');}
resetCfg();$cfg->set(['alert_email'=>'security@example.org']);check(true,'email valid');reject(['alert_email'=>'not-email'],'email invalid');
// All day subsets, all weekdays and exact window edges.
for($mask=1;$mask<128;$mask++){resetCfg();$days=[];for($d=1;$d<=7;$d++)if($mask&(1<<($d-1)))$days[]=$d;$cfg->set(['schedule_enabled'=>true,'timezone'=>'UTC','weekdays'=>$days]);for($d=1;$d<=7;$d++)foreach(['08:59:59'=>false,'09:00:00'=>true,'17:59:59'=>true,'18:00:00'=>false] as $clock=>$inside){$ts=strtotime('2026-10-'.(18+$d).' '.$clock.' UTC');check($policy->inSchedule($ts)===($inside&&in_array($d,$days,true)),'schedule subset '.$mask.'/'.$d.'/'.$clock);}}
resetCfg();$cfg->set(['schedule_enabled'=>true,'timezone'=>'Europe/Paris','weekdays'=>[7],'start'=>'02:00','end'=>'03:00']);
foreach(['2026-10-25 00:30:00 UTC'=>true,'2026-10-25 01:30:00 UTC'=>true,'2026-03-29 00:59:00 UTC'=>false,'2026-03-29 01:00:00 UTC'=>false] as $instant=>$expected)check($policy->inSchedule(strtotime($instant))===$expected,'DST '.$instant);
// Authorization cross product: role, disabled, MFA, schedule, JIT, emergency, lockdown.
resetCfg();$now=strtotime('2026-10-26 20:00:00 UTC');
foreach(['admin','developer','security','invalid'] as $role)foreach([false,true] as $disabled)foreach([false,true] as $mfa)foreach([false,true] as $enrolled)foreach([false,true] as $schedule)foreach([false,true] as $jit)foreach([false,true] as $emergency)foreach([false,true] as $lock){$cfg->set(['mfa'=>$mfa,'schedule_enabled'=>$schedule,'timezone'=>'UTC','jit_enabled'=>$jit]);$store->change('control',function()use($lock){return ['lockdown'=>$lock];});$account=['role'=>$role,'disabled'=>$disabled,'totp'=>$enrolled?'enrolled':''];$session=['jit_until'=>0,'emergency_until'=>$emergency?$now+60:0];$expected=in_array($role,['admin','developer'],true)&&!$disabled&&(!$mfa||$enrolled)&&!$lock&&(!$schedule||$emergency)&&(!$jit||$emergency);check($policy->allowed($session,$account,$now)===$expected,'authorization product');}
resetCfg();foreach([['192.0.2.128/25','192.0.2.127',false],['192.0.2.128/25','192.0.2.128',true],['2001:db8::/33','2001:db8:8000::1',false],['2001:db8::/33','2001:db8:7fff::1',true],['::/0','192.0.2.1',false]] as $c){$cfg->set(['ip_allowlist'=>$c[0]]);check($policy->ipAllowed($c[1])===$c[2],'CIDR bit boundary');}
resetCfg();(new \OmegaGuardian\ProfilerShield\Security\Accounts($store))->add('alice','Synthetic fixture secret42!','developer');$resp=new Response();$sessions->begin('alice',$resp,true);$cookies=[];foreach($resp->headers->getCookies() as $cookie){check($cookie->isSecure()&&$cookie->isHttpOnly()&&$cookie->getPath()==='/'&&$cookie->getDomain()===null,'secure cookie');$cookies[$cookie->getName()]=$cookie->getValue();}$req=Request::create('https://localhost/','GET',[],$cookies);check($sessions->current($req)!==null,'session valid');check($sessions->recognized($req),'device recognized');$state=$sessions->current($req);check($sessions->csrf($state,$state['csrf']),'CSRF valid');check(!$sessions->csrf($state,'wrong'),'CSRF invalid');
$id=hash('sha256',$cookies[Sessions::COOKIE]);$store->change('sessions',function($all)use($id){$all[$id]['created']=time()-7200;return $all;});$cfg->set(['absolute_hours'=>1]);check($sessions->current($req)===null,'shortened absolute lifetime revokes existing session');
resetCfg();$sessions->invalidateUser('alice');check($sessions->current($req)===null,'revoked session');
$renderer=new PublicErrorRenderer($cfg,sys_get_temp_dir());foreach(['native','generic','custom'] as $mode)foreach(['none','random','custom'] as $ref)foreach([400,401,403,404,405,429,500,503] as $status){$c=Settings::defaults();$c=array_replace($c,['error_mode'=>$mode,'error_reference'=>$ref,'error_custom_text'=>'<script>alert(1)</script>','error_reference_text'=>'<img src=x>']);$r=$renderer->neutral($status,$c);check($r->getStatusCode()===$status,'status preserved');check(strpos($r->getContent(),'<script>')===false&&strpos($r->getContent(),'<img src=x>')===false,'HTML escaped');check(strpos($r->headers->get('Cache-Control'),'no-store')!==false,'no-store error');}
foreach(['settings','sessions'] as $collection){$path=$dir.'/'.$collection.'.json';$old=file_get_contents($path);file_put_contents($path,'corrupt');try{$store->read($collection);check(false,'corrupt rejected');}catch(RuntimeException $e){check(true,'corrupt rejected');}file_put_contents($path,$old);}
echo json_encode(['assertions'=>$n,'failures'=>$fail,'settings_covered'=>$coverage],JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE),"\n";
foreach(glob($dir.'/*') as $p)unlink($p);@unlink($dir.'/.htaccess');rmdir($dir);exit($fail?1:0);
