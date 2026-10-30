<?php
namespace OmegaGuardian\ProfilerShield\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Only explicit service tags grant an application the ability to publish
 * selected exception data. No auto-discovery or blind Throwable serialization.
 */
final class PublicErrorMapperPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('omega_guardian.public_error_registry')) {
            return;
        }
        $registry = $container->getDefinition('omega_guardian.public_error_registry');
        $tagged = [];
        foreach ($container->findTaggedServiceIds('omega_guardian.public_error_mapper') as $id => $tags) {
            foreach ($tags as $tag) {
                $tagged[] = [
                    'id' => $id,
                    'priority' => (int) ($tag['priority'] ?? 0),
                ];
            }
        }
        usort($tagged, static function (array $a, array $b): int {
            return ($b['priority'] <=> $a['priority']) ?: strcmp($a['id'], $b['id']);
        });
        foreach ($tagged as $mapper) {
            $registry->addMethodCall('addMapper', [new Reference($mapper['id'])]);
        }
    }
}
