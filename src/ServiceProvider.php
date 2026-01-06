<?php

namespace JustBetter\StatamicStarterKit;

use Illuminate\Routing\Router;
use JustBetter\StatamicStarterKit\Http\Controllers\CP\StarterKitFormsController;
use Statamic\Http\Controllers\CP\Forms\FormsController;
use Statamic\Http\Middleware\RedirectAbsoluteDomains;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    public function bootAddon(): void
    {
        $this->app->booted(function () {
            $router = app(Router::class);
            $router->pushMiddlewareToGroup('web', RedirectAbsoluteDomains::class);
        });

        $this->app->singleton(FormsController::class, StarterKitFormsController::class);
    }
}
