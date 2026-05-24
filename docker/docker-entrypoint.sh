#!/bin/bash
set -e

echo "=== Starting ContractLabour Application ==="

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
max_attempts=60
attempt=0
while ! nc -z mysql 3306; do
  attempt=$((attempt + 1))
  if [ $attempt -ge $max_attempts ]; then
    echo "ERROR: MySQL failed to start after $max_attempts attempts"
    exit 1
  fi
  echo "  Attempt $attempt/$max_attempts..."
  sleep 1
done
echo "✓ MySQL is ready!"

# Verify catlmain database content
echo "Verifying catlmain database..."
TABLES=$(mysql -h mysql -u anahaw -panahaw catlmain -e "SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA='catlmain';" 2>/dev/null | tail -1)
echo "✓ catlmain database has $TABLES table(s)"

# Update Apache listen ports if not already configured
if ! grep -q "Listen 5000" /etc/apache2/ports.conf; then
  echo "Configuring Apache to listen on port 5000..."
  sed -i 's/Listen 80/Listen 80\nListen 5000/' /etc/apache2/ports.conf
fi

# Verify SSL certificates exist
if [ ! -f /etc/apache2/certs/server.crt ] || [ ! -f /etc/apache2/certs/server.key ]; then
    echo "WARNING: SSL certificates not found!"
    echo "Expected: /etc/apache2/certs/server.crt and /etc/apache2/certs/server.key"
    exit 1
fi

echo "✓ SSL certificates verified"
echo "=== Starting Apache on ports 80 and 5000 ==="

# Execute the main command
exec "$@"
