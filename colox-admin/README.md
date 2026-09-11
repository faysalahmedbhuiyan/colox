# Colox Admin Panel

Web-based admin panel for Colox operations team — manage drivers, passengers, trips, complaints, commission, and live tracking. Completely separate from Rider/Driver auth (business rule: no rider/driver credential can access this panel).

## Tech Stack

- React + Vite
- TailwindCSS (brand colors configured in `src/index.css`)
- React Router
- Axios (API client)

## Folder Convention

- `src/api` — API client + endpoint functions
- `src/features/{module}` — one folder per admin module (drivers, trips, complaints, etc.)
- `src/layouts` — shared page shells (sidebar + topbar)
- `src/routes` — route definitions + auth guards

## Local Setup

1. `npm install`
2. Copy `.env.example` to `.env`
3. `npm run dev`

## Status

Phase 0 — project skeleton, routing placeholder, Tailwind brand theme. No real auth or data yet.

See `colox-docs` for architecture decisions and full roadmap.
