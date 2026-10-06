# Browser validation

## Scope

The local portfolio product was validated on 6 October 2026 with the opt-in synthetic dataset. No deployment or external service was used.

## Super-admin flow

The following authenticated screens rendered with their expected page heading and without a server error:

- application dashboard;
- application inventory;
- dynamic attributes;
- project timeline;
- employees;
- projects and categories;
- internship periods;
- profile and password controls;
- administrator management;
- system audit.

The timeline, project, and internship screens were rechecked after defects found during browser validation were fixed. A fresh console-log window contained no errors.

## Admin authorization

The synthetic operator account:

- reached the shared application dashboard;
- did not receive governance navigation links;
- received HTTP 403 content when directly requesting administrator management.

## Responsive validation

The dashboard, timeline, and application inventory were checked at a 390 × 844 viewport. Each page:

- displayed the mobile navigation control;
- kept the document within the viewport width;
- retained its primary page heading and content.

## Automated evidence

`php artisan test` passes 32 tests with 126 assertions. The suite covers mutation workflows and failure cases that would be destructive or inefficient to repeat against the local demonstration database.
