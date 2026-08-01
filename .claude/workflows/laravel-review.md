# Laravel Code Review Workflow

## Purpose
Review Laravel backend changes for adherence to project architecture, best practices, and security.

## When to Use
- Code review before merging PR
- Quick architecture check on changes
- Verifying new Action/Controller follows patterns
- Ensuring proper use of Models, Resources, Policies

## Steps

### 1. Review Routes & Controllers
Check `routes/` and `app/Http/Controllers/`:

**Controllers should be thin:**
```php
// ✓ Good - delegates to action
public function store(CreateInvoiceRequest $request, CreateInvoiceAction $action)
{
    $invoice = $action->execute($request->validated());
    return redirect('/invoices')->with('success', 'Created');
}

// ✗ Bad - business logic in controller
public function store(CreateInvoiceRequest $request)
{
    $invoice = Invoice::create($request->validated());
    foreach ($request->items as $item) {
        InvoiceItem::create([...]);
    }
    return redirect('/invoices');
}
```

Checklist:
- ✅ Uses FormRequest for validation
- ✅ Calls $this->authorize() for authorization
- ✅ Delegates to Action for business logic
- ✅ Returns Inertia render or Resource
- ✅ Returns json for API endpoints
- ❌ No direct model manipulation
- ❌ No loops or complex logic
- ❌ No calling external services directly

### 2. Review FormRequests
Check `app/Http/Requests/`:

```php
// ✓ Good
class CreateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->team->can('create-invoices');
    }
    
    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'due_date' => ['required', 'date', 'after:today'],
        ];
    }
}

// ✗ Bad - missing authorize, validation too loose
class CreateInvoiceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'customer_id' => ['required'],
            'due_date' => ['required'],
        ];
    }
}
```

Checklist:
- ✅ Has authorize() method that checks permissions
- ✅ Validates all required fields
- ✅ Uses proper validation rules (email, unique, exists)
- ✅ Uses custom messages if error text unclear
- ✅ Uses prepareForValidation() for data transformation
- ❌ No validation in controller
- ❌ No missing rules for any input field
- ❌ No authorize() just returning true

### 3. Review Actions
Check `app/Actions/`:

```php
// ✓ Good - single responsibility, injectable, typed
class CreateInvoiceAction
{
    public function __construct(
        private InvoiceNumberService $numbers,
        private MailService $mail,
    ) {}
    
    public function execute(array $data): Invoice
    {
        $invoice = Invoice::create([
            ...$data,
            'invoice_number' => $this->numbers->next(),
        ]);
        
        $this->mail->send(new InvoiceCreated($invoice));
        return $invoice;
    }
}

// ✗ Bad - takes FormRequest, no clear responsibility
class CreateInvoiceAction
{
    public function execute(CreateInvoiceRequest $request): void
    {
        $invoice = Invoice::create($request->validated());
        // Also sends email? Also calls Stripe?
        Mail::send(...);
        Stripe::charge(...);
    }
}
```

Checklist:
- ✅ Single responsibility (one use case)
- ✅ Accepts array data, not FormRequest
- ✅ Returns typed result (Model or collection)
- ✅ Injects dependencies in constructor
- ✅ Calls external services (Mail, Stripe)
- ✅ Uses proper return types
- ❌ No taking FormRequest as parameter
- ❌ No mixing multiple concerns
- ❌ No untyped parameters or returns

### 4. Review Models
Check `app/Models/`:

```php
// ✓ Good - only relationships, scopes, accessors
class Invoice extends Model
{
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }
    
    public function scopeForTeam(Builder $query, Team $team): Builder
    {
        return $query->where('team_id', $team->id);
    }
    
    #[Attribute]
    public function total(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->items->sum('total'),
        );
    }
}

// ✗ Bad - business logic in model
class Invoice extends Model
{
    public function createItems($itemData)
    {
        foreach ($itemData as $item) {
            InvoiceItem::create($item);
        }
    }
    
    public function sendEmail()
    {
        Mail::send(new InvoiceEmail($this));
    }
}
```

