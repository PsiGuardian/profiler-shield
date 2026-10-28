<?php
/** Tests de la CLI via substituts ; n'attestent pas la compatibilité Symfony. */
namespace Symfony\Component\Console\Input {
    interface InputInterface {public function getOption($name); public function isInteractive();}
    final class InputOption { const VALUE_REQUIRED=1; const VALUE_NONE=2; }
}
namespace Symfony\Component\Console\Output {
    interface OutputInterface {public function writeln($messages, $options=0);}
}
namespace Symfony\Component\Console\Question {
    class Question {public function __construct($text,$default=null){} public function setHidden($h) {} public function setHiddenFallback($f) {} }
    class ConfirmationQuestion extends Question {}
}
namespace Symfony\Component\Console\Command {
    class Command {public function __construct(){} protected function setName($s){return $this;} protected function setDescription($s){return $this;} protected function addOption($a,$b=null,$c=null,$d='',$e=null){return $this;} protected function getHelper($name){} }
}
namespace Symfony\Bundle\WebProfilerBundle {class WebProfilerBundle {}}
namespace Symfony\Component\HttpKernel {class Kernel { const VERSION='7.4.0'; }}
namespace {
    $root=dirname(__DIR__);
    spl_autoload_register(function ($name) use ($root) {
        $prefix='OmegaGuardian\\ProfilerShield\\';
        if (strpos($name,$prefix)!==0) {return;}
        $file=$root.'/src/'.str_replace('\\','/',substr($name,strlen($prefix))).'.php';
        if (is_file($file)) {require $file;}
    });
    $app=sys_get_temp_dir().'/guardian-setup-'.bin2hex(random_bytes(6));
    mkdir($app); mkdir($app.'/bin'); file_put_contents($app.'/bin/console','<?php');
    $store=new \OmegaGuardian\ProfilerShield\Storage\FileStore($app.'/.guardian-shield');
    $accounts=new \OmegaGuardian\ProfilerShield\Security\Accounts($store);
    $setup=new \OmegaGuardian\ProfilerShield\Command\SetupCommand($app,$store,$accounts);
    $input=new class implements \Symfony\Component\Console\Input\InputInterface {
        public function getOption($name) {return array('path'=>null,'username'=>'admin','automatic'=>false,'no-admin'=>true)[$name];}
        public function isInteractive() {return false;}
    };
    $output=new class implements \Symfony\Component\Console\Output\OutputInterface {
        public $messages=array(); public function writeln($messages,$options=0) {$this->messages[]=$messages;}
    };
    $run=new \ReflectionMethod($setup,'execute'); $run->setAccessible(true);
    function ok($bool,$label) {if (!$bool) {throw new \RuntimeException('FAIL '.$label);} echo '[PASS] '.$label."\n";}
    try {
        ok($run->invoke($setup,$input,$output)===0,'Setup automatisé sans interaction');
        $env=file_get_contents($app.'/.env.local');
        ok((bool)preg_match('~GUARDIAN_LOGIN_PATH=/internal/[a-f0-9]{48}~',$env),'Chemin à forte entropie généré');
        ok((bool)preg_match('/GUARDIAN_ENCRYPTION_KEY=[a-f0-9]{64}/',$env),'Clé chiffrante générée');
        ok(strpos(file_get_contents($app.'/config/routes/guardian.php'),'OmegaGuardian generated route v1')!==false,'Fichier de route installé');
        ok(strpos(file_get_contents($app.'/config/packages/dev/zz_guardian_profiler.yaml'),'enabled: true')!==false,'Config toolbar adaptée à Symfony 7.4');
        ok($store->ready(),'Stockage privé initialisé');
        ok((fileperms($app.'/.env.local') & 0077)===0,'Permissions du fichier de secrets restrictives');
        ok($run->invoke($setup,$input,$output)===0,'Setup relançable sans régénération');
        ok(file_get_contents($app.'/.env.local')===$env,'Secrets existants inchangés');
        ok(strpos(file_get_contents($app.'/config/packages/dev/zz_guardian_profiler.yaml'),'enabled: true')!==false,'Configuration du profiler préservée au second lancement');
    } finally {
        $walk=function ($path) use (&$walk) {foreach (array_merge(glob($path.'/*'),glob($path.'/.*',GLOB_NOSORT)) as $f) {if (basename($f)==='.' || basename($f)==='..') {continue;} if (is_dir($f)) {$walk($f);rmdir($f);} else {unlink($f);}}};
        $walk($app); rmdir($app);
    }
}
