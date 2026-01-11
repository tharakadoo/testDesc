# Implementation Instructions

## General Rules

**IMPORTANT:** Before generating or modifying any code, ALWAYS read and apply ALL rules from the `.claude` folder:
- `.claude/testing.md` - Testing conventions and patterns
- `.claude/clean_code.md` - Clean code principles
- `.claude/codebase.md` - Codebase structure and conventions
- `.claude/production.md` - Production guidelines
- `.claude/onesyntax_guide.md` - Syntax guidelines

## Code Style

After making code changes, always run the code style fixer on the changed files:

```bash
./vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.php path/to/file1.php path/to/file2.php
```
