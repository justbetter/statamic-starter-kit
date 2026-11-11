<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Dashboard cache widget
    |--------------------------------------------------------------------------
    |
    | When enabled, the "Caches" dashboard widget is automatically added to the
    | Control Panel dashboard for users with the "clear caches" permission.
    |
    */

    'dashboard_cache_widget' => env('JUSTBETTER_STARTER_KIT_DASHBOARD_CACHE_WIDGET', true),

    /*
    |--------------------------------------------------------------------------
    | Convert to global component
    |--------------------------------------------------------------------------
    |
    | Replicator set action that creates a reusable entry and replaces the set
    | with a relationship to that entry. Override these handles when your
    | project uses different collection / fieldset names. Set enabled to
    | false for projects that do not use the entries-based global component
    | pattern (e.g. a select of named types).
    |
    */

    'convert_to_global_component' => [
        'enabled' => env('JUSTBETTER_STARTER_KIT_CONVERT_TO_GLOBAL_COMPONENT', true),

        // Collection where the reusable component entry is created
        'collection' => 'global_components',

        // Field on that entry that stores the converted set (array of one set)
        'page_builder_field' => 'page_builder',

        // Replicator set handle to switch the original set to after convert
        'set_handle' => 'global_component',

        // Field inside that set that stores the relationship to the new entry
        'relationship_field' => 'global_component',
    ],

];
