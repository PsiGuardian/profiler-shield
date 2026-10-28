"""Build regional documentation from reviewed prose and the shipped UI catalogues."""
from pathlib import Path
import json, os, re, shutil, subprocess
ROOT=Path(__file__).resolve().parents[2]
HERE=Path(__file__).resolve().parent
PHP=os.environ.get('DOCS_PHP') or shutil.which('php')
if not PHP: raise SystemExit('PHP is required to read the bundled catalogues')
def rows(name):
    result={}
    for line in (HERE/name).read_text().splitlines():
        lang,body=line.split('\t',1);result[lang.strip()]=body.split('|')
    return result
C=rows('commands.tsv');D=rows('details.tsv');L=rows('localization.tsv'); LABELS=rows('labels.tsv'); OPERATIONS=rows('operations.tsv'); COMMUNITY=rows('community.tsv')
assert len(C)==len(D)==len(L)==25
assert all(len(v)==22 for v in C.values()) and all(len(v)==10 for v in D.values())
C['en']=C.pop('en_GB')
raw=subprocess.check_output([PHP,'-r',r'''require $argv[1].'/src/I18n/UiTranslator.php'; $a=['locales'=>OmegaGuardian\ProfilerShield\I18n\UiTranslator::AVAILABLE,'catalogues'=>[]];foreach(glob($argv[1].'/translations/*.php')as$f){$a['catalogues'][substr(basename($f),9,2)]=require$f;}echo json_encode($a,JSON_UNESCAPED_UNICODE);''',str(ROOT)],text=True)
data=json.loads(raw);locales=data['locales'];catalogues=data['catalogues']
COMMANDS=[('guardian:setup','--automatic --env=dev',4),('guardian:install','--username=admin --env=dev',6),('guardian:access','--base-url=https://example.com --env=dev',7),('guardian:doctor','--env=dev',8),('guardian:password:reset','admin --env=dev',9),('guardian:mfa:reset','admin --env=dev',10),('guardian:login:unlock','admin --ip=192.0.2.10 --env=dev',11),('guardian:policy:reset','--env=dev',12),('guardian:lockdown','on --env=dev',13),('guardian:emergency','admin --env=dev',14),('guardian:alerts:flush','--env=dev',15),('guardian:password:benchmark','--iterations=3 --env=dev',16),('guardian:uninstall','--env=dev',17)]
def code(s,lang='bash'):return f'\n```{lang}\n{s}\n```\n'
def section(title,*paragraphs):return '\n## '+title+'\n\n'+'\n\n'.join(paragraphs)+'\n'
def table(headers,values):
    def esc(v):return str(v).replace('|','&#124;').replace('\n',' ').replace('<','&lt;').replace('>','&gt;')
    return '\n'+'| '+' | '.join(map(esc,headers))+' |\n|'+'|'.join('---' for _ in headers)+'|\n'+''.join('| '+' | '.join(map(esc,row))+' |\n' for row in values)
def page(title,body,toc):return f'# {title}\n\n[{toc}](README.md)\n\n'+body

def command_page(c,d,t):
    body=c[1]+'\n'+section(c[19],code('php bin/console list guardian --env=dev\nphp bin/console help guardian:setup --env=dev\nphp bin/console guardian:doctor --help --env=dev'),c[20])
    body+=section('`vendor/bin/guardian`',c[2],c[3],code('php vendor/bin/guardian --help\nphp vendor/bin/guardian setup'))
    body+=table(['CLI',c[0]],[(f'[`{name}`](#{name.replace(":", "")})',re.split(r'(?<=。)|(?<=\.)\s',c[i],maxsplit=1)[0]) for name,args,i in COMMANDS])
    for name,args,i in COMMANDS:
        body+=section('`'+name+'`',c[i],code('php bin/console '+name+' '+args))
        if name=='guardian:setup':
            body+=c[5]+'\n'+code(r'--path: ^/[a-zA-Z0-9/_-]{24,180}$'+'\n'+r'/_profiler; /_wdt → ∅','text')+code('php bin/console guardian:setup --username=operator --env=dev\nphp bin/console cache:clear --env=dev\nphp bin/console guardian:doctor --env=dev\nphp bin/console guardian:access --env=dev')
        if name=='guardian:install':body+='\n'+t('legacy.e736f330d72b')+'\n'
        if name=='guardian:login:unlock':body+=code('php bin/console guardian:login:unlock --all --env=dev')
        if name=='guardian:lockdown':body+=code('php bin/console guardian:lockdown off --env=dev')
        if name=='guardian:uninstall':body+=c[18]+'\n'+code('php bin/console guardian:uninstall --purge --env=dev\n# --purge --force --no-interaction')
    body+=section(c[21],code('php bin/console guardian:uninstall --env=dev\ncomposer remove --dev omegaguardian/profiler-shield\nphp bin/console cache:clear --env=dev'),d[7],'['+t('guide.removal_title')+'](desinstallation.md)')
    return page(c[0],body,d[0])

