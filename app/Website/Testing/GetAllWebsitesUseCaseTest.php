<?php

namespace App\Website\Testing;

use Mockery;
use Tests\TestCase;
use App\Website\Entities\Website;
use App\Website\UseCases\GetAllWebsitesUseCase;
use App\Website\Repositories\WebsiteRepositoryInterface;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\Test;

class GetAllWebsitesUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function when_get_all_websites_then_returns_collection_of_websites(): void
    {
        $websites = new Collection([
            new Website(['id' => 1, 'url' => 'https://example.com']),
            new Website(['id' => 2, 'url' => 'https://another.com']),
        ]);

        $websiteRepository = Mockery::mock(WebsiteRepositoryInterface::class);
        $websiteRepository->shouldReceive('all')
            ->once()
            ->andReturn($websites);

        $useCase = new GetAllWebsitesUseCase($websiteRepository);

        $result = $useCase->execute();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertEquals('https://example.com', $result[0]->url);
        $this->assertEquals('https://another.com', $result[1]->url);
    }

    #[Test]
    public function when_no_websites_then_returns_empty_collection(): void
    {
        $websiteRepository = Mockery::mock(WebsiteRepositoryInterface::class);
        $websiteRepository->shouldReceive('all')
            ->once()
            ->andReturn(new Collection());

        $useCase = new GetAllWebsitesUseCase($websiteRepository);

        $result = $useCase->execute();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(0, $result);
    }
}
