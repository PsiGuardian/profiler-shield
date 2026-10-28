<?php
require $argv[1];spl_autoload_register(function($c){$p='OmegaGuardian\\ProfilerShield\\';if(strpos($c,$p)===0)require __DIR__.'/../src/'.str_replace('\\','/',substr($c,strlen($p))).'.php';},true,true);
use OmegaGuardian\ProfilerShield\Storage\FileStore;
use OmegaGuardian\ProfilerShield\Security\{Settings,Sessions,Accounts,Policy,DiagnosticAccess,PublicErrorRenderer};
use OmegaGuardian\ProfilerShield\EventListener\PrivacySubscriber;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Request;
$dir=sys_get_temp_dir().'/guardian-headers-'.bin2hex(random_bytes(6));$_SERVER['GUARDIAN_ENCRYPTION_KEY']=bin2hex(random_bytes(32));$s=new FileStore($dir);$s->initialize();$cfg=new Settings($s);$a=new Accounts($s);$sess=new Sessions($s,$cfg);$pol=new Policy($cfg,$s);$access=new DiagnosticAccess($sess,$a,$pol);$sub=new PrivacySubscriber($sess,$a,$pol,$access,new PublicErrorRenderer($cfg,sys_get_temp_dir()));
$kernel=new class implements Symfony\Component\HttpKernel\HttpKernelInterface {public function handle(Request $request,int $type=1,bool $catch=true):Symfony\Component\HttpFoundation\Response{return new Symfony\Component\HttpFoundation\Response();}};
$n=0;$bad=[];
foreach(['text/html','application/json'] as $accept)foreach([401=>'WWW-Authenticate',405=>'Allow',429=>'Retry-After',503=>'Retry-After'] as $status=>$header){$req=Request::create('https://localhost/error');$req->headers->set('Accept',$accept);$val=$status===401?'Basic realm="test"':($status===405?'GET, POST':'120');$e=new ExceptionEvent($kernel,$req,1,new HttpException($status,'sensitive exception',null,['X-Debug-Exception'=>'secret',$header=>$val]));$sub->onException($e);$r=$e->getResponse();foreach([$r->getStatusCode()===$status,$r->headers->get($header)===$val,$r->headers->get('X-Debug-Exception')===null,strpos($r->getContent(),'sensitive exception')===false] as $ok){$n++;if(!$ok)$bad[]=$accept.'/'.$status.'/'.$header;}}
foreach(glob($dir.'/*') as $f)unlink($f);@unlink($dir.'/.htaccess');rmdir($dir);echo json_encode(['assertions'=>$n,'failures'=>$bad],JSON_PRETTY_PRINT),"\n";exit($bad?1:0);
