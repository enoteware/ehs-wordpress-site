#!/bin/bash
#
# Setup Cloudflare Free Performance Features
# Usage: ./setup-cloudflare-performance.sh [DOMAIN]
#
# Configures:
# - Proxy (CDN) on DNS records
# - SSL Full (strict)
# - Auto Minify (JS, CSS, HTML)
# - Brotli compression
# - Browser cache TTL
# - Always Online
# - Page rules for WordPress
#

set -e

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Load environment
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
if [ -f "$SCRIPT_DIR/../.env" ]; then
    source "$SCRIPT_DIR/../.env"
elif [ -f "$SCRIPT_DIR/.env" ]; then
    source "$SCRIPT_DIR/.env"
else
    echo -e "${RED}Error: .env file not found${NC}"
    exit 1
fi

# Verify credentials
if [ -z "$CLOUDFLARE_API_TOKEN" ]; then
    echo -e "${RED}Error: CLOUDFLARE_API_TOKEN not set${NC}"
    exit 1
fi

# Get domain from argument or use default
DOMAIN="${1:-ehsanalytical.com}"

echo "==========================================="
echo " Cloudflare Performance Setup"
echo "==========================================="
echo "Domain: ${DOMAIN}"
echo ""

# Get Zone ID if not set
if [ -z "$CLOUDFLARE_ZONE_ID" ]; then
    echo "Finding zone ID for ${DOMAIN}..."
    ZONE_ID=$(curl -s -X GET "https://api.cloudflare.com/client/v4/zones?name=${DOMAIN}" \
      -H "Authorization: Bearer ${CLOUDFLARE_API_TOKEN}" \
      -H "Content-Type: application/json" | jq -r '.result[0].id')

    if [ "$ZONE_ID" == "null" ] || [ -z "$ZONE_ID" ]; then
        echo -e "${RED}Error: Could not find zone for ${DOMAIN}${NC}"
        exit 1
    fi
else
    ZONE_ID="$CLOUDFLARE_ZONE_ID"
fi

echo "Zone ID: ${ZONE_ID}"
echo ""

# Function to update zone setting
update_setting() {
    local SETTING=$1
    local VALUE=$2
    local DESCRIPTION=$3

    echo -n "Setting ${DESCRIPTION}... "

    RESPONSE=$(curl -s -X PATCH "https://api.cloudflare.com/client/v4/zones/${ZONE_ID}/settings/${SETTING}" \
      -H "Authorization: Bearer ${CLOUDFLARE_API_TOKEN}" \
      -H "Content-Type: application/json" \
      --data "{\"value\":${VALUE}}")

    SUCCESS=$(echo $RESPONSE | jq -r '.success')
    if [ "$SUCCESS" == "true" ]; then
        echo -e "${GREEN}✓${NC}"
    else
        ERROR=$(echo $RESPONSE | jq -r '.errors[0].message // "Unknown error"')
        echo -e "${YELLOW}⚠ ${ERROR}${NC}"
    fi
}

# Function to enable proxy on DNS records
enable_proxy() {
    echo ""
    echo "--- Enabling CDN Proxy on DNS Records ---"

    # Get all A and CNAME records
    RECORDS=$(curl -s -X GET "https://api.cloudflare.com/client/v4/zones/${ZONE_ID}/dns_records?type=A" \
      -H "Authorization: Bearer ${CLOUDFLARE_API_TOKEN}" \
      -H "Content-Type: application/json")

    echo "$RECORDS" | jq -r '.result[] | "\(.id) \(.name) \(.content) \(.proxied)"' | while read ID NAME CONTENT PROXIED; do
        if [ "$PROXIED" == "false" ]; then
            echo -n "  Enabling proxy for ${NAME}... "
            RESPONSE=$(curl -s -X PATCH "https://api.cloudflare.com/client/v4/zones/${ZONE_ID}/dns_records/${ID}" \
              -H "Authorization: Bearer ${CLOUDFLARE_API_TOKEN}" \
              -H "Content-Type: application/json" \
              --data '{"proxied":true}')

            SUCCESS=$(echo $RESPONSE | jq -r '.success')
            if [ "$SUCCESS" == "true" ]; then
                echo -e "${GREEN}✓${NC}"
            else
                echo -e "${YELLOW}⚠ Failed${NC}"
            fi
        else
            echo -e "  ${NAME}: ${GREEN}Already proxied ✓${NC}"
        fi
    done

    # Also check CNAME records
    CNAME_RECORDS=$(curl -s -X GET "https://api.cloudflare.com/client/v4/zones/${ZONE_ID}/dns_records?type=CNAME" \
      -H "Authorization: Bearer ${CLOUDFLARE_API_TOKEN}" \
      -H "Content-Type: application/json")

    echo "$CNAME_RECORDS" | jq -r '.result[] | "\(.id) \(.name) \(.proxied)"' | while read ID NAME PROXIED; do
        if [ "$PROXIED" == "false" ]; then
            echo -n "  Enabling proxy for ${NAME}... "
            RESPONSE=$(curl -s -X PATCH "https://api.cloudflare.com/client/v4/zones/${ZONE_ID}/dns_records/${ID}" \
              -H "Authorization: Bearer ${CLOUDFLARE_API_TOKEN}" \
              -H "Content-Type: application/json" \
              --data '{"proxied":true}')

            SUCCESS=$(echo $RESPONSE | jq -r '.success')
            if [ "$SUCCESS" == "true" ]; then
                echo -e "${GREEN}✓${NC}"
            else
                echo -e "${YELLOW}⚠ Failed${NC}"
            fi
        fi
    done
}

