# Security policy

[Documentation contents](README.md)

Report vulnerabilities privately to bundle-sec@omegaguardian.fr to arrange an encrypted channel. No secrets, live tokens or private URLs in the first message. Include Guardian, Symfony and PHP versions, impact, prerequisites and minimal reproduction. Maintainers acknowledge, investigate, fix privately, test and coordinate advisory and patch.

Never email passwords, TOTP secrets, encryption keys or diagnostic URLs.

Back up vault and key together and test restoration. Lost keys are never regenerated automatically. After removal, check the profiler only in a private environment. Production requires APP_DEBUG=0 with WebProfilerBundle and collection disabled. A local audit chain does not prove integrity on a compromised host.
