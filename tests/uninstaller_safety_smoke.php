<?php
require dirname(__DIR__).'/src/Installation/Uninstaller.php';
use OmegaGuardian\ProfilerShield\Installation\Uninstaller;
error_reporting(E_ALL);
set_error_handler(function($severity,$message){ throw new RuntimeException($message); });
function ensure($true,$description) {if(!$true) throw new RuntimeException('FAIL: '.$description); echo '[PASS] '.$description."\n";}
function put($name,$body){$dir=dirname($name);if(!is_dir($dir))mkdir($dir,0700,true);file_put_contents($name,$body);}
function wipe($directory){foreach(scandir($directory) as $item){if($item==='.'||$item==='..')continue;$path=$directory.'/'.$item;if(is_dir($path)&&!is_link($path))wipe($path);else unlink($path);}rmdir($directory);}
function sampleRoute() {
    return <<<'ROUTE'
<?php
// OmegaGuardian generated route v1
// Chemin lu dans .env.local : jamais de valeur secrète dans ce fichier.
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
return function (RoutingConfigurator $routes) {
    $path = \OmegaGuardian\ProfilerShield\Util\Env::get('GUARDIAN_LOGIN_PATH');
    if (preg_match('~^/[a-zA-Z0-9/_-]{24,180}$~D', $path)) {
        $routes->add('omega_guardian_entry', $path)
            ->controller('omega_guardian.controller::entry')
            ->methods(array('GET','POST'));
    }
};
ROUTE;
}
function sampleProfiler() {
    return "# OmegaGuardian generated profiler v1\n"
        ."# Dev seulement : le profiler n'est JAMAIS une sécurité de production.\n"
        ."web_profiler:\n    toolbar: true\n    intercept_redirects: false\n"
        ."framework:\n    profiler:\n        collect: false\n";
}
$root=sys_get_temp_dir().'/guardian-removal-'.bin2hex(random_bytes(6));
mkdir($root,0700);
try {
    put($root.'/config/bundles.php', "<?php\nreturn [\n    \\Symfony\\Bundle\\FrameworkBundle\\FrameworkBundle::class => ['all' => true],\n];\n");
    put($root.'/.env.local', "APP_SECRET=applicationSecret\nDATABASE_URL=mysql://example\nGUARDIAN_EXISTING_DEFINED_BY_USER=yes\n");
    put($root.'/.gitignore', "/vendor/\n/.env.local\n");
    $snap=Uninstaller::snapshot($root);
    put($root.'/config/bundles.php', str_replace("\n];", "\n".Uninstaller::BUNDLE_LINE."\n];", file_get_contents($root.'/config/bundles.php')));
    put($root.'/.env.local', file_get_contents($root.'/.env.local')."GUARDIAN_LOGIN_PATH=/internal/secret123\nGUARDIAN_ENCRYPTION_KEY=abc\nGUARDIAN_COLLECTION_BRIDGE=1\n");
    put($root.'/.gitignore', file_get_contents($root.'/.gitignore')."# ΩGuardian : coffre persistant chiffré (ne jamais versionner)\n/.guardian-shield/\n");
    put($root.'/config/routes/guardian.php',sampleRoute());
    put($root.'/config/routes/guardian.yaml',"# OmegaGuardian generated route import v1\nomega_guardian_php:\n    resource: guardian.php\n    type: php\n");
    put($root.'/config/packages/dev/zz_guardian_profiler.yaml',sampleProfiler());
    put($root.'/.guardian-shield/accounts.json','encrypted');
    put($root.'/.guardian-shield/settings.json','encrypted');
    put($root.'/.guardian-shield/.htaccess','Require all denied');
    Uninstaller::writeReceipt($root,$snap);
    ensure(is_file($root.'/.guardian-shield/.installation.json'),'Manifeste enregistré');
    $receipt=file_get_contents($root.'/.guardian-shield/.installation.json');
    ensure(strpos($receipt,'applicationSecret')===false && strpos($receipt,'secret123')===false,'Aucun secret écrit dans le manifeste');
    $beforeRepeat=json_decode($receipt,true);
    Uninstaller::writeReceipt($root,Uninstaller::snapshot($root));
    $afterRepeat=json_decode(file_get_contents($root.'/.guardian-shield/.installation.json'),true);
    foreach (['route_hash','route_import_hash','profiler_hash'] as $field) {
        ensure(is_string($beforeRepeat[$field]) && $afterRepeat[$field]===$beforeRepeat[$field],'Repeated setup retains ownership: '.$field);
    }
    $log=[]; (new Uninstaller())->execute($root,function($line)use(&$log){$log[]=$line;},false);
    ensure(!is_file($root.'/config/routes/guardian.php'),'Route Guardian supprimée');
    ensure(!is_file($root.'/config/routes/guardian.yaml'),'Import YAML généré supprimé');
    ensure(!is_file($root.'/config/packages/dev/zz_guardian_profiler.yaml'),'Configuration toolbar Guardian supprimée');
    ensure(strpos(file_get_contents($root.'/config/bundles.php'),'Guardian')===false,'Enregistrement Guardian supprimé');
    ensure(strpos(file_get_contents($root.'/config/bundles.php'),'FrameworkBundle')!==false,'Autres bundles conservés');
    $env=file_get_contents($root.'/.env.local');
    ensure(strpos($env,'GUARDIAN_LOGIN_PATH')===false && strpos($env,'GUARDIAN_ENCRYPTION_KEY=abc')!==false, 'Clé conservée avec un coffre non purgé');
    ensure(strpos($env,'GUARDIAN_EXISTING_DEFINED_BY_USER=yes')!==false && strpos($env,'APP_SECRET=applicationSecret')!==false,'Variables préexistantes préservées');
    $ignore=file_get_contents($root.'/.gitignore');
    ensure(strpos($ignore,'/.guardian-shield/')!==false && strpos($ignore,'/.env.local')!==false,'Exclusions Git conservées tant que des secrets subsistent');
    ensure(is_file($root.'/.guardian-shield/accounts.json'),'Coffre conservé sans confirmation');
    // Simulation d'une réinstallation : le coffre et la clé ont été conservés.
    // Le nouveau reçu doit se souvenir que la clé provenait du premier setup.
    $reinstallSnapshot=Uninstaller::snapshot($root);
    put($root.'/config/routes/guardian.php',sampleRoute());
    put($root.'/config/routes/guardian.yaml',"# OmegaGuardian generated route import v1\nomega_guardian_php:\n    resource: guardian.php\n    type: php\n");
    put($root.'/config/packages/dev/zz_guardian_profiler.yaml',sampleProfiler());
    Uninstaller::writeReceipt($root,$reinstallSnapshot);
    $merged=json_decode(file_get_contents($root.'/.guardian-shield/.installation.json'),true);
    ensure(in_array('GUARDIAN_ENCRYPTION_KEY',$merged['managed_env_keys'],true),'Réinstallation : propriété de la clé conservée');
    put($root.'/config/routes/guardian.yaml',"custom_route: preserved\n");
    // Nettoyage relançable, pour une désinstallation partiellement interrompue.
    (new Uninstaller())->execute($root,function($line){},true);
    ensure(!is_dir($root.'/.guardian-shield'),'Coffre définitivement purgé après choix explicite');
    ensure(file_get_contents($root.'/config/routes/guardian.yaml')==="custom_route: preserved\n",'Import YAML personnalisé conservé');
    ensure(strpos(file_get_contents($root.'/.env.local'), 'GUARDIAN_ENCRYPTION_KEY') === false,'Clé supprimée après purge');
    ensure(strpos(file_get_contents($root.'/.gitignore'), '/.guardian-shield/') === false,'Ancienne exclusion de coffre retirée après purge');
    ensure(strpos(file_get_contents($root.'/.gitignore'), '/.env.local') !== false,'Exclusion Symfony préexistante conservée après purge');
    // Ne jamais supprimer une route déjà existante, même si elle porte un nom Guardian.
    put($root.'/config/routes/guardian.php','<?php // CUSTOM MY-APP ROUTE');
    (new Uninstaller())->execute($root,function($line){},true);
    ensure(is_file($root.'/config/routes/guardian.php'),'Route personnalisée conservée');
    // Sans reçu, même un fichier marqué Guardian ne prouve pas sa propriété.
    put($root.'/config/routes/guardian.php',sampleRoute());
    put($root.'/config/packages/dev/zz_guardian_profiler.yaml',sampleProfiler());
    put($root.'/.env.local',"APP_SECRET=applicationSecret\nGUARDIAN_LOGIN_PATH=/internal/unattributed\n");
    (new Uninstaller())->execute($root,function($line){},true);
    ensure(is_file($root.'/config/routes/guardian.php') && is_file($root.'/config/packages/dev/zz_guardian_profiler.yaml'),'Fichiers sans reçu conservés');
    ensure(strpos(file_get_contents($root.'/.env.local'),'GUARDIAN_LOGIN_PATH')!==false,'Variable sans reçu conservée');
    ensure(strpos(file_get_contents($root.'/.env.local'),'APP_SECRET=applicationSecret')!==false,'Secret applicatif conservé');
    put($root.'/.guardian-shield/accounts.json','old-encrypted');
    put($root.'/.env.local',"APP_SECRET=applicationSecret\nGUARDIAN_ENCRYPTION_KEY=abc\nGUARDIAN_STORAGE_DIR=/secure/mounted/guardian\n");
    (new Uninstaller())->execute($root,function($line){},true);
    ensure(is_file($root.'/.guardian-shield/accounts.json') && strpos(file_get_contents($root.'/.env.local'),'GUARDIAN_ENCRYPTION_KEY=abc')!==false,'Coffre externe : purge et perte de clé évitées');
    put($root.'/.env.local',"APP_SECRET=applicationSecret\nGUARDIAN_ENCRYPTION_KEY=abc\n");
    put($root.'/.guardian-shield/foreign.txt','application-file');
    (new Uninstaller())->execute($root,function($line){},true);
    ensure(is_file($root.'/.guardian-shield/foreign.txt') && strpos(file_get_contents($root.'/.env.local'),'GUARDIAN_ENCRYPTION_KEY=abc')!==false,'Fichier étranger : coffre et clé préservés');
} finally { restore_error_handler(); wipe($root); }
