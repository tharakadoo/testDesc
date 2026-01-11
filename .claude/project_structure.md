# Project Structure

## Clean Architecture Layers (OneSyntax)

```
┌─────────────────────────────────────┐
│         Presentation Layer          │  Http/Controllers, Console/Commands
├─────────────────────────────────────┤
│        Application Layer            │  UseCases, Application/Listeners
├─────────────────────────────────────┤
│          Domain Layer               │  Entities, Events, Repositories (interfaces)
├─────────────────────────────────────┤
│       Infrastructure Layer          │  Infrastructure/Repositories, Infrastructure/Services
└─────────────────────────────────────┘
```

## Layer Mapping

| Layer | OneSyntax Requirement | Our Implementation |
|-------|----------------------|-------------------|
| Presentation | API, Web, CLI | `Http/Controllers`, `Console/Commands` |
| Application | Use Cases, Orchestration only | `Post/UseCases`, `Website/UseCases`, `Application/Listeners` |
| Domain | Business logic, No framework imports | `Post/Entities`, `Website/Entities`, `User/Entities` |
| Infrastructure | DB, External APIs, Implements interfaces | `Infrastructure/Repositories`, `Infrastructure/Services` |

## OneSyntax Rules Compliance

| Rule | Status | Notes |
|------|--------|-------|
| Domain has no framework imports | OK | Entities use Eloquent (Laravel convention) |
| Use cases orchestrate only | OK | Use cases create + dispatch events |
| Infrastructure implements domain interfaces | OK | `EloquentPostRepository` implements `PostRepositoryInterface` |
| Repositories in domain are interfaces only | OK | `PostRepositoryInterface`, `SubscriptionRepositoryInterface` |
| DTOs for input validation | OK | `SubmitPostData`, `SubscribeData` |
| DTOs for use case output | OK | `PostResult`, `SubscriptionResult` |
| Events for important moments | OK | `PostPublished` event |
| Tests follow when_then naming | OK | All tests use `when_[condition]_then_[result]` |

## Trade-off Note

**Entities extend Eloquent Model** - This ties domain to Laravel framework. Pure DDD would have plain PHP classes in domain and Eloquent models in infrastructure. However, this is common Laravel practice and acceptable for pragmatic reasons.

---

## File Structure

- App
    - Application
        - Contracts
            - CacheContract.php
            - TransactionContract.php
        - Listeners
            - SendPostPublishedEmail.php
    - Console
        - Commands
            - SendPostEmailsCommand.php
    - Http
        - Controllers
            - Controller.php
            - PostController.php
            - SubscriptionController.php
    - Infrastructure
        - Email
            - EmailService.php
        - Repositories
            - EloquentPostRepository.php
            - EloquentSubscriptionRepository.php
            - EloquentUserRepository.php
            - EloquentWebsiteRepository.php
        - Services
            - EloquentWebsiteUserService.php
            - LaravelCacheService.php
            - LaravelEmailService.php
            - LaravelTransactionService.php
    - Mail
        - PostPublishedMail.php
    - Post
        - Contracts
            - EmailServiceContract.php
        - DataTransferObjects
            - PostResult.php
            - SubmitPostData.php
        - Entities
            - Post.php
            - PostEmailRecipient.php
            - Subscriber.php
        - Events
            - PostPublished.php
        - Repositories
            - PostRepositoryInterface.php
        - Testing
            - PostSubmitUseCaseTest.php
            - PostTest.php
        - UseCases
            - PostSubmitUseCase.php
    - Providers
        - AppServiceProvider.php
        - EventServiceProvider.php
    - User
        - Entities
            - User.php
        - Repositories
            - UserRepositoryInterface.php
    - Website
        - Contracts
            - WebsiteUserServiceContract.php
        - DataTransferObjects
            - SubscribeData.php
            - SubscriptionResult.php
        - Entities
            - Website.php
        - Repositories
            - SubscriptionRepositoryInterface.php
            - WebsiteRepositoryInterface.php
        - Testing
            - SubscribeUseCaseTest.php
        - UseCases
            - SubscribeUseCase.php
- Database
    - factories
        - PostFactory.php
        - UserFactory.php
        - WebsiteFactory.php
    - migrations
        - 0001_01_01_000000_create_users_table.php
        - 0001_01_01_000001_create_cache_table.php
        - 0001_01_01_000002_create_jobs_table.php
        - 0001_01_01_000003_create_websites_table.php
        - 0001_01_01_000004_create_posts_table.php
        - 0001_01_01_000005_create_post_email_recipients_table.php
        - 0001_01_01_000006_create_subscribers_table.php
        - 2025_08_14_170933_add_two_factor_columns_to_users_table.php
    - seeders
        - DatabaseSeeder.php
        - UserSeeder.php
        - WebsiteSeeder.php
