<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Sinag
# Sinag
# system
# Sinag

## Administrator provisioning

Migrations and seeders do not contain a default password. To provision the
administrator automatically on both localhost and Render, set the following
values in local `.env` and in Render's Environment Variables:



Docker startup runs migrations and then provisions or reactivates this account
without requiring a Render shell. The password is read only from the environment
and is never stored in source code.

For a manual local setup, provision an administrator interactively with:

```text
php artisan app:provision-admin admin@psu.edu.ph
```

The command can also promote an existing user after confirmation. Use a password
manager to generate the password; do not commit credentials.

## Render PostgreSQL connection

The Docker image supports both MySQL/TiDB and PostgreSQL. To use a Render
PostgreSQL database, set `DB_CONNECTION=pgsql` and connect the web service to
the database using Render's internal database URL or its internal host, port,
database name, username, and password. Keep the URL or password in Render
environment secrets; do not commit them. For the supplied database, the
database name is `siinag` and the internal host is
`dpg-dbo7iqvf3r2c73ad3l00-a`.

Changing the connection does not copy existing records. Back up the current
database and migrate its data into PostgreSQL before directing production
traffic to the new database. After import and before enabling writes, run
`php artisan app:sync-postgres-sequences` from a shell connected to the new
database to position generated IDs above imported records.

## Uploaded media storage and backups

All new user-uploaded media is stored in Cloudinary, not on the application
container. Set `CLOUDINARY_URL` in local `.env` and Render's Environment
Variables; keep its value private and out of source control.

Profile photos and suggestion attachments are public Cloudinary images.
Registration ID/selfie photos, password-reset ID pictures, and report evidence
use authenticated delivery and continue to be served through the application's
authorized routes. The database stores a compact `cloudinary://` reference with
the delivery type, resource type, and public ID; credentials are never stored in
media records. Existing local and S3 paths continue to work until migrated.

To migrate existing local or S3-backed files, first configure the source storage
disks that contain them. Check that the files are readable without changing
records:

```sh
php artisan media:migrate-to-cloudinary --dry-run
```

Then upload and update references:

```sh
php artisan media:migrate-to-cloudinary
```

The migration handles profile photos, registration verification images,
password-reset ID pictures, suggestion attachments, and report evidence. It
does not delete source files; verify that migrated media displays correctly
before cleaning up old storage.

The S3-compatible disks remain available for reading legacy files and storing
manual ZIP backups. Configure these values in Render only when using those
features; keep credentials out of source control:

```text
USER_PUBLIC_MEDIA_DISK=public_s3
USER_PRIVATE_MEDIA_DISK=private_s3
MEDIA_BACKUP_DRIVER=s3
AWS_ACCESS_KEY_ID=...
AWS_SECRET_ACCESS_KEY=...
AWS_DEFAULT_REGION=...
AWS_BUCKET=...
AWS_ENDPOINT=...       # S3-compatible endpoint, when required
AWS_URL=...            # Public bucket/CDN URL for public image attachments
AWS_USE_PATH_STYLE_ENDPOINT=false
```

Set `DB_SSL_CA` to a readable CA bundle for local remote-TiDB connections. The
Render Docker image installs system CA certificates and configures TiDB TLS
verification automatically.

For legacy deployments that used the S3 sync command, existing local uploads can
still be copied to the configured S3-compatible disks with:

```sh
php artisan media:sync-images-to-storage
```

The command copies existing user images to the matching S3 public/private disk
without overwriting files already there. It skips non-image attachments. Run it
while the old local storage is still available.

Create a manual ZIP archive containing uploaded images (including profile photos,
verification images, suggestion images, and image evidence) with:

```sh
php artisan media:backup-images
```

The archive is stored privately under the `media-backups/` prefix in the
configured S3-compatible bucket and can include Cloudinary-backed images. PDF
and other non-image report evidence is not included. The command is manual and
does not run on a schedule.

## One-time account removal migration

The migration `2026_11_20_000003_delete_configured_user` removes only the
non-admin account whose email matches `USER_EMAIL_TO_DELETE`, including its
database-cascaded records and associated uploaded media. Set this variable in
the production environment before deploying, then remove it after the migration
has run. The target email is intentionally not stored in the repository. The
migration cannot be rolled back to restore deleted data.

Render can alternatively use an attached persistent disk mounted at
`/var/www/html/storage/app`; retain the default storage paths or set
`PRIVATE_STORAGE_PATH=/var/www/html/storage/app/private` and
`PUBLIC_STORAGE_PATH=/var/www/html/storage/app/public`. Before mounting over an
existing storage directory, copy any surviving files out because mounting hides
the container's prior files.
# sinag-project

## Deployment history and testing

See [Deployment error history and testing](./docs/DEPLOYMENT-ERRORS-AND-TESTING.md)
for the deployment-era fixes identified in Git history, current automated test
results, and the Integration, Alpha, Beta, and Pilot Testing sections.
# sinag-project
# sinag-project
