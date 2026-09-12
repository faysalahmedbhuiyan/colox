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
- [ ] Auth: registration/login with role rules (rider-only vs driver, dual-role toggle), one-NID-one-account enforcement
- [ ] Verification flow: NID + license upload → admin manual approval
- [ ] Pickup/dropoff selection + map integration (MapLibre/flutter_map + OSRM routing)
- [ ] Basic trip lifecycle (request → accept/decline → in-progress → complete)
- [ ] Service fee auto-calculation (slab table from business rules)
- [ ] Basic admin panel: driver verification actions, complaint resolution
- [ ] Complaint system: auto Complaint ID, last-10 retention with auto-delete cron
- [ ] SOS basic version: sos_incidents table, live location + party info capture, admin real-time alert

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
