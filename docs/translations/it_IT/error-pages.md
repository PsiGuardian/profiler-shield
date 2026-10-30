# Pagine di errore pubblico

[Indice della documentazione](README.md)


## Pagine di errore pubblico

Mantieni le pagine dell'applicazione oppure mostra HTML neutro. I codici di stato HTTP reali non cambiano.

La moda nativa permette Symfony render errori. modalità generica ignora testo personalizzato. solo modalità personalizzata utilizza il messaggio del visitatore. la riferimento pubblica può essere assente, casuale o fissa; è indipendente dal messaggio. HTTP I codici di stato rimangono invariati.

Pagine di applicazione: conserva il sito esistente 404 risposte; le eccezioni utilizzano 404 modello quando disponibile, altrimenti una pagina neutrale. neutro: messaggio standard. personalizzato: utilizza il tuo testo.

Le eccezioni 500 anonime ricevono una pagina neutra. I dettagli richiedono identità Guardian autorizzata e GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1. Template Twig assenti o errati usano il rendering neutro; i controller di errore personalizzati richiedono integrazione propria. JSON e pagine riuscite non cambiano.

| error_mode | Impostazioni |
|---|---|
| native | Pagine di applicazione: conserva il sito esistente 404 risposte; le eccezioni utilizzano 404 modello quando disponibile, altrimenti una pagina neutrale. neutro: messaggio standard. personalizzato: utilizza il tuo testo. |
| generic | HTML 400–599 → Mantieni le pagine dell'applicazione oppure mostra HTML neutro. I codici di stato HTTP reali non cambiano. |
| custom | Disponibile solo con un messaggio neutro personalizzato. Il testo viene conservato cambiando modalità. (≤360 Unicode) |
| error_reference | none / random / custom |
| error_reference_text | Disponibile solo con una riferimento statica personalizzata. |
| error_custom_text | Disponibile solo con un messaggio neutro personalizzato. Il testo viene conservato cambiando modalità. |

## Riferimento visualizzato

Messaggi diversi o riferimenti statici possono rivelare l'errore reale. I codici HTTP non devono essere falsificati.

Il riferimento si applica a GuardianPagine neutre: generano casualmente un nuovo codice, non un messaggio casuale.

Le scelte vengono convalidate dal server prima del salvataggio.

```text
templates/bundles/TwigBundle/Exception/error404.html.twig
templates/bundles/TwigBundle/Exception/error.html.twig
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=1

HTTP 400 / 403 / 404 / 429 / 500 / 503
application/json → application/json; application/problem+json → application/problem+json
HTML 2xx → HTML 2xx
HTTP 404 → HTTP 404; HTTP 500 → HTTP 500
```

## Rete e implementazione

L’IP consente di accettare IPv4, IPv6 e CIDR Rete - Verifica Symfony Configurazione proxy affidabile prima di consentire. HTTPS per i cookie sicuri. Guardian non ripara i server web esposti o le basi di dati.

Negare diagnostici se identità, ruolo, sessione, rete, orari, JIT o blocco non sono verificabili. Il browser non decide scadenze. Raccolta e consultazione sono distinte. Ricordare un dispositivo non autentica. L’emergenza richiede identità, codice consumato e audit durevole; un guasto critico impedisce l’apertura. Non applicare configurazioni invalide parzialmente. Nessun servizio remoto imposto dai controlli opzionali.
