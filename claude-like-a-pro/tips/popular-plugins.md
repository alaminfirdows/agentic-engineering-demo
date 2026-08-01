# Popular Claude Code Plugins

Each plugin is a folder with its own config. You can browse what's available and install from there.

## Technical Plugins

| Plugin | Purpose |
|--------|---------|
| **typescript-lsp** | TypeScript language server integration. Real type checking, go-to-definition, and error diagnostics instead of guessing. Probably the single most impactful plugin. |
| **playwright** | Browser automation and testing. Claude can launch browsers, navigate pages, take screenshots, fill forms, and run end-to-end tests. |
| **security-guidance** | Vulnerability scanning. Catches hardcoded secrets, auth bypass patterns, and injection risks as Claude writes code. |
| **code-review** | Structured code review with quality scoring. Gives Claude a framework for reviewing PRs. |
| **pr-review-toolkit** | PR-focused code review. Generates review comments, suggests changes, and checks for common PR issues. |
| **commit-commands** | Standardizes commit messages. Good for conventional commits or consistent git history. |
| **code-simplifier** | Identifies overly complex code and suggests simplifications. Measures cyclomatic complexity. |
| **context7** | Documentation lookup. Claude fetches up-to-date docs instead of relying on training data. |

## Non-Technical Plugins

| Plugin | Purpose |
|--------|---------|
| **claude-md-management** | Auto-maintains your CLAUDE.md project file. Keeps it structured and prevents decay. |
| **explanatory-output-style** | More educational output. Explains the "why" behind decisions, not just the "what." |
| **learning-output-style** | Teaching-focused. Breaks things down more gradually and checks understanding. |
| **frontend-design** | UI/UX design patterns. Claude references design systems and accessibility standards. |
| **claude-code-setup** | Project scaffolding. Sets up new projects with proper structure and boilerplate. |
| **feature-dev** | Feature development workflow. Structures how Claude builds new features. |

## High-Impact Recommendations

### Tier 1 (Significant Difference)

- **typescript-lsp** — Code quality noticeably improves. Claude stops guessing at types.
- **security-guidance** — Catches real security issues Claude might miss.
- **context7** — No more outdated API suggestions; always works with current docs.
- **playwright** — Real browser automation beats screenshot guessing for frontend work.

### Tier 2 (Context-Dependent)

- **code-review** — Good for solo developers wanting a second pair of eyes.
- **claude-md-management** — Good if your CLAUDE.md gets messy frequently.
- **explanatory-output-style** — Good if you want to understand the code, not just use it.
- **frontend-design** — Good if you're building UI and want better defaults.

## Installation

```bash
/plugin install typescript-lsp@claude-plugins-official
/plugin install security-guidance@claude-plugins-official
# Run /plugin and browse the Discover tab for the full list
```

## The Bigger Picture

Claude Code at default settings runs at approximately 60% capacity. Plugins provide real capabilities:
- TypeScript LSP gives real type awareness
- Security guidance catches vulnerabilities passively
- Context7 ensures current documentation

There are 53+ plugins available. Browse them all with:

```bash
ls ~/.claude/plugins/marketplaces/claude-plugins-official/plugins/
```

