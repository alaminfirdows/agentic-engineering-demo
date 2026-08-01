# Known Issues & Tech Debt

Track recurring problems, architecture decisions, and things to avoid.

## Active Issues

(Update as issues are discovered and fixed)

---

## Tech Debt

### High Priority
- [ ] Identify and document high-priority tech debt items
- [ ] Link to related tests
- [ ] Estimate effort to address

### Medium Priority
- [ ] Refactoring opportunities
- [ ] Performance optimizations
- [ ] Code simplifications

### Low Priority
- [ ] Nice-to-have improvements
- [ ] Code cleanup
- [ ] Documentation gaps

---

## Architectural Decisions

Document major decisions and their rationale.

### Why Actions Pattern?
- Single responsibility per feature
- Reusable across controllers, commands, jobs
- Easier to test than controller logic
- Easy to add telemetry/logging

### Why No Repositories?
- Eloquent already provides abstraction
- Models + scopes are flexible
- Services handle complex queries
- Repositories add unnecessary layer

### Why DTOs Over Arrays?
- Type safety in complex data flows
- IDE autocomplete
- Self-documenting data structure
- Immutability for computed values

---

## Patterns to Avoid

### Anti-Patterns in This Codebase

❌ **Don't: Put Business Logic in Models**
- Models are for relationships + persistence
- Use Actions for orchestration

❌ **Don't: Validate in Controllers**
- Use FormRequest classes
- Keeps validation centralized

❌ **Don't: Create Unneeded Services**
- Only create if used by multiple Actions
- Or if it's external (email, payment, storage)

❌ **Don't: Prop Drilling in React**
- Use composition or Context for deeply nested data
- Components should own their state

❌ **Don't: Fetch Data in useEffect Without Dependencies**
```tsx
// ✗ Infinite loop
useEffect(() => {
    fetchData();
});

// ✓ Fetch once on mount
useEffect(() => {
    fetchData();
}, []);

// ✓ Fetch when dependency changes
useEffect(() => {
    fetchData(id);
}, [id]);
```

---

## Performance Insights

### N+1 Query Patterns
If loading many users and their invoices:
```php
// ✗ N+1 queries (1 for users + 1 per user for invoices)
$users = User::all();
$users->map(fn($u) => $u->invoices);

// ✓ Single query with eager load
$users = User::with('invoices')->get();
```

### React Rendering Issues
- Avoid recreating functions/objects in render
- Use `useCallback()` when passing handlers to memoized children
- Use `useMemo()` only for expensive calculations

---

## Database Considerations

### Indexing Strategy
- Always index foreign keys
- Index commonly filtered columns
- Composite indexes for frequent WHERE + ORDER BY

### Backup Pattern
- Always create new column before dropping old
- Keep data for 30 days before cleanup
- Document migration rationale

---

## Security Considerations

### Authorization
- Always check policies before model operations
- Don't rely on frontend validation
- Test with unauthorized users

### Validation
- Validate all user input in FormRequest
- Use built-in validators (email, unique, etc.)
- Sanitize output in Resources

### Secrets
- Never commit .env files
- Use environment variables for API keys
- Check git history for leaked credentials

---

## Testing Reminders

### Coverage Targets
- Critical paths: 100% (auth, payment, deletion)
- Features: 80%+ coverage
- Utilities: 70%+

### Test Patterns
- Feature tests for HTTP flows
- Unit tests for Actions
- Browser tests for critical user journeys (rare)

### Mocking
- Mock external services (email, payment)
- Use real database (tests are fast with SQLite)
- Avoid over-mocking internal code

---

## Code Style Reminders

### Naming
- Actions: `CreateUserAction`, `DeleteInvoiceAction`
- Services: `EmailService`, `PaymentService`
- DTOs: `InvoiceData`, `UserProfileData`
- Hooks: `useInvoices()`, `useForm()`
- Events: `UserCreated`, `InvoicePaymentFailed`

### Comments
- Only comment WHY, not WHAT
- Code should be self-documenting
- Use PHPDoc for complex data shapes

### Formatting
- Run `vendor/bin/pint --dirty` before commit
- Run `npm run format` for frontend
- Both checked in CI/CD

---

## Deployment Checklist

Before releasing:
- [ ] All tests pass
- [ ] No compiler warnings (Larastan, TypeScript)
- [ ] Database migrations tested
- [ ] No breaking API changes
- [ ] Documentation updated
- [ ] Performance checked (no obvious slowdowns)

---

## References

- Architecture flow: `.claude/docs/architecture.md`
- Example patterns: `.claude/examples/`
- Backend guide: `.claude/agents/backend.md`
- Frontend guide: `.claude/agents/frontend.md`
- Feature workflow: `.claude/workflows/new-feature.md`
- Bug fix workflow: `.claude/workflows/bug-fix.md`
