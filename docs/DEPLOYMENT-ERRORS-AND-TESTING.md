# Deployment Error History and Testing

## Scope and evidence

The repository history begins with project uploads on October 7, 2026. Deployment
work appears on October 9, and the first Render-specific production fixes appear
on October 10. This review covers the Git history through `2eec444` (October 18,
2026), checked on October 19, 2026.

This is a history of issues and corrections evidenced by commits, not a complete
production incident log. The repository does not establish the exact date of the
first live deployment, prove that every change followed a user-reported incident,
or confirm that each correction was verified on the live service. The items below
are therefore described as corrected in code history; production verification
should be confirmed separately.

## Errors and fixes identified

| Area | Issue evidenced by the history | Correction recorded in Git |
|---|---|---|
| Deployment build and web server | Compiled frontend assets were missing from deployment, HTTPS URLs did not account for the Render proxy, Render's Apache port configuration was incorrect, and Apache could fail when multiple MPMs started. | Included compiled assets, corrected proxy-aware HTTPS and port handling, and fixed Apache startup (`1b31972`, `4aae9ac`, `77c41a6`, `7f0bc54`). |
| Production database connectivity | Render/TiDB configuration could select localhost or an invalid connection, fail to parse a database URL or explicit environment settings, or fail when TiDB TLS/CA settings were absent or invalid. | Normalized Render database settings and defaults, supported database URLs and explicit TiDB settings, and hardened TLS/CA handling (`49c5e90`, `80abe61`, `1eb2b70`, `299d795`, `c90afef`, `1455483`, `b22332a`, `20b4370`, `f51e7b4`, `5c389fd`, `eee248b`, `44dd536`). |
| Startup, sessions, and deployment migrations | Login/presence could be affected by stale sessions or production session/cache defaults. Migration behavior was changed during deployment troubleshooting; a later fix repairs the legacy migration table and ensures deployment fails visibly if migrations fail. | Added portable/file-based session and cache handling and session refreshes; revised startup migration behavior; repaired the migration table and made migration failures stop deployment (`5ce7b8b`, `3f02e27`, `433cad9`, `b0cc505`, `fadb319`, `2ecd365`, `b63ba07`, `47886cc`, `00c81ec`, `9f7d878`). |
| Production schema and database IDs | Production database ID columns and legacy migration records did not always match the expected schema. TiDB inserts for users, reports, and suggestions could fail when relying on implicit IDs. Text-only suggestions could also encounter a missing image column. | Repaired production ID columns and legacy migration records, used explicit IDs for affected inserts, and allowed text suggestions with older schemas (`dd3e33a`, `906527d`, `61a170a`, `6f67277`, `fb903e2`, `00c81ec`, `47886cc`, `c04d26e`, `7483d60`). |
| Registration and account setup | Registration uploads and signup could fail; account approval responses and support-account availability needed correction. | Fixed registration uploads and signup handling, assigned TiDB user IDs, corrected approval responses, and bootstrapped production support accounts (`ad58f9e`, `1edc6dc`, `ece1951`, `906527d`, `61a170a`, `115dd2e`, `4d126b1`, `4554d92`). |
| Reports and schedules | Report submission encountered production schema differences, JSON/ID insert issues, fragile reloads, profile-write side effects, and exceptions that could surface as server errors. Schedule editing/synchronization and appointment/report actions also needed reliability and stale-session handling. | Hardened schema-safe report writes, validation, retries, error reporting, synchronization, and schedule updates; handled incomplete profiles and protected submission routes (`3fda330`, `93ea397`, `878a1b3`, `3b759fe`, `57114fa`, `41f771a`, `b90ff2b`, `5f4c2d5`, `aafbe3e`, `1907058`, `6fe34b3`, `dfab14f`, `5c966bd`, `516beb7`, `23837d6`, `c81acc4`, `b593d5b`, `d322098`, `0d766b0`, `f71c00a`, `ed9c558`, `8d51c53`, `1661990`, `4da68ed`, `157e80f`). |
| GAD report exports | PDF export could fail or produce errors with incomplete report data; PDF controls/fallbacks and PDF/Excel export behavior also required repair. | Made PDF rendering resilient to incomplete data, removed broken/unused controls, and restored/streamlined export behavior (`0681961`, `c6d25d0`, `e998f21`, `3422f36`, `1d224f5`, `26ae6be`, `56f34fe`, `ede8cf1`, `e26b678`). |
| Messaging, admin actions, and suggestions | Message sending/access, stale action buttons, admin authorization/deletion, suggestion actions/likes, and visibility of unapproved suggestions needed correction. Unread dots also needed to reflect unread state. | Corrected messaging and admin actions, supported message actions without optional history columns, fixed suggestion controls/likes and approval visibility, and limited unread dots to unread conversations (`4489e74`, `f1bc9d7`, `9fb4eeb`, `b4cca32`, `2520225`, `743e6ca`, `8c39474`, `fd9b059`, `e8295fa`, `ba7e39f`, `7f7f9b4`, `75dbee3`, `84b1d3d`, `2eec444`). |
| Responsive layout and missing images | Mobile/tablet headers, dashboard layouts, chat, and the schedule calendar had layout/overflow issues; image references or absent photos could leave assets/avatars missing. | Adjusted responsive layouts and image references, added avatar fallbacks, and allowed users to restore profile and ID images (`dc8bc10`, `d940086`, `a3ca949`, `9de1014`, `2548756`, `486c10a`, `93d7b36`, `d763ed6`, `7c8d5f6`, `61d7627`, `73f9795`, `7076747`, `bd21eb1`, `64b344b`, `075a950`). |

