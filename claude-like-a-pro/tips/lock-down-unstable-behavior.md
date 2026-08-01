# Lock down Claude Code's unstable behavior

Disable 1M context, adaptive thinking, and auto-memory in settings — and pin the subagent model to Sonnet — for noticeably more reliable runs.

## Configuration

Add to `~/.claude/settings.json`:

```json
{
  "effortLevel": "high",
  "env": {
    "CLAUDE_CODE_DISABLE_1M_CONTEXT": "1",
    "CLAUDE_CODE_DISABLE_ADAPTIVE_THINKING": "1",
    "CLAUDE_CODE_DISABLE_AUTO_MEMORY": "1",
    "CLAUDE_CODE_SUBAGENT_MODEL": "sonnet"
  }
}
```

## Effect

More predictable agent behavior, consistent model selection, and reduced erratic decisions in complex tasks.
