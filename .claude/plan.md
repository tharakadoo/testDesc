# Project Structure Improvement Plan

## Completed Phases (1-12)

| Phase | Description | Status |
|-------|-------------|--------|
| 1 | Remove duplicate contracts | Complete |
| 2 | Extract cache dependency | Complete |
| 3 | Split repository (SRP) | Complete |
| 4 | Fix test naming & attributes | Complete |
| 5 | Apply step-down rule | Complete |
| 6 | Fix test ordering | Complete |
| 7 | Rename variables | Complete |
| 8 | Remove docblocks | Complete |
| 9 | Refactor return types | Complete |
| 10 | Enrich domain models | Complete |
| 11 | Extract email logic to event | Complete |
| 12 | Create PostResult DTO | Complete |

---

## Phase 13: Add Domain Unit Tests
**Status:** [x] Complete

### Problem:
No unit tests for domain entities. Tests only cover use cases.

### Note:
Originally planned to remove mocks from `PostSubmitUseCaseTest`, but mocking is required by OneSyntax guidelines for isolation testing. Mocks remain in use case tests.

Created `PostTest.php` with integration tests for `Post` entity domain behavior. However, these may need to be removed if OneSyntax requires pure unit tests without database (RefreshDatabase trait).

### Tasks:
- [x] Create `app/Post/Testing/PostTest.php` with tests for `Post` entity behavior
- [x] Test `markEmailSentTo()` method
- [x] Test `hasUserReceivedEmail()` method
- [x] Keep mocks in `PostSubmitUseCaseTest` (OneSyntax requirement)

### Files:
| File | Action |
|------|--------|
| `app/Post/Testing/PostTest.php` | CREATE (may need removal if integration tests not allowed) |

### Run After:
```bash
composer test
```

---

## Phase 14: Add Transaction Handling
**Status:** [x] Complete

### Problem:
Use cases don't wrap operations in database transactions. If operation fails mid-way, data could be inconsistent.

### Why TransactionContract instead of DB::Transaction?

| Aspect | DB::Transaction | TransactionContract |
|--------|-----------------|---------------------|
| Testability | Hard to mock, tests hit real DB | Easy to mock, fast tests without DB |
| Framework coupling | UseCase depends on Laravel facade | UseCase depends on interface (Clean Architecture) |
| Swappable | Locked to Laravel | Can swap implementation |

**Trade-off:** More boilerplate (interface, service, binding) but follows OneSyntax rule: no framework imports in domain/use case layer.

**When to use DB::Transaction directly:** Small projects, team prefers Laravel conventions, tests already use RefreshDatabase.

### Tasks:
- [x] Create `App\Application\Contracts\TransactionContract` interface
- [x] Create `App\Infrastructure\Services\LaravelTransactionService` implementation
- [x] Wrap `PostSubmitUseCase::execute()` in transaction
- [x] Wrap `SubscribeUseCase::execute()` in transaction
- [x] Bind interface in `AppServiceProvider`
- [x] Update tests

### Files:
| File | Action |
|------|--------|
| `app/Application/Contracts/TransactionContract.php` | CREATE |
| `app/Infrastructure/Services/LaravelTransactionService.php` | CREATE |
| `app/Post/UseCases/PostSubmitUseCase.php` | MODIFY |
| `app/Website/UseCases/SubscribeUseCase.php` | MODIFY |
| `app/Providers/AppServiceProvider.php` | MODIFY |

### Run After:
```bash
composer test
```

---

# Summary

| Phase | Description | Complexity | Status |
|-------|-------------|------------|--------|
| 1-12 | (See completed phases above) | - | Complete |
| 13 | Add domain unit tests | Medium | Complete |
| 14 | Add transaction handling | Medium | Complete |
