# Changelog

## 0.2.32-pro - 2026-03-20

- Reduced publication package to English-only resources for Moodle submission.
- Removed Spanish language pack from `lang/es`.
- Removed Spanish manual from `docs/MANUAL_ES.md`.
- Updated README references and feature list to English-only documentation/language packaging.

## 0.2.31-pro - 2026-03-20

- Fixed PostgreSQL upgrade path for `local_smartnoticespro_log.userid`.
- Upgrade now drops dependent key/index structures before changing field nullability and recreates the foreign key afterwards.
- Prevents `ddldependencyerror` during upgrade on existing sites.

## 0.2.30-pro - 2026-03-20

- Fixed anonymous/login-page notice logging for non-authenticated users.
- Updated `local_smartnoticespro_log.userid` to allow `NULL` values:
  - corrected `db/install.xml`
  - added DB upgrade step for existing installations
- Prevents `dmlwriteexception` when an impression is logged before user login.

## 0.2.29-pro - 2026-03-20

- Fixed CI validation issues reported by `mustache`, `grunt`, `phpcs`, and `phpdoc`.
- Added example context to `templates/modal.mustache` for Mustache lint/HTML validation.
- Added missing JSDoc to AMD source and aligned modal source files with Moodle linting expectations.
- Updated CSS to satisfy Stylelint:
  - removed `!important`
  - replaced unsupported `min()` width usage
  - normalized hex color length
- Corrected Moodle boilerplate formatting in PHP language and page files.
- Fixed PHPCS issues in PHP files:
  - removed unnecessary `MOODLE_INTERNAL` checks where applicable
  - replaced `elseif` with `else if`
  - fixed anonymous function spacing
  - adjusted the public tracking endpoint to intentionally bypass the login sniff
- Completed PHPDoc cleanup:
  - added missing parameter docs
  - corrected parameter types
  - fixed interface ordering and multiline formatting in the privacy provider

## 0.2.28-pro - 2026-03-20

- Fixed Privacy API coverage for user-related log data:
  - added metadata for `local_smartnoticespro_log`
  - export/delete/userlist handling now includes interaction logs
- Added missing capability strings required by Moodle validation:
  - `smartnoticespro:manageglobalnotices`
  - `smartnoticespro:managecoursenotices`
  - `smartnoticespro:viewnotices`
- Removed manual stylesheet inclusion from `lib.php`; Moodle now manages `styles.css` loading.
- Removed risky `$GLOBALS` request guard and kept request-local render protection with `static`.
- Added/normalized Moodle boilerplate headers in PHP, CSS, JS, and Mustache source files.
- Fixed upgrade savepoint component names in `db/upgrade.php` to `smartnoticespro`.
- Added GitHub Actions CI workflow using `moodle-plugin-ci`.
- Cleaned README for Moodle/GitHub submission consistency.

## 0.2.27-pro - 2026-03-09

- Updated PRO screenshots from workspace source folder:
  - `/screenshots` (same root where release zips are generated)
- Removed missing image slot (former image 3) and rebuilt sequence to 8 consecutive files:
  - `smartnoticespro_01.png` ... `smartnoticespro_08.png`
- Updated screenshot references in:
  - `README.md`
  - `docs/MANUAL_ES.md`
  - `docs/MANUAL_EN.md`

## 0.2.26-pro - 2026-03-09

- Full PRO package normalization to `smartnoticespro`:
  - plugin folder: `local/smartnoticespro`
  - plugin component: `local_smartnoticespro`
  - local routes: `/local/smartnoticespro/...`
  - capabilities: `local/smartnoticespro:*`
  - namespaces/classes moved to `local_smartnoticespro\\...`
  - DB tables normalized to `local_smartnoticespro*`
  - language files renamed to:
    - `lang/en/local_smartnoticespro.php`
    - `lang/es/local_smartnoticespro.php`
- Screenshot assets normalized to PRO naming:
  - `screenshots/smartnoticespro_01.png` ... `smartnoticespro_09.png`
- Documentation and metadata aligned to `smartnoticespro` paths and naming.

