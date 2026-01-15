#!/bin/bash
#
# Export WordPress site from Nexcess
# Usage: ./1-export-from-nexcess.sh DOMAIN
#

set -e

if [ -z "$1" ]; then
    echo "Usage: $0 DOMAIN"
    echo "Example: $0 ehsanalytical.com"
    exit 1
fi

DOMAIN=$1
BACKUP_DIR="./backups/${DOMAIN}"
DATE=$(date +%Y%m%d_%H%M%S)

# Load SSH credentials
if [ -f ../.env ]; then
    source ../.env
else
    echo "Error: .env file not found"
    exit 1
fi

echo "=== Exporting ${DOMAIN} from Nexcess ==="
echo "Backup directory: ${BACKUP_DIR}"

# Create backup directory
mkdir -p "${BACKUP_DIR}"

# SSH into Nexcess and create archives
ssh ${SSH_USERNAME}@${SSH_HOSTNAME} << EOF
cd /home/${SSH_USERNAME}/${SSH_HOSTNAME}/html

# Find WordPress installation for this domain
if [ -d "${DOMAIN}" ]; then
    WP_DIR="${DOMAIN}"
elif [ -d "html/${DOMAIN}" ]; then
    WP_DIR="html/${DOMAIN}"
else
    echo "Searching for ${DOMAIN} WordPress installation..."
    WP_DIR=\$(find . -name "wp-config.php" -path "*${DOMAIN}*" | head -1 | xargs dirname)
fi

echo "WordPress directory: \${WP_DIR}"
cd "\${WP_DIR}"

# Export database
echo "Exporting database..."
wp db export /tmp/${DOMAIN}_${DATE}.sql --allow-root

# Compress database
gzip /tmp/${DOMAIN}_${DATE}.sql

# Create archive of WordPress files
echo "Creating files archive..."
tar -czf /tmp/${DOMAIN}_files_${DATE}.tar.gz \
    --exclude='wp-content/cache' \
    --exclude='wp-content/uploads/cache' \
    --exclude='.git' \
    .

echo "Export complete!"
ls -lh /tmp/${DOMAIN}_*
EOF

# Download files from Nexcess
echo ""
echo "=== Downloading files from Nexcess ==="
scp ${SSH_USERNAME}@${SSH_HOSTNAME}:/tmp/${DOMAIN}_${DATE}.sql.gz "${BACKUP_DIR}/"
scp ${SSH_USERNAME}@${SSH_HOSTNAME}:/tmp/${DOMAIN}_files_${DATE}.tar.gz "${BACKUP_DIR}/"

# Cleanup on Nexcess
ssh ${SSH_USERNAME}@${SSH_HOSTNAME} "rm -f /tmp/${DOMAIN}_*"

echo ""
echo "✓ Export complete!"
echo "  Database: ${BACKUP_DIR}/${DOMAIN}_${DATE}.sql.gz"
echo "  Files: ${BACKUP_DIR}/${DOMAIN}_files_${DATE}.tar.gz"
echo ""
echo "Next step: ./2-import-to-digitalocean.sh ${DOMAIN}"
