# Smart Notices Pro (`local_smartnoticespro`)

Smart Notices Pro is a local plugin to publish targeted modal announcements across key Moodle locations.

## Repository

- GitHub: https://github.com/antoniomexdf-boop/moodle-local_smartnoticespro
- Suggested repository short description:
  - `Modal announcements for Moodle with role/location/date targeting (Moodle 4.5+).`

## Author and Contact

- Author: Jesus Antonio Jimenez Aviña
- Email: antoniomexdf@gmail.com
- Email (secondary): antoniojamx@gmail.com

## Documentation

- Complete manual (EN): `docs/MANUAL_EN.md`

## Continuous Integration

- GitHub Actions workflow included at `.github/workflows/ci.yml`
- Runs `moodle-plugin-ci` checks for Moodle `MOODLE_405_STABLE`
- Covers `pgsql` and `mariadb` with PHP `8.1` and `8.2`

## Screenshots

![Main notices listing with actions and status badges](screenshots/smartnoticespro_01.png)
![Add global notice form with scope, locations and role target](screenshots/smartnoticespro_02.png)
![Course notice form with target group selection](screenshots/smartnoticespro_03.png)
![Modal preview with title and content](screenshots/smartnoticespro_04.png)
![Modal with confirmation button enabled](screenshots/smartnoticespro_05.png)
![Interaction report table per notice](screenshots/smartnoticespro_06.png)
![CSV export action from report page](screenshots/smartnoticespro_07.png)
![Sorted listing example with table controls](screenshots/smartnoticespro_08.png)

Recommended additional screenshots for GitHub and Moodle plugins page:

1. Course add/edit form with `Target group` (`All groups` and specific group).
2. Course toolbar with `Add notice` + `Go to course`.
3. Notice report page (interaction table).
4. CSV export example opened in spreadsheet software.
5. Listing sorted ascending and descending.

## Features

- CRUD for notices.
- Global notices managed by administrators.
- Course-specific notices managed by teachers (inside their own course only).
- Teacher course notices can target a specific course group (when groups exist).
- Display by location:
  - Login page
  - Front page
  - Área personal (`/my`)
  - My courses (`/my/courses.php`)
  - Course view (`/course/view.php`)
- Target audience by role:
  - All users
  - Student
  - Teacher
  - Manager
- Active/inactive status.
- Optional hidden modal title.
- Optional confirmation button per notice.
- When confirmation is enabled, confirmed notices stop showing for that user.
- Start/end visibility dates.
- Paginated notice listing.
- Built-in metrics per notice: impressions, closes, confirmations, and CTR.
- Per-notice interaction report with CSV export (Excel-compatible).
- Accessible modal rendering with Mustache + AMD.
- English language pack.

## Requirements

- Moodle 4.5+
- PHP 8.1+

## Installation

1. Copy folder `smartnoticespro` to `local/smartnoticespro`.
2. Go to **Site administration > Notifications**.
3. Complete the upgrade.
4. Purge caches after upgrade.

## Usage

- Global notices (admin/manager):
  - **Site administration > Plugins > Local plugins > Manage Smart Notices Pro**
- Course notices (teacher with capability):
  - Open a course, then **Course notices**.

## Capabilities

- `local/smartnoticespro:manageglobalnotices`
- `local/smartnoticespro:managecoursenotices`
- `local/smartnoticespro:viewnotices`

## Moodle Plugins Directory Notes

- License: GPL v3 or later.
- Component: `local_smartnoticespro`.
- Plugin type: `local`.
- Requires: Moodle 4.5+ and PHP 8.1+.
- Includes:
  - `version.php`
  - `settings.php`
  - `db/access.php`
  - `db/install.xml`
  - `db/upgrade.php`
  - `classes/`, `forms/`, `templates/`, `amd/`, `lang/`, `pix/`
  - `README.md`, `CHANGELOG.md`, `LICENSE`, `.gitignore`

## License

GNU GPL v3 or later.