## 0.2.25 - 2026-03-09

- Updated PRO branding text from `Smart Notices` to `Smart Notices Pro` in:
  - language strings (EN/ES)
  - README
  - manuals (EN/ES)
- Kept technical plugin component unchanged:
  - `local_smartnoticespro`

## 0.2.24 - 2026-03-09

- Removed screenshot `smartnotices_doc_03.png` from documentation set.
- Renamed screenshot files by removing `_doc` suffix:
  - from `smartnotices_doc_XX.png` to `smartnotices_XX.png`
- Renumbered screenshots to a continuous 9-file set:
  - `smartnotices_01.png` ... `smartnotices_09.png`
- Improved screenshot descriptions (alt text) in:
  - `README.md`
  - `docs/MANUAL_ES.md`
  - `docs/MANUAL_EN.md`

## 0.2.23 - 2026-03-09

- Replaced plugin screenshots with assets from `screenshots_smartnotices`.
- Renamed documentation screenshots to stable names:
  - `smartnotices_doc_01.png` ... `smartnotices_doc_10.png`
- Updated screenshot references in:
  - `README.md`
  - `docs/MANUAL_ES.md`
  - `docs/MANUAL_EN.md`

## 0.2.22 - 2026-03-09

- Modal rendering enhanced for simultaneous active notices:
  - Multiple active notices now render in a queue (shown one after another in the same page load).
  - Updated modal JS init to accept and process multiple modal ids.
  - Modal template now initializes hidden state and is revealed by JS queue controller.
- Keeps existing tracking flow (`impression`, `close`, `confirm`) for each rendered notice.

## 0.2.21 - 2026-03-09

- Coexistence hardening for parallel install with Lite plugin:
  - Added shared modal render guard (`$GLOBALS['smartnoticespro_any_modal_rendered']`) to prevent dual modal rendering on the same page request.
- No functional feature removals; plugin keeps full behavior.

## 0.2.20 - 2026-03-07

- Report UI improvement:
  - Replaced raw page URL display with user-friendly page names in report table.
  - Applied same page-name mapping in CSV export.
- Added page label formatter for report rows:
  - Login page
  - Front page
  - Dashboard
  - My courses
  - Course page (includes course name when available)
- Updated report column label from `Page URL` / `URL de página` to `Page` / `Página`.

## 0.2.19 - 2026-03-07

- Removed default preselection of `Course page` location in add notice form (global flow).
- Adjusted location validation:
  - no location selection is accepted while `scope = course` (location is normalized server-side to course page).

## 0.2.18 - 2026-03-07

- Updated global notice form behavior for `Target role`:
  - No default selection when scope is `global`.
  - Added placeholder option: `Select a target role`.
  - Added validation requiring target role selection for global notices.
- Added new language strings (EN/ES) for target-role placeholder and required validation message.

## 0.2.17 - 2026-03-07

- Course-specific auto behavior in global add/edit flow:
  - If scope is `course`, notice is automatically normalized to:
    - location: `course page` only
    - target role: `all users`
- Updated form UX for global flow:
  - `Target role` is hidden when scope is `course`.
  - Non-course locations are hidden when scope is `course`.
- Report page toolbar improved:
  - `Back` button moved to same row as `Export CSV`.
  - `Back` uses native Moodle button style (`btn btn-secondary`).
- Added screenshot staging folder for documentation work:
  - `screenshots/docs_pending/` with `.gitkeep`
- Updated README and manuals (EN/ES) with screenshot folder and filename conventions.

## 0.2.16 - 2026-03-07

- Added per-user confirmation persistence for notice visibility:
  - If `Enable confirmation button` is active and user clicks `Confirm`, that notice is no longer shown to that user.
  - If confirmation is disabled, notice continues displaying according to normal rules (status/date/location/role/course/group).
- Implemented filtering based on `local_smartnoticespro_log` `confirm` events in active notice resolution.

## 0.2.15 - 2026-03-07

- Expanded documentation with complete usage manuals:
  - Updated `docs/MANUAL_ES.md` to full-feature manual.
  - Updated `docs/MANUAL_EN.md` to full-feature manual.
