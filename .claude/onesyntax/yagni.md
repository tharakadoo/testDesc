# YAGNI Exceptions

## When NOT to Follow YAGNI

Build upfront (don't wait) for:

1.  Architectural boundaries
    *   Domain layer isolated
    *   Clean Architecture layers
    *   Repository pattern
    *   No framework in domain
    *   *Why: Refactoring across layers is expensive and error-prone.*

2.  Security concerns
    *   Authentication/authorization
    *   Data encryption
    *   Input validation
    *   SQL injection prevention
    *   *Why: Security bugs are expensive and dangerous.*

3.  Data integrity
    *   Database constraints
    *   Foreign keys
    *   Unique indexes
    *   Not null constraints
    *   *Why: Bad data is hard to fix later.*

4.  Scalability bottlenecks
    *   Database indexes on foreign keys
    *   Proper pagination
    *   Efficient queries
    *   *Why: Performance problems under load are hard to fix.*

5.  Audit requirements
    *   Who changed what when
    *   Soft deletes (if needed for compliance)
    *   Event logs
    *   *Why: Can't retroactively add audit trail.*

## When to Follow YAGNI

Don't build until needed:

1.  Performance optimizations
    *   Caching (unless proven bottleneck)
    *   Database denormalization
    *   Query optimization
    *   CDN setup
    *   *Why: Premature optimization. Measure first.*

2.  Feature flexibility
    *   "What if we need to support X in the future?"
    *   Configurable everything
    *   Plugin systems
    *   Extensibility hooks
    *   *Why: Requirements will change anyway. Build for today.*

3.  Generalization
    *   Building frameworks
    *   Supporting every edge case
    *   Configurable workflows
    *   Generic solutions
    *   *Why: Specific solutions simpler to maintain.*

4.  Scale beyond current needs
    *   Supporting millions of users (when you have 100)
    *   Microservices (when monolith works)
    *   Kafka (when background jobs suffice)
    *   Kubernetes (when single server works)
    *   *Why: Complexity without benefit.*

### Examples

#### Example 1: Simple → Rich Evolution

**Version 1 (Simple - Day 1):**

```php
// Simple user profile update
class User
{
public string $name;
public string $email;
}

class UpdateUserProfile
{
public function execute(UserId $userId, string $name, string $email): void
{
$user = $this->userRepository->find($userId);
$user->name = $name;
$user->email = $email;
$this->userRepository->save($user);
}
}
```

**Version 2 (Adding Value Objects - Month 2):**

```php
// Email validation becoming important
class User
{
public string $name;
public Email $email; // Value object with validation
}

class UpdateUserProfile
{
public function execute(UserId $userId, string $name, string $email): void
{
$user = $this->userRepository->find($userId);
$user->name = $name;
$user->email = Email::fromString($email); // Validation here
$this->userRepository->save($user);
}
}
```

**Version 3 (Rich Entity - Month 6):**

```php
// Complex rules emerging:
// - Email verification required
// - Name changes tracked
// - Profile completeness score

class User
{
private PersonName $name;
private Email $email;
private EmailVerificationStatus $emailStatus;
private ProfileCompleteness $completeness;

    public function updateEmail(Email $newEmail): void
    {
        if ($this->email->equals($newEmail)) {
            return; // No change
        }
        
        $this->email = $newEmail;
        $this->emailStatus = EmailVerificationStatus::pending();
        $this->recordEvent(new EmailChanged($this->id, $newEmail));
    }
    
    public function updateName(PersonName $newName): void
    {
        $this->name = $newName;
        $this->completeness = $this->calculateCompleteness();
    }
}
```

#### Example 2: Staying Simple

**V1 (Simple):**

```php
class Setting
{
public string $key;
public string $value;
}

class UpdateSetting
{
public function execute(string $key, string $value): void
{
$setting = $this->settingRepository->findByKey($key);
$setting->value = $value;
$this->settingRepository->save($setting);
}
}
```

**2 years later... still simple:**

*   No complex business rules emerged
*   No need for rich domain model
*   Simple CRUD suffices
*   Still in domain layer, just simple implementation
*   This is fine! Not everything needs to be complex.

### Common Mistakes

#### Mistake 1: Premature Complexity

*   **Problem:** Building complex domain model for simple CRUD.

    **Example:**
    ```php
    // Overkill for simple tag management
    class Tag
    {
    public TagName $name;
    public TagColor $color;
    public TagCategory $category;
    public TagUsageStatistics $statistics;
    // ... 500 lines of behavior for something that's just name + color
    }
    ```
*   **Solution:** Start simpler.

    ```php
    class Tag
    {
    public string $name;
    public string $color;
    }
    ```

#### Mistake 2: Anemic Everywhere

*   **Problem:** No entities ever have behavior, everything in services.

    **Example:**
    ```php
    // Order is complex domain concept but entity is anemic
    class Order
    {
    public $items;
    public $total;
    public $status;
    }

    // All logic in services
    class OrderCalculationService { }
    class OrderValidationService { }
    class OrderStatusService { }
    ```
*   **Solution:** Move behavior to entity when it's business logic.

#### Mistake 3: Violating Boundaries for Simplicity

*   **Problem:** "It's simpler to just query the database directly from the controller."

    **Example:**
    ```php
    // WRONG - Skipping layers for "simplicity"
    class OrderController
    {
    public function show($id)
    {
    $order = DB::table('orders')->find($id); // Direct DB access
    return view('orders.show', compact('order'));
    }
    }
    ```
*   **Solution:** Simplicity within layers, not by skipping layers.

    ```php
    // RIGHT - Simple implementation, proper structure
    class OrderController
    {
    public function show($id)
    {
    $order = $this->orderRepository->find(OrderId::fromString($id));
    return view('orders.show', compact('order'));
    }
    }
    ```