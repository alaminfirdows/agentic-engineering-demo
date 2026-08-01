**Write the spec, then ask the agent to implement**

A 30-line spec.md beats a 3-line prompt every time — agents follow written specs better than free-form chat.

```jsx
# spec.md — Add rate limiting

## Goal
Per-user 60 req/min on /api/* — return 429 with Retry-After.

## Acceptance
- [ ] middleware.ts intercepts /api/*
- [ ] Redis-backed (use existing src/redis.ts)
- [ ] Test: 61st req in a minute returns 429
- [ ] Existing /api/health stays unlimited

## Don't touch
- auth middleware (separate concern)
- /api/webhooks (provider retries need no limit)
```