Branding, naming, and other feature additions are not listed as errors unless the
commit specifically records a correction. Commit references identify the source
history and should not be interpreted as proof of a successful production rollout.

## Button and action issues

The commit history does identify several button/action problems and related
corrections:

| User-facing action | Issue evidenced by history | Correction recorded |
|---|---|---|
| Suggestion and comment actions | Suggestion routes were behind a completed-profile middleware gate, which could block the suggestion page's create, upvote, edit, delete, comment, and report actions. Some suggestion actions also needed explicit IDs for TiDB inserts. | Removed the profile-completion gate from those routes and changed TiDB inserts for suggestions, upvotes, comments, and reports (`743e6ca`, `8c39474`, `fd9b059`). |
| Admin suggestion delete | The admin delete control used the wrong route for its action. | Added an admin delete handler/route and pointed the form at that route (`2520225`). |
| Suggestion like/upvote | Boses suggestion likes needed a correction. | Fixed the like behavior (`75dbee3`). The repository history identifies the fix, but does not document a confirmed production retest. |
| Admin messaging and password reset | Stale authentication and messaging controls referenced routes/actions that did not match the current flows. | Updated the relevant password and messaging actions (`b4cca32`, `9fb4eeb`). |
| Appointment, report, and schedule actions | Submission, editing, or synchronization paths could fail with production schema/session differences or return server errors. | Hardened report/schedule handling and made schedule edits persist reliably (`3fda330`, `dfab14f`, `516beb7`, `1661990`, `4da68ed`). |
| Message edit/delete and unread indicators | Message actions needed to work with databases lacking optional history columns, and unread indicators could be inaccurate. | Added compatibility for message actions without those columns and corrected unread-state behavior (`e8295fa`, `ba7e39f`, `7f7f9b4`). |

These are action-level fixes found in commit history, not a complete inventory of
every button ever tested. The feature tests include coverage for suggestion
upvotes, message editing/deletion, appointment deletion, schedule persistence,
and report submission. They do not establish that every visible button was
manually exercised in each deployed release. The current automated test result
is recorded in Integration Testing below.

## Integration Testing

Integration testing checks that application components work together across
important user flows. The Laravel feature suite exercises report submission,
appointment formatting, schedule synchronization, messaging, suggestion review
and visibility, registration, image access, and GAD report export. Unit tests
also cover content moderation and suggestion status.

**Automated test run (October 19, 2026):** `php artisan test` completed with 45
tests passing and 1 failing (224 assertions). The failure is
`RegistrationNameTest::test_registration_form_has_required_first_and_last_names_and_optional_middle_name`:
the rendered form did not match the test's expected optional-middle-name label
markup. This needs investigation; the result is not an all-green test run.

These tests use an in-memory SQLite database, so they do not by themselves verify
production TiDB connectivity, Render startup/migrations, or live deployment
behavior.

## Alpha Testing

**Status: No completed alpha-test record was found in the repository.**

Alpha testing should be performed by the project team in a controlled
pre-release environment. Check core flows for students and administrators:
registration and approval, sign-in, reports, appointments and schedules,
messaging, suggestions and moderation, image access, and PDF/Excel exports.
Record the build/commit, environment, date, testers, cases run, defects, and
retest outcomes before marking alpha testing complete.

## Beta Testing

**Status: No completed beta-test record was found in the repository.**

Beta testing should invite a representative group of intended users to try the
release in a production-like environment and report usability or workflow
problems. Capture the release/build, dates, participant count, feedback,
severity and status of reported defects, and whether fixes were retested. Do not
record personal or sensitive case details in this report.

## Pilot Testing

**Status: No completed pilot-test record was found in the repository.**

Pilot testing should be a limited rollout of the release to a defined group
before wider deployment. Monitor availability, sign-in, database and migration
health, data persistence, uploads, and the main student/admin workflows. Record
the pilot group and dates, success criteria, issues and resolutions, rollback
decision (if any), and approval to proceed.

## Evidence needed to complete testing records

For each Alpha, Beta, and Pilot test, add the actual date and release identifier,
scope and environment, participants or group size, test cases and outcomes,
defects found, fix/retest references, and reviewer approval. Until those records
exist, these sections describe test scope and status, not completed user testing.

## Cold-start latency and keep-alive

For production performance evaluation, prefer an always-on application instance
and persistent cache/session services. If the hosting plan can scale an idle web
service to zero, configure an uptime monitor to make an HTTPS `GET` request to
the app health endpoint (`/up`) every five minutes, subject to the provider's
idle and monitoring policies. Treat polling as mitigation, not a latency
guarantee: record first-request and steady-state response times separately and
use an always-on plan if the cold-start target is not met.

If incident evidence was previously stored on the public disk, migrate those
files and their report paths to private storage before rollout, then remove the
public copies. New report uploads are stored privately; existing files are not
moved automatically.
