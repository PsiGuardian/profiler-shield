<?php
namespace OmegaGuardian\ProfilerShield\Command;
use OmegaGuardian\ProfilerShield\Storage\FileStore;
use OmegaGuardian\ProfilerShield\Security\Audit;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputInterface,InputArgument,InputOption};
use Symfony\Component\Console\Output\OutputInterface;
final class LoginUnlockCommand extends Command
{
    public function __construct(private FileStore $store,private Audit $audit) {parent::__construct();}
    protected function configure(): void {$this->setName('guardian:login:unlock')->setDescription('Récupération locale des blocages de connexion.')->addArgument('username',InputArgument::OPTIONAL,'Compte à débloquer')->addOption('ip',null,InputOption::VALUE_REQUIRED,'Origine réseau à débloquer également')->addOption('all',null,InputOption::VALUE_NONE,'Réinitialiser tous les compteurs de connexion');}
    protected function execute(InputInterface $input,OutputInterface $output): int
    {
        $username=strtolower(trim((string)$input->getArgument('username')));$ip=(string)$input->getOption('ip');$all=(bool)$input->getOption('all');
        if ((!$all && !preg_match('/^[a-z0-9._-]{3,64}$/D',$username)) || ($ip!=='' && !filter_var($ip,FILTER_VALIDATE_IP))) {$output->writeln('<error>Indiquer un compte valide ou --all, et éventuellement --ip ADRESSE.</error>');return 1;}
        if ($ip!=='') {$ip=inet_ntop(inet_pton($ip));}
        $this->store->change('attempts',function($state) use($username,$ip,$all){if($all)return [];unset($state[hash('sha256','account|'.$username)]);if($ip!=='')unset($state[hash('sha256','origin|'.$ip)]);return $state;});
        $this->audit->record('login_unlocked_cli',$all?'local-cli':$username,[],true);
        $output->writeln('<info>Compteurs réinitialisés. Les mots de passe, sessions et comptes restent inchangés.</info>');
        if (!$all && $ip==='') {$output->writeln('Un blocage par origine peut subsister : utiliser --ip ou --all depuis ce terminal.');}return 0;
    }
}
