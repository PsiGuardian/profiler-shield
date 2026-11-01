<?php
ob_start();
require $argv[1]??__DIR__.'/../vendor/autoload.php';
spl_autoload_register(function($class){$p='OmegaGuardian\\ProfilerShield\\';if(strpos($class,$p)===0){$file=__DIR__.'/../src/'.str_replace('\\','/',substr($class,strlen($p))).'.php';if(is_file($file))require $file;}},true,true);
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\{Request,Response};
use OmegaGuardian\ProfilerShield\Storage\FileStore;
use OmegaGuardian\ProfilerShield\Security\{Settings,Accounts};
final class GuardianReleaseFixtureController {
    public static function business(){return new Response('<!doctype html><html><body><h1>Business fixture</h1></body></html>',200,['Content-Type'=>'text/html']);}
    public static function failure(){throw new RuntimeException('GUARDIAN_PRIVATE_EXCEPTION_SENTINEL');}
}
final class GuardianReleaseKernel extends Symfony\Component\HttpKernel\Kernel {
    private $root;
    public function __construct($root){$this->root=$root;parent::__construct('dev',true);}
    public function getProjectDir():string{return $this->root;}
    public function getCacheDir():string{return $this->root.'/cache';}
    public function getLogDir():string{return $this->root.'/log';}
    public function registerBundles():iterable{return [new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),new Symfony\Bundle\TwigBundle\TwigBundle(),new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle(),new OmegaGuardian\ProfilerShield\OmegaGuardianProfilerShieldBundle()];}
    public function registerContainerConfiguration(LoaderInterface $loader):void {
        $loader->load(function(ContainerBuilder $c){
            $c->loadFromExtension('framework',['secret'=>'release-fixture-not-a-production-secret','router'=>['resource'=>__DIR__.'/fixtures/routes.php'],'profiler'=>['collect'=>false],'session'=>['storage_factory_id'=>'session.storage.factory.mock_file']]);
            $c->loadFromExtension('twig',['default_path'=>$this->root.'/templates']);
            $c->loadFromExtension('web_profiler',['toolbar'=>true,'intercept_redirects'=>false]);
        });
    }
}
$root=sys_get_temp_dir().'/guardian-release-kernel-'.bin2hex(random_bytes(8));mkdir($root,0700);mkdir($root.'/templates');
foreach(['GUARDIAN_STORAGE_DIR'=>$root.'/vault','GUARDIAN_ENCRYPTION_KEY'=>bin2hex(random_bytes(32)),'GUARDIAN_LOGIN_PATH'=>'/internal/release-fixture-'.bin2hex(random_bytes(16)),'GUARDIAN_COLLECTION_BRIDGE'=>'1','GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED'=>'1','GUARDIAN_SHOW_PHP_VERSION'=>'0'] as $k=>$v){$_SERVER[$k]=$_ENV[$k]=$v;putenv($k.'='.$v);}
$s=new FileStore($root.'/vault');$s->initialize();$cfg=new Settings($s);$a=new Accounts($s);$password=bin2hex(random_bytes(24));$a->add('release-admin',$password);$kernel=new GuardianReleaseKernel($root);$cookies=[];$checks=0;$failures=[];
function verify($ok,$message){global $checks,$failures;$checks++;if(!$ok)$failures[]=$message;}
function request($url,$method='GET',$data=[]){global $kernel,$cookies;$req=Request::create('https://localhost'.$url,$method,$data,$cookies,[],['REMOTE_ADDR'=>'127.0.0.1','HTTPS'=>'on']);$res=$kernel->handle($req);foreach($res->headers->getCookies() as $c)$cookies[$c->getName()]=$c->getValue();$kernel->terminate($req,$res);return $res;}
try {
 foreach(['/_profiler/','/_wdt/missing','/%5fprofiler/','/%255fprofiler/','/index.php/_profiler/'] as $url)verify(request($url)->getStatusCode()===404,'anonymous refused '.$url);
 $r=request('/business');verify($r->getStatusCode()===200,'business available');verify(!$r->headers->has('X-Debug-Token'),'anonymous no debug token');verify(strpos($r->getContent(),'sf-toolbar')===false,'anonymous no toolbar');
 $r=request('/failure');verify($r->getStatusCode()===500,'exception status');verify(strpos($r->getContent(),'GUARDIAN_PRIVATE_EXCEPTION_SENTINEL')===false,'exception private');
 $r=request('/business','POST');verify($r->getStatusCode()===405&&strpos($r->headers->get('Allow',''),'GET')!==false,'405 Allow preserved');
 $path=$_SERVER['GUARDIAN_LOGIN_PATH'];$r=request($path);verify($r->getStatusCode()===200,'login form');preg_match('/name="_csrf" value="([^"]+)"/',$r->getContent(),$m);
 $r=request($path,'POST',['action'=>'login','_csrf'=>$m[1],'username'=>'release-admin','password'=>$password]);verify($r->getStatusCode()===303,'login success');
 $r=request('/_profiler/');for($i=0;$i<5&&$r->isRedirection();$i++)$r=request($r->headers->get('Location'));verify($r->getStatusCode()===200,'authorized profiler');
 $r=request('/business');verify($r->headers->has('X-Debug-Token'),'authorized collection');
 $r=request($path.'?view=settings&lang=fr_FR');verify($r->getStatusCode()===200&&strpos($r->getContent(),'error-preview')!==false,'settings UI');
 foreach (OmegaGuardian\ProfilerShield\I18n\UiTranslator::AVAILABLE as $locale=>$label) {
  foreach (['home','settings','guide','audit','support'] as $view) {
   $r=request($path.'?view='.$view.'&lang='.$locale);
   verify($r->getStatusCode()===200,'localized view '.$locale.'/'.$view);
   if ($view==='settings') {
    verify(!str_contains($r->getContent(),'IDENTITY / GUARDIAN'), 'identity heading translated '.$locale);
    $catalog=require dirname(__DIR__).'/translations/guardian.'.substr($locale,0,2).'.php';
    verify(str_contains($r->getContent(),'class="section-kicker">'.htmlspecialchars($catalog['config.section'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'</span>'), 'localized security heading '.$locale);
   }
   if ($locale==='fr_FR') {verify(!preg_match('/QUICK SETTINGS|ACCESS &amp; IDENTITY|>admin<\/small>/', $r->getContent()), 'French labels and account role');}
   verify(strpos($r->getContent(),'lang="'.str_replace('_','-',$locale).'"')!==false && !preg_match('/(?:legacy\.[a-f0-9]{12}|security\.[a-z_]+)/',$r->getContent()),'localized markup '.$locale.'/'.$view);
  }
 }
 $cfg->set(['ip_allowlist'=>'192.0.2.0/24']);verify(request('/_profiler/')->getStatusCode()===404,'live network restriction');$cfg->set(['ip_allowlist'=>'']);
 $s->change('control',function(){return ['lockdown'=>true];});verify(request('/_profiler/')->getStatusCode()===404,'live lockdown');verify(request('/business')->getStatusCode()===200,'lockdown business independent');
} finally {
 $kernel->shutdown();$it=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root,FilesystemIterator::SKIP_DOTS),RecursiveIteratorIterator::CHILD_FIRST);foreach($it as $f){$f->isDir()?rmdir($f->getPathname()):unlink($f->getPathname());}rmdir($root);
}
echo json_encode(['checks'=>$checks,'failures'=>$failures],JSON_PRETTY_PRINT),"\n";exit($failures?1:0);
