# Inertia Rules

## Inertia v3 Essentials

This project uses Inertia v3. Key differences from v2:

- No separate Node.js server needed—SSR works in Vite dev mode automatically
- Axios removed—use built-in XHR client or install Axios separately
- `Inertia::lazy()` replaced by `Inertia::optional()`
- Event names: `invalid` → `httpException`, `exception` → `networkError`
- `router.cancel()` → `router.cancelAll()`
- All v2 future options are now always enabled

## Inertia::render() Instead of Blade

Always use `Inertia::render()` for page responses:

```php
// ✓ Good
return Inertia::render('Dashboard', [
    'user' => Auth::user(),
    'stats' => $this->getStats(),
]);

// ✗ Bad
return view('dashboard', ['user' => Auth::user()]);
```

## Props Types

### Default Props

Always sent to frontend:

```php
return Inertia::render('Dashboard', [
    'user' => Auth::user(),  // Always included
]);
```

### Deferred Props

Loaded after page mount with Suspense:

```php
return Inertia::render('Dashboard', [
    'user' => Auth::user(),
    'invoices' => Inertia::defer(fn() => InvoiceResource::collection(...)),
]);
```

Frontend:

```tsx
export default function Dashboard({ user, invoices = undefined }) {
  return (
    <Suspense fallback={<Skeleton />}>
      <InvoicesList invoices={invoices} />
    </Suspense>
  );
}
```

### Optional Props

For conditional data:

```php
return Inertia::render('Dashboard', [
    'user' => Auth::user(),
    'analytics' => Inertia::optional(fn() => $this->getAnalytics()),
]);
```

### Merge Props

For shared layout data:

```php
// middleware or provider
Inertia::share('auth', [
    'user' => Auth::user(),
    'permissions' => Auth::user()?->permissions(),
]);
```

Frontend:

```tsx
import { usePage } from '@inertiajs/react';

export default function Dashboard() {
  const { auth } = usePage().props;
  return <div>{auth.user.name}</div>;
}
```

## useForm Hook

Handle form state and submission:

```tsx
import { useForm } from '@inertiajs/react';

export default function UserForm() {
  const { data, setData, post, errors, processing } = useForm({
    email: '',
    name: '',
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post('/users', {
      onSuccess: () => alert('User created'),
      onError: () => alert('Validation failed'),
    });
  };

  return (
    <form onSubmit={handleSubmit}>
      <input
        value={data.email}
        onChange={(e) => setData('email', e.target.value)}
      />
      {errors.email && <span>{errors.email}</span>}
      <button disabled={processing}>Save</button>
    </form>
  );
}
```

## useHttp Hook

For standalone HTTP requests (v3 feature):

```tsx
import { useHttp } from '@inertiajs/react';

export default function InvoiceList() {
  const { get, loading, errors } = useHttp();

  const loadInvoices = () => {
    get('/invoices', {
      onSuccess: (data) => setInvoices(data),
    });
  };

  return <button onClick={loadInvoices}>Load</button>;
}
```

## router.visit() for Navigation

```tsx
import { router } from '@inertiajs/react';

// Simple navigation
router.visit('/dashboard');

// With method and data
router.post('/users', data, {
  onSuccess: () => console.log('Success'),
  preserveState: true,  // Keep component state
});

// Instant visit (no loading indicator)
router.visit('/dashboard', { instant: true });

// Polling
router.visit('/notifications', {
  polling: 5000,  // Every 5 seconds
});
```

## Prefetching

Preload pages on hover or visibility:

```tsx
import { Link } from '@inertiajs/react';

<Link href="/invoices" prefetch>
  Invoices
</Link>
```

## Optimistic Updates

Update UI before server confirmation:

```tsx
const { post } = useForm(data);

post('/update', {
  onStart: () => setOptimisticData(newData),
  onError: () => setOptimisticData(oldData),
});
```

## Deferred Loading with Suspense

Skeleton pattern:

```tsx
function InvoicesSkeleton() {
  return <div className="animate-pulse">Loading...</div>;
}

export default function Dashboard({ invoices = undefined }) {
  return (
    <Suspense fallback={<InvoicesSkeleton />}>
      <InvoicesList invoices={invoices} />
    </Suspense>
  );
}
```

## Layout Props with useLayoutProps

Share data across layouts:

```php
// Controller
return Inertia::render('Dashboard', [
    'user' => Auth::user(),
])->with('mainNav', $this->getNavigation());
```

Frontend:

```tsx
const { mainNav } = useLayoutProps();
```

## Error Handling

Use the new event names:

```tsx
router.visit('/endpoint', {
  onError: (errors) => console.error('Validation error'),
  on: {
    httpException: (response) => console.error('500 error'),
    networkError: (error) => console.error('Network failed'),
  },
});
```

## Shared Data

Set once, available everywhere:

```php
// AppServiceProvider or middleware
Inertia::share('flash', [
    'success' => session('success'),
    'error' => session('error'),
]);
```

## Page Components Structure

```
resources/js/pages/
├── Dashboard.tsx
├── Users/
│   ├── Index.tsx
│   ├── Create.tsx
│   ├── Show.tsx
│   └── Edit.tsx
└── Invoices/
    ├── Index.tsx
    └── Show.tsx
```

Each file is a full page component rendered by `Inertia::render()`.
