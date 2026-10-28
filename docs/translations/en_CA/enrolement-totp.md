# Personal two-factor authentication

[Documentation contents](README.md)


## Personal two-factor authentication

Protect your Guardian account with temporary codes, without external QR services.

Each person enrolls their own TOTP secret and confirms a code. Requiring MFA blocks profiler access for unenrolled accounts. The QR code stays local to the portal. Plan administrative recovery before requiring this protection for the whole team.

## 1. Confirm your identity

Enter the same password you use to sign in to Guardian. You will enter it only once for this setup.

## 2. Scan this QR code in your authenticator

Guardian generates the QR locally. No requests are sent to external services.

## 3. Confirm a six-digit code

Enter the code shown in your authenticator. No second password is required.

## Privacy: do not enter the website URL

If your authenticator asks for a name or website, enter only “ΩGuardian” (or a neutral alias). Do not enter your Symfony instance URL. Never use an online QR generator: it would receive your secret.

Keep this private. It can generate your TOTP codes. Do not share it or take screenshots.

The provisional secret expires after 10 minutes or 5 wrong codes; restart setup then. Wrong codes retain the form without requesting the password again. The confirmation counter is consumed to prevent replay. SVG QR uses bacon/bacon-qr-code and ext-iconv, without GD, XMLWriter or network. Manual entry remains available. The otpauth URI uses a neutral provider and account, not the site domain; never share the secret or private URL.

## Recovery and verification

Removes username’s TOTP secret and counter, revokes its sessions and records the action. Enroll a new authenticator; the global MFA requirement stays enabled and may still block profiler access.


```bash
php bin/console guardian:mfa:reset admin --env=dev
```
