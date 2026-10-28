<?php
/**
 * Simulation des questions Console sur une installation vierge ou reprise.
 * Aucun service Symfony réel ici : ces essais ne certifient pas l'intégration.
 */
namespace Symfony\Component\Console\Input {
    interface InputInterface { public function getOption($name); public function isInteractive(); }
    final class InputOption { const VALUE_REQUIRED=1; const VALUE_NONE=2; }
}
namespace Symfony\Component\Console\Output {
    interface OutputInterface { public function writeln($messages, $options=0); }
}
namespace Symfony\Component\Console\Question {
    class Question {
        public $prompt; public $default;
        public function __construct($text, $default=null) { $this->prompt=$text;$this->default=$default; }
        public function setHidden($enabled) {}
        public function setHiddenFallback($enabled) {}
    }
    class ConfirmationQuestion extends Question {}
}
namespace Symfony\Component\Console\Command {
    class Command {
        public function __construct() {}
        protected function setName($s) { return $this; }
        protected function setDescription($s) { return $this; }
        protected function addOption($a,$b=null,$c=null,$d='',$e=null) { return $this; }
        protected function getHelper($name) { return $GLOBALS['guardian_helper']; }
    }
}
namespace Symfony\Bundle\WebProfilerBundle { class WebProfilerBundle {} }
namespace Symfony\Component\HttpKernel { class Kernel { const VERSION='7.4.0'; } }
namespace {
    $root=dirname(__DIR__);
    spl_autoload_register(function ($name) use ($root) {
        $prefix='OmegaGuardian\\ProfilerShield\\';
        if (strpos($name,$prefix)!==0) return;
        $file=$root.'/src/'.str_replace('\\','/',substr($name,strlen($prefix))).'.php';
        if (is_file($file)) require $file;
    });
    function test($value,$label) {
        if (!$value) { throw new \RuntimeException('FAIL '.$label); }
        echo '[PASS] '.$label."\n";
    }
    class SimulatedHelper {
        public $answers=array(); public $questions=array();
        public function ask($input,$output,$question) {
            $this->questions[]=$question->prompt;
            if (!$this->answers) throw new \RuntimeException('Réponses interactives insuffisantes.');
            $answer=array_shift($this->answers);
            return $answer===null ? $question->default : $answer;
        }
    }
    class SimulatedInput implements \Symfony\Component\Console\Input\InputInterface {
        public $interactive; public $noAdmin;
        public function __construct($interactive=true,$noAdmin=false) {$this->interactive=$interactive;$this->noAdmin=$noAdmin;}
        public function getOption($name) {
            return array('path'=>null,'username'=>'admin','automatic'=>false,'no-admin'=>$this->noAdmin)[$name];
        }
        public function isInteractive() { return $this->interactive; }
    }
    class SimulatedOutput implements \Symfony\Component\Console\Output\OutputInterface {
        public $messages=array();
        public function writeln($messages,$options=0) { $this->messages[]=$messages; }
        public function log() { return implode("\n",$this->messages); }
    }
    function runSetup($cmd,$interactive=true,$noAdmin=false) {
        $ref=new \ReflectionMethod($cmd,'execute');$ref->setAccessible(true);
        $output=new SimulatedOutput();
        $exit=$ref->invoke($cmd,new SimulatedInput($interactive,$noAdmin),$output);
        return array($exit,$output->log());
    }
    $app=sys_get_temp_dir().'/guardian-setup-interactive-'.bin2hex(random_bytes(6));
    mkdir($app);mkdir($app.'/bin');file_put_contents($app.'/bin/console','<?php');
    $store=new \OmegaGuardian\ProfilerShield\Storage\FileStore($app.'/.guardian-shield');
    $accounts=new \OmegaGuardian\ProfilerShield\Security\Accounts($store);
    $setup=new \OmegaGuardian\ProfilerShield\Command\SetupCommand($app,$store,$accounts);
    $GLOBALS['guardian_helper']=new SimulatedHelper();
    try {
        // Une installation non interactive ne peut annoncer un succès
        // quand aucun administrateur n'est encore disponible.
        list($exit,$output)=runSetup($setup,false,false);
        test($exit===1,'Un setup non interactif sans admin échoue explicitement');
        test(strpos($output,'aucun administrateur actif')!==false,'Erreur et remédiation signalées');
        $originalEnv=file_get_contents($app.'/.env.local');
        test(!$accounts->allPublic(),'Aucun compte fictif créé en CI');

        // Une saisie de mot de passe incorrecte doit relancer la question.
        $GLOBALS['guardian_helper']->answers=array('admin','short','admin','CorrectPassword12345!','IncorrectPassword12345!','admin','CorrectPassword12345!','CorrectPassword12345!');
        list($exit,$output)=runSetup($setup,true,false);
        test($exit===0,'Une reprise interactive crée le compte admin');
        test($accounts->verify('admin','CorrectPassword12345!')!==null,'Le compte se connecte avec le mot de passe choisi');
        test(strpos($output,'Installation terminée')!==false,'Succès seulement après création du compte');
        test(strpos($output,'Mot de passe trop court')!==false,'Mot de passe court refusé');
        test(strpos($output,'ne correspondent pas')!==false,'Confirmation incorrecte refusée');
        test(strpos($output,'CorrectPassword12345!')===false,'Mot de passe absent des sorties console');
        test(file_get_contents($app.'/.env.local')===$originalEnv,'Clé et route préservées lors de la reprise');
        test(count($accounts->allPublic())===1,'Un seul compte administrateur créé');

        // Une seconde exécution ne doit ni demander un nouveau mot de
        // passe, ni créer d'autres identités.
        $GLOBALS['guardian_helper']->questions=array();
        list($exit,$output)=runSetup($setup,true,false);
        test($exit===0,'Setup idempotent avec compte existant');
        test(strpos($output,'Compte administrateur : admin')!==false,'Compte existant annoncé clairement');
        test(!$GLOBALS['guardian_helper']->questions,'Aucun nouveau dialogue sur réinstallation');

        // Développeur sans compte : un provisionnement volontairement incomplet
        // est affiché explicitement avec --no-admin.
        $app2=sys_get_temp_dir().'/guardian-setup-no-admin-'.bin2hex(random_bytes(6));
        mkdir($app2);mkdir($app2.'/bin');file_put_contents($app2.'/bin/console','<?php');
        $store2=new \OmegaGuardian\ProfilerShield\Storage\FileStore($app2.'/.guardian-shield');
        $setup2=new \OmegaGuardian\ProfilerShield\Command\SetupCommand($app2,$store2,new \OmegaGuardian\ProfilerShield\Security\Accounts($store2));
        list($exit,$output)=runSetup($setup2,false,true);
        test($exit===0 && strpos($output,'Configuration préparée')!==false,'--no-admin signale clairement le provisionnement incomplet');
        function rmTree($path) {
            if (!is_dir($path)) return;
            foreach (scandir($path) as $item) {
                if ($item==='.' || $item==='..') continue;
                $file=$path.'/'.$item;
                if (is_dir($file)) rmTree($file); else unlink($file);
            }
            rmdir($path);
        }
        rmTree($app2);
    } finally {
        if (!function_exists('rmTree')) {
            function rmTree($path) { foreach(scandir($path) as $item) {if ($item==='.'||$item==='..')continue;$file=$path.'/'.$item;if(is_dir($file))rmTree($file);else unlink($file);}rmdir($path); }
        }
        rmTree($app);
    }
}
