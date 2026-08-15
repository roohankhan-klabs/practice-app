# AGENTS.md

## Purpose

This repository is a Laravel 13 application with:

- Filament admin resources
- Inertia Laravel in the dependency set
- Sanctum/Fortify auth flows
- Pest for testing
- Pint and PHPStan/Larastan for code quality

Use this file as the first repo-specific guide for coding agents working here.

## Core Rules

- Do not add, update, or run tests unless the user explicitly asks for tests.
- Do not revert unrelated user changes in a dirty worktree.
- Prefer small, focused edits that match the existing structure instead of introducing new abstractions.
- Keep changes consistent with current code patterns, even when the codebase is imperfect.

## Project Layout

- `app/Filament/Resources` contains admin resources.
- `app/Filament/Resources/*/Schemas` contains Filament form and infolist schema classes.
- `app/Filament/Resources/*/Tables` contains Filament table definitions.
- `app/Filament/Resources/*/Pages` contains Filament page classes like create/edit/list/view.
- `app/Http/Controllers/Api` contains API controllers.
- `app/Models` contains Eloquent models.
- `database/migrations` contains schema definitions.
- `database/seeders` contains ordered seeders, ending with demo data seeding.
- `routes/api.php` contains versioned API routes under `api/v1`.
- `tests/Feature` uses Pest with `RefreshDatabase`, but do not touch tests unless explicitly asked.

## Laravel Conventions In This Repo

### Models

- Prefer attribute-based fillable declarations like `#[Fillable([...])]`.
- Prefer a `casts(): array` method when attribute casting is needed.
- Keep model relationships on the model; use the existing naming in that file rather than renaming methods opportunistically.
- If a stored field is JSON-backed, do not force a fake pivot relationship just to make it look relational. Match the real storage model.

### Controllers

- API controllers currently use inline `$request->validate(...)` rather than Form Requests in many places. Follow the local pattern unless the user asks for a larger refactor.
- API responses may go through a shared `formatResponse(...)` helper on the base API controller. Reuse it where the surrounding controller already does.
- Keep controller methods direct and readable; avoid adding service layers unless the codebase already uses one in that area.

### Routes

- Keep API routes in `routes/api.php` grouped under the existing `api/v1` prefix unless there is already a different pattern for the area you are editing.
- Follow the existing grouping style with nested `Route::prefix(...)` sections.

### Filament

- Preserve the current resource split:
  - resource class in `Resources/*Resource.php`
  - form/infolist schema classes in `Resources/*/Schemas`
  - table class in `Resources/*/Tables`
  - page classes in `Resources/*/Pages`
- Prefer building Filament forms with `Section`, `Select`, `TextInput`, `Textarea`, `Toggle`, and similar components already used in nearby resources.
- Put Filament-specific data transformation in the resource page lifecycle methods or resource helpers when that keeps the schema class simpler.
- When editing admin UX, prefer explicit, readable forms over overly dynamic abstractions.

### Database

- Match the current migration style used in this repo.
- Do not edit old migrations casually if the user is asking for a forward-looking schema change; add a new migration unless the user is clearly still in a local-only pre-release phase and the repo is already treating a migration as mutable.
- Seeders are ordered intentionally in `DatabaseSeeder.php`. Reference data should be seeded before dependent data.

## Known Repo-Specific Patterns

- Product and variant-related admin code is being built around Filament resources.
- Variant option selections are stored in `variants.variant_option_ids` as a JSON array of option IDs, not as a pivot table.
- API catalog endpoints currently eager load related data directly from controllers.
- The codebase uses Pest, but tests are opt-in by user instruction in this repository workflow.

## Commands

Use the narrowest useful command for the task.

- Syntax check a touched PHP file:
  - `php -l path\\to\\file.php`
- Format PHP:
  - `vendor\\bin\\pint`
  - or `composer lint`
- Static analysis:
  - `vendor\\bin\\phpstan analyse`
  - or `composer types:check`
- Full Laravel test pipeline:
  - `composer test`
  - Only run if the user explicitly asks for tests.

## Editing Guidance

- Prefer `rg` for searches.
- Prefer `apply_patch` for file edits.
- Preserve ASCII unless the file already needs Unicode.
- Add comments only when they explain non-obvious logic.
- Avoid renaming methods, files, or relationships unless required for correctness.
- Avoid introducing repositories, DTO layers, actions, or services into an area that does not already use them.

## Verification Guidance

- If the user did not ask for tests, prefer lightweight verification such as `php -l` on touched PHP files.
- If formatting or static analysis is necessary for confidence, mention what you ran.
- If you could not verify something, say so plainly.

## When Unsure

- Inspect sibling files first.
- Match the nearest existing pattern.
- Optimize for minimal surface area and predictable behavior.
