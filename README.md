# ΩGuardian Profiler Shield

Contrôle d’accès aux outils de diagnostic Symfony : comptes indépendants, authentification multifacteur, horaires, restrictions réseau et accès temporaires.

## Installation

Depuis la racine de l’application Symfony :

```bash
composer require --dev omegaguardian/profiler-shield
php vendor/bin/guardian setup
```

L’assistant prépare la configuration et le compte administrateur, vérifie l’installation et indique le chemin privé du panneau. Les identifiants sont enregistrés dans `.guardian-shield/initial-admin-credentials.txt`, ou dans le coffre indiqué. Le premier accès impose le changement du mot de passe.

PHP 8.3 ou supérieur avec Symfony 5.4, 6.4 ou 7.4 ; PHP 8.4 ou supérieur avec Symfony 8.1. WebProfilerBundle est nécessaire pour utiliser le profiler.

## Guides d’installation · Installation guides

Chaque guide couvre l’installation avec Composer, la configuration, le premier accès, les commandes locales et la désinstallation. Les organisations utilisant [Private Packagist Guardian](https://repo.packagist.com/guardian/) disposent d’une section dédiée.

| Langue · Language | Locale |
|---|---|
| [Français · France](docs/installation/fr_FR.md) | `fr_FR` |
| [English · United Kingdom](docs/installation/en_GB.md) | `en_GB` |
| [Deutsch · Deutschland](docs/installation/de_DE.md) | `de_DE` |
| [Español · España](docs/installation/es_ES.md) | `es_ES` |
| [Italiano · Italia](docs/installation/it_IT.md) | `it_IT` |
| [Português · Portugal](docs/installation/pt_PT.md) | `pt_PT` |
| [Nederlands · Nederland](docs/installation/nl_NL.md) | `nl_NL` |
| [Polski · Polska](docs/installation/pl_PL.md) | `pl_PL` |
| [Română · România](docs/installation/ro_RO.md) | `ro_RO` |
| [Čeština · Česko](docs/installation/cs_CZ.md) | `cs_CZ` |
| [Slovenčina · Slovensko](docs/installation/sk_SK.md) | `sk_SK` |
| [Magyar · Magyarország](docs/installation/hu_HU.md) | `hu_HU` |
| [Ελληνικά · Ελλάδα](docs/installation/el_GR.md) | `el_GR` |
| [Hrvatski · Hrvatska](docs/installation/hr_HR.md) | `hr_HR` |
| [Русский · Россия](docs/installation/ru_RU.md) | `ru_RU` |
| [Türkçe · Türkiye](docs/installation/tr_TR.md) | `tr_TR` |
| [العربية · السعودية](docs/installation/ar_SA.md) | `ar_SA` |
| [हिन्दी · भारत](docs/installation/hi_IN.md) | `hi_IN` |
| [简体中文 · 中国](docs/installation/zh_CN.md) | `zh_CN` |
| [日本語 · 日本](docs/installation/ja_JP.md) | `ja_JP` |
| [Bahasa Indonesia](docs/installation/id_ID.md) | `id_ID` |
| [Tiếng Việt · Việt Nam](docs/installation/vi_VN.md) | `vi_VN` |
| [ქართული · საქართველო](docs/installation/ka_GE.md) | `ka_GE` |
| [English · Ireland](docs/installation/en_IE.md) | `en_IE` |
| [Монгол · Монгол](docs/installation/mn_MN.md) | `mn_MN` |
| [Bahasa Melayu · Malaysia](docs/installation/ms_MY.md) | `ms_MY` |
| [English · United States](docs/installation/en_US.md) | `en_US` |
| [English · Canada](docs/installation/en_CA.md) | `en_CA` |

## Protections

- Comptes administrateur, développeur et sécurité ; sessions révocables.
- Authentification TOTP et remplacement obligatoire des mots de passe initiaux.
- Planning hebdomadaire avec fuseau horaire, accès temporaires et restrictions IPv4/IPv6.
- Limitation progressive des tentatives : 10 échecs puis 10 minutes de blocage par défaut ; paliers configurables.
- Politique des mots de passe et simulateur de recherche exhaustive avec hypothèses réglables.
- Pages d’erreur publiques personnalisables et journal filtrable par compte ou type d’événement.

[Réglages](docs/reglages.md) · [Authentification](docs/authentication.md) · [Architecture](docs/architecture.md) · [Signaler une vulnérabilité](SECURITY.md)

## Désinstallation

Arrêter les workers, puis exécuter chaque commande après la réussite de la précédente :

```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```

Le coffre et sa clé sont conservés. Pour effacer également les comptes et les paramètres, remplacer la première commande par `php bin/console guardian:uninstall --purge --env=dev`, puis confirmer. Redémarrer les workers après le vidage du cache.

[Guide détaillé](INSTALLATION.md) · [Gestion des données lors du retrait](docs/desinstallation.md)
