<?php
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes->add('safe_native_normal', '/api/health')
        ->controller('SafeNativeFixtureController::health')->methods(['GET']);
    $routes->add('safe_native_direct', '/api/direct-error')
        ->controller('SafeNativeFixtureController::directError')->methods(['GET']);
    $routes->add('safe_native_html', '/web/public-error')
        ->controller('SafeNativeFixtureController::htmlError')->methods(['GET']);
    $routes->add('safe_native_exception', '/api/throw/{kind}')
        ->controller('SafeNativeFixtureController::failure')->methods(['GET']);
    $routes->add('safe_native_ordinary', '/outside/throw/{kind}')
        ->controller('SafeNativeFixtureController::failure')->methods(['GET']);
    $routes->add('safe_native_guardian', $_SERVER['GUARDIAN_LOGIN_PATH'])
        ->controller('omega_guardian.controller::entry')->methods(['GET','POST']);
};
