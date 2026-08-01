# Application Architecture

## Core Data Flow

```
HTTP Request
    ↓
Route (named route via Wayfinder)
    ↓
Controller (thin, delegates)
    ↓
FormRequest (validates input)
    ↓
Action (executes business logic)
    ↓
Service (cross-cutting concerns: payment, email, etc.)
    ↓
Model (persistence via Eloquent)
    ↓
Database
    ↓
Eloquent Resource (transform for output)
    ↓
Inertia::render() or JSON response
```

## Layering

### HTTP Layer
- Routes (named routes, Wayfinder generates TS)
- Controllers (thin, delegate to actions)
- FormRequests (input validation)

### Application Layer
- Actions (single responsibility, one use case)
- DTOs (Data Transfer Objects - immutable data carriers)
- Services (email, payment, notification, storage)

### Domain Layer
- Models (Eloquent with relationships, no business logic)
- Resources (Eloquent API Resources)
- Policies (authorization)

### Database Layer
- Migrations
- Factories
- Seeders

## Frontend Architecture

```
React Page Component (Inertia)
    ↓
Custom Hooks (useForm, useHttp, custom)
    ↓
Child Components
    ↓
Tailwind CSS + shadcn/ui
    ↓
Wayfinder Routes (call Laravel actions)
    ↓
Backend APIs
```

## Action Pattern (Recommended)

One action = One business use case

```php
app/Actions/CreateInvoiceAction.php
    ↓ Uses
app/Services/InvoiceNumberService.php (generate next number)
app/Services/EmailService.php (send confirmation)
    ↓ Persists
app/Models/Invoice.php
    ↓ Validated by
app/Http/Requests/CreateInvoiceRequest.php
    ↓ Called from
app/Http/Controllers/InvoiceController.php::store()
    ↓ Rendered via
app/Http/Resources/InvoiceResource.php
```

## DTO Pattern (For Complex Data)

Use for:
- Transferring computed data between layers
- Immutable data objects
- Type-safe parameter passing

```php
app/Data/InvoiceData.php
    - userId
    - invoiceNumber
    - items[]
    - total
    - dueDate
```

## Service Pattern (For Cross-Cutting Concerns)

One service = One external system or concern

```php
app/Services/PaymentService.php
    - charge()
    - refund()
    - getStatus()

app/Services/EmailService.php
    - send()
    - queue()

app/Services/StorageService.php
    - upload()
    - delete()
    - getUrl()
```

## Policy Pattern (Authorization)

Define capabilities per resource:

```php
app/Policies/InvoicePolicy.php
    - view() - can user see this invoice?
    - update() - can user edit?
    - delete() - can user delete?
```

## FormRequest Pattern (Validation)

Centralize validation logic:

```php
app/Http/Requests/CreateInvoiceRequest.php
    - rules()
    - authorize()
    - prepareForValidation()
```

## Testing Structure

```
tests/Feature/
    - InvoiceControllerTest.php (HTTP endpoints)
    - CreateInvoiceActionTest.php (business logic)

tests/Unit/
    - InvoiceModelTest.php (relationships, scopes)
    - PaymentServiceTest.php (external integration)
```
