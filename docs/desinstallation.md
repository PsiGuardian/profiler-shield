# Désinstallation et données

[Sommaire de la documentation](README.md)

Prépare le retrait des fichiers générés attribuables à Guardian ; conserve par défaut comptes, paramètres, journaux et clé. Arrêter les workers et exécuter avant composer remove, puis vider le cache dev et redémarrer. Les fichiers personnalisés ou sans preuve de provenance restent à examiner.

--purge demande l’effacement des données avec confirmation, refus par défaut. --force nécessite --purge et confirme la destruction ; obligatoire avec --no-interaction pour une purge automatisée. Les coffres externes, liens et fichiers inconnus peuvent empêcher la purge complète : conserver la clé nécessaire et examiner le résultat.
```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```
Sauvegarder le coffre avec sa clé et tester la restauration. Aucun mécanisme ne régénère automatiquement une clé perdue. Après retrait, vérifier le profiler uniquement en environnement privé. En production : APP_DEBUG=0, WebProfilerBundle et collecte désactivés. Une chaîne d’audit locale ne prouve pas l’intégrité d’un hôte compromis.
```bash
php bin/console guardian:uninstall --purge --env=dev
```

## Référence des commandes

--purge demande l’effacement des données avec confirmation, refus par défaut. --force nécessite --purge et confirme la destruction ; obligatoire avec --no-interaction pour une purge automatisée. Les coffres externes, liens et fichiers inconnus peuvent empêcher la purge complète : conserver la clé nécessaire et examiner le résultat.


```bash
php bin/console guardian:uninstall --purge --force --no-interaction --env=dev
```


[Référence des commandes](commands.md)


Si Composer a déjà retiré le paquet, réinstaller temporairement la même version sans lancer setup. Exécuter guardian:uninstall, retirer à nouveau le paquet et vider le cache. Examiner manuellement les configurations personnalisées.
