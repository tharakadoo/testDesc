# Project Structure Issues & Improvement Plan

## Issues Identified

### 1. Duplicate Contracts (DRY Violation)

**Problem:** `EmailServiceContract` exists in two locations with different signatures:
- `app/Application/Contracts/EmailServiceContract.php` - returns `bool`
- `app/Post/Contracts/EmailServiceContract.php` - returns `void`

**OneSyntax Rule Violated:** DRY (Don't Repeat Yourself)

**Fix:** Remove duplicate, keep one contract in `Application/Contracts` or the domain that owns it.

---

### 2. Test Naming Convention Violations

**Problem:** Test names use `test_` prefix instead of `when_then` pattern.

**Current:**
```php
test_when_missing_required_field_then_throws_validation_exception
test_when_create_post_then_post_created
test_when_post_created_then_emails_sent_to_subscribers
```

**Should be:**
```php
when_missing_required_field_then_throws_validation_exception
when_post_created_then_post_created
when_post_created_then_emails_sent_to_subscribers
```

**OneSyntax Rule Violated:** Test Naming Convention (`when_[condition]_then_[expected_result]`)

**Files Affected:**
- `app/Post/Testing/PostSubmitUseCaseTest.php`
- `app/Website/Testing/SubscribeUseCaseTest.php`

---

### 3. Missing `#[Test]` Attribute

**Problem:** Tests use `test_` prefix instead of `#[Test]` attribute.

**OneSyntax Rule Violated:** Boy Scout Rule - "Replace old annotations to `#[Test]` attributes"

**Files Affected:**
- `app/Post/Testing/PostSubmitUseCaseTest.php`
- `app/Website/Testing/SubscribeUseCaseTest.php`

---

### 4. Step-Down Rule Violation in Tests

**Problem:** Protected helper methods are not ordered by first call appearance.

**Current order in `PostSubmitUseCaseTest`:**
1. `tearDown()`
2. Test methods
3. `missingFieldProvider()` (data provider)
4. `createUseCase()` - called in test 1
5. `buildPostData()` - called in test 2
6. `createMockPost()` - called in test 2
7. `createMockPostWithEmailTracking()` - called in test 3
8. `createMockPostWithExistingEmailedUser()` - called in test 4
9. `assertValidationException()` - called in test 1
10. `assertPostCreated()` - called in tests 2, 3, 4

**Should be (by first call appearance):**
1. `tearDown()`
2. Test methods
3. `createUseCase()` - first called in test 1
4. `assertValidationException()` - first called in test 1
5. `buildPostData()` - first called in test 2
6. `createMockPost()` - first called in test 2
7. `assertPostCreated()` - first called in test 2
8. `createMockPostWithEmailTracking()` - first called in test 3
9. `createMockPostWithExistingEmailedUser()` - first called in test 4
10. `missingFieldProvider()` (data provider - at end)

**OneSyntax Rule Violated:** Step-Down Rule

---

### 5. Technical Words in Variable Names

**Problem:** Using generic names like `$data`, `$postData`, `$subscribeData`

**Current:**
```php
$postData = $this->buildPostData();
$useCase->execute($postData);
```

**Better:**
```php
$submitPost = $this->buildSubmitPost();
$useCase->execute($submitPost);
```

**OneSyntax Rule Violated:** Self-Explanatory Variable Names, Avoid Technical Words

---

### 6. Inline Comment in DTO

**Problem:** `SubmitPostData.php` has a docblock comment `@throws ValidationException`

**Current:**
```php
/**
 * @throws ValidationException
 */
public static function fromArray(array $data): self
```

**Should be:** Remove the docblock - code should be self-documenting.

**OneSyntax Rule Violated:** No Inline Comments

---

### 7. SubscriptionRepository is Too Fat (SRP Violation)

**Problem:** `SubscriptionRepositoryInterface` has methods that don't belong to subscriptions:
- `findWebsite()` - belongs to WebsiteRepository
- `findOrCreateUser()` - belongs to UserRepository

**OneSyntax Rule Violated:** Single Responsibility Principle, Repository Pattern

**Fix:**
- Move `findWebsite()` to a `WebsiteRepositoryInterface`
- Move `findOrCreateUser()` to a `UserRepositoryInterface`

---

### 8. Test Data Coverage (1 to Many)

**Problem:** Some tests only verify with 1 record instead of multiple.

**Example in `PostSubmitUseCaseTest::test_when_create_post_then_post_created`:**
- Tests with 0 subscribers (empty collection)
- Should test with multiple subscribers to ensure proper behavior

**OneSyntax Rule Violated:** Test Data Coverage (1 to Many)

---

### 9. Negative Test Ordering

**Problem:** Positive tests appear before negative tests.

**Current order in `SubscribeUseCaseTest`:**
1. `test_when_missing_required_field...` (negative)
2. `test_when_website_not_found...` (negative)
3. `test_when_already_subscribed...` (negative)
4. `test_when_subscribe_then_subscription_created` (positive)

This is correct! But `PostSubmitUseCaseTest` has:
1. `test_when_missing_required_field...` (negative)
2. `test_when_create_post...` (positive) ← should come later
3. `test_when_post_created_then_emails_sent...` (positive)
4. `test_when_user_already_received_email...` (negative) ← should come earlier

**OneSyntax Rule Violated:** Test Ordering - Negative Scenarios First

---

### 10. Use Case Returns Formatted Output

**Problem:** `SubscribeUseCase` returns an array with formatted structure:
```php
return [
    'user' => $user,
    'website' => $website,
    'subscribed' => true,
];
```

**OneSyntax Rule:** Actions return domain data, not formatted output.

**Better:** Return a domain object or just the subscription entity.

---

### 11. Framework Import in Domain Layer

**Problem:** `PostSubmitUseCase` imports `Illuminate\Support\Facades\Cache` - a Laravel-specific class.

**OneSyntax Rule Violated:** Clean Architecture - "No framework imports in domain layer"

**Fix:** Inject a `CacheContract` interface instead of using the facade directly.

---

### 12. Missing Entities in User Domain

**Problem:** User domain only has `Entities/User.php`. Should follow same structure as other domains if needed:
- Contracts
- Repositories
- DataTransferObjects
- UseCases
- Testing

However, if User is simple CRUD with no business logic, this is acceptable per YAGNI.

---

---

# Execution Plan

## Phase 1: Remove Duplicate Contracts
**Status:** [x] Complete

### Tasks:
- [x] Delete `app/Application/Contracts/EmailServiceContract.php`
- [x] Verify `app/Post/Contracts/EmailServiceContract.php` is the only one used
- [x] Update any imports referencing the deleted contract
- [x] Fixed `EmailService.php` return type from `bool` to `void` to match contract
- [x] Removed unused `Log` import from `EmailService.php`

### Files:
| File | Action |
|------|--------|
| `app/Application/Contracts/EmailServiceContract.php` | DELETE |

### Run After:
```bash
composer test
```

---

## Phase 2: Extract Cache Dependency from UseCase
**Status:** [x] Complete

### Tasks:
- [x] Create `App\Application\Contracts\CacheContract` interface
- [x] Create `App\Infrastructure\Services\LaravelCacheService` implementation
- [x] Update `PostSubmitUseCase` to inject `CacheContract` instead of using facade
- [x] Bind interface in `AppServiceProvider`
- [x] Update tests to mock the new interface

### Files:
| File | Action |
|------|--------|
| `app/Application/Contracts/CacheContract.php` | CREATE |
| `app/Infrastructure/Services/LaravelCacheService.php` | CREATE |
| `app/Post/UseCases/PostSubmitUseCase.php` | MODIFY |
| `app/Providers/AppServiceProvider.php` | MODIFY |
| `app/Post/Testing/PostSubmitUseCaseTest.php` | MODIFY |

### Run After:
```bash
composer test
```

---

## Phase 3: Split SubscriptionRepositoryInterface (SRP)
**Status:** [x] Complete

### Tasks:
- [x] Create `App\Website\Repositories\WebsiteRepositoryInterface` with `find()` method
- [x] Create `App\User\Repositories\UserRepositoryInterface` with `findOrCreate()` method
- [x] Remove `findWebsite()` and `findOrCreateUser()` from `SubscriptionRepositoryInterface`
- [x] Create `EloquentWebsiteRepository` implementation
- [x] Create `EloquentUserRepository` implementation
- [x] Update `EloquentSubscriptionRepository` to only handle subscriptions
- [x] Update `SubscribeUseCase` to inject the new repositories
- [x] Bind new interfaces in `AppServiceProvider`
- [x] Update tests

### Files:
| File | Action |
|------|--------|
| `app/Website/Repositories/WebsiteRepositoryInterface.php` | CREATE |
| `app/User/Repositories/UserRepositoryInterface.php` | CREATE |
| `app/Website/Repositories/SubscriptionRepositoryInterface.php` | MODIFY |
| `app/Infrastructure/Repositories/EloquentWebsiteRepository.php` | CREATE |
| `app/Infrastructure/Repositories/EloquentUserRepository.php` | CREATE |
| `app/Infrastructure/Repositories/EloquentSubscriptionRepository.php` | MODIFY |
| `app/Website/UseCases/SubscribeUseCase.php` | MODIFY |
| `app/Providers/AppServiceProvider.php` | MODIFY |
| `app/Website/Testing/SubscribeUseCaseTest.php` | MODIFY |

### Run After:
```bash
composer test
```

---

## Phase 4: Fix Test Naming & Attributes
**Status:** [x] Complete

### Tasks:
- [x] Remove `test_` prefix from all test methods
- [x] Add `#[Test]` attribute to all test methods
- [x] Ensure test names follow `when_[condition]_then_[expected_result]` pattern

### Files:
| File | Action |
|------|--------|
| `app/Post/Testing/PostSubmitUseCaseTest.php` | MODIFY |
| `app/Website/Testing/SubscribeUseCaseTest.php` | MODIFY |

### Run After:
```bash
composer test
```

---

## Phase 5: Apply Step-Down Rule to Tests
**Status:** [x] Complete

### Tasks:
- [x] Reorder protected methods in `PostSubmitUseCaseTest` by first call appearance
- [x] Reorder protected methods in `SubscribeUseCaseTest` by first call appearance
- [x] Move data providers to end of file

### Files:
| File | Action |
|------|--------|
| `app/Post/Testing/PostSubmitUseCaseTest.php` | MODIFY |
| `app/Website/Testing/SubscribeUseCaseTest.php` | MODIFY |

### Run After:
```bash
composer test
```

---

## Phase 6: Fix Test Ordering (Negative First)
**Status:** [x] Complete

### Tasks:
- [x] Reorder test methods in `PostSubmitUseCaseTest`: negative scenarios before positive
- [x] Verify `SubscribeUseCaseTest` already follows correct order (confirmed)

### Files:
| File | Action |
|------|--------|
| `app/Post/Testing/PostSubmitUseCaseTest.php` | MODIFY |

### Run After:
```bash
composer test
```

---

## Phase 7: Rename Variables (Domain-Specific Names)
**Status:** [x] Complete

### Tasks:
- [x] Replace `$postData` with `$submitPost` in PostSubmitUseCaseTest
- [x] Rename `buildPostData()` to `buildSubmitPost()` in PostSubmitUseCaseTest
- [x] Replace `$subscribeData` with `$subscriptionRequest` in SubscribeUseCaseTest
- [x] Replace `$data` with `$submitPost` in PostSubmitUseCase::execute()
- [x] Replace `$data` with `$subscriptionRequest` in SubscribeUseCase::execute()

### Files:
| File | Action |
|------|--------|
| `app/Post/Testing/PostSubmitUseCaseTest.php` | MODIFY |
| `app/Website/Testing/SubscribeUseCaseTest.php` | MODIFY |
| `app/Post/UseCases/PostSubmitUseCase.php` | MODIFY |
| `app/Website/UseCases/SubscribeUseCase.php` | MODIFY |

### Run After:
```bash
composer test
```

---

## Phase 8: Remove Docblock Comments
**Status:** [x] Complete

### Tasks:
- [x] Remove `@throws ValidationException` docblock from `SubmitPostData.php`

### Files:
| File | Action |
|------|--------|
| `app/Post/DataTransferObjects/SubmitPostData.php` | MODIFY |

### Run After:
```bash
composer test
```

---

## Phase 9: Refactor UseCase Return Types
**Status:** [x] Complete

### Tasks:
- [x] Create a `Subscription` entity or value object if needed
- [x] Update `SubscribeUseCase` to return domain data instead of formatted array
- [x] Update controller to handle new return type
- [x] Update tests

### Files:
| File | Action |
|------|--------|
| `app/Website/UseCases/SubscribeUseCase.php` | MODIFY |
| `app/Http/Controllers/SubscriptionController.php` | MODIFY |
| `app/Website/Testing/SubscribeUseCaseTest.php` | MODIFY |

### Run After:
```bash
composer test
```

---

# Summary

| Phase | Description | Complexity |
|-------|-------------|------------|
| 1 | Remove duplicate contracts | Low |
| 2 | Extract cache dependency | Medium |
| 3 | Split repository (SRP) | High |
| 4 | Fix test naming & attributes | Low |
| 5 | Apply step-down rule | Low |
| 6 | Fix test ordering | Low |
| 7 | Rename variables | Low |
| 8 | Remove docblocks | Low |
| 9 | Refactor return types | Medium |
