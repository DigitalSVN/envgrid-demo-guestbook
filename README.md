# EnvGrid Demo: Guestbook

**This is a live [EnvGrid](https://www.envgrid.com) demo: every pull request gets its own preview URL.**

<!-- TODO: add the "Previewed with EnvGrid" badge here once it ships. -->

A deliberately tiny Laravel app: one page with a "sign the guestbook" form that writes a name and a
message to SQLite and lists the latest entries underneath. It exists to show EnvGrid working on a real,
running Laravel app, with migrations, form posts and a database, not a static export.

## See it in action

No setup needed:

1. Browse the [open pull requests](https://github.com/DigitalSVN/envgrid-demo-guestbook/pulls).
2. Open any of them and find EnvGrid's comment with the preview link.
3. Click the link and sign the guestbook in that PR's live preview.

Each preview gets its **own throwaway SQLite database**, so entries signed in one PR's preview never
appear in another's or on `main`.

## Try it on your own copy

To watch EnvGrid build a preview for a change *you* make, run it on your own fork. EnvGrid builds
pull requests within the repository it's installed on, so PRs opened from a fork against this repo
won't get a preview.

1. [Fork this repository](https://github.com/DigitalSVN/envgrid-demo-guestbook/fork).
2. Sign up at [envgrid.com](https://www.envgrid.com) and install the EnvGrid GitHub App on your
   fork. No other configuration is needed (see [How EnvGrid boots it](#how-envgrid-boots-it)).
3. In your fork, create a branch that makes a visible change. The quickest is recolouring the header:
   change `--header-bg` at the top of the `<style>` block in
   [`resources/views/guestbook.blade.php`](resources/views/guestbook.blade.php).
4. Open a pull request **within your fork**. On GitHub's "Open a pull request" page, set the base
   repository to *your fork* (it defaults to this upstream repo).
5. EnvGrid comments on the PR with a preview link once the environment is live. Open it and sign the
   guestbook.

## Stack

| | |
|---|---|
| PHP | 8.5 |
| Laravel | 13 |
| Database | SQLite |
| Frontend | Plain Blade + inline CSS (no Node / Vite build step) |

The whole app is:

- `database/migrations/*_create_guestbook_entries_table.php`: the one table
- `app/Models/GuestbookEntry.php`: the model, plus a `recent()` scope (newest 50 entries)
- `app/Http/Controllers/GuestbookController.php`: `index` (list + form) and `store` (sign)
- `app/Http/Requests/StoreGuestbookEntryRequest.php`: validation (name ≤ 100 chars, message ≤ 1000)
- `resources/views/guestbook.blade.php`: the single page

Signing is rate-limited to 10 posts per minute per visitor.

## How EnvGrid boots it

No per-repo EnvGrid configuration is needed. The default bootstrap covers everything:

1. `composer install --no-dev --optimize-autoloader`
2. *(frontend build skipped automatically, since there's no `vite.config.*`)*
3. create `database/database.sqlite`
4. `php artisan migrate --force`

Previews run with `APP_ENV=production`, `APP_DEBUG=false`, `DB_CONNECTION=sqlite`,
`SESSION_DRIVER=cookie` and `CACHE_STORE=file`, and EnvGrid provisions `APP_KEY` as a secret. Don't
commit an `.env` file or an app key.

## Running locally

Requires PHP 8.5 with the `pdo_sqlite` extension, and Composer. Dependency resolution is pinned to
PHP 8.5 via `config.platform.php` in `composer.json`, so `composer update` never pulls in a package
that EnvGrid's PHP 8.5 preview image can't run, even on a newer local PHP.

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan serve
```

Optionally add a few sample entries with `php artisan db:seed`.

## Seeding previews

To give each preview a few starter entries, add `php artisan db:seed --force` as a custom bootstrap
command in the EnvGrid dashboard. The seeder writes fixed entries rather than using model factories,
because previews install with `--no-dev`, so Faker (a dev dependency) isn't available. It's also safe
to run more than once. The `preview-boot` CI job runs these steps (no-dev install, migrate, seed) on
every PR.

### Tests

```bash
php artisan test
```

To check the app the way a preview runs it (production mode), set `APP_ENV=production` and
`APP_DEBUG=false` in `.env` and reload the page. A boot error then shows up as a bare 500 instead of
a stack trace.

## License

[MIT](LICENSE)