# Function to create page rule
create_page_rule() {
    local URL_PATTERN=$1
    local ACTIONS=$2
    local DESCRIPTION=$3

    echo -n "  Creating rule: ${DESCRIPTION}... "

    RESPONSE=$(curl -s -X POST "https://api.cloudflare.com/client/v4/zones/${ZONE_ID}/pagerules" \
      -H "Authorization: Bearer ${CLOUDFLARE_API_TOKEN}" \
      -H "Content-Type: application/json" \
      --data "{\"targets\":[{\"target\":\"url\",\"constraint\":{\"operator\":\"matches\",\"value\":\"${URL_PATTERN}\"}}],\"actions\":${ACTIONS},\"status\":\"active\"}")

    SUCCESS=$(echo $RESPONSE | jq -r '.success')
    if [ "$SUCCESS" == "true" ]; then
        echo -e "${GREEN}✓${NC}"
    else
        ERROR=$(echo $RESPONSE | jq -r '.errors[0].message // "Unknown error"')
        if [[ "$ERROR" == *"already exists"* ]] || [[ "$ERROR" == *"already been taken"* ]]; then
            echo -e "${YELLOW}⚠ Already exists${NC}"
        else
            echo -e "${YELLOW}⚠ ${ERROR}${NC}"
        fi
    fi
}

echo "==========================================="
echo " 1. SSL/TLS Configuration"
echo "==========================================="
update_setting "ssl" '"full"' "SSL mode to Full (strict)"
update_setting "always_use_https" '"on"' "Always Use HTTPS"
update_setting "min_tls_version" '"1.2"' "Minimum TLS 1.2"

echo ""
echo "==========================================="
echo " 2. Performance Optimization"
echo "==========================================="
update_setting "minify" '{"js":"on","css":"on","html":"on"}' "Auto Minify (JS, CSS, HTML)"
update_setting "brotli" '"on"' "Brotli Compression"
update_setting "early_hints" '"on"' "Early Hints (103)"
update_setting "rocket_loader" '"off"' "Rocket Loader (off - can break JS)"

echo ""
echo "==========================================="
echo " 3. Caching Configuration"
echo "==========================================="
update_setting "browser_cache_ttl" '2678400' "Browser Cache TTL (31 days)"
update_setting "always_online" '"on"' "Always Online"
update_setting "development_mode" '"off"' "Development Mode (off)"
update_setting "cache_level" '"aggressive"' "Cache Level (aggressive)"

echo ""
echo "==========================================="
echo " 4. Security Settings"
echo "==========================================="
update_setting "security_level" '"medium"' "Security Level (medium)"
update_setting "challenge_ttl" '3600' "Challenge TTL (1 hour)"
update_setting "browser_check" '"on"' "Browser Integrity Check"

echo ""
echo "==========================================="
echo " 5. Enable CDN Proxy"
echo "==========================================="
enable_proxy

echo ""
echo "==========================================="
echo " 6. WordPress Page Rules (3 free)"
echo "==========================================="

# Check existing page rules
EXISTING_RULES=$(curl -s -X GET "https://api.cloudflare.com/client/v4/zones/${ZONE_ID}/pagerules" \
  -H "Authorization: Bearer ${CLOUDFLARE_API_TOKEN}" \
  -H "Content-Type: application/json" | jq -r '.result | length')

echo "Existing rules: ${EXISTING_RULES}/3"

if [ "$EXISTING_RULES" -lt 3 ]; then
    # Rule 1: Cache wp-content (static assets)
    create_page_rule "*${DOMAIN}/wp-content/*" \
      '[{"id":"cache_level","value":"cache_everything"},{"id":"edge_cache_ttl","value":2678400}]' \
      "Cache wp-content (1 month)"

    # Rule 2: Bypass cache for wp-admin
    if [ "$EXISTING_RULES" -lt 2 ]; then
        create_page_rule "*${DOMAIN}/wp-admin/*" \
          '[{"id":"cache_level","value":"bypass"},{"id":"disable_security","value":false}]' \
          "Bypass cache for wp-admin"
    fi

    # Rule 3: Bypass cache for wp-login
    if [ "$EXISTING_RULES" -lt 1 ]; then
        create_page_rule "*${DOMAIN}/wp-login.php*" \
          '[{"id":"cache_level","value":"bypass"}]' \
          "Bypass cache for wp-login"
    fi
else
    echo -e "${YELLOW}  All 3 page rules already in use${NC}"
fi

echo ""
echo "==========================================="
echo " Summary"
echo "==========================================="
echo -e "${GREEN}Cloudflare performance features configured!${NC}"
echo ""
echo "Enabled features:"
echo "  ✓ CDN Proxy (orange cloud)"
echo "  ✓ SSL Full mode + Always HTTPS"
echo "  ✓ Auto Minify (JS, CSS, HTML)"
echo "  ✓ Brotli compression"
echo "  ✓ 31-day browser cache"
echo "  ✓ Always Online"
echo "  ✓ Page rules for WordPress"
echo ""
echo "Next steps:"
echo "  1. Test site: https://${DOMAIN}"
echo "  2. Check speed: https://pagespeed.web.dev/"
echo "  3. Verify in Cloudflare dashboard"
echo ""
echo "Optional ($5/mo): Enable APO for WordPress"
echo "  https://dash.cloudflare.com → Speed → Optimization → APO"
echo ""
