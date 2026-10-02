<?php

namespace App\Http\Controllers\Cp;

use App\Http\Requests\Cp\StoreGlobalComponentRequest;
use Statamic\Contracts\Entries\Entry as EntryContract;
use Statamic\Entries\Collection as StatamicCollection;
use Statamic\Entries\Entry as StatamicEntry;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry;
use Statamic\Facades\Site;
use Statamic\Http\Controllers\CP\CpController;
use Statamic\Sites\Site as StatamicSite;

class GlobalComponentController extends CpController
{
    /**
     * @return array<string, mixed>
     */
    public function __invoke(StoreGlobalComponentRequest $request): array
    {
        $collection = Collection::findByHandle('global_components');

        abort_if($collection === null, 404);

        $this->authorize('store', [EntryContract::class, $collection]);

        $title = $request->string('title')->toString();

        $component = $request->input('component');
        abort_unless(is_array($component), 422);

        $site = Site::default();
        abort_unless($site instanceof StatamicSite, 500);

        $siteHandle = $site->handle();
        abort_unless(is_string($siteHandle), 500);

        $entry = Entry::make();
        abort_unless($entry instanceof StatamicEntry, 500);

        $entry = $entry->collection($collection);
        abort_unless($entry instanceof StatamicEntry, 500);

        $entry->locale($siteHandle);
        $entry->slug($this->uniqueSlug($collection, $siteHandle, $title));

        $values = $entry
            ->blueprint()
            ->fields()
            ->addValues([
                'title' => $title,
                'page_builder' => [$component],
            ])
            ->process()
            ->values();

        $entry->data($values);

        $entry->save();

        return [
            'id' => $entry->id(),
            'title' => $entry->value('title'),
            'edit_url' => $entry->editUrl(),
        ];
    }

    private function uniqueSlug(StatamicCollection $collection, string $siteHandle, string $title): string
    {
        $baseSlug = str($title)->slug()->toString();
        $slug = $baseSlug;
        $attempt = 2;

        while ($collection->queryEntries()->where('site', $siteHandle)->where('slug', $slug)->count() > 0) {
            $slug = $baseSlug.'-'.$attempt;
            $attempt++;
        }

        return $slug;
    }
}