for locale,label in locales.items():
    lang=locale[:2];c=C[lang];d=D[lang];cat=catalogues[lang]; labels=LABELS[lang]
    def t(key):
        if key not in cat:raise KeyError((locale,key))
        return cat[key]
    out=ROOT/'docs/translations'/locale;out.mkdir(parents=True,exist_ok=True)
    titles={'installation.md':t('guide.identity_title'),'commands.md':c[0],'reglages.md':t('nav.settings'),'authentication.md':t('security.title'),'enrolement-totp.md':t('mfa.title'),'error-pages.md':t('guide.errors_title'),'architecture.md':d[1],'contrat-securite.md':d[2],'desinstallation.md':t('guide.removal_title'),'localization.md':d[3],'security.md':COMMUNITY[lang][0],'contributing.md':COMMUNITY[lang][2],'code-of-conduct.md':COMMUNITY[lang][4],'examples.md':COMMUNITY[lang][6],'features.md':labels[3]}
    bodies={}
    bodies['commands.md']=command_page(c,d,t)
    arch=section(d[1],d[4],t('guide.network'),t('guide.access'))
    arch+=table(['PHP / HTTP',d[1]],[('GuardianSubscriber / kernel.request','4096'),('CollectionSubscriber / kernel.request','3072'),('GuardianSubscriber::sanitize / kernel.response','-4096'),('GUARDIAN_COLLECTION_BRIDGE','0 → false; 1 → session ∧ policy'),('framework.profiler.collect','false'),(t('legacy.6959b4159575'),'256 bit → SHA-256'),('Cookie','__Host-; Secure; HttpOnly; SameSite=Lax; Path=/; Domain=∅'),('JSON / schema','schema=2; AES-256-GCM; nonce=12 B; tag=16 B; HMAC; AAD=collection'),('GUARDIAN_STORAGE_DIR','<Symfony>/.guardian-shield/'),('GUARDIAN_ENCRYPTION_KEY','64 hex; .env.local ≠ .guardian-shield/'),('HTTP','no-store; CSP; no-referrer; frame-ancestors none'),('HTTP headers','X-Debug-Exception=∅; X-Debug-Exception-File=∅; X-Powered-By=∅'),('CDN','∅'),('Symfony routes','/_profiler; /_wdt; index.php'),('Symfony route','/docs/_profiler/guide ≠ /_profiler')])
    arch+=section(d[2],d[8],d[7])+section(t('guide.audit_title'),t('guide.audit'))
    bodies['architecture.md']=page(d[1],arch,d[0])
    bodies['contrat-securite.md']=page(d[2],d[8]+'\n\n'+d[7]+'\n\n'+t('guide.access')+'\n\n'+t('guide.sessions')+'\n\n'+t('guide.network'),d[0])
    auth=section(t('guide.identity_title'),t('security.first_access'),t('guide.identity'),c[4],code('php bin/console guardian:access --env=dev'))
    auth+=table([t('security.required_policy'),t('security.minimum')],[('initial-admin-credentials.txt','0600; random_int; >=32'),('password_policy[min_length]','8–64; 15'),('input','Unicode; ≤512 UTF-8 B'),('Argon2id','PHP + Argon2id → Argon2id'),('bcrypt','PHP − Argon2id → bcrypt; ≤72 UTF-8 B')])
    auth+=section(t('security.policy_title'),t('security.policy_help'),t('security.length_advice'),t('security.hash_argon'),t('security.hash_bcrypt'),t('security.force_help'),c[9])
    auth+=section(t('security.throttle_title'),t('guide.throttle'),d[5],t('security.rule_help'))
    auth+=table([t('security.stage'),t('security.required_policy')],[(t('security.attempts'),'1–100'),(t('security.block_minutes'),'1–10080'),(t('security.window_minutes'),'1–1440'),(t('security.escalation_minutes'),'1–10080'),(t('security.stage'),'1–8; block[n+1] ≥ block[n]'),(labels[1],'10 / 10 / 30 / 30'),('5 → 10 min; 5 → 2 h','5 / 10 / 30 / 30 → 5 / 120 / 30 / 30'),('HTTP','429; Retry-After')])
    auth+=section(t('security.simulator'),t('security.simulation_help'),d[6],t('security.composition_caution'),t('security.simulation_caution'),t('security.number_help'))
    auth+=table([t('security.available_chars'),t('security.combinations')],[(t('security.class_lower'),'26'),(t('security.class_upper'),'26'),(t('security.class_digit'),'10'),(t('security.class_symbol'),'32'),(t('security.online_rate'),'1 / s'),(t('security.offline_rate'),'10000 / s'),(t('security.hour_cost'),'2 € / h')])
    auth+=code('php bin/console guardian:password:benchmark --iterations=3 --env=dev')+section(t('guide.recovery_title'),d[7],c[11],'['+c[0]+'](commands.md)')
    bodies['authentication.md']=page(t('security.title'),auth,d[0])
    mfa=section(t('mfa.title'),t('mfa.lead'),t('guide.mfa'))
    for i in (1,2,3):mfa+=section(str(i)+'. '+t(f'mfa.step{i}'),t(f'mfa.step{i}_help'))
    mfa+=section(t('mfa.privacy_title'),t('mfa.privacy_help'),t('mfa.manual_help'),d[9])+section(t('guide.recovery_title'),c[10],code('php bin/console guardian:mfa:reset admin --env=dev'))
    bodies['enrolement-totp.md']=page(t('mfa.title'),mfa,d[0])
    errors=section(t('guide.errors_title'),t('errors.lead'),t('guide.errors'),t('errors.mode_help'),OPERATIONS[lang][0])
    errors+=table(['error_mode',t('nav.settings')],[('native',t('errors.mode_help')),('generic','HTML 400–599 → '+t('errors.lead')),('custom',t('errors.custom_help')+' (≤360 Unicode)'),('error_reference','none / random / custom'),('error_reference_text',t('errors.ref_text_help')),('error_custom_text',t('errors.custom_help'))])
    errors+=section(t('errors.reference'),t('errors.warning'),t('errors.reference_help'),t('config.save_help'))
    errors+=code('templates/bundles/TwigBundle/Exception/error404.html.twig\ntemplates/bundles/TwigBundle/Exception/error.html.twig\nGUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1\n\nHTTP 400 / 403 / 404 / 429 / 500 / 503\napplication/json → application/json; application/problem+json → application/problem+json\nHTML 2xx → HTML 2xx\nHTTP 404 → HTTP 404; HTTP 500 → HTTP 500', 'text')
    errors+=section(t('guide.network_title'),t('guide.network'),d[8])
    bodies['error-pages.md']=page(t('guide.errors_title'),errors,d[0])
    s=section(t('config.title'),t('config.description'),t('guide.access'),code('POST ∧ CSRF ∧ role=admin ∧ password','text'))
    s+=table([t('legacy.e267d343821f'),t('nav.settings')],[(t('legacy.cd2a76692e65'),'solo: mfa=false; schedule=false; jit=false; idle=60 min; absolute=8 h; remember=14 d'),(t('legacy.019e75a9e3b8'),'team: mfa=true; schedule=false; jit=false; idle=45 min; absolute=8 h; remember=7 d'),(t('legacy.113f13b29570'),'strict: mfa=true; schedule=true; jit=true; idle=15 min; absolute=4 h; remember=1 d')])
    setting_keys=[('stealth','config.stealth_help','true'),('mfa','config.mfa_help','false'),('idle_minutes','config.idle_help','60'),('absolute_hours','config.absolute_help','8'),('remember_days','config.remember_help','14'),('schedule_enabled','schedule.description','false'),('weekdays','schedule.week_help','1,2,3,4,5'),('start','schedule.start','09:00'),('end','schedule.end','18:00'),('timezone','schedule.server_explain','OS / PHP CLI'),('portal_closed_outside_hours','schedule.portal_help','false'),('jit_enabled','config.jit_help','false'),('jit_minutes','config.jit_minutes_help','30'),('ip_allowlist','config.cidr_help','∅'),('audit_level','config.audit_help','standard'),('audit_retention_days','config.audit_retention_help','30'),('alert_email','config.alert_help','∅'),('emergency_enabled','config.emergency_help','false'),('login_rules','security.throttle_help','10 / 10 / 30 / 30'),('password_policy','security.policy_help','min_length=15'),('error_mode','errors.mode_help','native'),('error_reference','errors.reference_help','none'),('error_reference_text','errors.ref_text_help','∅'),('error_custom_text','errors.custom_help','∅ → custom')]
    s+=table([labels[0],t('nav.settings'),labels[1]],[(f'`{k}`',t(label),f'`{value}`') for k,label,value in setting_keys])
    for key in ('guide.schedule','guide.sessions','guide.jit','guide.network','guide.audit','guide.passwords','ui.audit_help'):s+='\n'+t(key)+'\n'
    s+=section(labels[2],code('GUARDIAN_LOGIN_PATH=/internal/<random-48-hex>\nGUARDIAN_ENCRYPTION_KEY=<64-hex>\nGUARDIAN_STORAGE_DIR=<private-directory>\nGUARDIAN_COLLECTION_BRIDGE=0\nGUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=0\nGUARDIAN_SHOW_PHP_VERSION=0','dotenv'),d[4],d[7])
    s+=section(t('guide.recovery_title'),c[12],c[13],code('php bin/console guardian:policy:reset --env=dev\nphp bin/console guardian:lockdown on --env=dev\nphp bin/console guardian:doctor --env=dev'))
    bodies['reglages.md']=page(t('nav.settings'),s,d[0])
    removal=c[17]+'\n\n'+c[18]+code('php bin/console guardian:uninstall --env=dev\ncomposer remove --dev omegaguardian/profiler-shield\nphp bin/console cache:clear --env=dev')+d[7]+code('php bin/console guardian:uninstall --purge --env=dev')
    removal+=section(c[0],c[18],code('php bin/console guardian:uninstall --purge --force --no-interaction --env=dev'),'['+c[0]+'](commands.md)')
    removal+='\n\n'+OPERATIONS[lang][1]+'\n'
    bodies['desinstallation.md']=page(t('guide.removal_title'),removal,d[0])
    bodies['localization.md']=page(d[3],L[lang][0]+code('php tests/translation_audit.php --strict\nphp tests/documentation_audit.php\ncomposer test'),d[0])
    community=COMMUNITY[lang]
    bodies['security.md']=page(community[0],community[1]+'\n\n'+t('support.note')+'\n\n'+d[7],d[0])
    bodies['contributing.md']=page(community[2],community[3]+code('composer install\ncomposer test\nphp tests/translation_audit.php --strict\nphp tests/documentation_audit.php\nnode tests/password_model.js\nvendor/bin/phpunit -c phpunit.xml.dist')+'\n['+community[0]+'](security.md) · ['+community[6]+'](examples.md)',d[0])
    bodies['code-of-conduct.md']=page(community[4],community[5]+'\n\n['+community[0]+'](security.md)',d[0])
    bodies['examples.md']=page(community[6],community[7]+code('GUARDIAN_EXPORT_PREVIEW=1 php tests/flow_smoke.php'),d[0])
    bodies['features.md']=page(labels[3],'\n'.join('- '+t('guide.'+key+'_title') for key in ['access','identity','passwords','throttle','simulation','schedule','mfa','sessions','jit','network','errors','audit','recovery','removal']),d[0])
    # Installation pages have one canonical regional source; keep their links local.
    install=(ROOT/'docs/installation'/f'{locale}.md').read_text()
    install=re.sub(r'\n*<!-- guardian:command-reference -->.*?<!-- /guardian:command-reference -->\n*','\n',install,flags=re.S)
    install=install.replace('## 5. CLI','## 5. '+c[0])
    addon='\n<!-- guardian:command-reference -->\n\n['+c[0]+'](../translations/'+locale+'/commands.md) · ['+d[0]+'](../translations/'+locale+'/README.md)\n'+code('php bin/console list guardian --env=dev\nphp bin/console help guardian:setup --env=dev')+'\n<!-- /guardian:command-reference -->\n'
    marker='\n## Private Packagist';assert marker in install
    install=install.replace(marker,addon+marker)
    (ROOT/'docs/installation'/f'{locale}.md').write_text(install)
    localized=install.replace('../translations/'+locale+'/', '').replace('https://packagist.com/docs/setup','https://packagist.com/docs/setup')
    bodies['installation.md']=localized
    for name,body in bodies.items():(out/name).write_text(body.rstrip()+'\n')
    index=f'# {d[0]}\n\nΩGuardian Profiler Shield · {label}\n\n'+''.join(f'- [{title}]({name})\n' for name,title in titles.items())
    index+='\n['+d[3]+'](../README.md)\n'
    (out/'README.md').write_text(index)
