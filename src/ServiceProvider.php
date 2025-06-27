<?php

namespace Justbetter\JustBetterStarterKit;

use Illuminate\Routing\Router;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Http\Middleware\RedirectAbsoluteDomains;

class ServiceProvider extends AddonServiceProvider
{
    public function bootAddon()
    {
        $this->app->booted(function() {
            $router = app(Router::class);
            $router->pushMiddlewareToGroup('web', RedirectAbsoluteDomains::class);
        });
    }
}
