<?php
namespace OmegaGuardian\ProfilerShield\Command;

use OmegaGuardian\ProfilerShield\Security\Settings;
use OmegaGuardian\ProfilerShield\Storage\FileStore;
use OmegaGuardian\ProfilerShield\Security\Accounts;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/** Diagnostics de déploiement locaux. Ne prétend pas tester l'exposition Internet. */
final class DoctorCommand extends Command
{
    private $settings; private $store; private $accounts; private $projectDirectory;
    public function __construct(Settings $settings, FileStore $store, Accounts $accounts, $projectDirectory)
    { $this->settings=$settings;$this->store=$store;$this->accounts=$accounts;$this->projectDirectory=$projectDirectory;parent::__construct(); }
    protected function configure(): void
    { $this->setName('guardian:doctor')->setDescription('Contrôle la configuration Guardian et signale ses limites.'); }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $warnings=0;
        $output->writeln('<info>ΩGuardian Profiler Shield — Doctor</info>');
        if (!$this->store->ready()) { $output->writeln('<error>ERREUR: stockage indisponible.</error>');$warnings++; }
        else { $output->writeln('<info>OK: stockage local accessible.</info>'); }
        // Une route secrète sans compte actif ne permet pas de se connecter.
        // Ce contrôle évite de considérer l'installation terminée alors que
        // le bootstrap des utilisateurs a été ignoré ou interrompu.
        try {
            $adminReady = false;
            $admins = array();
            foreach ($this->accounts->allPublic() as $account) {
                if ($account['role'] === 'admin' && !$account['disabled']) {
                    $adminReady = true;
                    $admins[] = $account['name'];
                }
            }
            if (!$adminReady) {
                $output->writeln('<error>ERREUR : aucun compte administrateur Guardian actif. Relancer guardian:setup.</error>');
                ++$warnings;
            } else {
                $output->writeln('<info>OK : administrateur(s) actif(s) : '.implode(', ', $admins).'.</info>');
            }
        } catch (\Throwable $ignored) {
            $output->writeln('<error>ERREUR : impossible de vérifier les comptes Guardian.</error>');
            ++$warnings;
        }
        $root=realpath($this->store->root());
        $public=realpath($this->projectDirectory.'/public');
        if ($root && $public && strpos($root.'/', $public.'/')===0) {
            $output->writeln('<error>ERREUR: stockage placé dans le répertoire public !</error>');$warnings++;
        }
        $path=\OmegaGuardian\ProfilerShield\Util\Env::get('GUARDIAN_LOGIN_PATH');
        if (!$path || !preg_match('~^/[a-zA-Z0-9/_-]{24,180}$~D',$path)) {
            $output->writeln('<error>ERREUR: GUARDIAN_LOGIN_PATH insuffisamment long ou non configuré.</error>');$warnings++;
        } else { $output->writeln('<info>OK: chemin privé syntaxiquement valide (entropie non mesurée).</info>'); }
        $key=\OmegaGuardian\ProfilerShield\Util\Env::get('GUARDIAN_ENCRYPTION_KEY');
        if (!preg_match('/^[a-f0-9]{64}$/Di',$key)) {
            $output->writeln('<error>ERREUR: GUARDIAN_ENCRYPTION_KEY invalide ou absent : coffre chiffré inaccessible.</error>');$warnings++;
        } else { $output->writeln('<info>OK : clé de chiffrement configurée (contenu masqué).</info>'); }
        if ($root) {
            $mode=fileperms($root) & 0077;
            if ($mode) { $output->writeln('<error>ERREUR: permissions du coffre trop ouvertes.</error>');$warnings++; }
            foreach (glob($root.'/*.json') as $file) {
                if (is_link($file) || (fileperms($file) & 0077)) { $output->writeln('<error>ERREUR: permissions/lien symbolique collection '.basename($file).'.</error>');$warnings++;continue; }
                $raw=json_decode((string)@file_get_contents($file),true);
                if (!is_array($raw) || !isset($raw['schema']) || $raw['schema']!==2) {
                    $output->writeln('<error>ERREUR: collection non chiffrée au format v2: '.basename($file).'.</error>');$warnings++;
                }
            }
        }
        $zone=\OmegaGuardian\ProfilerShield\Util\ServerClock::phpTimezone();
        $output->writeln('<info>Fuseau PHP CLI : '.$zone.' (peut différer de PHP-FPM).</info>');
        // Les paramètres sont vérifiés strictement ; aucune correction implicite.
        try {
            $s = $this->settings->all();
            $this->settings->validate($s);
            $output->writeln('<info>OK: politique opérationnelle cohérente.</info>');
            if ($s['timezone'] !== $zone) {
                $output->writeln('<comment>INFO : fuseau de politique différent du fuseau PHP CLI : '.$s['timezone'].'. Confirmer dans l’interface PHP-FPM.</comment>');
            }
        } catch (\InvalidArgumentException $e) {
            // Les messages du validateur nomment des CHAMPS, jamais les
            // valeurs ni les secrets. Filtrer les caractères de contrôle qui
            // pourraient se trouver dans une collection locale corrompue.
            $reason = preg_replace('/[\x00-\x1F\x7F<>]/', '', $e->getMessage());
            $output->writeln('<error>ERREUR: politique invalide : '.substr((string) $reason, 0, 180).'.</error>');
            ++$warnings;
        } catch (\Throwable $e) {
            $output->writeln('<error>ERREUR: politique illisible ou impossible à vérifier (coffre/clé/format).</error>');
            ++$warnings;
        }
        $project=$this->projectDirectory;
        $routeFile=$project.'/config/routes/guardian.php';
        if (!is_file($routeFile)) {
            $output->writeln('<error>ERREUR : route Guardian absente (guardian:setup).</error>');$warnings++;
        } else { $output->writeln('<info>OK : fichier de routage Guardian présent.</info>'); }
        $privateFile=$project.'/.env.local';
        if (is_file($privateFile) && (fileperms($privateFile) & 0007)) {
            $output->writeln('<comment>AVERTISSEMENT : .env.local lisible par tous sur ce serveur.</comment>');$warnings++;
        }
        if (\OmegaGuardian\ProfilerShield\Util\Env::get('GUARDIAN_SHOW_PHP_VERSION')==='1') {
            $output->writeln('<comment>ATTENTION : exposition volontaire de la version PHP activée.</comment>');$warnings++;
        }
        $output->writeln('<comment>VÉRIFICATION MANUELLE OBLIGATOIRE :</comment>');
        $output->writeln('  - APP_ENV != prod pour les diagnostics ; APP_DEBUG=0 en prod.');
        $output->writeln('  - web_profiler.toolbar: true en dev, mais collecte globale désactivée et bridge Guardian actif.');
        $output->writeln('  - framework.profiler.collect: false, SANS collect_parameter public.');
        $output->writeln('  - /_profiler et /_wdt renvoient 404 depuis une origine externe non authentifiée.');
        $output->writeln('  - X-Debug-Exception, X-Debug-Exception-File et X-Powered-By absents des 404/500.');
        if (ini_get('expose_php')) {
            $output->writeln('<comment>ATTENTION: le PHP CLI annonce expose_php=On ; vérifier également le PHP-FPM réellement utilisé.</comment>');
        }
        $output->writeln('  - php.ini ou pool FPM : expose_php=Off (et recharger PHP-FPM).');
        $output->writeln('  - tester les configurations reverse proxy et les versions Symfony réellement déployées.');
        return $warnings ? 1 : 0;
    }
}
