# Security Audit Workflow

## Purpose
Perform comprehensive security review of codebase changes, focusing on team isolation and authorization.

## When to Use
- Before merging to main
- When adding authentication or billing features
- When working with multi-tenant features
- Regular security checkups
- After external security review feedback

## Steps

### 1. Authorization Audit
Review all routes and controllers for proper authorization:

```bash
php artisan route:list --except-vendor
```

Check each endpoint:
- ✅ FormRequest has `authorize()` method?
- ✅ Controller calls `$this->authorize()`?
- ✅ Policy exists with proper checks?
- ❌ No direct model access without authorization

Common vulnerabilities:
- Missing `$this->authorize()` check
- Policy that doesn't verify team ownership
- Route model binding without team verification

### 2. Team Isolation Audit
Every multi-tenant data access must verify team:

**Database Queries:**
```php
// ✗ Bad - could access other team's data
$invoice = Invoice::find($id);

// ✓ Good - filtered by team
$invoice = Invoice::forTeam($user->team)->findOrFail($id);
```

Check:
- ✅ All model queries use `->forTeam()` scope?
- ✅ Policies verify `$user->team_id === $model->team_id`?
- ✅ FormRequest checks team ownership?
- ❌ No hardcoded team IDs
- ❌ No trusting team_id from request

**Script:**
```bash
grep -r "Invoice::find\|User::find\|Invoice::where" app/Http/Controllers/ --include="*.php"
# Should NOT see direct access without forTeam() scope
```

### 3. Input Validation Audit
Every endpoint must validate and sanitize input:

**FormRequest validation:**
```php
public function rules(): array
{
    return [
        'email' => ['required', 'email', 'unique:users'],
        'name' => ['required', 'string', 'max:255'],
    ];
}
```

Check:
- ✅ All required fields have `required` rule?
- ✅ Email fields have `email` rule?
- ✅ String fields have `max` length?
- ✅ Unique fields have `unique:table` rule?
- ✅ Foreign keys have `exists:table,id` rule?
- ❌ No missing validation
- ❌ No XSS vectors (sanitize HTML input)

### 4. Stripe & Payment Audit
If using Stripe:

```bash
grep -r "Stripe::\|stripe_" app/ --include="*.php"
```

Check:
- ✅ Never trust stripe_id from request
- ✅ Always verify via Stripe API:
  ```php
  $subscription = Stripe::subscriptions()->retrieve($id);
  if ($subscription->customer !== $team->stripe_customer_id) abort(403);
  ```
- ✅ Webhook signature verified
- ✅ Stripe keys in .env, never hardcoded
- ✅ Soft deletes on financial records
- ❌ No stripe_id directly from form data

### 5. API Response Audit
Check what data is exposed in API responses:

**Resources:**
```php
public function toArray(): array
{
    return [
        'id' => $this->id,
        'email' => $this->email,
        // ✗ Don't expose stripe_id or team_id
        // ✗ Don't expose passwords or tokens
    ];
}
```

Check:
- ✅ No sensitive fields in resources
- ✅ No exposing team_id in JSON
- ✅ No exposing internal IDs that could be guessed
- ❌ No passwords, API keys, tokens in response

### 6. Environment & Secrets Audit
```bash
# Check for hardcoded secrets
grep -r "stripe_sk\|api_key\|secret" app/ config/ --include="*.php"
# Should show only env() calls, not hardcoded values

# Check .env.example doesn't have real secrets
grep -E "sk_live|pk_live|REAL" .env.example .env
```

Check:
- ✅ All secrets in .env
- ✅ .env in .gitignore
- ✅ .env.example has dummy values only
- ✅ No API keys committed to git
- ❌ No `STRIPE_SK_LIVE=sk_live_xxx` in code

### 7. Common Vulnerabilities Check

**SQL Injection:**
```php
// ✗ Dangerous
DB::raw("WHERE id = " . $id)

// ✓ Safe - uses binding
DB::where('id', $id)
Invoice::where('id', $id)->first()
```

**XSS (Cross-Site Scripting):**
```jsx
// ✗ Dangerous
<div dangerouslySetInnerHTML={{__html: userInput}} />

// ✓ Safe - escaped by React
<div>{userInput}</div>
```

**CSRF (Cross-Site Request Forgery):**
```php
// Laravel handles automatically with middleware
// Verify: config('csrf') has middleware enabled
```

**Rate Limiting:**
```php
Route::post('/login', [...])
    ->middleware('throttle:5,1');  // 5 attempts per minute
```

## Rules

- ✅ Every endpoint needs authorization check
- ✅ Every multi-tenant query needs team filter
- ✅ Every input needs validation
- ✅ Every sensitive operation needs permission check
- ✅ Never trust user input
- ✅ Use Laravel's built-in security helpers
- ❌ Don't invent custom security mechanisms
- ❌ Don't assume frontend validation is enough
- ❌ Don't skip tests for security features

## Output

Security audit report format:

```
# Security Audit Report

## Summary
- Total endpoints checked: X
- Critical issues: X
- High issues: X
- Medium issues: X

## Critical Issues (fix immediately)
- [Issue 1]: [Recommendation]
- [Issue 2]: [Recommendation]

## High Issues (fix before merge)
- [Issue 3]: [Recommendation]

## Medium Issues (address in next sprint)
- [Issue 4]: [Recommendation]

## Verification
- [ ] All critical issues resolved
- [ ] All high issues resolved
- [ ] Tests added for security fixes
- [ ] No new vulnerabilities introduced
```

## Tools & Commands

```bash
# Scan for common patterns
grep -r "::find\|::where" app/Http/Controllers/
grep -r "stripe_" app/
grep -r "password\|secret\|key" config/

# Type checking
vendor/bin/larastan analyse app/

# Test coverage
php artisan test --coverage

# Security headers
php artisan tinker
>>> response()->headers->get('X-Frame-Options')
```