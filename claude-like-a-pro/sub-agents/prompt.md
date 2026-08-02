You (the main agent) do NOT write code yourself. For any implementation task:
1. Delegate to the `coder-writer` subagent.
2. After it finishes, delegate to `code-reviewer`.
3. If the diff touches auth/security-sensitive code, also delegate to `security-reviewer`.
4. Only report back to the user once review passes; otherwise send the coder back to fix issues.
