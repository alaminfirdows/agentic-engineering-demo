# Git Guardrails

Commits and pushes require your explicit approval. Three-layer defense:

## Defense Layers

**Layer 1: Commands Blocked**
- `git commit` — requires approval
- `git push` — requires approval
- `git push --force` — always blocked

**Layer 2: Permission Check**
Any `git *` command triggers a prompt.

**Layer 3: LLM Verification**
Claude checks you explicitly said "commit this" or "push", not just "implement".

## How to Use

**You ask directly:**
```
"commit the changes with message 'fix: auth bug'"
```
Claude runs the commit.

**Claude asks for permission:**
```
Claude: "Should I commit this?"
You: "yes, commit with message 'feat: new feature'"
```
Claude commits with your message.

## Why

Commits are irreversible. No guessing.

## Quick Reference

| Command | Blocked | Requires Approval |
|---------|---------|-------------------|
| `git add` | No | No |
| `git status` | No | No |
| `git commit` | **Yes** | **Yes** |
| `git push` | **Yes** | **Yes** |
| `git push --force` | **Yes** | Always |
