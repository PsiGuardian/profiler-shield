<?php
/** Vérifie la génération sécurisée et la reprise du bootstrap sans Symfony réel. */
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
    $app=sys_get_temp_dir().'/guardian-setup-automatic-'.bin2hex(random_bytes(6));
    mkdir($app); mkdir($app.'/bin'); file_put_contents($app.'/bin/console','<?php');
    $store=new \OmegaGuardian\ProfilerShield\Storage\FileStore($app.'/.guardian-shield');
    $accounts=new \OmegaGuardian\ProfilerShield\Security\Accounts($store);
    $setup=new \OmegaGuardian\ProfilerShield\Command\SetupCommand($app,$store,$accounts);
    $input=new class implements \Symfony\Component\Console\Input\InputInterface {
        public function getOption($name) {return array('path'=>null,'username'=>'admin','automatic'=>true,'no-admin'=>false)[$name];}
        public function isInteractive() {return false;}
    };
    $output=new class implements \Symfony\Component\Console\Output\OutputInterface {
        public $messages=array(); public function writeln($messages,$options=0) {$this->messages[]=$messages;}
        public function content() {return implode("\n",$this->messages);}
    };
    $run=new \ReflectionMethod($setup,'execute'); $run->setAccessible(true);
    function check($bool,$name) { if (!$bool) {throw new \RuntimeException('FAIL '.$name);} echo '[PASS] '.$name."\n"; }
    function removeAll($path) {if(!is_dir($path)) return;foreach(scandir($path) as $n){if($n==='.'||$n==='..')continue;$x=$path.'/'.$n;if(is_dir($x))removeAll($x);else unlink($x);}rmdir($path);}
    try {
        check($run->invoke($setup,$input,$output)===0,'Provisionnement automatique complet');
        check(strpos(file_get_contents($app.'/config/routes/guardian.yaml'),'resource: guardian.php')!==false,'Import YAML pour les anciens MicroKernel');
        $cred=$store->root().'/initial-admin-credentials.txt';
        check(is_file($cred),'Identifiants initiaux présents localement');
        check((fileperms($cred) & 0077)===0,'Identifiants 0600');
        check((fileperms($store->root()) & 0077)===0,'Coffre 0700');
        $contents=file_get_contents($cred);
        check((bool)preg_match('/Mot de passe : ([^\r\n]+)/', $contents,$match),'Mot de passe généré présent');
        check($accounts->verify('admin',$match[1])!==null,'Mot de passe utilisable pour authentifier admin');
        check(strpos($output->content(), $match[1])===false,'Mot de passe jamais affiché dans stdout');
        check(strpos(file_get_contents($app.'/.env.local'),$match[1])===false,'Mot de passe absent de .env.local');
        $originalEnv=file_get_contents($app.'/.env.local');
        check($run->invoke($setup,$input,$output)===0,'Relance automatique idempotente');
        check(count($accounts->allPublic())===1,'Pas de nouveau compte en relance');
        check(file_get_contents($app.'/.env.local')===$originalEnv,'Secrets conservés en relance');
        unlink($cred);
        check($run->invoke($setup,$input,$output)===0,'Relance après suppression des credentials');
        check(!is_file($cred),'Le fichier credentials n’est jamais recréé pour un compte existant');
        $before=file_get_contents($app.'/.env.local');
        file_put_contents($app.'/config/routes/guardian.yaml',"custom: user-owned-route\n");
        check($run->invoke($setup,$input,$output)!==0,'Import de routage personnalisé refusé');
        check(file_get_contents($app.'/.env.local')===$before,'Conflit de routage sans changement des secrets');
    } finally {removeAll($app);}
}
