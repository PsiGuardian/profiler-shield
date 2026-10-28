<?php
/**
 * Régression : Doctor doit utiliser projectDirectory avant tout diagnostic.
 * Exécutable sans dépendances Composer grâce aux doubles Console ci-dessous.
 * Ces doubles NE remplacent PAS les tests Symfony réels.
 */
namespace Symfony\Component\Console\Input {
    interface InputInterface {}
    final class FakeInput implements InputInterface {}
}
namespace Symfony\Component\Console\Output {
    interface OutputInterface { public function writeln($messages, $options = 0); }
    final class FakeOutput implements OutputInterface {
        public $lines = array();
        public function writeln($messages, $options = 0) { $this->lines[] = (string) $messages; }
    }
}
namespace Symfony\Component\Console\Command {
    class Command {
        public function __construct() {}
        public function setName($name) { return $this; }
        public function setDescription($description) { return $this; }
    }
}
namespace {
    spl_autoload_register(function ($class) {
        $prefix = 'OmegaGuardian\\ProfilerShield\\';
        if (strpos($class, $prefix) !== 0) { return; }
        $relative = substr($class, strlen($prefix));
        $file = dirname(__DIR__).'/src/'.str_replace('\\', '/', $relative).'.php';
        if (is_file($file)) { require $file; }
    });

    // Convertir chaque avertissement PHP en exception : l'incident remonté
    // par l'utilisateur doit faire échouer le test, même si le code continue.
    set_error_handler(function ($severity, $message) {
        if (!(error_reporting() & $severity)) { return false; }
        throw new \ErrorException($message, 0, $severity);
    });
    $project = sys_get_temp_dir().'/guardian-doctor-'.bin2hex(random_bytes(6));
    @mkdir($project.'/config/routes', 0700, true);
    @mkdir($project.'/public', 0700, true);
    file_put_contents($project.'/config/routes/guardian.php', '<?php return [];');
    $_SERVER['GUARDIAN_LOGIN_PATH'] = '/internal/'.bin2hex(random_bytes(24));
    $_SERVER['GUARDIAN_ENCRYPTION_KEY'] = bin2hex(random_bytes(32));
    $_SERVER['GUARDIAN_STORAGE_DIR'] = $project.'/.guardian-shield';
    $_SERVER['GUARDIAN_SHOW_PHP_VERSION'] = '0';

    try {
        $store = new \OmegaGuardian\ProfilerShield\Storage\FileStore($project.'/.guardian-shield');
        $store->initialize();
        $accounts = new \OmegaGuardian\ProfilerShield\Security\Accounts($store);
        $accounts->add('admin', 'example-secret-123456789!', 'admin');
        $settings = new \OmegaGuardian\ProfilerShield\Security\Settings($store);
        $settings->set(array('weekdays'=>array(1,2,3,4,5),'timezone'=>'America/New_York'));
        $cmd = new \OmegaGuardian\ProfilerShield\Command\DoctorCommand($settings, $store, $accounts, $project);
        $output = new \Symfony\Component\Console\Output\FakeOutput();
        $method = new \ReflectionMethod($cmd, 'execute');
        $method->setAccessible(true);
        $status = $method->invoke($cmd, new \Symfony\Component\Console\Input\FakeInput(), $output);
        $text = implode("\n", $output->lines);
        if (!is_int($status) || $status !== 0) { throw new \RuntimeException('Doctor doit valider un coffre neuf correctement paramétré : '.implode(' / ',$output->lines)); }
        foreach (array('administrateur(s) actif(s)', 'Fuseau PHP CLI', 'politique opérationnelle cohérente', 'fichier de routage Guardian présent') as $expected) {
            if (strpos($text, $expected) === false) {
                throw new \RuntimeException('Diagnostic incomplet : '.$expected.'\n'.$text);
            }
        }
        echo "OK doctor_smoke: diagnostic neuf complet, aucun Warning PHP.\n";
    } finally {
        foreach (glob($project.'/.guardian-shield/*') ?: array() as $file) { @unlink($file); }
        @unlink($project.'/.guardian-shield/.htaccess');
        @unlink($project.'/config/routes/guardian.php');
        @rmdir($project.'/config/routes');
        @rmdir($project.'/config');
        @rmdir($project.'/public');
        @rmdir($project.'/.guardian-shield');
        @rmdir($project);
    }
}
