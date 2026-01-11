
## Code Review Checklist

### 1. Domain Layer

- [ ] Business logic in domain (not services)
- [ ] Rich models (not anemic)
- [ ] Value objects for measurements
- [ ] Proper aggregate boundaries
- [ ] Domain events for important moments

### 2. Application Layer

- [ ] Use cases orchestrate only
- [ ] No business logic in use cases
- [ ] Proper dependency injection
- [ ] Returns DTOs (not domain entities)

### 3. Infrastructure

- [ ] Implements domain interfaces
- [ ] No business logic
- [ ] Proper mapping
- [ ] Transaction handling

### 4. Presentation

- [ ] Thin controllers
- [ ] Input validation
- [ ] Proper error handling
- [ ] No business logic

### 5. Testing

- [ ] Unit tests for domain (>90%)
- [ ] Integration tests for use cases
- [ ] Tests describe behavior
- [ ] No mocking domain models

### 6. Code Quality

- [ ] Clear naming
- [ ] Functions <50 lines
- [ ] Single responsibility
- [ ] No code duplication
- [ ] Every module has a README

---
