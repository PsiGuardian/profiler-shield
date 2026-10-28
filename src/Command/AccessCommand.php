<?php
namespace OmegaGuardian\ProfilerShield\Command;
use OmegaGuardian\ProfilerShield\Storage\FileStore;
use OmegaGuardian\ProfilerShield\Util\Env;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputInterface,InputOption};
use Symfony\Component\Console\Output\OutputInterface;
final class AccessCommand extends Command
{
    public function __construct(private FileStore $store) {parent::__construct();}
    protected function configure(): void {$this->setName('guardian:access')->setDescription('Indique le panneau privé et la procédure de première connexion, sans afficher le mot de passe.')->addOption('base-url',null,InputOption::VALUE_REQUIRED,'Origine HTTPS réelle, facultative');}
    protected function execute(InputInterface $input,OutputInterface $output): int
    {
        $path=Env::get('GUARDIAN_LOGIN_PATH');if (!$path) {$output->writeln('<error>Guardian non initialisé : php bin/console guardian:setup --automatic --env=dev</error>');return 1;}
        $base=(string)$input->getOption('base-url');
        if ($base!=='') {$parts=parse_url($base);if (!is_array($parts) || ($parts['scheme']??'')!=='https' || empty($parts['host']) || isset($parts['user'],$parts['pass']) || isset($parts['user']) || isset($parts['query']) || isset($parts['fragment']) || preg_match('/[\x00-\x20<>]/',$base)) {$output->writeln('<error>Fournir une origine HTTPS valide, sans identifiants, requête ou fragment.</error>');return 1;}}
        $url=($base!==''?rtrim($base,'/'):'https://VOTRE-HOTE').$path;
        $output->writeln('Panneau privé : '.\Symfony\Component\Console\Formatter\OutputFormatter::escape($url));
        $credentials=$this->store->root().'/initial-admin-credentials.txt';
        if (is_file($credentials)) {$output->writeln('Première connexion : compte admin, mot de passe généré dans le fichier privé : '.\Symfony\Component\Console\Formatter\OutputFormatter::escape($credentials));$output->writeln('Lire ce fichier localement, ouvrir le panneau, puis remplacer le mot de passe. Les diagnostics restent fermés jusque-là.');}
        else {$output->writeln('Utiliser vos identifiants Guardian. Récupération locale : php bin/console guardian:password:reset admin');}
        $output->writeln('Retrait : composer remove --dev omegaguardian/profiler-shield (coffre et clé conservés).');return 0;
    }
}
