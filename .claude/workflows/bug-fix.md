# Bug Fix Workflow

## Purpose
Identify and fix bugs systematically: reproduce → diagnose → test → verify → clean up.

## When to Use
- Issue reported but unclear how to reproduce
- Error in logs but root cause unknown
- Feature works sometimes but not always
- Performance problem or N+1 query

## Steps

### 1. Reproduce & Isolate
- Gather reproduction steps from issue
- Run: `php artisan test --compact` and `npm run dev`
- Check Laravel logs: `tail -f storage/logs/laravel.log`
- Check browser errors: `mcp__laravel-boost__browser-logs 10`
- Isolate: backend vs frontend? Which model/component?

### 2. Investigate Root Cause
**Backend:**
- Check model relationships: `php artisan tinker`
- Run queries: `mcp__laravel-boost__database-query "SELECT ..."`
- Look at recent changes: `git log --oneline -20`, `git diff main`
- Check for N+1 queries, missing indexes, incorrect logic

**Frontend:**
- Check browser console errors and warnings
- Verify TypeScript types match API response
- Add console.log to debug state/props
- Check component re-render dependencies

### 3. Write Failing Test
Before fixing, prove the bug with a test:
```php
test('invoice shows correct total', function () {
    $invoice = Invoice::factory()
        ->has(InvoiceItem::factory()->count(3))
        ->create();
    
    expect($invoice->total)->toBe(300.00);  // Should pass but fails
});
```

Run: `php artisan test --filter=testName` (confirm it fails)

### 4. Fix the Bug
Apply the fix to the source code. Common patterns:
- **Wrong relationship**: Check foreign key names
- **Missing eager load**: Use `with()` to prevent N+1
- **Missing authorization**: Add `$this->authorize()`
- **React not updating**: Check dependencies array
- **Type mismatch**: Verify TypeScript interfaces match API

### 5. Verify Fix
- Run failing test: should now pass
- Click through feature in browser
- Check related code for same issue elsewhere
- Run full test suite: `php artisan test --compact`
- Run linters: `vendor/bin/pint --dirty && npm run lint`

### 6. Clean Up
- Remove debug code (dd(), console.log(), comments)
- Run formatters: `vendor/bin/pint --dirty && npm run format`
- Add comment only if non-obvious (with reference to policy/rule)
- Final test run: `php artisan test --compact`

## Rules

- ✅ Write test BEFORE fixing (proves bug exists)
- ✅ Test all three cases: happy path, edge cases, error states
- ✅ Check for similar bugs in related code
- ✅ Verify no regressions with full test suite
- ✅ Use Boost tools for database queries, logs, schema
- ❌ Don't skip testing because "it works locally"
- ❌ Don't commit debug code (dd, var_dump, console.log)
- ❌ Don't assume one occurrence; search for pattern

## Output

Commit message format:
```
Fix: [brief description of bug]

- Root cause: [what was wrong]
- Solution: [how fixed]
- Test: [test that validates fix]
- Verified: [no regressions]
```

Example:
```
Fix: Invoice total calculation missing tax

- Root cause: total() method didn't call subtotal with tax parameter
- Solution: Updated total() to include tax calculation
- Test: test_invoice_shows_correct_total_with_tax
- Verified: All invoice tests pass, no regressions
```

## Phase 1: Reproduce

1. **Gather Information**
   - What's the expected behavior?
   - What's the actual behavior?
   - When does it happen? (always, sometimes, edge case)
   - How to reproduce?

2. **Reproduce Locally**
   ```bash
   php artisan test --compact
   npm run dev
   # Click through the flow
   ```

3. **Check Logs**
   ```bash
   # Laravel logs
   tail -f storage/logs/laravel.log
   
   # Browser logs
   mcp__laravel-boost__browser-logs 10
   
   # Database queries
   mcp__laravel-boost__database-query "SELECT * FROM table"
   ```

4. **Isolate the Issue**
   - Is it backend or frontend?
   - What model/action/component?
   - What input triggers it?

## Phase 2: Investigate Root Cause

### Backend Issues

**Check Model Relationships**
```bash
php artisan tinker
>>> User::find(1)->invoices
```

**Run Database Queries**
```bash
mcp__laravel-boost__database-query "SELECT * FROM invoices WHERE user_id = 1"
```

**Review Recent Changes**
```bash
git log --oneline -20
git diff main
```

