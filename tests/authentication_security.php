<?php
require $argv[1]??__DIR__.'/../vendor/autoload.php';
spl_autoload_register(function($c){$p='OmegaGuardian\\ProfilerShield\\';if(str_starts_with($c,$p)){require __DIR__.'/../src/'.str_replace('\\','/',substr($c,strlen($p))).'.php';}},true,true);
use OmegaGuardian\ProfilerShield\Security\{Accounts,Settings,LoginThrottle,LoginRateLimited,PasswordPolicy,Sessions,Policy};
use OmegaGuardian\ProfilerShield\Storage\FileStore;
use Symfony\Component\HttpFoundation\{Request,Response};
if (($argv[2]??'')==='--worker') {
    $shared=new FileStore($argv[3]);$guard=new LoginThrottle($shared,new Settings($shared));
    try {$ticket=$guard->begin('parallel','127.0.0.1');usleep(50000);$guard->finish($ticket,false);exit(0);} catch(LoginRateLimited $e) {exit(2);}
}
$_SERVER['GUARDIAN_ENCRYPTION_KEY']=bin2hex(random_bytes(32));
putenv('GUARDIAN_ENCRYPTION_KEY='.$_SERVER['GUARDIAN_ENCRYPTION_KEY']);
$dir=sys_get_temp_dir().'/guardian-auth-tests-'.bin2hex(random_bytes(8));$store=new FileStore($dir);$store->initialize();$settings=new Settings($store);$now=2000000000;$throttle=new LoginThrottle($store,$settings,function()use(&$now){return $now;});$checks=0;$fail=[];
function check($ok,$label){global $checks,$fail;++$checks;if(!$ok)$fail[]=$label;}
function denied($fn,$label){try{$fn();check(false,$label);}catch(InvalidArgumentException $e){check(true,$label);}}
function resetState(){global $store,$settings,$now;$store->change('attempts',fn()=>[]);$settings->set(['login_rules'=>LoginThrottle::defaults()]);$now=2000000000;}
function failure($user='admin',$ip='127.0.0.1'){global $throttle;check(!$throttle->finish($throttle->begin($user,$ip),false),'failed credential has no authorization');}
function blocked($label){global $throttle;try{$throttle->begin('admin','127.0.0.1');check(false,$label);return 0;}catch(LoginRateLimited $e){check(true,$label);return $e->retryAfter;}}
try {
    for($i=0;$i<10;$i++)failure();check(blocked('default locks at ten failures')===600,'default ten-minute ban');$now+=60;check(blocked('ban remains')===540,'blocked requests never extend ban');
    $before=count($store->read('attempts'));for($i=0;$i<12;$i++){try{$throttle->begin('unknown-'.$i,'127.0.0.1');}catch(LoginRateLimited $e){}}check(count($store->read('attempts'))===$before,'blocked origins cannot grow unknown-user storage');
    resetState();for($i=0;$i<10;$i++)failure('admin','192.0.2.'.($i+1));try{$throttle->begin('admin','198.51.100.20');check(false,'distributed account limit');}catch(LoginRateLimited $e){check($e->retryAfter===600,'distributed account limit');}
    resetState();for($i=0;$i<10;$i++)failure('user'.$i);check(blocked('origin aggregates usernames')===600,'origin lockout duration');
    resetState();for($i=0;$i<10;$i++)failure('ipv6-user'.$i,'2001:db8::1');
    try {$throttle->begin('another-user','2001:0db8:0000:0000:0000:0000:0000:0001');check(false,'IPv6 spelling bypass');}catch(LoginRateLimited $e){check($e->retryAfter===600,'equivalent IPv6 spellings share the same origin counter');}
    resetState();$rules=[['attempts'=>5,'block_minutes'=>10,'window_minutes'=>30,'escalation_minutes'=>30],['attempts'=>5,'block_minutes'=>120,'window_minutes'=>30,'escalation_minutes'=>30]];$settings->set(['login_rules'=>$rules]);
    for($i=0;$i<5;$i++)failure();check(blocked('stage one')===600,'first five failures ten minutes');$now+=600;
    for($i=0;$i<5;$i++)failure();check(blocked('stage two')===7200,'repeat five failures two hours');$now+=7200+1801;
    for($i=0;$i<5;$i++)failure();check(blocked('repeat window expired')===600,'expired repeat resets first stage');
    resetState();for($i=0;$i<9;$i++)failure();$now+=1800;failure();$ticket=$throttle->begin('admin','127.0.0.1');check($throttle->finish($ticket,true),'counting window expires at exact edge');
    resetState();$tickets=[];for($i=0;$i<10;$i++)$tickets[]=$throttle->begin('admin','127.0.0.1');check(blocked('in-flight reservations cap concurrency')===1,'busy reservation short retry');
    foreach($tickets as $t)check(!$throttle->finish($t,false),'concurrent failure');check(blocked('parallel failures create one lockout')===600,'parallel threshold exact');check(!$throttle->finish($tickets[0],true),'reservation cannot be replayed');
    resetState();$t=$throttle->begin('admin','127.0.0.1');$now+=120;check(!$throttle->finish($t,true),'expired reservation cannot authorize');
    resetState();$workers=[];
    for($i=0;$i<32;$i++) {
        $pipes=[];$process=proc_open([PHP_BINARY,__FILE__,$argv[1],'--worker',$dir],[0=>['file','/dev/null','r'],1=>['pipe','w'],2=>['pipe','w']],$pipes);
        if (!is_resource($process)) {throw new RuntimeException('Cannot start concurrent test worker');}$workers[]=[$process,$pipes];
    }
    $admitted=0;$rejected=0;
    foreach($workers as [$process,$pipes]) {$out=stream_get_contents($pipes[1]);$error=stream_get_contents($pipes[2]);fclose($pipes[1]);fclose($pipes[2]);$status=proc_close($process);$admitted+=$status===0;$rejected+=$status===2;check($status===0||$status===2,'concurrent worker completes without storage failure');}
    check($admitted===10 && $rejected===22,'32 real processes admit exactly ten password checks');
    resetState();$full=[];
    for($i=0;$i<10000;$i++) {$full[hash('sha256','capacity-'.$i)]=['level'=>0,'failures'=>0,'pending'=>[],'blocked_until'=>$now+600,'repeat_until'=>$now+2400,'window_started'=>0,'last'=>$now];}
    $store->change('attempts',fn()=>$full);check(blocked('capacity limit fails closed')===60,'live bans are retained at capacity');
    foreach($full as &$entry){$entry['blocked_until']=0;$entry['repeat_until']=0;$entry['last']=$now-2000;}unset($entry);
    $store->change('attempts',fn()=>$full);$t=$throttle->begin('admin','127.0.0.1');check(count($store->read('attempts'))===2,'expired buckets free capacity without evicting live bans');$throttle->finish($t,true);unset($full);
    foreach([[],null,'bad',[['attempts'=>0,'block_minutes'=>10,'window_minutes'=>30,'escalation_minutes'=>30]],[['attempts'=>'10','block_minutes'=>10,'window_minutes'=>30,'escalation_minutes'=>30]]] as $bad){denied(fn()=>LoginThrottle::configuration($bad),'invalid lockout structure');}
    denied(fn()=>LoginThrottle::configuration(array_reverse($rules)),'decreasing block duration refused');
    $p=PasswordPolicy::defaults();foreach(['short','passwordpassword',"long-password\0secret",str_repeat('a',513),"\xff".str_repeat('a',20)] as $bad){denied(fn()=>PasswordPolicy::validate($bad,$p),'invalid secret rejected');}
    PasswordPolicy::validate(str_repeat('é',15),$p);check(true,'Unicode length counts characters');
    foreach(['upper','lower','digit','symbol'] as $class){$q=$p;$q['require_'.$class]=true;$missing=['upper'=>'all lowercase secret','lower'=>'ALL UPPERCASE SECRET','digit'=>'No digits in this secret!','symbol'=>'No symbols in this secret42'];denied(fn()=>PasswordPolicy::validate($missing[$class],$q),'required '.$class);}
    $q=['min_length'=>64,'require_upper'=>true,'require_lower'=>true,'require_digit'=>true,'require_symbol'=>true];$generated=PasswordPolicy::generate($q);PasswordPolicy::validate($generated,$q);check(strlen($generated)===64,'generated secret meets strongest configured policy');
    $accounts=new Accounts($store);$accounts->add('admin','Initial synthetic secret42!', 'admin',true);$sessions=new Sessions($store,$settings);$policy=new Policy($settings,$store);$response=new Response();$sessions->begin('admin',$response);$token=$response->headers->getCookies()[0]->getValue();$request=Request::create('https://localhost/','GET',[],[Sessions::COOKIE=>$token]);$session=$sessions->current($request);
    check(!$policy->allowed($session,$accounts->find('admin')),'temporary password cannot open diagnostics');
    denied(fn()=>$accounts->setPassword('admin','Initial synthetic secret42!'),'initial password cannot be reused');
    $accounts->setPassword('admin','Replacement synthetic secret43!');check(!$accounts->find('admin')['must_change_password'],'rotation clears first-access requirement');check($sessions->current($request)===null,'password version invalidates existing token');check(!$policy->allowed($session,$accounts->find('admin')),'old version denied by shared access policy');check(!$accounts->verify('admin','Initial synthetic secret42!'),'old password no longer valid');
    $r=new Response();$sessions->begin('admin',$r);$newToken=$r->headers->getCookies()[0]->getValue();$req=Request::create('https://localhost/','GET',[],[Sessions::COOKIE=>$newToken]);check($newToken!==$token && $policy->allowed($sessions->current($req),$accounts->find('admin')),'fresh token after rotation opens diagnostics');
    $accounts->forcePasswordChange();check($sessions->current($req)===null && $accounts->find('admin')['must_change_password'],'forced campaign revokes sessions and requires rotation');
    $accounts->update('admin',function($user){unset($user['must_change_password']);return $user;});
    file_put_contents($dir.'/initial-admin-credentials.txt','legacy synthetic initial credentials');
    $r=new Response();$sessions->begin('admin',$r);$legacy=Request::create('https://localhost/','GET',[],[Sessions::COOKIE=>$r->headers->getCookies()[0]->getValue()]);
    check($accounts->needsPasswordChange('admin',$accounts->find('admin')) && !$policy->allowed($sessions->current($legacy),$accounts->find('admin')),'RC7 initial accounts cannot bypass mandatory rotation after upgrade');
    $accounts->setPassword('admin','Another replacement secret44!');check(!is_file($dir.'/initial-admin-credentials.txt'),'rotation removes legacy credential file too');
    denied(fn()=>$settings->set(['password_policy'=>['min_length'=>7]+array_diff_key($p,['min_length'=>1])]),'weak configuration bound');
} finally {
    $it=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir,FilesystemIterator::SKIP_DOTS),RecursiveIteratorIterator::CHILD_FIRST);foreach($it as $f){$f->isDir()?rmdir($f):unlink($f);}rmdir($dir);
}
echo json_encode(['checks'=>$checks,'failures'=>$fail],JSON_PRETTY_PRINT),PHP_EOL;exit($fail?1:0);
