<?php

namespace App\Infrastructure\Testing;

use Tests\TestCase;
use App\Website\Entities\Website;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Infrastructure\Repositories\EloquentWebsiteRepository;

class EloquentWebsiteRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentWebsiteRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentWebsiteRepository();
    }

    #[Test]
    public function when_find_called_with_existing_id_then_returns_website(): void
    {
        $existingWebsite = Website::factory()->create(['url' => 'https://example.com']);

        $foundWebsite = $this->repository->find($existingWebsite->id);

        $this->assertWebsiteFound($foundWebsite, $existingWebsite);
    }

    #[Test]
    public function when_find_called_with_non_existing_id_then_returns_null(): void
    {
        $foundWebsite = $this->repository->find(99999);

        $this->assertNull($foundWebsite);
    }

    #[Test]
    public function when_all_called_then_returns_all_websites(): void
    {
        $website1 = Website::factory()->create(['url' => 'https://site1.com']);
        $website2 = Website::factory()->create(['url' => 'https://site2.com']);
        $website3 = Website::factory()->create(['url' => 'https://site3.com']);

        $allWebsites = $this->repository->all();

        $this->assertAllWebsitesReturned($allWebsites, [$website1, $website2, $website3]);
    }

    #[Test]
    public function when_all_called_with_no_websites_then_returns_empty_collection(): void
    {
        $allWebsites = $this->repository->all();

        $this->assertTrue($allWebsites->isEmpty());
    }

    #[Test]
    public function when_multiple_websites_exist_then_find_returns_only_specific_website(): void
    {
        $website1 = Website::factory()->create(['url' => 'https://site1.com']);
        $website2 = Website::factory()->create(['url' => 'https://site2.com']);

        $foundWebsite = $this->repository->find($website1->id);

        $this->assertEquals($website1->id, $foundWebsite->id);
        $this->assertNotEquals($website2->id, $foundWebsite->id);
    }

    protected function assertWebsiteFound(?Website $foundWebsite, Website $existingWebsite): void
    {
        $this->assertNotNull($foundWebsite);
        $this->assertEquals($existingWebsite->id, $foundWebsite->id);
        $this->assertEquals($existingWebsite->url, $foundWebsite->url);
    }

    protected function assertAllWebsitesReturned($collection, array $expectedWebsites): void
    {
        $this->assertCount(count($expectedWebsites), $collection);

        foreach ($expectedWebsites as $website) {
            $this->assertTrue(
                $collection->contains('id', $website->id),
                "Expected website {$website->id} in collection"
            );
        }
    }
}
