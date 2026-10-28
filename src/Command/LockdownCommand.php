<?php
namespace OmegaGuardian\ProfilerShield\Command;

use OmegaGuardian\ProfilerShield\Storage\FileStore;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

/** Verrouillage global des diagnostics, jamais du site métier. */
final class LockdownCommand extends Command
{
    private $store;
    public function __construct(FileStore $store) { $this->store=$store;parent::__construct(); }
    protected function configure(): void
    {
        $this->setName('guardian:lockdown')->setDescription('Ferme ou rouvre le profiler sans toucher au site métier.')
            ->addArgument('state',InputArgument::REQUIRED,'on ou off');
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $state=$input->getArgument('state');
        if (!in_array($state,array('on','off'),true)) {
            $output->writeln('<error>Valeurs permises : on, off.</error>');return 1;
        }
        $this->store->change('control',function ($control) use ($state) {
            $control['lockdown']=$state==='on';
            $control['changed_at']=gmdate('c');
            return $control;
        });
        if ($state==='on') {
            // Révocation complète pour empêcher les anciennes sessions de
            // redevenir valides au moment où le lockdown sera levé.
            $this->store->change('sessions',function ($sessions) { return array(); });
        }
        $output->writeln($state==='on'?'Diagnostics verrouillés ; site principal inchangé.':'Lockdown levé ; authentification toujours obligatoire.');
        return 0;
    }
}
