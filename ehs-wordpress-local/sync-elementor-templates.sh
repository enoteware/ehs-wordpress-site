#!/bin/bash

# Sync Elementor Theme Builder Templates from Production to Local Dev
# This script exports templates from production and imports them into local dev

set -e

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Load SSH credentials from .env file if it exists
if [ -f "$(pwd)/.env" ]; then
    source "$(pwd)/.env"
fi
# Also try loading from project root
if [ -f "$(pwd)/../.env" ]; then
    source "$(pwd)/../.env"
fi

# Check if sshpass is available and password is set
USE_SSHPASS=false
if command -v sshpass > /dev/null 2>&1 && [ -n "$SSH_PASSWORD" ]; then
    USE_SSHPASS=true
    echo -e "${GREEN}Using sshpass for automated password authentication${NC}"
fi

# Production server details (use .env if available, otherwise defaults)
# Use SSH config alias if available, otherwise construct connection string
PROD_SSH_ALIAS="${PROD_SSH_ALIAS:-ehs-prod}"
PROD_SSH_USER="${PROD_SSH_USER:-a96c427e_1}"
PROD_SSH_HOST="${PROD_SSH_HOST:-832f87585d.nxcli.net}"
# Prefer SSH config alias, fallback to direct connection
# Note: BatchMode test will fail for password auth, so we'll try the alias first
PROD_SSH="$PROD_SSH_ALIAS"
PROD_WP_PATH="${PROD_WP_PATH:-/home/a96c427e/832f87585d.nxcli.net/html}"

# SSH connection multiplexing to avoid repeated password prompts
SSH_CONTROL_PATH="$HOME/.ssh/cm-%r@%h:%p"
SSH_OPTS="-o PreferredAuthentications=password -o PubkeyAuthentication=no -o ControlMaster=auto -o ControlPath=$SSH_CONTROL_PATH -o ControlPersist=300"

# Function to run SSH with or without sshpass
ssh_cmd() {
    if [ "$USE_SSHPASS" = true ]; then
        sshpass -p "$SSH_PASSWORD" ssh $SSH_OPTS "$@"
    else
        ssh $SSH_OPTS "$@"
    fi
}

# Function to run SCP with or without sshpass
scp_cmd() {
    if [ "$USE_SSHPASS" = true ]; then
        sshpass -p "$SSH_PASSWORD" scp $SSH_OPTS "$@"
    else
        scp $SSH_OPTS "$@"
    fi
}

# Local export directory (relative to script location)
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
EXPORT_DIR="$SCRIPT_DIR/exports/elementor-templates"
mkdir -p "$EXPORT_DIR"

echo -e "${BLUE}=== Elementor Template Sync Script ===${NC}\n"
echo -e "This script will:"
echo -e "  1. Find header/footer templates in production"
echo -e "  2. Export them with all Elementor data"
echo -e "  3. Import them into local dev\n"

# Check if DDEV is running
if ! ddev describe > /dev/null 2>&1; then
    echo -e "${RED}Error: DDEV is not running${NC}"
    echo -e "${YELLOW}Please start DDEV first: ddev start${NC}"
    exit 1
fi

# Step 1: Find templates in production
echo -e "${YELLOW}Step 1: Finding Elementor templates in production...${NC}"

# Get all elementor_library posts and check their template types
HEADER_ID=""
FOOTER_ID=""

echo "Fetching Elementor library posts..."
# Test SSH connection first
# Use connection multiplexing so we only authenticate once
echo -e "${YELLOW}Testing SSH connection to $PROD_SSH...${NC}"
if [ "$USE_SSHPASS" = true ]; then
    echo -e "${GREEN}Using automated password authentication${NC}"
else
    echo -e "${YELLOW}You will be prompted for password ONCE (connection will be reused)${NC}"
fi
if ! ssh_cmd -o ConnectTimeout=10 $PROD_SSH "echo 'Connection successful'" 2>&1; then
    echo -e "${RED}Error: Could not connect to production server${NC}"
    echo -e "${YELLOW}Please test connection manually: ssh $PROD_SSH${NC}"
    echo -e "${YELLOW}If connection works manually, try running this script again${NC}"
    exit 1
fi
echo -e "${GREEN}SSH connection successful (connection will be reused for subsequent commands)${NC}"

# Fetch templates (reuses the connection, no password prompt)
ALL_TEMPLATES=$(ssh_cmd -o ConnectTimeout=10 $PROD_SSH "cd $PROD_WP_PATH && wp post list --post_type=elementor_library --format=ids" 2>&1 || echo "")

if [ -z "$ALL_TEMPLATES" ]; then
    echo -e "${RED}Error: Could not fetch templates or no templates found${NC}"
    echo -e "${YELLOW}SSH output: $ALL_TEMPLATES${NC}"
    exit 1
fi

