#!/bin/bash

COMMAND="$CLAUDE_TOOL_INPUT"

if echo "$COMMAND" | grep -qE "git (push|commit)|--force|-f\s"; then
    echo "Blocked: Git commit/push operations are not allowed. Create a PR instead."
    exit 2
fi

exit 0
