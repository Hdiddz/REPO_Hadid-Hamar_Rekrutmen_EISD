---
paths:
  - 'database/migrations/**'
---

# Migrations

## Reserve jobs table for recruitment domain
The recruitment vacancy table is `jobs`. Laravel's database queue uses `queue_jobs`; keep `config/queue.php` and the framework queue migration aligned so the two concepts never collide.
