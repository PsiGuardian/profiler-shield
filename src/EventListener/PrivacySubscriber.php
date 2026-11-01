<?php
namespace OmegaGuardian\ProfilerShield\EventListener;

use OmegaGuardian\ProfilerShield\Security\Accounts;
use OmegaGuardian\ProfilerShield\Security\Policy;
use OmegaGuardian\ProfilerShield\Security\Sessions;
use OmegaGuardian\ProfilerShield\Security\DiagnosticAccess;
use OmegaGuardian\ProfilerShield\Security\PublicErrorRenderer;
use OmegaGuardian\ProfilerShield\Security\PublicErrorRegistry;
use OmegaGuardian\ProfilerShield\Security\SafeNativeRenderer;
use OmegaGuardian\ProfilerShield\Util\Env;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Protection des métadonnées et exceptions visibles aux visiteurs.
 *
 * IMPORTANT : l'affichage d'une page d'exception Symfony en APP_DEBUG=1
 * est une source de divulgation indépendante de /_profiler et /_wdt.
 * Ce listener se place AVANT le ErrorListener standard de Symfony.
 * En l'absence d'une session Guardian vérifiable, il fournit une erreur
 * neutre, en conservant le statut HTTP approprié.
 *
 * Les réponses métier ordinaires sans en-tête de diagnostic ne déclenchent
 * aucune lecture du coffre. Une réponse avec métadonnées X-Debug peut nécessiter
 * une décision locale ; une panne entraîne alors leur suppression, jamais une 500.
 *
 * LIMITES : cette barrière s'applique seulement aux réponses construites
 * par le noyau Symfony. Les erreurs antérieures à PHP/Symfony, les serveurs
 * web et les caches inverses nécessitent leurs propres réglages.
 */
final class PrivacySubscriber implements EventSubscriberInterface
{
    private $sessions;
    private $accounts;
    private $policy;
    private $diagnosticAccess;
    private $errorRenderer;
    private PublicErrorRegistry $publicErrorRegistry;
    private SafeNativeRenderer $safeNative;
    public function __construct(Sessions $sessions, Accounts $accounts, Policy $policy, DiagnosticAccess $diagnosticAccess, ?PublicErrorRenderer $errorRenderer = null, ?PublicErrorRegistry $publicErrorRegistry = null)
    {
        $this->sessions = $sessions;
        $this->accounts = $accounts;
        $this->policy = $policy;
        $this->diagnosticAccess = $diagnosticAccess;
        $this->errorRenderer = $errorRenderer;
        $this->publicErrorRegistry = $publicErrorRegistry ?? new PublicErrorRegistry();
        $this->safeNative = new SafeNativeRenderer();
    }

    public static function getSubscribedEvents(): array
    {
        return array(
            KernelEvents::EXCEPTION => array('onException', 4096),
            KernelEvents::RESPONSE => array('onResponse', -16384)
        );
    }

    public function onException(ExceptionEvent $event)
    {
        if (!$event->isMainRequest()) { return; }
        $request=$event->getRequest();
        // Autorisé : Symfony conserve son propre gestionnaire d'exceptions.
        // C'est essentiel pour restaurer le profiler et les détails d'erreur
        // en mode dev, SANS les publier pour un visiteur anonyme.
        if (Env::get('GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED') === '1'
            && $this->diagnosticAccess->allowed($request)) {
            return;
        }
        // Sur les routes du profiler, refuser les exceptions publiques même
        // si le contrôleur de diagnostic est défaillant.
        if (GuardianSubscriber::isDiagnosticPath($request->getPathInfo())) {
            $event->setResponse($this->publicPage(404));
            return;
        }
        $error=$event->getThrowable();
        $status=$error instanceof HttpExceptionInterface ? $error->getStatusCode() : 500;
        if ($status<400 || $status>599) { $status=500; }
        // Safe Native: an explicitly tagged application mapper may publish
        // a strictly validated business error. Unknown exceptions always
        // remain neutral; nothing is copied from Throwable::getMessage().
        $approved = $this->publicErrorRegistry->resolve($error, $request);
        $format = $this->safeNative->format($request);
        if ($format === 'html') {
            // Keep the established native/generic/custom HTML behavior.
            $response = $this->publicPage($status);
        } else {
            try {
                $response = $this->safeNative->render($status, $format, $approved);
            } catch (\Throwable) {
                // A failing mapper/renderer must never cause a second public
                // error containing stack details or filesystem information.
                $response = $this->generic($status);
            }
        }
        $this->preserveHttpHeaders($response, $error);
        $event->setResponse($response);
    }

