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

## Convert to global component

The Control Panel Replicator action that converts a set into a reusable global component entry is configurable. Publish the config (or set values in `config/statamic-starter-kit.php`) when your handles differ from the kit defaults.

```php
'convert_to_global_component' => [
    'enabled' => true,
    'collection' => 'globals',
    'page_builder_field' => 'global_page_builder',
    'set_handle' => 'globals_component',
    'relationship_field' => 'globals_component',
],
```

Set `enabled` to `false` (or `JUSTBETTER_STARTER_KIT_CONVERT_TO_GLOBAL_COMPONENT=false`) when the project does not use the entries-based global component pattern.

