# Testing Instructions

## Test Naming Convention

- Test names must follow the `when_[condition]_then_[expected_result]` pattern
- Example: `when_leave_is_on_holiday_then_report_shows_holiday`
- Avoid subjective or vague words like "correct", "proper", "valid", "right", "fails" in test names - they don't describe what the test actually verifies
- Test names should describe the actual behavior/outcome being tested

### Bad Example
```php
when_schedule_exists_then_response_has_correct_json_structure
when_user_submits_form_then_valid_response_returned
when_date_frequency_invalid_then_validation_fails
```

### Good Example
```php
when_schedule_exists_then_see_json_structure
when_user_submits_form_then_see_success_message
when_date_frequency_invalid_then_schedule_not_updated
```

## Single Custom Assertion per Test

Each test should end with a single custom assertion helper that encapsulates all assertions. Do not use multiple inline assertions or chained assertions in test methods.

### Bad Example - Chained Assertions
```php
#[Test]
public function when_schedule_exists_then_see_response()
{
    $response = $this->get('/schedules');

    $response->assertOk()
        ->assertJsonCount(1)
        ->assertJsonPath('0.enabled', true);
}
```

### Bad Example - Multiple Inline Assertions
```php
#[Test]
public function when_schedule_updated_then_database_updated()
{
    $response = $this->put('/schedules', $data);

    $response->assertOk();
    $this->assertDatabaseCount('schedules', 1);
    $this->assertDatabaseHas('schedules', [
        'user_id' => $this->user->id,
        'frequency' => '0 10 * * 1-4',
    ]);
}
```

### Good Example
```php
#[Test]
public function when_schedule_updated_then_database_updated()
{
    $response = $this->put('/schedules', $data);

    $this->assertScheduleUpdated($response, '0 10 * * 1-4');
}

protected function assertScheduleUpdated(TestResponse $response, string $frequency): void
{
    $response->assertOk();
    $this->assertDatabaseCount('schedules', 1);
    $this->assertDatabaseHas('schedules', [
        'user_id' => $this->user->id,
        'frequency' => $frequency,
    ]);
}
```

## Test Class Structure (Step-Down Rule)

