<?php
namespace OmegaGuardian\ProfilerShield\Command;

use OmegaGuardian\ProfilerShield\Storage\FileStore;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Expédie la file hors du cycle HTTP ; peut être exécuté par cron.
 * Le stockage conserve l'alerte tant que l'expédition a échoué.
 */
final class AlertCommand extends Command
{
    private $store;
    public function __construct(FileStore $store) { $this->store=$store;parent::__construct(); }
    protected function configure(): void
    { $this->setName('guardian:alerts:flush')->setDescription('Expédie les alertes Guardian en attente via mail().'); }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $settings=$this->store->read('settings');
        $email=isset($settings['alert_email'])?$settings['alert_email']:'';
        if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
            $output->writeln('<comment>Pas de destinataire valide ; file inchangée.</comment>');return 1;
        }
        $sent=0;$failed=0;
        $this->store->change('alerts',function ($alerts) use ($email,&$sent,&$failed) {
            foreach ($alerts as &$a) {
                if (!empty($a['delivered'])) {continue;}
                $e=$a['entry'];
                $body="Événement : ".$e['event']."\nCompte : ".$e['actor']."\nDate UTC : ".$e['at']."\n";
                // Aucune URL secrète, cookie ou jeton dans le message.
                if (@mail($email,'[OmegaGuardian] Alerte de sécurité',$body,'Content-Type: text/plain; charset=UTF-8')) {
                    $a['delivered']=true;$sent++;
                } else { $failed++; }
            }
            unset($a);
            return $alerts;
        });
        $output->writeln('Alertes expédiées : '.$sent.' ; échecs : '.$failed);
        return $failed > 0 ? 1 : 0;
    }
}
