# Project Restructure Plan

Migrate the current project structure to match the expected domain-module architecture defined in `.claude/onesyntax/repo.md`.

---

## Target Architecture (per module)

```
app/
├── Foundation/          # Shared kernel
│   ├── Entities/
│   ├── UseCases/
│   │   └── Repositories/
│   ├── Adapters/
│   ├── IO/
│   │   ├── Database/
│   │   ├── Http/
│   │   ├── Web/
│   │   ├── GraphQL/
│   │   ├── ExternalServices/
│   │   └── FoundationServiceProvider.php
│   ├── Specs/
│   └── Testing/
│
├── Post/
│   ├── Entities/
│   ├── UseCases/
│   │   └── Repositories/
│   ├── Adapters/
│   ├── IO/
│   │   ├── Database/
│   │   ├── Http/
│   │   ├── Web/
│   │   ├── ExternalServices/
│   │   └── PostServiceProvider.php
│   ├── Specs/
│   └── Testing/
│
├── User/
│   ├── Entities/
│   ├── UseCases/
│   │   └── Repositories/
│   ├── Adapters/
│   ├── IO/
│   │   ├── Database/
│   │   ├── Http/
│   │   └── UserServiceProvider.php
│   ├── Specs/
│   └── Testing/
│
└── Website/
    ├── Entities/
    ├── UseCases/
    │   └── Repositories/
    ├── Adapters/
    ├── IO/
    │   ├── Database/
    │   ├── Http/
    │   └── WebsiteServiceProvider.php
    ├── Specs/
    └── Testing/
```

---

## Phase 1: Create Foundation Module

The `Foundation/` module holds shared kernel code, base classes, and cross-cutting concerns.

### Step 1.1 — Create Foundation directory skeleton

```
app/Foundation/
├── Entities/
├── UseCases/
│   └── Repositories/
├── Adapters/
├── IO/
│   ├── Database/
│   │   ├── migrations/
│   │   ├── seeders/
│   │   └── factories/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Middleware/
│   ├── Web/
│   ├── ExternalServices/
│   └── FoundationServiceProvider.php
├── Specs/
└── Testing/
```

### Step 1.2 — Move shared application contracts

| From | To |
|---|---|
| `app/Application/Contracts/CacheContract.php` | `app/Foundation/UseCases/CacheContract.php` |
| `app/Application/Contracts/TransactionContract.php` | `app/Foundation/UseCases/TransactionContract.php` |

### Step 1.3 — Move shared infrastructure services

| From | To |
|---|---|
| `app/Infrastructure/Services/LaravelCacheService.php` | `app/Foundation/IO/ExternalServices/LaravelCacheService.php` |
| `app/Infrastructure/Services/LaravelTransactionService.php` | `app/Foundation/IO/ExternalServices/LaravelTransactionService.php` |

### Step 1.4 — Move base controller

| From | To |
|---|---|
| `app/Http/Controllers/Controller.php` | `app/Foundation/IO/Http/Controllers/Controller.php` |

### Step 1.5 — Move shared middleware

| From | To |
|---|---|
| `app/Http/Middleware/SetSentryUserContext.php` | `app/Foundation/IO/Http/Middleware/SetSentryUserContext.php` |

### Step 1.6 — Move shared database assets

| From | To |
|---|---|
| `database/migrations/0001_01_01_000001_create_cache_table.php` | `app/Foundation/IO/Database/migrations/` |
| `database/migrations/0001_01_01_000002_create_jobs_table.php` | `app/Foundation/IO/Database/migrations/` |
| `database/seeders/DatabaseSeeder.php` | `app/Foundation/IO/Database/seeders/` |

### Step 1.7 — Move service providers

| From | To |
|---|---|
| `app/Providers/AppServiceProvider.php` | `app/Foundation/IO/FoundationServiceProvider.php` (rename & refactor) |
| `app/Providers/EventServiceProvider.php` | Merge into `FoundationServiceProvider.php` or keep as `app/Foundation/IO/EventServiceProvider.php` |

### Step 1.8 — Move shared infrastructure tests

| From | To |
|---|---|
| `app/Infrastructure/Testing/LaravelEmailServiceTest.php` | `app/Foundation/Testing/LaravelEmailServiceTest.php` (if email is shared) |

