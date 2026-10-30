# Sign-ins and passwords

[Documentation contents](README.md)


## Accounts and first sign-in

Welcome to Guardian. Your initial password is temporary. Choose a new secret: the panel and diagnostics stay locked until you do. The initial credential file is removed after validation.

Guardian accounts are separate from site accounts. The initial account and new accounts must change their passwords before continuing. The security role views permitted information without profiler access. Changing a role or password invalidates previous sessions.

Prepares the private route, key, vault, Symfony files and an administrator if none is active. Preserves existing accounts. Refuses production configuration. After a direct invocation, clear the cache and run the checks.


```bash
php bin/console guardian:access --env=dev
```


| Saved password requirements | Minimum length (Unicode characters) |
|---|---|
| initial-admin-credentials.txt | 0600; random_int; &gt;=32 |
| password_policy[min_length] | 8–64; 15 |
| input | Unicode; ≤512 UTF-8 B |
| Argon2id | PHP + Argon2id → Argon2id |
| bcrypt | PHP − Argon2id → bcrypt; ≤72 UTF-8 B |

## Password policy

These rules apply to password creation and changes, on the web and in the CLI. They do not reveal or inspect existing passwords.

Initial value: 15 characters. Use a unique passphrase or a password manager. Required character categories do not guarantee an unpredictable password.

On this server, Guardian automatically uses Argon2id for new passwords. Keep its PHP support enabled. The application limit is 512 UTF-8 bytes.

This PHP server does not provide Argon2id. Guardian therefore uses bcrypt and rejects passwords exceeding 72 UTF-8 bytes. Ask your system administrator to enable Argon2id support in PHP.

This option immediately revokes existing sessions, including yours. Accounts must change their passwords before configuring Guardian or opening diagnostics.

Changes the existing username account’s password using hidden input under the active policy, revokes all its sessions and records the action. Never supply the password as an argument.

## Progressive lockouts

Each stage defines a failure limit, counting window, lockout duration and repeat window. Default: 10 failures trigger a 10-minute lockout. Repeated failures can trigger the next stage. Checks cover both account and network origin, including administrator confirmations.

Encrypted counters are independent per normalized account and origin; raw IP addresses are not retained. An atomic reservation precedes hashing and expires after 120 seconds. Abandoned reservations count as failures; late results cannot create sessions. Invalid CSRF reserves nothing. Successful login clears account failures but retains repeat and origin history. At 10000 active entries, new identities are refused rather than evicting lockouts.

Stages run in order. After a lockout, new failures within the repeat window use the next stage. Outside that window, the first stage applies again. The last stage repeats. Blocked requests do not extend the delay. Maximum 8 stages. Local recovery: guardian:login:unlock --all.

| Stage | Saved password requirements |
|---|---|
| Failures before lockout | 1–100 |
| Lockout (minutes) | 1–10080 |
| Counting window (minutes) | 1–1440 |
| Repeat window after unlock (minutes) | 1–10080 |
| Stage | 1–8; block[n+1] ≥ block[n] |
| Initial value | 10 / 10 / 30 / 30 |
| 5 → 10 min; 5 → 2 h | 5 / 10 / 30 / 30 → 5 / 120 / 30 / 30 |
| HTTP | 429; Retry-After |

## Exhaustive-search cost laboratory

Compare uniformly random secrets of a fixed length without entering a real password. Guess rates and hardware pricing are adjustable assumptions. No sign-in attempts are sent.

Calculation uses disjoint ASCII categories and BigInt. Inclusion-exclusion gives the exact compliant search space; missing categories or insufficient length yield zero. Average rank is (N+1)/2 without replacement. Expected lockout time accounts for thresholds and the repeating final stage. Estimates are disabled when the rate cannot fit the windows. Offline price is elapsed hours multiplied by assumed hourly cost.

With a fixed alphabet, requiring a category reduces the number of compliant secrets. Expanding the alphabet is a different operation. Lockout times assume continuous guesses fast enough to stay within configured windows.

This model does not measure the strength of a human-chosen password. It excludes dictionaries, reuse, leaks and targeted strategies. Web lockouts do not protect stolen hashes. The initial offline rate is illustrative, not a benchmark. Local CPU measurement: guardian:password:benchmark.

Large numbers are written in full, with digit grouping and without E notation. Times and costs are rounded. Compare the same row using the same assumptions.

| Available simulation alphabet | Possible compliant secrets |
|---|---|
| ASCII lowercase | 26 |
| ASCII uppercase | 26 |
| ASCII digits | 10 |
| ASCII symbols excluding space | 32 |
| Assumed online rate (guesses/s) | 1 / s |
| Assumed offline rate (hashes/s) | 10000 / s |
| Assumed hardware cost (€/hour) | 2 € / h |

```bash
php bin/console guardian:password:benchmark --iterations=3 --env=dev
```

## Recovery and verification

Back up vault and key together and test restoration. Lost keys are never regenerated automatically. After removal, check the profiler only in a private environment. Production requires APP_DEBUG=0 with WebProfilerBundle and collection disabled. A local audit chain does not prove integrity on a compromised host.

Resets username’s account counters. --ip ADDRESS also clears that IPv4/IPv6 origin’s counter; otherwise an origin lockout may remain. --all clears every counter. Accounts, passwords and sessions are unchanged; the action is audited.

[Command reference](commands.md)
