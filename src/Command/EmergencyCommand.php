<?php
namespace OmegaGuardian\ProfilerShield\Command;

use OmegaGuardian\ProfilerShield\Security\Accounts;
use OmegaGuardian\ProfilerShield\Security\Audit;
use OmegaGuardian\ProfilerShield\Storage\FileStore;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Génère des codes one-shot, uniquement via accès terminal au serveur.
 * Les codes ne seront plus affichés après la génération : l'opérateur
 * doit les stocker dans son gestionnaire de secrets hors machine.
 */
final class EmergencyCommand extends Command
{
    private $store;private $accounts;private $audit;
    public function __construct(FileStore $store,Accounts $accounts,Audit $audit)
    { $this->store=$store;$this->accounts=$accounts;$this->audit=$audit;parent::__construct(); }
    protected function configure(): void
    {
        $this->setName('guardian:emergency')->setDescription('Génère 5 codes de récupération uniques pour un compte.')
            ->addArgument('username',InputArgument::REQUIRED,'Compte local Guardian');
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username=strtolower($input->getArgument('username'));
        if (!$this->accounts->find($username)) { $output->writeln('<error>Compte introuvable.</error>');return 1; }
        $codes=array();$hashes=array();
        for ($i=0;$i<5;$i++) {
            $code=strtoupper(bin2hex(random_bytes(12)));
            $codes[]=$code;$hashes[]=password_hash($code,PASSWORD_DEFAULT);
        }
        $this->store->change('emergency_codes',function ($all) use ($username,$hashes) { $all[$username]=$hashes;return $all; });
        $this->audit->record('emergency_codes_rotated',$username,array(),true);
        $output->writeln('<comment>Conserver ces codes hors serveur. Ils sont affichés UNE FOIS :</comment>');
        foreach ($codes as $code) { $output->writeln($code); }
        return 0;
    }
}
