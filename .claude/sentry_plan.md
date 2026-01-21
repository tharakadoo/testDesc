# Sentry Integration Plan

## Project Analysis

**Project Type:** Laravel 12 RESTful API with Clean Architecture
**Framework:** Laravel 12.0+ with PHP 8.2+
**Current Logging:** Monolog with stack driver
**Database:** SQLite (development) / MySQL (production-ready)
**Queue System:** Database driver
**Entry Point:** `public/index.php` → `bootstrap/app.php`
**Error Handling:** Minimal (ready for Sentry)
**Sentry Status:** NOT INSTALLED - ready for fresh integration

---

## Architecture Overview

### Application Structure
```
/Users/kasun/Projects/testDesc/
├── app/
│   ├── Application/      (Use cases & domain logic)
│   ├── Http/            (API Controllers)
│   ├── Infrastructure/  (Repositories, Services)
│   ├── Post/            (Post domain)
│   ├── Website/         (Website domain)
│   ├── User/            (User domain)
│   ├── Console/         (Artisan commands)
│   ├── Providers/       (Service providers)
│   └── Mail/            (Email/notification logic)
├── routes/              (API routes)
├── bootstrap/           (Application bootstrap)
├── public/              (Entry point: index.php)
└── config/              (Configuration files)
```

### Current Error Handling
- Exception Handling Configuration: `/bootstrap/app.php`
- Uses Laravel 12's `Exceptions` configuration class
- Currently empty `withExceptions` closure - ready for customization

### Logging Setup
- **Default Channel:** `stack`
- **Log Stack Options:** `single` (configured in .env)
- **Log Level:** `debug` (in .env)
- **Available Handlers:** single, daily, slack, papertrail, stderr, syslog, errorlog, null

---

## Sentry Integration Readiness

**Readiness Level:** HIGH

**Key Advantages:**
1. ✅ Clean Architecture makes error boundaries clear
2. ✅ Logging already configured with Monolog (Sentry compatible)
3. ✅ Service provider pattern allows easy integration
4. ✅ Exception handling hook ready in `bootstrap/app.php`
5. ✅ No conflicting error handling systems
6. ✅ Database queue system (can monitor via Sentry)
7. ✅ Environment variables support for secrets (.env)
8. ✅ Comprehensive test suite (easier to validate integration)
9. ✅ CI/CD pipeline ready (GitHub Actions for deployment)
10. ✅ SonarCloud already integrated (similar tool maturity)

---

## Integration Points

1. **Exception Handler** - `bootstrap/app.php` (withExceptions closure)
2. **Logging Channel** - Add Sentry as new Monolog handler in `config/logging.php`
3. **Service Container** - Register Sentry in `AppServiceProvider.php`
4. **Configuration** - Add Sentry DSN to `.env` files
5. **Queue Monitoring** - Automatic with Sentry SDK
6. **Request Context** - Middleware can add user/request context

---

## Priority Areas for Monitoring

1. **HTTP Requests** - All three endpoints
   - `GET /api/websites`
   - `POST /api/websites/{website}/posts`
   - `POST /api/websites/{website}/subscribe`

2. **Background Jobs** - SendPostEmailsCommand and PostPublished event

3. **Database Transactions** - Via LaravelTransactionService

4. **Validation Errors** - BusinessValidationException

5. **Queue Processing** - Database driver integration

---

## Integration Steps

### 1. Install Sentry Package
```bash
composer require sentry/sentry-laravel
```

### 2. Create Sentry Account & Get DSN
- Go to [sentry.io](https://sentry.io)
- Sign up/login
- Create a new project (select "Laravel" as platform)
- Copy your DSN (looks like: `https://xxxxx@xxxxx.ingest.sentry.io/xxxxx`)
created above - SENTRY_LARAVEL_DSN=https://455766fd91bb148914963636b62d0231@o4510282050502656.ingest.de.sentry.io/4510746754941008

### 3. Add DSN to Environment
Update `.env`:
```
SENTRY_DSN=your_dsn_here
```

Update `.env.example`:
```
SENTRY_DSN=
```

### 4. Publish Sentry Configuration (Optional but recommended)
```bash
php artisan vendor:publish --provider="Sentry\Laravel\ServiceProvider"
```

This creates `/config/sentry.php` for advanced configuration.

### 5. Wire Up Exception Handling

Update `bootstrap/app.php` in the `withExceptions` closure:
```php
->withExceptions(function (Exceptions $exceptions): void {
    \Sentry\captureException($exceptions);
})
```

---

## Features Monitored Automatically

- ✅ Unhandled exceptions and errors
- ✅ HTTP requests (method, URL, status codes)
- ✅ Database queries (with performance tracking)
- ✅ Queue jobs (SendPostEmailsCommand monitoring)
- ✅ Performance (transaction tracing)
- ✅ User context (if authenticated)

---

## Files to Modify

1. `/composer.json` - Add sentry/sentry-laravel
2. `/.env` - Add SENTRY_DSN
3. `/.env.example` - Document SENTRY_DSN
4. `/bootstrap/app.php` - Register exception handler
5. `/config/logging.php` - Add Sentry channel (optional)
6. `/app/Providers/AppServiceProvider.php` - Optional Sentry client binding

---

## Implementation Status

- [ ] Install Sentry package via Composer
- [ ] Create Sentry project and obtain DSN
- [ ] Add SENTRY_DSN to .env and .env.example
- [ ] Publish Sentry configuration
- [ ] Update bootstrap/app.php with exception handler
- [ ] Test Sentry integration
- [ ] Configure performance monitoring (optional)
- [ ] Add custom context in controllers (optional)
