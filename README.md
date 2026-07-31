# JustBetter Statamic Starter Kit

This is a Statamic starter kit that is used to bootstrap new Statamic projects.

## Installation

Through Statamic CLI:

```bash
statamic new my-site justbetter/statamic-starter-kit
```

Or call through a command:

```php
$this->call('statamic:starter-kit:install', [
    'package' => 'justbetter/statamic-starter-kit',
]);
```

## Always included

New projects always receive this stack:

- `statamic/seo-pro`
- `statamic/eloquent-driver`
- `justbetter/statamic-detour`
- `justbetter/statamic-veto`
- `justbetter/statamic-cloudflare-purge`
- `justbetter/statamic-structured-data`
- `justbetter/statamic-glide-directive`
- `sentry/sentry-laravel`
- `rapidez/blade-components` / `rapidez/blade-directives`
- `just-better/laravel-healthchecks` (private)
- `just-better/statamic-authentik` (private)

## JustBetter Composer credentials

Private packages require access to `https://repo.justbetter.nl`.

During post-install you will be prompted for Composer credentials if they are not already available (project or global `auth.json`). Credentials are written to a local `auth.json` in the new project.

`auth.json` is gitignored and must not be committed.
