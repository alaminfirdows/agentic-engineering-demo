# Frontend Agent Instructions

## When to Activate

When working on:
- React page components (Inertia)
- Custom hooks
- Form handling
- State management
- API integration via Wayfinder
- Component composition
- Tailwind styling
- Frontend testing

## Methodology

### 1. File Organization

```
resources/js/
├── pages/              # Inertia page components (1-1 with routes)
├── components/         # Reusable UI components
├── hooks/              # Custom React hooks
├── layouts/            # Layout wrappers
├── services/           # API clients
└── types/              # TypeScript types
```

Each page = One Inertia route.
Name pages to match routes: `/dashboard` → `Dashboard.tsx`

### 2. Inertia v3 Patterns

**Page Component Receives Props:**
```tsx
import { FC } from 'react';
import type { User, Invoice } from '@/types/models';

type Props = {
    user: User;
    invoices?: Invoice[];  // Deferred (loaded after mount)
};

export default function InvoicePage({ user, invoices }: Props) {
    return <div>...</div>;
}
```

**Deferred Props with Suspense:**
```tsx
import { Suspense } from 'react';

export default function Dashboard({ user, invoices = undefined }) {
    return (
        <div>
            <UserCard user={user} />
            <Suspense fallback={<InvoicesSkeleton />}>
                <InvoicesList invoices={invoices} />
            </Suspense>
        </div>
    );
}
```

**Backend defers:**
```php
return Inertia::render('Dashboard', [
    'user' => Auth::user(),
    'invoices' => Inertia::defer(fn() => InvoiceResource::collection(...)),
]);
```

### 3. Forms with useForm Hook

```tsx
import { useForm } from '@inertiajs/react';

export default function CreateInvoiceForm() {
    const { data, setData, post, errors, processing } = useForm({
        customer_id: '',
        due_date: '',
        items: [],
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/invoices', {
            onSuccess: () => router.visit('/invoices'),
            onError: () => console.error('Validation failed'),
        });
    };

    return (
        <form onSubmit={handleSubmit}>
            <input
                value={data.customer_id}
                onChange={(e) => setData('customer_id', e.target.value)}
            />
            {errors.customer_id && <span>{errors.customer_id}</span>}
            <button disabled={processing}>Save</button>
        </form>
    );
}
```

### 4. Standalone HTTP Requests (useHttp)

For data fetching that isn't tied to navigation:

```tsx
import { useHttp } from '@inertiajs/react';

export default function NotificationCenter() {
    const { get, loading } = useHttp();
    const [notifications, setNotifications] = useState([]);

    const loadNotifications = () => {
        get('/api/notifications', {
            onSuccess: (data) => setNotifications(data),
        });
    };

    return (
        <div>
            <button onClick={loadNotifications}>Load</button>
            {loading && <span>Loading...</span>}
        </div>
    );
}
```

### 5. Custom Hooks

Extract reusable logic:

```tsx
// hooks/useInvoices.ts
export function useInvoices(filters?: object) {
    const [invoices, setInvoices] = useState([]);
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        setLoading(true);
        fetch(`/api/invoices?${new URLSearchParams(filters)}`)
            .then(r => r.json())
            .then(setInvoices)
            .finally(() => setLoading(false));
    }, [filters]);

    return { invoices, loading };
}

// Usage in component
export default function InvoiceList() {
    const { invoices, loading } = useInvoices({ status: 'unpaid' });
    return <div>{loading ? 'Loading...' : <ItemList items={invoices} />}</div>;
}
```

### 6. Wayfinder Routes

Routes are typed TS functions:

```tsx
import { useInvoiceUpdate } from '@/actions';  // Wayfinder-generated

export default function InvoiceForm({ invoice }) {
    const handleSave = async (data) => {
        // Routes are fully typed
        const result = await useInvoiceUpdate(invoice.id, data);
        if (result.ok) router.visit(`/invoices/${result.data.id}`);
    };

    return <form onSubmit={handleSave}>...</form>;
}
```

### 7. Component Composition

Prefer composition over prop drilling:

```tsx
// ✓ Good: Composition
<InvoicePanel>
    <InvoiceHeader invoice={invoice} />
    <InvoiceDetails invoice={invoice} />
    <InvoiceActions invoice={invoice} />
</InvoicePanel>

// ✗ Bad: Prop drilling
<InvoicePanel
    invoice={invoice}
    showHeader={true}
    showDetails={true}
    showActions={true}
/>
```

### 8. State Management

Keep state local to the component:

```tsx
// ✓ Good: Local state
function EditForm({ invoice }) {
    const [isEditing, setIsEditing] = useState(false);
    return <div>{isEditing ? <Input /> : <View />}</div>;
}

// Use useForm for server sync
const { data, post } = useForm(invoice);
```

Only use Context for truly global state (auth, theme, etc).

### 9. Suspense for Async Data

Show skeleton while loading deferred props:

```tsx
function InvoicesSkeleton() {
    return (
        <div className="space-y-4">
            {Array.from({ length: 3 }).map((_, i) => (
                <div key={i} className="h-12 bg-gray-200 animate-pulse rounded" />
            ))}
        </div>
    );
}

export default function Dashboard({ invoices = undefined }) {
    return (
        <Suspense fallback={<InvoicesSkeleton />}>
            <InvoicesList invoices={invoices} />
        </Suspense>
    );
}
```

### 10. Event Handlers

Use React synthetic events, not DOM listeners:

```tsx
// ✓ Good
<button onClick={handleClick}>Click</button>

// ✗ Bad (avoid)
useEffect(() => {
    document.addEventListener('click', handleClick);
}, []);
```

### 11. Naming Conventions

- **Components**: PascalCase (`UserProfile.tsx`)
- **Hooks**: `use*` camelCase (`useInvoices.ts`)
- **Utilities**: camelCase (`formatDate.ts`)
- **Event handlers**: `handle*` (`handleClick`, `handleSubmit`)
- **Conditions**: `is*` or `has*` (`isLoading`, `hasError`)

### 12. Testing

```tsx
import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';

test('displays user name', () => {
    render(<UserProfile user={{ id: 1, name: 'John' }} />);
    expect(screen.getByText('John')).toBeInTheDocument();
});

test('submits form on button click', async () => {
    const handleSave = vi.fn();
    render(<Form onSave={handleSave} />);
    
    await userEvent.click(screen.getByRole('button', { name: 'Save' }));
    expect(handleSave).toHaveBeenCalled();
});
```

## Commands to Use

```bash
npm run dev          # Start dev server
npm run build        # Build production
npm run lint         # ESLint
npm run format       # Prettier
npm test            # Run tests
npm test -- --ui    # Test UI
```

## What NOT to Do

- Don't prop drill deeply (>2 levels)
- Don't use class components
- Don't fetch in useEffect without deps
- Don't inline styles unless necessary
- Don't create unnecessary hooks
- Don't over-memoize (premature optimization)
- Don't use Axios if XHR client works
- Don't put business logic in components
