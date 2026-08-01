# New Feature Workflow

## Purpose
Implement complete feature end-to-end: database → backend → frontend → tests → verification.

## When to Use
- Building new user-facing feature
- Adding new API endpoint
- Implementing new data model
- Cross-system feature (backend + frontend + tests)

## Steps

### 1. Design & Planning
- [ ] Understand domain: read existing Models, Actions, API patterns
- [ ] Check schema: `mcp__laravel-boost__database-schema`
- [ ] Identify affected resources and relationships
- [ ] Verify no conflicts with existing features
- [ ] Plan team isolation if multi-tenant feature

### 2. Database & Models
```bash
php artisan make:migration create_invoices_table --create=invoices
php artisan make:model Invoice --migration --factory
php artisan migrate
```

Define relationships in models (belongsTo, hasMany, belongsToMany).
Create factory with realistic test data and states.

### 3. Backend: Validation & Actions
```bash
php artisan make:request CreateInvoiceRequest
php artisan make:action CreateInvoiceAction
php artisan make:policy InvoicePolicy --model=Invoice
php artisan make:resource InvoiceResource
```

FormRequest rules:
- Input validation: email, unique, max length
- Authorization: authorize() method
- Data transformation: prepareForValidation()

Action responsibilities:
- Single use case per action
- Dependency injection for services
- Proper return types
- Call external services (Mail, Stripe, etc.)

Policy methods:
- view, create, update, delete, restore
- Check team ownership and user role

### 4. Backend: Routes & Controller
```bash
php artisan make:controller InvoiceController --resource
```

Routes (with Wayfinder names):
```php
Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
```

Thin controller (max 15 lines per method):
- Validate via FormRequest
- Authorize via Policy
- Delegate to Action
- Return Inertia or Resource

Example:
```php
public function store(CreateInvoiceRequest $request, CreateInvoiceAction $action)
{
    $invoice = $action->execute($request->validated());
    return redirect('/invoices')->with('success', 'Invoice created');
}
```

### 5. Frontend: Pages & Forms
Create pages in `resources/js/pages/`:

Page structure:
```tsx
interface Props {
    invoices: Invoice[];
}

export default function InvoicesPage({ invoices }: Props) {
    return <div>{/* content */}</div>;
}
```

Form with useForm hook:
```tsx
const { data, setData, post, errors } = useForm({
    customer_id: '',
    due_date: '',
});

const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post('/invoices');
};
```

Reusable components in `resources/js/components/`.
Custom hooks in `resources/js/hooks/` if logic is reusable.

### 6. Testing
**Feature tests** (request → response):
```php
test('lists all invoices', function () {
    Invoice::factory()->count(3)->create();
    $response = $this->get('/invoices');
    expect($response)->status()->toBe(200);
});
```

Test three scenarios for each endpoint:
- ✅ Happy path (works correctly)
- ❌ Validation failures (bad input rejected)
- 🔒 Authorization failures (unauthorized users blocked)

**Unit tests** (Action logic):
```php
test('creates invoice with number', function () {
    $action = new CreateInvoiceAction(...);
    $invoice = $action->execute(['customer_id' => 1]);
    expect($invoice->invoice_number)->toMatch('/INV-/');
});
```

### 7. Polish & Verification
```bash
vendor/bin/pint --dirty          # Format PHP
npm run format                   # Format JavaScript
php artisan test --compact       # Run all tests
npm run lint                     # Check TypeScript/React
```

Before submitting:
- [ ] All tests pass (feature + unit + integration)
- [ ] No type warnings (Larastan, TypeScript)
- [ ] Code formatted (Pint, Prettier)
- [ ] Feature tested in browser (click through)
- [ ] Edge cases handled gracefully
- [ ] Error messages are user-friendly
- [ ] Unauthorized users are blocked (policy checked)
- [ ] Database queries optimized (no N+1)
- [ ] Team isolation verified (if multi-tenant feature)

## Rules

**Database**
- ✅ Use `php artisan make:migration` (never manual)
- ✅ Always set foreign keys: `$table->foreign('user_id')->on('users')`
- ✅ Use soft deletes for financial records: `$table->softDeletes()`
- ✅ Index foreign keys: `$table->foreign('user_id')->references('id')`

