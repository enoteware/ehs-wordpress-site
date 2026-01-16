#!/bin/bash
#
# Quick Deploy to DigitalOcean
# Fast deployment of local DDEV changes directly to DO server
#
# Usage:
#   ./quick-deploy-to-do.sh              # Sync theme only (fastest)
#   ./quick-deploy-to-do.sh --full       # Sync all wp-content
#   ./quick-deploy-to-do.sh --db         # Sync database only
#   ./quick-deploy-to-do.sh --all        # Sync everything (files + db)
#

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
DDEV_PATH="${SCRIPT_DIR}/../ehs-wordpress-local"
DDEV_PROJECT="ehs-mini"
LOCAL_DOMAIN="ehs-mini.ddev.site"
REMOTE_DOMAIN="dev.ehsanalytical.com"

# Load server credentials
if [ -f "${SCRIPT_DIR}/../.env.migration-server" ]; then
    source "${SCRIPT_DIR}/../.env.migration-server"
else
    echo -e "${RED}Error: .env.migration-server not found${NC}"
    exit 1
fi

SERVER_IP="${MIGRATION_SERVER_IP}"
SSH_USER="${MIGRATION_SSH_USER:-root}"

# Auto-detect WordPress path from nginx config
REMOTE_PATH=$(ssh "${SSH_USER}@${SERVER_IP}" "grep -h 'root ' /etc/nginx/sites-enabled/${REMOTE_DOMAIN} 2>/dev/null | head -1 | awk '{print \$2}' | tr -d ';'" 2>/dev/null)

if [ -z "$REMOTE_PATH" ]; then
    # Fallback: find wp-config.php
    REMOTE_PATH=$(ssh "${SSH_USER}@${SERVER_IP}" "find /var/www -name 'wp-config.php' 2>/dev/null | head -1 | xargs dirname" 2>/dev/null)
fi

if [ -z "$REMOTE_PATH" ]; then
    echo -e "${RED}Error: Could not detect WordPress path on server${NC}"
    exit 1
fi

# Parse arguments
SYNC_THEME=true
SYNC_FULL=false
SYNC_DB=false

case "$1" in
    --full)
        SYNC_FULL=true
        SYNC_THEME=false
        ;;
    --db)
        SYNC_DB=true
        SYNC_THEME=false
        ;;
    --all)
        SYNC_FULL=true
        SYNC_DB=true
        SYNC_THEME=false
        ;;
    --help|-h)
        echo "Quick Deploy to DigitalOcean"
        echo ""
        echo "Usage: $0 [option]"
        echo ""
        echo "Options:"
        echo "  (none)    Sync theme only (fastest, ~5 seconds)"
        echo "  --full    Sync all wp-content (slower, ~1-2 minutes)"
        echo "  --db      Sync database only"
        echo "  --all     Sync everything (files + database)"
        echo ""
        exit 0
        ;;
esac

echo -e "${GREEN}╔═══════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║  Quick Deploy to DigitalOcean                     ║${NC}"
echo -e "${GREEN}╚═══════════════════════════════════════════════════╝${NC}"
echo ""
echo "Server: ${SERVER_IP}"
echo "Domain: ${REMOTE_DOMAIN}"
echo "Path:   ${REMOTE_PATH}"
echo ""

# Check DDEV is running
if ! cd "${DDEV_PATH}" && ddev describe > /dev/null 2>&1; then
    echo -e "${YELLOW}Starting DDEV...${NC}"
    cd "${DDEV_PATH}" && ddev start
fi

cd "${DDEV_PATH}"

# Sync theme files (fastest option)
if [ "$SYNC_THEME" = true ]; then
    echo -e "${YELLOW}→ Syncing theme files...${NC}"

    rsync -avz --checksum --delete \
        --exclude='.git' \
        --exclude='node_modules' \
        --exclude='.DS_Store' \
        "${DDEV_PATH}/wordpress/wp-content/themes/hello-elementor-child/" \
        "${SSH_USER}@${SERVER_IP}:${REMOTE_PATH}/wp-content/themes/hello-elementor-child/"

    echo -e "${GREEN}✓ Theme synced${NC}"
fi

# Sync full wp-content
if [ "$SYNC_FULL" = true ]; then
    echo -e "${YELLOW}→ Syncing wp-content (this may take a minute)...${NC}"

    rsync -avz --checksum --delete \
        --exclude='.git' \
        --exclude='node_modules' \
        --exclude='.DS_Store' \
        --exclude='cache' \
        --exclude='wflogs' \
        --exclude='upgrade' \
        --exclude='backups' \
        --exclude='ai1wm-backups' \
        --exclude='updraft' \
        "${DDEV_PATH}/wordpress/wp-content/" \
        "${SSH_USER}@${SERVER_IP}:${REMOTE_PATH}/wp-content/"

    echo -e "${GREEN}✓ wp-content synced${NC}"
fi

# Sync database
if [ "$SYNC_DB" = true ]; then
    echo -e "${YELLOW}→ Exporting local database...${NC}"

    DB_FILE="/tmp/quick-deploy-db.sql"
    ddev export-db --file="${DB_FILE}" --gzip=false

    echo -e "${YELLOW}→ Uploading to server...${NC}"
    scp "${DB_FILE}" "${SSH_USER}@${SERVER_IP}:/tmp/quick-deploy-db.sql"

    echo -e "${YELLOW}→ Importing database and updating URLs...${NC}"
    ssh "${SSH_USER}@${SERVER_IP}" << EOF
cd ${REMOTE_PATH}

# Get database credentials from wp-config.php
DB_NAME=\$(grep "DB_NAME" wp-config.php | cut -d "'" -f 4)
DB_USER=\$(grep "DB_USER" wp-config.php | cut -d "'" -f 4)
DB_PASS=\$(grep "DB_PASSWORD" wp-config.php | cut -d "'" -f 4)

# Import database
mysql -u "\${DB_USER}" -p"\${DB_PASS}" "\${DB_NAME}" < /tmp/quick-deploy-db.sql

# Update URLs
wp search-replace '${LOCAL_DOMAIN}' '${REMOTE_DOMAIN}' --all-tables --allow-root
wp search-replace 'http://${REMOTE_DOMAIN}' 'https://${REMOTE_DOMAIN}' --all-tables --allow-root

# Cleanup
rm /tmp/quick-deploy-db.sql
EOF

    rm -f "${DB_FILE}"
    echo -e "${GREEN}✓ Database synced and URLs updated${NC}"
fi

# Clear caches on remote server
echo -e "${YELLOW}→ Clearing caches...${NC}"
ssh "${SSH_USER}@${SERVER_IP}" << EOF
cd ${REMOTE_PATH}

# Clear WordPress caches
wp cache flush --allow-root 2>/dev/null || true
wp transient delete --all --allow-root 2>/dev/null || true

# Clear Elementor cache
wp elementor flush-css --allow-root 2>/dev/null || true

# Clear any page cache
rm -rf wp-content/cache/* 2>/dev/null || true

# Fix permissions
chown -R www-data:www-data wp-content/
EOF

echo -e "${GREEN}✓ Caches cleared${NC}"

echo ""
echo -e "${GREEN}╔═══════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║  ✓ Deploy Complete!                               ║${NC}"
echo -e "${GREEN}╚═══════════════════════════════════════════════════╝${NC}"
echo ""
echo "Site: https://${REMOTE_DOMAIN}"
echo ""
