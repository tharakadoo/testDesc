# OneSyntax Company Guidelines

## Simplest Way

Always generate code using the simplest approach. Avoid unnecessary wrapper methods, abstractions, or indirection when a direct solution works.

## Avoid Technical Words in Function Names

Do not use technical/implementation words like `input`, `data`, `array`, `object`, `request`, `response` in function names. Use domain-specific names that describe what the function does.

### Bad Example
```php
protected function buildScheduleInput(Collection $scheduleInput): array
protected function buildRequestData(array $overrides): array
protected function createUserData(): array
```

### Good Example
```php
protected function buildSchedule(Collection $schedule): array
protected function buildInvalidSchedule(string $field, string $value): array
protected function createUser(): array
```

### Bad Example
```php
protected function createDailySchedule(Employment $employment, ?Collection $config = null): EmailReportSchedule
{
    $config = ($config ?? Collection::wrap([]))->put('type', 'daily');
    return $this->createSchedule($employment, $config);
}

protected function createWeeklySchedule(Employment $employment, ?Collection $config = null): EmailReportSchedule
{
    $config = ($config ?? Collection::wrap([]))->put('type', 'weekly');
    return $this->createSchedule($employment, $config);
}

// Usage
$this->createDailySchedule($this->admin);
$this->createWeeklySchedule($this->admin);
```

### Good Example
```php
protected function createSchedule(Employment $employment, string $type, ?Collection $config = null): EmailReportSchedule
{
    // direct implementation
}

// Usage
$this->createSchedule($this->admin, 'daily');
$this->createSchedule($this->admin, 'weekly');
```

## Consistent Terminology in Names

When naming similar functions or tests, use the same terminology for the same concept. Don't use different words to describe the same thing.

### Bad Example
```php
when_company_is_on_trial_then_invite_limit_scales_with_employees()
when_company_has_subscription_then_invite_limit_scales_with_quantity()
```

### Good Example
```php
when_company_is_on_trial_then_invite_limit_scales_with_employee_count()
when_company_has_subscription_then_invite_limit_scales_with_employee_count()
```

## DRY (Don't Repeat Yourself)

Avoid code duplication. When multiple methods differ by only one or two parameters, extract the common logic into a single parameterized method.

### Bad Example
```php
protected function assertSeeLeaveOnNonWorkingDay(): void
{
    Mail::assertSent(DailySummaryReport::class, function (DailySummaryReport $mail) {
        $leave = collect($mail->build()->viewData['leaves']);
        $this->assertCount(1, $leave);
        $expectedLeave = "**{$this->employee->full_name}** is on **Annual Leave** (non-working day) (pending approval).";
        $this->assertContains($expectedLeave, $leave);
        return true;
    });
}

protected function assertSeeLeaveOnHoliday(): void
{
    Mail::assertSent(DailySummaryReport::class, function (DailySummaryReport $mail) {
        $leave = collect($mail->build()->viewData['leaves']);
        $this->assertCount(1, $leave);
        $expectedLeave = "**{$this->employee->full_name}** is on **Annual Leave** (holiday) (pending approval).";
        $this->assertContains($expectedLeave, $leave);
        return true;
    });
}
```

### Good Example
```php
protected function assertSeeLeaveWithDayType(string $dayType): void
{
    Mail::assertSent(DailySummaryReport::class, function (DailySummaryReport $mail) use ($dayType) {
        $leave = collect($mail->build()->viewData['leaves']);
        $this->assertCount(1, $leave);
        $expectedLeave = "**{$this->employee->full_name}** is on **Annual Leave** ({$dayType}) (pending approval).";
        $this->assertContains($expectedLeave, $leave);
        return true;
    });
}

// Usage:
$this->assertSeeLeaveWithDayType('non-working day');
$this->assertSeeLeaveWithDayType('holiday');
```

## No Inline Comments

Do not add inline comments (`//`) to code. Code should be self-documenting through clear naming and structure.

