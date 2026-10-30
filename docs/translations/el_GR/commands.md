# Αναφορά εντολών

[Περιεχόμενα τεκμηρίωσης](README.md)

Εκτελέστε από τη ρίζα Symfony σε έμπιστο τερματικό. Τα παραδείγματα χρησιμοποιούν dev. Μην τοποθετείτε κωδικούς, κλειδιά ή κωδικούς ανάκτησης σε ορίσματα, αρχεία καταγραφής ή Git.

## Εύρεση εντολών και βοήθεια


```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
php bin/console guardian:doctor --help --env=dev
```


--env=dev και --no-interaction ανήκουν στο Symfony. Το --no-interaction δεν παρέχει μυστικά και δεν ενδείκνυται για διαδραστικούς κωδικούς. --help δείχνει σύνταξη, -v λεπτομέρειες. Κωδικός 0 επιβεβαιώνει το δηλωμένο αποτέλεσμα· --no-admin μπορεί να πετύχει χωρίς λειτουργική πύλη.

## `vendor/bin/guardian`

Ενεργοποιεί το bundle σε dev/test, καθαρίζει την cache, εκτελεί guardian:setup --automatic, καθαρίζει ξανά και εκτελεί guardian:doctor και guardian:access. Σταματά στο πρώτο σφάλμα. Η επανάληψη διατηρεί λογαριασμούς και κλειδιά. Απορρίπτει APP_ENV=prod και απαιτεί proc_open.

Χωρίς όρισμα, --help, -h ή help εμφανίζει μόνο βοήθεια. Το setup είναι η μόνη ενέργεια και δεν δέχεται πρόσθετες επιλογές.


```bash
php vendor/bin/guardian --help
php vendor/bin/guardian setup
```


