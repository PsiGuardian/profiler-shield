<?php
namespace OmegaGuardian\ProfilerShield\Util;

/** Encodage HTML centralisé, interdit les injections via paramètres utilisateur. */
final class Html
{
    public static function e($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
