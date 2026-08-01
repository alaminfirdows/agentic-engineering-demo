**Customize your status line**

Configure a custom status bar to monitor context window usage, costs, and git status in Claude Code

Save this to ~/.claude/statusline.sh (where ~ is your home directory, such as /Users/username on macOS or /home/username on Linux):

```
#!/bin/bash
# Read JSON data that Claude Code sends to stdin
input=$(cat)

# Extract fields using jq
MODEL=$(echo "$input" | jq -r '.model.display_name')
DIR=$(echo "$input" | jq -r '.workspace.current_dir')
# The "// 0" provides a fallback if the field is null
PCT=$(echo "$input" | jq -r '.context_window.used_percentage // 0' | cut -d. -f1)

# Output the status line - ${DIR##*/} extracts just the folder name
echo "[$MODEL] 📁 ${DIR##*/} | ${PCT}% context"
```

Tell Claude Code to run your script as the status line. Add this configuration to ~/.claude/settings.json, which sets type to "command" (meaning “run this shell command”) and points command to your script:

```
{
  "statusLine": {
    "type": "command",
    "command": "~/.claude/statusline.sh"
  }
}
```