echo "Checking template types..."
for template_id in $ALL_TEMPLATES; do
    template_type=$(ssh_cmd -o ConnectTimeout=10 $PROD_SSH "cd $PROD_WP_PATH && wp post meta get $template_id _elementor_template_type" 2>/dev/null || echo "")
    
    if [ "$template_type" = "header" ] && [ -z "$HEADER_ID" ]; then
        HEADER_ID=$template_id
        echo -e "${GREEN}Found header template (ID: $HEADER_ID)${NC}"
    elif [ "$template_type" = "footer" ] && [ -z "$FOOTER_ID" ]; then
        FOOTER_ID=$template_id
        echo -e "${GREEN}Found footer template (ID: $FOOTER_ID)${NC}"
    fi
done

if [ -z "$HEADER_ID" ] && [ -z "$FOOTER_ID" ]; then
    echo -e "${RED}Error: No header or footer templates found in production${NC}"
    exit 1
fi

# Step 2: Export templates using PHP script on production
echo -e "\n${YELLOW}Step 2: Exporting templates from production...${NC}"

# Create a modified version of the export script with correct WordPress path
EXPORT_SCRIPT="/tmp/export-elementor-$$.php"
EXPORT_SCRIPT_LOCAL="$(pwd)/export-elementor-templates.php"

if [ ! -f "$EXPORT_SCRIPT_LOCAL" ]; then
    echo -e "${RED}Error: Export script not found: $EXPORT_SCRIPT_LOCAL${NC}"
    exit 1
fi

# Modify the script to use absolute WordPress path
sed "s|__DIR__ . '/wordpress/wp-load.php'|'$PROD_WP_PATH/wp-load.php'|" "$EXPORT_SCRIPT_LOCAL" > /tmp/export-elementor-temp-$$.php

echo "Uploading export script to production..."
scp_cmd -o ConnectTimeout=10 /tmp/export-elementor-temp-$$.php $PROD_SSH:$EXPORT_SCRIPT 2>&1 || {
    echo -e "${RED}Error: Failed to upload export script${NC}"
    rm -f /tmp/export-elementor-temp-$$.php
    exit 1
}

# Cleanup local temp file
rm -f /tmp/export-elementor-temp-$$.php

# Run export on production (reuses connection, no password prompt)
if [ -n "$HEADER_ID" ] || [ -n "$FOOTER_ID" ]; then
    echo "Running export script on production..."
    ssh_cmd -o ConnectTimeout=10 $PROD_SSH "cd $PROD_WP_PATH && php /tmp/export-elementor-$$.php ${HEADER_ID:-0} ${FOOTER_ID:-0}" 2>&1 || {
        echo -e "${RED}Error: Failed to run export script${NC}"
        exit 1
    }
    
    # Download exported files (reuses connection, no password prompt)
    # The export script saves to /tmp/exports/elementor-templates/ based on the export script
    if [ -n "$HEADER_ID" ]; then
        echo "Downloading header template..."
        scp_cmd -o ConnectTimeout=10 $PROD_SSH:/tmp/exports/elementor-templates/header-template.json "$EXPORT_DIR/header-template.json" 2>&1 && \
            echo -e "${GREEN}Header template exported${NC}" || \
            echo -e "${RED}Failed to export header template${NC}"
    fi
    
    if [ -n "$FOOTER_ID" ]; then
        echo "Downloading footer template..."
        scp_cmd -o ConnectTimeout=10 $PROD_SSH:/tmp/exports/elementor-templates/footer-template.json "$EXPORT_DIR/footer-template.json" 2>&1 && \
            echo -e "${GREEN}Footer template exported${NC}" || \
            echo -e "${RED}Failed to export footer template${NC}"
    fi
    
    # Cleanup on production (reuses connection, no password prompt)
    ssh_cmd -o ConnectTimeout=10 $PROD_SSH "rm -f /tmp/export-elementor-$$.php /tmp/exports/elementor-templates/*.json" 2>/dev/null || true
fi

# Close the SSH control connection
if [ "$USE_SSHPASS" = true ]; then
    sshpass -p "$SSH_PASSWORD" ssh -o ControlPath=$SSH_CONTROL_PATH -O exit $PROD_SSH 2>/dev/null || true
else
    ssh -o ControlPath=$SSH_CONTROL_PATH -O exit $PROD_SSH 2>/dev/null || true
fi

# Cleanup local temp file
rm -f /tmp/export-elementor-local.php

# Step 3: Import into local dev
echo -e "\n${YELLOW}Step 3: Importing templates into local dev...${NC}"

if [ -f "$EXPORT_DIR/header-template.json" ] || [ -f "$EXPORT_DIR/footer-template.json" ]; then
    ddev wp eval-file import-elementor-templates.php
    echo -e "\n${GREEN}=== Sync Complete ===${NC}"
    echo -e "\n${YELLOW}Next steps:${NC}"
    echo -e "1. Go to WordPress admin: Templates > Theme Builder"
    echo -e "2. Verify header and footer templates are active"
    echo -e "3. Clear Elementor cache if needed"
else
    echo -e "${RED}Error: No templates were exported${NC}"
    exit 1
fi
