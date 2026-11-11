<?php

namespace JustBetter\StatamicStarterKit\Http\ViewComposers;

use Illuminate\View\View;
use Statamic\Facades\GlobalSet as GlobalSetFacade;
use Statamic\Facades\Site as SiteFacade;
use Statamic\Globals\GlobalSet;
use Statamic\Sites\Site;

class MailGlobalsComposer
{
    public function compose(View $view): void
    {
        /** @var Site $site */
        $site = SiteFacade::current();

        if (! $view->offsetExists('globals')) {
            $view->with('globals', (object) $this->getGlobals($site));
        }

        $view->with('siteUrl', $site->absoluteUrl());
    }

    /**
     * @return array<string, mixed>
     */
    protected function getGlobals(Site $site): array
    {
        $globals = [];

        /** @var GlobalSet $set */
        foreach (GlobalSetFacade::all() as $set) {
            if ($set->existsIn($site->handle())) {
                $globals[$set->handle()] = $set->in($site->handle());
            }
        }

        return $globals;
    }
}
