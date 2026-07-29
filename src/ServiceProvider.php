<?php

namespace JustBetter\StatamicStarterKit;

use Illuminate\Routing\Router;
use JustBetter\StatamicStarterKit\Http\Controllers\CP\StarterKitFormsController;
use Statamic\Facades\Icon;
use Statamic\Http\Controllers\CP\Forms\FormsController;
use Statamic\Http\Middleware\RedirectAbsoluteDomains;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    /** @phpstan-ignore-next-line */
    protected $vite = [
        'input' => [
            'resources/js/justbetter-starter-kit.js',
            'resources/css/justbetter-starter-kit.css',
        ],
        'publicDirectory' => 'resources/dist',
    ];

    public function bootAddon(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'justbetter-starter-kit');

        $this->app->booted(function () {
            $router = app(Router::class);
            $router->pushMiddlewareToGroup('web', RedirectAbsoluteDomains::class);
        });

        $this->app->singleton(FormsController::class, StarterKitFormsController::class);

        Icon::register('custom-svg', resource_path('svg'));
        Icon::register('custom-icons', public_path('icons'));
    }
}
