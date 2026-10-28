# Authentification et laboratoire de coût

## Premier accès

La commande explicite `guardian:setup --automatic` crée un compte `admin` avec un secret généré par `random_int`, adapté à la politique active, de 32 caractères au minimum. Le secret ne figure pas dans les logs Composer. Il est écrit uniquement dans le fichier initial privé 0600. La route et la procédure sont annoncées à la fin de l’installation et restent consultables avec `guardian:access`.

Le premier formulaire exige le secret actuel, un nouveau mot de passe et sa confirmation, avec CSRF. Réutiliser le secret actuel est refusé. Le changement incrémente la version du mot de passe : les anciennes sessions sont invalides même si leur suppression échoue ensuite. Un nouveau jeton est émis pour la session courante. Le fichier initial est supprimé ; son ancien secret ne permet plus de se connecter.


## Compteurs et paliers

Les compteurs sont chiffrés et persistants, avec verrouillage et écriture atomique. Ils portent sur le compte normalisé indépendamment des IP, et sur l’origine indépendamment des noms de compte. Les IP brutes ne sont pas stockées. Les tentatives avec mauvais second facteur comptent aussi ; les confirmations de mot de passe des actions sensibles utilisent la même limitation.

Avant de calculer un hachage, une réservation atomique empêche les requêtes concurrentes de dépasser le quota. Elle expire après 120 secondes ; un résultat arrivé trop tard ne peut pas créer une session. Une réservation abandonnée compte comme un échec à sa récupération. Les requêtes reçues pendant un blocage sont refusées avec HTTP 429 et Retry-After, sans prolonger la sanction. Un CSRF invalide ne réserve aucun calcul de mot de passe.

Chaque palier comporte quatre champs :

| Champ | Effet | Limites |
|---|---|---|
| Échecs | Nombre de vérifications échouées avant le blocage | 1–100 |
| Blocage | Durée du refus | 1–10 080 minutes |
| Fenêtre de comptage | Intervalle à partir du premier échec ; un compteur expiré repart à zéro | 1–1 440 minutes |
| Récidive | Intervalle après la fin du blocage pendant lequel les nouveaux échecs utilisent le palier suivant | 1–10 080 minutes |

Il faut de 1 à 8 paliers, avec des blocages de durées non décroissantes. Exemple : premier palier 5/10/30/30, deuxième palier 5/120/30/30. Le dernier palier se répète. Hors de la fenêtre de récidive, retour au premier. Une connexion réussie remet à zéro les échecs du compte, mais ne supprime pas l’historique de récidive ni celui de l’origine : un compte valide ne peut pas effacer les traces d’un spray de mots de passe contre d’autres comptes.

Au-delà de 10 000 entrées actives, les nouvelles identités sont temporairement refusées ; les blocages actifs ne sont pas évincés pour faire de la place. Le stockage reste destiné à un hôte unique. Des contrôles réseau complémentaires restent utiles contre une saturation de ressources. Un verrouillage peut aussi être provoqué par un tiers ; prévoir la récupération CLI `guardian:login:unlock --all` depuis un terminal de confiance.

## Politique des secrets

Minimum configurable de 8 à 64 caractères Unicode, 15 par défaut. Majuscules, minuscules, chiffres et symboles peuvent être exigés séparément. Les espaces restent autorisés ; les caractères de contrôle sont refusés. Le plafond d’entrée est de 512 octets. Guardian choisit automatiquement Argon2id pour les nouveaux hachages lorsque le serveur PHP le prend en charge. Si ce support manque, le repli bcrypt refuse tout secret de plus de 72 octets UTF-8, sans troncature silencieuse. Ce n’est pas une recommandation de désactiver Argon2id. Le petit filtre local de secrets évidents n’est pas une liste exhaustive de mots de passe compromis.

La politique s’applique aux nouveaux comptes et changements, y compris `guardian:password:reset`. Elle ne peut pas analyser les anciens secrets hachés. L’administrateur peut demander un renouvellement de tous les comptes actifs ; leurs sessions sont immédiatement révoquées, y compris la sienne. Toute modification exige CSRF, rôle administrateur et confirmation du secret actuel.

## Modèle du simulateur

L’alphabet simulé est ASCII : 26 minuscules, 26 majuscules, 10 chiffres et 32 symboles hors espace. La politique réelle accepte aussi Unicode et les espaces ; le modèle ne couvre donc pas tous les secrets possibles du serveur.

Pour une longueur fixe L et un alphabet de taille A, le nombre de secrets conformes aux catégories obligatoires est calculé exactement par inclusion-exclusion, avec BigInt. Les catégories sont disjointes. Une catégorie obligatoire absente de l’alphabet, ou une longueur insuffisante, donne zéro combinaison. À alphabet fixe, ajouter une contrainte de composition réduit cet ensemble ; agrandir l’alphabet est une autre opération.

Le modèle suppose un secret uniforme dans cet ensemble et un ordre de recherche sans remise. Le rang moyen est (N+1)/2. Pour les blocages, il calcule l’espérance du délai sur tous les rangs, avec les probabilités de franchir chaque seuil et une somme arithmétique pour le dernier palier répété. Ce calcul est comparé par test à l’énumération explicite des rangs de petits ensembles. Si le débit supposé ne respecte pas les fenêtres de comptage ou de récidive, cette estimation est désactivée plutôt que de présenter un temps trompeur.

Le mode hors ligne divise le rang moyen par le débit de hachages saisi, puis multiplie les heures par le prix matériel supposé. Le débit initial de 10 000 hachages/s et le prix de 2 €/h sont illustratifs : ils ne décrivent aucun GPU mesuré. `guardian:password:benchmark` mesure seulement un processus CPU local sur un secret synthétique avec le hachage réellement disponible. Il ne prédit pas la puissance d’un attaquant.

Le laboratoire n’accepte pas de vrai mot de passe et n’envoie aucun essai. Il ne modélise ni dictionnaires, habitudes humaines, réutilisations, fuites, phishing, ni attaques ciblées. Il n’attribue pas de garantie de robustesse aux secrets choisis par une personne. Les blocages en ligne n’agissent plus sur des hachages volés.

Références : https://cheatsheetseries.owasp.org/cheatsheets/Authentication_Cheat_Sheet.html et https://symfony.com/doc/current/setup.html.

Les nombres du simulateur utilisent une écriture complète avec groupement localisé des chiffres, sans exposant E. Les durées et coûts sont arrondis à quatre chiffres significatifs.
