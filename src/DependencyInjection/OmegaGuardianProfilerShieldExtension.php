<?php
namespace OmegaGuardian\ProfilerShield\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

/** Configuration de services stable sur les branches Symfony visées. */
final class OmegaGuardianProfilerShieldExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__, 2).'/config'));
        $loader->load('services.yaml');
    }

    public function getAlias(): string
    {
        return 'omega_guardian_profiler_shield';
    }
}
