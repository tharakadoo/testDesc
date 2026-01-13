# SonarQube Analysis for tharakadoo_testDesc

## Quality Gate Status: ❌ FAILED

The main issue causing the failure:

| Condition | Required | Actual | Status |
|-----------|----------|--------|--------|
| **New Code Coverage** | ≥ 80% | **0.0%** | ❌ Failed |

Your new code has **0% test coverage**, but SonarQube requires at least 80%.

---

## All Issues (13 total)

### Critical (4)
| File | Issue |
|------|-------|
| `app/Website/Testing/SubscribeUseCaseTest.php:161` | Duplicate literal "Expected ValidationException not thrown" (3 times) |
| `app/Website/Testing/SubscribeUseCaseTest.php:47` | Duplicate literal "user@example.com" (8 times) |
| `config/database.php:49` | Duplicate literal "127.0.0.1" (5 times) |
| `config/logging.php:63` | Duplicate literal "logs/laravel.log" (3 times) |

### Major (7)
- `bootstrap/app.php:14` - Unused parameter `$middleware`
- `bootstrap/app.php:17` - Unused parameter `$exceptions`
- `tests/Feature/ExampleTest.php:5` - Commented out code
- `resources/css/app.css:3-6` - Unknown `@source` at-rule (4 issues)

### Minor (2)
- `resources/js/bootstrap.js:2,4` - Use `globalThis` instead of `window`

---

## To Fix the Quality Gate

1. **Add tests** for your new code to achieve ≥80% coverage
2. **Define constants** for the duplicate string literals
3. Clean up unused parameters and commented code
