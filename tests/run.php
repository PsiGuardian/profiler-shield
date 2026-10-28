<?php
/**
 * Suite de tests autonome pour le coeur de Guardian.
 * Usage: php tests/run.php
 * Ne remplace PAS les tests d'intégration Composer/Symfony, disponibles dans
 * tests/Integration lorsque les dépendances peuvent être installées.
 */
error_reporting(E_ALL);
$root=dirname(__DIR__);
spl_autoload_register(function ($name) use ($root) {
    $prefix='OmegaGuardian\\ProfilerShield\\';
    if (strpos($name,$prefix)!==0) {return;}
    $path=$root.'/src/'.str_replace('\\','/',substr($name,strlen($prefix))).'.php';
    if (is_file($path)) {require $path;}
});
use OmegaGuardian\ProfilerShield\Storage\FileStore;
use OmegaGuardian\ProfilerShield\Security\Settings;
use OmegaGuardian\ProfilerShield\Security\Accounts;
use OmegaGuardian\ProfilerShield\Security\Policy;
use OmegaGuardian\ProfilerShield\Security\Totp;
use OmegaGuardian\ProfilerShield\Security\Audit;
use OmegaGuardian\ProfilerShield\Util\Html;

$checks=0;
function check($expr, $description)
{
    global $checks;
    if (!$expr) { throw new RuntimeException('ÉCHEC: '.$description); }
    $checks++;
    echo '[PASS] '.$description."\n";
}
function deleteTree($dir)
{
    if (!is_dir($dir)) {return;}
    foreach (scandir($dir) as $f) {
        if ($f==='.' || $f==='..') {continue;}
        $p=$dir.'/'.$f;
        if (is_dir($p)) {deleteTree($p);} else {@unlink($p);}
    }
    @rmdir($dir);
}
$_SERVER['GUARDIAN_ENCRYPTION_KEY']=bin2hex(random_bytes(32));
$tmp=sys_get_temp_dir().'/guardian-test-'.bin2hex(random_bytes(10));
try {
    $store=new FileStore($tmp);
    check(!$store->ready(),'Stockage non initialisé ne semble pas disponible');
    $store->initialize();
    check($store->ready(),'Stockage initialisé et accessible');
    $store->change('test',function ($data) { $data['counter']=1;return $data; });
    $store->change('test',function ($data) { $data['counter']++;return $data; });
    check($store->read('test')['counter']===2,'Transactions séquentielles cohérentes');
    check((fileperms($tmp.'/test.json') & 0077)===0,'Fichier de données non lisible par le groupe et les autres');

    $settings=new Settings($store);
    check($settings->all()['schedule_enabled']===false,'TimeLock est désactivé par défaut');
    // Contrat 1.0 : aucune normalisation silencieuse des préversions.
    $settings->set(array('weekdays'=>array(1,2,3,4,5),'timezone'=>'America/New_York'));
    check($settings->all()['weekdays'] === array(1,2,3,4,5), 'Planning propre correctement enregistré');
    check($settings->all()['timezone'] === 'America/New_York', 'Fuseau sauvegardé sur un stockage neuf');
    $invalidLegacy = false;
    try { $settings->validate(array('weekend_closed'=>true)); }
    catch (InvalidArgumentException $e) { $invalidLegacy = true; }
    check($invalidLegacy, 'Une ancienne option inconnue est explicitement refusée');
    check(!array_key_exists('weekend_closed', $settings->all()), 'Aucune clé historique générée');
    $settings->set(array('schedule_enabled'=>true,'weekdays'=>array(1,2,3,4,5),'start'=>'09:00','end'=>'18:00','timezone'=>'Europe/Paris'));
    $policy=new Policy($settings,$store);
    $account=array('role'=>'developer','totp'=>'');
    $session=array('emergency_until'=>0,'jit_until'=>0);
    $tuesday=strtotime('2026-10-20 12:00:00 UTC');
    check($policy->allowed($session,$account,$tuesday),'TimeLock autorise une heure de bureau');
    // Régression : samedi coché signifie samedi autorisé, sans second
    // interrupteur caché dans le stockage.
    $saturday=strtotime('2026-10-24 12:00:00 UTC');
    $settings->set(array('weekdays'=>array(1,2,3,4,5,6)));
    check($policy->inSchedule($saturday),'Samedi autorisé si coché dans le planning');
    $settings->set(array('weekdays'=>array(1,2,3,4,5)));
    check(!$policy->inSchedule($saturday),'Samedi refusé lorsqu’il est décoché');
    $night=strtotime('2026-10-20 20:30:00 UTC');
    check(!$policy->allowed($session,$account,$night),'TimeLock refuse une heure non ouvrée');
    $session['emergency_until']=$night+100;
    check($policy->allowed($session,$account,$night),'Une dérogation encore valide autorise la session authentifiée');
    $session['emergency_until']=0;
    $settings->set(array('jit_enabled'=>true));
    check(!$policy->allowed($session,$account,$tuesday),'Mode JIT refuse une session sans créneau');
    $session['jit_until']=$tuesday+120;
    check($policy->allowed($session,$account,$tuesday),'Mode JIT autorise une fenêtre active');

    $settings->set(array('ip_allowlist'=>'192.0.2.0/24, 2001:db8::/32'));
    check($policy->ipAllowed('192.0.2.10'),'Adresse IPv4 incluse dans le CIDR');
    check(!$policy->ipAllowed('198.51.100.3'),'Adresse IPv4 hors CIDR refusée');
    check($policy->ipAllowed('2001:db8::23'),'Adresse IPv6 incluse dans le CIDR');
    check(!$policy->ipAllowed('2001:db9::23'),'Adresse IPv6 hors CIDR refusée');
    $invalid=false;
    try { $settings->set(array('ip_allowlist'=>'not-an-ip')); }
    catch (InvalidArgumentException $e) { $invalid=true; }
    check($invalid,'Configuration réseau invalide rejetée');
    check($settings->all()['ip_allowlist']==='192.0.2.0/24, 2001:db8::/32','Échec de validation sans modification partielle');
    $settings->set(array('ip_allowlist'=>''));

    $accounts=new Accounts($store);
    $accounts->add('developer.one','aCorrectPasswordLong123!','developer');
    check($accounts->verify('developer.one','aCorrectPasswordLong123!')!==null,'Mot de passe correct vérifié');
    check($accounts->verify('developer.one','wrong')===null,'Mot de passe incorrect rejeté');
    check(strpos($accounts->find('developer.one')['hash'],'aCorrectPasswordLong123!')===false,'Mot de passe absent du stockage');
    $duplicate=false;
    try { $accounts->add('developer.one','anotherVeryLongPassword!','admin'); }
    catch (LogicException $e) {$duplicate=true;}
    check($duplicate,'Doublon de compte refusé');

    $totp=new Totp();
    $secret='GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ';
    check($totp->verify($secret,'287082',59),'Vecteur TOTP RFC6238 connu (6 chiffres)');
    check($totp->verifyWindow($secret,'287082',59)===1,'Compteur TOTP disponible contre le rejeu');
    check(!$totp->verify($secret,'000000',59),'Code TOTP invalide refusé');
    $encrypted=$totp->encrypt($secret);
    check($encrypted!==$secret,'Secret TOTP chiffré');
    check($totp->decrypt($encrypted)===$secret,'Déchiffrement de secret TOTP vérifié');
    check(Html::e('<script>')==='&lt;script&gt;','Échappement HTML contre injection');
    $i18n=new \OmegaGuardian\ProfilerShield\I18n\UiTranslator();
    check($i18n->trans('schedule.title','en')==='Working hours and automatic lock','Catalogue anglais chargé par clé stable');
    check($i18n->trans('schedule.title','fr')==='Horaires et fermeture automatique','Catalogue français chargé par clé stable');
    $fr=require $root.'/translations/guardian.fr.php';
    $en=require $root.'/translations/guardian.en.php';
    check(array_keys($fr)===array_keys($en),'Clés initiales des catalogues FR/EN identiques');

    $audit=new Audit($store);
    $audit->record('emergency_granted','developer.one',array('reason'=>'Test autorisé','cookie'=>'secret-nope'),true);
    $records=$store->read('audit');
    check(count($records)===1,'Audit ajouté');
    check(!isset($records[0]['meta']['cookie']),'Champs sensibles non admis dans les métadonnées audit');
    check(count($store->read('alerts'))===1,'Alerte d’urgence mise en file');

    $store->change('control',function ($x) { $x['lockdown']=true;return $x; });
    check(!$policy->allowed(array(),$account,$tuesday),'Lockdown refuse un accès normalement autorisé');
    $store->change('control',function ($x) { $x['lockdown']=false;return $x; });
    $settings->set(array('schedule_enabled'=>false,'jit_enabled'=>false));
    check($policy->allowed(array('emergency_until'=>0,'jit_until'=>0),$account),'Politique libre lorsque restrictions désactivées');
    echo "\nSUCCÈS : ".$checks." assertions du noyau indépendant.\n";
} finally {
    deleteTree($tmp);
}