- Updated `README.md` documentation section and added recommended screenshot checklist for:
  - GitHub repository documentation
  - Moodle Plugins Directory listing

## 0.2.14 - 2026-03-07

- Switched toolbar/action buttons to native Moodle/Boost button styles:
  - Removed custom premium button class usage in listing and reports.
  - Removed custom button-shadow CSS for better visual integration.
- Added safe table sorting (ASC/DESC) in notices listing:
  - Sort parameters: `sort` + `dir`.
  - Sortable headers:
    - Notice ID
    - Title
    - Status
    - Dates (start date)
  - Sorting persists while paginating and navigating course/global listings.

## 0.2.13 - 2026-03-07

- Updated teacher course notice form:
  - `Grupo objetivo` now includes `Todos los grupos` / `All groups`.
  - Group selection is optional when groups exist in the course.
- Updated course notices listing toolbar:
  - Added `Ir al curso` / `Go to course` button next to `Agregar aviso`.
- Improved table integration with Moodle/Boost styles:
  - Listing and report tables now use Moodle table classes (`table`, `table-striped`, `table-hover`).
- Improved group column display:
  - Course notices without a specific group now show `Todos los grupos` / `All groups`.

## 0.2.12 - 2026-03-07

- Added dedicated teacher page for course notices:
  - New file: `/local/smartnoticespro/course_edit.php`
  - Teacher add/edit flow is isolated from global edit page.
  - Course notice flow enforces:
    - course scope only
    - course location only
    - role target `all`
    - optional group target when course groups exist
- Updated course listing routes:
  - `Agregar aviso` (course context) now opens `course_edit.php`.
  - `Editar` from course notices now opens `course_edit.php`.
- After saving/canceling course notice edit, user always returns to:
  - `/local/smartnoticespro/index.php?courseid={id}`

## 0.2.11 - 2026-03-07

- Improved teacher notice creation in course context:
  - Added group targeting support (`groupid`) for course notices.
  - Teacher flow now targets group members when groups exist.
  - Added “Group” column in listing.
- Updated wording across plugin:
  - `Hide title in modal` -> `Hide title`
- Added DB upgrade for `groupid` field and related key/index.

## 0.2.10 - 2026-03-07

- Removed “What activity is next?” feature and all active code paths:
  - Removed course button and configuration page.
  - Removed dynamic auto-notice rendering logic.
  - Removed related language strings and README mention.
  - Added DB cleanup upgrade to drop `autonextactivity` field.

## 0.2.9 - 2026-03-07

- Fixed auto next-activity engine database compatibility:
  - Replaced dependency on non-portable `calendar_event` table with `assign.duedate` query.
  - Prevents `dmlreadexception` on sites without that table.

## 0.2.8 - 2026-03-07

- Fixed auto next-activity notice visibility for students in course context.
- Auto notice now has explicit priority in course rendering order.
- Improved auto-notice role filtering:
  - visible to enrolled students
  - hidden for teachers/managers/admins

## 0.2.7 - 2026-03-07

- Added automatic course-only notice mode: “What activity is next?”
- Added course configuration button and page:
  - `/local/smartnoticespro/auto.php`
- Added simple recommendation engine:
  - Prioritizes upcoming assignment due events.
  - Falls back to next incomplete visible activity.
- Added direct activity link in automatic notice message.
- Added DB field `autonextactivity` with upgrade step.
- Automatic notice uses existing metrics and interaction logging pipeline.

## 0.2.6 - 2026-03-07

- Fixed report/export fatal error on user interaction logs:
  - Removed dependency on `get_all_user_name_fields()` in log query.
  - Now selects explicit user fields (`firstname`, `lastname`, `email`) for compatibility.

## 0.2.5 - 2026-03-07

- Fixed XMLDB collision in `local_smartnoticespro_log` table definition:
  - Removed redundant indexes on foreign key fields (`noticeid`, `userid`) to avoid key/index name collisions during upgrade.

## 0.2.4 - 2026-03-07

