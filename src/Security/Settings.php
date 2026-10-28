<?php
namespace OmegaGuardian\ProfilerShield\Security;

use OmegaGuardian\ProfilerShield\Storage\FileStore;

/**
 * Politique opérationnelle modifiable sans recompiler le container Symfony.
 * Seules les clés documentées sont acceptées ; les invariants (refus si
 * session invalide, isolation des routes métier) ne figurent PAS ici.
 */
final class Settings
{
    private $store;

    public function __construct(FileStore $store) { $this->store = $store; }

    public static function defaults()
    {
        return array(
            'preset' => 'solo', 'stealth' => true, 'mfa' => false,
            'idle_minutes' => 60, 'absolute_hours' => 8,
            'remember_days' => 14, 'schedule_enabled' => false, 'portal_closed_outside_hours' => false,
            'timezone' => \OmegaGuardian\ProfilerShield\Util\ServerClock::initialTimezone(),
            'weekdays' => array(1,2,3,4,5), 'start' => '09:00', 'end' => '18:00',
            'jit_enabled' => false, 'jit_minutes' => 30,
            'ip_allowlist' => '', 'audit_level' => 'standard', 'audit_retention_days' => 30,
            'alert_email' => '', 'emergency_enabled' => false,
            'login_rules' => LoginThrottle::defaults(),
            'password_policy' => PasswordPolicy::defaults(),
            // Les codes HTTP réels sont toujours conservés ; seule la représentation HTML change.
            'error_mode' => 'native', 'error_reference' => 'none',
            'error_reference_text' => '', 'error_custom_text' => 'The requested page is unavailable.'
        );
    }

    /** Lecture des paramètres du format courant, sans conversion implicite. */
    public function all()
    {
        return array_replace(self::defaults(), $this->store->read('settings'));
    }

    public function set(array $input)
    {
        $clean = $this->validate($input);
        $this->store->change('settings', function ($state) use ($clean) {
            return array_replace($state, $clean);
        });
    }

    public function preset($name)
    {
        $values = array(
            'solo' => array('mfa'=>false, 'schedule_enabled'=>false, 'jit_enabled'=>false,'idle_minutes'=>60,'absolute_hours'=>8,'remember_days'=>14),
            'team' => array('mfa'=>true, 'schedule_enabled'=>false, 'jit_enabled'=>false,'idle_minutes'=>45,'absolute_hours'=>8,'remember_days'=>7),
            'strict' => array('mfa'=>true, 'schedule_enabled'=>true, 'jit_enabled'=>true,'idle_minutes'=>15,'absolute_hours'=>4,'remember_days'=>1)
        );
        if (!isset($values[$name])) { throw new \InvalidArgumentException('Profil inconnu.'); }
        $this->set(array_merge(array('preset'=>$name), $values[$name]));
    }

    public function validate(array $data)
    {
        $defaults = self::defaults();
        foreach ($data as $key => $value) {
            if (!array_key_exists($key, $defaults)) {
                throw new \InvalidArgumentException('Paramètre inconnu : '.$key);
            }
            if ($key==='login_rules') {LoginThrottle::configuration($value);}
            elseif ($key==='password_policy') {PasswordPolicy::configuration($value);}
            elseif (is_bool($defaults[$key])) {
                if (!is_bool($value)) { throw new \InvalidArgumentException('Booléen attendu : '.$key); }
            } elseif (is_int($defaults[$key])) {
                if (!is_int($value) || $value < 1 || $value > 720) {
                    throw new \InvalidArgumentException('Durée hors limites : '.$key);
                }
            } elseif ($key === 'weekdays') {
                if (!is_array($value) || array_filter($value, function ($day) {
                    return !is_int($day) || $day < 1 || $day > 7;
                })) {
                    throw new \InvalidArgumentException('Jours invalides');
                }
                $data[$key] = array_values(array_unique(array_map('intval', $value)));
            } elseif (!is_string($value) || strlen($value) > 512) {
                throw new \InvalidArgumentException('Texte invalide : '.$key);
            }
        }
        $merged = array_replace($this->all(), $data);
        try { new \DateTimeZone($merged['timezone']); }
        catch (\Exception $e) { throw new \InvalidArgumentException('Fuseau horaire invalide.'); }
        foreach (array('start','end') as $timeKey) {
            if (!preg_match('/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/D', $merged[$timeKey])) {
                throw new \InvalidArgumentException('Heure invalide : '.$timeKey);
            }
        }
        if ($merged['schedule_enabled'] && ($merged['start'] >= $merged['end'] || !$merged['weekdays'])) {
            throw new \InvalidArgumentException('Plage horaire invalide ou aucun jour autorisé.');
        }
        if ($merged['portal_closed_outside_hours'] && $merged['emergency_enabled']) {
            throw new \InvalidArgumentException('Portail caché hors horaires : urgence distante incompatible (CLI disponible).');
        }
        if (!in_array($merged['error_mode'], array('native','generic','custom'), true)) {
            throw new \InvalidArgumentException('Mode de page d’erreur inconnu.');
        }
        if (!in_array($merged['error_reference'], array('none','random','custom'), true)) {
            throw new \InvalidArgumentException('Mode de référence publique inconnu.');
        }
        // Aucun HTML arbitraire n'est accepté, l'affichage échappe le texte.
        if (strlen($merged['error_reference_text']) > 60 || preg_match('/[\x00-\x1F\x7F]/', $merged['error_reference_text'])) {
            throw new \InvalidArgumentException('Référence publique invalide.');
        }
        if (strlen($merged['error_custom_text']) > 360 || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $merged['error_custom_text'])) {
            throw new \InvalidArgumentException('Message public invalide.');
        }
        if (!in_array($merged['audit_level'], array('minimal','standard','advanced'), true)) {
            throw new \InvalidArgumentException('Niveau audit inconnu.');
        }
        if (!in_array($merged['preset'], array('solo','team','strict'), true)) {
            throw new \InvalidArgumentException('Profil inconnu.');
        }
        if (strlen($merged['ip_allowlist']) > 512) { throw new \InvalidArgumentException('Liste CIDR trop longue.'); }
        foreach (preg_split('/[,\s]+/', trim($merged['ip_allowlist']), -1, PREG_SPLIT_NO_EMPTY) as $cidr) {
            $pair = explode('/', $cidr, 2);
            $address = @inet_pton($pair[0]);
            if ($address === false) { throw new \InvalidArgumentException('Adresse IP invalide.'); }
            $bits = isset($pair[1]) ? $pair[1] : (strlen($address) === 4 ? '32' : '128');
            if (!ctype_digit((string) $bits) || (int) $bits > strlen($address) * 8) { throw new \InvalidArgumentException('Préfixe CIDR invalide.'); }
        }
        if ($merged['alert_email'] !== '' && !filter_var($merged['alert_email'], FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Email de sécurité invalide.');
        }
        return $data;
    }
}
