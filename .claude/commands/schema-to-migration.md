---
description: Generate a migration from a schema diff
argument-hint: <table-name>
---
Look at the current schema for $ARGUMENTS in database/schema.ts.
Compare to the latest migration in database/migrations/.
Write the next migration file. Forward + rollback. Be conservative
with NOT NULL on existing columns.
