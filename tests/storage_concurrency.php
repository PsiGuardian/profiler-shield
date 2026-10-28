<?php
require $argv[1];spl_autoload_register(function($c){$p='OmegaGuardian\\ProfilerShield\\';if(strpos($c,$p)===0)require __DIR__.'/../src/'.str_replace('\\','/',substr($c,strlen($p))).'.php';},true,true);
use OmegaGuardian\ProfilerShield\Storage\FileStore;
if(($argv[2]??'')==='worker'){$s=new FileStore($argv[3]);for($i=0;$i<50;$i++)$s->change('counter',function($a){$a['n']=($a['n']??0)+1;return $a;});exit;}
$dir=sys_get_temp_dir().'/guardian-concurrency-'.bin2hex(random_bytes(6));$key=bin2hex(random_bytes(32));putenv('GUARDIAN_ENCRYPTION_KEY='.$key);$_SERVER['GUARDIAN_ENCRYPTION_KEY']=$key;$s=new FileStore($dir);$s->initialize();$workers=[];$errors=[];
for($i=0;$i<4;$i++){$pipes=[];$p=proc_open([PHP_BINARY,__FILE__,$argv[1],'worker',$dir],[0=>['file','/dev/null','r'],1=>['pipe','w'],2=>['pipe','w']],$pipes);$workers[]=[$p,$pipes];}
foreach($workers as [$p,$pipes]){$out=stream_get_contents($pipes[1]);$err=stream_get_contents($pipes[2]);fclose($pipes[1]);fclose($pipes[2]);if(proc_close($p)!==0)$errors[]='worker failed';}
if(($s->read('counter')['n']??null)!==200)$errors[]='lost concurrent writes';
try{$s->change('counter',function($a){$a['n']=999;throw new RuntimeException('rollback');});}catch(RuntimeException $e){}
if($s->read('counter')['n']!==200)$errors[]='callback failure not rolled back';
foreach(glob($dir.'/*') as $f)unlink($f);@unlink($dir.'/.htaccess');rmdir($dir);echo json_encode(['workers'=>4,'writes'=>200,'failures'=>$errors],JSON_PRETTY_PRINT),"\n";exit($errors?1:0);
