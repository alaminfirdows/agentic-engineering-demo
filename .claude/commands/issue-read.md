---
description: Read and display a GitHub issue with full context
argument-hint: <issue-number-or-link>
allowed-tools: Bash(gh *)
---

## Issue context
- Issue details: !`gh issue view $ARGUMENTS --comments`

## Your task
Display this GitHub issue with:
1. Title and status
2. Full description
3. All comments and their authors
4. Labels and assignees
5. Related PRs if any

Parse both issue numbers (#123) and full GitHub URLs.
