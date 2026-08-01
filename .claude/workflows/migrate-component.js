export const meta = {
  name: 'migrate-component',
  description: 'Migrate React components to follow project patterns',
  phases: [
    { title: 'Discover', detail: 'find components needing migration' },
    { title: 'Transform', detail: 'rewrite each in isolated worktree' },
    { title: 'Verify', detail: 'test and validate changes' },
  ],
}

// Find target components
phase('Discover')
log('Finding components to migrate...')

const components = await agent(
  `List all .tsx files under resources/js/components/ that match this pattern:
${args || 'using class components or missing proper TypeScript'}

Return: { files: ["path/to/Component.tsx", ...] }`,
  {
    schema: {
      type: 'object',
      required: ['files'],
      properties: {
        files: {
          type: 'array',
          items: { type: 'string' },
        },
      },
    },
    label: 'discover-components',
    phase: 'Discover',
  }
)

log(`Found ${components.files.length} components to migrate`)

// Transform each in isolated worktree
phase('Transform')
const transforms = await pipeline(
  components.files,
  (file) =>
    agent(
      `Migrate ${file} to follow project patterns:
1. Use functional components with hooks
2. Add proper TypeScript interfaces for props
3. Extract custom hooks if reusable logic
4. Use Inertia hooks (useForm, useHttp, router)
5. Follow naming conventions (PascalCase components, camelCase hooks)

Make the changes and verify they compile.`,
      {
        label: file.split('/').pop(),
        phase: 'Transform',
        isolation: 'worktree',
      }
    )
)

// Verify changes don't break anything
phase('Verify')
log('Verifying migrations...')

const verified = await parallel(
  transforms.filter(Boolean).map((result) => () =>
    agent(
      `Verify this migration is correct:
- Component follows project patterns
- Props are properly typed
- No missing dependencies
- Hooks used correctly

Is this ready for production?`,
      {
        label: 'verify',
        phase: 'Verify',
      }
    )
  )
)

return {
  total: components.files.length,
  completed: transforms.filter(Boolean).length,
  verified: verified.filter((v) => v && v.includes('yes')).length,
}
