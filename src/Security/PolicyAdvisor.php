<?php
namespace OmegaGuardian\ProfilerShield\Security;

/**
 * Conseiller statique non prescriptif : aucune IA, aucun accès réseau.
 * N'infère pas un score de sécurité, n'active jamais automatiquement une règle.
 * Chaque recommandation doit décrire le contexte et la contrepartie métier.
 */
final class PolicyAdvisor
{
    public static function advise(array $settings, array $users)
    {
        $advice = array();
        if (!$settings['mfa']) {
            $advice[] = array('title'=>'MFA facultative', 'detail'=>'Si ce portail est accessible depuis Internet, proposer TOTP réduit le risque lié aux mots de passe compromis. Sur un poste isolé, tu peux conserver ce choix.');
        } else {
            $advice[] = array('title'=>'MFA obligatoire', 'detail'=>'Les comptes doivent posséder un authentificateur TOTP actif ; préparer la procédure CLI de récupération.');
        }
        if ($settings['ip_allowlist']==='') {
            $advice[] = array('title'=>'Aucune restriction réseau', 'detail'=>'Cohérent pour un développeur mobile ; préférer un VPN ou un CIDR si l’équipe dispose d’une passerelle stable.');
        }
        if ($settings['schedule_enabled']) {
            $advice[] = array('title'=>'Fermeture horaire activée', 'detail'=>'Vérifier que les astreintes et les déplacements sont couverts par des procédures de dérogation ou de récupération locale.');
        } else {
            $advice[] = array('title'=>'TimeLock non imposé', 'detail'=>'Approprié aux horaires flexibles : la connexion et les sessions restent protégées.');
        }
        if ($settings['jit_enabled']) {
            $advice[] = array('title'=>'JIT activé', 'detail'=>'Les droits de consultation expirent automatiquement. Vérifier séparément l’activation de la collecte Symfony.');
        }
        foreach ($users as $user) {
            if (!$user['disabled'] && !$user['mfa'] && $settings['mfa']) {
                $advice[] = array('title'=>'Compte non enrôlé', 'detail'=>'Un compte actif doit enrôler TOTP avant de pouvoir se connecter.');
                break;
            }
        }
        return $advice;
    }
}
