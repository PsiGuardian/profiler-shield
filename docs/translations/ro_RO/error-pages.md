# Pagini de eroare publică

[Cuprinsul documentației](README.md)


## Pagini de eroare publică

Păstrați paginile aplicației sau afișați HTML neutru. Codurile de stare HTTP reale rămân neschimbate.

Modele naționale permit Symfony Modul generic ignore textul personalizat. numai modul personalizat utilizează mesajul vizitatorului. referința publică poate fi absentă, aleatorie sau fixă; este independentă de mesaj. HTTP Codurile de stat rămân inalterate.

Pagini de aplicație: păstrează site-ul existent 404 Răspunsuri; excepţiile folosesc 404 șablon atunci când este disponibil, altfel o pagină neutră. neutru: mesaj standard. personalizat: utilizați textul dvs.

Excepțiile 500 anonime primesc pagină neutră. Detaliile necesită identitate Guardian autorizată și GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1. Șabloanele Twig absente sau defecte folosesc afișare neutră; controlerele proprii de eroare necesită integrare. JSON și paginile reușite nu se schimbă.

| error_mode | Setări |
|---|---|
| native | Pagini de aplicație: păstrează site-ul existent 404 Răspunsuri; excepţiile folosesc 404 șablon atunci când este disponibil, altfel o pagină neutră. neutru: mesaj standard. personalizat: utilizați textul dvs. |
| generic | HTML 400–599 → Păstrați paginile aplicației sau afișați HTML neutru. Codurile de stare HTTP reale rămân neschimbate. |
| custom | Disponibil numai cu un mesaj neutru personalizat. Textul se păstrează la schimbarea modului. (≤360 Unicode) |
| error_reference | none / random / custom |
| error_reference_text | Disponibil numai cu o referință statisă personalizată. |
| error_custom_text | Disponibil numai cu un mesaj neutru personalizat. Textul se păstrează la schimbarea modului. |

## Referință afișată

Mesajele diferite sau referințele statice pot divulga eroarea reală. Codurile HTTP nu trebuie falsificate.

Referința se aplică la GuardianPagini neutre: aleatoriu generează un nou cod, nu un mesaj aleatoriu.

Opțiunile sunt validate pe server înainte de salvare.

```text
templates/bundles/TwigBundle/Exception/error404.html.twig
templates/bundles/TwigBundle/Exception/error.html.twig
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1

HTTP 400 / 403 / 404 / 429 / 500 / 503
application/json → application/json; application/problem+json → application/problem+json
HTML 2xx → HTML 2xx
HTTP 404 → HTTP 404; HTTP 500 → HTTP 500
```

## Rețeaua și implementarea

Lista de permisiuni IP acceptă IPv4, IPv6 şi CIDR Rețeaua - Verificați Symfony Configurarea proxy încredere înainte de a permite. HTTPS Pentru cookie-uri sigure. Guardian nu repară serverele web expuse sau bazele de date.

Refuzați diagnosticele dacă identitatea, rolul, sesiunea, rețeaua, programul, JIT sau blocarea nu se pot verifica. Browserul nu decide expirarea. Consultarea și colectarea sunt distincte. Recunoașterea dispozitivului nu autentifică. Urgența necesită identitate, cod consumat și audit durabil; eșecul critic de audit împiedică deschiderea. Nu aplicați parțial configurații invalide. Controalele opționale nu impun servicii externe.
