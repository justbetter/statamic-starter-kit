<?php

namespace App\Tags;

use Illuminate\Support\Collection;
use Statamic\Entries\Entry;
use Statamic\Tags\Tags;
use Statamic\Taxonomies\Term;

class Faq extends Tags
{
    /**
     * @return Collection<int, Entry>
     */
    public function getItems(): Collection
    {
        $type = $this->params->get('type');

        if ($type === 'manual') {
            return $this->entries($this->params->get('items'));
        }

        $categories = $this->params->get('categories');

        if (! $categories instanceof Collection || $categories->isEmpty()) {
            return collect();
        }

        return $categories
            ->filter(fn (mixed $category): bool => $category instanceof Term)
            ->flatMap(fn (Term $category): Collection => $this->entries($category->entries()))
            ->values();
    }

    /**
     * @return Collection<int, Entry>
     */
    private function entries(mixed $items): Collection
    {
        if (! $items instanceof Collection) {
            return collect();
        }

        $entries = [];

        foreach ($items as $item) {
            if ($item instanceof Entry) {
                $entries[] = $item;
            }
        }

        return collect($entries);
    }
}
