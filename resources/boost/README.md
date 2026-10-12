# Boost guidelines & skills

Laravel Boost discovers this folder from the installed package:

- `guidelines/` → project agent guidelines on `boost:install` / `boost:update`
- `skills/` → copied/symlinked into the project (`.ai/skills`, `.cursor/skills`, `.claude/skills`, …)

Agents do **not** load skills from `vendor/` at runtime. Boost copies/symlinks them into the project (`.cursor/skills`, `.claude/skills`, `.github/skills`, …) and composes `AGENTS.md` (guidelines). Those outputs should be **gitignored** in projects — keep `boost.json` + `config/boost.php` committed, and sync on install:

```bash
php artisan boost:update --no-interaction --no-discover
# or via Composer (see project `sync-boost-skills` script)
composer sync-boost-skills
```

## Contents

| Path | Role |
|------|------|
| `guidelines/justbetter-conventions.md` | Project conventions (Blade, tokens, WCAG, skill map) |
| `skills/design-to-frontend/` | Order of work: design → frontend + Statamic |
| `skills/statamic-pagebuilder-components/` | Pagebuilder Blade + Statamic config |
| `skills/statamic-fieldsets/` | Fieldset rendering |
| `skills/rapidez-blade-components/` | Temporary copy until published from `rapidez/blade-components` |

Orchestration from a design URL/prototype lives in the personal skill **`statamic-design-setup`** (`~/.cursor/skills` and `~/.claude/skills`), not in this package.
