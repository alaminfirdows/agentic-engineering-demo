# Backend Agent Instructions

## When to Activate

When working on:
- Controllers, Routes, Actions
- Models, Database, Migrations
- FormRequests, Validation
- Services, DTOs
- API endpoints, Resources
- Authorization, Policies
- Testing (Feature & Unit)

## Methodology

### 1. Understand the Domain
- Read existing Actions for similar features
- Check Models for relationships
- Review Policies for authorization patterns
- Look at FormRequests for validation precedent

### 2. Follow the Action Pattern
Every backend feature follows:
```
Route → Controller → Action → Service/Model → Database
```

Controller is thin:
- Extract validation via FormRequest
- Delegate to Action
- Return response

### 3. Use Correct Layers

**HTTP Layer**
- Routes (Wayfinder generates TS)
- Controllers (thin, 10-20 lines)
- FormRequests (validation + auth)

**Application Layer**
- Actions (single use case, reusable)
- Services (external concerns: email, payment, storage)
- DTOs (immutable data carriers for complex data)

**Domain Layer**
- Models (relationships, scopes, no business logic)
- Policies (authorization)
- Resources (API output transformation)

### 4. Writing Actions

```php
class CreateInvoiceAction
{
    public function __construct(
        private InvoiceNumberService $numbers,
        private EmailService $email,
    ) {}

    public function execute(array $validated): Invoice
    {
        // 1. Validate preconditions
        // 2. Create/update models
        // 3. Call services
        // 4. Return result
        return $invoice;
    }
}
```

- One action = one business use case
- Inject dependencies
- Use explicit type hints
- Return typed result

### 5. Use FormRequests
- Always validate in FormRequest, never in Action
- Include authorization check
- Transform data in `prepareForValidation()`
- Enhance data in `validated()`

### 6. Services Are Cross-Cutting
Don't create a service for every Action. Services are for:
- Email sending
- Payment processing
- File storage
- Generating sequences
- External APIs

### 7. DTOs for Complex Data
Only use DTOs when:
- Data is computed/derived
- Needs to be passed between multiple layers
- Requires type safety

### 8. Testing

Feature tests (most important):
```php
test('creates invoice with valid data', function () {
    $response = $this->post('/invoices', validData());
    expect($response)->status()->toBe(201);
    expect(Invoice::count())->toBe(1);
});
```

Unit tests:
```php
test('generates correct invoice number', function () {
    $service = new InvoiceNumberService();
    expect($service->next())->toMatch('/INV-2026-\d{6}/');
});
```

### 9. Database Operations

- Use Eloquent, not raw SQL
- Use factories in tests
- Index foreign keys
- Use UUIDs for primary keys
- Migrations: descriptive names

### 10. Authorization

Define in Policy:
```php
public function update(User $user, Invoice $invoice): bool
{
    return $user->id === $invoice->user_id;
}
```

Check in Controller:
```php
$this->authorize('update', $invoice);
```

## Commands to Use

```bash
php artisan make:action CreateInvoiceAction
php artisan make:request CreateInvoiceRequest
php artisan make:test InvoiceControllerTest --pest
php artisan test --compact
php artisan route:list
php artisan tinker
vendor/bin/pint --dirty
```

## Files to Check

Before writing any feature, read:
1. Similar Action in `app/Actions/`
2. Similar Model in `app/Models/`
3. Similar FormRequest in `app/Http/Requests/`
4. Similar test in `tests/Feature/`

## What NOT to Do

- Don't put business logic in Models
- Don't create Services for single-use logic
- Don't use Repository pattern
- Don't validate in Controllers
- Don't mix concerns in Actions
- Don't create extra abstraction layers
