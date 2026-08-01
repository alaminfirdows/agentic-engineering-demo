# Claude Like a Pro Tips

Best practices for working with Claude Code and this project.

## Git & Commits

### Disable Co-Author Attribution

By default, Claude Code adds a `Co-Authored-By` trailer to commits:

```
Co-Authored-By: Claude Haiku 4.5 <noreply@anthropic.com>
```

To disable this behavior globally or per-project, add to `.claude/settings.json`:

```json
{
  "includeCoAuthoredBy": false
}
```

**Why disable it?**
- Keeps commit history cleaner
- Avoids unnecessary attribution lines
- Useful when commits should appear as solely from the human author
- Already configured in this project's `.claude/settings.json`

### Git Safety

This project enforces strict git safety guardrails:
- **Commits** require explicit approval (e.g., "commit this")
- **Pushes** require explicit approval
- **Force pushes** are always blocked

See `.claude/rules/guardrails.md` for details.

## Code Quality

- Always run `vendor/bin/pint --dirty --format agent` after modifying PHP files
- Activate relevant skills before working in a domain (use Skill tool)
- Run tests before considering work done: `php artisan test --compact`

## Documentation

- Only create docs if explicitly requested
- Keep explanations concise—focus on what's important
- Check sibling files for code conventions before creating new files

## Debugging

- Use Laravel Boost tools (`database-query`, `database-schema`, `browser-logs`)
- Use `search-docs` before making changes—it's version-specific
- Read logs with `read-log-entries` for multi-line PSR-3 formatted logs
