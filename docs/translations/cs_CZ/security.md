# Bezpečnostní politika

[Obsah dokumentace](README.md)

Zranitelnosti hlaste soukromě na bundle-sec@omegaguardian.fr pro dohodu o šifrovaném kanálu. Bez tajemství, aktivních tokenů a soukromých URL v první zprávě. Uveďte verze Guardian, Symfony a PHP, dopad, předpoklady a minimální reprodukci. Správci potvrdí, prošetří, opraví soukromě, otestují a koordinují oznámení a opravu.

Nikdy neposílejte e-mailem hesla, tajemství TOTP, šifrovací klíče ani diagnostické URL.

Zálohujte úložiště a klíč společně a vyzkoušejte obnovu. Ztracené klíče se automaticky neobnovují. Po odstranění kontrolujte profiler jen neveřejně. Produkce: APP_DEBUG=0, WebProfilerBundle a sběr vypnuté. Místní řetězec auditu nedokazuje integritu napadeného hostitele.
