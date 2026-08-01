# Workflows

Mention a workflow to guide Claude through structured phases. Example: "Fix this bug following .claude/workflows/bug-fix.md".

## Available Workflows

| Workflow | Purpose |
|----------|---------|
| `bug-fix.md` | Reproduce → Investigate → Test → Fix → Verify → Clean |
| `new-feature.md` | Design → Backend → Frontend → Test → Polish |
| `database-migration.md` | Plan → Create → Test → Migrate → Verify |
| `laravel-review.md` | Analyze → Security → Performance → Output |
| `security-audit.md` | Scan → Identify → Assess → Remediate |

## Quick Reference

```
"Fix the X bug using the workflow"
"Build feature Y following new-feature.md"
"Create a migration using the workflow"
"Let's do Phase 1 of bug-fix.md"
```

Each workflow includes phases, rules, tested commands, and real-world patterns. Claude references them automatically when you mention the file.
