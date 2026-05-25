#!/bin/bash
set -e

echo "=== Starting ContractLabour / CLMS v2 Application ==="

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

# ----------------------------------------------------------------
# CLMS v2 — set admin password hash (PHP bcrypt, cost 12)
# The SQL init file inserts a 'PLACEHOLDER' hash; we fix it here.
# ----------------------------------------------------------------
echo "Setting CLMS v2 admin password..."
ADMIN_HASH=$(php -r "echo password_hash('Admin@1234', PASSWORD_BCRYPT, ['cost' => 12]);")
mysql -h mysql -u anahaw -panahaw clms_v2 \
  -e "UPDATE users SET password_hash='$ADMIN_HASH' WHERE username='clmsadmin' AND password_hash='PLACEHOLDER';" \
  2>/dev/null && echo "✓ CLMS v2 admin password set (username: clmsadmin / password: Admin@1234)" \
              || echo "  (Admin password already set — skipping)"

# ----------------------------------------------------------------
# Ensure clms-v2 uploads directory exists with correct permissions
# ----------------------------------------------------------------
mkdir -p /var/www/html/clms-v2/uploads
chown -R www-data:www-data /var/www/html/clms-v2/uploads
chmod 750 /var/www/html/clms-v2/uploads

# ----------------------------------------------------------------
# SSL certificates (WARNING only — not fatal for dev)
# ----------------------------------------------------------------
if [ ! -f /etc/apache2/certs/server.crt ] || [ ! -f /etc/apache2/certs/server.key ]; then
    echo "WARNING: SSL certificates not found at /etc/apache2/certs/"
    echo "  HTTPS will not work. HTTP on ports 80 / 8082 / 8083 will still function."
    # Disable SSL site to prevent Apache from failing to start
    a2dissite default-ssl.conf 2>/dev/null || true
else
    echo "✓ SSL certificates verified"
fi

echo ""
echo "============================================================"
echo "  CLMS v2   → http://localhost:8083"
echo "  Legacy app → http://localhost:8082"
echo "  phpMyAdmin → http://localhost:8090"
echo "  MySQL      → localhost:3307 (user: anahaw / pass: anahaw)"
echo ""
echo "  CLMS v2 login: clmsadmin / Admin@1234"
echo "  (You will be prompted to change password on first login)"
echo "============================================================"
echo ""

# Execute the main command
exec "$@"
