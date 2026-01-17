# Project Structure

## Clean Architecture Layers

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

## File Structure

- App
    - Application
        - Contracts
            - CacheContract.php
            - TransactionContract.php
        - Listeners
            - SendPostPublishedEmail.php
        - Testing
            - SendPostPublishedEmailTest.php
    - Console
        - Commands
            - SendPostEmailsCommand.php
        - Testing
            - SendPostEmailsCommandTest.php
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
        - Testing
            - EloquentPostRepositoryTest.php
            - EloquentSubscriptionRepositoryTest.php
            - EloquentUserRepositoryTest.php
            - EloquentWebsiteRepositoryTest.php
            - EloquentWebsiteUserServiceTest.php
            - LaravelEmailServiceTest.php
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

---
