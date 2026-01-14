#!/bin/bash

# Site Audit Script - Check status of all Nexcess sites
# Helps determine which sites to keep vs. delete

set -e

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

# Site list from Nexcess
SITES=(
    "accreditedlabs.co"
    "koniclabs.com"
    "notewaredigital.com"
    "pickproslv.com"
    "gonsalvi.com"
    "iepa.com"
    "reflexwatch.com"
    "letsgoev.com"
    "old.avmw.com"
    "capitolstrategiesgroup.com"
    "dressfresh.com"
    "californiadistributorsassociation.com"
    "ehsanalytical.com"
    "frednoteware.com"
    "rrfmedia.com"
    "alisoltanilaw.com"
    "dragondigitalllc.com"
    "phoenixbottles.co"
    "kelseypilates.com"
    "japan.cultureroute.com"
    "lemeeseghosal.com"
    "acgautomotive.com"
    "rescuepawsthailand.org"
    "ibcpa.com"
    "4d20afbdc3.nxcli.net"
)

echo -e "${BLUE}=== Site Audit Report ===${NC}\n"
echo -e "Checking ${#SITES[@]} sites...\n"

# Create results file
RESULTS_FILE="site-audit-results-$(date +%Y%m%d).txt"
echo "Site Audit Results - $(date)" > "$RESULTS_FILE"
echo "=================================" >> "$RESULTS_FILE"
echo "" >> "$RESULTS_FILE"

# Function to check site status
check_site() {
    local domain=$1
    local url="https://$domain"
    
    echo -e "${YELLOW}Checking: $domain${NC}"
    
    # Try HTTP first, then HTTPS
    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" --max-time 10 "$url" 2>/dev/null || echo "000")
    
    if [ "$HTTP_CODE" = "000" ]; then
        # Try HTTP
        url="http://$domain"
        HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" --max-time 10 "$url" 2>/dev/null || echo "000")
    fi
    
    # Get redirect location if any
    REDIRECT=$(curl -s -o /dev/null -w "%{redirect_url}" --max-time 10 "$url" 2>/dev/null || echo "")
    
    # Get last modified date
    LAST_MOD=$(curl -s -I "$url" --max-time 10 2>/dev/null | grep -i "last-modified" | cut -d' ' -f2- || echo "Unknown")
    
    # Check if WordPress
    IS_WP=$(curl -s "$url" --max-time 10 2>/dev/null | grep -i "wp-content\|wp-includes" | head -1 | wc -l || echo "0")
    
    # Determine status
    if [ "$HTTP_CODE" = "200" ]; then
        STATUS="${GREEN}✓ ACTIVE${NC}"
        STATUS_TEXT="ACTIVE"
    elif [ "$HTTP_CODE" = "301" ] || [ "$HTTP_CODE" = "302" ]; then
        STATUS="${YELLOW}→ REDIRECT${NC}"
        STATUS_TEXT="REDIRECT"
    elif [ "$HTTP_CODE" = "000" ]; then
        STATUS="${RED}✗ OFFLINE${NC}"
        STATUS_TEXT="OFFLINE"
    else
        STATUS="${YELLOW}? $HTTP_CODE${NC}"
        STATUS_TEXT="HTTP_$HTTP_CODE"
    fi
    
    # Print results
    echo -e "  Status: $STATUS"
    echo -e "  HTTP Code: $HTTP_CODE"
    if [ -n "$REDIRECT" ] && [ "$REDIRECT" != "$url" ]; then
        echo -e "  Redirects to: $REDIRECT"
    fi
    if [ "$IS_WP" = "1" ]; then
        echo -e "  Type: ${BLUE}WordPress${NC}"
    fi
    if [ -n "$LAST_MOD" ] && [ "$LAST_MOD" != "Unknown" ]; then
        echo -e "  Last Modified: $LAST_MOD"
    fi
    echo ""
    
    # Write to file
    echo "$domain" >> "$RESULTS_FILE"
    echo "  Status: $STATUS_TEXT" >> "$RESULTS_FILE"
    echo "  HTTP Code: $HTTP_CODE" >> "$RESULTS_FILE"
    if [ -n "$REDIRECT" ] && [ "$REDIRECT" != "$url" ]; then
        echo "  Redirects to: $REDIRECT" >> "$RESULTS_FILE"
    fi
    echo "  WordPress: $([ "$IS_WP" = "1" ] && echo "Yes" || echo "No")" >> "$RESULTS_FILE"
    echo "" >> "$RESULTS_FILE"
}

# Check all sites
for site in "${SITES[@]}"; do
    check_site "$site"
    sleep 1  # Be nice to servers
done

echo -e "${GREEN}=== Audit Complete ===${NC}"
echo -e "Results saved to: ${BLUE}$RESULTS_FILE${NC}\n"

# Summary
echo -e "${BLUE}=== Quick Summary ===${NC}"
echo -e "Total sites checked: ${#SITES[@]}"
echo -e "\nReview the results file for detailed information."
echo -e "Sites marked OFFLINE or with errors may be candidates for deletion."
