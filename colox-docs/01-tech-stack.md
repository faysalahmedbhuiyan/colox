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
