<?php
namespace OmegaGuardian\ProfilerShield\Command;

use OmegaGuardian\ProfilerShield\Security\Accounts;
use OmegaGuardian\ProfilerShield\Storage\FileStore;
use OmegaGuardian\ProfilerShield\Util\Env;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * Initialisation guidée, reproductible et indépendante du site métier.
 *
 * Le code ne s'exécute JAMAIS sur une requête HTTP : aucun réseau, aucune
 * dépendance à des ressources publiques, aucune donnée secrète dans les logs.
 * L’initialisation est exécutée explicitement par guardian:setup après
 * activation via l’assistant local ou enregistrement manuel du bundle.
 *
 * La commande peut être lancée plusieurs fois : elle ne remplace jamais
 * une clé utilisée pour chiffrer des secrets TOTP ni un chemin déjà choisi.
 */
final class SetupCommand extends Command
{
    private $projectDirectory;
    private $store;
    private $accounts;

    public function __construct($projectDirectory, FileStore $store, Accounts $accounts)
    {
        $this->projectDirectory = rtrim($projectDirectory, '/');
        $this->store = $store;
        $this->accounts = $accounts;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('guardian:setup')
            ->setDescription('Assistant : route privée, clé AES, configuration Symfony et compte initial.')
            ->addOption('automatic', null, InputOption::VALUE_NONE, 'Configurer sans interaction et générer un compte initial (identifiants dans le coffre 0600)')
            ->addOption('path', null, InputOption::VALUE_REQUIRED, 'Chemin privé choisi explicitement (sinon génération aléatoire)')
            ->addOption('no-admin', null, InputOption::VALUE_NONE, 'Préparer uniquement les fichiers (CI), sans rendre le portail utilisable')
            ->addOption('username', null, InputOption::VALUE_REQUIRED, 'Identifiant administrateur initial proposé', 'admin');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $base = $this->projectDirectory;
        $before = \OmegaGuardian\ProfilerShield\Installation\Uninstaller::snapshot($base);
        $envFile = $base.'/.env.local';
        $routeFile = $base.'/config/routes/guardian.php';
        $routeImport = $base.'/config/routes/guardian.yaml';
        $profilerFile = $base.'/config/packages/dev/zz_guardian_profiler.yaml';
        $helper = $this->getHelper('question');
        $configurationChanged = false;
        try {
            if (is_link($base.'/.guardian-shield') || is_link($base.'/.guardian-shield/.installation.json')) {
                throw new \RuntimeException('Répertoire ou reçu Guardian symbolique : configuration refusée.');
            }
            // Le coffre persistant ne doit jamais être créé dans var/.
            if (rtrim($this->store->root(),'/') === $base.'/var/guardian-shield') {
                throw new \RuntimeException('Le coffre ne doit pas être placé dans var/.');
            }
            if (!is_file($base.'/bin/console')) {
                throw new \RuntimeException('Exécuter depuis une application Symfony contenant bin/console.');
            }
            // Ne pas effectuer de changements sur un système de production.
            $environment = Env::get('APP_ENV');
            if ($environment === 'prod') {
                throw new \RuntimeException('Configuration interactive interdite dans APP_ENV=prod.');
            }
            $existing = is_file($envFile) ? (string) file_get_contents($envFile) : '';
            if ($existing === '' && is_file($envFile)) {
                // Fichier vide autorisé ; les valeurs déjà injectées via Docker
                // resteront prioritaires sur celles créées dans .env.local.
            }
            $route = $this->readVar($existing, 'GUARDIAN_LOGIN_PATH');
            if ($route === null) { $route = Env::get('GUARDIAN_LOGIN_PATH'); }
            $supplied = $input->getOption('path');
            if ($route !== '' && $supplied !== null && $supplied !== $route) {
                throw new \RuntimeException('Chemin déjà configuré : rotation refusée par setup.');
            }
            if (!$route) {
                $route = $supplied !== null ? (string) $supplied : '/internal/'.bin2hex(random_bytes(24));
                if ($supplied === null && $input->isInteractive() && !$input->getOption('automatic')) {
                    $question = new Question('Chemin de connexion (Entrée = chemin aléatoire proposé) ['.$route.'] : ', $route);
                    $route = (string) $helper->ask($input, $output, $question);
                }
            }
            if (!preg_match('~^/[a-zA-Z0-9/_-]{24,180}$~D', $route)
                || preg_match('~^/(?:_profiler|_wdt)(?:/|$)~i', $route)) {
                throw new \RuntimeException('Chemin privé invalide : au moins 24 caractères, /, lettres, chiffres, _ ou -.');
            }
            $key = $this->readVar($existing, 'GUARDIAN_ENCRYPTION_KEY');
            if ($key === null) { $key = Env::get('GUARDIAN_ENCRYPTION_KEY'); }
            if (!$key) {
                $key = bin2hex(random_bytes(32));
                // La clé est générée par défaut. L'administrateur peut
                // fournir une clé existante via une saisie masquée, jamais
                // via une option CLI visible dans la liste des processus.
                if ($input->isInteractive() && !$input->getOption('automatic')) {
                    $choice = new Question('Clé AES : Entrée pour générer automatiquement, ou saisir 64 caractères hexadécimaux (saisie masquée) : ', '');
                    $choice->setHidden(true);
                    $choice->setHiddenFallback(false);
                    $manual = (string) $helper->ask($input,$output,$choice);
                    if ($manual !== '') { $key = $manual; }
                }
            }
            // Si Dotenv et une variable réelle d'environnement divergent,
            // ne pas créer un coffre dont la clé changerait après redémarrage.
            $runtimeKey=Env::get('GUARDIAN_ENCRYPTION_KEY');
            if ($runtimeKey !== '' && $runtimeKey !== $key) {
                throw new \RuntimeException('GUARDIAN_ENCRYPTION_KEY diffère entre l’environnement PHP et .env.local. Corriger avant initialisation.');
            }
            if (!preg_match('/^[a-f0-9]{64}$/Di', $key)) {
                throw new \RuntimeException('Clé existante invalide : doit être exactement 64 caractères hexadécimaux.');
            }
            if (is_file($routeFile) && strpos((string) file_get_contents($routeFile), 'OmegaGuardian generated route v1') === false) {
                throw new \RuntimeException('La route config/routes/guardian.php existe et ne semble pas générée par Guardian. Aucune modification.');
            }
            if (is_link($routeImport) || (file_exists($routeImport) && (string)file_get_contents($routeImport) !== "# OmegaGuardian generated route import v1\nomega_guardian_php:\n    resource: guardian.php\n    type: php\n")) {
                throw new \RuntimeException('Configuration de routage Guardian déjà occupée : '.$routeImport);
            }
            if (is_file($profilerFile) && strpos((string) file_get_contents($profilerFile), 'OmegaGuardian generated profiler v1') === false) {
                throw new \RuntimeException('Configuration profiler déjà occupée : '.$profilerFile);
            }
            // Ecriture des paramètres AVANT l'initialisation du stockage.
            // Le fichier reste hors /public et reçoit des droits restrictifs.
            $envData = $existing;
            foreach (array(
                'GUARDIAN_LOGIN_PATH'=>$route,
                'GUARDIAN_ENCRYPTION_KEY'=>$key,
                'GUARDIAN_COLLECTION_BRIDGE'=>'1',
                'GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED'=>'1',
                'GUARDIAN_SHOW_PHP_VERSION'=>'0'
            ) as $name=>$value) {
                if ($this->readVar($envData,$name) === null) {
                    $envData .= (substr($envData, -1) === "\n" || $envData === '' ? '' : "\n").$name.'='.$value."\n";
                }
            }
            // Installation neuve : ajout atomique de la configuration absente.
            $configurationChanged = true;
            $this->atomicWrite($envFile, $envData, 0600);
            $routeContent = <<<'PHP'
<?php
// OmegaGuardian generated route v1
// Chemin lu dans .env.local : jamais de valeur secrète dans ce fichier.
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
return function (RoutingConfigurator $routes) {
    // Harmless even if an old route cache survives package removal.
    if (!class_exists(\OmegaGuardian\ProfilerShield\Controller\GuardianController::class)) { return; }
    $path = \OmegaGuardian\ProfilerShield\Util\Env::get('GUARDIAN_LOGIN_PATH');
    if (preg_match('~^/[a-zA-Z0-9/_-]{24,180}$~D', $path)) {
        $routes->add('omega_guardian_entry', $path)
            ->controller('omega_guardian.controller::entry')
            ->methods(array('GET','POST'));
    }
};
PHP;
            if (!is_file($routeFile)) {$this->atomicWrite($routeFile, $routeContent."\n", 0644);}
            // Symfony 5.4's default MicroKernel imports YAML routes only.
            if (!is_file($routeImport)) {$this->atomicWrite($routeImport, "# OmegaGuardian generated route import v1\nomega_guardian_php:\n    resource: guardian.php\n    type: php\n", 0644);}
            // La toolbar reste activée au niveau de Symfony, mais le profiler
            // ne collecte rien par défaut. Guardian active le collecteur
            // UNIQUEMENT pour les sessions autorisées (bridge). Sans profil
            // valide, Symfony n'a pas de token à injecter dans la toolbar.
            // Vérifier les ordres de listeners sur chaque version de Symfony.
            // Jamais de collect_parameter accessible publiquement.
            $version = defined('\\Symfony\\Component\\HttpKernel\\Kernel::VERSION')
                ? \Symfony\Component\HttpKernel\Kernel::VERSION : '5.4';
            $newToolbar = version_compare($version,'7.3','>=');
            $toolbarYaml = $newToolbar ? "    toolbar:\n        enabled: true\n" : "    toolbar: true\n";
            $yaml = "# OmegaGuardian generated profiler v1\n"
                ."# Dev seulement : le profiler n'est JAMAIS une sécurité de production.\n"
                ."web_profiler:\n".$toolbarYaml."    intercept_redirects: false\n"
                ."framework:\n    profiler:\n        collect: false\n";
            // La collecte globale reste désactivée. Une ancienne installation
            // avec GUARDIAN_COLLECTION_BRIDGE=0 peut l'activer après audit et
            // tests d'intégration dans son environnement particulier.
            if (class_exists('Symfony\Bundle\WebProfilerBundle\WebProfilerBundle')) {
                $this->atomicWrite($profilerFile, $yaml, 0644);
            } else {
                $output->writeln('<comment>WebProfilerBundle absent : aucune configuration toolbar créée.</comment>');
            }
            // La nouvelle clé a été écrite dans .env.local mais l’instance
            // Symfony actuelle n'a pas forcément rechargé Dotenv.
            // Évite qu'un "git add ." dans l'application hôte verse
            // accidentellement le coffre (même chiffré) dans le dépôt.
            $ignoreFile=$base.'/.gitignore';
            if (is_link($ignoreFile)) { throw new \RuntimeException('.gitignore symbolique : configuration manuelle nécessaire.'); }
            $gitignore=is_file($ignoreFile) ? (string) file_get_contents($ignoreFile) : '';
            if (strpos($gitignore, '/.guardian-shield/') === false) {
                $gitignore.=(substr($gitignore,-1)==="\n" || $gitignore==='' ? '' : "\n")
                    ."# ΩGuardian : coffre persistant chiffré (ne jamais versionner)\n/.guardian-shield/\n";
            }
            if (strpos($gitignore, '/.env.local') === false) {
                $gitignore.=(substr($gitignore,-1)==="\n" || $gitignore==='' ? '' : "\n")
                    ."# ΩGuardian : chemins et clés privées\n/.env.local\n";
            }
            $this->atomicWrite($ignoreFile,$gitignore,0644);
            $this->store->configureKey($key);
            $this->store->initialize();
            // Track only changes made by this explicit CLI command.
            \OmegaGuardian\ProfilerShield\Installation\Uninstaller::writeReceipt($base,$before);
            // La zone par défaut vient du PHP qui exécute le setup, et non
            // d'une valeur France figée. On ne remplace pas les choix existants.
            if (!is_file($this->store->root().'/settings.json')) {
                $this->store->change('settings', function ($state) {
                    $state['timezone'] = \OmegaGuardian\ProfilerShield\Util\ServerClock::initialTimezone();
                    return $state;
                });
                $output->writeln('<info>Fuseau initial Guardian : '.\OmegaGuardian\ProfilerShield\Util\ServerClock::initialTimezone().' (OS si détectable, sinon PHP CLI).</info>');
                $output->writeln('<comment>Vérifier ensuite le fuseau PHP-FPM dans l’interface, car il peut différer du CLI.</comment>');
            }

            // La création d'identité fait partie intégrante de l'installation.
            // Une route privée et une clé ne suffisent PAS à rendre Guardian
            // utilisable : ne jamais afficher « installation terminée » si
            // aucun administrateur actif ne peut se connecter.
            $administrator = $this->activeAdministrator();
            if ($administrator === null && !$input->getOption('no-admin')) {
                if ($input->getOption('automatic')) {
                    // Le mot de passe initial ne transite ni par argv ni par stdout.
                    // Il est conservé dans un fichier local 0600, à supprimer
                    // après lecture. Aucun changement pour les comptes existants.
                    $administrator = $this->createAutomaticAdministrator($output);
                } else {
                    if (!$input->isInteractive()) {
                        $output->writeln('<error>Initialisation incomplète : aucun administrateur actif.</error>');
                        $output->writeln('Lancer php bin/console guardian:setup dans un terminal interactif.');
                        return 1;
                    }
                    $output->writeln('');
                    $output->writeln('<info>Étape 3/3 — Création du compte administrateur</info>');
                    $output->writeln('Ce compte est indépendant des utilisateurs de votre application Symfony.');
                    $administrator = $this->createAdministrator($input, $output, $helper);
                }
                if ($administrator === null) {
                    // Les fichiers de configuration sont conservés pour que
                    // setup puisse reprendre sans réinitialiser les secrets.
                    $output->writeln('<error>Installation incomplète : aucun compte administrateur créé.</error>');
                    $output->writeln('Corriger les informations puis relancer php bin/console guardian:setup.');
                    return 1;
                }
            }
            if ($administrator === null) {
                // Cette exemption est destinée uniquement au provisionnement
                // automatisé, jamais à une installation interactive ordinaire.
                $output->writeln('<comment>Configuration préparée (mode --no-admin) : l’accès au portail est impossible sans compte.</comment>');
                $output->writeln('Finaliser avec php bin/console guardian:install, puis guardian:doctor.');
                return 0;
            }
            $output->writeln('');
            $output->writeln('<info>ΩGuardian — Installation terminée.</info>');
            $output->writeln('Compte administrateur : '.$administrator);
            $output->writeln($input->getOption('automatic')
                ? 'Mot de passe initial : consulter le fichier privé indiqué ci-dessus, puis le supprimer.'
                : 'Mot de passe : celui que vous avez choisi (jamais enregistré en clair).');
            $output->writeln('Route privée : '.$route);
            $output->writeln('Ouvrir https://VOTRE-HÔTE'.$route.' puis se connecter avec ce compte.');
            $output->writeln('Diagnostic : php bin/console guardian:doctor');
            $output->writeln('Désinstallation : php bin/console guardian:uninstall, puis composer remove --dev omegaguardian/profiler-shield.');
            return 0;
        } catch (\Throwable $e) {
            $output->writeln('<error>Guardian setup interrompu : '.$e->getMessage().'</error>');
            return 1;
        } finally {
            if ($configurationChanged) {\OmegaGuardian\ProfilerShield\Util\ConfigurationChange::finish();}
        }
    }

