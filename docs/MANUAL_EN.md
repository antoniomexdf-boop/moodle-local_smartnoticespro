# Complete User Manual

This document covers all currently available features in **Smart Notices Pro** (`local_smartnoticespro`).

## 1. What Smart Notices Pro does

Smart Notices Pro displays modal announcements segmented by:

- Location
- Target role
- Course and group (for course notices)
- Start/end date range
- Active/inactive status

It also includes per-notice metrics, interaction reports, and CSV export.

## 2. Requirements

- Moodle 4.5+
- PHP 8.1+

## 3. Roles and capabilities

Plugin capabilities:

- `local/smartnoticespro:manageglobalnotices`
- `local/smartnoticespro:managecoursenotices`
- `local/smartnoticespro:viewnotices`

### Manager / Administrator

With `manageglobalnotices`, users can:

- Create/edit/delete global notices.
- Create/edit/delete course-specific notices from the main listing.
- Set target role, locations, and dates.
- View metrics and reports.
- Export CSV.

Main path:

- `Site administration > Plugins > Local plugins > Manage Smart Notices Pro`

### Teacher

With `managecoursenotices`, users can:

- Manage notices only for their own course.
- Create/edit notices in a dedicated course page.
- Set `Target group`:
  - `All groups`
  - Specific group
- View metrics/reports for course notices.

Main path:

- Inside course: `Course notices`

## 4. Available locations

- Login page (`/login/index.php`)
- Front page (`/index.php`)
- Dashboard (`/my`)
- My courses (`/my/courses.php`)
- Course page (`/course/view.php`)

## 5. Create a notice (step-by-step)

1. Open notice listing.
2. Click `Add notice`.
3. Fill:
   - `Title`
   - `Message`
   - `Hide title` (optional)
   - `Enable confirmation button` (optional)
   - `Active`
   - `Scope`
   - `Target role` (global flow)
   - `Target group` (course flow, when groups exist)
   - Locations
   - Start/end date
4. Save.

## 6. Modal display rules

A notice is shown only when all conditions match:

- Active status is enabled.
- Current date/time is within range.
- Current page location matches.
- User matches target role.
- If course notice: user is in that course.
- If target group is set: user belongs to that group.
- If `Enable confirmation button` is active:
  - Notice stops showing to that user after `Confirm`.
  - If user does not confirm, it keeps appearing.

## 7. Notice listing features

Listing includes:

- Visible unique ID (`SN-000123`)
- Title, scope, course, group
- Locations, role, status, dates
- Metrics:
  - Impressions
  - Closes
  - Confirmations
  - CTR
- Actions:
  - Edit
  - Delete
  - View report
  - Export CSV

UX functions:

- Pagination
- Asc/desc sorting by key columns (ID, title, status, dates)
- Course context toolbar button: `Go to course`

## 8. Metrics and reports

Tracked events per notice:

- `impression` (modal shown)
- `close` (modal dismissed)
- `confirm` (confirmation button clicked)

Per-notice report fields:

- User
- Email
- Action
- Course ID
- Page URL
- Date/time

Export:

- Excel-compatible CSV.

## 9. Best practices

- Keep titles short and clear.
- Avoid notices without end date.
- Use group segmentation for course-specific messages.
- Test with real users for each role.
- Purge caches after plugin update.

## 10. Troubleshooting

If notice modal is not visible:

1. Check `Active`.
2. Check start/end date.
3. Check location settings.
4. Check target role.
5. For course notice, verify course context.
6. For group notice, verify group membership.
7. Purge caches:
   - `Site administration > Development > Purge all caches`

If report is empty:

1. Verify capabilities.
2. Confirm users have interacted with the notice.

## 11. Suggested screenshots for docs

Recommended screenshots for GitHub and Moodle Plugins Directory:

1. Main listing with metrics and actions.
2. Global add/edit form.
3. Course add/edit form with `Target group`.
4. Login modal.
5. Dashboard modal.
6. Course modal.
7. Interaction report page.
8. CSV export (report page + spreadsheet opened).
9. Asc/desc sorting in listing.
10. `Add notice` + `Go to course` toolbar in course context.

## 12. Current screenshots included

![Main notices listing with actions](../screenshots/smartnoticespro_01.png)
![Global notice creation form](../screenshots/smartnoticespro_02.png)
![Course notice form with target group](../screenshots/smartnoticespro_03.png)
![Notice modal display](../screenshots/smartnoticespro_04.png)
![Modal with confirmation button enabled](../screenshots/smartnoticespro_05.png)
![Per-notice interaction report](../screenshots/smartnoticespro_06.png)
![CSV export action from report](../screenshots/smartnoticespro_07.png)
![Listing with sorting applied](../screenshots/smartnoticespro_08.png)
