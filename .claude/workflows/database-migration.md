# Database Migration Workflow

## Purpose
Plan, implement, test, and verify database schema changes safely with zero downtime.

## When to Use
- Adding new tables or columns
- Changing column types
- Adding constraints or indexes
- Renaming tables or columns
- Removing deprecated fields (with backfill plan)

## Steps

### 1. Plan the Migration
Before writing code:

**Understand current schema:**
```bash
mcp__laravel-boost__database-schema --filter=invoices
```

**Plan the change:**
- What's the current structure?
- What needs to change and why?
- How will it affect existing data?
- Any backward compatibility issues?
- Zero-downtime approach?

**Examples:**
- Adding nullable column: no data migration needed
- Adding NOT NULL column: need default or backfill
- Renaming column: create new, backfill, drop old (3 migrations)

### 2. Create Migration File
```bash
php artisan make:migration add_tax_to_invoices --table=invoices
# or
php artisan make:migration create_invoices_table --create=invoices
```

**File location:** `database/migrations/`
**Naming:** `YYYY_MM_DD_HHMMSS_description.php`

### 3. Write Forward Migration (Up)
```php
public function up(): void
{
    Schema::table('invoices', function (Blueprint $table) {
        $table->decimal('tax', 8, 2)->nullable();  // New column
        $table->index('team_id');                   // Add index
        $table->foreign('supplier_id')
            ->references('id')
            ->on('suppliers');                      // Add constraint
    });
}
```

**Best practices:**
- ✅ Make new columns nullable first (safer)
- ✅ Always add indexes to foreign keys
- ✅ Always add timestamp columns
- ✅ Use proper data types (decimal for money, not float)
- ✅ Use soft deletes for audit trails
- ❌ No hardcoded values in schema
- ❌ No dropping columns immediately (plan for rollback)

### 4. Write Rollback Migration (Down)
```php
public function down(): void
{
    Schema::table('invoices', function (Blueprint $table) {
        $table->dropIndex(['team_id']);
        $table->dropForeignKey(['supplier_id']);
        $table->dropColumn('tax');
    });
}
```

**Rules:**
- ✅ Exactly reverses the up() changes
- ✅ Must work even if up() was never run
- ✅ Should succeed without data loss (if possible)

### 5. Data Migration (if needed)
For adding NOT NULL columns or changing data:

```php
public function up(): void
{
    // Step 1: Add nullable column
    Schema::table('invoices', function (Blueprint $table) {
        $table->string('invoice_number')->nullable();
    });
    
    // Step 2: Backfill data
    Invoice::query()
        ->whereNull('invoice_number')
        ->each(function (Invoice $invoice) {
            $invoice->update(['invoice_number' => 'INV-' . $invoice->id]);
        });
    
    // Step 3: Make NOT NULL
    Schema::table('invoices', function (Blueprint $table) {
        $table->string('invoice_number')->change();
    });
}
```

**Pattern:**
1. Add nullable column or temp column
2. Backfill existing data
3. Change to NOT NULL (or drop temp)

### 6. Test the Migration
```bash
# Fresh database
php artisan migrate:fresh

# Verify schema
mcp__laravel-boost__database-schema --filter=invoices

# Test data integrity
php artisan tinker
>>> Invoice::count()
>>> Invoice::whereNull('tax')->count()
```

### 7. Test Rollback
```bash
# Rollback one step
php artisan migrate:rollback --step=1

# Verify rolled back
mcp__laravel-boost__database-schema --filter=invoices

# Migrate again
php artisan migrate
```

### 8. Update Models & Factories
```php
// app/Models/Invoice.php
protected $fillable = ['tax', 'invoice_number'];

protected $casts = [
    'tax' => 'decimal:2',
];

// database/factories/InvoiceFactory.php
'tax' => $this->faker->randomFloat(2, 0, 100),
'invoice_number' => 'INV-' . $this->faker->unique()->numerify('######'),
```

### 9. Write Tests
```php
test('migration creates tax column', function () {
    $invoice = Invoice::factory()->create(['tax' => 10.50]);
    expect($invoice->tax)->toBe(10.50);
});

test('migration backfills invoice numbers', function () {
    Invoice::factory()->create();
    // Run migration manually if testing backfill logic
    expect(Invoice::first()->invoice_number)->toMatch('/INV-/');
});
```

### 10. Code Review & Merge
Before merging:
- [ ] Migration is reversible (down() works)
- [ ] Data backfill is safe (handles edge cases)
- [ ] No data loss on rollback
- [ ] Models and factories updated
- [ ] Tests verify schema changes
- [ ] Foreign keys have proper indexes
- [ ] Timestamps and soft deletes present

## Rules

**Column Design**
- ✅ Use `nullable()` for optional data
- ✅ Use `default()` for sensible defaults
- ✅ Use `unique()` for uniqueness constraints
- ✅ Use `decimal(8,2)` for money, NOT `float`
- ✅ Use `text` for long strings, `string` for short
- ✅ Always add timestamps: `$table->timestamps()`
- ❌ No `nullable()` on foreign keys (use `constrainedBy()`)
- ❌ No removing columns without a plan

**Indexes**
- ✅ Index foreign keys automatically
- ✅ Index frequently queried columns
- ✅ Index columns used in WHERE clauses
- ✅ Index columns used in JOIN conditions
- ❌ No unnecessary indexes (they slow writes)

**Constraints**
- ✅ Foreign keys should reference by ID
- ✅ Cascade delete only for non-financial data
- ✅ Use `restrict` for important relationships
- ❌ Never soft-delete data that must stay referenced

**Zero-Downtime**
- ✅ Add nullable columns (no downtime)
- ✅ Add indexes on existing columns (usually OK)
- ✅ Backfill in separate migration after deploying schema
- ❌ Don't add NOT NULL without backfill
- ❌ Don't drop columns until code doesn't use them

## Output

Migration checklist:
- [ ] Migration file created with proper naming
- [ ] Up method implements schema changes
- [ ] Down method reverses all changes
- [ ] Data backfill logic (if needed)
- [ ] Models updated with new columns
- [ ] Factories generate data for new columns
- [ ] Tests verify schema changes
- [ ] Migration reversible (test rollback)
- [ ] No data loss on rollback
- [ ] Indexes on foreign keys
- [ ] Timestamps present
- [ ] Ready for code review

## Example: Add Subscription Column
```bash
php artisan make:migration add_subscription_status_to_teams --table=teams
```

```php
public function up(): void
{
    Schema::table('teams', function (Blueprint $table) {
        $table->string('subscription_status')->default('free');  // or 'active', 'past_due', 'canceled'
        $table->timestamp('subscription_ends_at')->nullable();
        $table->string('stripe_subscription_id')->nullable()->unique();
    });
}

public function down(): void
{
    Schema::table('teams', function (Blueprint $table) {
        $table->dropUnique(['stripe_subscription_id']);
        $table->dropColumn(['subscription_status', 'subscription_ends_at', 'stripe_subscription_id']);
    });
}
```

Test: `php artisan migrate:fresh && php artisan migrate:rollback && php artisan migrate`