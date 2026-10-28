<?php
$root=dirname(__DIR__);
require $root.'/src/I18n/UiTranslator.php';
use OmegaGuardian\ProfilerShield\I18n\UiTranslator;
$reference=require $root.'/translations/guardian.en.php';
$missing=array();
$problems=[];
foreach (UiTranslator::AVAILABLE as $locale=>$label) {
    $lang=substr($locale,0,2);
    $catalog=require $root.'/translations/guardian.'.$lang.'.php';
    $missingKeys=array_diff(array_keys($reference),array_keys($catalog));
    $missing[$locale]=count($missingKeys);
    foreach ($catalog as $key=>$value) {
        if (preg_match('/გამოგზავნილია|ბადაგონის|სალაყაია|#[0-9]{6}|Custom neutral message|Pinned scenario|Event type|QUICK SETTINGS|ACCESS & IDENTITY/u', $value) && $lang !== 'en') {$problems[]=$locale.': untranslated or corrupted '.$key;}
        if ($key === 'guide.removal' && strpos($value,'Flex') !== false) {$problems[]=$locale.': obsolete removal instruction';}
        if (!is_string($value) || trim($value)==='' || preg_match('//u',$value)!==1) {$problems[]=$locale.': invalid '.$key;continue;}
        if (!array_key_exists($key,$reference)) {$problems[]=$locale.': unexpected '.$key;}
        preg_match_all('/%[a-zA-Z0-9_]+%/', $reference[$key]??'', $expected);
        preg_match_all('/%[a-zA-Z0-9_]+%/', $value, $actual);
        sort($expected[0]);sort($actual[0]);
        if ($expected[0]!==$actual[0]) {$problems[]=$locale.': placeholders '.$key;}
        preg_match_all('~GUARDIAN_[A-Z_]+|guardian:[a-z:]+|APP_DEBUG=0|/_profiler|/_wdt|expose_php=Off~', $reference[$key]??'', $technical);
        if (str_starts_with($key,'guide.') || in_array($key,['security.hash_argon','security.hash_bcrypt','security.length_advice','security.number_help'],true)) {
            preg_match_all('~Argon2id|bcrypt|UTF-8|Guardian|Symfony|PHP|MFA|TOTP|JIT|CIDR|HTTPS|HTTP|IPv4|IPv6|composer remove --dev omegaguardian/profiler-shield|\.guardian-shield|[0-9]+~',$reference[$key]??'',$currentTechnical);
            foreach ($currentTechnical[0] as $literal) {if (strpos($value,$literal)===false) {$problems[]=$locale.': current technical literal '.$key.' / '.$literal;}}
        }
        foreach ($technical[0] as $literal) {
            if (strpos($value,$literal)===false) {$problems[]=$locale.': technical literal '.$key.' / '.$literal;}
        }
    }
    echo $locale.': '.count($catalog).'/'.count($reference).' clés traduites, '.count($missingKeys).' en repli anglais'.PHP_EOL;
}
if (count(UiTranslator::AVAILABLE)!==28) {throw new RuntimeException('28 options attendues');}
foreach (array('fr','en') as $lang) { if ($missing[$lang==='fr'?'fr_FR':'en_GB']!==0) {throw new RuntimeException('Catalogue de référence incomplet');} }
if (isset(UiTranslator::AVAILABLE['ar_SA'])===false) {throw new RuntimeException('RTL absent');}
foreach (require $root.'/src/I18n/SourceMessages.php' as $source=>$key) {
    if (!array_key_exists($key,$reference)) {$problems[]='Unknown source-message key '.$key;}
}
if (strpos(file_get_contents($root.'/src/Controller/GuardianController.php'),'->localizeMarkup(')!==false) {$problems[]='Rendered user content must not be post-translated';}
foreach (UiTranslator::AVAILABLE as $locale=>$label) {
    $guide=$root.'/docs/installation/'.$locale.'.md';
    $content=is_file($guide)?file_get_contents($guide):'';
    foreach (['composer require --dev omegaguardian/profiler-shield','php vendor/bin/guardian setup','guardian:access --env=dev','guardian:uninstall --purge --env=dev','composer remove --dev omegaguardian/profiler-shield'] as $command) {
        if (strpos($content,$command)===false) {$problems[]=$locale.': missing installation instruction '.$command;}
    }
    if (strpos(file_get_contents($root.'/README.md'),'docs/installation/'.$locale.'.md')===false) {$problems[]=$locale.': missing README guide link';}
}
echo 'OK: 28 choix, catalogues et guides vérifiés.' . PHP_EOL;
if ($problems) {fwrite(STDERR,implode(PHP_EOL,$problems).PHP_EOL);exit(1);}
if (in_array('--strict',$argv,true) && array_sum($missing)>0) {
    fwrite(STDERR, 'Catalogues incomplets.' . PHP_EOL);
    exit(1);
}
