<?php

namespace App\Website\Testing;

use Mockery;
use Tests\TestCase;
use App\User\Entities\User;
use App\Website\Entities\Website;
use App\Application\Contracts\TransactionContract;
use App\User\Repositories\UserRepositoryInterface;
use App\Website\DataTransferObjects\SubscriptionResult;
use App\Website\UseCases\SubscribeUseCase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use App\Website\Repositories\SubscriptionRepositoryInterface;
use App\Website\Repositories\WebsiteRepositoryInterface;

class SubscribeUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    #[DataProvider('missingFieldProvider')]
    public function when_missing_required_field_then_throws_validation_exception(array $subscriptionRequest, string $field, array $expectedMessage): void
    {
        $websiteRepository = Mockery::mock(WebsiteRepositoryInterface::class);
        $userRepository = Mockery::mock(UserRepositoryInterface::class);
        $subscriptionRepository = Mockery::mock(SubscriptionRepositoryInterface::class);
        $transaction = $this->createMockTransaction();

        $subscriptionRepository->shouldNotReceive('subscribe');

        $useCase = new SubscribeUseCase($websiteRepository, $userRepository, $subscriptionRepository, $transaction);

        $this->assertValidationException($useCase, $subscriptionRequest, $field, $expectedMessage);
    }

    #[Test]
    public function when_website_not_found_then_throws_validation_exception(): void
    {
        $subscriptionRequest = [
            'email' => 'user@example.com',
            'website_id' => 999,
        ];

        $websiteRepository = Mockery::mock(WebsiteRepositoryInterface::class);
        $websiteRepository->shouldReceive('find')
            ->once()
            ->with(999)
            ->andReturn(null);

        $userRepository = Mockery::mock(UserRepositoryInterface::class);
        $userRepository->shouldNotReceive('findOrCreate');

        $subscriptionRepository = Mockery::mock(SubscriptionRepositoryInterface::class);
        $subscriptionRepository->shouldNotReceive('subscribe');

        $transaction = $this->createMockTransaction();

        $useCase = new SubscribeUseCase($websiteRepository, $userRepository, $subscriptionRepository, $transaction);

        $this->assertWebsiteNotFound($useCase, $subscriptionRequest);
    }

    #[Test]
    public function when_already_subscribed_then_throws_validation_exception(): void
    {
        $subscriptionRequest = [
            'email' => 'user@example.com',
            'website_id' => 1,
        ];

        $website = new Website(['id' => 1, 'url' => 'https://example.com']);
        $user = new User(['id' => 1, 'email' => 'user@example.com']);

        $websiteRepository = Mockery::mock(WebsiteRepositoryInterface::class);
        $websiteRepository->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($website);

        $userRepository = Mockery::mock(UserRepositoryInterface::class);
        $userRepository->shouldReceive('findOrCreate')
            ->once()
            ->with('user@example.com')
            ->andReturn($user);

        $subscriptionRepository = Mockery::mock(SubscriptionRepositoryInterface::class);
        $subscriptionRepository->shouldReceive('isSubscribed')
            ->once()
            ->with($user, $website)
            ->andReturn(true);
        $subscriptionRepository->shouldNotReceive('subscribe');

        $transaction = $this->createMockTransaction();

        $useCase = new SubscribeUseCase($websiteRepository, $userRepository, $subscriptionRepository, $transaction);

        $this->assertAlreadySubscribed($useCase, $subscriptionRequest);
    }

    #[Test]
    public function when_subscribe_then_subscription_created(): void
    {
        $subscriptionRequest = [
            'email' => 'user@example.com',
            'website_id' => 1,
        ];

        $website = new Website(['id' => 1, 'url' => 'https://example.com']);
        $user = new User(['id' => 1, 'email' => 'user@example.com']);

        $websiteRepository = Mockery::mock(WebsiteRepositoryInterface::class);
        $websiteRepository->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($website);

        $userRepository = Mockery::mock(UserRepositoryInterface::class);
        $userRepository->shouldReceive('findOrCreate')
            ->once()
            ->with('user@example.com')
            ->andReturn($user);

        $subscriptionRepository = Mockery::mock(SubscriptionRepositoryInterface::class);
        $subscriptionRepository->shouldReceive('isSubscribed')
            ->once()
            ->with($user, $website)
            ->andReturn(false);
        $subscriptionRepository->shouldReceive('subscribe')
            ->once()
            ->with($user, $website);

        $transaction = $this->createMockTransaction();

        $useCase = new SubscribeUseCase($websiteRepository, $userRepository, $subscriptionRepository, $transaction);

        $result = $useCase->execute($subscriptionRequest);

        $this->assertSubscriptionCreated($result, $user, $website);
    }

    protected function createMockTransaction(): TransactionContract
    {
        $transaction = Mockery::mock(TransactionContract::class);
        $transaction->shouldReceive('execute')
            ->andReturnUsing(fn(callable $callback) => $callback());

        return $transaction;
    }

    protected function assertValidationException(SubscribeUseCase $useCase, array $subscriptionRequest, string $field, array $expectedMessage): void
    {
        try {
            $useCase->execute($subscriptionRequest);
            $this->fail('Expected ValidationException not thrown');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey($field, $e->errors());
            $this->assertEquals($expectedMessage, $e->errors()[$field]);
        }
    }

    protected function assertWebsiteNotFound(SubscribeUseCase $useCase, array $subscriptionRequest): void
    {
        try {
            $useCase->execute($subscriptionRequest);
            $this->fail('Expected ValidationException not thrown');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('website_id', $e->errors());
            $this->assertEquals(['Website not found'], $e->errors()['website_id']);
        }
    }

    protected function assertAlreadySubscribed(SubscribeUseCase $useCase, array $subscriptionRequest): void
    {
        try {
            $useCase->execute($subscriptionRequest);
            $this->fail('Expected ValidationException not thrown');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('email', $e->errors());
            $this->assertEquals(['Already subscribed to this website'], $e->errors()['email']);
        }
    }

    protected function assertSubscriptionCreated(SubscriptionResult $result, User $user, Website $website): void
    {
        $this->assertInstanceOf(SubscriptionResult::class, $result);
        $this->assertSame($user->email, $result->user->email);
        $this->assertSame($website->url, $result->website->url);
    }

    public static function missingFieldProvider(): array
    {
        return [
            'missing email' => [
                ['email' => '', 'website_id' => 1],
                'email',
                ['Email is required'],
            ],
            'invalid email' => [
                ['email' => 'invalid-email', 'website_id' => 1],
                'email',
                ['Email must be valid'],
            ],
            'missing website_id' => [
                ['email' => 'user@example.com', 'website_id' => null],
                'website_id',
                ['Website is required'],
            ],
        ];
    }
}
