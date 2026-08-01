# Pin files to Cursor chat with @ before asking

@-mention 2-3 representative files before your question — Cursor's retrieval is good but explicit is better for refactors.

## Example

```
@src/auth/session.ts @src/auth/types.ts @app/api/me/route.ts

Refactor session reading to use the new SessionContext type.
Update all three files. Keep the existing cookie shape.
```

## Benefit

Ensures the agent sees the full context of related files, reducing misunderstandings and incomplete refactors.
