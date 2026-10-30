# Architektura i granice zaufania

[Spis dokumentacji](README.md)


## Architektura i granice zaufania

Proxy obsługuje TLS, normalizację i ochronę przed dużym ruchem. Brama sprawdza diagnostykę przed routerem; trasy biznesowe nie czytają kont ani sesji. Tożsamości Guardian są oddzielne od Symfony. Kopiuj prywatny magazyn i klucz razem poza katalogiem publicznym. Przejęcie PHP lub hosta może ujawnić oba. Magazyn jest dla jednego hosta. Błędy startu, cache lub proxy mogą dotyczyć całej aplikacji.

IP dopuszczalnik akceptuje IPv4, IPv6 i CIDR Źródła - sprawdź Symfony zaufana konfiguracja proxy przed umożliwieniem. użyj HTTPS Dla bezpiecznych plików cookie. Guardian nie naprawia wystawionych serwerów internetowych lub baz danych.

Guardian kontroluje profil i pasek narzędzi debug. Konto autoryzowanego administratora lub deweloperów jest wymagane. MFAi harmonogramów, JIT, ograniczenia sieciowe i zamknięcie mogą nadal odmówić dostępu. tryb ukrywa punkty wejściowe; nie zastępuje tych kontrol.

| PHP / HTTP | Architektura i granice zaufania |
|---|---|
| GuardianSubscriber / kernel.request | 4096 |
| CollectionSubscriber / kernel.request | 3072 |
| GuardianSubscriber::sanitize / kernel.response | -4096 |
| GUARDIAN_COLLECTION_BRIDGE | 0 → false; 1 → session ∧ policy |
| framework.profiler.collect | false |
| sesja | 256 bit → SHA-256 |
| Cookie | __Host-; Secure; HttpOnly; SameSite=Lax; Path=/; Domain=∅ |
| JSON / schema | schema=2; AES-256-GCM; nonce=12 B; tag=16 B; HMAC; AAD=collection |
| GUARDIAN_STORAGE_DIR | &lt;Symfony&gt;/.guardian-shield/ |
| GUARDIAN_ENCRYPTION_KEY | 64 hex; .env.local ≠ .guardian-shield/ |
| HTTP | no-store; CSP; no-referrer; frame-ancestors none |
| HTTP headers | X-Debug-Exception=∅; X-Debug-Exception-File=∅; X-Powered-By=∅ |
| CDN | ∅ |
| Symfony routes | /_profiler; /_wdt; index.php |
| Symfony route | /docs/_profiler/guide ≠ /_profiler |

## Niezmienne zasady bezpieczeństwa

Odmów diagnostyki, gdy nie da się sprawdzić tożsamości, roli, sesji, sieci, harmonogramu, JIT lub blokady. Przeglądarka nie ustala wygaśnięcia. Zbieranie i odczyt profili są oddzielne. Zapamiętanie urządzenia nie uwierzytelnia. Awaria wymaga tożsamości, zużytego kodu i trwałego audytu; krytyczny błąd audytu blokuje otwarcie. Nie stosuj częściowo błędnej konfiguracji. Opcjonalne kontrole nie wymagają zdalnej usługi.

Kopiuj magazyn i klucz razem i testuj odtworzenie. Utracone klucze nie są automatycznie regenerowane. Po usunięciu sprawdzaj profiler tylko prywatnie. Produkcja: APP_DEBUG=0, WebProfilerBundle i zbieranie wyłączone. Lokalny łańcuch audytu nie dowodzi integralności przejętego hosta.

## Dziennik i ostrzeżenia

poziom i utrzymanie określa dostępne wydarzenia, aż do 5000 wpisy. filtracja według typu i konta przed zmianą stron. ostrzeżenia są przekazywane; skonfigurować ich transport i dostawę. lokalny łańcuch hash nie gwarantuje integralności zagrożonego hostingu.
