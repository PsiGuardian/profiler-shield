<?php
namespace OmegaGuardian\ProfilerShield\Command;

use OmegaGuardian\ProfilerShield\Security\Accounts;
use OmegaGuardian\ProfilerShield\Storage\FileStore;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/** Installation atomique, depuis CLI uniquement ; aucune route publique bootstrap. */
final class InstallCommand extends Command
{
    private $accounts; private $store;
    public function __construct(Accounts $accounts, FileStore $store)
    {
        $this->accounts=$accounts;$this->store=$store;
        parent::__construct();
    }
    protected function configure(): void
    {
        $this->setName('guardian:install')
            ->setDescription('Initialise Guardian et crée un administrateur autonome.')
            ->addOption('username',null,InputOption::VALUE_REQUIRED,'Identifiant administrateur','admin');
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->store->initialize();
            $name=$input->getOption('username');
            if ($this->accounts->find($name)) { $output->writeln('<error>Compte déjà existant.</error>');return 1; }
            $helper=$this->getHelper('question');
            $question=new Question('Mot de passe admin (politique active) : ');
            $question->setHidden(true);
            $question->setHiddenFallback(false);
            $password=$helper->ask($input,$output,$question);
            if (!is_string($password)) { throw new \RuntimeException('Mot de passe absent.'); }
            $this->accounts->add($name,$password,'admin');
            $output->writeln('<info>Compte créé. Configurer un chemin secret GUARDIAN_LOGIN_PATH et HTTPS avant activation.</info>');
            $output->writeln('Vérifier : php bin/console guardian:doctor');
            return 0;
        } catch (\Throwable $e) {
            $output->writeln('<error>Installation interrompue : '. $e->getMessage().'</error>');return 1;
        }
    }
}
