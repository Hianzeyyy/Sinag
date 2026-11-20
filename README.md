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

The local `public` and `local` disks remain the development defaults. For durable
S3-compatible production storage, configure the following environment values on
the web service (and keep credentials out of source control):

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

Public profile and suggestion images use `public_s3`; verification images, report
evidence, and password-reset ID images use `private_s3`. Private images continue
to be served through authorization-checked application routes. Existing files on
the local disks remain readable when those disks are available; configure S3
before switching uploads so new images are saved durably.

Keep the bucket private by default. If suggestion attachments need direct public
URLs, configure a bucket policy or public custom domain limited to the
`profile-photos/` and `suggestions/` prefixes; do not make verification,
`evidence/`, or `password-reset-requests/` objects public.

After setting the S3 environment variables, migrate existing local uploads with:

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
configured S3-compatible bucket. PDF and other non-image report evidence is not
included. The command is manual and does not run on a schedule.

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
