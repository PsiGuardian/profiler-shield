<?php
/**
 * À copier dans config/routes/guardian.php (Symfony 5.4 à 8.x).
 *
 * La route n'existe PAS si GUARDIAN_LOGIN_PATH est absent ou invalide :
 * cela évite de révéler une URL de connexion par défaut et permet de
 * maintenir l'application métier disponible si la variable est oubliée.
 *
 * Après rotation du chemin, reconstruire/vider le cache des routes Symfony.
 */
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $path = \OmegaGuardian\ProfilerShield\Util\Env::get('GUARDIAN_LOGIN_PATH');
    if (preg_match('~^/[a-zA-Z0-9/_-]{24,180}$~D', $path)) {
        $routes->add('omega_guardian_entry', $path)
            ->controller('omega_guardian.controller::entry')
            ->methods(array('GET', 'POST'));
    }
};
