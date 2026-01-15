# Implementation Instructions

## Project Overview

This is a **full-stack application** with separate frontend and backend repositories:

- **Backend (this repo):** PHP/Laravel API
- **Frontend:** React application located at `../testDescFront`

## Frontend Repository

The frontend is a React 19 application using:
- **Build Tool:** Vite
- **State Management:** @ngneat/elf with RxJS
- **Styling:** Tailwind CSS
- **Testing:**
  - Unit tests: Vitest + React Testing Library
  - E2E tests: Playwright

### Frontend Commands
```bash
cd ../testDescFront
npm run dev        # Start dev server
npm run build      # Production build
npm run lint       # ESLint
npm run test       # Unit tests (watch mode)
npm run test:run   # Unit tests (single run)
npm run e2e        # Playwright E2E tests
npm run e2e:ui     # Playwright with UI
```

## Backend Testing

Run PHP tests with:
```bash
./vendor/bin/phpunit
```

## General Rules

**IMPORTANT:** Before generating or modifying any code, ALWAYS read and apply ALL rules from the `.claude` folder:
- `.claude/onesyntax/playbook.md` - Rules for development
- `.claude/onesyntax/repo.md` - company approved repo structure
- `.claude/onesyntax/code_reviewer.md` - expected standards

## Code Style

After making code changes, always run the code style fixer on the changed files:

```bash
./vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.php path/to/file1.php path/to/file2.php
```
