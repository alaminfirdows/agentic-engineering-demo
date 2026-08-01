# Code Review Checklist

Use this before finalizing code for a feature or fix.

## Architecture & Design

- [ ] Code follows layered architecture (HTTP → Application → Domain → Database)
- [ ] Each class has single responsibility
- [ ] No business logic in Models
- [ ] Actions are used for features (not inline in controllers)
- [ ] FormRequests handle validation (not controllers)
- [ ] Services only for external concerns or cross-cutting logic
- [ ] DTOs used when transferring complex computed data
- [ ] No Repository pattern (use Models + Scopes)

## Laravel Conventions

### Models
- [ ] Model has relationships defined
- [ ] Scopes for common queries
- [ ] No business logic in model methods
- [ ] Uses explicit return types
- [ ] Constructor property promotion used
- [ ] No unnecessary empty constructors

### Controllers
- [ ] Thin controllers (10-20 lines max)
- [ ] Delegates to Actions
- [ ] Uses FormRequest for validation
- [ ] Authorizes before operations
- [ ] Returns Inertia or Resource

### Actions
- [ ] Single responsibility (one use case)
- [ ] Dependencies injected
- [ ] Explicit type hints on parameters and return
- [ ] Uses Services for external concerns
- [ ] Returns typed result
- [ ] Testable without side effects

### FormRequests
- [ ] `rules()` method includes validation
- [ ] `authorize()` checks permissions
- [ ] `prepareForValidation()` cleans/normalizes data
- [ ] `validated()` returns array with proper shape
- [ ] Error messages are user-friendly
- [ ] Validated data passed to Action

### Services
- [ ] One service = one concern (email, payment, storage)
- [ ] No business logic, only orchestration
- [ ] External API calls abstracted here
- [ ] Can be mocked in tests
- [ ] Injected where needed

### Resources
- [ ] Used for API/Inertia output
- [ ] Transforms Model to output format
- [ ] Includes computed fields if needed
- [ ] Collections for relationships
- [ ] Hides sensitive data

### Policies
- [ ] One policy per Model
- [ ] Methods for each action (view, create, update, delete)
- [ ] Checked in controllers: `$this->authorize('action', $model)`
- [ ] Never bypassed on frontend

### Migrations
- [ ] Descriptive names (e.g., `create_invoices_table`)
- [ ] Foreign keys indexed
- [ ] nullable() explicit
- [ ] Default values sensible
- [ ] Idempotent (can run multiple times safely)

### Factories
- [ ] Provide realistic test data
- [ ] Use states for variations
- [ ] Relationships created via `->has()`

## PHP Code Quality

- [ ] Type hints on all parameters
- [ ] Return type declarations
- [ ] Constructor property promotion where applicable
- [ ] Curly braces on all control structures
- [ ] No trailing whitespace
- [ ] Proper indentation (4 spaces)
- [ ] PHPDoc for complex types
- [ ] Array shape types in PHPDoc
- [ ] `declare(strict_types=1)` at top of files
- [ ] No unused imports

## React Code Quality

### Components
- [ ] Functional components only (no classes)
- [ ] Proper TypeScript types for props
- [ ] Component names in PascalCase
- [ ] No inline styles (use Tailwind)
- [ ] Composition over prop drilling
- [ ] Suspense for async data

### Hooks
- [ ] Custom hooks extracted to separate file
- [ ] Hook names start with `use`
- [ ] Dependencies array complete and correct
- [ ] No infinite loops in useEffect

### State Management
- [ ] State kept as local as possible
- [ ] useForm for server sync
- [ ] useHttp for standalone requests
- [ ] Context only for truly global state

### Performance
- [ ] No unnecessary re-renders
- [ ] useCallback only when needed (memoized children)
- [ ] useMemo only for expensive calculations
- [ ] Lists have proper keys

## Testing

- [ ] Feature tests for HTTP endpoints
- [ ] Unit tests for Actions/Services
- [ ] Tests check happy path
- [ ] Tests check validation failures
- [ ] Tests check authorization
- [ ] Tests check edge cases
- [ ] Mocked external services
- [ ] Factories used for test data
- [ ] No manual data setup
- [ ] Tests are deterministic (no random failures)

## Security

- [ ] All user input validated
- [ ] Authorization checks present
- [ ] Sensitive data hidden in Resources
- [ ] Mass assignment guarded (Model fillable/guarded)
- [ ] SQL injection impossible (using ORM)
- [ ] XSS prevented (output escaped)
- [ ] CSRF token checked (Laravel default)
- [ ] No secrets in code
- [ ] Passwords hashed
- [ ] Rate limiting if needed

## Performance

- [ ] No N+1 queries (use eager load)
- [ ] Appropriate indexes on foreign keys
- [ ] Cache used for expensive operations
- [ ] Pagination for large lists
- [ ] No blocking operations in HTTP response
- [ ] Heavy work queued
- [ ] React components don't render unnecessarily
- [ ] Bundle size reasonable (no unused imports)

## Accessibility

- [ ] HTML semantic (button vs div)
- [ ] ARIA labels where needed
- [ ] Color not only differentiator
- [ ] Keyboard navigation works
- [ ] Focus visible
- [ ] Error messages clear

## Documentation

- [ ] Comments explain WHY, not WHAT
- [ ] Complex logic documented
- [ ] PHPDoc on public methods
- [ ] README updated if needed
- [ ] Breaking changes documented

## Database

- [ ] No N+1 queries
- [ ] Foreign keys indexed
- [ ] Appropriate column types
- [ ] nullable explicit
- [ ] No cascade delete on financial data
- [ ] Soft deletes for audit trails
- [ ] Timestamps added

## Code Formatting

- [ ] `vendor/bin/pint --dirty` run
- [ ] `npm run format` run
- [ ] No linter warnings (ESLint, TypeScript)
- [ ] Consistent style with codebase
- [ ] File size reasonable (break up large files)

## Before Commit

- [ ] All tests pass: `php artisan test --compact`
- [ ] Linting passes: `vendor/bin/pint --dirty` + `npm run lint`
- [ ] TypeScript compiles: no errors
- [ ] Code works in browser (manual test)
- [ ] No debug code (dd, var_dump, console.log)
- [ ] No commented code
- [ ] Git status clean

## Common Issues to Look For

- ❌ N+1 queries (use `with()` to eager load)
- ❌ Missing authorization checks
- ❌ Validation in controller (should be FormRequest)
- ❌ Business logic in Model
- ❌ Prop drilling (use composition)
- ❌ No TypeScript types on props
- ❌ Untracked dependencies in useEffect
- ❌ Unnecessary re-renders
- ❌ Unused imports/variables
- ❌ Mixed promises and async/await

## Sign-Off

When all checks pass:

```
✓ Architecture follows conventions
✓ Code is testable and tested
✓ Performance is acceptable
✓ Security considerations addressed
✓ Code is formatted
✓ Ready for production
```
