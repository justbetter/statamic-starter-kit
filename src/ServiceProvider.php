<?php

namespace JustBetter\StatamicStarterKit;

use Illuminate\Routing\Router;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Http\Middleware\RedirectAbsoluteDomains;

class ServiceProvider extends AddonServiceProvider
{
    public function bootAddon(): void
    {
        $this->app->booted(function() {
            $router = app(Router::class);
            $router->pushMiddlewareToGroup('web', RedirectAbsoluteDomains::class);
        });
    }
}
