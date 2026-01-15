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
| 13 | Add domain unit tests | Medium | Complete |
| 14 | Add transaction handling | Medium | Complete |

---


## Phase 15: SonarQube Pre-Commit Analysis for Both Repos
**Status:** [x] Complete

### Goal
Set up local linting with auto-fix on commit, failing only on unfixable errors. SonarQube full scan runs in CI/CD.

### Current State

| Component | Backend | Frontend |
|-----------|---------|----------|
| SonarCloud config | Yes | No |
| CI/CD pipeline | Yes (GitHub Actions) | No |
| Git hooks | No | No |
| Linting | PHP-CS-Fixer (manual) | ESLint (manual) |

---

### Phase 15.1: Backend Pre-Commit Hooks (testDesc) ✅ COMPLETE

**15.1.1 Install Husky & lint-staged**
```bash
npm init -y
npm install --save-dev husky lint-staged
npx husky init
```

**15.1.2 Configure lint-staged in package.json**
```json
{
  "lint-staged": {
    "*.php": [
      "./vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.php",
      "./vendor/bin/phpstan analyse --configuration=phpstan.neon --no-progress"
    ]
  }
}
```

**15.1.3 Create pre-commit hook**
File: `.husky/pre-commit`
```bash
npx lint-staged
```

**15.1.4 Create pre-push hook**
File: `.husky/pre-push`
```bash
composer test
composer test:arch
```

---

### Phase 15.2: Frontend Pre-Commit Hooks (testDescFront) ✅ COMPLETE

**15.2.1 Install Husky & lint-staged**
```bash
npm install --save-dev husky lint-staged
npx husky init
```

**15.2.2 Configure lint-staged in package.json**
```json
{
  "lint-staged": {
    "*.{js,jsx}": [
      "eslint --fix"
    ]
  }
}
```

**15.2.3 Create pre-commit hook**
File: `.husky/pre-commit`
```bash
npx lint-staged
```

**15.2.4 Create pre-push hook**
File: `.husky/pre-push`
```bash
npm run test:run
```

---

### Phase 15.3: Frontend SonarCloud Setup ✅ COMPLETE

**15.3.1 Create sonar-project.properties**
```properties
sonar.projectKey=tharakadoo_testDescFront
sonar.organization=tharakadoo
sonar.sources=src
sonar.exclusions=**/node_modules/**,**/dist/**
sonar.javascript.lcov.reportPaths=coverage/lcov.info
```

**15.3.2 Add coverage to Vitest**
```bash
npm install --save-dev @vitest/coverage-v8
```

Update `vite.config.js`:
```js
test: {
  coverage: {
    provider: 'v8',
    reporter: ['text', 'lcov'],
  }
}
```

**15.3.3 Create GitHub Actions workflow**
File: `.github/workflows/sonar.yml`
- Trigger on push/PR to master
- Run `npm run lint`
- Run `npm run test:run -- --coverage`
- Run SonarQube scanner

---

### Files to Create/Modify

#### Backend (testDesc)
| File | Action |
|------|--------|
| `package.json` | Create |
| `.husky/pre-commit` | Create |
| `.husky/pre-push` | Create |

#### Frontend (testDescFront)
| File | Action |
|------|--------|
| `package.json` | Update |
| `.husky/pre-commit` | Create |
| `.husky/pre-push` | Create |
| `sonar-project.properties` | Create |
| `.github/workflows/sonar.yml` | Create |
| `vite.config.js` | Update |

---

### Verification

1. **Test backend pre-commit:**
   ```bash
   cd testDesc
   echo "<?php \$x=1 ;" > test.php  # Bad formatting
   git add test.php
   git commit -m "test"  # Should auto-fix and commit
   ```

2. **Test frontend pre-commit:**
   ```bash
   cd testDescFront
   echo "const x = 1" > src/test.js  # Missing semicolon
   git add src/test.js
   git commit -m "test"  # Should auto-fix and commit
   ```

3. **Test pre-push (should fail if tests fail):**
   ```bash
   git push  # Runs tests before push
   ```

4. **Verify SonarCloud:**
   - Push to GitHub
   - Check SonarCloud dashboard for both projects

---

