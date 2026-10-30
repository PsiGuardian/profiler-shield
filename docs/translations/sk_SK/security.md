# Bezpečnostná politika

[Obsah dokumentácie](README.md)

Zraniteľnosti hláste súkromne na bundle-sec@omegaguardian.fr pre dohodu o šifrovanom kanáli. Bez tajomstiev, aktívnych tokenov a súkromných URL v prvej správe. Uveďte verzie Guardian, Symfony a PHP, dopad, predpoklady a minimálnu reprodukciu. Správcovia potvrdia, prešetria, opravia súkromne, otestujú a koordinujú oznámenie a opravu.

Nikdy neposielajte e-mailom heslá, tajomstvá TOTP, šifrovacie kľúče ani diagnostické URL.

Zálohujte úložisko a kľúč spolu a vyskúšajte obnovu. Stratené kľúče sa automaticky neobnovujú. Po odstránení kontrolujte profiler len neverejne. Produkcia: APP_DEBUG=0, WebProfilerBundle a zber vypnuté. Miestny reťazec auditu nedokazuje integritu napadnutého hostiteľa.
