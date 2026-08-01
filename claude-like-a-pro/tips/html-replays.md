# Share Claude Code sessions as HTML replays

`claude-replay` turns your `~/.claude/projects/*.jsonl` transcripts into a self-contained HTML player with speed controls and chapter bookmarks.

## Usage

```bash
npx claude-replay ~/.claude/projects/<your-project>/session-id.jsonl \
  -o replay.html
```

## Benefit

Share reproducible sessions with team members, stakeholders, or for documentation without exposing the underlying terminal or code.
