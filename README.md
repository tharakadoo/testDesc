[![Quality gate](https://sonarcloud.io/api/project_badges/quality_gate?project=tharakadoo_testDesc)](https://sonarcloud.io/summary/new_code?id=tharakadoo_testDesc)
## OneSyntax TDD Test - Kasun

This is a **full-stack application** with separate frontend and backend repositories.

| Component | Tech Stack | Repository |
|-----------|------------|------------|
| Backend | PHP 8.2+ / Laravel | This repo |
| Frontend | React 19 / Vite / Tailwind | https://github.com/tharakadoo/testDescFront |

---

## Backend

### Requirements
- PHP 8.2+
- Composer
- MySQL

### Installation

```bash
# Clone the repository
git clone https://github.com/tharakadoo/testDesc
cd testDesc

# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env

# Run migrations
php artisan migrate

# Seed sample data
php artisan db:seed
```

### Running the Application

```bash
# Start the development server
php artisan serve

# Start the queue worker (required for background email processing)
php artisan queue:work
```

### Git Hooks (Husky)

This project uses **Husky** for automated Git hooks to enforce code quality:

**Pre-commit Hook** (runs on `git commit`)
- PHP code style fixes (php-cs-fixer)
- Static analysis (PHPStan)

**Pre-push Hook** (runs on `git push`)
- All unit and integration tests: `composer test`
- Architecture tests: `composer test:arch`

### Running Tests

```bash
# Run all tests
composer test

# Run architecture tests only
composer test:arch
```

#### Test Summary

| Type | Command | Count |
|------|---------|-------|
| Domain Layer Tests | `composer test` | 12 |
| Application Layer Tests | `composer test` | 6 |
| Infrastructure Layer Tests | `composer test` | 18 |
| Architecture Tests | `composer test:arch` | 15 |
| **Total** | | **47** |


### Code Quality & CI/CD

#### SonarQube Integration

This project uses **SonarCloud** for continuous code quality analysis:

**View project:** [SonarCloud Dashboard](https://sonarcloud.io/summary/new_code?id=tharakadoo_testDesc)

#### GitHub Actions

Automated CI/CD pipeline runs on every push:

**Workflow:** `.github/workflows/` (configured with ArchUnit tests)

**What runs:**
1. PHP linting and syntax checks
2. All unit, integration and Architecture tests
3. SonarCloud quality analysis
4. Code coverage report generation

**Quality Gate Checks:**
- ✅ All tests must pass
- ✅ Code must pass linting
- ✅ SonarQube quality gate status

### API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/websites/{website}/subscribe` | Subscribe to a website |
| POST | `/api/websites/{website}/posts` | Create a post for a website |

---

## Frontend

The frontend is in a separate repository with its own documentation.

**Repository:** [tharakadoo/testDescFront](https://github.com/tharakadoo/testDescFront)

Please refer to the frontend repository's README.md for setup and development instructions.