---

## Phase 2: Restructure Post Module

### Step 2.1 — Entities (already correct, keep as-is)

- `app/Post/Entities/Post.php` — no change
- `app/Post/Entities/Subscriber.php` — no change

### Step 2.2 — Move Events into Entities

| From | To |
|---|---|
| `app/Post/Events/PostPublished.php` | `app/Post/Entities/Events/PostPublished.php` |

### Step 2.3 — Reorganise UseCases

| From | To |
|---|---|
| `app/Post/UseCases/PostSubmitUseCase.php` | `app/Post/UseCases/PostSubmitUseCase.php` (no change) |
| `app/Post/Repositories/PostRepositoryInterface.php` | `app/Post/UseCases/Repositories/PostRepositoryInterface.php` |
| `app/Post/Contracts/EmailServiceContract.php` | `app/Post/UseCases/EmailServiceContract.php` |
| `app/Post/DataTransferObjects/PostResult.php` | `app/Post/UseCases/DataTransferObjects/PostResult.php` |
| `app/Post/DataTransferObjects/SubmitPostData.php` | `app/Post/UseCases/DataTransferObjects/SubmitPostData.php` |

### Step 2.4 — Create Adapters (empty for now)

```
app/Post/Adapters/   (create empty directory)
```

### Step 2.5 — Create IO layer

| From | To |
|---|---|
| `app/Http/Controllers/PostController.php` | `app/Post/IO/Http/Controllers/PostController.php` |
| `app/Infrastructure/Repositories/EloquentPostRepository.php` | `app/Post/IO/Database/EloquentPostRepository.php` |
| `app/Infrastructure/Email/EmailService.php` | `app/Post/IO/ExternalServices/EmailService.php` |
| `app/Infrastructure/Services/LaravelEmailService.php` | `app/Post/IO/ExternalServices/LaravelEmailService.php` |
| `app/Mail/PostPublishedMail.php` | `app/Post/IO/ExternalServices/PostPublishedMail.php` |
| `app/Application/Listeners/SendPostPublishedEmail.php` | `app/Post/IO/Listeners/SendPostPublishedEmail.php` |
| `app/Console/Commands/SendPostEmailsCommand.php` | `app/Post/IO/Http/Commands/SendPostEmailsCommand.php` |
| `database/migrations/0001_01_01_000004_create_posts_table.php` | `app/Post/IO/Database/migrations/` |
| `database/migrations/0001_01_01_000005_create_post_email_recipients_table.php` | `app/Post/IO/Database/migrations/` |
| `database/migrations/0001_01_01_000006_create_subscribers_table.php` | `app/Post/IO/Database/migrations/` |
| `database/factories/PostFactory.php` | `app/Post/IO/Database/factories/PostFactory.php` |

### Step 2.6 — Create PostServiceProvider

Create `app/Post/IO/PostServiceProvider.php` — bind repository interfaces, register event listeners, register routes.

### Step 2.7 — Consolidate Testing

| From | To |
|---|---|
| `app/Post/Testing/PostSubmitUseCaseTest.php` | `app/Post/Testing/PostSubmitUseCaseTest.php` (no change) |
| `app/Post/Testing/PostTest.php` | `app/Post/Testing/PostTest.php` (no change) |
| `app/Infrastructure/Testing/EloquentPostRepositoryTest.php` | `app/Post/Testing/EloquentPostRepositoryTest.php` |
| `app/Application/Testing/SendPostPublishedEmailTest.php` | `app/Post/Testing/SendPostPublishedEmailTest.php` |
| `app/Console/Testing/SendPostEmailsCommandTest.php` | `app/Post/Testing/SendPostEmailsCommandTest.php` |

### Step 2.8 — Create Specs (empty for now)

```
app/Post/Specs/   (create empty directory)
```

---

## Phase 3: Restructure User Module

### Step 3.1 — Entities (no change)

- `app/User/Entities/User.php` — no change

### Step 3.2 — Reorganise UseCases

| From | To |
|---|---|
| `app/User/Repositories/UserRepositoryInterface.php` | `app/User/UseCases/Repositories/UserRepositoryInterface.php` |

### Step 3.3 — Create Adapters (empty)

```
app/User/Adapters/
```

