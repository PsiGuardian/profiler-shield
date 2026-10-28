# Politique de sécurité — ΩGuardian Profiler Shield

> **Prépublication.** Aucune branche n’est encore garantie pour une utilisation sur un environnement exposé.

## Signalement confidentiel

N’ouvrez **pas** d’issue GitHub publique pour une vulnérabilité exploitable, un secret divulgué ou une preuve de concept sensible. Utilisez la fonction **Report a vulnerability** des *Security Advisories* du dépôt dès son activation par les responsables. Vous pouvez également contacter **bundle-sec@omegaguardian.fr** pour demander un canal chiffré de communication. Ne joignez jamais un secret, un jeton actif ni une URL privée au premier courriel.

## À inclure dans un signalement privé

Version Guardian et Symfony, version PHP, impact, prérequis, étapes de reproduction minimales, correctif envisagé. Ne transmettez aucun secret réel ni journal contenant des cookies actifs.

## Mesures fondamentales

Le Symfony Profiler doit rester désactivé en production. Un bundle ne remplace pas une configuration de serveur web, les restrictions réseau et les tests HTTP externes.

## Processus des mainteneurs

Accuser réception, confirmer la portée, préparer le correctif en privé, exécuter les tests de non-divulgation, publier la correction et un avis de sécurité coordonné. Les délais de réponse et de maintenance seront publiés après désignation de l’équipe.
