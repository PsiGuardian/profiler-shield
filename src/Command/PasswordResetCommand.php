<?php
namespace OmegaGuardian\ProfilerShield\Command;

use OmegaGuardian\ProfilerShield\Security\Accounts;
use OmegaGuardian\ProfilerShield\Security\Sessions;
use OmegaGuardian\ProfilerShield\Security\Audit;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/** Mot de passe récupérable UNIQUEMENT depuis le terminal de l'hôte. */
final class PasswordResetCommand extends Command
{
    private $accounts;private $sessions;private $audit;
    public function __construct(Accounts $accounts,Sessions $sessions,Audit $audit)
    { $this->accounts=$accounts;$this->sessions=$sessions;$this->audit=$audit;parent::__construct(); }
    protected function configure(): void
    {
        $this->setName('guardian:password:reset')->setDescription('Réinitialise localement un mot de passe et invalide les sessions du compte.')
            ->addArgument('username',InputArgument::REQUIRED,'Identifiant de compte');
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username=strtolower(trim((string)$input->getArgument('username')));
        if (!$this->accounts->find($username)) { $output->writeln('<error>Compte absent.</error>');return 1; }
        $q=new Question('Nouveau mot de passe (politique Guardian active) : ');
        $q->setHidden(true);$q->setHiddenFallback(false);
        $pass=$this->getHelper('question')->ask($input,$output,$q);
        try { $this->accounts->setPassword($username,(string)$pass); }
        catch (\InvalidArgumentException $e) { $output->writeln('<error>Mot de passe refusé par la politique active : '.(new \OmegaGuardian\ProfilerShield\I18n\UiTranslator())->trans($e->getMessage(),'fr_FR').'</error>');return 1; }
        $this->sessions->invalidateUser($username);
        $this->audit->record('password_reset_cli',$username,array(),true);
        $output->writeln('<info>Mot de passe changé, toutes les sessions ont été révoquées.</info>');
        return 0;
    }
}
