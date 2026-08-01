# Auto-generate a tight CLAUDE.md with /init

An experimental `CLAUDE_CODE_NEW_INIT` flag rewrites `/init` to scan your stack, linters, and CI — producing a 60-line CLAUDE.md instead of 400.

## Configuration

Add to `.claude/settings.local.json`:

```json
{
  "env": {
    "CLAUDE_CODE_NEW_INIT": "1"
  }
}
```

## Usage

```bash
/init
```

## Result

Claude Code scans your project stack, linters, and CI configuration to auto-generate a concise, focused CLAUDE.md tailored to your specific setup.
