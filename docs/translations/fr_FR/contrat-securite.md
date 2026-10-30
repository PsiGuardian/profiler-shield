# Invariants de sécurité

[Sommaire de la documentation](README.md)

Refuser les diagnostics si identité, rôle, session, réseau, horaires, JIT ou verrouillage ne peuvent être vérifiés. Le navigateur ne décide pas des expirations. L’accès aux profils et la collecte sont distincts. Un appareil reconnu n’authentifie pas. L’urgence exige une identité, un code consommé et un audit durable ; une panne critique d’audit interdit l’ouverture. Une configuration invalide ne doit pas être partiellement appliquée. Les options facultatives n’imposent aucun service distant.

Sauvegarder le coffre avec sa clé et tester la restauration. Aucun mécanisme ne régénère automatiquement une clé perdue. Après retrait, vérifier le profiler uniquement en environnement privé. En production : APP_DEBUG=0, WebProfilerBundle et collecte désactivés. Une chaîne d’audit locale ne prouve pas l’intégrité d’un hôte compromis.

Guardian contrôle le profiler et la barre de débogage. Un compte administrateur ou développeur autorisé est nécessaire. MFA, horaires, JIT, restrictions réseau et verrouillage peuvent encore refuser l’accès. Le mode discret masque les points d’entrée ; il ne remplace pas ces contrôles.

La durée d’inactivité, la durée absolue et la mémorisation de l’appareil ont des fonctions distinctes. Mémoriser un appareil ne contourne ni les horaires ni le JIT. La révocation coupe les sessions concernées côté serveur. Utilisez les comptes individuels pour attribuer les actions dans le journal.

La liste IP accepte des adresses IPv4, IPv6 et des réseaux CIDR. Vérifiez la configuration des proxys de confiance Symfony avant son activation. Utilisez HTTPS pour les cookies sécurisés. Guardian ne corrige pas une exposition du serveur web ou de la base de données.
