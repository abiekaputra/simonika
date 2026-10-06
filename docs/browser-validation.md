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

## Integrated workflow

After the Phase 4 migration and synthetic seed completed, the project screen showed `Modernisasi Portal` linked to `Portal Layanan`, categorized as `Layanan Digital`, with one timeline entry. The application inventory reported one related project, and its detail modal displayed the project name, category, and timeline count. No browser console errors were captured during this check.

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

`php artisan test` passes 35 tests with 168 assertions. The suite covers mutation workflows, the integrated inventory-to-project flow, and failure cases that would be destructive or inefficient to repeat against the local demonstration database.
