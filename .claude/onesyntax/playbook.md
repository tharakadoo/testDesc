# Test-First Development Playbook

## For Each User Story

### 1. Write Acceptance Criteria (Three Amigos)

```
Given: User has old email "old@example.com"
When: User changes to "new@example.com" and verifies
Then: User can log in with new email
```

### 2. Write Failing Acceptance Test (Developer)

```php
test('user can change email address')
{
    // Test fails because feature doesn't exist yet
}
```

### 3. Write Failing Unit Tests (Developer)

```php
test('email value object validates format')
test('user can change email')
test('EmailChanged event is generated')
// All fail because code doesn't exist
```

### 4. Implement Feature Following TDD

1. Write unit test
2. Write code to pass test
3. Refactor
4. Repeat

### 5. Verify Acceptance Test Passes

- Feature complete when acceptance test passes
- All unit tests also passing
- Feature ready for review

---

## Acceptance Test Maintenance

### Keep Tests Updated

- When requirements change, update acceptance criteria first
- Then update acceptance tests
- Then update implementation
- Never let tests and code drift

### Refactor Tests

- Extract common setup into helpers
- Use test factories for data
- Keep tests readable
- Remove obsolete tests

### Test Coverage

- Aim for 100% of acceptance criteria covered
- Not 100% code coverage (unit tests handle that)
- Every scenario in acceptance criteria has a test

---

## Rule of Thumb

| Test Type | Count per Feature |
|-----------|-------------------|
| E2E tests | 1-5 (critical paths only) |
| Integration tests | 10-20 |
| Unit tests | 50-100 |

---

## Writing Good Tests

### Good Acceptance Criteria

- Written in Given-When-Then format
- Describes behavior, not implementation
- Covers happy path
- Covers error cases
- Covers edge cases
- Uses business language (Ubiquitous Language)
- Concrete and testable
- Understandable by non-technical stakeholders

### Avoid

- Technical jargon ("validates email regex")
- Implementation details ("calls EmailValidator service")
- Vague language ("works correctly", "handles errors")

---

# Core DDD Concepts

## 1. Ubiquitous Language

Everyone uses the same terms.

```typescript
// Domain expert says "Order" - we say "Order"
class Order {
    place(): void
    cancel(): void
    ship(): void
}
```

## 2. Rich Domain Models

Business logic lives in the domain, not in services.

## 3. Entities vs Value Objects

Entities have identity. Value Objects don't.

- **Entities** - things with unique identity (e.g., User, Order)
- **Value Objects** - defined by their attributes (e.g., Money, Email, Address)

> **Rule:** Use entities for things with lifecycle and identity. Use value objects for measurements and descriptions.

## 4. Aggregates

Aggregates enforce consistency boundaries.

> **Rule:** Only modify aggregates through their root. Never expose internal entities.

## 5. Domain Events

Important business moments are modeled explicitly.

> **Rule:** Model important business moments as events. Use events to decouple systems.

---

## Bounded Contexts

Different parts of business have different models.

### Example in E-commerce:

| Context | Models |
|---------|--------|
| **Sales Context** | Order (with payment, shipping), Customer (with billing info) |
| **Inventory Context** | Product (with stock levels), Warehouse (with locations) |
| **Shipping Context** | Shipment (with tracking), Delivery (with routes) |

> **Rule:** Don't try to create one model for everything. Separate contexts have separate models.

---

# Part 2: Clean Architecture

## The Layers

```
┌─────────────────────────────────────┐
│         Presentation Layer          │  (API, Web, CLI)
├─────────────────────────────────────┤
│        Application Layer            │  (Use Cases)
├─────────────────────────────────────┤
│          Domain Layer               │  (Business Logic)
├─────────────────────────────────────┤
│       Infrastructure Layer          │  (DB, External APIs)
└─────────────────────────────────────┘
```

> **Dependency Rule:** Inner layers NEVER depend on outer layers.

---

### Layer 1: Domain Layer (Core)

Pure business logic. No dependencies.

- No framework imports
- No database code
- Just business logic

### Layer 2: Application Layer (Use Cases)

Orchestrates domain objects to fulfill use cases.

- Orchestration only
- No business rules
- No infrastructure details

### Layer 3: Infrastructure Layer

Implements interfaces defined by inner layers.

- Database-specific
- Implements domain interfaces

### Layer 4: Presentation Layer

Handles HTTP, validation, serialization.

- HTTP-specific
- Delegates to use cases

---

## Dependency Injection

Outer layers construct inner layers.

