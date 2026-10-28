<?php
require $argv[1]??__DIR__.'/../vendor/autoload.php';
spl_autoload_register(function($c){$p='OmegaGuardian\\ProfilerShield\\';if(str_starts_with($c,$p)){require __DIR__.'/../src/'.str_replace('\\','/',substr($c,strlen($p))).'.php';}},true,true);
use OmegaGuardian\ProfilerShield\Installation\Uninstaller;
use OmegaGuardian\ProfilerShield\Command\UninstallCommand;
use Symfony\Component\Console\Tester\CommandTester;
$n=0;function ok($b,$s){global $n;if(!$b)throw new RuntimeException($s);$n++;}
$base=sys_get_temp_dir().'/guardian-explicit-removal-'.bin2hex(random_bytes(7));mkdir($base,0700);
function fixture($base,$name){$p=$base.'/'.$name;mkdir($p,0700);mkdir($p.'/bin');file_put_contents($p.'/bin/console','<?php');file_put_contents($p.'/composer.json','{"type":"project"}');$before=Uninstaller::snapshot($p);mkdir($p.'/.guardian-shield',0700);file_put_contents($p.'/.guardian-shield/accounts.json','synthetic-encrypted-fixture');file_put_contents($p.'/.env.local',"APP_SECRET=synthetic-business-secret\nGUARDIAN_ENCRYPTION_KEY=synthetic-fixture-key\n");Uninstaller::writeReceipt($p,$before);return $p;}
function wipe($p){if(is_dir($p)&&!is_link($p)){foreach(scandir($p) as $v){if($v==='.'||$v==='..')continue;wipe($p.'/'.$v);}rmdir($p);}else unlink($p);}
try{
 foreach(['purge','force','decline','retain','unknown','external','prod'] as $mode){
  $p=fixture($base,$mode);$cmd=new CommandTester(new UninstallCommand($p));$args=[];$options=['interactive'=>false];
  if(in_array($mode,['purge','force','decline','unknown','external'],true))$args['--purge']=true;
  if(in_array($mode,['force','unknown','external'],true))$args['--force']=true;
  if($mode==='decline'){$options['interactive']=true;$cmd->setInputs(['no']);}
  if($mode==='unknown')file_put_contents($p.'/.guardian-shield/foreign.txt','not-Guardian');
  if($mode==='external'){putenv('GUARDIAN_STORAGE_DIR='.$p.'/external-vault');$_SERVER['GUARDIAN_STORAGE_DIR']=$p.'/external-vault';}
  if($mode==='prod'){putenv('APP_ENV=prod');$_SERVER['APP_ENV']='prod';}
  $before=file_get_contents($p.'/.env.local');$exit=$cmd->execute($args,$options);
  if($mode==='force'){ok($exit===0,'explicit forced purge succeeds');ok(!is_dir($p.'/.guardian-shield'),'synthetic vault removed');ok(!str_contains(file_get_contents($p.'/.env.local'),'GUARDIAN_ENCRYPTION_KEY'),'managed key removed');}
  elseif($mode==='retain'){ok($exit===0,'default cleanup succeeds');ok(is_file($p.'/.guardian-shield/accounts.json'),'default keeps accounts');ok(str_contains(file_get_contents($p.'/.env.local'),'GUARDIAN_ENCRYPTION_KEY'),'default keeps key');}
  else{ok($exit!==0,'unsafe or declined request is not reported as complete: '.$mode);ok(is_file($p.'/.guardian-shield/accounts.json'),'data retained: '.$mode);ok(file_get_contents($p.'/.env.local')===$before,'key and business configuration retained: '.$mode);}
  unset($_SERVER['GUARDIAN_STORAGE_DIR'],$_SERVER['APP_ENV']);putenv('GUARDIAN_STORAGE_DIR');putenv('APP_ENV');
 }
 echo json_encode(['checks'=>$n,'failures'=>[]],JSON_PRETTY_PRINT),"\n";
}finally{wipe($base);}