| CLI | Αναφορά εντολών |
|---|---|
| [`guardian:setup`](#guardiansetup) | Προετοιμάζει ιδιωτική διαδρομή, κλειδί, θησαυροφυλάκιο, αρχεία Symfony και διαχειριστή αν λείπει. |
| [`guardian:install`](#guardianinstall) | Αρχικοποιεί το θησαυροφυλάκιο και δημιουργεί διαχειριστή με --username, προεπιλογή admin. |
| [`guardian:access`](#guardianaccess) | Εμφανίζει τη διαδρομή του πίνακα και τη θέση αρχικών διαπιστευτηρίων, ποτέ τον κωδικό. |
| [`guardian:doctor`](#guardiandoctor) | Ελέγχει θησαυροφυλάκιο, δικαιώματα, κλειδί, διαδρομή, ενεργό διαχειριστή και ρυθμίσεις. |
| [`guardian:password:reset`](#guardianpasswordreset) | Αλλάζει τον κωδικό του υπάρχοντος username με κρυφή εισαγωγή σύμφωνα με την πολιτική, ανακαλεί όλες τις συνεδρίες και καταγράφει την ενέργεια. |
| [`guardian:mfa:reset`](#guardianmfareset) | Αφαιρεί το μυστικό και τον μετρητή TOTP του username, ανακαλεί συνεδρίες και καταγράφει. |
| [`guardian:login:unlock`](#guardianloginunlock) | Μηδενίζει μετρητές username. |
| [`guardian:policy:reset`](#guardianpolicyreset) | Αδειάζει τη λίστα IP και απενεργοποιεί ωράριο, JIT και απομακρυσμένη έκτακτη πρόσβαση. |
| [`guardian:lockdown`](#guardianlockdown) | on κλείνει τα διαγνωστικά και αφαιρεί όλες τις συνεδρίες· οι επιχειρησιακές σελίδες λειτουργούν. |
| [`guardian:emergency`](#guardianemergency) | Δημιουργεί 5 κωδικούς μίας χρήσης για υπάρχον username, αντικαθιστώντας τους παλιούς. |
| [`guardian:alerts:flush`](#guardianalertsflush) | Στέλνει ειδοποιήσεις σε αναμονή στο alert_email μέσω PHP mail(). |
| [`guardian:password:benchmark`](#guardianpasswordbenchmark) | Μετρά επαλήθευση hash με συνθετικό μυστικό, όχι κωδικό χρήστη. |
| [`guardian:uninstall`](#guardianuninstall) | Προετοιμάζει αφαίρεση αρχείων που αποδεδειγμένα δημιούργησε το Guardian. |

## `guardian:setup`

Προετοιμάζει ιδιωτική διαδρομή, κλειδί, θησαυροφυλάκιο, αρχεία Symfony και διαχειριστή αν λείπει. Διατηρεί λογαριασμούς. Απορρίπτει παραγωγή. Μετά την άμεση εκτέλεση καθαρίστε την cache και κάντε τους ελέγχους.


```bash
php bin/console guardian:setup --automatic --env=dev
```

--automatic δημιουργεί admin και μυστικό σε ιδιωτικό αρχείο 0600. --path ορίζει διαδρομή 25–181 χαρακτήρων χωρίς αντικατάσταση υπάρχουσας. --no-admin παραλείπει τη δημιουργία διαχειριστή· χωρίς ενεργό λογαριασμό η πύλη δεν λειτουργεί. --username προτείνει διαδραστικό όνομα, προεπιλογή admin· δεν αλλάζει τον αυτόματο λογαριασμό.

```text
--path: ^/[a-zA-Z0-9/_-]{24,180}$
/_profiler; /_wdt → ∅
```

```bash
php bin/console guardian:setup --username=operator --env=dev
php bin/console cache:clear --env=dev
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```

## `guardian:install`

Αρχικοποιεί το θησαυροφυλάκιο και δημιουργεί διαχειριστή με --username, προεπιλογή admin. Ζητά κρυφή εισαγωγή κωδικού σύμφωνα με την πολιτική. Απορρίπτει υπάρχοντα λογαριασμό. Δεν δημιουργεί διαδρομές ή ρυθμίσεις Symfony· συνήθως χρησιμοποιήστε τον πλήρη οδηγό.


```bash
php bin/console guardian:install --username=admin --env=dev
```


Όνομα χρήστη : 3 Για 64 ΑΛΦΑΝΟΥΡΙΚΟΙ ΑΝΘΡΩΠΟΙ...

## `guardian:access`

Εμφανίζει τη διαδρομή του πίνακα και τη θέση αρχικών διαπιστευτηρίων, ποτέ τον κωδικό. --base-url δέχεται την πραγματική προέλευση HTTPS με προαιρετική θύρα, χωρίς διαπιστευτήρια, ερώτημα ή τμήμα. Αντικαταστήστε τον ενδεικτικό διακομιστή.


```bash
php bin/console guardian:access --base-url=https://example.com --env=dev
```


## `guardian:doctor`

Ελέγχει θησαυροφυλάκιο, δικαιώματα, κλειδί, διαδρομή, ενεργό διαχειριστή και ρυθμίσεις. Κωδικός εξόδου 1 για πρόβλημα. PHP CLI και PHP-FPM μπορεί να έχουν διαφορετικές ζώνες ώρας. Δεν ελέγχει έκθεση στο Internet ή κανόνες proxy.


```bash
php bin/console guardian:doctor --env=dev
```


## `guardian:password:reset`

Αλλάζει τον κωδικό του υπάρχοντος username με κρυφή εισαγωγή σύμφωνα με την πολιτική, ανακαλεί όλες τις συνεδρίες και καταγράφει την ενέργεια. Μην δίνετε τον κωδικό ως όρισμα.


```bash
php bin/console guardian:password:reset admin --env=dev
```


## `guardian:mfa:reset`

Αφαιρεί το μυστικό και τον μετρητή TOTP του username, ανακαλεί συνεδρίες και καταγράφει. Εγγράψτε νέο authenticator· η καθολική απαίτηση MFA παραμένει και μπορεί να αποκλείει τον profiler.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```


## `guardian:login:unlock`

Μηδενίζει μετρητές username. --ip ΔΙΕΥΘΥΝΣΗ καθαρίζει και τον μετρητή της προέλευσης IPv4/IPv6· διαφορετικά μπορεί να παραμένει αποκλεισμός δικτύου. --all διαγράφει όλους. Λογαριασμοί, κωδικοί και συνεδρίες δεν αλλάζουν· η ενέργεια καταγράφεται.


```bash
php bin/console guardian:login:unlock admin --ip=192.0.2.10 --env=dev
```


```bash
php bin/console guardian:login:unlock --all --env=dev
```

## `guardian:policy:reset`

Αδειάζει τη λίστα IP και απενεργοποιεί ωράριο, JIT και απομακρυσμένη έκτακτη πρόσβαση. Έλεγχος ταυτότητας, MFA, καθολικό κλείδωμα και αποκλεισμοί σύνδεσης παραμένουν. Ρυθμίστε ξανά τους επιθυμητούς περιορισμούς.


```bash
php bin/console guardian:policy:reset --env=dev
```


## `guardian:lockdown`

on κλείνει τα διαγνωστικά και αφαιρεί όλες τις συνεδρίες· οι επιχειρησιακές σελίδες λειτουργούν. off αίρει μόνο το καθολικό κλείδωμα: απαιτείται νέα σύνδεση και ισχύουν οι άλλες πολιτικές.


```bash
php bin/console guardian:lockdown on --env=dev
```


```bash
php bin/console guardian:lockdown off --env=dev
```

## `guardian:emergency`

Δημιουργεί 5 κωδικούς μίας χρήσης για υπάρχον username, αντικαθιστώντας τους παλιούς. Εμφανίζονται μία φορά· φυλάξτε τους εκτός διακομιστή σε διαχειριστή μυστικών. Απομακρυσμένη χρήση απαιτεί ενεργή έκτακτη πρόσβαση, συνεδρία, κωδικό και αιτιολογία. Δεν παρακάμπτει το καθολικό κλείδωμα.


```bash
php bin/console guardian:emergency admin --env=dev
```


## `guardian:alerts:flush`

Στέλνει ειδοποιήσεις σε αναμονή στο alert_email μέσω PHP mail(). Ρυθμίστε μεταφορά και προγραμματισμένη εργασία, ελέγξτε πραγματική παράδοση. Αποτυχίες παραμένουν στην ουρά. Κωδικός 1 για άκυρο παραλήπτη ή αποτυχία αποστολής.


```bash
php bin/console guardian:alerts:flush --env=dev
```


## `guardian:password:benchmark`

Μετρά επαλήθευση hash με συνθετικό μυστικό, όχι κωδικό χρήστη. --iterations: 1–10, προεπιλογή 3. JSON με αλγόριθμο, χρόνο και επαληθεύσεις ανά δευτερόλεπτο. Μετρά μία τοπική διεργασία CPU, όχι GPU ή επιτιθέμενο.


```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```


## `guardian:uninstall`

Προετοιμάζει αφαίρεση αρχείων που αποδεδειγμένα δημιούργησε το Guardian. Διατηρεί λογαριασμούς, ρυθμίσεις, καταγραφές και κλειδί. Σταματήστε workers, εκτελέστε πριν το composer remove, καθαρίστε cache dev και επανεκκινήστε. Ελέγξτε προσαρμοσμένα αρχεία ή αβέβαιη προέλευση.


```bash
php bin/console guardian:uninstall --env=dev
```

--purge ζητά διαγραφή δεδομένων με προεπιλεγμένη άρνηση. --force απαιτεί --purge και επιβεβαιώνει καταστροφή· υποχρεωτικό με --no-interaction. Εξωτερικά θησαυροφυλάκια, σύνδεσμοι και άγνωστα αρχεία μπορεί να εμποδίσουν πλήρη διαγραφή. Κρατήστε αναγκαία κλειδιά και ελέγξτε το αποτέλεσμα.

```bash
php bin/console guardian:uninstall --purge --env=dev
# --purge --force --no-interaction
```

## Απεγκατάσταση και δεδομένα


```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```


Αντιγράψτε θησαυροφυλάκιο και κλειδί μαζί και δοκιμάστε επαναφορά. Χαμένα κλειδιά δεν αναδημιουργούνται αυτόματα. Μετά την αφαίρεση ελέγξτε τον profiler μόνο ιδιωτικά. Παραγωγή: APP_DEBUG=0, WebProfilerBundle και συλλογή ανενεργά. Τοπική αλυσίδα δεν αποδεικνύει ακεραιότητα παραβιασμένου host.

[Απομάκρυνση και δεδομένα](desinstallation.md)