- Added notice option in edit form to enable/disable confirmation button.
- Added interaction log table for per-user event tracking with timestamp:
  - impression
  - close
  - confirm
- Added report page per notice with pagination and export icon:
  - CSV export (Excel-compatible)
- Added new actions in listing:
  - report view icon
  - export CSV icon
- Extended metrics tracking integration to register user actions in logs.

## 0.2.3 - 2026-03-07

- Added listing pagination (20 records per page).
- Added analytics metrics per notice in listing:
  - impressions
  - closes
  - confirmations
  - CTR (`confirmations / impressions`).
- Added modal interaction tracking endpoint:
  - `/local/smartnoticespro/track.php`
- Added modal confirmation button (`Got it` / `Entendido`) with tracking.
- Added database fields for metrics with upgrade step.
- Improved badge rendering robustness for scope, role, and status columns.

## 0.2.2 - 2026-03-07

- Added unique visible notice ID column in listing (format `SN-000123`).
- Upgraded listing UI:
  - Primary styled “Add notice” button with icon and improved spacing.
  - Badge-style display for scope, target role, and status.

## 0.2.1 - 2026-03-07

- Added screenshots folder to the plugin package and documentation references.
- Updated repository URL to:
  - `https://github.com/antoniomexdf-boop/moodle-local_smartnoticespro`
- Added screenshot sections to:
  - `README.md`
  - `docs/MANUAL_EN.md`
  - `docs/MANUAL_ES.md`

## 0.2.0 - 2026-03-07

- Renamed product display name from `Smart Notices for Moodle` to `Smart Notices` across plugin strings and documentation.

## 0.1.9 - 2026-03-06

- Added English quick manual:
  - `docs/MANUAL_EN.md`
- Updated README documentation section to include EN + ES manuals.

## 0.1.8 - 2026-03-06

- Added GitHub documentation manual in Spanish:
  - `docs/MANUAL_ES.md`
- Included manager and teacher usage flows in the manual.
- Linked manual from README.

## 0.1.7 - 2026-03-06

- Updated edit redirect flow:
  - After saving an edited notice, users with global management access are redirected to the main plugin page.
  - Course-only teachers keep course-specific fallback redirect to avoid permission errors.

## 0.1.6 - 2026-03-06

- Updated author full name across code headers and documentation:
  - Jesus Antonio Jimenez Aviña

## 0.1.5 - 2026-03-06

- Prepared plugin metadata for GitHub and Moodle Plugins Directory publication.
- Added maintainer and contact details in source headers and documentation.
- Added `AUTHORS.md` with author emails and GitHub URL.
- Updated release metadata to stable and marked support range for Moodle 4.5.

## 0.1.4 - 2026-03-06

- Fixed role labels to use actual Moodle role names from configured archetype roles.
- Updated Spanish label for dashboard location to `Área personal`.

## 0.1.3 - 2026-03-06

- Moved the \"Add notice\" button into page content under the heading/menu area.
- Added \"Hide title\" option for modal notices.
- Added \"My courses\" as a new display location (`/my/courses.php`).
- Switched role labels to Moodle archetype names (Student, Teacher, Manager).
- Added database upgrade step to create `hidetitle` field for existing installations.

## 0.1.2 - 2026-03-06

- Fixed role targeting logic using real role assignments/capabilities for student/teacher/manager checks.
- Updated course-management screens to use a standalone plugin page context (not embedded in course layout).
- Updated modal template to render visible by default and keep JS close/escape behavior.

## 0.1.1 - 2026-03-06

- Fixed modal rendering reliability by adding a top-of-body callback fallback.
- Improved page location detection (`login`, `frontpage`, `dashboard`, `course`) using pagetype and URL path.
- Prevented duplicate modal injection when both callbacks run.

## 0.1.0 - 2026-03-06

- Initial MVP release.
- Added notice database schema and capabilities.
- Added admin and teacher CRUD interfaces.
- Added role/location/date visibility rules.
- Added Mustache modal + AMD auto-open behavior.
- Added English and Spanish translations.
