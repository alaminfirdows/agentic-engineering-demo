---
paths:
  - "app/Http/Controllers/**/*.php"
  - "app/Http/Requests/**/*.php"
  - "app/Actions/**/*.php"
---

# Laravel Backend Rules

## Controllers - Keep Thin

- Single responsibility: one action per method
- Receive: HTTP request with validation via FormRequest
- Call: appropriate Action with validated data
- Return: JSON Resource or Inertia render
- Never put business logic here; delegate to Actions

```php
public function store(CreateUserRequest $request, CreateUserAction $action): JsonResponse
{
    $user = $action->execute($request->validated());
    return response()->json(new UserResource($user), 201);
}
```

## FormRequest Validation

- All input validation in `app/Http/Requests/CreateUserRequest.php`
- Never validate in controllers
- Return array of rules: `['email' => ['required', 'email', 'unique:users'], 'name' => ['required', 'string', 'max:255']]`
- Laravel Fortify handles auth routes automatically

## Actions - Business Logic

- One action = one use case: `CreateUserAction`, `SendInvoiceEmailAction`, `ApproveOrderAction`
- Located: `app/Actions/Domain/ActionName.php`
- Dependency injection: `public function __construct(private UserRepository $users, private MailService $mail) {}`
- Public execute method: `public function execute(array $data): User`
- Always type parameters and return values
- Reusable from: Controllers, Commands, Jobs, Event Listeners

## Models - Keep Simple

- Relationships only: `hasMany()`, `belongsTo()`, `belongsToMany()`
- Scopes only: `scopeActive()` → `User::active()->get()`
- Accessors/mutators: `#[Attribute] public function fullName()`
- Table configuration: `protected $fillable`, `protected $casts`
- No business logic; queries go in Actions or Repositories

## API Resources - Output Format

- All API responses via resources: `new UserResource($user)`
- Collections: `UserResource::collection($users)`
- Format: `public function toArray(): array { return ['id' => $this->id, 'email' => $this->email]; }`
- Relationships: `'invoices' => InvoiceResource::collection($this->invoices),`

## Authorization with Policies

- Define in `app/Policies/InvoicePolicy.php`
- Methods: `view(User $user, Invoice $invoice): bool`
- Check in controllers: `$this->authorize('view', $invoice)`
- Returns boolean: `return $user->id === $invoice->user_id`

## Type Everything

- Strict types: `<?php declare(strict_types=1);` at top
- All parameters typed: `public function calculate(int $quantity, float $price): float`
- Return types: `: string`, `: ?User`, `: User|Guest`
- Nullable: `?string $description = null`

## Dependency Injection

- Register in `AppServiceProvider` or domain providers
- Constructor injection only
- Never use facades inside domain (User::, Mail::, etc.)
- Prefer repositories for data access

## Collections Over Arrays

- `Illuminate\Support\Collection`: `$users->filter()->map()->pluck('email')->unique()`
- Methods: `->where()`, `->map()`, `->groupBy()`, `->keyBy()`, `->unique()`
- Lazy: operations deferred until iteration
