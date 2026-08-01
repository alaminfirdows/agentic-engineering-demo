# Architecture Rules

## Layered Application Structure

```
Request → Routes → Controllers → Actions → Models → Database
Response ← Resources ← Actions ← Models ← Database
```

## Domain-Driven Boundaries

- **Domains**: Isolated business contexts (Users, Invoices, Subscriptions, etc.)
- **Actions**: Single-responsibility business logic classes
- **Services**: Shared infrastructure concerns (email, storage, payments)
- **Models**: Eloquent models with relationships, not business logic
- **Resources**: API output formatting (JSON serialization)
- **Policies**: Authorization rules
- **Requests**: Input validation and transformation

## Frontend Architecture

```
Pages (Inertia) → Components → Hooks → Services
                      ↓
                   State (React Context or Component)
                      ↓
                   Laravel Actions (via fetch/Inertia)
```

## Data Flow

1. **Create/Update**: Request → Controller → Action → Model → Database
2. **Read**: Routes → Controller → Action → Model → Resource → Inertia/API
3. **Delete**: Request → Policy (authorization) → Action → Model → Database

## No Domain Logic in Controllers

Controllers should be thin:
- Handle input transformation
- Call appropriate Action
- Return response (Resource or Inertia render)

Example:

```php
public function store(CreateUserRequest $request, CreateUserAction $action): JsonResponse
{
    $user = $action->execute($request->validated());
    return response()->json(new UserResource($user));
}
```

## Avoid Eloquent Facades

Use dependency injection:

```php
// ✓ Good
public function __construct(private UserRepository $users) {}

// ✗ Bad
User::where('active', true)->count();
```

## Service Container

Register actions, repositories, and services in `AppServiceProvider` or domain-specific providers.

## Testing Pyramid

- Unit tests: Actions, Validation rules, Policies
- Feature tests: Full request/response cycle
- Browser tests (Pest): Critical user flows only

Keep browser tests minimal—they're slow.

## Folder Structure

```
app/
├── Actions/          # Business logic
├── Policies/         # Authorization
├── Http/
│   ├── Controllers/
│   └── Requests/     # Form validation
├── Models/
├── Repositories/     # Data access (optional)
├── Resources/        # API output
├── Services/         # Shared infrastructure
└── View Models/      # Computed data for Inertia

database/
├── factories/
├── migrations/
└── seeders/

resources/
├── js/
│   ├── pages/        # Inertia page components
│   ├── components/   # Reusable UI components
│   ├── hooks/        # Custom React hooks
│   └── services/     # API clients

tests/
├── Feature/          # Full request tests
├── Unit/             # Isolated logic
└── Browser/          # Critical flows
```
