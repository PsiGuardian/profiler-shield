# Translations

[Documentation contents](README.md)

The portal offers 28 regional variants and 25 catalogues; the four English variants share a catalogue. Static text is translated then escaped. Usernames, custom messages and user data are not translated. No remote service receives text. Audits check keys, parameters, UTF-8, technical commands and documentation links; HTTP tests visit the views in every locale.
```bash
php tests/translation_audit.php --strict
php tests/documentation_audit.php
composer test
```
