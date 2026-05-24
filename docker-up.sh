#!/bin/bash

# ContractLabour Application - Docker Startup Script
# Brings up PHP 8.2.31 + Apache + MySQL 5.7.44-48 (Percona) containers

set -e

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$SCRIPT_DIR"

echo "🚀 Starting ContractLabour application Docker containers..."
echo ""

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Error: Docker is not running. Please start Docker Desktop."
    exit 1
fi

# Check if docker-compose.yml exists
if [ ! -f "docker-compose.yml" ]; then
    echo "❌ Error: docker-compose.yml not found in $SCRIPT_DIR"
    exit 1
fi

# Generate SSL certificates if they don't exist
mkdir -p certs
if [ ! -f "certs/server.crt" ] || [ ! -f "certs/server.key" ]; then
    echo "🔑 Generating self-signed SSL certificates..."
    openssl req -x509 -newkey rsa:2048 -keyout certs/server.key -out certs/server.crt -days 365 -nodes -subj "/C=US/ST=State/L=City/O=Organization/CN=localhost" > /dev/null 2>&1
    echo "✅ SSL certificates generated successfully!"
fi

# Check if catlmain.sql.gz exists to prevent Docker Compose mount failures
if [ ! -f "catlmain.sql.gz" ]; then
    if [ -f "/Users/sivaprasad/speed/catlmain.sql.gz" ]; then
        echo "📦 Found catlmain.sql.gz in speed workspace, copying for database auto-import..."
        cp "/Users/sivaprasad/speed/catlmain.sql.gz" ./catlmain.sql.gz
    else
        echo "📝 Creating a database placeholder archive to prevent Docker Compose mount errors..."
        echo "-- Placeholder empty database" | gzip > catlmain.sql.gz
    fi
fi

# Start containers in background and build if needed
docker-compose up -d --build

echo ""
echo "✅ Containers starting..."
echo "⏳ Waiting for MySQL to initialize (can take 5-10s for the first run)..."
sleep 5

# Check if containers are running
if docker-compose ps | grep -q "Up"; then
    echo ""
    echo "🎉 Containers are running successfully!"
    echo ""
    echo "📍 Application URLs:"
    echo "   HTTPS: https://localhost:5002"
    echo "   HTTP:  http://localhost:8082"
    echo ""
    echo "📊 Database:"
    echo "   Host:     mysql (from containers) or localhost (from host)"
    echo "   Port:     3307"
    echo "   User:     anahaw"
    echo "   Password: anahaw"
    echo ""
    echo "📝 Useful scripts:"
    echo "   View logs:     ./docker-logs.sh web"
    echo "   DB logs:       ./docker-logs.sh mysql"
    echo "   Stop:          ./docker-down.sh"
    echo "   Web Shell:     ./docker-shell.sh"
    echo "   MySQL Shell:   ./docker-mysql.sh"
    echo ""
else
    echo "❌ Error: Containers failed to start"
    docker-compose logs
    exit 1
fi