    /**
     * Recherche un compte actif doté du rôle admin, et non simplement un compte
     * nommé « admin ». Lors d'une relance, évite d'exiger un mot de passe neuf.
     * L'identifiant retrouvé est affiché afin d'éviter les comptes « fantômes ».
     */
    private function activeAdministrator()
    {
        foreach ($this->accounts->allPublic() as $account) {
            if ($account['role'] === 'admin' && !$account['disabled']) {
                return $account['name'];
            }
        }
        return null;
    }

    /**
     * Dialogue CLI de premier démarrage. Le nom et le mot de passe sont saisis
     * séparément ; le mot de passe n'apparaît ni dans l'historique shell,
     * ni dans le fichier .env, ni dans les sorties de la commande.
     *
     * Une double saisie protège des fautes de frappe lors de l'initialisation.
     * En cas d'erreur ou d'annulation, l'installation reste relançable et
     * n'annonce jamais faussement « terminée ».
     */
    /**
     * Provisionnement autonome pour composer require en développement.
     * Le secret est disponible uniquement sur le disque local, jamais dans
     * les journaux Composer ou l'historique shell. Un fichier déjà existant
     * bloque l'opération pour éviter d'écraser un premier secret non lu.
     */
    private function createAutomaticAdministrator(OutputInterface $output)
    {
        $username = 'admin';
        if ($this->accounts->find($username) !== null) {
            throw new \RuntimeException('Le compte admin existe mais aucun administrateur actif n’est disponible : rétablissement manuel requis.');
        }
        $credentialsPath = $this->store->root().'/initial-admin-credentials.txt';
        if (is_file($credentialsPath) || is_link($credentialsPath)) {
            throw new \RuntimeException('Ancien fichier de première connexion détecté : le supprimer après vérification avant de relancer setup.');
        }
        $password = \OmegaGuardian\ProfilerShield\Security\PasswordPolicy::generate((new \OmegaGuardian\ProfilerShield\Security\Settings($this->store))->all()['password_policy']);
        $handle = @fopen($credentialsPath, 'x');
        if ($handle === false) {
            throw new \RuntimeException('Impossible de créer les identifiants de première connexion dans le coffre.');
        }
        @chmod($credentialsPath, 0600);
        try {
            $content = "\xCE\xA9Guardian - première connexion uniquement\n"
                ."Identifiant : ".$username."\n"
                ."Mot de passe : ".$password."\n"
                ."À supprimer après connexion et changement du mot de passe.\n";
            $written = fwrite($handle, $content);
            if ($written !== strlen($content) || !fflush($handle)) {
                throw new \RuntimeException('Écriture des identifiants initiaux incomplète.');
            }
            $this->accounts->add($username, $password, 'admin', true);
        } catch (\Throwable $e) {
            fclose($handle);
            @unlink($credentialsPath);
            throw $e;
        }
        fclose($handle);
        $output->writeln('<info>Administrateur initial créé sans interaction.</info>');
        $output->writeln('<comment>Première connexion : changement du mot de passe obligatoire avant toute configuration ou diagnostic. Retrouvez ces instructions avec php bin/console guardian:access.</comment>');
        $output->writeln('<comment>Identifiants initiaux (fichier privé 0600, jamais envoyé à un serveur externe) : '.$credentialsPath.'</comment>');
        $output->writeln('<comment>Lire ce fichier localement puis le supprimer immédiatement après le premier accès et le changement du mot de passe.</comment>');
        return $username;
    }

