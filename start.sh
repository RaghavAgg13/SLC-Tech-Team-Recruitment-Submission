#!/bin/bash
# SLC Recruitment - Full Stack Startup Script
# This script starts both Task A (GraphQL Backend) and Task B (WordPress Frontend)

echo "=== SLC Recruitment Full Stack Startup ==="
echo ""

# Step 1: Start Task A (GraphQL Backend)
echo "[1/4] Starting Task A (GraphQL Backend)..."
cd "$(dirname "$0")"
docker compose up -d

# Wait for services to be healthy
echo "[2/4] Waiting for services to start..."
sleep 10

# Step 2: Start Task B (WordPress)
echo "[3/4] Starting Task B (WordPress)..."
cd taskB
docker compose up -d
cd ..

echo "[4/4] Waiting for WordPress to initialize..."
sleep 5

echo ""
echo "=== Startup Complete ==="
echo ""
echo "Services available at:"
echo "  - Task A Frontend: http://localhost"
echo "  - Task A GraphQL:  http://localhost/graphql"
echo "  - WordPress:       http://localhost:8080"
echo ""
echo "To populate data, run:"
echo "  cd taskA && python populate_data.py"
echo ""
echo "WordPress Setup (first time only):"
echo "  1. Go to http://localhost:8080 and complete WordPress install"
echo "  2. Activate the 'ShoeStop' theme (Appearance > Themes)"
echo "  3. Activate the 'SLC Connector' plugin (Plugins)"
echo "  4. Create pages with shortcodes [slc_clubs] and [slc_events]"
