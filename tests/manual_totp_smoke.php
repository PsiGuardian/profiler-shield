<?php
declare(strict_types=1);
/**
 * Black-box/manual TOTP enrolment gate. No QR dependency or third-party call.
 * This is intentionally standalone and is exercised by tools/test.php.
 */
require $argv[1]??__DIR__.'/../vendor/autoload.php';

use OmegaGuardian\ProfilerShield\Storage\FileStore;
use OmegaGuardian\ProfilerShield\Security\{Accounts,Settings,Sessions,Policy,Audit,Totp};
use OmegaGuardian\ProfilerShield\Controller\GuardianController;
use OmegaGuardian\ProfilerShield\I18n\UiTranslator;

$root=dirname(__DIR__);$fail=[];$n=0;
$check=static function(bool $condition,string $description)use(&$fail,&$n):void {
 ++$n;if(!$condition)$fail[]=$description;
};
$manifest=json_decode((string)file_get_contents($root.'/composer.json'),true,512,JSON_THROW_ON_ERROR);
$check(!isset($manifest['require']['bacon/bacon-qr-code']),'QR library still declared');
$check(!is_file($root.'/src/Security/LocalQrCode.php'),'legacy QR generator still present');
$source=file_get_contents($root.'/src/Controller/GuardianController.php');
$check(!str_contains($source,'LocalQrCode::'),'controller still invokes QR generator');
$check(!str_contains($source,'otpauth://'),'controller still builds secret-bearing provisioning URI');

$catalogs=glob($root.'/translations/guardian.*.php');
$check(count($catalogs)===25,'expected exactly 25 translated language catalogues');
foreach($catalogs as $file) {
 $entries=require $file;
 foreach(['mfa.lead','mfa.start','mfa.step2','mfa.step2_help','mfa.manual_secret','guide.mfa','mfa.privacy_help'] as $key){
  $value=$entries[$key]??null;
  $check(is_string($value) && trim($value)!=='','missing/manual entry translation: '.basename($file).' '.$key);
  $check(!preg_match('/QR|二维码/u',(string)$value),'stale QR instruction: '.basename($file).' '.$key);
 }
 foreach(['mfa.qr_alt','mfa.qr_unavailable','mfa.scan_help','mfa.local_only'] as $key){
  $check(!array_key_exists($key,$entries),'obsolete QR translation key: '.basename($file).' '.$key);
 }
}
foreach(UiTranslator::AVAILABLE as $locale=>$label) {
 $page=$root.'/docs/translations/'.$locale.'/enrolement-totp.md';
 $check(is_file($page),'regional MFA guide missing: '.$locale);
 $contents=(string)@file_get_contents($page);
 $check(!preg_match('/QR|二维码|bacon\/bacon-qr-code/iu',$contents),'obsolete QR instruction in regional guide: '.$locale);
 $check(str_contains($contents,'TOTP'),'regional MFA document no longer discusses TOTP: '.$locale);
}
$check(!preg_match('/QR|二维码|bacon\/bacon-qr-code/iu',(string)file_get_contents($root.'/docs/enrolement-totp.md')),'French root MFA guide still discusses QR');
$_SERVER['GUARDIAN_ENCRYPTION_KEY']=bin2hex(random_bytes(32));
putenv('GUARDIAN_ENCRYPTION_KEY='.$_SERVER['GUARDIAN_ENCRYPTION_KEY']);
$dir=sys_get_temp_dir().'/guardian-manual-totp-'.bin2hex(random_bytes(8));
try{
 $store=new FileStore($dir);$store->initialize();
 $settings=new Settings($store);$accounts=new Accounts($store);
 $accounts->add('fixture-dev','OnlySyntheticForManualTest!42','developer');
 $sessions=new Sessions($store,$settings);$policy=new Policy($settings,$store);
 $totp=new Totp();$audit=new Audit($store);
 $controller=new GuardianController($store,$settings,$accounts,$sessions,$policy,$audit,$totp,new UiTranslator());
 $secret=$totp->generateSecret();
 $session=['username'=>'fixture-dev','pending_totp'=>$totp->encrypt($secret),'pending_until'=>time()+600,'pending_otp_attempts'=>0];
 $user=['totp'=>'','role'=>'developer'];
 $method=new ReflectionMethod(GuardianController::class,'personalMfaForm');
 $html=$method->invoke($controller,'synthetic-csrf-token',$session,$user,'fr_FR','');
 $check(str_contains($html,htmlspecialchars($secret,ENT_QUOTES,'UTF-8')),'manual key is not displayed in authenticated pending form');
 $check(str_contains($html,'name="action" value="mfa_confirm"'),'TOTP confirm action missing');
 $check(str_contains($html,'name="action" value="mfa_cancel"'),'TOTP cancel action missing');
 $check(str_contains($html,'name="otp"'),'six-digit confirmation field missing');
 $check(!str_contains($html,'data:image/')&&!str_contains($html,'<img')&&!str_contains($html,'otpauth://'),'secret-bearing QR image/URI still exposed by form');
 $check(!str_contains($html,'mfa.step2')&&!str_contains($html,'mfa.manual_secret'),'untranslated manual labels');
 $check(str_contains($html,'SHA-1')&&str_contains($html,'30 secondes'),'manual setup protocol parameters absent');
 $check($totp->decrypt($session['pending_totp'])===$secret,'manual workflow changed secret encryption/decryption');
 $check($totp->verifyWindow('GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ','287082',59)===1,'RFC 6238 six-digit known-answer vector failed');
 $check($totp->verifyWindow('GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ','287083',59)===null,'invalid TOTP code accepted');
}finally{
 if(is_dir($dir)){
  $files=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir,FilesystemIterator::SKIP_DOTS),RecursiveIteratorIterator::CHILD_FIRST);
  foreach($files as $file)$file->isDir()?@rmdir($file->getPathname()):@unlink($file->getPathname());
  @rmdir($dir);
 }
}
if($fail){fwrite(STDERR,"Manual TOTP checks FAILED:\n".implode("\n",$fail)."\n");exit(1);}
echo "Manual TOTP verified: ".$n." assertions, 25 catalogues, 28 regional guides, no QR dependency.\n";