    private function createAdministrator(InputInterface $input, OutputInterface $output, $helper)
    {
        $defaultName = (string) $input->getOption('username');
        for ($attempt = 0; $attempt < 3; ++$attempt) {
            $question = new Question('Identifiant administrateur ['.$defaultName.'] : ', $defaultName);
            $username = strtolower(trim((string) $helper->ask($input, $output, $question)));
            if (!preg_match('/^[a-z0-9._-]{3,64}$/D', $username)) {
                $output->writeln('<error>Identifiant invalide : 3 à 64 caractères, lettres, chiffres, points, tirets ou _.</error>');
                continue;
            }
            if ($this->accounts->find($username) !== null) {
                $output->writeln('<error>Ce compte existe déjà. Choisir un autre identifiant.</error>');
                continue;
            }
            $first = new Question('Mot de passe (politique active, saisie masquée) : ');
            $first->setHidden(true);
            $first->setHiddenFallback(false);
            $password = $helper->ask($input, $output, $first);
            try { \OmegaGuardian\ProfilerShield\Security\PasswordPolicy::validate($password,(new \OmegaGuardian\ProfilerShield\Security\Settings($this->store))->all()['password_policy']); }
            catch (\InvalidArgumentException $e) {$output->writeln('<error>'.(new \OmegaGuardian\ProfilerShield\I18n\UiTranslator())->trans($e->getMessage(),'fr_FR').'</error>');continue;}
            $second = new Question('Confirmer le mot de passe (saisie masquée) : ');
            $second->setHidden(true);
            $second->setHiddenFallback(false);
            $confirmation = $helper->ask($input, $output, $second);
            if (!is_string($confirmation) || !hash_equals($password, $confirmation)) {
                $output->writeln('<error>Les deux mots de passe ne correspondent pas.</error>');
                continue;
            }
            try {
                $this->accounts->add($username, $password, 'admin');
            } catch (\InvalidArgumentException $e) {
                $output->writeln('<error>'.$e->getMessage().'</error>');
                continue;
            }
            $output->writeln('<info>Compte administrateur « '.$username.' » créé.</info>');
            return $username;
        }
        return null;
    }

    private function readVar($content, $name)
    {
        if (preg_match('/^'.preg_quote($name,'/').'=(.*)$/m', $content, $matches)) {
            return trim($matches[1], " \t\r\n\"'");
        }
        return null;
    }

    private function atomicWrite($path, $content, $mode)
    {
        $folder=dirname($path);
        if (!is_dir($folder) && !mkdir($folder, 0700, true) && !is_dir($folder)) {
            throw new \RuntimeException('Répertoire non créable : '.$folder);
        }
        $tmp=tempnam($folder,'.guardian-setup-');
        if ($tmp===false) { throw new \RuntimeException('Fichier temporaire impossible.'); }
        try {
            if (file_put_contents($tmp,$content,LOCK_EX)===false) {
                throw new \RuntimeException('Écriture impossible : '.$path);
            }
            chmod($tmp,$mode);
            if (!rename($tmp,$path)) { throw new \RuntimeException('Renommage impossible : '.$path); }
        } finally { if (is_file($tmp)) { unlink($tmp); } }
    }
}
