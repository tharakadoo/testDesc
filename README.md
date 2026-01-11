
## OneSyntax TDD Test - Kasun

### Requirements
- PHP 8.2+
- Composer
- MySQL
- Node.js & npm (for frontend)

### Installation

```bash
# Clone the repository
git clone <repository-url>
cd testDesc

# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=your_database
# DB_USERNAME=your_username
# DB_PASSWORD=your_password

# Run migrations
php artisan migrate

# Seed websites data
php artisan db:seed --class=WebsiteSeeder
```

### Running the Application

```bash
# Start the development server
php artisan serve

# Start the queue worker (required for background email processing)
php artisan queue:work
```

### Running Tests

```bash
composer test
```

### API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/websites/{website}/subscribe` | Subscribe to a website |
| POST | `/api/websites/{website}/posts` | Create a post for a website |

#### Subscribe to a Website
```bash
curl -X POST http://localhost:8000/api/websites/1/subscribe \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com"}'
```

#### Create a Post
```bash
curl -X POST http://localhost:8000/api/websites/1/posts \
  -H "Content-Type: application/json" \
  -d '{"title": "Post Title", "description": "Post description"}'
```

### Artisan Commands

```bash
# Send emails for a specific post manually
php artisan posts:send-emails {post_id}
```

### Project Structure:
- App
  - Application
    - Contracts `shared contracts across domains`
      - CacheContract.php
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
  - Infrastructure `framework-specific implementations`
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
  - Mail
    - PostPublishedMail.php
  - Post `Post domain (DDD bounded context)`
    - Contracts
      - EmailServiceContract.php
    - DataTransferObjects `SRP - validation separated from business logic`
      - SubmitPostData.php
    - Entities
      - Post.php
      - PostEmailRecipient.php
      - Subscriber.php
    - Events
      - PostPublished.php
    - Repositories `dependency inversion - interfaces only`
      - PostRepositoryInterface.php
    - Testing
      - PostSubmitUseCaseTest.php
    - UseCases `business logic orchestration`
      - PostSubmitUseCase.php
  - Providers
    - AppServiceProvider.php
    - EventServiceProvider.php
  - User `User domain (DDD bounded context)`
    - Entities
      - User.php
    - Repositories
      - UserRepositoryInterface.php
  - Website `Website domain (DDD bounded context)`
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
