# Double authentification personnelle

[Sommaire de la documentation](README.md)


## Double authentification personnelle

Chaque personne enrôle son propre secret TOTP et confirme un code.

Chaque personne enrôle son propre secret TOTP et confirme un code. Activer l’exigence MFA bloque le profiler pour les comptes non enrôlés. Prévoyez une récupération administrative avant d’imposer cette protection à toute l’équipe. Choisissez l’ajout manuel et saisissez la clé secrète affichée. Configurez SHA-1, six chiffres et une période de 30 secondes.

## 1. Confirmez votre identité

Utilisez le mot de passe avec lequel vous vous connectez à Guardian. Il sera demandé une seule fois pour cette opération.

## 2. Saisissez la clé dans votre authentificateur

Choisissez l’ajout manuel et saisissez la clé secrète affichée. Configurez SHA-1, six chiffres et une période de 30 secondes.

## 3. Confirmez un code à six chiffres

Saisissez le code affiché par votre application. Aucun second mot de passe n’est demandé.

## Confidentialité : n’indiquez pas l’URL du site

Si votre application demande un nom ou un site, indiquez seulement « ΩGuardian » (ou un alias neutre). Ne saisissez pas l’adresse de votre instance Symfony.

À conserver privée. Elle donne la possibilité de générer vos codes TOTP. Ne la partagez pas et évitez les captures d’écran.

Le secret provisoire expire après 10 minutes ou 5 codes incorrects ; recommencer alors la configuration. Un code erroné conserve le formulaire sans redemander le mot de passe. Le compteur du code de confirmation est consommé pour éviter son rejeu.

## Récupération et vérifications

Efface le secret TOTP et le compteur du compte username, révoque ses sessions et journalise l’action. Enrôler un nouvel authentificateur ; l’exigence MFA globale n’est pas désactivée et peut continuer à interdire le profiler.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```