```typescript
// infrastructure/di/container.ts
export function buildContainer(): Container {
    const container = new Container();

    // Infrastructure
    container.register('Database', () => new PostgresClient(config));
    container.register('EventBus', () => new RabbitMQEventBus(config));

    // Repositories (infrastructure implements domain interfaces)
    container.register('OrderRepository', (c) =>
        new PostgresOrderRepository(c.resolve('Database'))
    );

    // Use Cases (application layer)
    container.register('PlaceOrderUseCase', (c) =>
        new PlaceOrderUseCase(
            c.resolve('OrderRepository'),
            c.resolve('EventBus')
        )
    );

    // Controllers (presentation layer)
    container.register('OrderController', (c) =>
        new OrderController(c.resolve('PlaceOrderUseCase'))
    );

    return container;
}
```

---



# Part 3: Test-Driven Development (TDD)

## The TDD Cycle

```
1. RED     - Write failing test
2. GREEN   - Make it pass (simplest way)
3. REFACTOR - Improve design
```

## ZOMBIES for Unit Tests, Given-When-Then for Acceptance

### ZOMBIES Approach (Unit Tests)

| Letter | Meaning |
|--------|---------|
| **Z** | Zero case |
| **O** | One case |
| **M** | Many cases |
| **B** | Boundaries |
| **I** | Interfaces |
| **E** | Exceptions |
| **S** | Simple scenarios first |

### Given-When-Then (Acceptance Tests)

- Business scenarios
- Happy path + error cases
- User perspective
- End-to-end validation

### Both Are Necessary

- Unit tests catch domain logic bugs
- Acceptance tests catch integration bugs
- Together provide comprehensive coverage

---

## Testing Strategy

Different layers need different tests:

### 1. Domain Tests (Unit Tests)

Fast, no dependencies.

```typescript
describe('Money', () => {
    it('should add same currency', () => {
        const a = new Money(10, Currency.USD);
        const b = new Money(5, Currency.USD);

        expect(a.add(b)).toEqual(new Money(15, Currency.USD));
    });

    it('should reject adding different currencies', () => {
        const a = new Money(10, Currency.USD);
        const b = new Money(5, Currency.EUR);

        expect(() => a.add(b)).toThrow(CurrencyMismatchError);
    });
});
```

### 2. Application Tests (Integration Tests)

Test use cases with in-memory implementations.

```typescript
describe('PlaceOrderUseCase', () => {
    it('should place order and publish event', async () => {
        const repository = new InMemoryOrderRepository();
        const eventBus = new InMemoryEventBus();
        const useCase = new PlaceOrderUseCase(repository, eventBus);

        const order = Order.create(customerId);
        order.addItem(product, 1);
        await repository.save(order);

        await useCase.execute(new PlaceOrderCommand(order.id));

        const saved = await repository.findById(order.id);
        expect(saved.status).toBe(OrderStatus.Placed);
        expect(eventBus.published).toContainEvent(OrderPlacedEvent);
    });
});
```

### 3. Infrastructure Tests (Integration Tests)

Test actual database.

```typescript
describe('PostgresOrderRepository', () => {
    let db: PostgresClient;
    let repository: PostgresOrderRepository;

    beforeEach(async () => {
        db = await setupTestDatabase();
        repository = new PostgresOrderRepository(db);
    });

    it('should persist and retrieve order', async () => {
        const order = Order.create(customerId);
        await repository.save(order);

        const retrieved = await repository.findById(order.id);
        expect(retrieved).toEqual(order);
    });

    afterEach(async () => {
        await cleanupTestDatabase(db);
    });
});
```

### 4. End-to-End Tests

Test full flow through HTTP.

```typescript
describe('POST /orders/:id/place', () => {
    it('should place order', async () => {
        const orderId = await createTestOrder();

        const response = await request(app)
            .post(`/orders/${orderId}/place`)
            .expect(200);

        const order = await getOrderFromDatabase(orderId);
        expect(order.status).toBe('placed');
    });
});
```

---

## Putting It All Together

A complete example:

### 1. Start with Domain (TDD)

```typescript
// 1. Write test
it('should calculate order total correctly');

// 2. Implement domain logic
class Order {
    calculateTotal() { ... }
}

// 3. Refactor
```

### 2. Build Use Case

```typescript
class PlaceOrderUseCase {
    // Orchestrate domain
    // Delegates to domain methods
}
```

### 3. Implement Infrastructure

```typescript
class PostgresOrderRepository implements OrderRepository {
    // Database-specific implementation
}
```

### 4. Add Presentation

```typescript
class OrderController {
    // HTTP handling
    // Delegates to use case
}
```

-- to continue from section 3 - enforcement