### Bad Example
```php
// Create an employment for the user in the new company
Employment::factory()
    ->forCompany($company)
    ->forUser($this->admin->user)
    ->admin()
    ->create();
```

### Good Example
```php
Employment::factory()
    ->forCompany($company)
    ->forUser($this->admin->user)
    ->admin()
    ->create();
```

## Function Parameter Limit

Pass maximum 2 parameters to a function (3 is the absolute maximum). When more data is needed, use a Collection.

### Bad Example
```php
protected function assertOnlyDailyEnabled(array $result, string $dateFrequency, string $time, int $scopeCount): void
{
    // ...
}

$this->assertOnlyDailyEnabled($result, '1-5', '09:00am', 1);
```

### Good Example
```php
protected function assertOnlyDailyEnabled(array $result, Collection $expected): void
{
    $this->assertSame($expected->get('dateFrequency'), $result['daily']['date_frequency']);
    $this->assertSame($expected->get('time'), $result['daily']['time']);
    $this->assertCount($expected->get('scopeCount'), $result['daily']['scopes']);
}

$expected = Collection::wrap([
    'dateFrequency' => '1-5',
    'time' => '09:00am',
    'scopeCount' => 1,
]);
$this->assertOnlyDailyEnabled($result, $expected);
```

## Self-Explanatory Variable Names

Variable names must be self-explanatory. Avoid generic names like `$options`, `$data`, `$params`, or `$config`. The name should describe what the variable contains.

### Bad Example
```php
$options = Collection::wrap([
    'enabled' => true,
    'dateFrequency' => '1-5',
    'time' => '09:00',
]);
$this->buildDailyInput($options);

$data = Collection::wrap(['department' => $this->department]);
$this->createDailySchedule($this->admin, $data);
```

### Good Example
```php
$dailyScheduleInput = Collection::wrap([
    'enabled' => true,
    'dateFrequency' => '1-5',
    'time' => '09:00',
]);
$this->buildDailyInput($dailyScheduleInput);

$scheduleConfig = Collection::wrap(['department' => $this->department]);
$this->createDailySchedule($this->admin, $scheduleConfig);
```

### Naming Guidelines
- Use context-specific prefixes: `$dailyScheduleInput`, `$weeklyScheduleInput`
- Describe the content: `$expectedScope`, `$expectedDepartments`, `$scheduleConfig`
- For assertions: `$expectedScheduleSettings`, `$expectedGroupedScope`
- Parameter names should also be descriptive: `Collection $scheduleInput` not `Collection $options`

## Use Collection::wrap() Instead of collect()

When creating collections, use `Collection::wrap()` instead of `collect()`.

### Bad Example
```php
$scheduleConfig = collect([
    'enabled' => true,
    'dateFrequency' => '1-5',
]);
```

### Good Example
```php
$scheduleConfig = Collection::wrap([
    'enabled' => true,
    'dateFrequency' => '1-5',
]);
```

## Use Imports Instead of Fully Qualified Namespaces

Always import classes at the top of the file using `use` statements. Do not use fully qualified namespaces inline.

### Bad Example
```php
$validDepartmentIds = \Domain\Company\Models\Department::query()
    ->where('company_id', $companyId)
    ->pluck('id');
```

### Good Example
```php
use Domain\Company\Models\Department;

$validDepartmentIds = Department::query()
    ->where('company_id', $companyId)
    ->pluck('id');
```

## One Class Per File

Each class must be in its own file. Do not declare multiple classes in a single file.

### Bad Example
```php
// UpdateEmailReportSchedulesCommand.php
class UpdateEmailReportSchedulesCommand extends Data
{
    public function __construct(
        public ?UpdateScheduleTypeCommand $daily = null,
        public ?UpdateScheduleTypeCommand $weekly = null,
    ) {
    }
}

class UpdateScheduleTypeCommand extends Data
{
    public function __construct(
        public bool $enabled,
        public ?string $dateFrequency = null,
    ) {
    }
}
```

