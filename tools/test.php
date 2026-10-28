<?php
// Every suite runs in a separate process: test doubles cannot shadow Symfony.
$root=dirname(__DIR__);$autoload=$argv[1]??$root.'/vendor/autoload.php';
if(!is_file($autoload)){fwrite(STDERR,"Dependencies missing. Run composer install first.\n");exit(1);}
$withDependencies=['uninstall_command.php','ui_updates.php','authentication_security.php','alerts_transport.php','http_headers.php','kernel_http.php','options_matrix.php','state_options.php','storage_concurrency.php'];$results=[];
foreach(glob($root.'/tests/*.php') as $test){
 $command=[PHP_BINARY,$test];if(in_array(basename($test),$withDependencies,true))$command[]=realpath($autoload);
 $process=proc_open($command,[0=>['file',DIRECTORY_SEPARATOR==='\\'?'NUL':'/dev/null','r'],1=>['pipe','w'],2=>['pipe','w']],$pipes,$root);
 if(!is_resource($process)){fwrite(STDERR,'Cannot start '.basename($test)."\n");exit(1);}
 $out=stream_get_contents($pipes[1]);$err=stream_get_contents($pipes[2]);fclose($pipes[1]);fclose($pipes[2]);$status=proc_close($process);
 $results[]=['suite'=>basename($test),'exit'=>$status];echo ($status===0?'PASS ':'FAIL ').basename($test)."\n";
 if($status!==0){fwrite(STDERR,$out.$err);}
}
if(!is_dir($root.'/var'))mkdir($root.'/var',0700,true);
file_put_contents($root.'/var/test-results.json',json_encode($results,JSON_PRETTY_PRINT)."\n");
exit(count(array_filter($results,function($r){return $r['exit']!==0;}))?1:0);
