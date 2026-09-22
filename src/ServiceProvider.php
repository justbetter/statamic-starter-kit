<?php

namespace JustBetter\StatamicStarterKit;

use Illuminate\Routing\Router;
use JustBetter\StatamicStarterKit\Http\Controllers\CP\StarterKitFormsController;
use JustBetter\StatamicStarterKit\Widgets\Caches;
use Statamic\Facades\Icon;
use Statamic\Facades\Permission;
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

    protected $widgets = [
        Caches::class,
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

        $this->bootPermissions()
            ->bootDashboardCacheWidget();
    }

    protected function bootPermissions(): self
    {
        Permission::extend(function () {
            Permission::group('justbetter', 'JustBetter', function () {
                Permission::register('clear caches')
                    ->label(__('justbetter-starter-kit::messages.permission_clear_caches'));
            });
        });

        return $this;
    }

    protected function bootDashboardCacheWidget(): self
    {
        if (! config('statamic-starter-kit.dashboard_cache_widget', true)) {
            return $this;
        }

        $widgets = collect(config('statamic.cp.widgets', []));

        $alreadyPresent = $widgets->contains(function (mixed $widget): bool {
            $type = is_string($widget) ? $widget : ($widget['type'] ?? null);

            return $type === 'caches';
        });

        if ($alreadyPresent) {
            return $this;
        }

        config([
            'statamic.cp.widgets' => $widgets
                ->push([
                    'type' => 'caches',
                    'width' => 50,
                    'can' => 'clear caches',
                ])
                ->all(),
        ]);

        return $this;
    }
}