### Step 3.4 — Create IO layer

| From | To |
|---|---|
| `app/Infrastructure/Repositories/EloquentUserRepository.php` | `app/User/IO/Database/EloquentUserRepository.php` |
| `database/migrations/0001_01_01_000000_create_users_table.php` | `app/User/IO/Database/migrations/` |
| `database/migrations/2025_08_14_170933_add_two_factor_columns_to_users_table.php` | `app/User/IO/Database/migrations/` |
| `database/factories/UserFactory.php` | `app/User/IO/Database/factories/UserFactory.php` |
| `database/seeders/UserSeeder.php` | `app/User/IO/Database/seeders/UserSeeder.php` |

### Step 3.5 — Create UserServiceProvider

Create `app/User/IO/UserServiceProvider.php` — bind `UserRepositoryInterface` to `EloquentUserRepository`.

### Step 3.6 — Consolidate Testing

| From | To |
|---|---|
| `app/Infrastructure/Testing/EloquentUserRepositoryTest.php` | `app/User/Testing/EloquentUserRepositoryTest.php` |

### Step 3.7 — Create Specs (empty)

```
app/User/Specs/
```

---

## Phase 4: Restructure Website Module

### Step 4.1 — Entities (no change)

- `app/Website/Entities/Website.php` — no change

### Step 4.2 — Reorganise UseCases

| From | To |
|---|---|
| `app/Website/UseCases/SubscribeUseCase.php` | no change |
| `app/Website/UseCases/GetAllWebsitesUseCase.php` | no change |
| `app/Website/Repositories/SubscriptionRepositoryInterface.php` | `app/Website/UseCases/Repositories/SubscriptionRepositoryInterface.php` |
| `app/Website/Repositories/WebsiteRepositoryInterface.php` | `app/Website/UseCases/Repositories/WebsiteRepositoryInterface.php` |
| `app/Website/Contracts/WebsiteUserServiceContract.php` | `app/Website/UseCases/WebsiteUserServiceContract.php` |
| `app/Website/DataTransferObjects/SubscribeData.php` | `app/Website/UseCases/DataTransferObjects/SubscribeData.php` |
| `app/Website/DataTransferObjects/SubscriptionResult.php` | `app/Website/UseCases/DataTransferObjects/SubscriptionResult.php` |

### Step 4.3 — Create Adapters (empty)

```
app/Website/Adapters/
```

### Step 4.4 — Create IO layer

| From | To |
|---|---|
| `app/Http/Controllers/SubscriptionController.php` | `app/Website/IO/Http/Controllers/SubscriptionController.php` |
| `app/Http/Controllers/WebsiteController.php` | `app/Website/IO/Http/Controllers/WebsiteController.php` |
| `app/Infrastructure/Repositories/EloquentSubscriptionRepository.php` | `app/Website/IO/Database/EloquentSubscriptionRepository.php` |
| `app/Infrastructure/Repositories/EloquentWebsiteRepository.php` | `app/Website/IO/Database/EloquentWebsiteRepository.php` |
| `app/Infrastructure/Services/EloquentWebsiteUserService.php` | `app/Website/IO/Database/EloquentWebsiteUserService.php` |
| `database/migrations/0001_01_01_000003_create_websites_table.php` | `app/Website/IO/Database/migrations/` |
| `database/factories/WebsiteFactory.php` | `app/Website/IO/Database/factories/WebsiteFactory.php` |
| `database/seeders/WebsiteSeeder.php` | `app/Website/IO/Database/seeders/WebsiteSeeder.php` |

### Step 4.5 — Create WebsiteServiceProvider

Create `app/Website/IO/WebsiteServiceProvider.php` — bind repository interfaces, register routes.

### Step 4.6 — Consolidate Testing

| From | To |
|---|---|
| `app/Website/Testing/SubscribeUseCaseTest.php` | no change |
| `app/Website/Testing/GetAllWebsitesUseCaseTest.php` | no change |
| `app/Infrastructure/Testing/EloquentSubscriptionRepositoryTest.php` | `app/Website/Testing/EloquentSubscriptionRepositoryTest.php` |
| `app/Infrastructure/Testing/EloquentWebsiteRepositoryTest.php` | `app/Website/Testing/EloquentWebsiteRepositoryTest.php` |
| `app/Infrastructure/Testing/EloquentWebsiteUserServiceTest.php` | `app/Website/Testing/EloquentWebsiteUserServiceTest.php` |

