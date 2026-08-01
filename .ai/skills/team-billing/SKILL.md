---
name: team-billing
description: Enforce team isolation patterns and Stripe billing logic when modifying controllers or models.
paths:
  - "app/Http/Controllers/**/*.php"
  - "app/Models/**/*.php"
  - "app/Actions/**/*.php"
---

# Team Billing & Multi-Tenancy Security

## Team Isolation (Critical)

Every data query must verify team ownership. Never access records without team context.

### Policy-First Authorization

```php
// ✓ Always check policy first
public function show(Invoice $invoice)
{
    $this->authorize('view', $invoice);  // Enforces team_id match
    return new InvoiceResource($invoice);
}

// ✗ Bad: Direct access without team check
public function show($id)
{
    return Invoice::find($id);  // Leaks other teams' data
}
```

### Model Scope Pattern

```php
// app/Models/Invoice.php
public function scopeForTeam(Builder $query, Team $team): Builder
{
    return $query->where('team_id', $team->id);
}

// Usage
$invoices = Invoice::forTeam($user->team)->get();
```

### Policy Implementation

```php
// app/Policies/InvoicePolicy.php
public function view(User $user, Invoice $invoice): bool
{
    return $user->team_id === $invoice->team_id;
}

public function update(User $user, Invoice $invoice): bool
{
    return $user->team_id === $invoice->team_id && $user->hasRole('admin');
}

public function delete(User $user, Invoice $invoice): bool
{
    return $user->team_id === $invoice->team_id && $user->hasRole('admin');
}
```

### Middleware Layer

```php
// app/Http/Middleware/VerifyTeamAccess.php
public function handle(Request $request, Closure $next)
{
    $model = $request->route()->parameter('invoice');  // or any model
    
    if ($model->team_id !== auth()->user()->team_id) {
        abort(403, 'Unauthorized team access');
    }
    
    return $next($request);
}
```

## Stripe Billing with Laravel Cashier

### Subscription States

Track in `subscriptions` table:
- `status`: 'active' | 'past_due' | 'canceled' | 'incomplete'
- `stripe_id`: Stripe subscription reference
- `price_id`: Stripe price (monthly/yearly)
- `current_period_end`: Next billing date

### Action Pattern (Abstract Stripe)

Never call Cashier directly in controllers. Use Actions:

```php
// app/Actions/Billing/CreateTeamSubscriptionAction.php
class CreateTeamSubscriptionAction
{
    public function __construct(private StripeService $stripe) {}
    
    public function execute(Team $team, string $priceId): Subscription
    {
        $subscription = $team->subscriptionBuilder()
            ->add('price', $priceId)
            ->create();
        
        return Subscription::create([
            'team_id' => $team->id,
            'stripe_id' => $subscription->id,
            'price_id' => $priceId,
            'status' => $subscription->status,
            'current_period_end' => now()->addMonth(),
        ]);
    }
}
```

### Controller Usage

```php
// app/Http/Controllers/BillingController.php
public function upgrade(UpgradeRequest $request, CreateTeamSubscriptionAction $action)
{
    $subscription = $action->execute(auth()->user()->team, $request->price_id);
    
    return redirect('/billing')->with('success', 'Upgraded successfully');
}
```

### Webhook Handling

```php
// app/Http/Controllers/StripeWebhookController.php
public function handleSubscriptionUpdated(array $payload)
{
    $subscription = Subscription::where('stripe_id', $payload['id'])->firstOrFail();
    
    $subscription->update([
        'status' => $payload['status'],
        'current_period_end' => Carbon::createFromTimestamp($payload['current_period_end']),
    ]);
}

public function handleSubscriptionDeleted(array $payload)
{
    Subscription::where('stripe_id', $payload['id'])
        ->update(['status' => 'canceled']);
}
```

## Feature Gating by Subscription

```php
// app/Models/Team.php
public function isPro(): bool
{
    return $this->subscription?->status === 'active'
        && str_contains($this->subscription->price_id, 'pro');
}

public function canCreateInvoices(): bool
{
    return $this->isPro() || $this->subscription?->invoice_limit === null;
}
```

### In Controllers

```php
public function create()
{
    if (!auth()->user()->team->canCreateInvoices()) {
        return redirect('/billing/upgrade');
    }
    
    return inertia('CreateInvoice');
}
```

### In Policies

```php
public function create(User $user): bool
{
    return $user->team->canCreateInvoices();
}
```

## Usage Tracking

```php
// Track API calls or resource creation
public function trackUsage(Team $team, string $metric)
{
    $team->increment("usage_{$metric}");
    
    $limit = $team->subscription?->limits[$metric] ?? 0;
    if ($limit > 0 && $team->{"usage_{$metric}"} >= $limit) {
        throw new UsageLimitExceededException("$metric limit reached");
    }
}
```

## Testing

```php
// tests/Feature/BillingTest.php
test('user can upgrade to pro', function () {
    $team = Team::factory()->create();
    $user = User::factory()->for($team)->create();
    
    $response = $this->actingAs($user)
        ->post('/billing/upgrade', ['price_id' => 'price_pro']);
    
    expect($response)->redirect('/billing');
    expect($team->refresh()->isPro())->toBeTrue();
});

test('webhook updates subscription status', function () {
    $subscription = Subscription::factory()->create();
    
    $event = [
        'type' => 'customer.subscription.updated',
        'data' => [
            'object' => [
                'id' => $subscription->stripe_id,
                'status' => 'past_due',
            ],
        ],
    ];
    
    $this->postJson('/stripe/webhook', $event, [
        'Stripe-Signature' => $this->fakeSignature($event),
    ]);
    
    expect($subscription->refresh()->status)->toBe('past_due');
});
```

## Security Checklist

- [ ] All model queries use `forTeam()` scope
- [ ] All routes protected by policies checking `team_id`
- [ ] Controllers never call Stripe directly (use Actions)
- [ ] Webhook signature verified: `Stripe::webhook()->verify()`
- [ ] Stripe keys in `.env`, never hardcoded
- [ ] Soft-deletes on financial records
- [ ] Audit log for billing changes (timestamps, user IDs)
- [ ] Request validation prevents arbitrary team_id injection
- [ ] Frontend doesn't expose team IDs in API params
- [ ] Rate limiting on Stripe API calls

## Common Vulnerabilities

❌ Accessing model without authorization
```php
return Invoice::find($request->invoice_id);
```

✅ Proper access
```php
$invoice = Invoice::find($request->invoice_id);
$this->authorize('view', $invoice);
return $invoice;
```

❌ Trusting Stripe webhook without verification
```php
$subscription = Subscription::where('stripe_id', $request->input('stripe_id'))->first();
```

✅ Verified access
```php
$event = Stripe::webhook()->verify($payload);
$subscription = Subscription::where('stripe_id', $event['id'])->firstOrFail();
```

❌ Feature gating without subscription check
```php
public function export() { ... }  // Anyone can export
```

✅ Protected feature
```php
public function export()
{
    abort_unless(auth()->user()->team->isPro(), 403);
    // Export logic
}
```
