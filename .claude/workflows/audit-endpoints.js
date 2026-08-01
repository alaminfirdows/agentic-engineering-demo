export const meta = {
  name: 'audit-endpoints',
  description: 'Audit all API endpoints for authorization and team isolation issues',
  phases: [
    { title: 'Scan', detail: 'find all route handlers' },
    { title: 'Audit', detail: 'check each for auth and team access' },
    { title: 'Verify', detail: 'cross-check critical findings' },
  ],
}

// Discover all route files
phase('Scan')
log('Scanning for API endpoints...')

const routes = await agent(
  'List every route file under app/Http/Controllers/. Return as array of file paths.',
  {
    schema: {
      type: 'object',
      required: ['files'],
      properties: {
        files: {
          type: 'array',
          items: { type: 'string' },
          description: 'PHP controller files',
        },
      },
    },
    label: 'find-routes',
    phase: 'Scan',
  }
)

// Audit each endpoint
phase('Audit')
log(`Auditing ${routes.files.length} endpoints...`)

const findings = await pipeline(
  routes.files,
  (file) =>
    agent(
      `Audit ${file} for these issues:
1. Missing $this->authorize() call
2. Direct model access without team filter (e.g., Invoice::find())
3. Missing team scope in queries (e.g., Invoice::forTeam())
4. FormRequest validation present and correct

Report: for each method, is it secure or not? Why?`,
      {
        label: file.split('/').pop(),
        phase: 'Audit',
      }
    )
)

// Verify critical findings
phase('Verify')
const criticalIssues = findings.filter(
  (f) => f && (f.includes('Missing authorize') || f.includes('Direct access'))
)

log(`Found ${criticalIssues.length} critical issues, verifying...`)

const verified = await parallel(
  criticalIssues.map((issue) => () =>
    agent(
      `Verify this is a real security issue (not false positive):
"${issue}"

Is this actually a vulnerability? Why or why not? Be strict.`,
      {
        label: 'verify',
        phase: 'Verify',
      }
    )
  )
)

return {
  total: routes.files.length,
  issues: findings.filter(Boolean),
  verified: verified.filter(Boolean),
}
