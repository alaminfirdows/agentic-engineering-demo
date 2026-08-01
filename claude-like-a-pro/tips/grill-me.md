**Make Claude Code grill you before building**

Matt Pocock's `/grill-me` (`gril-with-docs`) skill walks down the design tree question by question, surfacing every hidden assumption before a single line is written.

```jsx
# Install
npx skills@latest add mattpocock/skills/grill-me

# Or hand-roll: .claude/skills/grill-me/SKILL.md
"""
Interview me relentlessly about every aspect of this plan
until we reach a shared understanding. Walk down each branch
of the design tree resolving dependencies between decisions
one by one.

If a question can be answered by exploring the codebase,
explore the codebase instead.

For each question, provide your recommended answer.
"""
```
