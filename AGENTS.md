# Codex Rules

## Purpose
- This repository uses t-wada style TDD as the default development workflow.
- Codex must prefer small, verifiable steps over large speculative implementations.

## TDD Workflow
1. Start from the outside with a failing test that represents the next smallest behavior.
2. Confirm the test fails for the intended reason before changing production code.
3. Implement only the minimum production code required to make that single test pass.
4. Run the smallest relevant test scope and confirm it passes.
5. Refactor only after the test is green.
6. Repeat in small cycles until the feature is complete.

## Test Design Rules
- Add or update tests before editing production code, unless the task is strictly non-functional and cannot be covered by tests.
- Each test should express one behavior or decision point.
- When boundary values exist, add explicit boundary tests.
- Prefer feature tests for endpoint behavior and add lower-level tests only when they reduce ambiguity or cost.
- When fixing a bug, first add a regression test that fails against the current behavior.

## Implementation Rules
- Do not implement multiple behaviors in one step without a corresponding failing test.
- Do not refactor while tests are red.
- Keep each production change as small as possible so the cause of a red or green result is obvious.
- When introducing configuration or branching behavior, add tests for the default path and critical branches.

## Verification Rules
- After each green step, run the narrowest relevant test scope first.
- Before finishing, run the broader related test scope for the touched area.
- If tests cannot be run, state the reason explicitly and stop short of claiming completion.

## Communication Rules
- In progress updates, state whether the current step is red, green, or refactor.
- In final summaries, mention the test scopes executed and any remaining risks or untested assumptions.
