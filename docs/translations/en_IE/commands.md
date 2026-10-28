# Command reference

[Documentation contents](README.md)

Run from the Symfony root in a trusted terminal. Examples use dev. Never put passwords, keys or recovery codes in arguments, logs or Git.

## Discover commands and get help


```bash
php bin/console list guardian --env=dev
php bin/console help guardian:setup --env=dev
php bin/console guardian:doctor --help --env=dev
```


--env=dev and --no-interaction are Symfony options. --no-interaction does not supply requested secrets; do not use it for interactive password commands. --help describes syntax; -v increases detail. Exit code 0 indicates the result reported by the command; --no-admin can succeed without a usable portal.

## `vendor/bin/guardian`

Enables the bundle in dev/test, clears the cache, runs guardian:setup --automatic, clears the cache again, then runs guardian:doctor and guardian:access. Stops on the first failure; repeating preserves accounts and keys. Refuses APP_ENV=prod and requires proc_open.

No argument, --help, -h or help displays read-only help. setup is the only action and accepts no additional options.


```bash
php vendor/bin/guardian --help
php vendor/bin/guardian setup
```


| CLI | Command reference |
|---|---|
| [`guardian:setup`](#guardiansetup) | Prepares the private route, key, vault, Symfony files and an administrator if none is active. |
| [`guardian:install`](#guardianinstall) | Initializes the vault and creates an administrator with --username, default admin. |
| [`guardian:access`](#guardianaccess) | Prints the panel path and location of initial credentials, never the password. |
| [`guardian:doctor`](#guardiandoctor) | Checks the vault, permissions, key, route, active administrator and configuration consistency. |
| [`guardian:password:reset`](#guardianpasswordreset) | Changes the existing username account’s password using hidden input under the active policy, revokes all its sessions and records the action. |
| [`guardian:mfa:reset`](#guardianmfareset) | Removes username’s TOTP secret and counter, revokes its sessions and records the action. |
| [`guardian:login:unlock`](#guardianloginunlock) | Resets username’s account counters. |
| [`guardian:policy:reset`](#guardianpolicyreset) | Clears the IP allowlist and disables scheduling, JIT and remote emergency access. |
| [`guardian:lockdown`](#guardianlockdown) | on closes diagnostics and removes every session; business pages stay available. |
| [`guardian:emergency`](#guardianemergency) | Generates 5 single-use codes for an existing username, replacing its previous codes. |
| [`guardian:alerts:flush`](#guardianalertsflush) | Sends queued alerts to alert_email through PHP mail(). |
| [`guardian:password:benchmark`](#guardianpasswordbenchmark) | Benchmarks hash verification with a synthetic secret, never a user password. |
| [`guardian:uninstall`](#guardianuninstall) | Prepares removal of generated files attributable to Guardian; retains accounts, settings, logs and key by default. |

## `guardian:setup`

Prepares the private route, key, vault, Symfony files and an administrator if none is active. Preserves existing accounts. Refuses production configuration. After a direct invocation, clear the cache and run the checks.


```bash
php bin/console guardian:setup --automatic --env=dev
```

--automatic generates admin and a secret in a private 0600 file. --path selects an explicit path of 25 to 181 characters and cannot rotate an existing path. --no-admin skips administrator creation; without an active account the portal is unusable. --username suggests the interactive account name, default admin; it does not rename the automatic account.

```text
--path: ^/[a-zA-Z0-9/_-]{24,180}$
/_profiler; /_wdt → ∅
```

```bash
php bin/console guardian:setup --username=operator --env=dev
php bin/console cache:clear --env=dev
php bin/console guardian:doctor --env=dev
php bin/console guardian:access --env=dev
```

## `guardian:install`

Initializes the vault and creates an administrator with --username, default admin. Requests a hidden password and applies the active policy. Rejects an existing account. Does not create routes or Symfony configuration; normally use the complete assistant.


```bash
php bin/console guardian:install --username=admin --env=dev
```


Username: 3 to 64 alphanumeric characters, . _ -

## `guardian:access`

Prints the panel path and location of initial credentials, never the password. --base-url accepts the real HTTPS origin, optionally with a port, without credentials, query or fragment. Replace the example host with the application host.


```bash
php bin/console guardian:access --base-url=https://example.com --env=dev
```


## `guardian:doctor`

Checks the vault, permissions, key, route, active administrator and configuration consistency. Returns exit code 1 when a check reports a problem. PHP CLI and PHP-FPM timezones may differ; local diagnostics do not test Internet exposure or proxy rules.


```bash
php bin/console guardian:doctor --env=dev
```


## `guardian:password:reset`

Changes the existing username account’s password using hidden input under the active policy, revokes all its sessions and records the action. Never supply the password as an argument.


```bash
php bin/console guardian:password:reset admin --env=dev
```


## `guardian:mfa:reset`

Removes username’s TOTP secret and counter, revokes its sessions and records the action. Enroll a new authenticator; the global MFA requirement stays enabled and may still block profiler access.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```


## `guardian:login:unlock`

Resets username’s account counters. --ip ADDRESS also clears that IPv4/IPv6 origin’s counter; otherwise an origin lockout may remain. --all clears every counter. Accounts, passwords and sessions are unchanged; the action is audited.


```bash
php bin/console guardian:login:unlock admin --ip=192.0.2.10 --env=dev
```


```bash
php bin/console guardian:login:unlock --all --env=dev
```

## `guardian:policy:reset`

Clears the IP allowlist and disables scheduling, JIT and remote emergency access. Authentication, MFA, global lockdown and login lockouts remain. Reconfigure the desired restrictions in the panel after recovery.


```bash
php bin/console guardian:policy:reset --env=dev
```


## `guardian:lockdown`

on closes diagnostics and removes every session; business pages stay available. off lifts only global lockdown: users must sign in again and other policies still apply.


```bash
php bin/console guardian:lockdown on --env=dev
```


```bash
php bin/console guardian:lockdown off --env=dev
```

## `guardian:emergency`

Generates 5 single-use codes for an existing username, replacing its previous codes. Displayed once: keep them off-server in a secret manager. Remote use requires emergency access enabled, an authenticated session, password and reason; it cannot bypass global lockdown.


```bash
php bin/console guardian:emergency admin --env=dev
```


## `guardian:alerts:flush`

Sends queued alerts to alert_email through PHP mail(). Configure the mail transport and a scheduled task, then verify actual delivery. Failed alerts remain queued; exit code 1 means an invalid recipient or a failed send.


```bash
php bin/console guardian:alerts:flush --env=dev
```


## `guardian:password:benchmark`

Benchmarks hash verification with a synthetic secret, never a user password. --iterations accepts 1 to 10, default 3. Outputs JSON with algorithm, elapsed time and verifications per second. Measures one local CPU process, not a GPU or an attacker.


```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```


## `guardian:uninstall`

Prepares removal of generated files attributable to Guardian; retains accounts, settings, logs and key by default. Stop workers and run before composer remove, then clear the dev cache and restart. Customized files or files without ownership evidence require review.


```bash
php bin/console guardian:uninstall --env=dev
```

--purge requests data deletion with a default-no confirmation. --force requires --purge and confirms destruction; required with --no-interaction for automated purging. External vaults, links and unknown files may prevent complete deletion: retain necessary keys and inspect the result.

```bash
php bin/console guardian:uninstall --purge --env=dev
# --purge --force --no-interaction
```

## Uninstallation and data


```bash
php bin/console guardian:uninstall --env=dev
composer remove --dev omegaguardian/profiler-shield
php bin/console cache:clear --env=dev
```


Back up vault and key together and test restoration. Lost keys are never regenerated automatically. After removal, check the profiler only in a private environment. Production requires APP_DEBUG=0 with WebProfilerBundle and collection disabled. A local audit chain does not prove integrity on a compromised host.

[Uninstallation and data](desinstallation.md)
