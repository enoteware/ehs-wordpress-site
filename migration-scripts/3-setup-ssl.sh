#!/bin/bash
#
# Setup SSL/HTTPS for migrated site
# Usage: ./3-setup-ssl.sh DOMAIN EMAIL
#
# NOTE: DNS must be pointing to the DigitalOcean server before running this!
#

set -e

if [ -z "$1" ] || [ -z "$2" ]; then
    echo "Usage: $0 DOMAIN EMAIL"
    echo "Example: $0 ehsanalytical.com enoteware@gmail.com"
    echo ""
    echo "IMPORTANT: DNS must be pointing to the server before running this!"
    exit 1
fi

DOMAIN=$1
EMAIL=$2

# Load server credentials
if [ -f ../.env.migration-server ]; then
    source ../.env.migration-server
else
    echo "Error: .env.migration-server file not found"
    exit 1
fi

echo "=== Setting up SSL for ${DOMAIN} ==="
echo "Email: ${EMAIL}"
echo "Server: ${MIGRATION_SERVER_IP}"

# Check DNS first
echo ""
echo "Checking DNS..."
CURRENT_IP=$(dig +short ${DOMAIN} @8.8.8.8 | tail -1)
echo "  ${DOMAIN} resolves to: ${CURRENT_IP}"
echo "  Server IP: ${MIGRATION_SERVER_IP}"

if [ "$CURRENT_IP" != "$MIGRATION_SERVER_IP" ]; then
    echo ""
    echo "WARNING: DNS is not pointing to this server yet!"
    echo "Current: ${CURRENT_IP}"
    echo "Expected: ${MIGRATION_SERVER_IP}"
    echo ""
    read -p "Continue anyway? (y/N) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        exit 1
    fi
fi

# Setup SSL on server
ssh root@${MIGRATION_SERVER_IP} << EOF
set -e

echo "=== Installing SSL certificate for ${DOMAIN} ==="

# Install certbot if not already installed
which certbot || apt-get install -y certbot python3-certbot-nginx

# Get SSL certificate
certbot --nginx \
    -d ${DOMAIN} \
    -d www.${DOMAIN} \
    --non-interactive \
    --agree-tos \
    --email ${EMAIL} \
    --redirect

# Update WordPress URLs to HTTPS
cd /var/www/${DOMAIN}/html
wp search-replace 'http://${DOMAIN}' 'https://${DOMAIN}' --skip-columns=guid --allow-root
wp search-replace 'http://${MIGRATION_SERVER_IP}' 'https://${DOMAIN}' --skip-columns=guid --allow-root

# Force HTTPS in wp-config.php
if ! grep -q "FORCE_SSL_ADMIN" wp-config.php; then
    sed -i "/DB_COLLATE/a define('FORCE_SSL_ADMIN', true);" wp-config.php
fi

# Test site
echo ""
echo "Testing HTTPS..."
curl -I https://${DOMAIN} 2>&1 | head -5

echo ""
echo "✓ SSL configured successfully!"
echo "  Site: https://${DOMAIN}"
echo "  Admin: https://${DOMAIN}/wp-admin"

EOF

echo ""
echo "=== SSL Setup Complete ==="
echo "✓ SSL certificate installed"
echo "✓ WordPress URLs updated to HTTPS"
echo "✓ Site: https://${DOMAIN}"
echo ""
echo "Migration complete! Test thoroughly before updating DNS permanently."
