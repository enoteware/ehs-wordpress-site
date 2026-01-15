#!/bin/bash
#
# Quick migration script - runs all steps
# Usage: ./quick-migrate.sh DOMAIN [EMAIL]
#

set -e

if [ -z "$1" ]; then
    echo "Usage: $0 DOMAIN [EMAIL]"
    echo "Example: $0 ehsanalytical.com enoteware@gmail.com"
    exit 1
fi

DOMAIN=$1
EMAIL=${2:-enoteware@gmail.com}

echo "╔═══════════════════════════════════════════════════╗"
echo "║  WordPress Migration: ${DOMAIN}"
echo "║  From: Nexcess → DigitalOcean"
echo "╚═══════════════════════════════════════════════════╝"
echo ""

# Step 1: Export from Nexcess
echo "=== Step 1/3: Exporting from Nexcess ==="
./1-export-from-nexcess.sh ${DOMAIN}

echo ""
read -p "Export complete. Continue with import? (Y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Nn]$ ]]; then
    exit 0
fi

# Step 2: Import to DigitalOcean
echo ""
echo "=== Step 2/3: Importing to DigitalOcean ==="
./2-import-to-digitalocean.sh ${DOMAIN}

echo ""
echo "=== Testing imported site ==="
if [ -f ../.env.migration-server ]; then
    source ../.env.migration-server
    echo "Preview site at: http://${MIGRATION_SERVER_IP}"
fi

echo ""
read -p "Site imported. Set up SSL now? (y/N) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo ""
    echo "Migration paused. To continue:"
    echo "  1. Update DNS: ${DOMAIN} → A record → ${MIGRATION_SERVER_IP}"
    echo "  2. Wait for DNS propagation (use: dig ${DOMAIN})"
    echo "  3. Run: ./3-setup-ssl.sh ${DOMAIN} ${EMAIL}"
    exit 0
fi

# Step 3: Setup SSL
echo ""
echo "=== Step 3/3: Setting up SSL ==="
./3-setup-ssl.sh ${DOMAIN} ${EMAIL}

echo ""
echo "╔═══════════════════════════════════════════════════╗"
echo "║  ✓ Migration Complete!"
echo "║  "
echo "║  Site: https://${DOMAIN}"
echo "║  Admin: https://${DOMAIN}/wp-admin"
echo "╚═══════════════════════════════════════════════════╝"
echo ""
echo "Next steps:"
echo "  1. Test the site thoroughly"
echo "  2. Check admin panel access"
echo "  3. Verify email functionality"
echo "  4. Test forms and plugins"
echo "  5. Update DNS if not done already"
echo "  6. Monitor for 24-48 hours"
echo "  7. Delete site from Nexcess when confident"