See [onesyntax_guide.md](onesyntax_guide.md#step-down-rule) for the Step-Down Rule.

## Assert Actual Values, Not Just Structure

Custom assertions should verify actual data values (IDs, names, etc.) from test fixtures, not just check that keys exist or structure is correct.

### Bad Example
```php
protected function assertResponseStructure(array $result): void
{
    $this->assertArrayHasKey('daily', $result);
    $this->assertArrayHasKey('weekly', $result);
    $this->assertArrayHasKey('id', $result['companies'][0]);
    $this->assertArrayHasKey('name', $result['companies'][0]);
}
```

### Good Example
```php
protected function assertScheduleResponse(array $result): void
{
    $this->assertSame([
        'enabled' => true,
        'dateFrequency' => '1-5',
        'time' => '09:00am',
        'scopes' => [
            ['companyId' => $this->admin->company->getKey()],
        ],
    ], $result['daily']);

    $this->assertSame($this->admin->company->getKey(), $result['companies'][0]['id']);
    $this->assertSame($this->admin->company->name, $result['companies'][0]['name']);
}
```

## Test Data Coverage (1 to Many)

Tests should always verify behavior with multiple records, not just a single record. This ensures edge cases are covered and the implementation handles collections correctly.

### Bad Example
```php
#[Test]
public function when_leave_is_on_non_working_day_then_report_shows_non_working_day()
{
    // Only testing with 1 leave record - insufficient coverage
    $this->setupLeaveForEmployee($employee, CarbonPeriod::create('2019-03-16 09:00', '2019-03-16 17:30'));

    // Assert only 1 record
    $this->assertCount(1, $leave);
}
```

### Good Example
```php
#[Test]
public function when_leave_is_on_non_working_day_then_report_shows_non_working_day()
{
    // Testing with 2 leave records - proper coverage
    $this->setupLeaveForEmployee($employeeOne, CarbonPeriod::create('2019-03-16 09:00', '2019-03-16 17:30'));
    $this->setupLeaveForEmployee($employeeTwo, CarbonPeriod::create('2019-03-16 09:00', '2019-03-16 17:30'));

    // Assert 2 records
    $this->assertCount(2, $leave);
}
```

## Use Data Providers for Similar Tests

When multiple tests share the same structure but differ only in input data (e.g., validation tests), use `#[DataProvider]` to consolidate them.

### Bad Example
```php
#[Test]
public function when_time_field_is_missing_then_validation_fails()
{
    $response = $this->post('/endpoint', ['date_frequency' => '1-5']);
    $response->assertJsonValidationErrors(['time']);
}

#[Test]
public function when_date_frequency_field_is_missing_then_validation_fails()
{
    $response = $this->post('/endpoint', ['time' => '09:00']);
    $response->assertJsonValidationErrors(['date_frequency']);
}
```

### Good Example
```php
#[Test]
#[DataProvider('invalidRequestDataProvider')]
public function when_required_field_is_invalid_then_validation_fails(array $data, string $expectedError)
{
    $response = $this->postJson('/endpoint', $this->buildRequestData($data));
    $response->assertUnprocessable()->assertJsonValidationErrors([$expectedError]);
}

protected function buildRequestData(array $overrides): array
{
    return array_merge([
        'time' => '09:00',
        'date_frequency' => '1-5',
        'report_scope' => ['report_company_id:uuid'],
    ], $overrides);
}

public static function invalidRequestDataProvider(): array
{
    return [
        'missing time' => [['time' => null], 'time'],
        'missing date_frequency' => [['date_frequency' => null], 'date_frequency'],
        'missing report_scope' => [['report_scope' => null], 'report_scope'],
        'report_scope not an array' => [['report_scope' => 'string'], 'report_scope'],
    ];
}
```

## Extract Common Code to Base Test Case

When multiple test classes extend the same base test case, move duplicated helper methods to the base class.

### Bad Example
```php
class DailyReportTest extends ReportTestCase
{
    protected function assertJsonStructure(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonStructure(['*' => ['id', 'name']]);
    }
}

class WeeklyReportTest extends ReportTestCase
{
    protected function assertJsonStructure(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonStructure(['*' => ['id', 'name']]);
    }
}
```

### Good Example
```php
class ReportTestCase extends TestCase
{
    protected function assertJsonStructure(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonStructure(['*' => ['id', 'name']]);
    }
}

class DailyReportTest extends ReportTestCase
{
    // Uses inherited assertJsonStructure()
}

class WeeklyReportTest extends ReportTestCase
{
    // Uses inherited assertJsonStructure()
}
```

### Parameterize Slight Differences

When methods are almost identical but differ in one value, add a parameter with a default value instead of duplicating the method.

```php
// Bad: Duplicated in DailyTest and WeeklyTest with only date_frequency different
protected function buildRequestData(array $overrides): array
{
    return array_merge([
        'time' => '09:00',
        'date_frequency' => '1-5', // or '1' in weekly
        'report_scope' => ["report_company_id:{$this->admin->company_id}"],
    ], $overrides);
}

// Good: Single method in base class with parameter
protected function buildRequestData(array $overrides, string $dateFrequency = '1-5'): array
{
    return array_merge([
        'time' => '09:00',
        'date_frequency' => $dateFrequency,
        'report_scope' => ["report_company_id:{$this->admin->company_id}"],
    ], $overrides);
}

// Usage in weekly test:
$this->buildRequestData($data, '1');
```

## Assert Against Return Values, Not Database

Do not use `assertDatabaseHas`, `assertDatabaseCount`, or similar database assertions. Instead, assert against the returned result from the action/method being tested.

### Bad Example
```php
protected function assertDailyScheduleCreated(array $result, string $companyId): void
{
    $this->assertTrue($result['daily']['enabled']);
    $this->assertDatabaseHas('email_report_schedules', [
        'user_id' => $this->admin->user->getKey(),
        'type' => EmailReportSchedule::TYPES['DAILY'],
        'report_company_id' => $companyId,
    ]);
}

protected function assertScheduleDeleted(array $result): void
{
    $this->assertFalse($result['daily']['enabled']);
    $this->assertDatabaseCount('email_report_schedules', 0);
}
```

### Good Example
```php
protected function assertDailyScheduleCreated(array $result, string $companyId): void
{
    $this->assertTrue($result['daily']['enabled']);
    $this->assertScopeContainsCompany($result['daily']['scopes'], $companyId);
}

protected function assertDailyScheduleDisabled(array $result): void
{
    $this->assertFalse($result['daily']['enabled']);
}

protected function assertScopeContainsCompany(array $scopes, string $companyId): void
{
    $companyIds = array_column($scopes, 'company_id');
    $this->assertContains($companyId, $companyIds);
}
```

## Test Structure: Preparation → Action → Assertion

Every test should follow this structure:
1. **Preparation** - Set up the test data (can be absent for simple tests)
2. **Action** - Execute the code being tested (required)
3. **Assertion** - Verify the expected outcome (required)

### Good Example
```php
#[Test]
public function when_company_is_on_trial_then_invite_limit_scales_with_employee_count()
{
    // Preparation
    RateLimiter::clear('invitation.employment');
    $this->company->update([
        'current_billing_plan' => BillingPlan::Starter,
        'billing_status' => BillingStatus::Trial,
    ]);
    $employment = $this->createUninvitedEmployment($this->company, 'trial@example.com');

    // Action
    $response = $this->inviteEmployment($employment);

    // Assertion
    $this->assertRateLimitHeaders($response, 8, 7);
}
```

## Feature Tests Use Endpoints, Not Mocks

Feature tests should test behavior through HTTP endpoints, not by mocking services. Mocking services in feature tests is unit testing - save that for dedicated unit tests.

### Bad Example
```php
#[Test]
public function when_company_is_on_trial_then_rate_limit_scales_with_employee_count()
{
    $this->company->update(['billing_status' => BillingStatus::Trial]);

    // Mocking the service - this is unit testing, not feature testing
    $limit = app(InvitationRateLimitService::class)->getLimit($this->company, $this->createMockRequest());

    $this->assertEquals(6, $limit->maxAttempts);
}
```

### Good Example
```php
#[Test]
public function when_company_is_on_trial_then_invite_limit_scales_with_employee_count()
{
    RateLimiter::clear('invitation.employment');
    $this->company->update(['billing_status' => BillingStatus::Trial]);
    $employment = $this->createUninvitedEmployment($this->company, 'trial@example.com');

    // Call the actual endpoint
    $response = $this->inviteEmployment($employment);

    // Assert on response headers
    $this->assertRateLimitHeaders($response, 8, 7);
}
```

## No Logic in Tests

Tests should use hardcoded expected values, not calculations. Logic in tests can mask bugs and makes tests harder to understand.

### Bad Example
```php
#[Test]
public function when_company_is_on_trial_then_invite_limit_scales_with_employee_count()
{
    $employment = $this->createUninvitedEmployment($this->company, 'trial@example.com');

    $response = $this->inviteEmployment($employment);

    // Logic in test - calculating expected value
    $expectedLimit = $this->company->employments()->count() * 2;
    $this->assertRateLimitHeaders($response, $expectedLimit, $expectedLimit - 1);
}
```

### Good Example
```php
#[Test]
public function when_company_is_on_trial_then_invite_limit_scales_with_employee_count()
{
    $employment = $this->createUninvitedEmployment($this->company, 'trial@example.com');

    $response = $this->inviteEmployment($employment);

    // Hardcoded expected values - 4 employees × 2 = 8
    $this->assertRateLimitHeaders($response, 8, 7);
}
```

## Test Ordering: Negative Scenarios First

Within a test class, order tests so that negative/edge case scenarios come before the positive/success scenarios. This follows the pattern of testing boundaries and error conditions before the happy path.

### Good Example
```php
// 1. Negative scenarios first
#[Test]
public function when_employment_has_no_email_then_invitation_not_sent() { }

#[Test]
public function when_employment_already_joined_then_invitation_not_sent() { }

#[Test]
public function when_employment_not_found_then_default_limit_used() { }

// 2. Positive scenarios after
#[Test]
public function when_employment_not_invited_then_invitation_sent() { }

#[Test]
public function when_company_is_free_forever_then_invite_limit_is_hourly() { }
```

## Boy Scout Rule

Leave the file cleaner than you found it. When working on a test file:

1. **Rename old test methods** - Update existing test names to follow the `when_then` pattern
2. **Replace old annotations** - Convert `/** @test */` docblock annotations to `#[Test]` attributes
3. **Fix method ordering** - Ensure stepdown rule is followed (public tests first, protected helpers below)
