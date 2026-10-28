# Local interface fixtures

Run `GUARDIAN_EXPORT_PREVIEW=1 php tests/flow_smoke.php` to generate disposable HTML views for interface development. They use synthetic accounts and destroyed test sessions; they are not connected panels and must not be published as an administration service. Generated preview HTML is excluded from the release archives.
