# Autonomous task loops with /goal

Make Claude an independent agent that loops, verifies, and iterates until a goal is truly met—no manual prompts needed.

## Usage

```bash
/goal Refactor auth to JWT tokens with 100% test coverage
/goal Fix all TypeScript errors until `npm run type-check` passes
/goal Add billing history feature with tests and database migrations
```

## How it works

- Executes code, tests against success condition, fixes, and repeats
- Independent evaluator model judges if goal is met (prevents premature success claims)
- Runs until done or manually stopped

## When to use

Refactoring, comprehensive testing, bug fixing, feature implementation with tests.

## Benefit

Autonomous iteration on complex tasks without back-and-forth prompting.
