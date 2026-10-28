<?php
namespace OmegaGuardian\ProfilerShield\Command {function mail($to,$subject,$body,$headers){$GLOBALS['mailCalls']++;return $GLOBALS['mailResult'];}}
namespace {
require $argv[1];spl_autoload_register(function($c){$p='OmegaGuardian\\ProfilerShield\\';if(strpos($c,$p)===0)require __DIR__.'/../src/'.str_replace('\\','/',substr($c,strlen($p))).'.php';},true,true);
use OmegaGuardian\ProfilerShield\Storage\FileStore;
use OmegaGuardian\ProfilerShield\Security\{Settings,Audit};
use OmegaGuardian\ProfilerShield\Command\AlertCommand;
$dir=sys_get_temp_dir().'/guardian-alerts-'.bin2hex(random_bytes(6));$_SERVER['GUARDIAN_ENCRYPTION_KEY']=bin2hex(random_bytes(32));$s=new FileStore($dir);$s->initialize();$cfg=new Settings($s);$audit=new Audit($s);$audit->record('emergency_granted','test',[],true);$command=new AlertCommand($s);$input=new Symfony\Component\Console\Input\ArrayInput([]);$output=new Symfony\Component\Console\Output\BufferedOutput();$GLOBALS['mailResult']=false;$GLOBALS['mailCalls']=0;$bad=[];$n=0;
function c($ok,$message){global $bad,$n;$n++;if(!$ok)$bad[]=$message;}
c($command->run($input,$output)===1,'no destination failure');c($GLOBALS['mailCalls']===0,'no mail without valid destination');$cfg->set(['alert_email'=>'test@example.invalid']);c($command->run($input,$output)===1,'transport failure exit');c(empty($s->read('alerts')[0]['delivered']),'failure retained');$GLOBALS['mailResult']=true;c($command->run($input,$output)===0,'transport success exit');c($s->read('alerts')[0]['delivered']===true,'delivered state');$calls=$GLOBALS['mailCalls'];$command->run($input,$output);c($GLOBALS['mailCalls']===$calls,'delivered not resent');
foreach(glob($dir.'/*') as $f)unlink($f);@unlink($dir.'/.htaccess');rmdir($dir);echo json_encode(['assertions'=>$n,'failures'=>$bad,'transport'=>'in-process stub; no external email'],JSON_PRETTY_PRINT),"\n";exit($bad?1:0);
}
