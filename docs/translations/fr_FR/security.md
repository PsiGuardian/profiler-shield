# Politique de sécurité

[Sommaire de la documentation](README.md)

Signaler une vulnérabilité en privé à bundle-sec@omegaguardian.fr pour convenir d’un canal chiffré. Aucun secret, jeton actif ou URL privée dans le premier message. Indiquer versions Guardian, Symfony et PHP, impact, prérequis et reproduction minimale. Les mainteneurs accusent réception, examinent, corrigent en privé, testent et coordonnent l’avis et le correctif.

Ne communiquez jamais de mots de passe, de secrets TOTP, de clés de chiffrement ni d’URL de diagnostic dans un courriel.

Sauvegarder le coffre avec sa clé et tester la restauration. Aucun mécanisme ne régénère automatiquement une clé perdue. Après retrait, vérifier le profiler uniquement en environnement privé. En production : APP_DEBUG=0, WebProfilerBundle et collecte désactivés. Une chaîne d’audit locale ne prouve pas l’intégrité d’un hôte compromis.
