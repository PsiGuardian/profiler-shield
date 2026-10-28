<?php
require $argv[1]??__DIR__.'/../vendor/autoload.php';
spl_autoload_register(function($class){$p='OmegaGuardian\\ProfilerShield\\';if(strpos($class,$p)===0){$file=__DIR__.'/../src/'.str_replace('\\','/',substr($class,strlen($p))).'.php';if(is_file($file))require $file;}},true,true);
use OmegaGuardian\ProfilerShield\Storage\FileStore;
use OmegaGuardian\ProfilerShield\Security\{Settings,Accounts,Sessions,Policy,Audit,Totp};
use OmegaGuardian\ProfilerShield\Controller\GuardianController;
use OmegaGuardian\ProfilerShield\Ui\{SecurityPanel,AuditPanel,LanguagePicker};
use OmegaGuardian\ProfilerShield\I18n\UiTranslator;
use Symfony\Component\HttpFoundation\{Request,Response};
$n=0;function ensure($ok,$label){global $n;if(!$ok)throw new RuntimeException($label);$n++;}
$dir=sys_get_temp_dir().'/guardian-ui-updates-'.bin2hex(random_bytes(8));
$_SERVER['GUARDIAN_ENCRYPTION_KEY']=bin2hex(random_bytes(32));
try {
 $store=new FileStore($dir);$store->initialize();$settings=new Settings($store);$accounts=new Accounts($store);
 $settings->set(['password_policy'=>['min_length'=>8,'require_upper'=>true,'require_lower'=>true,'require_digit'=>true,'require_symbol'=>true]]);
 $accounts->add('fixture-admin','SyntheticLocalFixture42!','admin');$accounts->add('fixture-dev','SyntheticLocalFixture43!','developer');
 $sessions=new Sessions($store,$settings);$response=new Response();$sessions->begin('fixture-admin',$response,false);$cookies=[];foreach($response->headers->getCookies() as $c)$cookies[$c->getName()]=$c->getValue();
 $i18n=new UiTranslator();$controller=new GuardianController($store,$settings,$accounts,$sessions,new Policy($settings,$store),new Audit($store),new Totp(),$i18n);
 $audit=[];for($k=0;$k<160;$k++)$audit[]=['at'=>gmdate('c',1700000000+$k),'event'=>$k%2?'login_failed':'login_success','actor'=>$k%3?'fixture-dev':'fixture-admin'];
 $store->change('audit',static fn()=>$audit);
 foreach(['settings','guide','audit'] as $view){
  $request=Request::create('https://localhost/synthetic-panel?view='.$view.'&lang=fr_FR','GET',[],$cookies,[],['REMOTE_ADDR'=>'127.0.0.1','HTTPS'=>'on']);
  $res=$controller->entry($request);ensure($res->getStatusCode()===200,'view '.$view);
  $html=$res->getContent();ensure(!preg_match('/(?:guide\.|ui\.(?:audit_help|filter|no_results)|security\.(?:hash_|required_policy))/',$html),'no untranslated UI keys '.$view);
  if($view==='settings'){
   ensure(strpos($html,'aria-describedby="creation-policy"')!==false,'creation password linked to saved policy');
   preg_match('~id="creation-policy"(.*?)</ul>~s',$html,$m);
   foreach(['Au moins une majuscule','Au moins une minuscule','Au moins un chiffre','Au moins un symbole'] as $text)ensure(strpos($m[1]??'',$text)!==false,'required badge '.$text);
   $hashHelp=$i18n->trans(defined('PASSWORD_ARGON2ID')?'security.hash_argon':'security.hash_bcrypt','fr_FR');
   ensure(strpos($html,htmlspecialchars($hashHelp,ENT_QUOTES,'UTF-8'))!==false,'actual server hash algorithm explained');
   ensure(strpos($html,'data-language-picker')!==false && strpos($html,'data-account-search')!==false,'language and account filters rendered');
  }
  if($view==='guide')ensure(substr_count($html,'<details>')===14,'14 current guide sections');
  if($view==='audit')ensure(substr_count($html,'class="event"')===50,'journal bounded to 50 events');
  if(isset($argv[2])){if(!is_dir($argv[2]))mkdir($argv[2],0700,true);file_put_contents($argv[2].'/'.$view.'.html',$html);}
 }
 $panel=AuditPanel::render($audit,['event_type'=>'login_failed','actor'=>'fixture-admin'],$i18n,'fr_FR');
 ensure(substr_count($panel,'class="event"')===27,'filters intersect across entire journal');
 $page=AuditPanel::render($audit,['page'=>'2'],$i18n,'fr_FR');ensure(substr_count($page,'class="event"')===50 && strpos($page,'2 / 4')!==false,'second page');
 foreach([['page'=>'999999999999999999999'],['page'=>['bad']],['event_type'=>['bad']]] as $q)ensure(strpos(AuditPanel::render($audit,$q,$i18n,'fr_FR'),'<section')===0,'malformed query handled');
 $xss=AuditPanel::render([['at'=>'<script>bad</script>','event'=>'<img src=x>','actor'=>'" onfocus="bad']],[],$i18n,'fr_FR');
 ensure(strpos($xss,'<script>')===false && strpos($xss,'<img')===false,'journal and filter labels escaped');
 ensure(strpos(AuditPanel::render($audit,['actor'=>'nonexistent'],$i18n,'fr_FR'),'class="event"')===false,'unknown account never broadens filter');
 foreach(UiTranslator::AVAILABLE as $locale=>$unused){
  $picker=LanguagePicker::render($i18n,$locale,'settings');ensure(substr_count($picker,'type="radio"')===28,'28 language choices '.$locale);ensure(substr_count($picker,' checked')===1,'single checked locale '.$locale);
  $cfg=Settings::defaults();$requirements=(new SecurityPanel($i18n))->requirements($cfg,$locale,'test');ensure(strpos($requirements,'security.')===false,'default password requirements '.$locale);
 }
 echo json_encode(['checks'=>$n,'failures'=>[]],JSON_PRETTY_PRINT),"\n";
}finally{
 if(is_dir($dir)){foreach(glob($dir.'/{*,.*}',GLOB_BRACE) as $p){if(is_file($p))unlink($p);}rmdir($dir);}
}
