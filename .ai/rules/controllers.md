---
paths:
  - 'app/Http/Controllers/**/ApplicationController.php'
---

# Controllers

## Keep applicant resumes private
Resume PDFs belong on the private local disk. Serve them only through authorized employer/admin download controllers; do not expose them through `public/storage` or direct URLs.
