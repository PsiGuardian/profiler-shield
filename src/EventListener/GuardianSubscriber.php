<?php
namespace OmegaGuardian\ProfilerShield\EventListener;

use OmegaGuardian\ProfilerShield\Security\Sessions;
use OmegaGuardian\ProfilerShield\Security\Policy;
use OmegaGuardian\ProfilerShield\Security\Settings;
use OmegaGuardian\ProfilerShield\Security\Audit;
use OmegaGuardian\ProfilerShield\Security\Accounts;
use OmegaGuardian\ProfilerShield\Security\DiagnosticAccess;
use OmegaGuardian\ProfilerShield\Security\PublicErrorRenderer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Response;

/**
 * PASSERELLE PRÉ-ROUTAGE : protège /_profiler et /_wdt à priorité élevée.
 *
 * Invariant de disponibilité : AUCUN accès au stockage / réseau sur les
 * routes métier. La lecture de chemin est une opération constante.
 * Invariant de sécurité : toute exception des services de sécurité
 * provoque un refus générique sur une route technique.
 *
 * Limites : les proxys/CDN et serveurs HTTP doivent fournir des règles
 * concordantes ; la collecte globale doit rester désactivée et la toolbar n'est autorisée qu'après une collecte contrôlée.
 */
final class GuardianSubscriber implements EventSubscriberInterface
{
    private $sessions;
    private $policy;
    private $settings;
    private $audit;
    private $accounts;
    private $diagnosticAccess;
    private $errorRenderer;

    public function __construct(Sessions $sessions, Policy $policy, Settings $settings, Audit $audit, Accounts $accounts, DiagnosticAccess $diagnosticAccess, ?PublicErrorRenderer $errorRenderer = null)
    {
        $this->sessions = $sessions;
        $this->policy = $policy;
        $this->settings = $settings;
        $this->audit = $audit;
        $this->accounts = $accounts;
        $this->diagnosticAccess = $diagnosticAccess;
        $this->errorRenderer = $errorRenderer;
    }

    public static function getSubscribedEvents(): array
    {
        return array(
            KernelEvents::REQUEST => array('guard', 4096)
        );
    }

    public static function isDiagnosticPath($path)
    {
        // Une seule étape de décodage défensive ; le serveur web doit
        // aussi bloquer les représentations alternatives dangereuses.
        // Double décodage défensif : certains frontaux normalisent deux fois.
        // La configuration du serveur web reste la protection de référence.
        $path = rawurldecode(rawurldecode((string) $path));
        $path = str_replace('\\', '/', $path);
        // Ne pas bloquer une route métier légitime telle que /docs/_profiler/guide.
        // Les montages de profiler sous un préfixe personnalisé doivent être
        // protégés séparément au routeur/serveur web puis testés.
        return (bool) preg_match('~^/(?:index\.php/|app_dev\.php/|app\.php/)?(?:_profiler|_wdt)(?:/|$)~i', $path);
    }

    public function guard(RequestEvent $event)
    {
        if (!$event->isMainRequest()) { return; }
        $request = $event->getRequest();
        if (!self::isDiagnosticPath($request->getPathInfo())) { return; }
        // Le résultat est identique à celui des listeners d'exception et
        // d'en-têtes. Toute erreur aboutit à une décision négative.
        $approved = $this->diagnosticAccess->allowed($request);
        $status = 404;
        if (!$approved) {
            try { $status = $this->settings->all()['stealth'] ? 404 : 403; }
            catch (\Throwable $ignored) { $status = 404; }
        }
        if (!$approved) {
            $response = new Response('Not Found', $status, array(
                'Content-Type'=>'text/plain; charset=UTF-8',
                'Cache-Control'=>'no-store, private',
                'X-Content-Type-Options'=>'nosniff',
                'Referrer-Policy'=>'no-referrer'
            ));
            if ($this->errorRenderer !== null && PublicErrorRenderer::acceptsHtml($request)) {
                try { $response=$this->errorRenderer->exceptionPage($status,$this->errorRenderer->config()); }
                catch (\Throwable $ignored) { /* toujours refuser un diagnostic */ }
            }
            $event->setResponse($response);
        }
    }

}
