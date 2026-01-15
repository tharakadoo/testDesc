# Frontend Implementation Plan

have to create new repo for front end. ../testDescFront. 

## Overview
Add React frontend with @ngneat/elf state management and optimistic UI for website subscription.

**Tech Stack:**
- React 18 + @ngneat/elf + @ngneat/use-observable + RxJS
- Vite with @vitejs/plugin-react
- Tailwind CSS (already configured)
- Vitest for frontend testing

---

## Phase 2: Frontend Setup

### 2.1 Install Dependencies
```bash
npm install react react-dom @vitejs/plugin-react @ngneat/elf @ngneat/use-observable rxjs
npm install -D @testing-library/react @testing-library/jest-dom vitest jsdom @testing-library/user-event
```

### 2.2 Update Vite Config
**File:** `vite.config.js`
- Add React plugin
- Add Vitest test config
- Change entry point to `app.jsx`

### 2.3 Update package.json
- Add test scripts: `"test": "vitest"`, `"test:run": "vitest run"`

### 2.4 Create Test Setup
**File:** `resources/js/setupTests.js`

---

## Phase 3: Frontend Store (TDD)

### 3.1 Create Store Test
**File:** `resources/js/stores/subscriptionStore.test.js`
- Test initial state
- Test setWebsites action
- Test optimistic UI flow (startSubscription -> success/error)

### 3.2 Implement Store
**File:** `resources/js/stores/subscriptionStore.js`
- State: websites, isLoadingWebsites, isSubscribing, subscriptionStatus, errorMessage, optimisticSubscription
- Actions: setWebsites, startSubscription (optimistic), subscriptionSuccess, subscriptionError

---

## Phase 4: API Service (TDD)

### 4.1 Create API Test
**File:** `resources/js/services/api.test.js`

### 4.2 Implement API Service
**File:** `resources/js/services/api.js`
- `websiteApi.getAll()` - GET /api/websites
- `subscriptionApi.subscribe(websiteId, email)` - POST /api/websites/{id}/subscribe

---

## Phase 5: Custom Hook (TDD)

### 5.1 Create Hook Test
**File:** `resources/js/hooks/useSubscription.test.js`

### 5.2 Implement Hook
**File:** `resources/js/hooks/useSubscription.js`
- Uses @ngneat/use-observable to connect React to elf store
- Exposes: websites, isLoadingWebsites, isSubscribing, subscriptionStatus, errorMessage
- Actions: loadWebsites(), subscribe(websiteId, email), clearStatus()

---

## Phase 6: Components (TDD)

### 6.1 Create Component Test
**File:** `resources/js/components/SubscriptionForm.test.jsx`
- Test: renders form elements
- Test: loads websites on mount
- Test: shows success immediately on submit (optimistic)
- Test: reverts and shows error on API failure

### 6.2 Implement Components
**File:** `resources/js/components/SubscriptionForm.jsx`
- Website dropdown (populated from API)
- Email input with validation
- Subscribe button with loading state
- Success/error messages

**File:** `resources/js/components/App.jsx`
- Main app wrapper

### 6.3 Update Entry Point
**File:** `resources/js/app.jsx` (rename from app.js)
- Mount React app to #app div

---

## Phase 7: Laravel Integration

### 7.1 Create Blade View
**File:** `resources/views/app.blade.php`
- Include @viteReactRefresh
- Include @vite directives for CSS and JSX

### 7.2 Update Web Route
**File:** `routes/web.php`
- Return app view for root route

### 7.3 Run & Test
```bash
npm run dev
php artisan serve
# Open http://localhost:8000
```

---

## File Summary

| Action | File |
|--------|------|
| CREATE | `app/Website/Testing/GetAllWebsitesUseCaseTest.php` |
| MODIFY | `app/Website/Repositories/WebsiteRepositoryInterface.php` |
| MODIFY | `app/Infrastructure/Repositories/EloquentWebsiteRepository.php` |
| CREATE | `app/Website/UseCases/GetAllWebsitesUseCase.php` |
| CREATE | `app/Http/Controllers/WebsiteController.php` |
| MODIFY | `routes/api.php` |
| MODIFY | `vite.config.js` |
| MODIFY | `package.json` |
| CREATE | `resources/js/setupTests.js` |
| CREATE | `resources/js/stores/subscriptionStore.js` |
| CREATE | `resources/js/stores/subscriptionStore.test.js` |
| CREATE | `resources/js/services/api.js` |
| CREATE | `resources/js/services/api.test.js` |
| CREATE | `resources/js/hooks/useSubscription.js` |
| CREATE | `resources/js/hooks/useSubscription.test.js` |
| CREATE | `resources/js/components/App.jsx` |
| CREATE | `resources/js/components/SubscriptionForm.jsx` |
| CREATE | `resources/js/components/SubscriptionForm.test.jsx` |
| CREATE | `resources/js/app.jsx` |
| CREATE | `resources/views/app.blade.php` |
| MODIFY | `routes/web.php` |

---

## Optimistic UI Pattern

```
User clicks Subscribe
       |
       v
+------------------+
| startSubscription|  <- Immediately show success (optimistic)
| status: success  |
| isSubscribing: T |
+--------+---------+
         |
         v
    API Request
         |
    +----+----+
    |         |
 Success    Error
    |         |
    v         v
+--------+ +------------+
|Confirm | |Revert to   |
|success | |error state |
+--------+ +------------+
```

---

## Verification

### Backend Tests
```bash
composer test
```

### Frontend Tests
```bash
npm test
```

### Manual Testing
1. Start Laravel: `php artisan serve`
2. Start Vite: `npm run dev`
3. Open http://localhost:8000
4. Select website from dropdown
5. Enter email
6. Click Subscribe
7. Verify success message appears immediately
8. Try duplicate email - verify error message appears
