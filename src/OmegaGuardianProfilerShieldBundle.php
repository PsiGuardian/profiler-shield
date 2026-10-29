<?php
namespace OmegaGuardian\ProfilerShield;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Point d'entrée du bundle.
 *
 * Le code n'intervient pas dans le boot de l'application : il ne lit pas
 * le stockage Guardian et n'exécute aucune requête réseau. Les erreurs
 * d'état ne doivent concerner que les routes protégées.
 */
final class OmegaGuardianProfilerShieldBundle extends Bundle
{
    public function build(\Symfony\Component\DependencyInjection\ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new \OmegaGuardian\ProfilerShield\DependencyInjection\Compiler\PublicErrorMapperPass());
    }
}
