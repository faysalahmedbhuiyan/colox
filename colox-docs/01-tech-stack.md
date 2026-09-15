# Tech Stack — Final (No Alternatives)

| Layer                       | Tool                                                                                            |
| --------------------------- | ----------------------------------------------------------------------------------------------- |
| Mobile (Rider + Driver app) | Flutter                                                                                         |
| Backend                     | Laravel                                                                                         |
| Database                    | PostgreSQL + PostGIS                                                                            |
| Cache/Queue                 | Redis                                                                                           |
| Admin Panel Frontend        | React.js + TailwindCSS (Vite)                                                                   |
| Map rendering (2D+3D)       | MapLibre GL JS + OpenFreeMap (mobile: flutter_map + latlong2 as MapLibre-compatible equivalent) |
| Routing/Directions          | OSRM (self-hosted)                                                                              |
| Geocoding/Autocomplete      | Nominatim + Photon (self-hosted)                                                                |
| Realtime/WebSocket          | Laravel Reverb                                                                                  |
| Push Notification           | Firebase Cloud Messaging                                                                        |
| File Storage                | Hosting server's own disk (self-hosted, encrypted folder)                                       |
| CI/CD                       | GitHub Actions                                                                                  |
| Error Monitoring            | Sentry                                                                                          |
| Uptime Monitoring           | UptimeRobot                                                                                     |
| DNS/CDN/SSL                 | Cloudflare                                                                                      |
| Registration OTP            | Email-based (free SMTP) initially; SMS gateway later once revenue starts                        |

## Budget Policy

Owner covers only: domain, hosting server, app store launch fees (Google Play $25 one-time, Apple $99/year if needed).

**Everything else must use free/open-source/self-hosted tools.** If any feature genuinely requires a paid API/service (e.g. future SMS OTP), this must be explicitly flagged in advance — never add a paid service unilaterally.

## Live Traffic Data

No free live traffic source exists initially. Plan: build crowd-sourced traffic estimation gradually from own drivers' real-time GPS speed data (the way Waze started). This is a Phase 3 feature.

## AI Features

Support chatbot or any AI feature comes last, in the final phase only, and must use a self-hosted open-source model (no paid API calls). Do not spend time on this early.

> **Security note (pre-production TODO):** Driver documents (NID/license photos) currently use Laravel's `public` disk for simplicity during development. Before production launch, must switch to a private disk with signed/expiring URLs so sensitive documents aren't publicly guessable via direct link.

> **OSRM setup:** Runs via Docker Compose in `colox-osrm/`. Map data (OSM extract + processed routing graph) is gitignored — regenerate locally per `colox-osrm/README.md`. Currently uses `car.lua` profile for both car and motorcycle rides. Verified working (tested Dhaka→Chittagong route, returned correct ~253km distance).
> **Reverb setup:** Self-hosted WebSocket server (`php artisan reverb:start`), runs as a separate process alongside `php artisan serve`. Used currently for SOS real-time admin alerts (private channel `admin.sos-alerts`, authorized via `is_admin` flag). Broadcast payload deliberately excludes sensitive rider/driver snapshot data — admin must call the authenticated `GET /admin/sos/{id}` endpoint (which creates an access log entry) to view full details.
>
> **Production deployment note:** Both `php artisan serve` (or proper web server) and `php artisan reverb:start` must run as persistent, auto-restarting processes (e.g. via Supervisor) — not manually in a terminal.
