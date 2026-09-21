<?php

namespace JustBetter\StatamicStarterKit\Widgets;

use Statamic\Facades\User;
use Statamic\Widgets\VueComponent;
use Statamic\Widgets\Widget;

class Caches extends Widget
{
    public function component(): ?VueComponent
    {
        if (! User::current()?->can('clear caches')) {
            return null;
        }

        return VueComponent::render('justbetter-caches-widget', [
            'title' => __('justbetter-starter-kit::messages.caches_widget_title'),
            'clearApplicationUrl' => cp_route('justbetter.caches.clear-application'),
            'clearStaticUrl' => cp_route('justbetter.caches.clear-static'),
            'staticCacheEnabled' => (bool) config('statamic.static_caching.strategy'),
            'labels' => [
                'application' => __('justbetter-starter-kit::messages.caches_application'),
                'application_description' => __('justbetter-starter-kit::messages.caches_application_description'),
                'static' => __('justbetter-starter-kit::messages.caches_static'),
                'static_description' => __('justbetter-starter-kit::messages.caches_static_description'),
                'static_disabled' => __('justbetter-starter-kit::messages.caches_static_disabled'),
                'clear' => __('justbetter-starter-kit::messages.caches_clear'),
                'confirm_application_title' => __('justbetter-starter-kit::messages.caches_confirm_application_title'),
                'confirm_application_body' => __('justbetter-starter-kit::messages.caches_confirm_application_body'),
                'confirm_static_title' => __('justbetter-starter-kit::messages.caches_confirm_static_title'),
                'confirm_static_body' => __('justbetter-starter-kit::messages.caches_confirm_static_body'),
                'confirm_button' => __('justbetter-starter-kit::messages.caches_confirm_button'),
                'success_application' => __('justbetter-starter-kit::messages.caches_success_application'),
                'success_static' => __('justbetter-starter-kit::messages.caches_success_static'),
                'error' => __('justbetter-starter-kit::messages.caches_error'),
            ],
        ]);
    }
}
