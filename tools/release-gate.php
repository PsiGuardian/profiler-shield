<?php
/** Validate the files and metadata distributed with the bundle. */
$root=dirname(__DIR__);$errors=[];
$data=json_decode(file_get_contents($root.'/composer.json'),true);
if (!is_array($data) || isset($data['version']) || ($data['name']??'')!=='omegaguardian/profiler-shield') {$errors[]='Invalid Composer metadata';}
if (($data['type']??'')!=='symfony-bundle' || ($data['bin']??[])!==['bin/guardian']) {$errors[]='Invalid bundle entry points';}
foreach (['LICENSE','README.md','SECURITY.md','INSTALLATION.md','docs/desinstallation.md','.github/workflows/ci.yml'] as $file) {
    if (!is_file($root.'/'.$file)) {$errors[]='Missing '.$file;}
}
foreach (['.env.local','.guardian-shield'] as $file) {
    if (file_exists($root.'/'.$file)) {$errors[]='Private application content: '.$file;}
}
foreach (glob($root.'/public/*.sql') as $dump) {$errors[]='SQL dump under public/';}
if ($errors) {fwrite(STDERR,implode(PHP_EOL,$errors).PHP_EOL);exit(1);}
echo "Distribution metadata and required files: OK\n";