**Backend**
- ✅ Thin controllers: delegate to Actions
- ✅ All input validation in FormRequest
- ✅ All authorization in Policies
- ✅ Type all parameters and returns
- ✅ Inject dependencies, never use facades inside domain logic
- ❌ No business logic in controllers
- ❌ No business logic in models (except relationships, scopes)

**Frontend**
- ✅ Use functional components with hooks only
- ✅ Type all props with TypeScript
- ✅ Use useForm for forms
- ✅ Use Inertia hooks (useForm, useHttp, router)
- ✅ Extract reusable logic into custom hooks
- ❌ No class components
- ❌ No untyped props

**Testing**
- ✅ Use Pest v5 (not PHPUnit)
- ✅ Test happy path + validation + authorization
- ✅ Use factories for all test data
- ✅ Mock external services (Mail, Stripe)
- ✅ One assertion per test (when possible)
- ❌ Never use manual `new Model()` in tests
- ❌ Never mock internal classes/actions

**Multi-Tenant**
- ✅ All queries filtered by team: `Invoice::forTeam($user->team)`
- ✅ Policies verify team ownership: `$user->team_id === $invoice->team_id`
- ✅ Check subscription status before premium features
- ❌ Never trust team_id from request parameter

## Output

Checklist for completion:
- [ ] Database migration created and ran
- [ ] Models with relationships defined
- [ ] FormRequest with validation
- [ ] Action with dependency injection
- [ ] Controller thin and delegating
- [ ] Policy with authorization checks
- [ ] API Resource for output
- [ ] Frontend pages and components
- [ ] Forms with useForm hook
- [ ] Feature tests (happy + fail + auth)
- [ ] All tests passing
- [ ] Code formatted
- [ ] Type checking clean
- [ ] Tested in browser
- [ ] Ready for code review

## Prerequisites

- [ ] Feature approved by product/design
- [ ] Understand domain (read existing Models, Actions)
- [ ] Identify affected resources
- [ ] Check for conflicts with existing features

## Phase 1: Database & Models

1. **Inspect Schema**
   ```bash
   php artisan database:show
   # or use Laravel Boost: mcp__laravel-boost__database-schema
   ```

2. **Create Migration**
   ```bash
   php artisan make:migration create_invoices_table --create=invoices
   ```

3. **Create Model**
   ```bash
   php artisan make:model Invoice --migration --factory
   ```

4. **Define Relationships**
   - Add `belongsTo()`, `hasMany()` in Models
   - Add inverse relationships

5. **Create Factory**
   - Populate with realistic test data
   - Add states for variations

## Phase 2: Backend

### Routes
```php
Route::get('/invoices', [InvoiceController::class, 'index']);
Route::post('/invoices', [InvoiceController::class, 'store']);
Route::get('/invoices/{invoice}', [InvoiceController::class, 'show']);
```

Named routes for Wayfinder:
```php
Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
```

### FormRequest Validation
```bash
php artisan make:request CreateInvoiceRequest
php artisan make:request UpdateInvoiceRequest
```

Include:
- `rules()`
- `authorize()`
- `prepareForValidation()` for data transformation

### Actions
```bash
php artisan make:action CreateInvoiceAction
php artisan make:action UpdateInvoiceAction
php artisan make:action DeleteInvoiceAction
```

Each action:
- Single responsibility
- Inject dependencies
- Return typed result
- Call services as needed

### Services (If Needed)
```bash
php artisan make:class Services/InvoiceNumberService
php artisan make:class Services/InvoiceMailService
```

Only create if used by multiple Actions or involves external concerns.

### Controller
```bash
php artisan make:controller InvoiceController --resource
```

- Thin controller: 10-20 lines max
- Validate via FormRequest
- Delegate to Action
- Return Inertia or Resource

### Authorization (Policies)
```bash
php artisan make:policy InvoicePolicy --model=Invoice
```

Define capabilities:
- `view()`, `create()`, `update()`, `delete()`, `restore()`
- Check in Controller: `$this->authorize('update', $invoice);`

