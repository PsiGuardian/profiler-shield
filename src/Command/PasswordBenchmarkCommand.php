<?php
namespace OmegaGuardian\ProfilerShield\Command;
use OmegaGuardian\ProfilerShield\Security\PasswordPolicy;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputInterface,InputOption};
use Symfony\Component\Console\Output\OutputInterface;
final class PasswordBenchmarkCommand extends Command
{
    protected function configure(): void {$this->setName('guardian:password:benchmark')->setDescription('Mesure locale de vérifications synthétiques ; aucun mot de passe utilisateur n’est testé.')->addOption('iterations',null,InputOption::VALUE_REQUIRED,'1 à 10 vérifications',3);}
    protected function execute(InputInterface $input,OutputInterface $output): int
    {
        $raw=(string)$input->getOption('iterations');if(!preg_match('/^(?:[1-9]|10)$/D',$raw)){$output->writeln('<error>1 à 10 itérations.</error>');return 1;}$count=(int)$raw;
        $password=PasswordPolicy::generate(PasswordPolicy::defaults());$hash=PasswordPolicy::hash($password);$start=hrtime(true);
        for($i=0;$i<$count;$i++){if(!password_verify($password,$hash))return 1;}
        $seconds=max(0.000001,(hrtime(true)-$start)/1e9);$output->writeln(json_encode(['algorithm'=>password_get_info($hash)['algoName'],'iterations'=>$count,'seconds'=>$seconds,'verifications_per_second'=>$count/$seconds,'scope'=>'single local CPU worker; not a GPU or attacker benchmark'],JSON_PRETTY_PRINT));return 0;
    }
}
