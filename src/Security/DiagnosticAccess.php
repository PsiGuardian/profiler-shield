<?php
namespace OmegaGuardian\ProfilerShield\Security;

use Symfony\Component\HttpFoundation\Request;

/**
 * Décision unique utilisée par la passerelle, les erreurs et les métadonnées.
 *
 * Invariant : aucune information de diagnostic ne devient publique si un
 * composant Guardian échoue. L'absence de cookie évite toute lecture du
 * stockage sur les requêtes normales des visiteurs.
 *
 * Une session valide n'est PAS une autorisation suffisante : l'état actuel
 * du compte, l'adresse réseau, TimeLock, JIT et Lockdown sont réévalués.
 */
final class DiagnosticAccess
{
    private $sessions;
    private $accounts;
    private $policy;

    public function __construct(Sessions $sessions, Accounts $accounts, Policy $policy)
    {
        $this->sessions = $sessions;
        $this->accounts = $accounts;
        $this->policy = $policy;
    }

    public function allowed(Request $request)
    {
        if (!$request->cookies->has(Sessions::COOKIE)) { return false; }
        try {
            if (!$this->policy->ipAllowed($request->getClientIp())) { return false; }
            $session = $this->sessions->current($request);
            if (!$session || empty($session['username'])) { return false; }
            $account = $this->accounts->find($session['username']);
            return $account !== null && $this->policy->allowed($session, $account);
        } catch (\Throwable $ignored) {
            // Fermeture sur erreur, sans toucher au fonctionnement du site.
            return false;
        }
    }
}
