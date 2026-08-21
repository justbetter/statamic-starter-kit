<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Statamic\Entries\Entry as StatamicEntry;
use Statamic\Facades\Entry;
use Tests\TestCase;

class GlobalComponentControllerTest extends TestCase
{
    #[Test]
    public function it_converts_a_component_to_a_global_component(): void
    {
        $this->actingAs(User::factory()->create(['super' => true]));

        $component = [
            '_id' => 'component-id',
            'type' => 'banner',
            'enabled' => true,
            'display' => 'full_width',
            'size' => 'normal',
        ];

        $response = $this->postJson(cp_route('global-components.convert'), [
            'title' => 'Reusable Banner',
            'component' => $component,
        ]);

        $response->assertOk()
            ->assertJsonPath('title', 'Reusable Banner');

        $entry = Entry::find($response->json('id'));

        $this->assertInstanceOf(StatamicEntry::class, $entry);
        $this->assertSame('global_components', $entry->collectionHandle());
        $this->assertSame('Reusable Banner', $entry->value('title'));

        $pageBuilder = $entry->value('page_builder');

        $this->assertCount(1, $pageBuilder);
        $this->assertSame('component-id', $pageBuilder[0]['id']);
        $this->assertSame('banner', $pageBuilder[0]['type']);
        $this->assertSame('full_width', $pageBuilder[0]['display']);
        $this->assertSame('normal', $pageBuilder[0]['size']);
    }

    #[Test]
    public function it_rejects_a_global_component_set(): void
    {
        $this->actingAs(User::factory()->create(['super' => true]));

        $this->postJson(cp_route('global-components.convert'), [
            'title' => 'Reusable Banner',
            'component' => [
                'type' => 'global_component',
                'global_component' => ['existing-id'],
            ],
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('component.type');
    }

    #[Test]
    public function it_requires_a_title(): void
    {
        $this->actingAs(User::factory()->create(['super' => true]));

        $this->postJson(cp_route('global-components.convert'), [
            'component' => [
                'type' => 'banner',
            ],
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('title');
    }

    #[Test]
    public function it_generates_a_unique_slug_for_duplicate_titles(): void
    {
        $this->actingAs(User::factory()->create(['super' => true]));

        $payload = [
            'title' => 'Reusable Banner',
            'component' => [
                'type' => 'banner',
            ],
        ];

        $first = $this->postJson(cp_route('global-components.convert'), $payload)->assertOk();
        $second = $this->postJson(cp_route('global-components.convert'), $payload)->assertOk();

        $firstEntry = Entry::find($first->json('id'));
        $secondEntry = Entry::find($second->json('id'));

        $this->assertInstanceOf(StatamicEntry::class, $firstEntry);
        $this->assertInstanceOf(StatamicEntry::class, $secondEntry);
        $this->assertSame('reusable-banner', $firstEntry->slug());
        $this->assertSame('reusable-banner-2', $secondEntry->slug());
    }
}
