#!/bin/bash

# Export/Import Elementor Theme Builder Header and Footer Templates
# This script exports templates from production and imports them into local dev

set -e

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Production server details
PROD_SSH="a96c427e_1@832f87585d.nxcli.net"
PROD_WP_PATH="/home/a96c427e/832f87585d.nxcli.net/html"

# Local export directory
EXPORT_DIR="$(pwd)/ehs-wordpress-local/exports/elementor-templates"
mkdir -p "$EXPORT_DIR"

echo -e "${GREEN}=== Elementor Template Export/Import Script ===${NC}\n"

# Step 1: Find header and footer templates in production
echo -e "${YELLOW}Step 1: Finding Elementor templates in production...${NC}"

HEADER_ID=$(ssh $PROD_SSH "cd $PROD_WP_PATH && wp post list --post_type=elementor_library --meta_key=_elementor_template_type --meta_value=header --field=ID --format=count" 2>/dev/null || echo "")
FOOTER_ID=$(ssh $PROD_SSH "cd $PROD_WP_PATH && wp post list --post_type=elementor_library --meta_key=_elementor_template_type --meta_value=footer --field=ID --format=count" 2>/dev/null || echo "")

if [ -z "$HEADER_ID" ] && [ -z "$FOOTER_ID" ]; then
    echo -e "${RED}Error: Could not connect to production or find templates${NC}"
    echo -e "${YELLOW}Trying alternative method...${NC}"
    
    # Alternative: Get all elementor_library posts and filter
    HEADER_ID=$(ssh $PROD_SSH "cd $PROD_WP_PATH && wp post list --post_type=elementor_library --format=ids" 2>/dev/null | while read id; do
        template_type=$(ssh $PROD_SSH "cd $PROD_WP_PATH && wp post meta get $id _elementor_template_type" 2>/dev/null)
        if [ "$template_type" = "header" ]; then
            echo $id
            break
        fi
    done)
    
    FOOTER_ID=$(ssh $PROD_SSH "cd $PROD_WP_PATH && wp post list --post_type=elementor_library --format=ids" 2>/dev/null | while read id; do
        template_type=$(ssh $PROD_SSH "cd $PROD_WP_PATH && wp post meta get $id _elementor_template_type" 2>/dev/null)
        if [ "$template_type" = "footer" ]; then
            echo $id
            break
        fi
    done)
fi

if [ -z "$HEADER_ID" ] && [ -z "$FOOTER_ID" ]; then
    echo -e "${RED}Error: Could not find header or footer templates in production${NC}"
    echo -e "${YELLOW}Listing all Elementor templates to help debug...${NC}"
    ssh $PROD_SSH "cd $PROD_WP_PATH && wp post list --post_type=elementor_library --format=table" || true
    exit 1
fi

echo -e "${GREEN}Found templates:${NC}"
[ -n "$HEADER_ID" ] && echo -e "  Header Template ID: $HEADER_ID" || echo -e "  ${YELLOW}No header template found${NC}"
[ -n "$FOOTER_ID" ] && echo -e "  Footer Template ID: $FOOTER_ID" || echo -e "  ${YELLOW}No footer template found${NC}"

# Step 2: Export templates
echo -e "\n${YELLOW}Step 2: Exporting templates from production...${NC}"

if [ -n "$HEADER_ID" ]; then
    echo -e "Exporting header template (ID: $HEADER_ID)..."
    ssh $PROD_SSH "cd $PROD_WP_PATH && wp post get $HEADER_ID --format=json" > "$EXPORT_DIR/header-template.json" 2>/dev/null || {
        echo -e "${RED}Failed to export header template${NC}"
    }
    echo -e "${GREEN}Header template exported${NC}"
fi

if [ -n "$FOOTER_ID" ]; then
    echo -e "Exporting footer template (ID: $FOOTER_ID)..."
    ssh $PROD_SSH "cd $PROD_WP_PATH && wp post get $FOOTER_ID --format=json" > "$EXPORT_DIR/footer-template.json" 2>/dev/null || {
        echo -e "${RED}Failed to export footer template${NC}"
    }
    echo -e "${GREEN}Footer template exported${NC}"
fi

# Step 3: Export all post meta for templates
echo -e "\n${YELLOW}Step 3: Exporting template metadata...${NC}"

if [ -n "$HEADER_ID" ]; then
    echo -e "Exporting header template metadata..."
    ssh $PROD_SSH "cd $PROD_WP_PATH && wp post meta list $HEADER_ID --format=json" > "$EXPORT_DIR/header-meta.json" 2>/dev/null || {
        echo -e "${YELLOW}Warning: Could not export header metadata (may need manual export)${NC}"
    }
fi

if [ -n "$FOOTER_ID" ]; then
    echo -e "Exporting footer template metadata..."
    ssh $PROD_SSH "cd $PROD_WP_PATH && wp post meta list $FOOTER_ID --format=json" > "$EXPORT_DIR/footer-meta.json" 2>/dev/null || {
        echo -e "${YELLOW}Warning: Could not export footer metadata (may need manual export)${NC}"
    }
fi

echo -e "\n${GREEN}Export complete! Files saved to: $EXPORT_DIR${NC}"
echo -e "\n${YELLOW}Next: Run the import script to import these templates into local dev${NC}"
echo -e "Or use: ddev wp import-elementor-template [header|footer]"
