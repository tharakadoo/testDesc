# Project Structure

## Domain-Module Architecture

Each domain module follows the same layered structure:

```
┌─────────────────────────────────────┐
│          Entities Layer             │  Domain models, events, business rules
├─────────────────────────────────────┤
│          UseCases Layer             │  Application logic, contracts, repository interfaces, DTOs
├─────────────────────────────────────┤
│          Adapters Layer             │  Framework-agnostic interface adapters
├─────────────────────────────────────┤
│            IO Layer                 │  Controllers, Eloquent repos, services, migrations, providers
├─────────────────────────────────────┤
│          Testing Layer              │  All tests for the module
└─────────────────────────────────────┘
```

## File Structure

- App
    - Foundation
        - Entities
        - UseCases
            - CacheContract.php
            - TransactionContract.php
        - Adapters
        - IO
            - Database
                - migrations
                    - 0001_01_01_000001_create_cache_table.php
                    - 0001_01_01_000002_create_jobs_table.php
                - seeders
                    - DatabaseSeeder.php
            - Http
                - Controllers
                    - Controller.php
                - Middleware
                    - SetSentryUserContext.php
            - ExternalServices
                - LaravelCacheService.php
                - LaravelTransactionService.php
            - FoundationServiceProvider.php
        - Specs
        - Testing
    - Post
        - Entities
            - Post.php
            - Subscriber.php
            - Events
                - PostPublished.php
        - UseCases
            - PostSubmitUseCase.php
            - EmailServiceContract.php
            - DataTransferObjects
                - PostResult.php
                - SubmitPostData.php
            - Repositories
                - PostRepositoryInterface.php
        - Adapters
        - IO
            - Database
                - EloquentPostRepository.php
                - factories
                    - PostFactory.php
                - migrations
                    - 0001_01_01_000004_create_posts_table.php
                    - 0001_01_01_000005_create_post_email_recipients_table.php
                    - 0001_01_01_000006_create_subscribers_table.php
            - Http
                - Controllers
                    - PostController.php
                - Commands
                    - SendPostEmailsCommand.php
            - ExternalServices
                - EmailService.php
                - LaravelEmailService.php
                - PostPublishedMail.php
            - Listeners
                - SendPostPublishedEmail.php
            - PostServiceProvider.php
        - Specs
        - Testing
            - EloquentPostRepositoryTest.php
            - LaravelEmailServiceTest.php
            - PostSubmitUseCaseTest.php
            - PostTest.php
            - SendPostEmailsCommandTest.php
            - SendPostPublishedEmailTest.php
    - User
        - Entities
            - User.php
        - UseCases
            - Repositories
                - UserRepositoryInterface.php
        - Adapters
        - IO
            - Database
                - EloquentUserRepository.php
                - factories
                    - UserFactory.php
                - migrations
                    - 0001_01_01_000000_create_users_table.php
                    - 2025_08_14_170933_add_two_factor_columns_to_users_table.php
                - seeders
                    - UserSeeder.php
            - UserServiceProvider.php
        - Specs
        - Testing
            - EloquentUserRepositoryTest.php
    - Website
        - Entities
            - Website.php
        - UseCases
            - GetAllWebsitesUseCase.php
            - SubscribeUseCase.php
            - WebsiteUserServiceContract.php
            - DataTransferObjects
                - SubscribeData.php
                - SubscriptionResult.php
            - Repositories
                - SubscriptionRepositoryInterface.php
                - WebsiteRepositoryInterface.php
        - Adapters
        - IO
            - Database
                - EloquentSubscriptionRepository.php
                - EloquentWebsiteRepository.php
                - EloquentWebsiteUserService.php
                - factories
                    - WebsiteFactory.php
                - migrations
                    - 0001_01_01_000003_create_websites_table.php
                - seeders
                    - WebsiteSeeder.php
            - Http
                - Controllers
                    - SubscriptionController.php
                    - WebsiteController.php
            - WebsiteServiceProvider.php
        - Specs
        - Testing
            - EloquentSubscriptionRepositoryTest.php
            - EloquentWebsiteRepositoryTest.php
            - EloquentWebsiteUserServiceTest.php
            - GetAllWebsitesUseCaseTest.php
            - SubscribeUseCaseTest.php

---