    /** Preserve protocol semantics without copying diagnostic headers. */
    private function preserveHttpHeaders(Response $response, $error)
    {
        if (!$error instanceof HttpExceptionInterface) { return; }
        foreach ($error->getHeaders() as $name => $value) {
            if (in_array(strtolower($name), array('allow','retry-after','www-authenticate'), true)) {
                $response->headers->set($name, $value);
            }
        }
    }

    public function onResponse(ResponseEvent $event)
    {
        $response=$event->getResponse();
        $request=$event->getRequest();
        $status=$response->getStatusCode();
        // Mode obfuscation EXPLICITE : seules les erreurs HTML publiques
        // sont remplacées. Le défaut "native" garde intacts les 404 déjà
        // produits par l’hôte, les API et l'ensemble des réponses métier.
        if ($this->errorRenderer !== null && $status >= 400 && $status <= 599
            && PublicErrorRenderer::acceptsHtml($request)
            && (!isset($request->attributes) || !method_exists($request->attributes, 'get')
                || !is_string($request->attributes->get('_controller',''))
                || (strpos($request->attributes->get('_controller',''), 'GuardianController') === false
                    && strpos($request->attributes->get('_controller',''), 'omega_guardian.controller::') !== 0))) {
            $type=(string)$response->headers->get('Content-Type','');
            if ($type === '' || stripos($type, 'text/html') !== false) {
                $config=$this->errorRenderer->config();
                if (($config['error_mode'] ?? 'native') !== 'native'
                    && !$this->diagnosticAccess->allowed($request)) {
                    $new=$this->errorRenderer->neutral($status,$config);
                    $response->setContent($new->getContent());
                    foreach (array('Content-Type','Content-Security-Policy','Cache-Control',
                        'Referrer-Policy','X-Content-Type-Options') as $header) {
                        $response->headers->set($header,$new->headers->get($header));
                    }
                    // Pas de longueur périmée, d'ETag ou de compression héritée.
                    foreach (array('Content-Length','Content-Encoding','ETag','Last-Modified','Expires') as $header) {
                        $response->headers->remove($header);
                    }
                }
            }
        }
        // Les métadonnées Symfony sont visibles uniquement pour les comptes
        // qui ont le droit d'ouvrir le profiler à cet instant. En particulier,
        // un cookie expiré, un compte désactivé ou un TimeLock actif ne suffisent
        // jamais à conserver les en-têtes X-Debug-*.
        // Pas de consultation du coffre pour une réponse métier sans métadonnées
        // de diagnostic : cela évite tout coût et toute dépendance inutile.
        $debugNames=array('X-Debug-Token','X-Debug-Token-Link','X-Profiler-Token',
            'X-Debug-Exception','X-Debug-Exception-File');
        $hasDebugHeaders=false;
        foreach ($debugNames as $name) {
            if ($response->headers->get($name) !== null) { $hasDebugHeaders=true; break; }
        }
        if ($hasDebugHeaders && !$this->diagnosticAccess->allowed($event->getRequest())) {
            foreach ($debugNames as $name) { $response->headers->remove($name); }
        }
        $response->headers->remove('X-Powered-By');
        // Le header X-Powered-By est parfois émis directement par PHP,
        // sans passer par HttpFoundation. PHP-FPM doit AUSSI avoir expose_php=Off.
        if (function_exists('header_remove') && !headers_sent()) {
            @header_remove('X-Powered-By');
        }
        // Révéler la version PHP est un choix explicite et risqué.
        // Jamais activé automatiquement par les profils prédéfinis.
        if (Env::get('GUARDIAN_SHOW_PHP_VERSION') === '1') {
            $response->headers->set('X-Powered-By', 'PHP/'.PHP_VERSION);
        }
    }

    private function publicPage($status)
    {
        if ($this->errorRenderer !== null) {
            try { return $this->errorRenderer->exceptionPage($status, $this->errorRenderer->config()); }
            catch (\Throwable $ignored) { /* l'erreur publique ne peut jamais créer une 500 secondaire */ }
        }
        return $this->generic($status);
    }

    private function generic($status)
    {
        $body = $status===404 ? 'Not Found' : ($status===403 ? 'Forbidden' : 'Request failed');
        return new Response($body,$status,array(
            'Content-Type'=>'text/plain; charset=UTF-8',
            'Cache-Control'=>'no-store, private',
            'X-Content-Type-Options'=>'nosniff',
            'Referrer-Policy'=>'no-referrer'
        ));
    }
}
