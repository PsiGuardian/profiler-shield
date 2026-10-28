<?php
namespace OmegaGuardian\ProfilerShield\Util;

/**
 * Décrit la zone horaire effective du PROCESSUS PHP qui sert la requête.
 * Ne pas inférer un fuseau depuis l'IP du visiteur ou l'heure du navigateur.
 * PHP-FPM peut avoir une configuration différente de celle de PHP CLI.
 */
final class ServerClock
{
    public static function phpTimezone()
    {
        $timezone = date_default_timezone_get();
        return in_array($timezone, \DateTimeZone::listIdentifiers(\DateTimeZone::ALL_WITH_BC), true) ? $timezone : 'UTC';
    }

    /** Zone OS indicative, si elle est explicitement exposée sur l'hôte. */
    public static function operatingSystemTimezone()
    {
        $file = '/etc/timezone';
        if (is_readable($file)) {
            $name = trim((string) @file_get_contents($file));
            if (in_array($name, \DateTimeZone::listIdentifiers(\DateTimeZone::ALL_WITH_BC), true)) { return self::normalizeUtc($name); }
        }
        $resolved = realpath('/etc/localtime');
        if ($resolved && strpos($resolved, '/zoneinfo/') !== false) {
            $name = substr($resolved, strpos($resolved, '/zoneinfo/') + 10);
            if (in_array($name, \DateTimeZone::listIdentifiers(\DateTimeZone::ALL_WITH_BC), true)) { return self::normalizeUtc($name); }
        }
        return null;
    }

    /** Normalise les alias UTC les plus courants. */
    private static function normalizeUtc($name)
    {
        return in_array($name,array('Etc/UTC','Etc/GMT','Etc/GMT0'),true) ? 'UTC' : $name;
    }

    /** Installation CLI : l'OS est prioritaire si détectable, sinon PHP CLI. */
    public static function initialTimezone()
    {
        return self::operatingSystemTimezone() ?: self::phpTimezone();
    }

    public static function description($zone)
    {
        $now = new \DateTimeImmutable('now', new \DateTimeZone($zone));
        return $zone.' · UTC'.$now->format('P').' · '.$now->format('Y-m-d H:i:s');
    }
}
