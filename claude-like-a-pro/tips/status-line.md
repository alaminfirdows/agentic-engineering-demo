# Customize your status line

Configure a custom status bar to monitor context window usage, costs, and git status in Claude Code.

## Create the script

Save to `~/.claude/statusline.sh` (replace `~` with your home directory):

```bash
#!/bin/bash
# Read JSON data that Claude Code sends to stdin
input=$(cat)

# Extract fields using jq
MODEL=$(echo “$input” | jq -r '.model.display_name')
DIR=$(echo “$input” | jq -r '.workspace.current_dir')
# The “// 0” provides a fallback if the field is null
PCT=$(echo “$input” | jq -r '.context_window.used_percentage // 0' | cut -d. -f1)

# Output the status line - ${DIR##*/} extracts just the folder name
echo “[$MODEL] 📁 ${DIR##*/} | ${PCT}% context”
```

## Configure Claude Code

Add to `~/.claude/settings.json`:

```json
{
  “statusLine”: {
    “type”: “command”,
    “command”: “~/.claude/statusline.sh”
  }
}
```

## Result

Your status bar now displays the active model, current folder, and context usage percentage in real-time.
