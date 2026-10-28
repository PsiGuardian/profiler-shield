<?php
require dirname(__DIR__).'/src/Installation/Uninstaller.php';
require dirname(__DIR__).'/src/Installation/BundleRegistration.php';
use OmegaGuardian\ProfilerShield\Installation\BundleRegistration;
$root=sys_get_temp_dir().'/guardian-registration-'.bin2hex(random_bytes(8));mkdir($root,0700);mkdir($root.'/config',0700);$checks=0;
function check($condition,$message){global $checks;if(!$condition)throw new RuntimeException($message);++$checks;}
try {
 foreach (["<?php return [];\n", "<?php return [\\Other\\Bundle::class => ['all'=>true]];\n", "<?php return [\\Other\\Bundle::class => ['all'=>true] // final comment\n];\n", "<?php // OmegaGuardian\\ProfilerShield\\OmegaGuardianProfilerShieldBundle\nreturn [];\n"] as $source) {
  file_put_contents($root.'/config/bundles.php',$source);chmod($root.'/config/bundles.php',0640);
  (new BundleRegistration())->register($root);$result=file_get_contents($root.'/config/bundles.php');
  check(str_contains($result,"['dev' => true, 'test' => true]"),'Only dev/test activation');
  token_get_all($result,TOKEN_PARSE);check(true,'Valid PHP without evaluating host code');
  (new BundleRegistration())->register($root);check(file_get_contents($root.'/config/bundles.php')===$result,'Idempotent');
  check((fileperms($root.'/config/bundles.php')&0777)===0640,'Permissions preserved');
 }
 foreach (["<?php return array();\n", "<?php use OmegaGuardian\\ProfilerShield\\OmegaGuardianProfilerShieldBundle as Shield; return [Shield::class => [\'all\'=>true]];\n", "<?php return [;\n", "<?php file_put_contents('NEVER_EXECUTE','x'); return array();\n"] as $source) {
  file_put_contents($root.'/config/bundles.php',$source);$refused=false;
  try{(new BundleRegistration())->register($root);}catch(Throwable $e){$refused=true;}
  check($refused,'Unusual or invalid configuration refused');check(file_get_contents($root.'/config/bundles.php')===$source,'Original preserved');
 }
 unlink($root.'/config/bundles.php');file_put_contents($root.'/outside.php','<?php return [];');symlink($root.'/outside.php',$root.'/config/bundles.php');
 $refused=false;try{(new BundleRegistration())->register($root);}catch(Throwable $e){$refused=true;}
 check($refused,'Symlink refused');check(file_get_contents($root.'/outside.php')==='<?php return [];','Symlink target preserved');
 $metadata=json_decode(file_get_contents(dirname(__DIR__).'/composer.json'),true);
 check($metadata['type']==='symfony-bundle' && !isset($metadata['extra']['class']) && !isset($metadata['require']['composer-plugin-api']) && $metadata['bin']===['bin/guardian'],'Standard bundle and explicit assistant metadata');
 check(!isset($metadata['require']['omegaguardian/profiler-shield-installer']),'No installer package');
 $events=['pre-install-cmd','post-install-cmd','pre-update-cmd','post-update-cmd','pre-autoload-dump','post-autoload-dump','post-package-install','post-package-update','pre-package-uninstall','post-package-uninstall'];
 check(!array_intersect($events,array_keys($metadata['scripts']??[])),'No automatic Composer lifecycle scripts');
 check(!isset($metadata['autoload']['files']),'No implicit autoload files');
 check(!is_file(dirname(__DIR__).'/src/Composer/Plugin.php'),'Plugin implementation removed');
 echo json_encode(['checks'=>$checks,'failed'=>0])."\n";
} finally {
 foreach(glob($root.'/config/*') as $f)unlink($f);rmdir($root.'/config');if(is_file($root.'/outside.php'))unlink($root.'/outside.php');rmdir($root);
}
