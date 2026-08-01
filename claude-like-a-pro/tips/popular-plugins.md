Each plugin is a folder with its own config. You can browse what's available and install from there.

The full list, categorized

I split these into two buckets: technical (for developers) and non-technical (for workflow/style/project management).

Technical plugins:

typescript-lsp -- Adds TypeScript language server integration. Claude gets real type checking, go-to-definition, and error diagnostics instead of guessing. If you write TypeScript this is probably the single most impactful plugin.

playwright -- Browser automation and testing. Claude can launch a browser, navigate pages, take screenshots, fill forms, run end-to-end tests. Useful if you're building anything with a frontend.

security-guidance -- Scans for common vulnerabilities. Catches things like hardcoded secrets, auth bypass patterns, and injection risks. Runs passively as Claude writes code.

code-review -- Structured code review with quality scoring. Gives Claude a framework for reviewing PRs rather than just saying "looks good."

pr-review-toolkit -- Similar to code-review but focused on the PR workflow specifically. Generates review comments, suggests changes, checks for common PR issues.

commit-commands -- Standardizes commit messages. If you care about conventional commits or consistent git history, this helps.

code-simplifier -- Identifies overly complex code and suggests simplifications. Measures cyclomatic complexity and flags functions that are doing too much.

context7 -- Documentation lookup. Claude can fetch up-to-date docs for libraries instead of relying on training data. Useful when you're working with fast-moving frameworks.

Non-technical plugins:

claude-md-management -- Auto-maintains your CLAUDE.md project file. Keeps it structured, updates sections, prevents it from becoming a mess over time.

explanatory-output-style -- Changes Claude's output style to be more educational. It explains the "why" behind decisions, not just the "what." Useful if you're learning or want better documentation in conversations.

learning-output-style -- Similar to explanatory but specifically geared toward teaching. Claude breaks things down more gradually and checks understanding.

frontend-design -- UI/UX design patterns and guidance. Claude references established design systems and accessibility standards when building frontend components.

claude-code-setup -- Project scaffolding. Helps set up new projects with proper structure, configs, and boilerplate.

feature-dev -- Feature development workflow. Structures how Claude approaches building a new feature: requirements, design, implementation, testing.

There are about 13+ more that I haven't listed because they're either very niche or I haven't tested them enough to have an opinion. You can browse the full directory yourself.

Which ones I actually recommend (high impact)

After installing and testing several of these, here's my tier list:

typescript-lsp -- The difference in code quality is noticeable. Claude stops guessing at types and actually checks them.

security-guidance -- Caught a real auth bypass in my codebase that Claude had originally written and never flagged. Worth it for that alone.

context7 -- No more outdated API suggestions. It actually looks up current docs.

playwright -- If you have any frontend, being able to run real browser tests through Claude is a significant upgrade.

Worth trying (depends on your workflow):

5. code-review -- Good if you're a solo dev and want a second pair of eyes.
6. claude-md-management -- Good if your CLAUDE.md keeps getting messy.
7. explanatory-output-style -- Good if you want to understand the code Claude writes, not just use it.
8. frontend-design -- Good if you're building UI and want better defaults.

The bigger picture

My rough estimate is that Claude Code at default settings is running at maybe 60% of what it can actually do. These plugins aren't just cosmetic -- typescript-lsp gives it real type awareness, security-guidance catches vulnerabilities passively, and context7 means it's working with current documentation instead of whatever was in its training data.

The surprising thing to me was how many of these exist and how little they're discussed. I've been using Claude Code daily for months and only found these by accident.

Has anyone else been using these plugins? Curious which ones other people have found useful?

EDIT: Thanks for the feedback (and the roasting). Some corrections based on the comments:

The actual plugin count is 53, not 27. i only listed the ones i had tested or had opinions on, but should have stated the full count upfront. my bad.

Several of these ARE in the docs if you know where to look. "hadn't seen anyone talk about" was my experience, not a statement about discoverability for everyone. Poor framing on my part.

Fixed the hookify description in an earlier edit (it's for Claude Code hooks, not React hooks). Thanks u/KnifeFed for catching that.

For anyone wanting the complete list, just run ls ~/.claude/plugins/marketplaces/claude-plugins-official/plugins/ and browse yourself. Better than taking my word for it.

