# Sicherheitsrichtlinie

[Dokumentationsübersicht](README.md)

Schwachstellen privat an bundle-sec@omegaguardian.fr melden und verschlüsselten Kanal vereinbaren. Keine Geheimnisse, aktiven Tokens oder privaten URLs im Erstkontakt. Versionen von Guardian, Symfony und PHP, Auswirkungen, Voraussetzungen und minimale Reproduktion nennen. Verantwortliche bestätigen, untersuchen, korrigieren privat, testen und koordinieren Hinweis und Korrektur.

Senden Sie niemals Passwörter, TOTP-Geheimnisse, Verschlüsselungsschlüssel oder Diagnose-URLs per E-Mail.

Tresor und Schlüssel gemeinsam sichern und Wiederherstellung testen. Verlorene Schlüssel werden nie automatisch neu erzeugt. Nach Entfernung den Profiler nur privat prüfen. Produktion: APP_DEBUG=0, WebProfilerBundle und Sammlung deaktiviert. Eine lokale Protokollkette beweist keine Integrität eines kompromittierten Hosts.