### Step 4.7 — Create Specs (empty)

```
app/Website/Specs/
```

---

## Phase 5: Update Namespaces & References

After all files are moved, every file needs its namespace and `use` statements updated.

### 5.1 — Namespace mapping

| Old Namespace Prefix | New Namespace Prefix |
|---|---|
| `App\Application\Contracts` | `App\Foundation\UseCases` |
| `App\Application\Listeners` | `App\Post\IO\Listeners` |
| `App\Application\Testing` | `App\Post\Testing` |
| `App\Console\Commands` | `App\Post\IO\Http\Commands` |
| `App\Console\Testing` | `App\Post\Testing` |
| `App\Http\Controllers` | `App\Foundation\IO\Http\Controllers` (base), `App\Post\IO\Http\Controllers`, `App\Website\IO\Http\Controllers` |
| `App\Http\Middleware` | `App\Foundation\IO\Http\Middleware` |
| `App\Infrastructure\Repositories` | `App\<Module>\IO\Database` |
| `App\Infrastructure\Services` | `App\Foundation\IO\ExternalServices` or `App\<Module>\IO\Database` |
| `App\Infrastructure\Email` | `App\Post\IO\ExternalServices` |
| `App\Mail` | `App\Post\IO\ExternalServices` |
| `App\Post\Contracts` | `App\Post\UseCases` |
| `App\Post\DataTransferObjects` | `App\Post\UseCases\DataTransferObjects` |
| `App\Post\Events` | `App\Post\Entities\Events` |
| `App\Post\Repositories` | `App\Post\UseCases\Repositories` |
| `App\User\Repositories` | `App\User\UseCases\Repositories` |
| `App\Website\Contracts` | `App\Website\UseCases` |
| `App\Website\DataTransferObjects` | `App\Website\UseCases\DataTransferObjects` |
| `App\Website\Repositories` | `App\Website\UseCases\Repositories` |
| `App\Providers` | `App\Foundation\IO` |

### 5.2 — Update `composer.json` autoload

Verify `psr-4` autoload mapping still resolves correctly (it should if the root `App\` => `app/` mapping is unchanged).

### 5.3 — Update `bootstrap/providers.php`

Replace references to old service providers with new module service providers:
- `App\Foundation\IO\FoundationServiceProvider`
- `App\Post\IO\PostServiceProvider`
- `App\User\IO\UserServiceProvider`
- `App\Website\IO\WebsiteServiceProvider`

### 5.4 — Update route files

Update `routes/web.php` and `routes/api.php` controller references, or move route definitions into each module's service provider.

### 5.5 — Update `phpunit.xml`

Adjust test suite paths if tests are discovered from `app/*/Testing/` and `app/*/Specs/`.

### 5.6 — Update config files

- `config/sentry.php` — update middleware class reference
- `config/app.php` — update any hardcoded class references

---

## Phase 6: Cleanup

### 6.1 — Delete emptied directories

After all moves are complete, remove:
- `app/Application/`
- `app/Console/`
- `app/Http/`
- `app/Infrastructure/`
- `app/Mail/`
- `app/Providers/`
- `app/Post/Contracts/`
- `app/Post/DataTransferObjects/`
- `app/Post/Events/`
- `app/Post/Repositories/`
- `app/User/Repositories/`
- `app/Website/Contracts/`
- `app/Website/DataTransferObjects/`
- `app/Website/Repositories/`
- `database/factories/` (if fully moved)
- `database/migrations/` (if fully moved)
- `database/seeders/` (if fully moved)

### 6.2 — Run tests

```bash
php artisan test
```

### 6.3 — Clear caches

```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
composer dump-autoload
```

---

## Execution Order Summary

1. **Phase 1** — Foundation module (shared kernel, base classes)
2. **Phase 2** — Post module restructure
3. **Phase 3** — User module restructure
4. **Phase 4** — Website module restructure
5. **Phase 5** — Namespace updates, config updates, autoload
6. **Phase 6** — Cleanup and verify

Each phase should be committed separately so changes are reviewable and reversible.
