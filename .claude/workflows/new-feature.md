# New Feature Workflow

## Goal
Implement a complete feature end-to-end: backend + frontend + tests.

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
