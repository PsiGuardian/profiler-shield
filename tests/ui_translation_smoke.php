<?php
require_once __DIR__.'/../src/I18n/UiTranslator.php';
$t=new OmegaGuardian\ProfilerShield\I18n\UiTranslator();
$markup='<span>ΩGuardian Profiler Shield</span><label>Profil</label><style>.Profil{color:red}</style><script>var Profil="Profil";</script>';
$html=$t->localizeMarkup($markup,'en_GB');
foreach(['ΩGuardian Profiler Shield','<label>Preset</label>','.Profil{color:red}','var Profil="Profil";'] as $expected){if(strpos($html,$expected)===false){fwrite(STDERR,'Missing '.$expected);exit(1);}}
echo "4 translation boundary and code-preservation checks: OK\n";
