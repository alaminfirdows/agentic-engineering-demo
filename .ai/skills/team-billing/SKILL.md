---
name: team-billing
description: Enforce team isolation patterns and Stripe billing logic when modifying controllers or models.
---

# Team Billing Development

Use this skill whenever building features related to user accounts, payments, or team-scoped queries.

## Coding Conventions

- **Team Scope:** Every team-facing model must implement the `BelongsToTeam` trait.
- **Stripe Actions:** Do not call Cashier methods inside controllers directly; abstract them into an Action class under `App\Actions\Billing`.
- **Testing:** Always create a `Feature/BillingTest` using Pest, mocking the Stripe Gateway API response.