**Check for N+1 Queries**
```php
// In test, enable query logging
$queries = DB::getQueryLog();
foreach ($queries as $query) {
    echo $query['query'] . "\n";
}
```

### Frontend Issues

**Check Browser Console**
```bash
mcp__laravel-boost__browser-logs 20
```

**Verify TypeScript Types**
- Are props typed correctly?
- Is API response matching type?

**Check Component State**
```tsx
// Add console.log to debug
console.log('invoices:', invoices);
console.log('loading:', loading);
```

## Phase 3: Write Failing Test

Before fixing, write a test that fails:

```php
test('invoice shows correct amount', function () {
    $invoice = Invoice::factory()
        ->has(InvoiceItem::factory()->count(3))
        ->create(['amount' => 100.00]);
    
    // This should pass but currently fails
    expect($invoice->total)->toBe(100.00);
});
```

Run to confirm it fails:
```bash
php artisan test --filter=testName
```

## Phase 4: Fix the Bug

### Common Patterns

**Wrong Relationship**
```php
// ✗ Wrong
public function invoices()
{
    return $this->hasMany(Invoice::class, 'owner_id');
}

// ✓ Correct (use 'user_id' by default)
public function invoices()
{
    return $this->hasMany(Invoice::class);
}
```

**Missing Eager Load**
```php
// ✗ N+1 queries
$users = User::all();
foreach ($users as $user) {
    $user->invoices->count();  // Query per user
}

// ✓ Single query
$users = User::with('invoices')->get();
foreach ($users as $user) {
    $user->invoices->count();  // Loaded
}
```

**Wrong Authorization Check**
```php
// ✗ Missing check
public function show(Invoice $invoice)
{
    return response()->json($invoice);  // Anyone can view
}

// ✓ With authorization
public function show(Invoice $invoice)
{
    $this->authorize('view', $invoice);
    return response()->json($invoice);
}
```

**Frontend Conditional Bug**
```tsx
// ✗ Always shows spinner
{loading ? <Spinner /> : null}
{invoices.map(i => <Item key={i.id} {...i} />)}

// ✓ Wait for invoices
{loading ? <Spinner /> : invoices.map(i => <Item key={i.id} {...i} />)}
```

## Phase 5: Verify Fix

1. **Run Test**
   ```bash
   php artisan test --filter=testName
   ```

2. **Test Locally**
   - Click through the feature
   - Verify the fix works
   - Check edge cases

3. **Check for Regressions**
   ```bash
   php artisan test --compact
   npm run lint
   ```

4. **Review Related Code**
   - Are there similar bugs elsewhere?
   - Can this be prevented architecturally?

## Phase 6: Clean Up

1. **Format Code**
   ```bash
   vendor/bin/pint --dirty
   npm run format
   ```

2. **Remove Debug Code**
   - Remove `dd()`, `var_dump()`, `console.log()`
   - Remove commented code

3. **Add Comment (If Non-Obvious)**
   ```php
   // User must be authorized before loading invoices
   // See: PolicyName::view()
   $this->authorize('view', $invoice);
   ```

4. **Run Final Tests**
   ```bash
   php artisan test --compact
   ```

## Common Bug Patterns

### "It worked yesterday"
- Check recent git changes: `git log --oneline -10`
- Revert the commit: `git revert HEAD`
- Or bisect: `git bisect start`

### "It only happens sometimes"
- Timing issue (race condition)
- Order-dependent code
- Missing validation

### "It's slow"
- Check for N+1: `php artisan tinker` + `DB::enableQueryLog()`
- Check for missing indexes
- Cache the result

### "Authorization bypass"
- Missing policy check
- Wrong policy logic
- Incorrect route model binding

### "Frontend doesn't update"
- Component not re-rendering (missing dependency)
- State not updating (immutable issue)
- API not called (network error)

## Debugging Tools

```bash
# Database
mcp__laravel-boost__database-schema
mcp__laravel-boost__database-query "SELECT ..."

# Logs
mcp__laravel-boost__read-log-entries 20
mcp__laravel-boost__browser-logs 20
mcp__laravel-boost__last-error

# Code
git log --oneline
git diff main
php artisan tinker
```

## When to Ask for Help

- Root cause is unclear after 30 mins
- Bug involves multiple systems
- Uncertain about architectural impact
- Regression risk is high