Checklist:
- ✅ Only relationships: belongsTo, hasMany, belongsToMany
- ✅ Only scopes for common queries: scopeActive(), scopeForTeam()
- ✅ Only accessors/mutators for computed fields
- ✅ Configuration: $fillable, $casts, $hidden, timestamps
- ✅ Proper type hints on relationships
- ❌ No business logic (loops, external calls)
- ❌ No methods that modify data
- ❌ No calling services or mail

### 5. Review Policies
Check `app/Policies/`:

```php
// ✓ Good - clear authorization logic per action
class InvoicePolicy
{
    public function view(User $user, Invoice $invoice): bool
    {
        return $user->team_id === $invoice->team_id;
    }
    
    public function update(User $user, Invoice $invoice): bool
    {
        return $user->team_id === $invoice->team_id
            && $user->hasRole('admin');
    }
    
    public function delete(User $user, Invoice $invoice): bool
    {
        return $this->update($user, $invoice)
            && $invoice->status === 'draft';
    }
}

// ✗ Bad - no team check, overly permissive
class InvoicePolicy
{
    public function view(User $user, Invoice $invoice): bool
    {
        return true;  // Anyone can view!
    }
    
    public function update(User $user, Invoice $invoice): bool
    {
        return $user->id !== null;  // Any logged-in user
    }
}
```

Checklist:
- ✅ Methods: view, create, update, delete, restore
- ✅ view() checks team ownership (team_id match)
- ✅ update() checks team AND user role
- ✅ delete() checks team AND business logic
- ✅ All return boolean
- ✅ Used in controller: $this->authorize('view', $model)
- ❌ No missing team checks
- ❌ No returning true for all users
- ❌ No business logic (should be in Action)

### 6. Review Resources
Check `app/Http/Resources/`:

```php
// ✓ Good - clean output format
class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer' => new CustomerResource($this->customer),
            'items' => InvoiceItemResource::collection($this->items),
            'total' => $this->total,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}

// ✗ Bad - exposing internal fields
class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return $this->all();  // Exposes everything!
    }
}
```

Checklist:
- ✅ Only includes public fields
- ✅ Uses proper formats for dates (ISO 8601)
- ✅ Includes relationships as nested resources
- ✅ Computes fields if needed
- ❌ No exposing stripe_id, team_id, or internal fields
- ❌ No exposing passwords or secrets
- ❌ No using $this->all() or * wildcard

### 7. Check Database Interactions
```bash
grep -r "::find\|::where" app/Http/Controllers/ --include="*.php"
```

Should not see direct model access:
- ✅ `Invoice::forTeam($team)->find($id)`
- ❌ `Invoice::find($id)` (no team filter!)

### 8. Check for Common Issues
```bash
# No use of facades inside domain
grep -r "Mail::\|Stripe::\|User::\|Cache::" app/Actions/ app/Services/

# No validation in controllers
grep -r "validate(\|rules()" app/Http/Controllers/ --include="*.php"

# No dd() or var_dump() left in code
grep -r "dd(\|var_dump(\|die(" app/ --include="*.php"
```

## Rules

- ✅ Controllers delegate to Actions
- ✅ Actions handle business logic
- ✅ Models handle relationships and scopes only
- ✅ Policies handle authorization
- ✅ Resources format output
- ✅ FormRequests handle validation
- ✅ All code is typed
- ✅ Team isolation enforced
- ❌ No business logic in controllers
- ❌ No business logic in models
- ❌ No facades in domain logic
- ❌ No untyped parameters or returns

## Output

Review comment template:

```
## Architecture Review

### Issues Found
- [ ] Controller doing X (should be in Action)
- [ ] Model method doing Y (should be scope)
- [ ] Missing authorization check

### Questions
- Why does X accept FormRequest instead of array?
- Where is the policy for this endpoint?

### Good Practices Observed
- ✅ Proper use of Actions
- ✅ Clean controller delegation
- ✅ Team isolation verified

### Verdict
- [ ] Approve
- [ ] Request changes
- [ ] Needs discussion
```