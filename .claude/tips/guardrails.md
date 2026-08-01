# Git Guardrails: Claude Like a Pro

This project has built-in guardrails to prevent accidental commits and pushes without your explicit approval.

## The Three-Layer Defense

### Layer 1: Permission Denial
Certain git commands are blocked by default:
- `git commit` — always requires approval
- `git push` — always requires approval  
- `git push --force` — always blocked

### Layer 2: Permission Ask
Any `git *` command triggers a permission prompt before running.

### Layer 3: LLM Verification
Claude verifies you **explicitly** said something like:
- "commit this"
- "go ahead and push"
- "create a commit with message..."

Not: "implement the feature" (might commit, might not)

## How to Use

**When Claude asks for approval:**

You: "commit the changes with message 'fix: auth bug'"

Claude checks: Did they explicitly ask to commit? YES ✅

Claude runs: `git commit -m "fix: auth bug"`

**When Claude wants to commit but hasn't asked:**

Claude: "Should I commit this?"

You: "yes, commit with message 'feat: new feature'"

Claude runs commit with your message.

## Why This Exists

Commits and pushes are **irreversible**:
- Can't undo if wrong
- Affects shared history
- Requires explicit intent, not inference

Claude won't guess. You won't be surprised.

## Override (Emergencies Only)

Edit `.claude/settings.json` and remove the git rules from `permissions.deny`:

```json
{
  "permissions": {
    "deny": []  // Removed git rules temporarily
  }
}
```

⚠️ Re-enable them immediately after.

## Quick Reference

| Command | Blocked | Requires Approval |
|---------|---------|-------------------|
| `git add` | ❌ No | No |
| `git status` | ❌ No | No |
| `git log` | ❌ No | No |
| `git commit` | ✅ Yes | ✅ Yes |
| `git push` | ✅ Yes | ✅ Yes |
| `git push --force` | ✅ Yes | Always |
