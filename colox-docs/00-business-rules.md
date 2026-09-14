# Business Rules — Hard Constraints

These rules must NEVER be violated in implementation. Any code touching these areas must be reviewed against this list.

## 1. Dual-Role Toggle

- A verified driver account MAY switch to "Rider Mode" to book rides occasionally.
- A rider-only account MUST NEVER access driver features/dashboard.
- This is one-directional. Must be enforced via role-based middleware/guard on BOTH backend (Laravel middleware) and frontend (route guard) — hiding UI is not sufficient.
- Current status: Flutter route guard skeleton exists (`colox-app/lib/core/router/app_router.dart`), enforces at navigation level. Backend middleware not yet built (Phase 1).

## 2. Account Uniqueness

- One NID/phone number = max 1 Rider account + 1 Driver account.
- Must be enforced at database level (unique constraint) AND at registration-time (duplicate check).
- Not yet implemented (Phase 1 — auth/registration).
- Implemented via Laravel unique validation on `nid_number` column (plus DB-level unique constraint). Fresh driver signup auto-grants rider role (dual-mode by default); rider-to-driver upgrade path also available via separate authenticated endpoint.

## 3. Admin Panel Isolation

- Admin panel is completely separate. No rider/driver credential can log into it.
- Separate auth guard, separate subdomain (recommended).
- Not yet implemented (Phase 1).

## 4. Service Fee Slabs (editable from admin)

| Ride Price    | Service Fee |
| ------------- | ----------: |
| ৳100 or below |         ৳10 |
| ৳101 – ৳250   |         ৳15 |
| ৳251 – ৳499   |         ৳20 |
| ৳500 or above |         ৳50 |

Must be stored in DB (not hardcoded) so admin can edit. Not yet implemented (Phase 1).

- Implemented as `service_fee_slabs` table (min_fare, max_fare, fee) — seeded with initial values matching the slab table above, editable via future admin panel UI (not hardcoded in code).

## 5. Complaint System

- Every complaint gets an automatic unique Complaint ID.
- Only the last 10 complaints per user/driver are retained; older ones auto-delete via scheduled job (cron).
- Not yet implemented (Phase 1).

## 6. SOS Feature (must-have, cannot be skipped)

- On SOS trigger: live location of triggerer + full info of both parties (name, phone, NID/license number, photo, vehicle number) + trip ID + timestamp saved to a separate secure `sos_incidents` table.
- This data must be usable in future police investigations: encrypted at rest, with access logs, so it can be handed to law enforcement if needed.
- Admin panel gets instant real-time alert (WebSocket + sound).
- The person who triggered SOS sees nearest police station name, number, and map directions instantly — station data seeded starting with Noakhali district.
- Not yet implemented (Phase 1 — basic version; full version with directions in Phase 3).
- Implemented: `sos_incidents.rider_snapshot` and `driver_snapshot` use Laravel's `encrypted:array` cast — encrypted at rest using `APP_KEY`, transparently decrypted when read via the model. Verified via raw DB query that the stored value is ciphertext, not plaintext.
- `sos_access_logs` table records every time an admin views or acknowledges an incident (who, when, IP address) — created automatically on `GET /admin/sos/{id}` and acknowledge action.
- **CRITICAL production requirement:** `APP_KEY` must be backed up securely and never regenerated after go-live — doing so would make all previously stored SOS data permanently undecryptable.
- **PRE-LAUNCH TODO:** Police station data currently seeded with placeholder/demo entries (named with "(DEMO)" suffix for visibility). Must be replaced with verified real station names/phones/coordinates via admin panel CRUD before production launch.

## Non-negotiable Reminders

- No shortcuts on authentication, role-permission, SOS data handling, or payment — these are explicitly called out as zero-tolerance-for-shortcuts areas.
