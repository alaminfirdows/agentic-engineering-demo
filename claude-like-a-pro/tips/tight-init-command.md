**Auto-generate a tight CLAUDE.md with /init**

An experimental `CLAUDE_CODE_NEW_INIT` flag rewrites `/init` to scan your stack, linters, and CI — producing a 60-line CLAUDE.md instead of 400.

```jsx
// .claude/settings.local.json
{
  "env": {
    "CLAUDE_CODE_NEW_INIT": "1"
  }
}

// Then:
`/init`
```
