#!/bin/bash

# ContractLabour Application - Docker Logs Script
# View container logs in real-time

set -e

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$SCRIPT_DIR"

# Check arguments
if [ $# -eq 0 ]; then
    SERVICE="web"
else
    SERVICE="$1"
fi

# Validate service
if [ "$SERVICE" != "web" ] && [ "$SERVICE" != "mysql" ] && [ "$SERVICE" != "all" ]; then
    echo "Usage: $0 [web|mysql|all]"
    echo ""
    echo "Examples:"
    echo "  $0 web      - View web (Apache) logs"
    echo "  $0 mysql    - View MySQL logs"
    echo "  $0 all      - View all logs"
    exit 1
fi

echo "📋 Viewing $SERVICE logs (Ctrl+C to exit)..."
echo ""

if [ "$SERVICE" = "all" ]; then
    docker-compose logs -f
else
    docker-compose logs -f "$SERVICE"
fi
