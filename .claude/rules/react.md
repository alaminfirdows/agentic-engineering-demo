---
paths:
  - "resources/js/**/*.tsx"
  - "resources/js/**/*.ts"
---

# React Rules

## Functional Components Only

Always use function components with hooks:

```tsx
export default function UserProfile({ user }: Props) {
  const [isEditing, setIsEditing] = useState(false);
  return <div>...</div>;
}
```

Never use class components.

## Hooks and Custom Hooks

Extract reusable logic into custom hooks:

```tsx
function useUser(id: number) {
  const [user, setUser] = useState<User | null>(null);
  
  useEffect(() => {
    fetchUser(id).then(setUser);
  }, [id]);

  return user;
}
```

Use hooks from:
- React (useState, useEffect, useCallback, useMemo)
- Inertia (useForm, useHttp, useLayoutProps, router)
- Custom hooks
- Third-party libraries

## Composition Over Props Drilling

Pass data via composition, not through deeply nested props:

```tsx
// ✓ Good
<UserPanel>
  <UserHeader user={user} />
  <UserForm user={user} />
</UserPanel>

// ✗ Bad
<UserPanel user={user} showHeader={true} showForm={true} />
  <UserHeader user={user} />
  <UserForm user={user} />
</UserPanel>
```

## Lazy Load Heavy Pages

Use React.lazy for code splitting:

```tsx
const UserAnalytics = lazy(() => import('./pages/UserAnalytics'));

export default function Dashboard() {
  return (
    <Suspense fallback={<LoadingSpinner />}>
      <UserAnalytics />
    </Suspense>
  );
}
```

## useMemo for Expensive Calculations

Only use when you have a performance bottleneck:

```tsx
const expensiveValue = useMemo(
  () => calculateFromLargeArray(data),
  [data]
);
```

Avoid over-memoizing—measure first.

## useCallback for Event Handlers

Only when passing to memoized children:

```tsx
const handleClick = useCallback(() => {
  saveUser(user.id);
}, [user.id]);
```

## Suspense for Async Data

Use with deferred props:

```tsx
export default function Dashboard({ user, invoices = undefined }) {
  return (
    <div>
      <UserInfo user={user} />
      <Suspense fallback={<InvoicesSkeleton />}>
        <InvoicesList invoices={invoices} />
      </Suspense>
    </div>
  );
}
```

Backend defers the `invoices` prop:

```php
return Inertia::render('Dashboard', [
    'user' => user(),
    'invoices' => Inertia::defer(fn() => InvoiceResource::collection(...)),
]);
```

## Event Delegation

Use React's synthetic events, not manual listeners:

```tsx
// ✓ Good
<button onClick={handleClick}>Click me</button>

// ✗ Bad
useEffect(() => {
  document.addEventListener('click', handleClick);
}, []);
```

## State Lifting

Keep state as local as possible:

```tsx
// ✓ Good: State in form component
function EditUserForm({ user, onSave }) {
  const [formData, setFormData] = useState(user);
}

// ✗ Bad: State in parent
<EditUserForm user={user} onSave={onSave} />
```

## Naming Conventions

- Components: PascalCase (`UserProfile.tsx`)
- Hooks: camelCase starting with `use` (`useUser.ts`)
- Utilities: camelCase (`formatDate.ts`)
- Variables: camelCase (`isActive`, `userData`, `handleClick`)

## No Inline Objects

Prevent unnecessary re-renders:

```tsx
// ✓ Good
const buttonProps = { className: 'primary', disabled: false };
return <Button {...buttonProps} />;

// ✗ Bad
return <Button className="primary" disabled={false} />;
```

Actually, the second example is fine. Only extract when passing to memoized components or deps.

## Import Organization

```tsx
// React and libraries
import { useState, useEffect } from 'react';
import { router } from '@inertiajs/react';

// Components
import UserForm from './UserForm';
import Layout from '@/layouts/Layout';

// Utilities
import { formatDate } from '@/utils/date';

// Types
import type { User } from '@/types/models';
```

## Null Checks and Type Safety

Use TypeScript:

```tsx
type Props = {
  user: User;
  onSave?: (user: User) => void;
};

export default function UserForm({ user, onSave }: Props) {
  const handleSave = () => {
    onSave?.(user);  // Safe optional call
  };
}
```

## Testing

- Test user interactions (clicks, form submission)
- Test conditional rendering
- Mock external API calls
- Use React Testing Library (not implementation details)

```tsx
test('displays user name', () => {
  render(<UserProfile user={{ id: 1, name: 'John' }} />);
  expect(screen.getByText('John')).toBeInTheDocument();
});
```
