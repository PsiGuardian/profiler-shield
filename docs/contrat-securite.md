# Contrat de sécurité du projet

Les règles suivantes ne sont pas des paramètres que l'administrateur peut
cocher/décocher : elles sont des invariants de conception.

1. Aucune autorisation par défaut si session, rôle, calendrier, lockdown,
   restriction IP ou stockage ne peut être vérifié.
2. Un code d'urgence n'est pas une alternative au mot de passe : il exige
   une identité Guardian déjà authentifiée.
3. Le navigateur n'est jamais l'autorité sur l'expiration d'une session.
4. L'accès au profiler est distinct de la permission de collecter des profils.
5. Le site métier n'est jamais bloqué par une règle TimeLock ou lockdown.
6. La clé de chiffrement TOTP n'est pas stockée dans le dépôt et les secrets
   TOTP ne sont pas envoyés à des services externes.
7. Une demande de configuration invalidée ne doit pas modifier partiellement
   l'état opérationnel.
8. Une option facultative ne doit pas activer de dépendance réseau obligatoire.
9. Une panne critique d'audit pendant la procédure d'urgence interdit
   l'ouverture de la dérogation.
10. Une action désactivant le bundle doit s'accompagner d'une stratégie hôte
    qui empêche la réexposition des outils de diagnostic.
