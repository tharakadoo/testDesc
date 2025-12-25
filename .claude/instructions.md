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

## Linear Card Workflow

When working on a Linear card:
1. Create a folder `.claude/cards/[card-id]/` (e.g., `.claude/cards/LD-630/`)
2. Save planning notes to `plan.md` in that folder
3. Save implementation details to `implementation.md` in that folder

## Planning Phase

**IMPORTANT:** Do NOT start implementation until the user explicitly says "start implementation". Focus on:
1. Understanding and clarifying requirements
2. Creating a clear, detailed plan
3. Getting user approval on the plan

When working on a Linear card, follow this structured approach:

### 1. Explain the Issue
- Clearly explain what the Linear card is asking for
- What is the current behavior?
- What is the expected/desired behavior?

### 2. How to Reproduce
- Provide steps to reproduce the issue or see the current behavior
- Include any specific data setup required

### 3. How to Manually Test Current Implementation
- Explain how to test the existing functionality
- Specify the exact endpoints, methods, and tools needed (e.g., API client, GraphQL playground, browser, etc.)
- Provide example request/response if applicable

### 4. Find Existing Related Tests
- Search for existing tests related to the feature
- Identify the most relevant test file
- If no tests exist, ask the user whether to create a new test before proceeding

### 5. Implementation
- Explain what you're doing and why at each step
- Describe how the new code behaves
- Keep the user informed throughout the process

### 6. How to Manually Test New Implementation
- Provide clear steps to test the new functionality
- Include example requests and expected responses
- Specify any setup required for testing

### 7. Card Description (On Completion)

When finishing work on a card, create a `description.md` file in the card folder (e.g., `.claude/cards/LD-630/description.md`). This description will be posted as a comment on the Linear card.

**Format:**

```markdown
## Changes Completed

* Brief summary of what was implemented
* List key changes made

### API Overview (if applicable)

| Operation | Endpoint | Description |
| --------- | -------- | ----------- |
| Query     | endpoint | Description |
| Mutation  | endpoint | Description |

## Validation Rules (if applicable)

* List validation rules
* Include allowed values and formats

## How to Test

Test using [tool/method].

### Query Example (if applicable)

```graphql
query MyQuery {
  ...
}
```

### Mutation Examples (if applicable)

#### 1. Example name

```graphql
mutation MyMutation {
  ...
}
```

#### 2. Another example

```graphql
mutation MyMutation {
  ...
}
```
```

**Guidelines:**
- Use clear headings with `##` and `###`
- Include working examples that can be copy-pasted
- Use tables for structured information
- Keep explanations concise
- Include all common use cases (create, update, delete, query)

