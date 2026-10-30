# Autentificare în doi pași

[Cuprinsul documentației](README.md)


## Autentificare în doi pași

Fiecare persoană își înregistrează propriul TOTP secret și confirmă un cod.

Fiecare persoană își înregistrează propriul TOTP secret și confirmă un cod. cerere MFA blocuri de profil acces pentru conturile neînregistrate. Alegeți configurarea manuală și introduceți cheia afișată. Folosiți SHA-1, șase cifre și un interval de 30 de secunde.

## 1. Confirmați identitatea

Introduceți parola folosită la autentificarea Guardian. Este cerută o singură dată.

## 2. Introduceți cheia în aplicația de autentificare

Alegeți configurarea manuală și introduceți cheia afișată. Folosiți SHA-1, șase cifre și un interval de 30 de secunde.

## 3. Confirmați un cod de șase cifre

Introduceți codul din aplicație. Nu este cerută o a doua parolă.

## Confidențialitate: nu introduceți URL-ul site-ului

Dacă aplicația cere numele sau site-ul, introduceți doar „ΩGuardian” sau un alias neutru. Nu introduceți URL-ul instanței Symfony.

Păstrați-o secretă: permite generarea codurilor TOTP. Nu o distribuiți și nu faceți capturi de ecran.

Secretul provizoriu expiră după 10 minute sau 5 coduri greșite; reluați atunci. O eroare păstrează formularul fără a cere iar parola. Contorul de confirmare se consumă contra reluării.

## Recuperarea și verificarea

Șterge secretul și contorul TOTP pentru username, revocă sesiunile și înregistrează acțiunea. Înrolați alt autentificator; cerința MFA globală rămâne și poate bloca profilerul.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```
