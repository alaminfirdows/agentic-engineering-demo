---
paths:
  - "tests/**/*.php"
---

# Pest Testing Rules

- Use Pest v5: `php artisan make:test FeatureTest --pest`
- Structure: Feature tests (`tests/Feature/`) for full request cycles, Unit tests (`tests/Unit/`) for actions
- Run all: `php artisan test --compact` | Filter: `php artisan test --filter=testName` | Fast: `php artisan test --tia`
- Test names: `test('user can create invoice')` or `it('prevents duplicate emails')`
- One assertion per test when possible; related assertions together are fine
- Always use factories: `User::factory()->create()` never manual `new User()`
- Factory states: `User::factory()->admin()->verified()->create()`
- Database isolation: `uses(RefreshDatabase::class)` at test top
- Assertions: `expect($response)->status()->toBe(200)` | `expect($users)->toHaveCount(3)` | `expect($response)->redirect('/dashboard')`
- Test three scenarios: happy path + validation failures + authorization failures
- Mock external services only (Mail, Queue, HTTP): `Mail::fake()`, `Http::fake()`
- Never mock internal classes/actions; test them directly with factories
- Authorization: `expect($user->can('view', $invoice))->toBeTrue()`
- Validation: `expect($response)->status()->toBe(422)` and `expect($response)->json('errors.email')`

