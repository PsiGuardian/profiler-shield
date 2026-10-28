<?php
namespace OmegaGuardian\ProfilerShield\Command;

use OmegaGuardian\ProfilerShield\Security\Accounts;
use OmegaGuardian\ProfilerShield\Security\Sessions;
use OmegaGuardian\ProfilerShield\Security\Audit;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

/** Récupération MFA uniquement depuis l'accès terminal au serveur. */
final class MfaResetCommand extends Command
{
    private $accounts;private $sessions;private $audit;
    public function __construct(Accounts $accounts,Sessions $sessions,Audit $audit)
    { $this->accounts=$accounts;$this->sessions=$sessions;$this->audit=$audit;parent::__construct(); }
    protected function configure(): void
    {
        $this->setName('guardian:mfa:reset')->setDescription('Réinitialise TOTP et révoque toutes les sessions du compte.')
            ->addArgument('username',InputArgument::REQUIRED,'Compte concerné');
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username=strtolower((string)$input->getArgument('username'));
        try {
            $this->accounts->update($username,function ($user) {
                $user['totp']='';unset($user['last_otp_counter']);return $user;
            });
            $this->sessions->invalidateUser($username);
            $this->audit->record('mfa_reset_cli',$username,array(),true);
            $output->writeln('<info>MFA réinitialisée. Reconfigurer TOTP avant de restaurer l’accès si MFA globale est obligatoire.</info>');
            return 0;
        } catch (\Throwable $e) {
            $output->writeln('<error>Réinitialisation impossible : '.$e->getMessage().'</error>');return 1;
        }
    }
}
