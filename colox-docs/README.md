# Colox — Project Documentation

Central documentation for the Colox ride-hailing platform (Noakhali, Bangladesh). This folder is the single source of truth for business rules, architecture decisions, and project progress — read this first in any new chat/session before touching code.

## Contents

- [`00-business-rules.md`](./00-business-rules.md) — Hard constraints that must never be broken
- [`01-tech-stack.md`](./01-tech-stack.md) — Final technology choices and budget policy
- [`02-architecture-decisions.md`](./02-architecture-decisions.md) — Why key structural decisions were made
- [`03-phase-tracker.md`](./03-phase-tracker.md) — What's done, what's next, per phase

## Repo Structure

Single monorepo at `github.com/faysalahmedbhuiyan/colox`:
colox/
colox-backend/ Laravel API (Rider + Driver + Admin all served from here)
colox-app/ Flutter (Rider + Driver, single codebase, role-based)
colox-admin/ React + Vite admin panel
colox-docs/ This folder

## For a New Chat Session

If starting fresh with an AI assistant on this project, share:

1. This README
2. `03-phase-tracker.md` (current status)
3. `00-business-rules.md` (non-negotiable rules)
