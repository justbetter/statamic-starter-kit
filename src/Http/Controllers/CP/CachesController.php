<?php

namespace JustBetter\StatamicStarterKit\Http\Controllers\CP;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;
use Statamic\Facades\StaticCache;
use Statamic\Http\Controllers\CP\CpController;

class CachesController extends CpController
{
    public function clearApplication(): JsonResponse
    {
        $this->authorize('clear caches');

        Artisan::call('cache:clear');

        return response()->json([
            'message' => __('justbetter-starter-kit::messages.caches_success_application'),
        ]);
    }

    public function clearStatic(): JsonResponse
    {
        $this->authorize('clear caches');

        abort_unless((bool) config('statamic.static_caching.strategy'), 404);

        StaticCache::flush();

        return response()->json([
            'message' => __('justbetter-starter-kit::messages.caches_success_static'),
        ]);
    }
}
