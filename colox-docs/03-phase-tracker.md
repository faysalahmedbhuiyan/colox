# Phase Tracker

## Phase 0 — Project Setup ✅ COMPLETE

- [x] `colox-backend`: Laravel skeleton, domain folder structure, health-check endpoint (`GET /api/health`), fixed api routing registration bug
- [x] `colox-app`: Flutter skeleton, clean architecture folders (core + features: auth/rider/driver/shared), role-based route guard skeleton, tested working on Chrome
- [x] `colox-admin`: React + Vite skeleton, TailwindCSS with brand colors, React Router skeleton, Axios API client base, tested working
- [x] `colox-docs`: this documentation set
- [x] All three code projects pushed to `github.com/faysalahmedbhuiyan/colox` (single monorepo)
- [ ] Android SDK / Android Studio install (in progress on owner's machine — not blocking, Flutter dev continuing via Chrome in the meantime)

## Phase 1 — Core MVP (NEXT UP)

Planned breakdown (will be delivered in small sub-steps):

- [x] Database schema: users (with account_status + complaints_against_count), user_roles, driver_profiles, rides, complaints, sos_incidents, account_holds — all migrations run successfully on PostgreSQL + PostGIS. PostGIS geometry columns, SOS field encryption, and sos_access_logs deferred to dedicated later steps.
- [x] Auth Step 2a: Sanctum installed, User/UserRole/DriverProfile models created, rider registration endpoint (`POST /api/auth/register/rider`) working — token issued on registration. Duplicate NID/phone check and login still pending.
- [x] Auth Step 2c: Role-based middleware (`role:rider` / `role:driver`) with account_status re-check on every request. Verified rider tokens are correctly blocked from driver-only routes (403) and vice versa. Driver registration (which will auto-grant rider role too, enabling dual-role toggle) deferred to verification flow step.
- [x] Auth Step 2d: Driver registration (fresh signup + rider-upgrade paths), one-NID-one-account enforced via unique validation, dual-role auto-grant (driver signup automatically gets rider role too). Verification is still `pending` by default — admin approval flow not yet built.
- [x] Step 3: Driver document upload (NID/license/profile photos via multipart form-data, stored on public disk). Admin verification endpoints (pending list, approve, reject) protected by temporary `is_admin` flag — full admin auth system deferred to dedicated Admin Panel phase.
- [x] Self-hosted OSRM routing server set up via Docker (Bangladesh OSM extract), verified working via test route query (Dhaka→Chittagong, ~253km, correct). Backend integration (RoutingService calling OSRM for fare calculation) and Flutter map screens are separate upcoming steps.
- [x] Fare calculation: `fare_settings` (base fare + per-km rate, per vehicle type) and `service_fee_slabs` tables, both DB-driven and seeded with initial defaults (car: ৳50 base + ৳30/km, motorcycle: ৳25 base + ৳15/km — adjustable later). RoutingService integrates with self-hosted OSRM. Fare estimate endpoint (`POST /api/rides/estimate-fare`) working.
- [ ] Pickup/dropoff selection + map integration (MapLibre/flutter_map + OSRM routing)
- [ ] Basic trip lifecycle (request → accept/decline → in-progress → complete)
- [ ] Service fee auto-calculation (slab table from business rules)
- [ ] Basic admin panel: driver verification actions, complaint resolution
- [ ] Complaint system: auto Complaint ID, last-10 retention with auto-delete cron
- [ ] SOS basic version: sos_incidents table, live location + party info capture, admin real-time alert
- [x] SOS Sub-step A: SOS trigger endpoint (active-ride only), encrypted rider/driver info snapshot (verified encrypted at rest), sos_access_logs for audit trail, admin view/acknowledge endpoints. Sub-step B (real-time WebSocket alert via Reverb, nearest police station info) pending.
- [x] SOS Sub-step B1: police_stations table seeded with DEMO/placeholder Noakhali data (clearly labeled "(DEMO)" — must be replaced with real station data via admin panel before launch), admin CRUD endpoints, nearest-station lookup (Haversine formula) now included in SOS trigger response. Real-time WebSocket alert (Reverb) still pending — Sub-step B2.
- [x] SOS Sub-step B2: Laravel Reverb installed and configured, `SosTriggered` broadcasting event (fires on SOS trigger, private `admin.sos-alerts` channel, authorized via is_admin, payload excludes sensitive snapshot data). SOS Basic Version now functionally complete on the backend — frontend (Flutter SOS button + Admin panel WebSocket listener) integration pending.
- [x] SOS basic version: sos_incidents table, live location + party info capture (encrypted), admin real-time alert (Reverb), nearest police station lookup (demo data, admin-editable)

## Phase 2 — Bidding + Wallet + Chat (NOT STARTED)

- [ ] Bidding system (InDrive-style)
- [ ] In-app wallet
- [ ] In-app chat (rider ↔ driver)
- [ ] Road safety reporting (basic)

## Phase 3 — Payments + Safety + Traffic (NOT STARTED)

- [ ] bKash/Nagad payment integration
- [ ] Fraud detection
- [ ] Crowd-sourced live traffic estimation
- [ ] 3D map
- [ ] Road safety heatmap
- [ ] SOS full version (police station data + directions, seeded for Noakhali district)

## Phase 4 — Growth Features (NOT STARTED)

- [ ] Scheduled rides
- [ ] Ride pooling
- [ ] Referral system
- [ ] Self-hosted AI features (support chatbot etc.) — LAST priority, self-hosted open-source model only

---

_Last updated: Phase 0 completion_

- [x] Frontend Step F2: Rider home screen with live map (flutter_map), current-location marker via geolocator, graceful permission-denied handling. Android location permissions added to manifest. NOTE: using public OSM tile server for now (dev-only, rate-limited) — must migrate to self-hosted OpenFreeMap (per tech stack) before production.
