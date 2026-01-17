# SonarQube Fix Plan

**Project:** tharakadoo_testDesc
**Quality Gate Status:** ❌ FAILED
**Date:** 2026-01-17

---

## Summary

| Category | Status | Priority |
|----------|--------|----------|
| Critical Code Smells | ✅ DONE | - |
| Minor Code Smells | ✅ DONE | - |
| Code Coverage (63.6% → 80%) | ⏳ Pending | 🔴 High |

---

## Test Coverage Plan

### Issue: New Code Coverage Below Threshold

- **Current:** 63.6%
- **Required:** 80%
- **Gap:** 16.4%

### Files Needing Tests

| File | Lines | Description | Priority |
|------|-------|-------------|----------|
| `app/Application/Listeners/SendPostPublishedEmail.php` | 50 | Event listener - sends emails to subscribers | High |
| `app/Console/Commands/SendPostEmailsCommand.php` | 37 | Artisan command - triggers email sending | High |
| `app/Infrastructure/Services/LaravelEmailService.php` | 18 | Email service wrapper | Medium |
| `app/Infrastructure/Repositories/EloquentPostRepository.php` | 31 | Post repository | Medium |
| `app/Infrastructure/Repositories/EloquentSubscriptionRepository.php` | 20 | Subscription repository | Medium |

### Proposed Tests

> Following OneSyntax Testing Guidelines (`.claude/onesyntax/onesyntax_test.md`)

#### Test 1: `SendPostPublishedEmailTest` (High Priority)
**Location:** `app/Application/Testing/SendPostPublishedEmailTest.php`

**Test Cases** (using `when_[condition]_then_[expected]` naming):
1. `when_website_is_null_then_no_email_sent` - early return branch
2. `when_user_already_received_email_then_user_skipped` - skip logic
3. `when_user_has_not_received_email_then_email_sent_and_marked` - happy path
4. `when_multiple_users_subscribed_then_only_eligible_users_receive_email` - tests both affected and unaffected

**Structure:**
- Preparation: Create Post, Website, Users with factory
- Action: Dispatch `PostPublished` event
- Assertion: Custom helper like `assertEmailSentTo($user)`, `assertEmailNotSentTo($user)`

**Notes:**
- Test with multiple records (not just single user)
- Assert return values, not database
- Use `fresh()` with eager loading for reloading models

---

#### Test 2: `SendPostEmailsCommandTest` (High Priority)
**Location:** `app/Console/Testing/SendPostEmailsCommandTest.php`

**Test Cases:**
1. `when_post_not_found_then_returns_failure_with_error_message`
2. `when_post_has_no_website_then_returns_failure_with_error_message`
3. `when_valid_post_provided_then_dispatches_event_and_returns_success`

**Structure:**
- Test through artisan command (feature test approach)
- Assert command exit code and output messages
- Use `Event::fake()` to verify event dispatched

---

#### Test 3: `LaravelEmailServiceTest` (Medium Priority)
**Location:** `app/Infrastructure/Testing/LaravelEmailServiceTest.php`

**Test Cases:**
1. `when_send_called_then_mail_queued_to_recipient`

**Structure:**
- Use `Mail::fake()`
- Assert `Mail::assertQueued(PostPublishedMail::class, fn($mail) => ...)`

---

#### Test 4: `EloquentPostRepositoryTest` (Medium Priority)
**Location:** `app/Infrastructure/Testing/EloquentPostRepositoryTest.php`

**Test Cases:**
1. `when_create_called_then_post_persisted_with_attributes`
2. `when_find_by_id_with_existing_post_then_returns_post`
3. `when_find_by_id_with_non_existing_id_then_returns_null`
4. `when_update_called_then_post_attributes_changed`
5. `when_delete_called_then_post_removed_and_others_unchanged`

**Notes:**
- Test with multiple posts to verify only target affected
- Use `fresh()` to reload models before assertions
- Assert actual attribute values, not just structure

---

#### Test 5: `EloquentSubscriptionRepositoryTest` (Medium Priority)
**Location:** `app/Infrastructure/Testing/EloquentSubscriptionRepositoryTest.php`

**Test Cases:**
1. `when_user_subscribed_to_website_then_is_subscribed_returns_true`
2. `when_user_not_subscribed_to_website_then_is_subscribed_returns_false`
3. `when_subscribe_called_then_user_attached_to_website`

**Notes:**
- Test with multiple websites to verify subscription is website-specific
- Assert relationship exists via model, not raw DB query

---

## Implementation Checklist

### Test Coverage (DONE)

#### High Priority Tests
- [x] Create `app/Application/Testing/SendPostPublishedEmailTest.php` (4 test cases)
- [x] Create `app/Console/Testing/SendPostEmailsCommandTest.php` (2 test cases - 1 removed due to cascade FK)

#### Medium Priority Tests
- [x] Create `app/Infrastructure/Testing/LaravelEmailServiceTest.php` (1 test case)
- [x] Create `app/Infrastructure/Testing/EloquentPostRepositoryTest.php` (5 test cases)
- [x] Create `app/Infrastructure/Testing/EloquentSubscriptionRepositoryTest.php` (3 test cases)

### Verification
- [x] Run all tests locally (34 passed)
- [ ] Run SonarQube analysis to verify coverage >= 80%

---

## Verification Commands

```bash
# Run all unit tests
php artisan test

# Trigger SonarQube analysis (via CI/CD or manually)
sonar-scanner
```

Expected result: Quality Gate should pass with coverage >= 80%.
