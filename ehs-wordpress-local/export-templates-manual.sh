#!/bin/bash

# Manual Elementor Template Export Script
# Run this script and it will prompt you to SSH into production and run commands
# Then it will download the exported files and import them

set -e

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Load from .env
if [ -f "$(pwd)/../.env" ]; then
    source "$(pwd)/../.env"
fi

PROD_SSH_USER="${PROD_SSH_USER:-a96c427e_1}"
PROD_SSH_HOST="${PROD_SSH_HOST:-832f87585d.nxcli.net}"
PROD_SSH="${PROD_SSH_USER}@${PROD_SSH_HOST}"
PROD_WP_PATH="${PROD_WP_PATH:-/home/a96c427e/832f87585d.nxcli.net/html}"

EXPORT_DIR="$(pwd)/exports/elementor-templates"
mkdir -p "$EXPORT_DIR"

echo -e "${BLUE}=== Manual Elementor Template Export ===${NC}\n"
echo -e "This script will guide you through exporting templates manually.\n"
echo -e "${YELLOW}Step 1: SSH into production${NC}"
echo -e "Run this command:"
echo -e "  ${GREEN}ssh ${PROD_SSH}${NC}\n"

echo -e "${YELLOW}Step 2: Once connected, run these commands:${NC}\n"

cat << 'EOF'
# Find template IDs
cd /home/a96c427e/832f87585d.nxcli.net/html
wp post list --post_type=elementor_library --format=table

# Find header and footer IDs (look for _elementor_template_type = header or footer)
HEADER_ID=$(wp post list --post_type=elementor_library --meta_key=_elementor_template_type --meta_value=header --field=ID --format=csv | head -1)
FOOTER_ID=$(wp post list --post_type=elementor_library --meta_key=_elementor_template_type --meta_value=footer --field=ID --format=csv | head -1)

echo "Header ID: $HEADER_ID"
echo "Footer ID: $FOOTER_ID"

# Export using the PHP script
php -r "
require_once 'wp-load.php';
\$export_dir = '/tmp/elementor-export';
if (!is_dir(\$export_dir)) mkdir(\$export_dir, 0755, true);

function export_template(\$post_id, \$export_dir, \$type) {
    \$post = get_post(\$post_id);
    if (!\$post) return false;
    \$all_meta = get_post_meta(\$post_id);
    \$data = [
        'post' => [
            'ID' => \$post->ID,
            'post_title' => \$post->post_title,
            'post_name' => \$post->post_name,
            'post_content' => \$post->post_content,
            'post_status' => \$post->post_status,
            'post_type' => \$post->post_type,
            'post_date' => \$post->post_date,
            'post_modified' => \$post->post_modified,
            'post_author' => \$post->post_author,
        ],
        'meta' => [],
    ];
    foreach (\$all_meta as \$key => \$values) {
        \$data['meta'][\$key] = count(\$values) === 1 ? maybe_unserialize(\$values[0]) : array_map('maybe_unserialize', \$values);
    }
    file_put_contents(\$export_dir . '/' . \$type . '-template.json', json_encode(\$data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    return true;
}

if (\$HEADER_ID) export_template(\$HEADER_ID, \$export_dir, 'header');
if (\$FOOTER_ID) export_template(\$FOOTER_ID, \$export_dir, 'footer');
echo 'Export complete. Files in /tmp/elementor-export/';
"

EOF

echo -e "\n${YELLOW}Step 3: Download the exported files${NC}"
echo -e "From your local machine, run:"
echo -e "  ${GREEN}scp ${PROD_SSH}:/tmp/elementor-export/*.json ${EXPORT_DIR}/${NC}\n"

echo -e "${YELLOW}Step 4: Import into local dev${NC}"
echo -e "Run:"
echo -e "  ${GREEN}cd $(pwd) && ddev wp eval-file import-elementor-templates.php${NC}\n"

echo -e "${BLUE}Press Enter when you've completed the export and download...${NC}"
read

# Check if files exist
if [ -f "$EXPORT_DIR/header-template.json" ] || [ -f "$EXPORT_DIR/footer-template.json" ]; then
    echo -e "\n${GREEN}Files found! Importing...${NC}\n"
    ddev wp eval-file import-elementor-templates.php
    echo -e "\n${GREEN}=== Import Complete ===${NC}"
else
    echo -e "\n${YELLOW}No template files found. Please complete the export and download steps first.${NC}"
fi
