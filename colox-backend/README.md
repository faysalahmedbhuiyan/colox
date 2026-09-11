# Colox Backend

Ride-hailing platform backend (Noakhali, Bangladesh) — Laravel API serving Rider app, Driver app, and Admin panel.

## Tech Stack

-   Laravel (API only, no Blade views except health-check)
-   PostgreSQL + PostGIS
-   Redis (cache, queue, session)
-   Laravel Reverb (WebSocket/realtime)

## Local Setup

1. `composer install`
2. Copy `.env.example` to `.env`, fill in DB/Redis credentials
3. `php artisan key:generate`
4. `php artisan migrate`
5. `php artisan serve`
6. Verify: `GET /api/health` should return `{"status":"ok"}`

## Folder Convention

-   `app/Domain/{Ride,User,Driver,Complaint,Sos}` — business logic grouped by domain, not by generic MVC folders
-   `app/Http/Controllers/Api` — all API controllers here
-   `app/Services` — cross-domain services (e.g. fee calculation, SOS dispatch)

## Status

Phase 0 — project skeleton. No business logic yet.

See `colox-docs` repo for architecture decisions and full roadmap.
