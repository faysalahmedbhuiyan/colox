# Architecture Decisions

Format: Decision — Reasoning. Add new entries at the bottom with date.

---

### ADR-001: Single Monorepo (not 3 separate repos)

**Decision:** One GitHub repo (`colox`) containing `colox-backend/`, `colox-app/`, `colox-admin/`, `colox-docs/` as subfolders — not separate repos with separate `.git`.

**Reasoning:** Owner manages solo; a single repo is simpler to track, commit, and push across all three projects without juggling multiple remotes. Each subfolder still has its own README/CHANGELOG for independent context.

**Date:** Phase 0

---

### ADR-002: Domain-based folder structure in Laravel backend

**Decision:** `app/Domain/{Ride,User,Driver,Complaint,Sos}` instead of generic Laravel MVC-only folders.

**Reasoning:** Business logic grouped by domain makes it easier to reason about SOS/complaint/ride logic in isolation — especially important given the strict security requirements around SOS data handling.

**Date:** Phase 0

---

### ADR-003: Single Flutter codebase for Rider + Driver (not two separate apps)

**Decision:** One Flutter app with `features/rider` and `features/driver` folders, gated by a role-based router guard (`core/router/app_router.dart`).

**Reasoning:** Business rule requires dual-role toggle (driver can become rider temporarily) — sharing one codebase makes this toggle natural. Strict one-directional access (rider can never reach driver features) is enforced at the router level now, and will be reinforced by backend middleware in Phase 1 — UI hiding alone is explicitly insufficient per business rules.

**Date:** Phase 0

---

### ADR-004: Laravel routing fix — explicit `api:` registration required

**Decision:** In `bootstrap/app.php`, `withRouting()` must explicitly include `api: __DIR__.'/../routes/api.php'`.

**Reasoning:** Newer Laravel skeleton (bootstrap/app.php-based, no `Kernel.php`) does not auto-register `routes/api.php` like older Laravel versions did. Without this line, all `/api/*` routes 404 even if the route file itself is correct. This caused a debugging session in Phase 0 — documenting here so it's not re-discovered from scratch.

**Date:** Phase 0

---

### ADR-005: Vite over Create React App for admin panel

**Decision:** `colox-admin` uses React + Vite, not Create React App.

**Reasoning:** CRA is deprecated/unmaintained; Vite is faster and the current community standard.

**Date:** Phase 0

### ADR-006: Role middleware doubles as account_status gatekeeper

**Decision:** `EnsureHasRole` middleware checks both role membership AND `account_status === 'active'` on every protected request, not just at login time.

**Reasoning:** A user's account can transition to `held`/`banned` at any time (e.g. after the 10th complaint), but their existing Sanctum token remains technically valid until revoked. Checking status only at login leaves a window where a held/banned user could keep using an already-issued token. Centralizing this check in the same middleware that already gates every protected route closes that gap without extra queries per route.

**Date:** Phase 1

### ADR-007: Temporary is_admin flag instead of full admin auth

**Decision:** Admin-only routes are protected by a simple `is_admin` boolean on the `users` table + `EnsureIsAdmin` middleware, rather than a complete separate admin authentication system.

**Reasoning:** Building full admin auth (separate login flow, permission granularity, admin-specific session handling per business rule #3) is a substantial piece of work deserving its own focused phase. Gating it behind a simple flag now lets driver verification logic be built and tested end-to-end without blocking on that larger piece. Must be replaced before production launch — tracked as a required follow-up, not a permanent shortcut.

**Date:** Phase 1
