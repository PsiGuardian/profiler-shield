<?php
namespace OmegaGuardian\ProfilerShield\Util;

/** Lit les variables définies par Symfony Dotenv sans nécessiter putenv(). */
final class Env
{
    public static function get($name)
    {
        if (isset($_SERVER[$name]) && is_string($_SERVER[$name])) { return $_SERVER[$name]; }
        if (isset($_ENV[$name]) && is_string($_ENV[$name])) { return $_ENV[$name]; }
        $value = getenv($name);
        return $value === false ? '' : (string) $value;
    }
}
