# Autentificare în doi pași

[Cuprinsul documentației](README.md)


## Autentificare în doi pași

Protejați-vă contul Guardian cu coduri temporare, fără servicii QR externe.

Fiecare persoană își înregistrează propriul TOTP secret și confirmă un cod. cerere MFA blocuri de profil acces pentru conturile neînregistrate. QR Planul de recuperare administrativă înainte de a solicita această protecție pentru întreaga echipă.

## 1. Confirmați identitatea

Introduceți parola folosită la autentificarea Guardian. Este cerută o singură dată.

## 2. Scanați codul QR în aplicația de autentificare

Guardian generează QR local, fără solicitări către servicii externe.

## 3. Confirmați un cod de șase cifre

Introduceți codul din aplicație. Nu este cerută o a doua parolă.

## Confidențialitate: nu introduceți URL-ul site-ului

Dacă aplicația cere numele sau site-ul, introduceți doar „ΩGuardian” sau un alias neutru. Nu introduceți URL-ul instanței Symfony. Nu folosiți generatoare QR online: ar primi secretul dvs.

Păstrați-o secretă: permite generarea codurilor TOTP. Nu o distribuiți și nu faceți capturi de ecran.

Secretul provizoriu expiră după 10 minute sau 5 coduri greșite; reluați atunci. O eroare păstrează formularul fără a cere iar parola. Contorul de confirmare se consumă contra reluării. QR SVG folosește bacon/bacon-qr-code și ext-iconv fără GD, XMLWriter sau rețea. Introducerea manuală rămâne. URI otpauth folosește furnizor neutru și cont, fără domeniul site-ului. Nu distribuiți secretul sau URL-ul privat.

## Recuperarea și verificarea

Șterge secretul și contorul TOTP pentru username, revocă sesiunile și înregistrează acțiunea. Înrolați alt autentificator; cerința MFA globală rămâne și poate bloca profilerul.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```
