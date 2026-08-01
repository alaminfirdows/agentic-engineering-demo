# Run skills in isolated context with `context: fork`

Adding `context: fork` and `agent: Explore` to a skill's frontmatter runs it in a subagent — your main thread only sees the final summary.

## Example

Create `.claude/skills/deep-research/SKILL.md`:

```markdown
---
name: deep-research
description: Research a topic thoroughly
context: fork
agent: Explore
---

Research $ARGUMENTS thoroughly:

1. Find relevant files using Glob and Grep
2. Read and analyze the code
3. Summarize findings with specific file references
```

## Benefit

Isolates complex research in a separate context window, keeping your main session clean while the subagent digs into details.
