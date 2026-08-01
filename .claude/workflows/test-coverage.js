export const meta = {
  name: 'test-coverage',
  description: 'Analyze and improve test coverage for Laravel features',
  phases: [
    { title: 'Analyze', detail: 'find untested code paths' },
    { title: 'Write', detail: 'create tests for gaps' },
    { title: 'Verify', detail: 'ensure tests pass' },
  ],
}

// Analyze current coverage
phase('Analyze')
log('Analyzing test coverage...')

const coverage = await agent(
  `Run: php artisan test --coverage-html=coverage

Then analyze coverage report:
1. Which actions have <80% coverage?
2. Which policies are untested?
3. Which API endpoints lack tests?

Return: { gaps: ["action/path", "policy/path", ...], coverage: X% }`,
  {
    schema: {
      type: 'object',
      required: ['gaps'],
      properties: {
        gaps: {
          type: 'array',
          items: { type: 'string' },
          description: 'Files with low coverage',
        },
        coverage: { type: 'number', description: 'Overall coverage %' },
      },
    },
    label: 'analyze-coverage',
    phase: 'Analyze',
  }
)

log(`Found ${coverage.gaps.length} coverage gaps (${coverage.coverage}% overall)`)

// Write tests for gaps
phase('Write')
const tests = await pipeline(
  coverage.gaps.slice(0, 10),  // Limit to 10 to avoid explosion
  (gap) =>
    agent(
      `Write comprehensive Pest tests for: ${gap}

Include:
1. Happy path test
2. Validation error test
3. Authorization test (if applicable)
4. Edge cases relevant to this code

Use factories for test data. Mock external services.`,
      {
        label: gap.split('/').pop(),
        phase: 'Write',
      }
    )
)

// Verify tests pass
phase('Verify')
log('Running test suite...')

const verified = await agent(
  `Run: php artisan test --compact --filter="test pattern matching new tests"

Do all tests pass? Report any failures.`,
  {
    label: 'run-tests',
    phase: 'Verify',
  }
)

return {
  gaps: coverage.gaps.length,
  tests_written: tests.filter(Boolean).length,
  tests_passing: verified.includes('passed'),
  new_coverage: coverage.coverage,
}
