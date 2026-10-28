<?php
namespace OmegaGuardian\ProfilerShield\Command;

use OmegaGuardian\ProfilerShield\Installation\Uninstaller;
use OmegaGuardian\ProfilerShield\Util\Env;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/** Local cleanup before removing the Composer package. */
final class UninstallCommand extends Command
{
    public function __construct(private string $projectDirectory) { parent::__construct(); }

    protected function configure(): void
    {
        $this->setName('guardian:uninstall')
            ->setDescription('Prépare le retrait : configuration attribuable à Guardian, coffre conservé par défaut.')
            ->addOption('purge',null,InputOption::VALUE_NONE,'Demander aussi l’effacement définitif des comptes, paramètres, secrets MFA, journaux et clé Guardian gérée.')
            ->addOption('force',null,InputOption::VALUE_NONE,'Confirmer explicitement la purge, y compris sans interaction.');
    }

    protected function execute(InputInterface $input,OutputInterface $output): int
    {
        $changed=false;
        try {
            if (Env::get('APP_ENV')==='prod') {throw new \RuntimeException('Exécuter le retrait depuis l’environnement dev, après arrêt des workers de développement.');}
            if ($input->getOption('force') && !$input->getOption('purge')) {throw new \RuntimeException('--force nécessite --purge.');}
            $purge=(bool)$input->getOption('purge');
            if ($purge && !$input->getOption('force')) {
                if (!$input->isInteractive()) {throw new \RuntimeException('Purge non interactive refusée sans --purge --force. Aucune donnée modifiée.');}
                $confirmed=$this->getHelper('question')->ask($input,$output,new ConfirmationQuestion('Effacer définitivement les comptes, paramètres, secrets MFA, sessions, journaux et clé Guardian gérée ? [y/N] ',false));
                if (!$confirmed) {$output->writeln('<comment>Purge annulée. Aucune donnée ni configuration modifiée.</comment>');return 1;}
            }
            $root=rtrim($this->projectDirectory,'/');
            if (!is_file($root.'/composer.json') || !is_file($root.'/bin/console')) {throw new \RuntimeException('Racine Symfony introuvable. Aucun nettoyage effectué.');}
            $output->writeln('Projet cible : '.$root);
            $changed=true;
            (new Uninstaller())->execute($root,static function($line) use($output){$output->writeln($line);},$purge);
            // An unsafe external/unknown vault must never be reported as purged.
            if ($purge && (is_dir($root.'/.guardian-shield') || Env::get('GUARDIAN_STORAGE_DIR')!=='')) {
                $output->writeln('<error>Effacement complet non réalisé. Conserver la clé et traiter le coffre signalé manuellement.</error>');
                $output->writeln('La configuration générée a pu être retirée ; examiner les messages précédents.');
                return 1;
            }
            $output->writeln('<info>Préparation du retrait terminée.</info>');
            $output->writeln('Suite : composer remove --dev omegaguardian/profiler-shield');
            $output->writeln('Puis : php bin/console cache:clear --env=dev ; redémarrer les workers de développement.');
            $output->writeln('Vérifier le profiler natif uniquement dans un environnement privé.');
            return 0;
        } catch (\Throwable $error) {
            $output->writeln('<error>Retrait interrompu : '.$error->getMessage().'</error>');
            return 1;
        } finally {
            if ($changed) {\OmegaGuardian\ProfilerShield\Util\ConfigurationChange::finish();}
        }
    }
}
