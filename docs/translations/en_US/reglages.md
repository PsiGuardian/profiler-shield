# Settings

[Documentation contents](README.md)


## Additional protections

Controls are grouped with their relevant settings. Disabled options are greyed out and their values retained for later use.

Guardian controls the profiler and debug toolbar. An authorized administrator or developer account is required. MFA, schedules, JIT, network restrictions and lockdown may still deny access. Stealth mode hides entry points; it does not replace these checks.


```text
POST ∧ CSRF ∧ role=admin ∧ password
```


| Preset | Settings |
|---|---|
| Solo · flexible | solo: mfa=false; schedule=false; jit=false; idle=60 min; absolute=8 h; remember=14 d |
| Team · enhanced | team: mfa=true; schedule=false; jit=false; idle=45 min; absolute=8 h; remember=7 d |
| Sensitive · restricted | strict: mfa=true; schedule=true; jit=true; idle=15 min; absolute=4 h; remember=1 d |

| Parameter | Settings | Initial value |
|---|---|---|
| `stealth` | Return 404 rather than 403 on unauthorized diagnostic routes. | `true` |
| `mfa` | All users must complete TOTP enrollment before enabling this requirement. | `false` |
| `idle_minutes` | Minutes without activity before the session expires. | `60` |
| `absolute_hours` | Hours regardless of activity. | `8` |
| `remember_days` | Days. Never replaces login or working-hours policy. | `14` |
| `schedule_enabled` | Choose when diagnostic tools are accessible. Business pages remain available. | `false` |
| `weekdays` | Select the days when diagnostics are permitted. | `1,2,3,4,5` |
| `start` | Opening time | `09:00` |
| `end` | Closing time | `18:00` |
| `timezone` | Detected from the PHP runtime serving this page. PHP CLI, PHP-FPM and the operating system can use different zones. Check the real server configuration before saving. | `OS / PHP CLI` |
| `portal_closed_outside_hours` | The private URL returns 404 outside hours. Local CLI recovery only. | `false` |
| `jit_enabled` | Only open the profiler during a requested diagnostic window. | `false` |
| `jit_minutes` | Minutes, only used when enabled. | `30` |
| `ip_allowlist` | Warning: mistakes can lock you out. Local recovery: guardian:policy:reset. | `∅` |
| `audit_level` | Select audit detail and local retention duration. | `standard` |
| `audit_retention_days` | Days before log cleanup. | `30` |
| `alert_email` | Receives emergency alerts. Access must not depend on email delivery. | `∅` |
| `emergency_enabled` | Grant a 30-minute exception with a one-time code and audit logging. | `false` |
| `login_rules` | Default: 10 failures, then a 10-minute lockout. Counters apply to both account and network origin, including password confirmations. | `10 / 10 / 30 / 30` |
| `password_policy` | These rules apply to password creation and changes, on the web and in the CLI. They do not reveal or inspect existing passwords. | `min_length=15` |
| `error_mode` | Application pages: preserves the site’s existing 404 responses; exceptions use its 404 template when available, otherwise a neutral page. Neutral: standard message. Custom: uses your text. | `native` |
| `error_reference` | The reference applies to Guardian’s neutral pages: random generates a new code, not a random message. It is not a log correlation identifier. | `none` |
| `error_reference_text` | Available only with a custom static reference. | `∅` |
| `error_custom_text` | Available only with “Custom neutral message”. Your text is retained when switching modes. | `∅ → custom` |

Enable scheduling, select days, an inclusive start time and an exclusive end time. The saved timezone determines local times. Overnight intervals are not accepted. Hiding the portal outside hours prevents remote emergency access; keep local recovery commands available.

Idle timeout, absolute lifetime and device remembrance serve different purposes. Remembering a device does not bypass schedules or JIT. Revocation terminates relevant sessions on the server. Use individual accounts to attribute actions in the journal.

JIT requires activating a temporary window from an authorized session. Emergency access, when enabled, requires a password, a single-use code and a reason. It does not bypass global lockdown. Local commands remain necessary if the portal is unreachable.

The IP allowlist accepts IPv4, IPv6 and CIDR networks. Check Symfony trusted proxy configuration before enabling it. Use HTTPS for secure cookies. Guardian does not repair exposed web servers or databases.

Level and retention determine available events, up to 5000 entries. Filter by type and account before changing pages. Alerts are queued; configure their transport and delivery. A local hash chain does not guarantee integrity on a compromised host.

Set minimum length and required categories in Connections and passwords. Account creation badges describe the saved policy. Changes apply to new passwords; select mandatory renewal for existing accounts. Guardian chooses Argon2id when PHP provides it, otherwise bcrypt.

Filter all retained events by type and account. Newest events appear first, in pages of 50. Scrolling does not load the next page.

## Environment


```dotenv
GUARDIAN_LOGIN_PATH=/internal/<random-48-hex>
GUARDIAN_ENCRYPTION_KEY=<64-hex>
GUARDIAN_STORAGE_DIR=<private-directory>
GUARDIAN_COLLECTION_BRIDGE=0
GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED=0
GUARDIAN_SHOW_PHP_VERSION=0
```


The proxy handles TLS, normalization and volumetric protection. The gateway checks diagnostics before routing; business routes do not read accounts or sessions. Guardian identity is separate from Symfony accounts. Back up the private vault and its key together, outside the public directory. Compromised PHP or host access can expose both. Storage is single-host. PHP boot, cache or proxy failures can affect the whole application.

Back up vault and key together and test restoration. Lost keys are never regenerated automatically. After removal, check the profiler only in a private environment. Production requires APP_DEBUG=0 with WebProfilerBundle and collection disabled. A local audit chain does not prove integrity on a compromised host.

## Recovery and verification

Clears the IP allowlist and disables scheduling, JIT and remote emergency access. Authentication, MFA, global lockdown and login lockouts remain. Reconfigure the desired restrictions in the panel after recovery.

on closes diagnostics and removes every session; business pages stay available. off lifts only global lockdown: users must sign in again and other policies still apply.


```bash
php bin/console guardian:policy:reset --env=dev
php bin/console guardian:lockdown on --env=dev
php bin/console guardian:doctor --env=dev
```
