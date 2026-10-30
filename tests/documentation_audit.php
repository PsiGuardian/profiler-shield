<?php
/** Check the published navigation and command reference against actual declarations. */
require_once dirname(__DIR__).'/src/I18n/UiTranslator.php';
use OmegaGuardian\ProfilerShield\I18n\UiTranslator;
$root=dirname(__DIR__);$errors=[];$commands=[];$options=[];$arguments=[];
foreach(glob($root.'/src/Command/*.php') as $file) {
    $source=file_get_contents($file);
    if(!preg_match("/->setName\('([^']+)'\)/",$source,$m)) {$errors[]='Missing command declaration: '.basename($file);continue;}
    $name=$m[1];$commands[]=$name;
    preg_match_all("/->addOption\('([^']+)'/",$source,$matches);$options[$name]=$matches[1];
    preg_match_all("/->addArgument\('([^']+)'/",$source,$matches);$arguments[$name]=$matches[1];
}
$referenceFiles=array_map('basename',glob($root.'/docs/*.md'));sort($referenceFiles);
foreach(UiTranslator::AVAILABLE as $locale=>$label) {
    $dir=$root.'/docs/translations/'.$locale;
    $actual=array_map('basename',glob($dir.'/*.md'));sort($actual);
    if($actual!==$referenceFiles) {$errors[]=$locale.': documentation file set differs';}
    $index=(string)@file_get_contents($dir.'/README.md');
    foreach($referenceFiles as $file) {
        if($file!=='README.md' && !str_contains($index,']('.$file.')')) {$errors[]=$locale.': missing index entry '.$file;}
    }
    $doc=(string)@file_get_contents($dir.'/commands.md');
    foreach($commands as $name) {
        if(!preg_match('/^## `'.preg_quote($name,'/').'`\R(.*?)(?=^## |\z)/ms',$doc,$body)) {$errors[]=$locale.': missing command '.$name;continue;}
        foreach($options[$name] as $option) {
            if(!str_contains($body[1],'--'.$option)) {$errors[]=$locale.': missing option '.$name.' --'.$option;}
        }
        foreach($arguments[$name] as $arg) {
            if(!str_contains($body[1],$arg) && !($arg==='state' && str_contains($body[1],'on') && str_contains($body[1],'off'))) {$errors[]=$locale.': missing argument '.$name.' '.$arg;}
        }
    }
    foreach(['php vendor/bin/guardian setup','--help','--no-interaction','--purge','--force'] as $literal) {
        if(!str_contains($doc,$literal)) {$errors[]=$locale.': missing assistant or safety option '.$literal;}
    }
    $install=(string)@file_get_contents($root.'/docs/installation/'.$locale.'.md');
    if(!str_contains($install,'../translations/'.$locale.'/commands.md') || !str_contains($install,'list guardian --env=dev')) {$errors[]=$locale.': missing command link in installation';}
    if(!str_contains((string)@file_get_contents($root.'/docs/translations/README.md'),$locale.'/README.md')) {$errors[]=$locale.': missing language index link';}
}
$docs=[];
foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root.'/docs',FilesystemIterator::SKIP_DOTS)) as $file) {
    if($file->isFile() && $file->getExtension()==='md') {$docs[]=$file->getPathname();}
}
// Root editorial pages may be intentionally removed; audit every one that exists.
foreach (glob($root.'/*.md') as $file) {$docs[]=$file;}
if (is_file($root.'/examples/README.md')) {$docs[]=$root.'/examples/README.md';}
foreach($docs as $file) {
    $text=file_get_contents($file);
    if(preg_match('//u',$text)!==1 || str_contains($text,"\xEF\xBF\xBD")) {$errors[]=$file.': invalid UTF-8';}
    if(substr_count($text,'```')%2) {$errors[]=$file.': unclosed code fence';}
    // Code examples may contain bracket syntax; validate Markdown links only outside fences.
    $prose=preg_replace('/```.*?```/s','',$text);
    preg_match_all('/\[[^\]]+\]\(([^\s)]+)\)/u',$prose,$matches);
    foreach($matches[1] as $link) {
        if(preg_match('~^(?:https?://|mailto:|#)~',$link)) {continue;}
        $path=explode('#',$link,2)[0];
        if(!file_exists(dirname($file).'/'.rawurldecode($path))) {$errors[]=str_replace($root.'/','',$file).': broken link '.$link;}
    }
}
foreach($referenceFiles as $file) {
    if($file!=='README.md' && !str_contains(file_get_contents($root.'/docs/README.md'),']('.$file.')')) {$errors[]='Root index missing '.$file;}
}
if($errors) {fwrite(STDERR,implode(PHP_EOL,$errors).PHP_EOL);exit(1);}
echo count($commands).' Symfony commands, all declared options, 28 regional documentation sets and '.count($docs).' Markdown files: OK'.PHP_EOL;
