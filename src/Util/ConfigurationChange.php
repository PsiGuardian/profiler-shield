<?php
namespace OmegaGuardian\ProfilerShield\Util;

/** Symfony cache freshness uses whole seconds, including its CLI fast path. */
final class ConfigurationChange
{
    public static function finish(): void
    {
        $second=time();
        // Let the following cache:clear process start strictly after the
        // configuration/container writes. Bounded to roughly one second.
        for ($i=0;$i<22 && time()<=$second;++$i) {usleep(50000);}
    }
}
