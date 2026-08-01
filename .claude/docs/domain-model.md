# Domain Model Overview

Document your application's core entities and relationships.

## Entity Relationship Diagram

```
User (1) ──────→ (M) Invoice
         ──────→ (M) Payment
         ──────→ (M) Subscription

Invoice (1) ──────→ (M) InvoiceItem
         ──────→ (1) Customer

Payment (M) ──────← (1) Invoice
        ──────→ (1) PaymentMethod

Subscription (1) ──────→ (M) SubscriptionRenewal
            ──────→ (1) Plan
```

---

## Core Entities

### User
Represents an authenticated user in the system.

**Attributes:**
- `id` - Primary key
- `name` - User's full name
- `email` - Unique email address
- `password` - Hashed password
- `created_at`, `updated_at` - Timestamps
- `deleted_at` - Soft delete (nullable)

**Relationships:**
- Has many Invoices
- Has many Payments
- Has many Subscriptions

**Key Behaviors:**
- Can create invoices
- Can view their own data
- Can subscribe to plans

---

### Invoice
Represents a billable transaction.

**Attributes:**
- `id` - UUID
- `user_id` - Foreign key to User
- `invoice_number` - Unique identifier (INV-2026-000001)
- `amount` - Total invoice amount
- `status` - draft|sent|paid|cancelled
- `due_date` - Payment deadline
- `paid_at` - Payment completion timestamp (nullable)
- `created_at`, `updated_at` - Timestamps
- `deleted_at` - Soft delete (nullable)

**Relationships:**
- Belongs to User
- Has many InvoiceItems
- Has one Payment (if paid)

**Key Behaviors:**
- Cannot be deleted (soft delete + marked as cancelled)
- Can only be refunded (not directly deleted)
- Status transitions: draft → sent → paid

**Business Rules:**
- Invoice number is auto-generated
- Amount should equal sum of line items
- Cannot be paid twice
- Due date must be in the future

---

### InvoiceItem
Line item on an invoice.

**Attributes:**
- `id` - Primary key
- `invoice_id` - Foreign key to Invoice
- `description` - Line description
- `quantity` - Quantity ordered
- `unit_price` - Price per unit
- `amount` - Computed (quantity × unit_price)
- `created_at` - Timestamp

**Relationships:**
- Belongs to Invoice

---

### Payment
Represents a payment transaction.

**Attributes:**
- `id` - UUID
- `invoice_id` - Foreign key to Invoice
- `user_id` - Foreign key to User
- `transaction_id` - External ID (from payment provider)
- `amount` - Amount paid
- `method` - Payment method (card|bank|paypal)
- `status` - succeeded|failed|refunded
- `created_at`, `updated_at` - Timestamps

**Relationships:**
- Belongs to Invoice
- Belongs to User

**Key Behaviors:**
- Records external transaction ID
- Supports refunds
- Can be marked as failed

---

### Subscription
User subscription to a plan.

**Attributes:**
- `id` - UUID
- `user_id` - Foreign key to User
- `plan_id` - Foreign key to Plan
- `status` - active|cancelled|expired
- `started_at` - Subscription start date
- `renews_at` - Next renewal date
- `cancelled_at` - Cancellation timestamp (nullable)
- `created_at`, `updated_at` - Timestamps

**Relationships:**
- Belongs to User
- Belongs to Plan
- Has many SubscriptionRenewals

**Key Behaviors:**
- Only one active subscription per user
- Auto-renews on `renews_at`
- Can be cancelled anytime
- Grace period: 14 days after expiration

---

### Plan
Subscription plan definition.

**Attributes:**
- `id` - Primary key
- `name` - Plan name (Professional, Enterprise)
- `slug` - URL-safe identifier (professional, enterprise)
- `price` - Monthly/annual price
- `billing_cycle` - monthly|annual
- `features` - JSON array of feature strings
- `created_at`, `updated_at` - Timestamps

**Relationships:**
- Has many Subscriptions

---

## Data Validation Rules

### User
- Email must be unique and valid format
- Name must be 1-255 characters
- Password minimum 8 characters

### Invoice
- User must exist
- Due date must be in future
- Must have at least one item
- Total amount must be > 0

### Payment
- Invoice must be unpaid
- Amount must match invoice total
- Transaction ID must be unique

### Subscription
- User can only have one active subscription
- Plan must exist
- Start date cannot be in past

---

## Business Rules

### Invoicing Flow
```
1. User creates invoice with items
2. Invoice generated with unique number
3. Invoice in "draft" status
4. User sends invoice (status: "sent")
5. Customer pays (Payment created)
6. Invoice status: "paid"
7. Invoice can be refunded (not deleted)
```

### Subscription Flow
```
1. User selects plan
2. Subscription created (status: "active")
3. System creates renewal reminder 3 days before expiration
4. On renewal date, system creates next subscription
5. User can cancel anytime
6. 14-day grace period after expiration
7. Auto-cleanup after grace period
```

### Authorization Rules
- Users can only view their own invoices
- Users can only edit unpaid invoices
- Only invoice owner can process payment
- Admins can override any permission

---

## Query Patterns

### Common Queries

**Get all invoices for user:**
```php
$invoices = $user->invoices()->with('items')->get();
```

**Get unpaid invoices:**
```php
$unpaid = Invoice::where('status', '!=', 'paid')->get();
```

**Get upcoming subscription renewals:**
```php
$renewing = Subscription::where('renews_at', '<=', now()->addDays(3))
    ->where('status', 'active')
    ->get();
```

**Calculate total revenue:**
```php
$revenue = Invoice::where('status', 'paid')
    ->sum('amount');
```

---

## Computed Fields

### Invoice
- `total` - Sum of all items
- `is_overdue` - Due date in past?
- `days_until_due` - Days remaining

### Subscription
- `is_active` - Currently active?
- `expires_soon` - Expires in < 7 days?
- `in_grace_period` - Within 14 days of expiration?

---

## Events

### Domain Events

These events should be fired:

**InvoiceCreated**
- When: New invoice created
- Data: Invoice, User
- Action: Log audit trail

**InvoiceSent**
- When: Invoice sent to customer
- Data: Invoice, recipient email
- Action: Send email notification

**PaymentProcessed**
- When: Payment received
- Data: Payment, Invoice, User
- Action: Update invoice status, send receipt

**SubscriptionCreated**
- When: User subscribes to plan
- Data: Subscription, User, Plan
- Action: Grant access to features

**SubscriptionRenewalReminder**
- When: 3 days before renewal
- Data: Subscription, User, Plan
- Action: Send renewal reminder email

---

## Audit Trail

Keep complete audit logs of:
- Invoice creation, status changes
- Payment transactions
- Subscription changes
- User data modifications

Use soft deletes to preserve data history.

---

## Growth Considerations

As the app scales:

1. **Invoices Table**: Index on (user_id, created_at)
2. **Payments Table**: Index on transaction_id (external)
3. **Subscriptions Table**: Index on (user_id, status, renews_at)
4. **Archive old data**: Move invoices > 2 years to archive table
5. **Cache frequently accessed**: User plans, active subscriptions
6. **Queue**: Invoice generation, renewal checks, email sending

---

## References

- Related Actions: `app/Actions/`
- Database Schema: `database/migrations/`
- API Resources: `app/Http/Resources/`
- Models: `app/Models/`
- Policies: `app/Policies/`
- Tests: `tests/Feature/`, `tests/Unit/`
