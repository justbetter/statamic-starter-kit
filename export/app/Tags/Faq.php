<?php

namespace App\Tags;

use Illuminate\Support\Collection;
use Statamic\Entries\Entry;
use Statamic\Tags\Tags;

class Faq extends Tags
{
    /**
     * @return \Illuminate\Support\Collection<int, Entry>
     */
    public function getItems(): Collection
    {
        $type = $this->params->get('type') ?? false;
        $categories = $this->params->get('categories') ?? collect();
        $items = $this->params->get('items') ?? collect();

        if ($type === 'manual') {
            return $items;
        }

        if (! $categories || $categories->isEmpty()) {
            return collect();
        }

        return $categories->flatMap(function ($category) {
            return $category->entries();
        });
    }
}
