# Agentic Engineering Demo: Claude + Laravel

This repository is the **official reference for building AI-agent-driven Laravel applications**. It demonstrates how Claude (via Claude Code and the Claude API) integrates seamlessly with Laravel to create intelligent, production-ready systems powered by agentic workflows.

Built on the [Laravel Starter Kit](https://laravel.com/docs/starter-kits) with React + Inertia, this project showcases best practices for:

- **Structured AI agent orchestration** via `.claude/` folder patterns
- **AI-powered development workflows** with Claude Code integration
- **Agent-driven backend logic** with Laravel's modern architecture
- **Multi-agent coordination** through custom skills and commands

---

## What Is This?

This is **not just a boilerplate**—it's a **documented reference implementation** showing how to:

1. **Configure Claude** to understand and work within your Laravel codebase
2. **Set up agents** that can independently tackle complex tasks
3. **Define custom skills** for domain-specific workflows
4. **Orchestrate multi-agent systems** for parallel, intelligent task execution

The `.claude/` and `.ai/` directories are the **core innovation** here. They demonstrate how to guide Claude's behavior and extend Claude Code with custom workflows.

---

## Key Directories

### `.claude/` – Claude Code Configuration

The single source of truth for how Claude interacts with your project:

- **`agents/`** – Multi-agent team definitions for complex tasks
- **`commands/`** – Custom Claude Code commands (e.g., `/security-review`, `/fix-issue`)
- **`skills/`** – Specialized workflows for specific domains (Laravel patterns, React development, testing)
- **`rules/`** – Layered guidelines that override and extend Claude's defaults
- **`docs/`** – Knowledge base documentation Claude references
- **`examples/`** – Real examples of agent-driven problem-solving
- **`memory/`** – Session memory and context for multi-turn workflows
- **`workflows/`** – Deterministic orchestration scripts for agent teams
- **`settings.json`** – Harness configuration, permissions, hooks

### `.ai/` – Broader AI Integrations

Extends Claude Code with additional integrations:

- **`commands/`** – Custom CLI commands for AI workflows
- **`skills/`** – Additional skills for API-driven or headless scenarios

---

## How Claude Code Works With This Project

When you run Claude Code in this directory, it:

1. **Reads CLAUDE.md** – Learns the project's conventions, tech stack, and rules
2. **Discovers `.claude/rules/`** – Applies layered behavior rules (foundation → boost → specific tools)
3. **Activates skills** – Automatically invokes domain-specific workflows (Laravel, React, testing, etc.)
4. **Uses MCP servers** – Integrates with Laravel Boost (schema, logs, documentation, database queries)
5. **Runs workflows** – Orchestrates multi-agent teams for complex tasks

**Example workflow:**
```bash
# Claude Code reads the task, checks CLAUDE.md, activates laravel-best-practices skill
# then runs a multi-agent workflow to review, refactor, and test your code
claude-code
> Refactor this controller to follow SOLID principles and add comprehensive tests
```

---

## The Technology Stack

This is production-grade, built with latest versions:

- **Backend:** Laravel 13, PHP 8.5
- **Frontend:** React 19, Inertia.js v3, TailwindCSS 4
- **Testing:** Pest v5, PHPUnit 13
- **AI Integration:** Claude Code, Claude API, Laravel Boost MCP, Laravel MCP
- **Development:** Vite, Pint (formatter), Pail (logging), Wayfinder (type-safe routing)
- **DevOps:** Laravel Sail (Docker)

---

## Quick Start

### 1. Clone and Install

```bash
git clone <this-repo>
cd agentic-engineering-demo
composer install
npm install
cp .env.example .env
php artisan key:generate
```

### 2. Set Up Database

```bash
php artisan migrate
npm run dev  # or composer run dev
```

### 3. Open with Claude Code

```bash
# Start Claude Code in this directory
claude-code .
```

Claude will automatically load `.claude/` configuration and be ready to help.

---

## Example: Using Claude Code

### Security Review

```
/security-review
```

Triggers a multi-agent workflow that:
- Scans the codebase for vulnerabilities
- Checks Laravel security best practices
- Runs adversarial verification on findings
- Generates a prioritized report

### Fix a Bug

```
/fix-issue path/to/file.php "bug description"
```

Claude will:
- Analyze the code and error logs
- Check database schema if needed
- Write and run tests
- Apply the fix with context awareness

### Add a Feature

```
Refactor the authentication system to support OAuth2 with rate limiting
```

Claude will:
- Search version-specific documentation
- Activate laravel-best-practices skill
- Design the implementation with your input
- Write tests, migrations, and UI components
- Verify everything works

---

## The `.claude/` Reference

### Rules Structure

Rules are **layered**, each building on the previous:

1. **Foundation** (`rules/foundation/`) – Universal coding standards (PHP 8 syntax, naming, structure)
2. **Boost** (`rules/boost/`) – Laravel Boost integration (MCP tools, documentation search)
3. **Ecosystem** (`rules/ecosystem/`) – Version-specific patterns (Laravel 13, React 19, Inertia v3)
4. **Specialized** (`rules/specialized/`) – Domain rules (API patterns, testing, deployment)

### Skills Activation

Skills are **context-aware**. They activate when:

- You mention a keyword (e.g., "security review" → `security-review` skill)
- You're working in a domain (e.g., editing React component → `inertia-react-development` skill)
- You explicitly invoke them (e.g., `/pest-testing`)

Browse `.claude/skills/` to see what's available.

### Workflows

Workflows are **deterministic orchestrations** of multiple agents:

```javascript
// Example: Review changes across dimensions
export const meta = {
  name: 'review-changes',
  description: 'Review changed files for bugs, security, and performance',
  phases: [{ title: 'Review' }, { title: 'Verify' }]
}

// Agents run in parallel by default
const results = await pipeline(
  DIMENSIONS,
  d => agent(d.prompt, {label: `review:${d.key}`, schema: FINDINGS_SCHEMA}),
  // Verify each finding independently while others are still reviewing
  review => parallel(review.findings.map(f => () => verifyPrompt(f)))
)
```

---

## Best Practices Demonstrated

### 1. **Configuration Over Convention**

Every rule, skill, and workflow is explicit. No magic. Claude learns exactly how to work with your codebase.

### 2. **Multi-Agent Orchestration**

Complex tasks are broken into specialized agents:
- Finder agents discover issues
- Reviewer agents evaluate findings
- Verifier agents adversarially test conclusions
- Synthesizer agents produce final output

### 3. **Version-Specific Guidance**

Rules are tied to exact package versions. Claude knows Laravel 13 != Laravel 12, React 19 != 18, etc.

### 4. **Security & Compliance**

Skills like `security-review` embed security patterns directly into workflows. Tests are mandatory. Code must pass linting.

### 5. **Documentation-Driven Development**

Claude searches your exact package versions' documentation before making changes. No hallucinations.

---

## Extending This Project

### Add a New Skill

Create `.claude/skills/my-domain/` with:

```markdown
# My Domain Skill

**Activation:** When working with [thing], this skill activates.

## Patterns

- Pattern 1
- Pattern 2

## Examples

[Real examples of the pattern in action]
```

### Add a Custom Agent

Create `.claude/agents/my-agent.md` to define a specialized agent type that Claude Code can spawn.

### Add a Workflow

Create `.claude/workflows/my-workflow.js` with deterministic orchestration logic for multi-agent tasks.

### Add Rules

Create `.claude/rules/my-domain/` with layered rules. They'll automatically be discovered and applied.

---

## Running Tests

```bash
# All tests
php artisan test

# Specific test file
php artisan test --compact tests/Feature/ExampleTest.php

# By filter
php artisan test --compact --filter=testUserCanLogin

# Watch mode
php artisan test --watch
```

---

## Deployment

This project is designed for [Laravel Cloud](https://cloud.laravel.com/), the fastest way to deploy Laravel apps at scale.

For other platforms, follow standard Laravel deployment practices—the `.claude/` configuration will guide Claude through the process.

---

## Contributing

This is a reference implementation. Feel free to:

- Extend skills with new patterns
- Create specialized agents for your domain
- Add workflows for your team's processes
- Share improvements via GitHub issues/PRs

---

## Resources

- **Claude Code:** [claude.com/claude-code](https://claude.com/claude-code) or install the CLI
- **Laravel Docs:** [laravel.com/docs](https://laravel.com/docs)
- **Inertia.js:** [inertiajs.com](https://inertiajs.com)
- **Claude API:** [console.anthropic.com](https://console.anthropic.com)
- **Laravel Boost MCP:** [Laravel ecosystem integration](https://laravel.com/docs/boost)

---

## License

This project is open source and available under the [MIT license](LICENSE).

---

**Built with ❤️ to show how AI and Laravel work better together.**
