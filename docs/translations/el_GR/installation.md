# ΩGuardian Profiler Shield — Εγκατάσταση και χρήση

PHP 8.3 ή νεότερη· Symfony 5.4, 6.4 ή 7.4. Το Symfony 8.1 απαιτεί PHP 8.4 ή νεότερη. Απαιτούνται Composer και η επέκταση OpenSSL + Tokenizer. Εγκαταστήστε το WebProfilerBundle για τον profiler.

## 1. Εγκατάσταση

Εκτελέστε από τον ριζικό κατάλογο της εφαρμογής Symfony που περιέχει τα composer.json και bin/console.

```bash
composer require --dev omegaguardian/profiler-shield
```

## 2. Ρύθμιση

```bash
php vendor/bin/guardian setup
```

Ο οδηγός ενεργοποιεί το bundle στο dev/test, προετοιμάζει το θησαυροφυλάκιο και τον διαχειριστή, καθαρίζει την κρυφή μνήμη και ελέγχει τις ρυθμίσεις. Συνεχίστε μόνο μετά την επιτυχία κάθε βήματος. Κρατήστε την εφαρμογή ιδιωτική κατά τη διαδικασία. Η επανάληψη διατηρεί λογαριασμούς και κλειδιά.

## 3. Άνοιγμα πίνακα

```bash
php bin/console guardian:access --env=dev
```

Η εντολή εμφανίζει την ιδιωτική διαδρομή. Αντικαταστήστε το https://VOTRE-HOTE με την πραγματική διεύθυνση HTTPS, μαζί με τη θύρα. Διαβάστε τα διαπιστευτήρια στο .guardian-shield/initial-admin-credentials.txt ή στο υποδεικνυόμενο θησαυροφυλάκιο. Η πρώτη σύνδεση απαιτεί αλλαγή κωδικού. Μην κοινοποιείτε το αρχείο.

## 4. Απεγκατάσταση

Σταματήστε τις διεργασίες worker και εκτελέστε τις εντολές με τη σειρά, ελέγχοντας κάθε αποτέλεσμα. Το θησαυροφυλάκιο και το κλειδί διατηρούνται. Επανεκκινήστε τις διεργασίες και ελέγξτε τον profiler σε ιδιωτικό περιβάλλον.

```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```

Για διαγραφή λογαριασμών και ρυθμίσεων, αντικαταστήστε την πρώτη εντολή με την παρακάτω και επιβεβαιώστε. Η άρνηση δεν αλλάζει τίποτα. Ελέγξτε χειροκίνητα εξωτερικά θησαυροφυλάκια και προσαρμοσμένα αρχεία· κρατήστε τα απαραίτητα κλειδιά.

```bash
php bin/console guardian:uninstall --purge --env=dev
```

## 5. Αναφορά εντολών

Η βοήθεια περιγράφει τις εντολές. Η εντολή πρόσβασης εντοπίζει τον πίνακα· η διάγνωση ελέγχει τις ρυθμίσεις. Απαιτείται έμπιστη τοπική πρόσβαση.

```bash
php vendor/bin/guardian --help
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```
<!-- guardian:command-reference -->

[Αναφορά εντολών](commands.md) · [Περιεχόμενα τεκμηρίωσης](README.md)

```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
```

<!-- /guardian:command-reference -->

## Private Packagist

Για οργανισμούς με Private Packagist, ρυθμίστε αυτό το αποθετήριο πριν την εγκατάσταση και αποθηκεύστε τα διαπιστευτήρια Composer σύμφωνα με τις οδηγίες τους. Μην αποθηκεύετε διακριτικά στο Git.

```bash
composer config repositories.guardian composer https://repo.packagist.com/guardian/
```

[Private Packagist](https://packagist.com/docs/setup)
