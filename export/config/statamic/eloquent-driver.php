<?php

return [

    'connection' => env('STATAMIC_ELOQUENT_CONNECTION', ''),
    'table_prefix' => env('STATAMIC_ELOQUENT_PREFIX', 'statamic_'),

    'asset_containers' => [
        'driver' => 'file',
        'model' => \Statamic\Eloquent\Assets\AssetContainerModel::class,
    ],

    'assets' => [
        'driver' => 'eloquent',
        'model' => \Statamic\Eloquent\Assets\AssetModel::class,
        'asset' => \Statamic\Eloquent\Assets\Asset::class,
    ],

    'blueprints' => [
        'driver' => 'file',
        'model' => \Statamic\Eloquent\Fields\BlueprintModel::class,
        'namespaces' => 'all',
    ],

    'collections' => [
        'driver' => 'file',
        'model' => \Statamic\Eloquent\Collections\CollectionModel::class,
        'update_entry_order_queue' => 'default',
        'update_entry_order_connection' => 'default',
    ],

    'collection_trees' => [
        'driver' => 'eloquent',
        'model' => \Statamic\Eloquent\Structures\TreeModel::class,
        'tree' => \Statamic\Eloquent\Structures\CollectionTree::class,
    ],

    'entries' => [
        'driver' => 'eloquent',
        'model' => \Statamic\Eloquent\Entries\EntryModel::class,
        'entry' => \Statamic\Eloquent\Entries\Entry::class,
        'map_data_to_columns' => false,
    ],

    'fieldsets' => [
        'driver' => 'file',
        'model' => \Statamic\Eloquent\Fields\FieldsetModel::class,
    ],

    'forms' => [
        'driver' => 'eloquent',
        'model'  => \Statamic\Eloquent\Forms\FormModel::class,
    ],

    'form_submissions' => [
        'driver' => 'eloquent',
        'model'  => \Statamic\Eloquent\Forms\SubmissionModel::class,
    ],

    'global_sets' => [
        'driver' => 'file',
        'model' => \Statamic\Eloquent\Globals\GlobalSetModel::class,
    ],

    'global_set_variables' => [
        'driver' => 'eloquent',
        'model' => \Statamic\Eloquent\Globals\VariablesModel::class,
    ],

    'navigations' => [
        'driver' => 'file',
        'model' => \Statamic\Eloquent\Structures\NavModel::class,
    ],

    'navigation_trees' => [
        'driver' => 'eloquent',
        'model' => \Statamic\Eloquent\Structures\TreeModel::class,
        'tree' => \Statamic\Eloquent\Structures\NavTree::class,
    ],

    'revisions' => [
        'driver' => 'eloquent',
        'model' => \Statamic\Eloquent\Revisions\RevisionModel::class,
    ],

    'taxonomies' => [
        'driver' => 'file',
        'model' => \Statamic\Eloquent\Taxonomies\TaxonomyModel::class,
    ],

    'terms' => [
        'driver' => 'eloquent',
        'model' => \Statamic\Eloquent\Taxonomies\TermModel::class,
    ],

    'tokens' => [
        'driver' => 'eloquent',
        'model' => \Statamic\Eloquent\Tokens\TokenModel::class,
    ],

    'sites' => [
        'driver' => 'file',
        'model' => \Statamic\Eloquent\Sites\SiteModel::class,
    ],
];