### Resources
```bash
php artisan make:resource InvoiceResource
```

- Transform Model for API/Inertia output
- Include computed fields if needed
- Use Collections for relationships

## Phase 3: Frontend

### Page Component
Create in `resources/js/pages/`:
```tsx
// resources/js/pages/Invoices/Index.tsx
import { Suspense } from 'react';
import type { Invoice } from '@/types/models';

type Props = {
    invoices: Invoice[];
};

export default function InvoicesPage({ invoices }: Props) {
    return <div>...</div>;
}
```

### Form Component
```tsx
// resources/js/pages/Invoices/Create.tsx
import { useForm } from '@inertiajs/react';

export default function CreateInvoice() {
    const { data, setData, post, errors } = useForm({
        customer_id: '',
        due_date: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/invoices');
    };

    return <form onSubmit={handleSubmit}>...</form>;
}
```

### Custom Hooks (If Needed)
```tsx
// resources/js/hooks/useInvoices.ts
export function useInvoices(filters?: object) {
    // Fetch logic
    return { invoices, loading };
}
```

### Components
Reusable UI components in `resources/js/components/`:
```tsx
// resources/js/components/InvoiceCard.tsx
export function InvoiceCard({ invoice }: { invoice: Invoice }) {
    return <div>...</div>;
}
```

## Phase 4: Testing

### Feature Tests
```bash
php artisan make:test InvoiceControllerTest --pest
```

Test the happy path:
```php
test('lists all invoices', function () {
    $invoices = Invoice::factory()->count(3)->create();
    
    $response = $this->get('/invoices');
    
    expect($response)->status()->toBe(200);
    expect(count($response->props['invoices']))->toBe(3);
});
```

Test validation failures:
```php
test('fails without customer_id', function () {
    $response = $this->post('/invoices', [
        'due_date' => now()->addDay()->toDateString(),
    ]);
    
    expect($response)->status()->toBe(422);
});
```

Test authorization:
```php
test('user cannot view others\' invoices', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $invoice = Invoice::factory()->for($other)->create();
    
    $response = $this->actingAs($user)->get("/invoices/{$invoice->id}");
    expect($response)->status()->toBe(403);
});
```

### Unit Tests
```php
test('creates invoice with number', function () {
    $action = new CreateInvoiceAction(
        new InvoiceNumberService(),
        mock(EmailService::class),
    );
    
    $invoice = $action->execute(['customer_id' => 1]);
    expect($invoice->invoice_number)->toMatch('/INV-/');
});
```

## Phase 5: Polish

### Code Quality
```bash
vendor/bin/pint --dirty                 # Fix formatting
php artisan test --compact              # Run tests
php artisan route:list                  # Verify routes
npm run lint                            # ESLint
npm run format                          # Prettier
```

### Documentation
- Add comments for non-obvious logic
- Document Action parameters
- Add PHPDoc to Services

### Types
- Ensure TypeScript types match backend (Wayfinder)
- Use proper return types in PHP

## Phase 6: Verification

Before marking complete:

- [ ] All tests pass
- [ ] No compiler warnings (Larastan, TypeScript)
- [ ] Code formatted (Pint, Prettier)
- [ ] Feature tested in browser (click through)
- [ ] Edge cases handled
- [ ] Error messages user-friendly
- [ ] Unauthorized users blocked
- [ ] Database queries optimized

## Commands Quick Reference

```bash
# Database
php artisan make:migration create_table --create=table
php artisan migrate
php artisan migrate:refresh

# Backend
php artisan make:model Model --migration --factory
php artisan make:action ActionName
php artisan make:request RequestName
php artisan make:controller ControllerName --resource
php artisan make:policy PolicyName --model=Model
php artisan make:resource ResourceName

# Testing
php artisan make:test TestName --pest
php artisan test --compact
php artisan test --filter=TestName

# Code Quality
vendor/bin/pint --dirty
npm run lint
npm run format
```

## Timeline

- **Simple feature** (CRUD): 1-2 hours
- **Complex feature** (with relationships, auth): 2-4 hours
- **Feature with external API**: 4-6 hours
