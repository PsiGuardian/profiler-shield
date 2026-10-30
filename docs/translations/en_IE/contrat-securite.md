# Security invariants

[Documentation contents](README.md)

Deny diagnostics if identity, role, session, network, schedule, JIT or lockdown cannot be checked. The browser does not decide expiry. Viewing profiles and collecting them are distinct. Remembering devices does not authenticate. Emergency access requires identity, a consumed code and durable audit; critical audit failure prevents opening. Invalid configuration must not be partially applied. Optional controls require no remote service.

Back up vault and key together and test restoration. Lost keys are never regenerated automatically. After removal, check the profiler only in a private environment. Production requires APP_DEBUG=0 with WebProfilerBundle and collection disabled. A local audit chain does not prove integrity on a compromised host.

Guardian controls the profiler and debug toolbar. An authorized administrator or developer account is required. MFA, schedules, JIT, network restrictions and lockdown may still deny access. Stealth mode hides entry points; it does not replace these checks.

Idle timeout, absolute lifetime and device remembrance serve different purposes. Remembering a device does not bypass schedules or JIT. Revocation terminates relevant sessions on the server. Use individual accounts to attribute actions in the journal.

The IP allowlist accepts IPv4, IPv6 and CIDR networks. Check Symfony trusted proxy configuration before enabling it. Use HTTPS for secure cookies. Guardian does not repair exposed web servers or databases.