# French canonical documentation and root installation entry point.
fr=ROOT/'docs/translations/fr_FR'
for name in ['commands.md','architecture.md','authentication.md','contrat-securite.md','desinstallation.md','enrolement-totp.md','error-pages.md','reglages.md','localization.md','security.md','contributing.md','code-of-conduct.md','examples.md','features.md']:
    (ROOT/'docs'/name).write_text((fr/name).read_text())
# Root editorial files are maintained by hand. Never recreate deleted files or
# overwrite SECURITY.md, CONTRIBUTING.md, CODE_OF_CONDUCT.md or CHANGELOG.md.
(ROOT/'examples/README.md').write_text((fr/'examples.md').read_text().replace('(README.md)','(../docs/README.md)'))
original=(ROOT/'docs/installation/fr_FR.md').read_text()
(ROOT/'docs/installation.md').write_text(original.replace('../translations/fr_FR/','translations/fr_FR/'))
(ROOT/'INSTALLATION.md').write_text(original.replace('../translations/fr_FR/','docs/translations/fr_FR/'))
translation_index='# Documentation · 28 variantes régionales\n\n'+''.join(f'- [{label}]({locale}/README.md)\n' for locale,label in locales.items())
(ROOT/'docs/translations/README.md').write_text(translation_index)
index='# Sommaire de la documentation\n\n'
fr_titles={'installation.md':'Installation et premier accès','commands.md':'Toutes les commandes','reglages.md':'Options et réglages','authentication.md':'Authentification et simulateur','enrolement-totp.md':'Authentificateur TOTP','error-pages.md':'Pages d’erreur publiques','architecture.md':'Architecture et frontières de confiance','contrat-securite.md':'Invariants de sécurité','desinstallation.md':'Désinstallation et données','localization.md':'Traductions et contrôles','security.md':'Politique de sécurité','contributing.md':'Contribuer','code-of-conduct.md':'Code de conduite','examples.md':'Exemples d’interface','features.md':'Fonctionnalités'}
index+=''.join(f'- [{title}]({name})\n' for name,title in fr_titles.items())
editorial_links=[('README','README.md'),('SECURITY','SECURITY.md'),('CONTRIBUTING','CONTRIBUTING.md'),('CODE_OF_CONDUCT','CODE_OF_CONDUCT.md'),('CHANGELOG','CHANGELOG.md'),('examples','examples/README.md')]
index+='\n'+' · '.join(f'[{label}](../{path})' for label,path in editorial_links if (ROOT/path).is_file())+'\n'
index+='\n## Documentation par langue\n\n'+''.join(f'- [{label}](translations/{locale}/README.md) · [Installation](installation/{locale}.md)\n' for locale,label in locales.items())
(ROOT/'docs/README.md').write_text(index)
readme=ROOT/'README.md';text=readme.read_text()
addition='[Sommaire de toute la documentation](docs/README.md) · [Référence de toutes les commandes](docs/commands.md) · [Documentation dans les 28 variantes régionales](docs/translations/README.md)'
if addition not in text:text=text.replace('## Protections',addition+'\n\n## Protections')
readme.write_text(text)
print(f'{len(locales)} regional documentation sets generated')
