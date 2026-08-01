# Allowlist read-only commands to kill prompt fatigue

Add `Bash(ls:*)`, `Bash(rg:*)`, `Bash(git status:*)` etc to settings — the agent stops asking permission for safe inspection commands.

## Configuration

Add to `.claude/settings.json`:

```json
{
  "permissions": {
    "allow": [
      "Bash(ls:*)",
      "Bash(rg:*)",
      "Bash(git status:*)",
      "Bash(git diff:*)",
      "Bash(git log:*)",
      "Bash(jq:*)",
      "Bash(pnpm test:*)"
    ]
  }
}
```

## Benefit

No more permission prompts for safe read-only operations, letting the agent work faster without interruptions.
