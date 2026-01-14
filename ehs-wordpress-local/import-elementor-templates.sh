#!/bin/bash

# Import Elementor Theme Builder Templates into Local Dev
# This script imports previously exported templates

set -e

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Export directory
EXPORT_DIR="$(pwd)/ehs-wordpress-local/exports/elementor-templates"

if [ ! -d "$EXPORT_DIR" ]; then
    echo -e "${RED}Error: Export directory not found: $EXPORT_DIR${NC}"
    echo -e "${YELLOW}Please run export-import-elementor-templates.sh first${NC}"
    exit 1
fi

echo -e "${GREEN}=== Elementor Template Import Script ===${NC}\n"

# Check if DDEV is running
if ! ddev describe > /dev/null 2>&1; then
    echo -e "${RED}Error: DDEV is not running${NC}"
    echo -e "${YELLOW}Please start DDEV first: ddev start${NC}"
    exit 1
fi

# Function to import a template
import_template() {
    local template_type=$1
    local json_file="$EXPORT_DIR/${template_type}-template.json"
    local meta_file="$EXPORT_DIR/${template_type}-meta.json"
    
    if [ ! -f "$json_file" ]; then
        echo -e "${YELLOW}Warning: ${template_type} template JSON not found, skipping...${NC}"
        return 1
    fi
    
    echo -e "\n${YELLOW}Importing ${template_type} template...${NC}"
    
    # Parse JSON and create post
    local title=$(cat "$json_file" | ddev wp eval 'echo json_decode(file_get_contents("php://stdin"))->post_title;' 2>/dev/null || echo "${template_type^} Template")
    local content=$(cat "$json_file" | ddev wp eval 'echo json_decode(file_get_contents("php://stdin"))->post_content;' 2>/dev/null || echo "")
    local status=$(cat "$json_file" | ddev wp eval 'echo json_decode(file_get_contents("php://stdin"))->post_status;' 2>/dev/null || echo "publish")
    
    # Create the post
    local new_id=$(ddev wp post create --post_type=elementor_library --post_title="$title" --post_content="$content" --post_status="$status" --porcelain 2>/dev/null)
    
    if [ -z "$new_id" ]; then
        echo -e "${RED}Failed to create ${template_type} template post${NC}"
        return 1
    fi
    
    echo -e "${GREEN}Created ${template_type} template (ID: $new_id)${NC}"
    
    # Import metadata
    if [ -f "$meta_file" ]; then
        echo -e "Importing metadata..."
        # This is complex - we'll need to parse and set each meta key
        # For now, we'll set the critical ones manually
    fi
    
    # Set critical Elementor meta fields
    echo -e "Setting Elementor metadata..."
    ddev wp post meta update $new_id _elementor_template_type "$template_type" 2>/dev/null || true
    ddev wp post meta update $new_id _elementor_edit_mode "builder" 2>/dev/null || true
    
    # Try to get and set Elementor data from production
    echo -e "${YELLOW}Note: Elementor data (_elementor_data) needs to be copied manually${NC}"
    echo -e "${YELLOW}You may need to export it separately from production${NC}"
    
    echo -e "${GREEN}${template_type^} template imported successfully (ID: $new_id)${NC}"
    return 0
}

# Import header template
if [ -f "$EXPORT_DIR/header-template.json" ]; then
    import_template "header"
else
    echo -e "${YELLOW}Header template not found, skipping...${NC}"
fi

# Import footer template
if [ -f "$EXPORT_DIR/footer-template.json" ]; then
    import_template "footer"
else
    echo -e "${YELLOW}Footer template not found, skipping...${NC}"
fi

echo -e "\n${GREEN}Import complete!${NC}"
echo -e "\n${YELLOW}Next steps:${NC}"
echo -e "1. Check WordPress admin: Templates > Theme Builder"
echo -e "2. Verify header and footer templates are active"
echo -e "3. If Elementor data is missing, you may need to manually copy _elementor_data meta from production"
