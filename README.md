[![Quality gate](https://sonarcloud.io/api/project_badges/quality_gate?project=tharakadoo_testDesc)](https://sonarcloud.io/summary/new_code?id=tharakadoo_testDesc)
## OneSyntax TDD Test - Kasun

This is a **full-stack application** with separate frontend and backend repositories.

| Component | Tech Stack | Repository |
|-----------|------------|------------|
| Backend | PHP 8.2+ / Laravel | This repo |
| Frontend | React 19 / Vite / Tailwind | `../testDescFront` |

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

---

## Frontend

has own readme.md pls refer.
