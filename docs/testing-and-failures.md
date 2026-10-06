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
- Completed timeline statuses and completion dates must agree.
- Project deletion cascades timeline entries.
- Projects can link to inventory applications, while application deletion safely nulls the link.
- One end-to-end scenario verifies linked records across user screens, JSON detail, CSV export, and audit logs.
- Categories in use cannot be removed through the application.
- Employee identifiers and operational date ranges are validated.
- Deleting an administrator preserves prior audit events with an anonymous actor.
- Profile password changes require the current password.
- Primary authenticated screens render without server errors.

## Current result

The Phase 4 gate runs the full behavioral suite plus the integrated inventory-to-completed-project scenario. Blade compilation, route discovery, Laravel Pint, and the repository file-length check run alongside this suite.

Browser validation covers all ten primary authenticated screens for a super admin, admin navigation restrictions, the forbidden governance route, fresh console errors, and 390 × 844 responsive behavior. See [browser validation](browser-validation.md).

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
