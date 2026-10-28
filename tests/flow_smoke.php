<?php
/**
 * Smoke test de flux via substituts minimaux HttpFoundation/HttpKernel.
 * NE remplace PAS un test d'intégration réel Symfony.
 */
namespace Symfony\Component\EventDispatcher {
    interface EventSubscriberInterface { public static function getSubscribedEvents(): array; }
}
namespace Symfony\Component\HttpKernel {
    interface HttpKernelInterface {}
    final class KernelEvents { const REQUEST='kernel.request'; const RESPONSE='kernel.response'; const EXCEPTION='kernel.exception'; }
}
namespace Symfony\Component\HttpFoundation {
    class Bag {
        private $data;
        public function __construct(array $data=array()) { $this->data=$data; }
        public function get($key,$default=null) { return isset($this->data[$key])?$this->data[$key]:$default; }
        public function has($key) {return array_key_exists($key,$this->data);}
        public function all($key=null) {return $key===null?$this->data:(isset($this->data[$key])?$this->data[$key]:array());}
    }
    class Request {
        public $cookies;public $request;public $query;private $path;private $method;
        public function __construct($path='/', $method='GET',array $form=array(),array $cookies=array(),array $query=array()) {
            $this->path=$path;$this->method=$method;$this->request=new Bag($form);$this->cookies=new Bag($cookies);$this->query=new Bag($query);
        }
        public function getPathInfo() {return $this->path;}
        public function isMethod($method) {return strtoupper($this->method)===strtoupper($method);}
        public function getClientIp() {return '127.0.0.1';}
        public function getRequestFormat() {return strpos($this->path,'/api/')===0?'json':'html';}
    }
    class Cookie {
        private $name; private $value;
        public function __construct($name,$value,$expire=0,$path='/',$domain=null,$secure=true,$httpOnly=true,$raw=false,$sameSite=null) {
            $this->name=$name;$this->value=$value;
        }
        public function getName() {return $this->name;}
        public function getValue() {return $this->value;}
    }
    class HeaderBag {
        private $headers=array();private $cookies=array();
        public function __construct($headers=array()) {foreach ($headers as $key=>$value) {$this->set($key,$value);}}
        public function set($key,$value) {$this->headers[strtolower($key)]=$value;}
        public function get($key,$default=null) {return isset($this->headers[strtolower($key)])?$this->headers[strtolower($key)]:$default;}
        public function remove($key) {unset($this->headers[strtolower($key)]);}
        public function setCookie(Cookie $c) {$this->cookies[$c->getName()]=$c;}
        public function getCookies() {return array_values($this->cookies);}
    }
    class Response {
        public $headers;private $body;private $status;
        public function __construct($body='',$status=200,array $headers=array()) {$this->body=$body;$this->status=$status;$this->headers=new HeaderBag($headers);}
        public function getStatusCode() {return $this->status;}
        public function getContent() {return $this->body;}
        public function setContent($body) {$this->body=$body; return $this;}
    }
}
namespace Symfony\Component\HttpKernel\Exception {
    interface HttpExceptionInterface { public function getStatusCode(); public function getHeaders(); }
}
namespace Symfony\Component\HttpKernel\Event {
    class ExceptionEvent {
        private $request; private $error; private $response;
        public function __construct($request, $error) {$this->request=$request;$this->error=$error;}
        public function isMainRequest() {return true;}
        public function getRequest() {return $this->request;}
        public function getThrowable() {return $this->error;}
        public function setResponse($response) {$this->response=$response;}
        public function getResponse() {return $this->response;}
    }
    class RequestEvent {
        private $request;private $response;
        public function __construct($request) {$this->request=$request;}
        public function isMainRequest() {return true;}
        public function getRequest() {return $this->request;}
        public function setResponse($response) {$this->response=$response;}
        public function hasResponse() {return $this->response!==null;}
        public function getResponse() {return $this->response;}
    }
    class ResponseEvent {
        private $response;private $request;
        public function __construct($response,$request=null) {$this->response=$response;$this->request=$request?:new \Symfony\Component\HttpFoundation\Request('/');}
        public function getResponse() {return $this->response;}
        public function getRequest() {return $this->request;}
    }
}
namespace {
    error_reporting(E_ALL);
    $root=dirname(__DIR__);
    spl_autoload_register(function ($name) use ($root) {
        $prefix='OmegaGuardian\\ProfilerShield\\';
        if (strpos($name,$prefix)!==0) {return;}
        $file=$root.'/src/'.str_replace('\\','/',substr($name,strlen($prefix))).'.php';
        if (is_file($file)) {require $file;}
    });
    use OmegaGuardian\ProfilerShield\Storage\FileStore;
    use OmegaGuardian\ProfilerShield\Security\Settings;
    use OmegaGuardian\ProfilerShield\Security\Accounts;
    use OmegaGuardian\ProfilerShield\Security\Sessions;
    use OmegaGuardian\ProfilerShield\Security\Policy;
    use OmegaGuardian\ProfilerShield\Security\Audit;
    use OmegaGuardian\ProfilerShield\Security\Totp;
    use OmegaGuardian\ProfilerShield\Security\DiagnosticAccess;
    use OmegaGuardian\ProfilerShield\Controller\GuardianController;
    use OmegaGuardian\ProfilerShield\EventListener\GuardianSubscriber;
    use OmegaGuardian\ProfilerShield\EventListener\PrivacySubscriber;
    use Symfony\Component\HttpKernel\Event\ExceptionEvent;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpKernel\Event\RequestEvent;
    use Symfony\Component\HttpKernel\Event\ResponseEvent;
    function yes($condition,$message) {if (!$condition) {throw new \RuntimeException('FAILED: '.$message);}echo '[PASS] '.$message."\n";}
    $_SERVER['GUARDIAN_ENCRYPTION_KEY']=bin2hex(random_bytes(32));
    $dir=sys_get_temp_dir().'/guardian-smoke-'.bin2hex(random_bytes(6));
    try {
        $store=new FileStore($dir);$store->initialize();
        $settings=new Settings($store);$accounts=new Accounts($store);
        $accounts->add('admin','TestPassword!Works2026','admin');
        $sessions=new Sessions($store,$settings);
        $policy=new Policy($settings,$store);$audit=new Audit($store);
        $controller=new GuardianController($store,$settings,$accounts,$sessions,$policy,$audit,new Totp());
        $access=new DiagnosticAccess($sessions,$accounts,$policy);
        $gate=new GuardianSubscriber($sessions,$policy,$settings,$audit,$accounts,$access);
        $path='/internal/verylongtemporaryroute987654321';
        $response=$controller->entry(new Request($path));
        yes($response->getStatusCode()===200 && strpos($response->getContent(),'Accès développeur')!==false,'Portail rend le formulaire de connexion');
        if (getenv('GUARDIAN_EXPORT_PREVIEW')==='1') {
            $markup=str_replace('<body>', '<body><div style="background:#142d50;color:#addcff;text-align:center;padding:9px;font:13px system-ui">APERÇU STATIQUE — maquette non connectée</div>', $response->getContent());
            file_put_contents($root.'/examples/preview-login.html',$markup);
        }
        $csrf=$response->headers->getCookies()[0]->getValue();
        $form=array('action'=>'login','username'=>'admin','password'=>'TestPassword!Works2026','_csrf'=>$csrf);
        $login=$controller->entry(new Request($path,'POST',$form,array(GuardianController::CSRF_COOKIE=>$csrf)));
        yes($login->getStatusCode()===303,'Authentification redirige vers le portail');
        $token=null;
        foreach ($login->headers->getCookies() as $cookie) {if ($cookie->getName()===Sessions::COOKIE) {$token=$cookie->getValue();}}
        yes(is_string($token)&&strlen($token)===64,'Session Guardian opaque créée');
        $request=new Request($path,'GET',array(),array(Sessions::COOKIE=>$token));
        $page=$controller->entry($request);
        yes(strpos($page->getContent(),'Centre de sécurité')!==false,'Dashboard authentifié accessible');
        $team=$sessions->activitySummary(array('admin'=>$accounts->find('admin')));
        yes($team['recent']===1 && $team['roles']['admin']===1,'Activité de l’équipe détectée sans révéler les jetons');
        yes(strpos($page->getContent(),'Activité de l’équipe')!==false,'Tableau de bord contient le nouveau panneau équipe');
        $support=$controller->entry(new Request($path,'GET',array(),array(Sessions::COOKIE=>$token),array('view'=>'support')));
        yes($support->getStatusCode()===200 && strpos($support->getContent(),'bundle-sec@omegaguardian.fr')!==false,
            'Page Support Bundle accessible et adresse correcte');
        $settingsPage=$controller->entry(new Request($path,'GET',array(),array(Sessions::COOKIE=>$token),array('view'=>'settings')));
        yes(strpos($settingsPage->getContent(),'guardian-error-pages')!==false,
            'Configuration des erreurs présente sur la page des réglages');
        $invalidMode=$controller->entry(new Request($path,'POST',array('action'=>'error_settings',
            '_csrf'=>$sessions->current($request)['csrf'],'password'=>'TestPassword!Works2026','error_mode'=>'redirect_200'),
            array(Sessions::COOKIE=>$token)));
        yes($invalidMode->getStatusCode()===400 && $settings->all()['error_mode']==='native',
            'Mode invalidé côté serveur et configuration inchangée');
        $validMode=$controller->entry(new Request($path,'POST',array('action'=>'error_settings',
            '_csrf'=>$sessions->current($request)['csrf'],'password'=>'TestPassword!Works2026',
            'error_mode'=>'generic','error_reference'=>'random','error_reference_text'=>'',
            'error_custom_text'=>''),array(Sessions::COOKIE=>$token)));
        yes($validMode->getStatusCode()===303 && $settings->all()['error_mode']==='generic',
            'Enregistrement du mode neutre avec mot de passe et CSRF');
        $settings->set(array('error_mode'=>'native', 'error_reference'=>'none'));
        if (getenv('GUARDIAN_EXPORT_PREVIEW')==='1') {
            $markup=str_replace('<body>', '<body><div style="background:#142d50;color:#addcff;text-align:center;padding:9px;font:13px system-ui">APERÇU STATIQUE — commandes non connectées</div>', $page->getContent());
            file_put_contents($root.'/examples/preview-dashboard.html',$markup);
        }
        yes(strpos($page->getContent(),'https://omegaguardian.fr')===false,'Footer sans lien ΩGuardian externe');
        $event=new RequestEvent(new Request('/_profiler/','GET',array(),array(Sessions::COOKIE=>$token)));
        $gate->guard($event);
        yes(!$event->hasResponse(),'Session autorisée peut accéder au profiler');
        $event=new RequestEvent(new Request('/_profiler/'));
        $gate->guard($event);
        yes($event->hasResponse() && $event->getResponse()->getStatusCode()===404,'Visiteur public bloqué avec 404');
        $event=new RequestEvent(new Request('/_wdt/test'));
        $gate->guard($event);
        yes($event->hasResponse() && $event->getResponse()->getStatusCode()===404,'Route toolbar bloquée');
        $event=new RequestEvent(new Request('/shop/cart'));
        $gate->guard($event);
        yes(!$event->hasResponse(),'Requête métier non interceptée');
        $event=new RequestEvent(new Request('/docs/_profiler/guide'));
        $gate->guard($event);
        yes(!$event->hasResponse(),'Route métier contenant le mot _profiler préservée');
        $response=new Response('OK',200);
        $response->headers->set('X-Debug-Token','should-be-removed');
        $privacy=new PrivacySubscriber($sessions,$accounts,$policy,$access);
        $privacy->onResponse(new ResponseEvent($response));
        yes($response->headers->get('X-Debug-Token')===null,'En-tête de diagnostic supprimé au public');
        $authorizedHeaders=new Response('OK',200);
        $authorizedHeaders->headers->set('X-Debug-Token','dev-token');
        $privacy->onResponse(new ResponseEvent($authorizedHeaders,new Request('/app','GET',array(),array(Sessions::COOKIE=>$token))));
        yes($authorizedHeaders->headers->get('X-Debug-Token')==='dev-token','En-tête de diagnostic restauré pour le développeur');
        $error404 = new class('not found',404) extends \RuntimeException implements \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface {
            public function getStatusCode() {return 404;}
            public function getHeaders() {return array();}
        };
        $err=new ExceptionEvent(new Request('/route-inexistante'),$error404);
        $privacy->onException($err);
        yes($err->getResponse()!==null && $err->getResponse()->getStatusCode()===404 && $err->getResponse()->getContent()==='Not Found','Erreur 404 publique : réponse générique');
        $err=new ExceptionEvent(new Request('/account','GET',array(),array()),new \RuntimeException('private /var/log/path'));
        $privacy->onException($err);
        yes($err->getResponse()!==null && strpos($err->getResponse()->getContent(),'private')===false,'Erreur 500 publique : chemins cachés');
        $err=new ExceptionEvent(new Request('/account','GET',array(),array(Sessions::COOKIE=>$token)),new \RuntimeException('debug'));
        $_SERVER['GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED']='1';
        $privacy->onException($err);
        yes($err->getResponse()===null,'Erreurs détaillées conservées pour le développeur Guardian autorisé');
        $e=new ExceptionEvent(new Request('/_profiler/foo','GET',array(),array(Sessions::COOKIE=>$token)),new \RuntimeException('debug'));
        $privacy->onException($e);
        yes($e->getResponse()===null,'Exceptions détaillées du profiler accessibles à un développeur autorisé');
        $e=new ExceptionEvent(new Request('/_profiler/foo'),new \RuntimeException('secret'));
        $privacy->onException($e);
        yes($e->getResponse()!==null && $e->getResponse()->getStatusCode()===404,'Exceptions du profiler restent interdites au public');
        $headerResponse=new Response('',404);
        foreach (array('X-Debug-Exception','X-Debug-Exception-File','X-Powered-By','X-Debug-Token') as $h) {$headerResponse->headers->set($h,'secret');}
        $privacy->onResponse(new ResponseEvent($headerResponse));
        yes($headerResponse->headers->get('X-Debug-Exception')===null && $headerResponse->headers->get('X-Debug-Exception-File')===null && $headerResponse->headers->get('X-Powered-By')===null,'Métadonnées d’erreur Symfony et version PHP supprimées');
        $_SERVER['GUARDIAN_SHOW_PHP_VERSION']='1';
        $privacy->onResponse(new ResponseEvent($headerResponse));
        yes($headerResponse->headers->get('X-Powered-By')==='PHP/'.PHP_VERSION,'Exposition volontaire PHP fonctionne uniquement sur opt-in');
        unset($_SERVER['GUARDIAN_SHOW_PHP_VERSION']);

        // Rendu des erreurs : aucun affichage des détails et aucun statut HTTP falsifié.
        $renderer=new \OmegaGuardian\ProfilerShield\Security\PublicErrorRenderer($settings, $root);
        $native404=$renderer->exceptionPage(404,$renderer->config());
        $templateDir=$dir.'/host/templates/bundles/TwigBundle/Exception';
        mkdir($templateDir,0700,true);
        file_put_contents($templateDir.'/error404.html.twig','site-owned template');
        $fakeTwig=new class {
            public function render($name,$data) {
                if ($name!=='@Twig/Exception/error404.html.twig' || $data['status_code']!==404) {throw new \RuntimeException('Template incorrect');}
                return '<main>Custom 404 owned by host</main>';
            }
        };
        $siteRenderer=new \OmegaGuardian\ProfilerShield\Security\PublicErrorRenderer($settings,$dir.'/host',$fakeTwig);
        $site404=$siteRenderer->exceptionPage(404,$siteRenderer->config());
        yes($site404->getContent()==='<main>Custom 404 owned by host</main>',
            '404 Twig du site utilisée si présente');
        yes($native404->getStatusCode()===404 && strpos($native404->getContent(),'Symfony')===false,
            '404 neutre sans page debug Symfony ni marqueur Guardian');
        $settings->set(array('error_mode'=>'generic','error_reference'=>'none'));
        $apiError=new ExceptionEvent(new Request('/api/order'),new \RuntimeException('secret from API'));
        $privacyApi=new PrivacySubscriber($sessions,$accounts,$policy,$access,$renderer);
        $privacyApi->onException($apiError);
        yes($apiError->getResponse()!==null && $apiError->getResponse()->getStatusCode()===500
            && $apiError->getResponse()->headers->get('Content-Type')==='application/json; charset=UTF-8'
            && strpos($apiError->getResponse()->getContent(),'secret')===false,
            'Erreur JSON publique non transformée en HTML ni divulguée');
        $sensitive=new Response('<h1>Exception: private path /srv/app</h1>',500,array('Content-Type'=>'text/html'));
        $privacyWithRenderer=new PrivacySubscriber($sessions,$accounts,$policy,$access,$renderer);
        $privacyWithRenderer->onResponse(new ResponseEvent($sensitive,new Request('/shop/error')));
        yes($sensitive->getStatusCode()===500 && strpos($sensitive->getContent(),'/srv/app')===false,
            'Mode neutre masque le corps HTML sans altérer HTTP 500');
        $ok=new Response('Business OK',200,array('Content-Type'=>'text/html'));
        $privacyWithRenderer->onResponse(new ResponseEvent($ok,new Request('/shop/cart')));
        yes($ok->getContent()==='Business OK','Pages métier 200 jamais transformées');
        $settings->set(array('error_mode'=>'native'));
        $custom404=new Response('<main>Page non trouvée du site</main>',404,array('Content-Type'=>'text/html'));
        $privacyWithRenderer->onResponse(new ResponseEvent($custom404,new Request('/shop/absent')));
        yes($custom404->getContent()==='<main>Page non trouvée du site</main>',
            '404 métier personnalisée préservée par défaut');
        $settings->set(array('error_mode'=>'custom','error_custom_text'=>'Message privé <script>bad()</script>',
            'error_reference'=>'custom','error_reference_text'=>'Ref fixe'));
        $escaped=$renderer->neutral(403,$renderer->config());
        yes($escaped->getStatusCode()===403 && strpos($escaped->getContent(),'<script>')===false
            && strpos($escaped->getContent(),'&lt;script&gt;')!==false,
            'Réponse personnalisée échappée sans code HTTP falsifié');
        $settings->set(array('error_mode'=>'native','error_reference'=>'none'));

        $store->change('control',function ($s) { $s['lockdown']=true;return $s; });
        $event=new RequestEvent(new Request('/_profiler/','GET',array(),array(Sessions::COOKIE=>$token)));
        $gate->guard($event);
        yes($event->hasResponse() && $event->getResponse()->getStatusCode()===404,'Lockdown bloque session authentifiée');
        $event=new RequestEvent(new Request('/shop/cart'));
        $gate->guard($event);
        yes(!$event->hasResponse(),'Lockdown ne bloque pas application métier');
        $store->change('control',function ($s) { $s['lockdown']=false;return $s; });
        $current=$sessions->current(new Request($path,'GET',array(),array(Sessions::COOKIE=>$token)));
        $adminPost=array('_csrf'=>$current['csrf'],'password'=>'TestPassword!Works2026');
        $new=$adminPost+array('action'=>'user_create','new_username'=>'developer2','new_password'=>'DeveloperPassword!2026','role'=>'developer');
        $result=$controller->entry(new Request($path,'POST',$new,array(Sessions::COOKIE=>$token)));
        yes($result->getStatusCode()===303 && $accounts->find('developer2')!==null,'Création d’un second compte depuis le dashboard');
        $roleChange=$adminPost+array('action'=>'user_role','username'=>'developer2','role'=>'security');
        $changed=$controller->entry(new Request($path,'POST',$roleChange,array(Sessions::COOKIE=>$token)));
        yes($changed->getStatusCode()===303 && $accounts->find('developer2')['role']==='security','Changement de rôle administrateur avec révocation');
        $ownRole=$adminPost+array('action'=>'user_role','username'=>'admin','role'=>'developer');
        $refused=$controller->entry(new Request($path,'POST',$ownRole,array(Sessions::COOKIE=>$token)));
        yes($refused->getStatusCode()===400 && $accounts->find('admin')['role']==='admin','Auto-rétrogradation d’un administrateur refusée');
        $revoke=$adminPost+array('action'=>'user_revoke','username'=>'developer2');
        yes($controller->entry(new Request($path,'POST',$revoke,array(Sessions::COOKIE=>$token)))->getStatusCode()===303,'Révocation ciblée des sessions disponible');
        $disabled=$adminPost+array('action'=>'user_disable','username'=>'developer2');
        $result=$controller->entry(new Request($path,'POST',$disabled,array(Sessions::COOKIE=>$token)));
        yes($result->getStatusCode()===303 && !empty($accounts->find('developer2')['disabled']),'Désactivation d’un compte avec mot de passe admin');
        $settingsForm=$adminPost+array(
            'action'=>'settings','stealth'=>'1','mfa'=>'0','schedule_enabled'=>'0',
            'jit_enabled'=>'0','emergency_enabled'=>'0','idle_minutes'=>'60','absolute_hours'=>'8',
            'remember_days'=>'14','jit_minutes'=>'30','timezone'=>\OmegaGuardian\ProfilerShield\Util\ServerClock::phpTimezone(),
            'start'=>'09:00','end'=>'18:00','weekdays'=>array('1','2','3','4','5'),
            'alert_email'=>'','audit_level'=>'standard','audit_retention_days'=>'30','ip_allowlist'=>''
        );
        $result=$controller->entry(new Request($path,'POST',$settingsForm,array(Sessions::COOKIE=>$token)));
        yes($result->getStatusCode()===303 && $settings->all()['idle_minutes']===60,'Enregistrement transactionnel de paramètres via POST');
        $differentZone=\OmegaGuardian\ProfilerShield\Util\ServerClock::phpTimezone()==='Asia/Tokyo'?'Europe/Paris':'Asia/Tokyo';
        $invalidZoneForm=array_replace($settingsForm,array('timezone'=>$differentZone));
        $declined=$controller->entry(new Request($path,'POST',$invalidZoneForm,array(Sessions::COOKIE=>$token)));
        yes($declined->getStatusCode()===400 && $settings->all()['timezone']!==$differentZone,'Décalage horaire refusé sans confirmation serveur');
        $accepted=$controller->entry(new Request($path,'POST',array_replace($invalidZoneForm,array('timezone_ack'=>'1')),array(Sessions::COOKIE=>$token)));
        yes($accepted->getStatusCode()===303 && $settings->all()['timezone']===$differentZone,'Décalage horaire accepté après confirmation');
        $settings->set(array('timezone'=>\OmegaGuardian\ProfilerShield\Util\ServerClock::phpTimezone()));
        $configuration=$controller->entry(new Request($path,'GET',array(),array(Sessions::COOKIE=>$token),array('view'=>'settings')));
        yes(strpos($configuration->getContent(),'guardian-access-schedule')!==false && strpos($configuration->getContent(),'week-selector')!==false,'Planning graphique inclus dans les paramètres');
        if (getenv('GUARDIAN_EXPORT_PREVIEW')==='1') {
            file_put_contents($root.'/examples/preview-settings.html',$configuration->getContent());
        }
        yes(strpos($configuration->getContent(),'https://omegaguardian.fr')===false,'Aucun lien externe sur la page de configuration');
        $translated=$controller->entry(new Request($path,'GET',array(),array(Sessions::COOKIE=>$token),array('lang'=>'en_GB','view'=>'settings')));
        yes(strpos($translated->getContent(),'Working hours and automatic lock')!==false,'Catalogue anglais affiché sur le calendrier');
        yes(strpos($translated->getContent(),'Your policy. Your workflow.')!==false,'Anciens textes de configuration également traduits en anglais');
        yes(strpos($translated->getContent(),'<html lang="en-GB"')!==false,'Langue HTML cohérente avec la langue choisie');
        yes(strpos($translated->getContent(),'PHP server time zone')!==false,'Fuseau PHP visible en anglais');
        if (getenv('GUARDIAN_EXPORT_PREVIEW')==='1') { file_put_contents($root.'/examples/preview-settings-en.html',$translated->getContent()); }
        yes(count($translated->headers->getCookies())>=1,'Préférence linguistique persistée par cookie sécurisé');
        $tomorrow=((int)(new \DateTimeImmutable('now',new \DateTimeZone('Europe/Paris')))->format('N'))%7+1;
        $settings->set(array('schedule_enabled'=>true,'weekdays'=>array($tomorrow),'start'=>'09:00','end'=>'18:00'));
        $event=new RequestEvent(new Request('/_profiler/','GET',array(),array(Sessions::COOKIE=>$token)));
        $gate->guard($event);
        yes($event->hasResponse(),'TimeLock refuse même le cookie authentifié');
        $restrictedHeaders=new Response('',200);
        $restrictedHeaders->headers->set('X-Debug-Token','secret');
        $privacy->onResponse(new ResponseEvent($restrictedHeaders,new Request('/private','GET',array(),array(Sessions::COOKIE=>$token))));
        yes($restrictedHeaders->headers->get('X-Debug-Token')===null,'En-têtes redeviennent privés lorsque TimeLock verrouille la session');
        $settings->set(array('emergency_enabled'=>true));
        $recovery='ABCD1234ABCD1234ABCD1234';
        $store->change('emergency_codes',function ($state) use ($recovery) { $state['admin']=array(password_hash($recovery,PASSWORD_DEFAULT));return $state; });
        $emergency=$adminPost+array('action'=>'emergency','code'=>$recovery,'reason'=>'Incident de maintenance autorisé');
        $result=$controller->entry(new Request($path,'POST',$emergency,array(Sessions::COOKIE=>$token)));
        yes($result->getStatusCode()===303,'Dérogation d’urgence à usage unique accordée');
        yes(count($store->read('alerts'))===1,'Alerte d’urgence mise en file avant ouverture');
        yes(count($store->read('emergency_codes')['admin'])===0,'Code de récupération consommé');
        $event=new RequestEvent(new Request('/_profiler/','GET',array(),array(Sessions::COOKIE=>$token)));
        $gate->guard($event);
        yes(!$event->hasResponse(),'Dérogation active ouvre le profiler hors TimeLock');
        $result=$controller->entry(new Request($path,'POST',$emergency,array(Sessions::COOKIE=>$token)));
        yes($result->getStatusCode()===400,'Code d’urgence réutilisé refusé');
        // Régression de l'assistant MFA : un seul mot de passe Guardian,
        // code à six chiffres et messages locaux dans le panneau de réglage.
        $mfaStartBad=array('_csrf'=>$current['csrf'],'action'=>'mfa_prepare','password'=>'wrong-value');
        $bad=$controller->entry(new Request($path,'POST',$mfaStartBad,array(Sessions::COOKIE=>$token)));
        yes($bad->getStatusCode()===400 && strpos($bad->getContent(),'guardian-mfa')!==false
            && strpos($bad->getContent(),'Mot de passe de votre compte Guardian incorrect')!==false,
            'Erreur de mot de passe MFA directement dans le formulaire de configuration');
        $mfaStartGood=array('_csrf'=>$current['csrf'],'action'=>'mfa_prepare','password'=>'TestPassword!Works2026');
        $good=$controller->entry(new Request($path,'POST',$mfaStartGood,array(Sessions::COOKIE=>$token)));
        yes($good->getStatusCode()===303,'Vérification initiale du mot de passe ouvre la configuration TOTP');
        $pending=$sessions->current(new Request($path,'GET',array(),array(Sessions::COOKIE=>$token)));
        yes(!empty($pending['pending_totp']) && $pending['pending_until']>time(), 'Secret temporaire lié à la session chiffrée');
        $mfaPage=$controller->entry(new Request($path,'GET',array(),array(Sessions::COOKIE=>$token),array('view'=>'settings')));
        $mfaHtml=$mfaPage->getContent();
        yes(strpos($mfaHtml,'guardian-mfa')!==false && strpos($mfaHtml,'Ne saisissez pas l’adresse')!==false,
            'Assistant QR et avertissement de confidentialité visibles');
        yes(strpos($mfaHtml,'otpauth://')===false && strpos($mfaHtml,'duckduckgo.com')===false,
            'URI TOTP jamais envoyée ou exposée en clair dans des URLs HTML');
        preg_match('~<form method="post" class="mfa-confirm-form"[^>]*>(.*?)</form>~s',$mfaHtml,$mfaMatch);
        yes(isset($mfaMatch[1]) && strpos($mfaMatch[1],'name="password"')===false,
            'Seul le code TOTP est demandé en seconde étape, pas le mot de passe');
        $badCode=$controller->entry(new Request($path,'POST',array('_csrf'=>$current['csrf'],'action'=>'mfa_confirm','otp'=>'000000'),array(Sessions::COOKIE=>$token)));
        yes($badCode->getStatusCode()===400 && strpos($badCode->getContent(),'mfa-confirm-form')!==false
            && strpos($badCode->getContent(),'Code incorrect')!==false,
            'Code TOTP erroné : erreur locale, QR et formulaire préservés');
        $pending=$sessions->current(new Request($path,'GET',array(),array(Sessions::COOKIE=>$token)));
        $testSecret=(new Totp())->decrypt($pending['pending_totp']);
        // Calcul TOTP selon RFC 6238 pour vérifier le parcours réel sans
        // dépendre d'une application mobile dans ce test automatisé.
        $alphabet=Totp::BASE32;
        $bitString='';
        for ($i=0;$i<strlen($testSecret);$i++) {
            $index=strpos($alphabet,$testSecret[$i]);
            $bitString.=str_pad(decbin($index),5,'0',STR_PAD_LEFT);
        }
        $decoded='';
        foreach (str_split($bitString,8) as $bits) { if (strlen($bits)===8) {$decoded.=chr(bindec($bits));} }
        $ctr=(int) floor(time()/30);
        $hmac=hash_hmac('sha1',pack('N2',0,$ctr),$decoded,true);
        $offset=ord(substr($hmac,-1)) & 15;
        $value=unpack('N',substr($hmac,$offset,4))[1] & 0x7fffffff;
        $testCode=str_pad((string)($value % 1000000),6,'0',STR_PAD_LEFT);
        $success=$controller->entry(new Request($path,'POST',array('_csrf'=>$current['csrf'],'action'=>'mfa_confirm','otp'=>$testCode),array(Sessions::COOKIE=>$token)));
        yes($success->getStatusCode()===303 && !empty($accounts->find('admin')['totp']),
            'Code TOTP valide active MFA sans second mot de passe');
        $after=$sessions->current(new Request($path,'GET',array(),array(Sessions::COOKIE=>$token)));
        yes(empty($after['pending_totp']) && $accounts->find('admin')['last_otp_counter']===$ctr,
            'Secret provisoire effacé et code TOTP consommé contre le rejeu');
        echo "Smoke test substitut : terminé. Tests Symfony réels restent nécessaires.\n";
    } finally {
        if (is_dir($dir)) {foreach (glob($dir.'/*') as $file) {@unlink($file);}@rmdir($dir);}
    }
}
