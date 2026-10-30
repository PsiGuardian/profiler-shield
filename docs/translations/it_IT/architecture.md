# Architettura e confini di fiducia

[Indice della documentazione](README.md)


## Architettura e confini di fiducia

Il proxy gestisce TLS, normalizzazione e protezione volumetrica. Il gateway controlla i diagnostici prima del router; le pagine aziendali non leggono account o sessioni. Le identità Guardian sono separate da Symfony. Salvare archivio e chiave insieme fuori dalla directory pubblica. PHP o host compromessi possono esporre entrambi. L’archiviazione è per un solo host. Errori di avvio, cache o proxy possono incidere su tutta l’applicazione.

L’IP consente di accettare IPv4, IPv6 e CIDR Rete - Verifica Symfony Configurazione proxy affidabile prima di consentire. HTTPS per i cookie sicuri. Guardian non ripara i server web esposti o le basi di dati.

Guardian controlla il profilo e la barra degli strumenti di debug. È richiesto un account amministratore o sviluppatore autorizzato. MFAIl calendario, JITLe restrizioni di rete e il blocco possono ancora rifiutare l'accesso. modalità Stealth nasconde i punti d'ingresso; non sostituisce questi controlli.

| PHP / HTTP | Architettura e confini di fiducia |
|---|---|
| GuardianSubscriber / kernel.request | 4096 |
| CollectionSubscriber / kernel.request | 3072 |
| GuardianSubscriber::sanitize / kernel.response | -4096 |
| GUARDIAN_COLLECTION_BRIDGE | 0 → false; 1 → session ∧ policy |
| framework.profiler.collect | false |
| Sessione | 256 bit → SHA-256 |
| Cookie | __Host-; Secure; HttpOnly; SameSite=Lax; Path=/; Domain=∅ |
| JSON / schema | schema=2; AES-256-GCM; nonce=12 B; tag=16 B; HMAC; AAD=collection |
| GUARDIAN_STORAGE_DIR | &lt;Symfony&gt;/.guardian-shield/ |
| GUARDIAN_ENCRYPTION_KEY | 64 hex; .env.local ≠ .guardian-shield/ |
| HTTP | no-store; CSP; no-referrer; frame-ancestors none |
| HTTP headers | X-Debug-Exception=∅; X-Debug-Exception-File=∅; X-Powered-By=∅ |
| CDN | ∅ |
| Symfony routes | /_profiler; /_wdt; index.php |
| Symfony route | /docs/_profiler/guide ≠ /_profiler |

## Invarianti di sicurezza

Negare diagnostici se identità, ruolo, sessione, rete, orari, JIT o blocco non sono verificabili. Il browser non decide scadenze. Raccolta e consultazione sono distinte. Ricordare un dispositivo non autentica. L’emergenza richiede identità, codice consumato e audit durevole; un guasto critico impedisce l’apertura. Non applicare configurazioni invalide parzialmente. Nessun servizio remoto imposto dai controlli opzionali.

Salvare archivio e chiave insieme e provare il ripristino. Le chiavi perse non vengono rigenerate automaticamente. Dopo la rimozione verificare il profiler solo in privato. Produzione: APP_DEBUG=0, WebProfilerBundle e raccolta disattivati. La catena locale non prova integrità su host compromessi.

## Giornale e avvertenze

Il livello e la ritenzione determinano gli eventi disponibili, fino a 5000 Entrate. filtro per tipo e conto prima di cambiare pagine. gli avvisi vengono tagliati; configurare il loro trasporto e consegna. una catena hash locale non garantisce l'integrità su un host compromesso.
