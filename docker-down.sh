#!/bin/bash

# ContractLabour Application - Docker Shutdown Script
# Brings down PHP 8.2.31 + Apache + MySQL 5.7.44-48 containers

set -e

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$SCRIPT_DIR"

echo "🛑 Stopping ContractLabour application Docker containers..."
echo ""

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Error: Docker is not running."
    exit 1
fi

# Check if docker-compose.yml exists
if [ ! -f "docker-compose.yml" ]; then
    echo "❌ Error: docker-compose.yml not found in $SCRIPT_DIR"
    exit 1
fi

# Stop containers
docker-compose down

echo ""
echo "✅ Containers stopped!"
echo ""
echo "💾 Database data is preserved (stored in Docker volume)"
echo "   To delete all database data and start completely fresh, run:"
echo "   docker-compose down -v"
echo ""
