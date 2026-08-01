# Quick Commands Reference

## Feature Development

### Build a New Feature
```
I want to build: [feature description]
```
Triggers:
- Explore existing similar features
- Check domain models
- Plan database changes
- Generate Actions, FormRequests
- Build frontend components
- Write tests

Follow: `.claude/workflows/new-feature.md`

### Example
```
I want to build: Recurring invoice feature
- Invoices can be set to recur monthly/quarterly/yearly
- System should create next invoice 3 days before due date
- Should send reminder email 2 days before due date
```

---

## Bug Fixing

### Report a Bug
```
Bug: [what's wrong]
Expected: [what should happen]
Actual: [what happens instead]
Steps: [how to reproduce]
```

Triggers:
- Reproduce locally
- Check logs
- Write failing test
- Find root cause
- Fix
- Verify no regressions

Follow: `.claude/workflows/bug-fix.md`

### Example
```
Bug: Invoice total is wrong
Expected: Should be $300 (3 items × $100)
Actual: Shows $600
Steps:
1. Create invoice with 3 items, $100 each
2. View invoice detail page
3. Amount shown is doubled
```

---

## Code Review

### Review This Code
```
Review this for:
- Architecture violations
- Security issues
- Performance problems
- Code quality
```

Checks:
- SOLID principles
- Laravel conventions
- React best practices
- Authorization gaps
- N+1 queries
- Unnecessary renders

---

## Optimization

### Speed Up This Feature
```
The [feature] is slow. Profile and optimize.
```

Checks:
- Database queries (N+1, missing indexes)
- Cache opportunities
- React rendering issues
- Bundle size
- Network requests

---

## Architecture

### Explain the Architecture
```
How does [feature] work? Show the flow.
```

Returns:
- Data flow diagram
- Layer breakdown
- Key files involved
- Authorization flow

### Suggest Refactoring
```
Refactor [code path] for better architecture.
```

Checks:
- Layer violations
- Responsibility mixing
- Abstraction levels
- SOLID principles

---

## Testing

### Write Tests for This Feature
```
Write comprehensive tests for: [feature]
```

Creates:
- Feature tests (happy path + errors)
- Unit tests (isolated logic)
- Authorization tests
- Edge case tests

### Fix Broken Tests
```
These tests are failing:
php artisan test --filter=TestName
```

Debugs and fixes failing tests.

---

## Database

### Add New Table
```
Add table for: [entity]
With fields: [list fields]
Relationships: [describe]
```

Creates:
- Migration
- Model
- Factory
- Foreign keys

### Modify Schema
```
Change table [name]:
- Rename column [old] to [new]
- Add index on [column]
- Add foreign key to [table]
```

---

## Frontend

### Build a Page
```
Create page: [name]
With:
- Form for [action]
- Table showing [data]
- Filter/search by [field]
```

Creates:
- React page component
- Form component
- Custom hooks if needed
- Styling

### Fix UI Issue
```
The [component] looks wrong. Fix it.
Steps to reproduce: [...]
```

---

## API & Routes

### Add API Endpoint
```
Create endpoint:
POST /api/invoices
- Accept: customer_id, due_date, items
- Return: Invoice with number
```

Creates:
- Route
- Controller
- FormRequest
- Action
- Resource

### Generate TypeScript Routes
```
Update Wayfinder routes.
```

Regenerates typed route functions from Laravel routes.

---

## Database Queries

### Debug Queries
```
Show me all queries being run for: [endpoint]
```

Uses:
```bash
mcp__laravel-boost__database-query "SELECT ..."
php artisan tinker
DB::enableQueryLog()
```

### Optimize Query
```
Optimize this query: [SQL or description]
```

Suggests:
- Joins vs relationships
- Indexes needed
- Eager loading
- Query alternatives

---

## Cleanup

### Remove Dead Code
```
Find and remove:
- Unused routes
- Unused components
- Unused methods
- Dead imports
```

Uses:
```bash
grep -r "functionName" app/
git log --oneline -p -- file
```

### Update Dependencies
```
Update [package] from v1 to v2.
Handle breaking changes.
```

---

## Migrations

### Rename Column
```
Rename column in [table]:
[old_name] → [new_name]
```

Creates:
- Safe migration (add new, copy, drop old)
- Updates all references

### Drop Column (Safely)
```
Remove [column] from [table].
```

Uses soft delete pattern:
- Migration to drop
- Keeps backups
- Adds comment

---

## Documentation

### Explain [Code]
```
Explain how [code path] works.
```

Provides:
- High-level overview
- Step-by-step flow
- Key decisions
- Related code

### Update CLAUDE.md
```
Update guidelines for: [topic]
```

---

## General Patterns

### "I noticed a problem with..."
Investigate and fix.

### "How do I...?"
Provide tutorial + example.

### "Is this correct?"
Code review + suggestions.

### "Make this faster"
Profile and optimize.

### "Can we...?"
Feasibility check + approach.

---

## Pro Tips

- **Be specific**: Better to say "Invoice total is wrong" than "It's broken"
- **Show steps**: Include reproduction steps for bugs
- **Ask for review**: Get feedback before committing
- **Read patterns**: Check `.claude/examples/` for code patterns
- **Follow workflows**: Use `.claude/workflows/` for common tasks
