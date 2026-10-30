# Biztonsági szabályzat

[Dokumentáció tartalma](README.md)

Sérülékenységet privát módon a bundle-sec@omegaguardian.fr címen jelezzen titkosított csatorna egyeztetéséhez. Első üzenetben ne legyen titok, aktív token vagy privát URL. Adja meg Guardian, Symfony és PHP verzióját, hatást, feltételeket és minimális reprodukciót. A karbantartók visszaigazolnak, vizsgálnak, privát javítanak, tesztelnek és összehangolják a közleményt és javítást.

Soha ne küldjön e-mailben jelszavakat, TOTP-titkokat, titkosítási kulcsokat vagy diagnosztikai URL-eket.

Tárolót és kulcsot együtt mentse, próbálja a visszaállítást. Elveszett kulcs nem generálódik újra automatikusan. Eltávolítás után a profilert csak privát környezetben ellenőrizze. Éles környezet: APP_DEBUG=0, WebProfilerBundle és gyűjtés kikapcsolva. Helyi auditlánc nem bizonyít sértetlenséget feltört gépen.
