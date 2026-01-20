#!/bin/bash
#
# Import WordPress site from local DDEV to DigitalOcean server
# Usage: ./import-ddev-to-digitalocean.sh DDEV_PROJECT_NAME DOMAIN [DDEV_PATH]
#
# Example: ./import-ddev-to-digitalocean.sh ehs-mini dev.ehsanalytical.com
# Example: ./import-ddev-to-digitalocean.sh ehs-mini dev.ehsanalytical.com /path/to/ehs-wordpress-local
#

set -e

if [ -z "$1" ] || [ -z "$2" ]; then
    echo "Usage: $0 DDEV_PROJECT_NAME DOMAIN [DDEV_PATH]"
    echo "Example: $0 ehs-mini dev.ehsanalytical.com"
    echo "Example: $0 ehs-mini dev.ehsanalytical.com /path/to/ehs-wordpress-local"
    exit 1
fi

DDEV_PROJECT=$1
DOMAIN=$2
DDEV_PATH=${3:-"../ehs-wordpress-local"}

# Load server credentials
if [ -f ../.env.migration-server ]; then
    source ../.env.migration-server
else
    echo "Error: .env.migration-server file not found"
    exit 1
fi

# Setup SSH key
SSH_KEY="${MIGRATION_SSH_KEY:-}"
if [ -n "$SSH_KEY" ]; then
    SSH_KEY="${SSH_KEY/#\~/$HOME}"
fi

# Build SSH/SCP commands with key if specified
SSH_CMD="ssh"
SCP_CMD="scp"
if [ -n "$SSH_KEY" ] && [ -f "$SSH_KEY" ]; then
    SSH_CMD="ssh -i $SSH_KEY"
    SCP_CMD="scp -i $SSH_KEY"
fi

echo "=== Importing DDEV site to DigitalOcean ==="
echo "DDEV Project: ${DDEV_PROJECT}"
echo "Domain: ${DOMAIN}"
echo "DDEV Path: ${DDEV_PATH}"
echo "Server: ${MIGRATION_SERVER_IP}"
echo ""

# Check if DDEV path exists
if [ ! -d "$DDEV_PATH" ]; then
    echo "Error: DDEV path not found: ${DDEV_PATH}"
    exit 1
fi

# Check if DDEV project is running
cd "$DDEV_PATH"
if ! ddev describe ${DDEV_PROJECT} > /dev/null 2>&1; then
    echo "Error: DDEV project '${DDEV_PROJECT}' not found or not running"
    echo "Start DDEV with: cd ${DDEV_PATH} && ddev start"
    exit 1
fi

DATE=$(date +%Y%m%d_%H%M%S)
TEMP_DIR="/tmp/ddev-migration-${DATE}"
mkdir -p "${TEMP_DIR}"

echo "=== Step 1: Exporting from DDEV ==="
echo "Exporting database..."
ddev export-db --file="${TEMP_DIR}/ddev-export.sql.gz"

echo "Creating wp-content archive..."
cd "$DDEV_PATH"
if [ -d "wordpress/wp-content" ]; then
    tar -czf "${TEMP_DIR}/ddev-wpcontent.tar.gz" -C wordpress wp-content
elif [ -d "wp-content" ]; then
    tar -czf "${TEMP_DIR}/ddev-wpcontent.tar.gz" wp-content
else
    echo "Error: wp-content directory not found"
    exit 1
fi

echo "✓ Export complete"
echo "  Database: ${TEMP_DIR}/ddev-export.sql.gz"
echo "  Files: ${TEMP_DIR}/ddev-wpcontent.tar.gz"
echo ""

# Upload files to server
echo "=== Step 2: Uploading to DigitalOcean server ==="
$SCP_CMD "${TEMP_DIR}/ddev-export.sql.gz" root@${MIGRATION_SERVER_IP}:/tmp/
$SCP_CMD "${TEMP_DIR}/ddev-wpcontent.tar.gz" root@${MIGRATION_SERVER_IP}:/tmp/
echo "✓ Files uploaded"
echo ""

# Determine WordPress path on server
WP_PATH="/var/www/${DOMAIN}/html"
if [ "$DOMAIN" != "${DOMAIN#test.}" ]; then
    # If domain starts with "test.", use test.ehsanalytical.com structure
    WP_PATH="/var/www/test.ehsanalytical.com/html"
fi

echo "=== Step 3: Importing on server ==="
$SSH_CMD root@${MIGRATION_SERVER_IP} << EOF
set -e

WP_PATH="${WP_PATH}"
DOMAIN="${DOMAIN}"
DATE="${DATE}"

