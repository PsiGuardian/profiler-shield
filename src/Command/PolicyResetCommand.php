<?php
namespace OmegaGuardian\ProfilerShield\Command;

use OmegaGuardian\ProfilerShield\Security\Settings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/** Récupération LOCALE après un verrouillage réseau ou horaire involontaire. */
final class PolicyResetCommand extends Command
{
    private $settings;
    public function __construct(Settings $settings) { $this->settings=$settings;parent::__construct(); }
    protected function configure(): void
    { $this->setName('guardian:policy:reset')->setDescription('Annule les restrictions IP/horaire/JIT, depuis le serveur uniquement.'); }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->settings->set(array('ip_allowlist'=>'','schedule_enabled'=>false,'jit_enabled'=>false,'emergency_enabled'=>false));
        $output->writeln('<info>Restrictions opérationnelles réinitialisées. Le contrôle d’authentification du profiler reste actif.</info>');
        return 0;
    }
}
