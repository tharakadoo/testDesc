[![Quality gate](https://sonarcloud.io/api/project_badges/quality_gate?project=tharakadoo_testDesc)](https://sonarcloud.io/summary/new_code?id=tharakadoo_testDesc)
## OneSyntax TDD Test - Kasun

This is a **full-stack application** with separate frontend and backend repositories.

| Component | Tech Stack | Repository |
|-----------|------------|------------|
| Backend | PHP 8.2+ / Laravel | This repo |
| Frontend | React 19 / Vite / Tailwind | `https://github.com/tharakadoo/testDescFront` |

---

## Backend

### Requirements
- PHP 8.2+
- Composer
- MySQL

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

#### Test Summary

| Type | Command | Count |
|------|---------|-------|
| Domain Layer Tests | `composer test` | 12 |
| Application Layer Tests | `composer test` | 6 |
| Infrastructure Layer Tests | `composer test` | 18 |
| Architecture Tests | `composer test:arch` | 15 |
| **Total** | | **47** |

### API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/websites/{website}/subscribe` | Subscribe to a website |
| POST | `/api/websites/{website}/posts` | Create a post for a website |


### Artisan Commands

```bash
# Send emails for a specific post manually
php artisan posts:send-emails {post_id}
```

---

## Frontend

has own readme.md pls refer.
