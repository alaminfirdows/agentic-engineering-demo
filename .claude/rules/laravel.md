---
paths:
  - "app/**/*.php"
  - "database/**/*.php"
  - "routes/**/*.php"
---

# Laravel Rules

## FormRequest Validation

Always use FormRequest classes for validation:

```php
// app/Http/Requests/CreateUserRequest.php
public function rules(): array
{
    return [
        'email' => ['required', 'email', 'unique:users'],
        'name' => ['required', 'string', 'max:255'],
    ];
}
```

Never validate inside controllers.

## Actions over Traits

Use Actions for reusable business logic:

```php
class CreateUserAction
{
    public function __construct(private UserRepository $users) {}

    public function execute(array $data): User
    {
        return $this->users->create($data);
    }
}
```

Apply the same action from:
- HTTP controller
- Command
- Event listener
- Queue job

## Models Stay Simple

Keep Eloquent models focused on:
- Relationships
- Scopes
- Accessors/mutators
- Table configuration

No business logic in models.

```php
class User extends Model
{
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }
}
```

## Resources for API Output

Always use API Resources for serialization:

```php
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
```

## Policies for Authorization

Define capabilities in Policy classes:

```php
class InvoicePolicy
{
    public function view(User $user, Invoice $invoice): bool
    {
        return $user->id === $invoice->user_id;
    }
}
```

Check in controllers:

```php
$this->authorize('view', $invoice);
```

## Route Model Binding

Use implicit binding:

```php
Route::get('/invoices/{invoice}', InvoiceController::class.'@show');

// Controller receives resolved Invoice model
public function show(Invoice $invoice) {}
```

## Queue Heavy Work

Move expensive operations to jobs:

```php
SendInvoiceEmail::dispatch($invoice);
```

Never block HTTP requests on slow operations.

## Helpers Over Facades Inside Domain

Inject dependencies:

```php
// ✓ Good
class CreateInvoiceAction
{
    public function __construct(private Carbon $carbon) {}
}

// ✗ Bad
class CreateInvoiceAction
{
    public function execute()
    {
        $now = now();
    }
}
```

## No Cascade Delete on Financial Data

Soft deletes for audit trails:

```php
Schema::create('invoices', function (Blueprint $table) {
    $table->id();
    $table->softDeletes();
});
```

## Migrations

- Always use nullable() explicitly
- Index foreign keys
- Use UUIDs for primary keys
- Descriptive migration names

```php
Schema::create('invoices', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->uuid('user_id')->index();
    $table->foreign('user_id')->references('id')->on('users');
    $table->string('status')->default('draft');
    $table->timestamps();
});
```

## Type Everything

Always use strict types and return type hints:

```php
<?php

declare(strict_types=1);

public function calculate(int $quantity, float $price): float
{
    return $quantity * $price;
}
```

## Collections Over Raw Arrays

Use Illuminate\Support\Collection:

```php
$users->filter(fn($user) => $user->active)
    ->map(fn($user) => new UserResource($user))
    ->toArray();
```

## Testing Conventions

- Test the happy path
- Test validation failures
- Test authorization failures
- Use factories, not manual setup
- One assertion per test (when possible)

```php
test('creates user with valid data', function () {
    $response = $this->post('/users', [
        'email' => 'test@example.com',
        'name' => 'Test User',
    ]);

    expect($response)->redirect('/dashboard');
    expect(User::count())->toBe(1);
});
```
