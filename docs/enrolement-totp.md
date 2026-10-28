# Enrôler votre authentificateur TOTP

## Parcours administrateur

1. Connectez-vous à Guardian puis ouvrez **Configuration → Double authentification personnelle**.
2. Saisissez **le mot de passe de connexion à votre compte Guardian** (pas votre code TOTP, ni votre mot de passe Symfony). Il est vérifié **une seule fois**.
3. Scannez le QR affiché depuis l'application TOTP de votre choix. Le QR est produit localement par le serveur avec une bibliothèque PHP. Il n'est jamais envoyé à un générateur QR externe.
4. Saisissez uniquement le **code à six chiffres** affiché par l'application. L'enrôlement est immédiatement activé si le code est correct.

Si vous ne pouvez pas scanner, copiez manuellement la clé secrète affichée. Elle est aussi sensible qu'un mot de passe : ne la partagez pas, ne la téléversez pas, ne la collez pas dans DuckDuckGo et ne la prenez pas en photo.

**Vie privée :** lorsque votre application TOTP propose d'associer une URL ou un site au compte, utilisez seulement « ΩGuardian » (ou un alias neutre). N'indiquez pas l'URL secrète du site Symfony. L'URI `otpauth://` émise par Guardian porte un nom de fournisseur neutre et le nom du compte, **sans le domaine de l'application**.

## Si le code est erroné

Le panneau reste ouvert avec son QR et son formulaire. Il n'est pas nécessaire de retaper le mot de passe. Les secrets provisoires expirent au bout de dix minutes ou après cinq échecs ; la configuration peut alors être recommencée.

## Si le QR ne s'affiche pas

La clé de saisie manuelle reste disponible. Vérifiez l'installation Composer de `bacon/bacon-qr-code` et la présence de `ext-iconv`. Le générateur SVG local ne nécessite ni GD, ni XMLWriter, ni accès réseau pour produire une image.

## Important

Le compte ne peut pas réutiliser immédiatement le code utilisé à la confirmation : Guardian consomme son compteur pour réduire le risque de rejeu. La réinitialisation du MFA reste possible via la commande administrative locale.
