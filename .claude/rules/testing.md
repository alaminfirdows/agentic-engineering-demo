---
paths:
  - "tests/**/*.php"
  - "**/*.test.ts"
  - "**/*.test.tsx"
---

# Testing Rules

## PHP/Pest Testing

- Use Pest v5 for all tests: `php artisan make:test Name --pest`
- Test structure: Feature tests for endpoints, Unit tests for actions/logic
- Run: `php artisan test --compact` (filtered: `--filter=testName`)
- Always use factories for test data, never manual setup
- Test one behavior per test: "should [expected] when [condition]"
- Factories with states: `User::factory()->admin()->create()`
- Assertions: `expect($response)->status()->toBe(200)`, `expect($users)->toHaveCount(3)`
- Mock only external services (Mail, Queue), not internal classes
- Use `RefreshDatabase` trait for database isolation per test
- Test happy path + validation failures + authorization failures

## TypeScript/React Testing

- Test names: "should [expected] when [condition]"
- Mock external dependencies (API calls, localStorage), not internal modules
- Clean up side effects in `afterEach` hooks
- Test user interactions (clicks, form submission), not implementation details
- Use React Testing Library: `screen.getByRole()`, `screen.getByText()`

