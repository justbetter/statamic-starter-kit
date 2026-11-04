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

## Deploying

Pagebuilder set preview images live in `public/previews` and are committed to git. With the Eloquent asset driver, Statamic only shows an image that has a database row. Add this to every environment's deploy script:

```bash
php please eloquent:sync-assets --container=previews
```

