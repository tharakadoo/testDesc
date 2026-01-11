Original Requirement

Create a simple subscription platform (RESTful APIs with MySQL, Vue.js UI) in which users can subscribe to one or more websites
Whenever a new post is published on a particular website, all its subscribers shall receive an email with the post title and description in it.
No authentication of any kind is required.
Requirements:
MUST:
● Endpoint to create a "post" for a "particular website".
● UI and Endpoint to allow user to subscribe to a "particular website" with validations.
● Use commands to send emails to the subscribers.
● Use queues to schedule sending in the background.
● Follow TDD when implementing all the features.
● Write migrations for the required tables.
● No duplicate posts should get sent to subscribers by email.
NICE TO HAVE:
● Seeded data of the websites.
● Open API documentation (or) Postman collection demonstrating available APIs & their usage.
● Use of the latest Laravel version.
● Use of contracts & services.
● Use of caching wherever applicable.
● Use of events/listeners.
OUTPUTS:
● Deploy the code on a public GitHub repository and provide the link
● Provide special instructions (if any) to make to codebase run on our local/remote platform.

Use Laravel and React as the tech stack for optimistic UI development (https://github.com/ngneat/elf)

# Requirement

Create a simple subscription platform (RESTful APIs with MySQL, React elf UI) in which users can subscribe to one or more websites. Whenever a new post is published on a particular website, all its subscribers shall receive an email with the post title and description in it. No authentication of any kind is required.

---

## MUST Requirements

| Requirement | Status | Implementation |
|-------------|--------|----------------|
| Endpoint to create a "post" for a "particular website" | Done | `POST /api/websites/{website}/posts` - `PostController@store` |
| Endpoint to subscribe to a "particular website" with validations | Done | `POST /api/websites/{website}/subscribe` - `SubscriptionController@store` |
| Use commands to send emails to subscribers | Done | `php artisan posts:send-emails {post_id}` - `SendPostEmailsCommand` |
| Use queues to schedule sending in background | Done | `SendPostPublishedEmail` implements `ShouldQueue` |
| Follow TDD when implementing all features | Done | 15 tests in `PostSubmitUseCaseTest`, `SubscribeUseCaseTest`, `PostTest` |
| Write migrations for required tables | Done | 8 migrations (users, websites, posts, subscribers, post_email_recipients, jobs, cache) |
| No duplicate posts should get sent to subscribers | Done | `Post::hasUserReceivedEmail()` + `post_email_recipients` table |

---

## NICE TO HAVE Requirements

| Requirement | Status | Implementation |
|-------------|--------|----------------|
| Seeded data of websites | Done | `WebsiteSeeder` with 5 websites |
| Open API documentation / Postman collection | Not Done | - |
| Use of latest Laravel version | Done | Laravel 11 |
| Use of contracts & services | Done | `CacheContract`, `TransactionContract`, `EmailServiceContract`, repository interfaces |
| Use of caching wherever applicable | Done | Caching in `SendPostPublishedEmail`, `SendPostEmailsCommand` |
| Use of events/listeners | Done | `PostPublished` event, `SendPostPublishedEmail` listener |

---

## Additional Improvements (OneSyntax Compliance)

### Architecture
- Clean Architecture with 4 layers (Presentation, Application, Domain, Infrastructure)
- DDD bounded contexts (Post, Website, User domains)
- Dependency Inversion via repository interfaces

### Code Quality (14 Phases Completed)
1. Removed duplicate contracts (DRY)
2. Extracted cache dependency from use case
3. Split SubscriptionRepository (SRP)
4. Fixed test naming (`when_then` pattern)
5. Applied step-down rule to tests
6. Fixed test ordering (negative first)
7. Renamed variables (domain-specific names)
8. Removed docblock comments
9. Refactored use case return types (DTOs)
10. Enriched domain models (removed anemic models)
11. Extracted email logic to event/listener
12. Created PostResult DTO
13. Added domain unit tests
14. Added transaction handling

### Key Files Created/Modified
- `TransactionContract` + `LaravelTransactionService` - DB transaction abstraction
- `CacheContract` + `LaravelCacheService` - Cache abstraction
- `PostResult`, `SubscriptionResult` - Use case output DTOs
- `Post::hasUserReceivedEmail()`, `Post::markEmailSentTo()` - Domain behavior

---

## How to Run

```bash
# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Run migrations and seeders
php artisan migrate
php artisan db:seed --class=WebsiteSeeder

# Start server
php artisan serve

# Start queue worker (for background email processing)
php artisan queue:work

# Run tests
composer test
```

---

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/websites/{website}/subscribe` | Subscribe to a website |
| POST | `/api/websites/{website}/posts` | Create a post for a website |

### Subscribe Example
```bash
curl -X POST http://localhost:8000/api/websites/1/subscribe \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com"}'
```

### Create Post Example
```bash
curl -X POST http://localhost:8000/api/websites/1/posts \
  -H "Content-Type: application/json" \
  -d '{"title": "Post Title", "description": "Post description"}'
```
