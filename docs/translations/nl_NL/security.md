# Beveiligingsbeleid

[Documentatie-inhoud](README.md)

Meld kwetsbaarheden privé aan bundle-sec@omegaguardian.fr om een versleuteld kanaal af te spreken. Geen geheimen, actieve tokens of privé-URL’s in het eerste bericht. Vermeld Guardian-, Symfony- en PHP-versies, impact, voorwaarden en minimale reproductie. Beheerders bevestigen, onderzoeken, herstellen privé, testen en coördineren melding en oplossing.

Verstuur nooit wachtwoorden, TOTP-geheimen, encryptiesleutels of diagnostische URL's per e-mail.

Maak samen een back-up van kluis en sleutel en test herstel. Verloren sleutels worden nooit automatisch hermaakt. Controleer na verwijdering de profiler alleen privé. Productie: APP_DEBUG=0, WebProfilerBundle en verzameling uit. Een lokale auditketen bewijst geen integriteit op een gecompromitteerde host.
