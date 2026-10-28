# Double authentification personnelle

[Sommaire de la documentation](README.md)


## Double authentification personnelle

Activez un code temporaire sur votre compte Guardian, sans service QR externe.

Chaque personne enrôle son propre secret TOTP et confirme un code. Activer l’exigence MFA bloque le profiler pour les comptes non enrôlés. L’affichage du QR code reste local au portail. Prévoyez une récupération administrative avant d’imposer cette protection à toute l’équipe.

## 1. Confirmez votre identité

Utilisez le mot de passe avec lequel vous vous connectez à Guardian. Il sera demandé une seule fois pour cette opération.

## 2. Scannez le QR depuis votre authentificateur

Le QR est généré localement par Guardian, sans aucun appel à un service externe.

## 3. Confirmez un code à six chiffres

Saisissez le code affiché par votre application. Aucun second mot de passe n’est demandé.

## Confidentialité : n’indiquez pas l’URL du site

Si votre application demande un nom ou un site, indiquez seulement « ΩGuardian » (ou un alias neutre). Ne saisissez pas l’adresse de votre instance Symfony. N’utilisez jamais un générateur QR en ligne : il recevrait votre secret.

À conserver privée. Elle donne la possibilité de générer vos codes TOTP. Ne la partagez pas et évitez les captures d’écran.

Le secret provisoire expire après 10 minutes ou 5 codes incorrects ; recommencer alors la configuration. Un code erroné conserve le formulaire sans redemander le mot de passe. Le compteur du code de confirmation est consommé pour éviter son rejeu. Le QR SVG utilise bacon/bacon-qr-code et ext-iconv, sans GD, XMLWriter ni réseau. La saisie manuelle reste possible. L’URI otpauth utilise un fournisseur neutre et le compte, sans domaine du site ; ne jamais partager le secret ou l’URL privée.

## Récupération et vérifications

Efface le secret TOTP et le compteur du compte username, révoque ses sessions et journalise l’action. Enrôler un nouvel authentificateur ; l’exigence MFA globale n’est pas désactivée et peut continuer à interdire le profiler.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```