echo "WordPress path: \${WP_PATH}"

# Check if WordPress installation exists
if [ ! -d "\${WP_PATH}" ]; then
    echo "Error: WordPress path not found: \${WP_PATH}"
    echo "Create the site first or check the path"
    exit 1
fi

cd "\${WP_PATH}"

# Get database credentials
DB_NAME=\$(wp config get DB_NAME --allow-root 2>/dev/null || echo "")
DB_USER=\$(wp config get DB_USER --allow-root 2>/dev/null || echo "")

if [ -z "\$DB_NAME" ]; then
    echo "Error: Could not determine database name from wp-config.php"
    exit 1
fi

echo "Database: \$DB_NAME"
echo ""

# Backup existing site
echo "=== Backing up existing site ==="
BACKUP_DATE=\$(date +%Y%m%d_%H%M%S)
if [ -f wp-config.php ]; then
    wp db export /tmp/backup-\${DOMAIN}-\${BACKUP_DATE}.sql --allow-root 2>/dev/null || true
    gzip /tmp/backup-\${DOMAIN}-\${BACKUP_DATE}.sql 2>/dev/null || true
    tar -czf /tmp/backup-\${DOMAIN}-wpcontent-\${BACKUP_DATE}.tar.gz wp-content 2>/dev/null || true
    echo "✓ Backup created"
fi
echo ""

# Import database
echo "=== Importing database ==="
gunzip < /tmp/ddev-export.sql.gz | mysql -u root "\$DB_NAME"
echo "✓ Database imported"
echo ""

# Replace wp-content
echo "=== Replacing wp-content ==="
mv wp-content wp-content.backup-\${BACKUP_DATE} 2>/dev/null || true
tar -xzf /tmp/ddev-wpcontent.tar.gz 2>&1 | grep -v "LIBARCHIVE.xattr" | tail -5 || true
echo "✓ wp-content replaced"
echo ""

# Update URLs
echo "=== Updating URLs ==="
LOCAL_URL="http://\${DDEV_PROJECT:-${DDEV_PROJECT}}.ddev.site"
wp search-replace "\${LOCAL_URL}" "https://\${DOMAIN}" --all-tables --skip-columns=guid --allow-root 2>/dev/null || true
wp search-replace "\${LOCAL_URL/http:/https:}" "https://\${DOMAIN}" --all-tables --skip-columns=guid --allow-root 2>/dev/null || true
wp option update siteurl "https://\${DOMAIN}" --allow-root 2>/dev/null || true
wp option update home "https://\${DOMAIN}" --allow-root 2>/dev/null || true
echo "✓ URLs updated"
echo ""

# Set permissions
echo "=== Setting permissions ==="
chown -R www-data:www-data "\${WP_PATH}"
find "\${WP_PATH}" -type d -exec chmod 755 {} \;
find "\${WP_PATH}" -type f -exec chmod 644 {} \;
chmod 600 "\${WP_PATH}/wp-config.php" 2>/dev/null || true
chmod -R 755 "\${WP_PATH}/wp-content"
echo "✓ Permissions set"
echo ""

# Clear caches
echo "=== Clearing caches ==="
wp cache flush --allow-root 2>/dev/null || true
wp elementor flush-css --allow-root 2>/dev/null || true
wp transient delete --all --allow-root 2>/dev/null || true
systemctl reload nginx 2>/dev/null || true
echo "✓ Caches cleared"
echo ""

# Cleanup uploaded files
rm -f /tmp/ddev-export.sql.gz /tmp/ddev-wpcontent.tar.gz

echo "=== Migration Complete ==="
echo "Site: https://\${DOMAIN}"
echo "Admin: https://\${DOMAIN}/wp-admin"
echo ""
echo "Backups saved to:"
echo "  /tmp/backup-\${DOMAIN}-\${BACKUP_DATE}.sql.gz"
echo "  /tmp/backup-\${DOMAIN}-wpcontent-\${BACKUP_DATE}.tar.gz"

EOF

# Cleanup local temp files
rm -rf "${TEMP_DIR}"

echo ""
echo "=== Migration Summary ==="
echo "✓ Site imported successfully"
echo "  Domain: ${DOMAIN}"
echo "  Site: https://${DOMAIN}"
echo "  Admin: https://${DOMAIN}/wp-admin"
echo ""
echo "Next steps:"
echo "  1. Test the site at https://${DOMAIN}"
echo "  2. Verify all plugins are active"
echo "  3. Check that theme is active"
echo "  4. Test key functionality (forms, pages, etc.)"
