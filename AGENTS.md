# AGENTS.md

## Purpose
- This file gives coding agents the minimum project-specific instructions needed to work safely and efficiently in this repository.
- Keep instructions concrete, executable, and specific to this Laravel API project.
- Prefer small diffs, narrow test scopes, and explicit verification over large speculative changes.

## Project Overview
- Repository: `pmapp-back-v2`
- Stack: Laravel 8, PHP 8.2, PostgreSQL 13, Redis 7, Docker Compose, PHPUnit, PHP_CodeSniffer, Larastan.
- Main responsibility: backend API endpoints under `routes/api/v2/*.php` with controllers in `app/Http/Controllers` and feature tests under `tests/Feature`.
- Default local workflow uses Docker. Prefer running PHP, Composer, Artisan, and PHPUnit commands through `docker compose exec app`.

## Working Agreement
- Start by reading the closest `AGENTS.md`, then inspect the touched code paths before proposing or making changes.
- When changing behavior, update or add tests first unless the task is strictly documentation or other non-functional work.
- Do not make unrelated cleanup changes in the same task.
- If instructions conflict, prioritize direct user instructions, then the nearest `AGENTS.md`, then other repository docs.

## Setup And Execution
- Start the environment: `docker compose up -d --build`
- Install dependencies in the app container: `docker compose exec app composer install`
- Generate the app key when needed: `docker compose exec app php artisan key:generate`
- Run migrations when schema changes are involved: `docker compose exec app php artisan migrate`
- Open a shell in the app container when repeated commands are needed: `docker compose exec app bash`

## TDD Workflow
1. Start from the outside with a failing test for the next smallest behavior.
2. Confirm the test fails for the intended reason before editing production code.
3. Implement only the minimum production change needed to make that single test pass.
4. Re-run the narrowest relevant test scope and confirm it passes.
5. Refactor only after the test is green.
6. Repeat until the feature or fix is complete.

## Test Design Rules
- Each test should express one behavior or one decision point.
- Prefer feature tests for endpoint behavior. Add unit-level tests only when they make intent clearer or reduce setup cost.
- Add explicit boundary tests for validation, authorization, nullability, empty states, and branching behavior.
- For bug fixes, write a regression test that fails against the current behavior before changing production code.
- Avoid changing multiple behaviors in one red-green cycle.

## Test Commands
- Run all tests: `docker compose exec app composer test`
- Run a single test file: `docker compose exec app vendor/bin/phpunit tests/Feature/app/Http/Controllers/Account/AccountIndexControllerTest.php`
- Filter by test name: `docker compose exec app vendor/bin/phpunit --filter '<test name>'`
- Run coverage when explicitly needed: `docker compose exec app composer test-coverage`
- Run code style checks: `docker compose exec app composer phpcs`
- Auto-fix style issues when appropriate: `docker compose exec app composer phpcs-fix`
- Run static analysis: `docker compose exec app composer larastan`

## Change Guidance For This Codebase
- API route changes usually require matching updates across `routes/api/v2/*.php`, the relevant controller, request validation class, and feature tests.
- Database changes require a migration and tests that cover both the default path and the changed branch or constraint.
- When editing request validation, add tests for both accepted and rejected inputs.
- When editing authorization or authentication behavior, add tests for allowed and denied cases.
- Reuse existing response formatting patterns instead of introducing endpoint-specific response shapes without a clear reason.

## Verification Rules
- After each green step, run the narrowest relevant test scope first.
- Before finishing, run the broader related scope for the touched area.
- For PHP changes, also run relevant quality checks when they are affected:
  - `composer phpcs` for formatting or touched PHP files.
  - `composer larastan` for type or structural changes.
- If you cannot run the necessary checks, say exactly what was not run and why.

## Safety Rules
- Do not edit `.env` or production-like secrets unless the user explicitly asks.
- Do not add new Composer dependencies without a clear need.
- Do not rewrite large existing areas when a small targeted change will do.
- Do not run destructive commands such as data resets, volume deletion, or forceful git operations unless explicitly requested.
- Treat migrations, seeders, and authentication flows as high-risk areas and verify them more carefully than ordinary refactors.

## Communication Rules
- In progress updates, state whether the current step is `red`, `green`, or `refactor`.
- In final summaries, mention:
  - the behavior changed,
  - the test scopes executed,
  - any checks not run,
  - any remaining risks or assumptions.

## Maintaining This File
- Keep this file short enough that an agent can act on it quickly.
- Prefer command examples and repository facts over generic advice.
- When a subdirectory needs different rules, add a closer `AGENTS.md` there instead of overloading this root file.
