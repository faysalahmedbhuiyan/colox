# Colox App (Rider + Driver)

Single Flutter codebase serving both Rider and Driver roles. Dual-role toggle allowed (verified driver can switch to rider mode), but rider-only accounts can NEVER access driver features — enforced via `roleGuard` in `core/router`.

## Tech Stack

- Flutter
- Riverpod (state management)
- go_router (navigation + role guard)
- dio (API client)
- flutter_map + latlong2 (MapLibre-compatible mapping)
- geolocator (GPS)

## Folder Convention

- `lib/core` — shared infra: network, theme, router, utils
- `lib/features/auth` — login/register/verification
- `lib/features/rider` — rider-only screens/logic
- `lib/features/driver` — driver-only screens/logic
- `lib/features/shared` — used by both (e.g. chat, trip details)

## Local Setup

1. `flutter pub get`
2. Run on Chrome (dev, no Android SDK needed): `flutter run -d chrome --dart-define=API_BASE_URL=http://127.0.0.1:8000/api`
3. Run on Android (once SDK installed): `flutter run --dart-define=API_BASE_URL=http://10.0.2.2:8000/api`

## Status

Phase 0 — project skeleton + role-based routing guard structure. No real auth or API calls yet.

See `colox-docs` for architecture decisions and full roadmap.
