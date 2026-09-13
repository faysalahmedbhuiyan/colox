# Colox OSRM Routing Server

Self-hosted routing engine using OSRM (Open Source Routing Machine) — provides turn-by-turn routing, distance, and duration calculations for the Colox backend. Chosen because paid routing APIs (Google Directions, Mapbox) are outside the free/self-hosted budget policy.

## Data

Uses OpenStreetMap extract for Bangladesh (Geofabrik). The `.osm.pbf` and processed `.osrm*` files are gitignored — they're large binary data, not code, and must be regenerated locally or on the server (see setup below).

## Setup (fresh machine/server)

1. Download Bangladesh extract: https://download.geofabrik.de/asia/bangladesh-latest.osm.pbf → place in `data/`
2. Process it (one-time, or whenever the map data needs updating):

```bash
   docker run -t -v "$(pwd)/data:/data" osrm/osrm-backend osrm-extract -p /opt/car.lua /data/bangladesh-latest.osm.pbf
   docker run -t -v "$(pwd)/data:/data" osrm/osrm-backend osrm-partition /data/bangladesh-latest.osrm
   docker run -t -v "$(pwd)/data:/data" osrm/osrm-backend osrm-customize /data/bangladesh-latest.osrm
```

3. Start the routing server: `docker-compose up -d`
4. Verify: `GET http://localhost:5000/route/v1/driving/{lon1},{lat1};{lon2},{lat2}?overview=false`

## Notes

- Uses OSRM's `car.lua` profile for both car and motorcycle rides (no dedicated motorcycle profile exists in OSRM by default — revisit if routing accuracy for motorcycles becomes an issue).
- Backend (`colox-backend`) calls this server internally at `http://localhost:5000` (or the server's internal address in production) — never exposed directly to the internet in production; only the Laravel backend should talk to it.

## Status

Phase 1 — routing server running locally via Docker. Backend integration (fare/distance calculation service) is a separate step.