### Good Example
```php
// UpdateEmailReportSchedulesCommand.php
class UpdateEmailReportSchedulesCommand extends Data
{
    public function __construct(
        public ?UpdateScheduleTypeCommand $daily = null,
        public ?UpdateScheduleTypeCommand $weekly = null,
    ) {
    }
}

// UpdateScheduleTypeCommand.php (separate file)
class UpdateScheduleTypeCommand extends Data
{
    public function __construct(
        public bool $enabled,
        public ?string $dateFrequency = null,
    ) {
    }
}
```

## Step-Down Rule

The Step-Down Rule is a code organization principle from Robert C. Martin's Clean Code. It states that code should read like a top-down narrative—each function should be followed by those at the next level of abstraction, so you can read the program by descending one level of abstraction at a time.

In practice for test files:
1. Properties and `setUp()` at the top
2. Test methods in order
3. Protected methods ordered by **first call appearance** across all test methods

The protected methods should appear in the exact order they are first called when reading test methods from top to bottom.

### Example

Given these test methods:
```php
#[Test]
public function when_schedule_exists_then_response_has_structure(): void
{
    $this->createDailySchedule($this->admin);      // 1st call: createDailySchedule
    $result = $this->getSchedules($this->admin);   // 2nd call: getSchedules
    $this->assertResponseStructure($result);       // 3rd call: assertResponseStructure
}

#[Test]
public function when_no_schedules_exist_then_returns_default(): void
{
    $result = $this->getSchedules($this->admin);   // already seen
    $this->assertDefaultSchedulesReturned($result); // 4th call: assertDefaultSchedulesReturned
}

#[Test]
public function when_daily_schedule_exists_then_returns_daily(): void
{
    $this->createDailySchedule($this->admin);      // already seen
    $result = $this->getSchedules($this->admin);   // already seen
    $this->assertDailyScheduleReturned($result);   // 5th call: assertDailyScheduleReturned
}
```

### Bad Example
```php
class GetEmailReportSchedulesTest extends TestCase
{
    protected function setUp(): void { ... }

    #[Test]
    public function when_schedule_exists_then_response_has_structure(): void { ... }

    #[Test]
    public function when_no_schedules_exist_then_returns_default(): void { ... }

    // WRONG: Methods grouped by type, not call order
    protected function getSchedules(): array { ... }
    protected function createDailySchedule(): EmailReportSchedule { ... }
    protected function createWeeklySchedule(): EmailReportSchedule { ... }

    protected function assertResponseStructure(array $result): void { ... }
    protected function assertDefaultSchedulesReturned(array $result): void { ... }
    protected function assertDailyScheduleReturned(array $result): void { ... }
}
```

### Good Example
```php
class GetEmailReportSchedulesTest extends TestCase
{
    protected function setUp(): void { ... }

    #[Test]
    public function when_schedule_exists_then_response_has_structure(): void { ... }

    #[Test]
    public function when_no_schedules_exist_then_returns_default(): void { ... }

    #[Test]
    public function when_daily_schedule_exists_then_returns_daily(): void { ... }

    // Methods in order of first call appearance:
    protected function createDailySchedule(): EmailReportSchedule { ... }  // 1st
    protected function getSchedules(): array { ... }                       // 2nd
    protected function assertResponseStructure(array $result): void { ... } // 3rd
    protected function assertDefaultSchedulesReturned(array $result): void { ... } // 4th
    protected function assertDailyScheduleReturned(array $result): void { ... } // 5th
}
```

## Actions Return Domain Data, Not Formatted Output

Actions (use cases) should return raw domain data (models, collections). Output formatting for APIs (GraphQL, REST) should happen in the presentation layer (queries, mutations, controllers, resources).

### Bad Example
```php
// Action doing formatting - WRONG
class QueryEmailReportSchedulesAction
{
    public function execute(User $user): array
    {
        $schedules = EmailReportSchedule::query()->get();

        return [
            'daily' => [
                'enabled' => $schedules->isNotEmpty(),
                'dateFrequency' => $schedules->first()->report_frequency[0],
                'scopes' => $this->formatScopes($schedules),
            ],
        ];
    }
}
```

