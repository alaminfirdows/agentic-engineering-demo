**Allowlist read-only commands to kill prompt fatigue**

Add `Bash(ls:*)`, `Bash(rg:*)`, `Bash(git status:*)` etc to settings — the agent stops asking permission for safe inspection.

```jsx
// .claude/settings.json
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
