# Personal two-factor authentication

[Documentation contents](README.md)


## Personal two-factor authentication

Each person enrolls their own TOTP secret and confirms a code.

Each person enrolls their own TOTP secret and confirms a code. Requiring MFA blocks profiler access for unenrolled accounts. Plan administrative recovery before requiring this protection for the whole team. Choose manual account setup and enter the displayed secret key. Use SHA-1, six digits and a 30-second period.

## 1. Confirm your identity

Enter the same password you use to sign in to Guardian. You will enter it only once for this setup.

## 2. Enter the key in your authenticator

Choose manual account setup and enter the displayed secret key. Use SHA-1, six digits and a 30-second period.

## 3. Confirm a six-digit code

Enter the code shown in your authenticator. No second password is required.

## Privacy: do not enter the website URL

If your authenticator asks for a name or website, enter only “ΩGuardian” (or a neutral alias). Do not enter your Symfony instance URL.

Keep this private. It can generate your TOTP codes. Do not share it or take screenshots.

The provisional secret expires after 10 minutes or 5 wrong codes; restart setup then. Wrong codes retain the form without requesting the password again. The confirmation counter is consumed to prevent replay.

## Recovery and verification

Removes username’s TOTP secret and counter, revokes its sessions and records the action. Enroll a new authenticator; the global MFA requirement stays enabled and may still block profiler access.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```