### Good Example
```php
// Action returns domain data
class QueryEmailReportSchedulesAction
{
    public function execute(User $user): array
    {
        return [
            'dailySchedules' => $this->getSchedulesByType($user, EmailReportSchedule::TYPES['DAILY']),
            'weeklySchedules' => $this->getSchedulesByType($user, EmailReportSchedule::TYPES['WEEKLY']),
            'companies' => $this->getCompaniesWithDepartments($user),
        ];
    }
}

// GraphQL Query handles formatting
class EmailReportSchedules
{
    public function __invoke($_, array $args, HttpGraphQLContext $context): array
    {
        $result = $this->action->execute($context->request->user());

        return [
            'daily' => $this->formatScheduleType($result['dailySchedules']),
            'weekly' => $this->formatScheduleType($result['weeklySchedules']),
            'companies' => $this->formatCompanies($result['companies']),
        ];
    }
}
```

## Tell Don't Ask (Avoid Feature Envy)

Don't ask an object for data and then perform logic on it. Instead, tell the object what you need and let it handle the logic internally. When a class fetches data from another object just to make decisions or build something, it's a code smell called "Feature Envy".

### Bad Example
```php
// Feature Envy: RouteServiceProvider asks for data and builds the Limit itself
RateLimiter::for('invitation', function (Request $request) {
    $company = $request->route('company');

    $service = app(InvitationRateLimitService::class);
    $maxAttempts = $service->getMaxAttempts($company);
    $decayMinutes = $service->getDecayMinutes($company);

    return Limit::perMinutes($decayMinutes, $maxAttempts)
        ->by($request->ip().'-'.$request->user()->id.'-'.$company->getKey());
});
```

### Good Example
```php
// Tell Don't Ask: Service returns the complete Limit object
RateLimiter::for('invitation', function (Request $request) {
    $company = $request->route('company');
    $key = $request->ip().'-'.$request->user()->id.'-'.$company->getKey();

    return app(InvitationRateLimitService::class)->getLimit($company, $key);
});

// Service owns the responsibility of creating the Limit
class InvitationRateLimitService
{
    public function getLimit(Company $company, string $key): Limit
    {
        return Limit::perMinutes($this->getDecayMinutes($company), $this->getMaxAttempts($company))
            ->by($key);
    }

    private function getMaxAttempts(Company $company): int { ... }
    private function getDecayMinutes(Company $company): int { ... }
}
```

## Actions Should Not Call Other Actions

Actions (interactors/use cases) should not call other actions. If you need to compose multiple actions, do it in the controller/mutation/query layer, not within the action itself.

### Bad Example
```php
class UpdateEmailReportSchedulesAction
{
    public function __construct(
        protected QueryEmailReportSchedulesAction $queryAction
    ) {
    }

    public function execute(User $user, Command $command): EmailReportSchedules
    {
        // ... update logic ...

        // Calling another action - WRONG
        return $this->queryAction->execute($user);
    }
}
```

### Good Example
```php
// Action only does one thing
class UpdateEmailReportSchedulesAction
{
    public function execute(User $user, Command $command): void
    {
        // ... update logic only ...
    }
}

// Controller/Mutation orchestrates multiple actions
class UpdateEmailReportSchedules
{
    public function __construct(
        protected UpdateEmailReportSchedulesAction $updateEmailReportSchedules,
        protected QueryEmailReportSchedulesAction $queryEmailReportSchedules
    ) {
    }

    public function __invoke($_, array $args, HttpGraphQLContext $context): array
    {
        $user = $context->request->user();
        $command = Command::validateAndCreate($args);

        $this->updateEmailReportSchedules->execute($user, $command);

        return $this->queryEmailReportSchedules->execute($user)->toArray();
    }
}
```


