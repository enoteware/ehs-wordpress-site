#!/bin/bash
#
# List all WordPress sites on Nexcess server
#

set -e

# Load SSH credentials
if [ -f ../.env ]; then
    source ../.env
else
    echo "Error: .env file not found"
    exit 1
fi

echo "=== WordPress Sites on Nexcess ==="
echo "Server: ${SSH_HOSTNAME}"
echo ""

ssh ${SSH_USERNAME}@${SSH_HOSTNAME} << 'EOF'
# Find all wp-config.php files
echo "Searching for WordPress installations..."
echo ""

find /home -name "wp-config.php" 2>/dev/null | while read config; do
    WP_DIR=$(dirname "$config")
    cd "$WP_DIR"

    # Get site info using WP-CLI
    if command -v wp &> /dev/null; then
        SITE_URL=$(wp option get siteurl --allow-root 2>/dev/null || echo "Unknown")
        SITE_TITLE=$(wp option get blogname --allow-root 2>/dev/null || echo "Unknown")
        WP_VERSION=$(wp core version --allow-root 2>/dev/null || echo "Unknown")

        echo "────────────────────────────────────────"
        echo "Site: $SITE_TITLE"
        echo "URL: $SITE_URL"
        echo "Version: WordPress $WP_VERSION"
        echo "Path: $WP_DIR"

        # Get database info
        DB_NAME=$(wp config get DB_NAME --allow-root 2>/dev/null || echo "Unknown")
        echo "Database: $DB_NAME"

        # Get directory size
        SIZE=$(du -sh "$WP_DIR" 2>/dev/null | cut -f1)
        echo "Size: $SIZE"

    else
        echo "────────────────────────────────────────"
        echo "Path: $WP_DIR"
        echo "(WP-CLI not available for details)"
    fi
    echo ""
done

echo "========================================="
echo "Search complete!"
EOF
