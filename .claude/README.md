# Claude AI Operating System for This Project

This `.claude/` directory encodes engineering knowledge, architecture decisions, and workflows specifically for this Laravel + Inertia + React project.

**Think of it as a knowledge base that makes Claude behave like a senior team member who already understands the project.**

---

## Directory Structure

### 📋 `/docs` - Architecture & Knowledge
Deep dives into how the system works.

- **`architecture.md`** - Core data flow, layering, patterns (Actions, DTOs, Services)
- **`domain-model.md`** - Entity relationships, business rules, validation

Use these to understand the system structure before implementing features.

---

### 📚 `/examples` - Code Patterns
Real code examples showing recommended patterns.

- **`action-pattern.php`** - How to write Actions (business logic)
- **`dto-pattern.php`** - How to write DTOs (data transfer objects)
- **`service-pattern.php`** - How to write Services (external concerns)
- **`form-request-pattern.php`** - How to write FormRequests (validation)

Copy these patterns when creating new features.

---

### 🤖 `/agents` - Specialized Instructions
Agent personas for different parts of the system.

- **`backend.md`** - Build backend features (Controllers, Actions, Models)
- **`frontend.md`** - Build frontend (React, Inertia, Hooks)

Use these when working on specific domains.

---

### 🔄 `/workflows` - Common Processes
Step-by-step workflows for recurring tasks.

- **`new-feature.md`** - Complete guide to building a feature (DB → Backend → Frontend → Tests)
- **`bug-fix.md`** - Systematic approach to finding and fixing bugs

Follow these when starting a new task.

---

### ⚡ `/commands` - Quick Reference
Common prompt patterns and what they trigger.

- **`quick-reference.md`** - Examples of prompts and what they do

Use this to understand how to communicate with Claude.

---

### ✅ `/checklists` - Quality Assurance
Checklists to verify code before finalizing.

- **`code-review.md`** - Comprehensive review checklist for architecture, testing, performance, security

Use before committing code.

---

### 💾 `/memory` - Project-Specific Context
Long-term memory for recurring issues, decisions, tech debt.

- **`known-issues.md`** - Bugs, patterns to avoid, architectural decisions, tech debt

Update as you discover patterns and issues.

---

## How to Use This

### 👤 When You're Claude

1. **Before implementing anything**: Read the relevant guide
   - Feature? → Read `/docs/architecture.md` + `/workflows/new-feature.md`
   - Bug fix? → Read `/workflows/bug-fix.md`
   - Backend? → Read `/agents/backend.md` + `/examples/`
   - Frontend? → Read `/agents/frontend.md`

2. **When writing code**: Follow patterns in `/examples/`
   - Never create Actions without looking at existing Actions first
   - Never write Services without understanding their purpose
   - Never skip tests

3. **Before finalizing**: Use `/checklists/code-review.md`
   - Every feature/fix should pass the checklist
   - Run `vendor/bin/pint --dirty` + tests

### 👤 When You're the User

1. **Describe what you want**
   ```
   Build a new feature: Recurring invoices
   ```

2. **Claude automatically**
   - Reads relevant guides from this directory
   - Follows established patterns
   - Writes code matching your conventions
   - Runs tests and checks quality

3. **Review & approve**
   - Code follows your standards
   - Tests pass
   - No regressions

---

## Key Principles Encoded Here

### 1. Architecture Over Configuration
- Explicit layering (HTTP → Application → Domain → Database)
- Actions for business logic (not inline in controllers)
- Services for external concerns
- DTOs for complex data
- No Repository pattern

### 2. Convention Over Custom Code
- Follow existing patterns in `app/Actions/`, `app/Models/`, etc.
- Use FormRequests for validation
- Use Policies for authorization
- Use Eloquent Resources for output

### 3. Testing First
- Every change needs a test
- Feature tests for HTTP flows
- Unit tests for Actions
- Write test before fix

### 4. Type Safety
- TypeScript for React
- PHP 8 type hints everywhere
- Explicit return types
- PHPDoc for complex types

### 5. Security by Default
- Validate all input (FormRequest)
- Authorize all operations (Policies)
- Protect sensitive data (Resources)
- Queue heavy work (don't block HTTP)

---

## Most Important Files

**Start with these when onboarding:**

1. `/docs/architecture.md` - Understand the flow
2. `/agents/backend.md` - Backend conventions
3. `/agents/frontend.md` - Frontend conventions
4. `/examples/*` - Code patterns
5. `/workflows/new-feature.md` - Feature development
6. `/checklists/code-review.md` - Quality gates

---

## Keeping This Up to Date

As you discover patterns:
- Add to `/memory/known-issues.md`
- Update `/examples/` with new patterns
- Refine `/checklists/code-review.md`
- Document new `/workflows/`

The goal is to make this more useful over time, not maintain it as static documentation.

---

## Quick Commands

```bash
# See current status
git status

# Run tests
php artisan test --compact

# Format code
vendor/bin/pint --dirty
npm run format

# Start dev server
npm run dev

# Check routes
php artisan route:list

# Check migrations
php artisan migrate:status
```

---

## When to Escalate to User

- Architectural decisions beyond this guide
- Changing dependencies
- Major refactoring
- Changing test strategy
- Creating new base folders
- Anything that breaks existing conventions

Ask the user when in doubt.

---

## Additional Resources

In your main `CLAUDE.md`:
- Package versions and compatibility
- Project-wide rules (PHP, Pest, Inertia, etc.)
- Deployment strategy
- Global conventions

This `.claude/` directory adds **project-specific patterns** on top of those global rules.

---

## Examples of How This Works

### Scenario 1: Build New Feature

User: *"I want to build a customer portal where users can view and manage subscriptions."*

Claude:
1. Reads `/docs/architecture.md` (understand layers)
2. Reads `/docs/domain-model.md` (understand Subscription entity)
3. Reads `/workflows/new-feature.md` (step-by-step)
4. Copies `/examples/action-pattern.php` for CreateSubscriptionAction
5. Copies `/examples/form-request-pattern.php` for validation
6. Writes SubscriptionController, SubscriptionPolicy, SubscriptionResource
7. Builds frontend in React using `/agents/frontend.md` guidance
8. Writes tests following patterns
9. Runs `/checklists/code-review.md` to verify

### Scenario 2: Fix Tricky Bug

User: *"Invoices show the wrong total sometimes."*

Claude:
1. Reads `/workflows/bug-fix.md`
2. Reproduces the issue
3. Checks `/memory/known-issues.md` for related problems
4. Writes failing test that reproduces bug
5. Investigates root cause (likely an N+1 query or missing eager load)
6. Fixes using patterns from `/examples/`
7. Runs test suite
8. Uses `/checklists/code-review.md` to ensure fix is solid

### Scenario 3: Code Review

User: *"Review this code for quality issues."*

Claude:
1. Reads `/checklists/code-review.md`
2. Checks architecture (follows patterns from `/docs/`)
3. Checks tests (follows patterns from `/agents/`)
4. Checks performance (N+1?, unnecessary renders?)
5. Checks security (validation, authorization?)
6. Reports findings with suggestions

---

## Final Thoughts

This isn't a rules document. It's a **knowledge base**.

The goal is: *"Claude should behave like someone who understands this codebase, knows the patterns, and can write code that fits naturally into the system."*

Update it as you learn. Delete outdated patterns. Add new ones. Make it reflect your team's actual practices.
