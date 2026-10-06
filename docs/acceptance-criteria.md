# Acceptance criteria

## Authentication and account recovery

- Valid users can sign in, optionally remember the session, and sign out.
- Failed sign-in and password-reset requests do not disclose whether an email exists.
- Reset tokens are hashed, expire after 60 minutes, and cannot be reused.
- Admin routes reject guests; governance routes reject non-super-admin users.

## Application inventory

- An admin can create, inspect, update, filter, export, and delete an application.
- Application names are unique and required metadata is validated.
- Global attributes appear on every application and validate values by their declared type.
- An incompatible attribute-definition change is rejected without partial writes.
- CSV export streams rows and neutralizes spreadsheet formulas.

## Operational records

- Employee names, emails, and phone numbers are unique.
- A project belongs to an existing category and its timeline references existing employees.
- Timeline start, deadline, completion date, and status must describe a consistent state.
- A category in use cannot be removed.
- Deleting an employee or project also removes timelines that cannot exist without it.
- Internship participant counts are positive and the exit date is after the entry date.

## Governance and profile

- Only a super admin can manage administrator accounts and view the governance dashboard.
- Account creation is rolled back if credential delivery fails.
- An administrator email remains unique and passwords are stored only as hashes.
- A user can update their own name and can change their password only after supplying the current password.
- Mutating core records produces an activity entry with the actor, module, action, and safe summary.

## User interface

- Every authenticated screen uses one navigation and feedback system.
- Forms preserve understandable server validation messages.
- Destructive actions require explicit confirmation.
- Lists provide empty states and pagination or documented small-catalogue filtering.
- The interface remains usable at 375 px viewport width without hiding required actions.
- Main user flows produce no browser-console errors.

## Reproducibility and quality

- `composer install` succeeds on PHP 8.3 from the committed lock file.
- Migrations run from an empty database and roll back cleanly.
- `composer quality` passes tests, formatting, and file-length checks.
- CI executes the same quality command on every push and pull request.
- Optional synthetic demo data can populate every product area without enabling itself in production.

