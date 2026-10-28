<?php
namespace OmegaGuardian\ProfilerShield\EventListener;

use OmegaGuardian\ProfilerShield\Security\Sessions;
use OmegaGuardian\ProfilerShield\Security\Accounts;
use OmegaGuardian\ProfilerShield\Security\Policy;
use OmegaGuardian\ProfilerShield\Util\Env;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Profiler\Profiler;

/**
 * Pont facultatif vers le collecteur Symfony, STRICTEMENT limité aux sessions
 * Guardian autorisées. Le paramètre d'activation est explicite.
 *
 * Préconditions obligatoires côté application :
 *   framework.profiler.collect: false
 *   AUCUN collect_parameter public
 *   web_profiler.toolbar: true (les visiteurs ne collectent pas de profil)
 *
 * Limite importante : l'ordre effectif des listeners de collecte Symfony
 * varie selon sa branche. Valider avec debug:event-dispatcher et tests HTTP.
 * Tant que cette validation n'est pas acquise, laisser le pont DÉSACTIVÉ.
 */
final class CollectionSubscriber implements EventSubscriberInterface
{
    private $profiler;private $sessions;private $accounts;private $policy;

    public function __construct($profiler, Sessions $sessions, Accounts $accounts, Policy $policy)
    {
        $this->profiler=$profiler;
        $this->sessions=$sessions;
        $this->accounts=$accounts;
        $this->policy=$policy;
    }

    public static function getSubscribedEvents(): array
    {
        return array(KernelEvents::REQUEST => array('onRequest', 3072));
    }

    public function onRequest(RequestEvent $event)
    {
        if (!$event->isMainRequest() || $this->profiler === null || Env::get('GUARDIAN_COLLECTION_BRIDGE') !== '1') { return; }
        $request=$event->getRequest();
        // Le collecteur est fermé d'abord. La lecture de stockage et la
        // décision d'ouverture interviennent APRÈS cette fermeture.
        $this->profiler->disable();
        if (GuardianSubscriber::isDiagnosticPath($request->getPathInfo())) { return; }
        if ($request->getPathInfo() === Env::get('GUARDIAN_LOGIN_PATH')) { return; }
        if (!$request->cookies->has(Sessions::COOKIE)) { return; }
        try {
            $session=$this->sessions->current($request);
            if (!$session) { return; }
            $account=$this->accounts->find($session['username']);
            if ($account !== null && $this->policy->ipAllowed($request->getClientIp())
                && $this->policy->allowed($session,$account)) {
                $this->profiler->enable();
            }
        } catch (\Throwable $ignored) {
            // L'absence d'une décision fiable conserve le profiler fermé.
            $this->profiler->disable();
        }
    }
}
