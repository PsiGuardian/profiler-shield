<?php
namespace OmegaGuardian\ProfilerShield\Security;

/**
 * Moteur d'autorisation déterministe et pur en ce qui concerne l'heure.
 * La décision ne dépend jamais de l'expiration d'un cookie uniquement.
 * L'exception d'urgence n'est valide qu'après authentification normale.
 */
final class Policy
{
    private $settings;
    private $store;
    public function __construct(Settings $settings, \OmegaGuardian\ProfilerShield\Storage\FileStore $store)
    {
        $this->settings = $settings; $this->store = $store;
    }

    /** Contrôle temporel autonome, réutilisable par l'entrée web. */
    public function inSchedule($now = null)
    {
        $cfg=$this->settings->all();
        if (!$cfg['schedule_enabled']) { return true; }
        $now=$now===null?time():(int)$now;
        $local=(new \DateTimeImmutable('@'.$now))->setTimezone(new \DateTimeZone($cfg['timezone']));
        // Les sept cases du planning constituent l'unique source de vérité.
        // Aucune fermeture implicite du week-end ne contredit un jour coché.
        return in_array((int)$local->format('N'),$cfg['weekdays'],true)
            && $local->format('H:i') >= $cfg['start']
            && $local->format('H:i') < $cfg['end'];
    }

    /** Réseau CIDR strict, IPv4 et IPv6, sans confiance implicite au X-Forwarded-For. */
    public function ipAllowed($ip)
    {
        $list = trim($this->settings->all()['ip_allowlist']);
        if ($list === '') { return true; }
        $remote = @inet_pton((string) $ip);
        if ($remote === false) { return false; }
        foreach (preg_split('/[,\s]+/', $list, -1, PREG_SPLIT_NO_EMPTY) as $cidr) {
            $parts = explode('/', $cidr, 2);
            $network = @inet_pton($parts[0]);
            if ($network === false || strlen($network) !== strlen($remote)) { continue; }
            $bits = isset($parts[1]) ? (int) $parts[1] : strlen($remote) * 8;
            $whole = intdiv($bits, 8);
            $partial = $bits % 8;
            if (substr($remote,0,$whole) !== substr($network,0,$whole)) { continue; }
            if ($partial) {
                $mask = (0xff << (8-$partial)) & 0xff;
                if ((ord($remote[$whole]) & $mask) !== (ord($network[$whole]) & $mask)) { continue; }
            }
            return true;
        }
        return false;
    }

    public function allowed(array $session, array $account, $now = null)
    {
        $cfg = $this->settings->all();
        if (!empty($account['must_change_password']) || ($session['password_version']??0)!==($account['password_version']??0)) {return false;}
        if (($session['username']??'')==='admin' && !array_key_exists('must_change_password',$account) && is_file($this->store->root().'/initial-admin-credentials.txt')) {return false;}
        if (!in_array($account['role'], array('admin','developer'), true) || !empty($account['disabled'])) { return false; }
        if ($cfg['mfa'] && empty($account['totp'])) { return false; }
        $now = $now === null ? time() : (int) $now;
        // Mode lockdown absolu : seule la commande CLI peut le lever.
        $control=$this->store->read('control');
        if (!empty($control['lockdown'])) { return false; }
        if (!$this->inSchedule($now)) {
            if (empty($session['emergency_until']) || $session['emergency_until'] <= $now) {
                return false;
            }
        }
        if ($cfg['jit_enabled'] && (empty($session['jit_until']) || $session['jit_until'] <= $now)) {
            if (empty($session['emergency_until']) || $session['emergency_until'] <= $now) { return false; }
        }
        return true;
    }
}
