# OneSyntax Testing Guidelines

## Test Naming
- Use `when_[condition]_then_[expected_result]` pattern
- Avoid vague words: "correct", "proper", "valid", "right", "fails"
- Describe actual behavior/outcome

## Test Structure
1. **Preparation** - Set up test data
2. **Action** - Execute code being tested
3. **Assertion** - Single custom assertion helper

## Assertions
- One custom assertion method per test (encapsulates all assertions)
- Assert actual values, not just structure
- Assert against return values, not database (`assertDatabaseHas` discouraged)
- No logic in tests - use hardcoded expected values
- When testing that action X affects only target Y, assert both:
  - Target Y was affected (e.g., record removed/changed)
  - Other records were NOT affected (unchanged)
- Use collection methods like `hasAllowance`, `hasBreakdown` instead of raw DB queries
- Use `fresh()` with eager loading to reload models before assertions

```php
// Bad - raw DB query
$count = EmploymentAllowanceBreakdown::query()
    ->where('allowance_breakdown_type_id', $type->getKey())
    ->where('should_deduct', false)
    ->count();
$this->assertGreaterThan(0, $count);

// Good - ask the model
$this->assertTrue(
    $this->company->fresh(['employments.allowances.breakdowns'])->employments->some(
        fn (Employment $employment) => $employment->allowances->hasAllowance(collect([
            'breakdowns' => collect([
                collect([
                    'allowance_breakdown_type_id' => $type->getKey(),
                    'should_deduct' => 0,
                ]),
            ]),
        ]))
    )
);
```

## Test Class Order
1. Properties and `setUp()` at top
2. Test methods (negative scenarios first, then positive)
3. Protected methods in order of first call appearance

## Data Providers
Use `#[DataProvider]` when tests share structure but differ in input data.

## Coverage
Test with multiple records (1 to many), not just single record.

## Feature Tests
Test through HTTP endpoints, not by mocking services.

## DRY
Extract common helpers to base test case. Parameterize methods that differ by one value.

## Boy Scout Rule
- Rename old tests to `when_then` pattern
- Convert `/** @test */` to `#[Test]`
- Fix method ordering
