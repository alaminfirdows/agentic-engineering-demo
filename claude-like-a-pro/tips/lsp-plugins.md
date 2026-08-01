# Catch type errors before commit with LSP plugins

Language Server plugins give Claude live diagnostics after every save — type errors and unused imports get fixed before you even read the diff.

## Installation

```bash
/plugin install php-lsp@claude-plugins-official
/plugin install typescript-lsp@claude-plugins-official
```

Run `/plugin` and open the Discover tab for the full list of available language servers.

## Benefit

Automatic error detection and fixing in real-time, preventing regressions before they make it to version control.
