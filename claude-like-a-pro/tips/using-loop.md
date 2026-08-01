# Monitor background tasks with /loop

Use `/loop` to run recurring prompts in the background while you stay focused on real work.

## Usage

```bash
/loop 5m check if the deploy succeeded and report back
/loop 20m /review-pr 1234
/loop check the build status   # defaults to 10m
```

## Examples

- **Deploy monitoring**: `/loop 5m check if the deploy succeeded`
- **PR review cycles**: `/loop 20m /review-pr 1234`
- **Build status**: `/loop check the build status`

## Benefit

Stay in Claude Code without context switching to check external processes. You continue working while the loop monitors in the background and notifies you of changes.
