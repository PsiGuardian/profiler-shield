# Connexions et mots de passe

[Sommaire de la documentation](README.md)


## Comptes et première connexion

Bienvenue dans Guardian. Votre mot de passe initial est temporaire. Choisissez un nouveau secret : le panneau et les diagnostics restent fermés jusqu’à ce changement. Le fichier initial sera supprimé après validation.

Les comptes Guardian sont indépendants des comptes du site. Le compte initial et les nouveaux comptes doivent changer leur mot de passe avant de continuer. Le rôle sécurité consulte les informations autorisées sans accès au profiler. Un changement de rôle ou de mot de passe invalide les anciennes sessions.

Prépare la route privée, la clé, le coffre, les fichiers Symfony et un administrateur s’il en manque. Les comptes existants sont conservés. Refuse la configuration en production. Après une exécution directe, vider le cache et lancer les vérifications.


```bash
php bin/console guardian:access --env=dev
```


| Exigences du mot de passe enregistré | Longueur minimale (caractères Unicode) |
|---|---|
| initial-admin-credentials.txt | 0600; random_int; &gt;=32 |
| password_policy[min_length] | 8–64; 15 |
| input | Unicode; ≤512 UTF-8 B |
| Argon2id | PHP + Argon2id → Argon2id |
| bcrypt | PHP − Argon2id → bcrypt; ≤72 UTF-8 B |

## Politique de mots de passe

Ces règles s’appliquent aux créations et changements de mot de passe, sur le web comme en CLI. Elles ne révèlent ni ne vérifient les anciens mots de passe.

Valeur initiale : 15 caractères. Utilisez une phrase de passe unique ou un gestionnaire. Les catégories obligatoires ne garantissent pas un mot de passe imprévisible.

Sur ce serveur, Guardian utilise automatiquement Argon2id pour les nouveaux mots de passe. Conservez sa prise en charge dans PHP. La limite applicative est de 512 octets UTF-8.

Ce serveur PHP ne propose pas Argon2id. Guardian utilise donc bcrypt et refuse les mots de passe dépassant 72 octets UTF-8. Demandez à votre administrateur système d’activer la prise en charge d’Argon2id dans PHP.

Cette option révoque immédiatement les sessions existantes, y compris la vôtre. Les comptes devront changer leur mot de passe avant de configurer Guardian ou d’ouvrir les diagnostics.

Change le mot de passe du compte username, demandé en saisie masquée selon la politique active, puis révoque toutes ses sessions et journalise l’action. Le compte doit exister. Aucun mot de passe ne se passe en argument.

## Blocages progressifs

Chaque palier définit un nombre d’échecs, une période de comptage, une durée de blocage et une période de récidive. Par défaut : 10 échecs entraînent 10 minutes de blocage. Une récidive peut déclencher le palier suivant. Les contrôles portent sur le compte et l’origine réseau, y compris pour les confirmations administrateur.

Les compteurs chiffrés sont indépendants par compte normalisé et origine ; les IP brutes ne sont pas conservées. Une réservation atomique précède le hachage et expire après 120 secondes. Une réservation abandonnée devient un échec, un résultat tardif ne crée aucune session. Un CSRF invalide ne réserve rien. Une connexion réussie efface les échecs du compte mais conserve l’historique de récidive et de l’origine. À 10000 entrées actives, les nouvelles identités sont refusées plutôt que d’évincer les blocages.

Les paliers se lisent dans l’ordre. Après un blocage, de nouveaux échecs dans la fenêtre de récidive déclenchent le palier suivant. Hors de cette fenêtre, retour au premier palier. Le dernier palier se répète. Les requêtes bloquées ne prolongent pas le délai. Maximum 8 paliers. Récupération locale : guardian:login:unlock --all.

| Palier | Exigences du mot de passe enregistré |
|---|---|
| Échecs avant blocage | 1–100 |
| Blocage (minutes) | 1–10080 |
| Fenêtre de comptage (minutes) | 1–1440 |
| Récidive après déblocage (minutes) | 1–10080 |
| Palier | 1–8; block[n+1] ≥ block[n] |
| Valeur initiale | 10 / 10 / 30 / 30 |
| 5 → 10 min; 5 → 2 h | 5 / 10 / 30 / 30 → 5 / 120 / 30 / 30 |
| HTTP | 429; Retry-After |

## Laboratoire de coût d’une recherche exhaustive

Comparez des secrets aléatoires uniformes de longueur fixe, sans saisir de vrai mot de passe. Les débits et le prix du matériel sont des hypothèses modifiables. Aucun essai de connexion n’est envoyé.

Le calcul utilise des catégories ASCII disjointes et BigInt. L’inclusion-exclusion donne le nombre exact de secrets conformes ; une catégorie absente ou une longueur insuffisante donne zéro. Le rang moyen est (N+1)/2 pour un ordre sans remise. L’espérance des blocages tient compte des seuils et du dernier palier répété. Si le débit ne respecte pas les fenêtres, l’estimation est désactivée. Le prix hors ligne est le temps en heures multiplié par le coût horaire supposé.

À alphabet fixe, imposer une catégorie réduit le nombre de secrets conformes. Agrandir l’alphabet est une autre opération. Les temps avec blocages supposent des essais continus assez rapides pour respecter les fenêtres configurées.

Ce modèle n’est pas une mesure de la solidité d’un mot de passe humain. Il ignore dictionnaires, réutilisations, fuites et stratégies ciblées. Le blocage web ne protège pas des hachages volés. Le débit hors ligne initial est illustratif, pas un benchmark. Mesure CPU locale : guardian:password:benchmark.

Les grands nombres sont écrits en entier, avec des séparateurs de groupes et sans notation E. Les durées et coûts sont arrondis. Pour comparer, utilisez la même ligne et les mêmes hypothèses.

| Alphabet disponible pour la simulation | Secrets conformes possibles |
|---|---|
| Minuscules ASCII | 26 |
| Majuscules ASCII | 26 |
| Chiffres ASCII | 10 |
| Symboles ASCII hors espace | 32 |
| Débit en ligne supposé (essais/s) | 1 / s |
| Débit hors ligne supposé (hachages/s) | 10000 / s |
| Coût matériel supposé (€/heure) | 2 € / h |

```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```

## Récupération et vérifications

Sauvegarder le coffre avec sa clé et tester la restauration. Aucun mécanisme ne régénère automatiquement une clé perdue. Après retrait, vérifier le profiler uniquement en environnement privé. En production : APP_DEBUG=0, WebProfilerBundle et collecte désactivés. Une chaîne d’audit locale ne prouve pas l’intégrité d’un hôte compromis.

Réinitialise les compteurs du compte username. --ip ADRESSE efface aussi le compteur de cette origine IPv4/IPv6 ; sans cela, un blocage réseau peut subsister. --all efface tous les compteurs. Ne change ni comptes, ni mots de passe, ni sessions ; action journalisée.

[Référence des commandes](commands.md)
