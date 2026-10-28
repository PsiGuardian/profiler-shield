<?php
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
return function (RoutingConfigurator $routes) {
    $path=(new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle())->getPath().'/Resources/config/routing/';
    $routes->import($path.(is_file($path.'profiler.php')?'profiler.php':'profiler.xml'))->prefix('/_profiler');
    $routes->import($path.(is_file($path.'wdt.php')?'wdt.php':'wdt.xml'))->prefix('/_wdt');
    $routes->add('guardian_entry',$_SERVER['GUARDIAN_LOGIN_PATH'])->controller('omega_guardian.controller::entry')->methods(['GET','POST']);
    $routes->add('fixture_business','/business')->controller('GuardianReleaseFixtureController::business')->methods(['GET']);
    $routes->add('fixture_exception','/failure')->controller('GuardianReleaseFixtureController::failure');
};
