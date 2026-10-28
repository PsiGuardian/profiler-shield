<?php
namespace OmegaGuardian\ProfilerShield\Security;

use OmegaGuardian\ProfilerShield\Storage\FileStore;

/**
 * Audit sans URLs ni cookies, file d'alertes non bloquante pour le site métier.
 * L'intégrité totale du journal ne peut être assurée sur le même hôte compromis ;
 * envoyer les empreintes vers un stockage externe pour les cas sensibles.
 */
final class Audit
{
    private $store;
    public function __construct(FileStore $store) { $this->store = $store; }

    public function record($event, $actor = '', array $metadata = array(), $alert = false)
    {
        $settings=$this->store->read('settings');
        $level=isset($settings['audit_level'])?$settings['audit_level']:'standard';
        // Le niveau minimal conserve les événements critiques.
        if ($level==='minimal' && in_array($event,array('login_failed','login_success','jit_enabled'),true)) { return; }
        $allowedKeys = array('reason','duration','result','target');
        $meta = array_intersect_key($metadata, array_flip($allowedKeys));
        foreach ($meta as $key=>$value) { $meta[$key] = substr((string) $value,0,160); }
        $entry = array('at'=>gmdate('c'),'event'=>substr($event,0,48), 'actor'=>substr($actor,0,64), 'meta'=>$meta);
        $this->store->change('audit', function ($all) use ($entry,$settings) {
            $last = $all ? end($all)['digest'] : str_repeat('0',64);
            $entry['previous'] = $last;
            $entry['digest'] = hash('sha256', $last.json_encode($entry));
            $all[] = $entry;
            $days=isset($settings['audit_retention_days'])?(int)$settings['audit_retention_days']:30;
            $cutoff=time()-max(1,$days)*86400;
            $all=array_values(array_filter($all,function ($item) use ($cutoff) {return strtotime($item['at']) >= $cutoff;}));
            return array_slice($all, -5000);
        });
        if ($alert) {
            $this->store->change('alerts', function ($all) use ($entry) {
                $all[] = array('entry'=>$entry,'delivered'=>false);
                return array_slice($all,-1000);
            });
        }
    }
}
