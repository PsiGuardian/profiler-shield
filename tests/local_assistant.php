<?php
$script=dirname(__DIR__).'/bin/guardian';$root=sys_get_temp_dir().'/guardian-assistant-'.bin2hex(random_bytes(8));mkdir($root,0700);$checks=0;
function verify($condition,$message){global $checks;if(!$condition)throw new RuntimeException($message);++$checks;}
function launch($args){global $script,$root;$process=proc_open([PHP_BINARY,$script,...$args],[0=>['file','/dev/null','r'],1=>['pipe','w'],2=>['pipe','w']],$pipes,$root);$out=stream_get_contents($pipes[1]);$err=stream_get_contents($pipes[2]);fclose($pipes[1]);fclose($pipes[2]);return [proc_close($process),$out.$err];}
try{
 foreach([[],['--help'],['help']] as $args){[$exit,$text]=launch($args);verify($exit===0,'Help exits successfully');foreach(['guardian setup','guardian:access','initial-admin-credentials.txt','guardian:uninstall --purge'] as $token)verify(str_contains($text,$token),'Complete local instructions');verify(count(scandir($root))===2,'Help has no filesystem effects');}
 [$exit]=launch(['unknown']);verify($exit===2,'Invalid command refused');
 [$exit]=launch(['setup']);verify($exit===1,'Setup refuses non-Symfony directory');verify(count(scandir($root))===2,'No changes on refused setup');
 mkdir($root.'/bin',0700);file_put_contents($root.'/composer.json','{}');file_put_contents($root.'/bin/console','<?php // not executed');file_put_contents($root.'/.env',"APP_ENV=prod\n");
 [$exit,$text]=launch(['setup']);verify($exit===1 && str_contains($text,'APP_ENV=prod'),'Production configuration refused');verify(!is_dir($root.'/config'),'Production refusal precedes activation');
 unlink($root.'/bin/console');rmdir($root.'/bin');unlink($root.'/composer.json');unlink($root.'/.env');
 echo json_encode(['checks'=>$checks,'failed'=>0])."\n";
}finally{rmdir($root);}
