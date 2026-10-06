# Testing and failure handling

## Quality command

```bash
composer quality
```

The command fails when a production non Python file exceeds 300 lines, a Python file exceeds 400 lines, a test file exceeds 1,000 lines, a behavioral test fails, or PHP formatting differs from the repository standard.

## Covered behavior

- Known and unknown login failures use the same response.
- Password reset requests do not reveal account existence.
- Reset tokens are hashed, expire, and cannot be reused.
- Admin users cannot access super admin routes.
- Remember me login stores a recaller token.
- Application and dynamic attribute writes validate before mutation.
- Invalid typed values leave the database unchanged.
- Attribute definition changes cannot invalidate existing values.
- Attribute and application deletion cascades pivot data.
- CSV export neutralizes cells interpreted as spreadsheet formulas.
- Timeline status and date ranges are constrained.
- Project deletion cascades timeline entries.
- Categories in use cannot be removed through the application.
- Employee identifiers and operational date ranges are validated.

## Failure boundaries

Application writes wrap the base record, dynamic pivot values, and activity log in one database transaction. Timeline mutations and their activity records use the same approach. A database failure therefore cannot leave the primary mutation without its corresponding log.

Mail delivery is currently synchronous. A mail provider failure returns an error to the administrator and is logged without exposing provider details to the browser. Moving mail to an after commit queue is the next reliability improvement.

## Manual verification

Before release, run:

```bash
php artisan migrate:fresh
php artisan migrate:reset
composer quality
composer audit --locked
```

Then exercise login, application create/edit/delete, typed attributes, timeline edits, CSV download, and role restricted screens using synthetic data.
