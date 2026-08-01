# Testing Rules

## Pest Framework

This project uses Pest v5. Always write tests with Pest.

Create tests:
```bash
php artisan make:test UserControllerTest --pest
php artisan make:test UserFactoryTest --pest --unit
```

Run tests:
```bash
php artisan test                      # All tests
php artisan test --compact            # Condensed output
php artisan test --filter=UserTest    # Specific test
php artisan test --tia                # Test Impact Analysis (fast)
```

## Test Structure

### Feature Tests (tests/Feature)

Test complete request/response cycles:

```php
test('displays user dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get('/dashboard');

    expect($response)
        ->status()->toBe(200)
        ->toHaveSession('success_message');
});
```

### Unit Tests (tests/Unit)

Test isolated logic:

```php
test('calculates invoice total correctly', function () {
    $invoice = Invoice::factory()
        ->has(InvoiceItem::factory()->count(3))
        ->create();

    expect($invoice->total())->toBe(300.00);
});
```

## Test Data with Factories

Always use factories, never manual data:

```php
// ✓ Good
$user = User::factory()->create(['email' => 'test@example.com']);

// ✗ Bad
$user = new User();
$user->email = 'test@example.com';
$user->save();
```

## Factories with States

Create reusable data shapes:

```php
// app/Database/Factories/UserFactory.php
public function admin(): static
{
    return $this->state(fn (array $attributes) => [
        'role' => 'admin',
        'verified_at' => now(),
    ]);
}

// In test
$admin = User::factory()->admin()->create();
```

## Assertions

Use Pest's readable syntax:

```php
// Collections
expect($users)->toHaveCount(3);
expect($users)->each->toHaveProperty('email');

// Exceptions
expect(fn () => User::findOrFail(999))
    ->toThrow(ModelNotFoundException::class);

// Strings
expect($response->getContent())
    ->toContain('Welcome')
    ->not()->toContain('Error');

// Booleans
expect($user->isAdmin())->toBeTrue();
```

## Testing HTTP Endpoints

```php
test('creates user with valid data', function () {
    $response = $this->post('/api/users', [
        'email' => 'test@example.com',
        'name' => 'Test User',
    ]);

    expect($response)
        ->status()->toBe(201)
        ->json('data.email')->toBe('test@example.com');

    expect(User::count())->toBe(1);
});

test('fails with invalid email', function () {
    $response = $this->post('/api/users', [
        'email' => 'invalid',
        'name' => 'Test User',
    ]);

    expect($response)->status()->toBe(422);
    expect(User::count())->toBe(0);
});
```

## Testing Authorization

```php
test('user cannot view others\' invoices', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $invoice = Invoice::factory()->for($other)->create();

    $response = $this->actingAs($user)
        ->get("/invoices/{$invoice->id}");

    expect($response)->status()->toBe(403);
});
```

## Testing with Database Traits

Use `RefreshDatabase` to rollback after each test:

```php
uses(RefreshDatabase::class);

test('creates user', function () {
    // Clean database for this test
    User::factory()->create();
});
```

## Testing Actions

```php
test('create user action', function () {
    $action = new CreateUserAction();

    $user = $action->execute([
        'email' => 'test@example.com',
        'name' => 'Test User',
    ]);

    expect($user)->email->toBe('test@example.com');
    expect(User::count())->toBe(1);
});
```

## Testing Policies

```php
test('user can view own invoices', function () {
    $user = User::factory()->create();
    $invoice = Invoice::factory()->for($user)->create();

    expect($user->can('view', $invoice))->toBeTrue();
});

test('user cannot view others\' invoices', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $invoice = Invoice::factory()->for($other)->create();

    expect($user->can('view', $invoice))->toBeFalse();
});
```

## Testing Validation

```php
test('email is required', function () {
    $response = $this->post('/users', [
        'name' => 'Test User',
    ]);

    expect($response)
        ->status()->toBe(422)
        ->json('errors.email.0')->toBe('The email field is required.');
});
```

## Mocking

For external services:

```php
use Illuminate\Support\Facades\Mail;

test('sends welcome email', function () {
    Mail::fake();

    $this->post('/users', ['email' => 'test@example.com']);

    Mail::assertSent(WelcomeEmail::class);
});
```

## Datasets

Test multiple scenarios:

```php
test('validates email format', function ($email, $valid) {
    $response = $this->post('/users', [
        'email' => $email,
        'name' => 'Test',
    ]);

    if ($valid) {
        expect($response)->status()->toBe(201);
    } else {
        expect($response)->status()->toBe(422);
    }
})->with([
    ['test@example.com', true],
    ['invalid-email', false],
    ['another@domain.org', true],
]);
```

## Test Naming

Use `test()` or `it()` for clarity:

```php
test('creates user with valid email', function () { ... });

it('prevents duplicate emails', function () { ... });

it('soft-deletes user records', function () { ... });
```

## One Assertion Per Test (When Possible)

```php
// ✓ Good: One clear behavior
test('creates user', function () {
    $response = $this->post('/users', validData());
    expect($response)->status()->toBe(201);
});

test('stores email in database', function () {
    $this->post('/users', validData());
    expect(User::count())->toBe(1);
});

// ✓ Also fine: Related assertions
test('creates user and sends email', function () {
    Mail::fake();
    $this->post('/users', validData());
    
    expect(User::count())->toBe(1);
    Mail::assertSent(WelcomeEmail::class);
});
```

## Browser Testing (Sparse)

Use only for critical user flows:

```php
test('user can create invoice', function () {
    $user = User::factory()->create();

    $browser = $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/invoices')
            ->click('@create-button')
            ->type('amount', '100.00')
            ->press('Save')
            ->assertPathIs('/invoices');
    });
});
```

Keep browser tests minimal—they're slow.
