#!/bin/bash
#
# Setup Cloudflare DNS for DigitalOcean migration server
# Usage: ./setup-cloudflare-dns.sh DOMAIN [SUBDOMAIN]
#
# Examples:
#   ./setup-cloudflare-dns.sh ehsanalytical.com          # Setup @ and www
#   ./setup-cloudflare-dns.sh ehsanalytical.com test     # Setup test.ehsanalytical.com
#

set -e

if [ -z "$1" ]; then
    echo "Usage: $0 DOMAIN [SUBDOMAIN]"
    echo "Example: $0 ehsanalytical.com test"
    exit 1
fi

DOMAIN=$1
SUBDOMAIN=$2

# Load Cloudflare credentials
if [ -f ../.env ]; then
    source ../.env
else
    echo "Error: .env file not found"
    echo "Add CLOUDFLARE_API_TOKEN to .env file"
    exit 1
fi

# Load server IP
if [ -f ../.env.migration-server ]; then
    source ../.env.migration-server
else
    echo "Error: .env.migration-server not found"
    exit 1
fi

if [ -z "$CLOUDFLARE_API_TOKEN" ]; then
    echo "Error: CLOUDFLARE_API_TOKEN not set in .env"
    exit 1
fi

SERVER_IP="${MIGRATION_SERVER_IP}"

echo "=== Cloudflare DNS Setup ==="
echo "Domain: ${DOMAIN}"
echo "Server IP: ${SERVER_IP}"

# Get Zone ID
echo ""
echo "Finding zone ID for ${DOMAIN}..."
ZONE_ID=$(curl -s -X GET "https://api.cloudflare.com/client/v4/zones?name=${DOMAIN}" \
  -H "Authorization: Bearer ${CLOUDFLARE_API_TOKEN}" \
  -H "Content-Type: application/json" | \
  jq -r '.result[0].id')

if [ "$ZONE_ID" == "null" ] || [ -z "$ZONE_ID" ]; then
    echo "Error: Could not find zone for ${DOMAIN}"
    echo "Make sure the domain is in your Cloudflare account"
    exit 1
fi

echo "Zone ID: ${ZONE_ID}"

# Function to create/update DNS record
update_dns_record() {
    local NAME=$1
    local TYPE=$2
    local CONTENT=$3

    echo ""
    echo "Setting up ${NAME} → ${CONTENT}"

    # Check if record exists
    RECORD_ID=$(curl -s -X GET "https://api.cloudflare.com/client/v4/zones/${ZONE_ID}/dns_records?name=${NAME}&type=${TYPE}" \
      -H "Authorization: Bearer ${CLOUDFLARE_API_TOKEN}" \
      -H "Content-Type: application/json" | \
      jq -r '.result[0].id')

    if [ "$RECORD_ID" != "null" ] && [ -n "$RECORD_ID" ]; then
        # Update existing record
        echo "Updating existing record..."
        RESPONSE=$(curl -s -X PUT "https://api.cloudflare.com/client/v4/zones/${ZONE_ID}/dns_records/${RECORD_ID}" \
          -H "Authorization: Bearer ${CLOUDFLARE_API_TOKEN}" \
          -H "Content-Type: application/json" \
          --data "{\"type\":\"${TYPE}\",\"name\":\"${NAME}\",\"content\":\"${CONTENT}\",\"ttl\":300,\"proxied\":false}")
    else
        # Create new record
        echo "Creating new record..."
        RESPONSE=$(curl -s -X POST "https://api.cloudflare.com/client/v4/zones/${ZONE_ID}/dns_records" \
          -H "Authorization: Bearer ${CLOUDFLARE_API_TOKEN}" \
          -H "Content-Type: application/json" \
          --data "{\"type\":\"${TYPE}\",\"name\":\"${NAME}\",\"content\":\"${CONTENT}\",\"ttl\":300,\"proxied\":false}")
    fi

    SUCCESS=$(echo $RESPONSE | jq -r '.success')
    if [ "$SUCCESS" == "true" ]; then
        echo "✓ DNS record configured: ${NAME}"
    else
        echo "✗ Failed to configure DNS record"
        echo $RESPONSE | jq -r '.errors[]'
    fi
}

# Configure DNS records
if [ -n "$SUBDOMAIN" ]; then
    # Subdomain only
    FULL_DOMAIN="${SUBDOMAIN}.${DOMAIN}"
    update_dns_record "${FULL_DOMAIN}" "A" "${SERVER_IP}"
else
    # Root domain and www
    update_dns_record "${DOMAIN}" "A" "${SERVER_IP}"
    update_dns_record "www.${DOMAIN}" "CNAME" "${DOMAIN}"
fi

echo ""
echo "=== DNS Configuration Complete ==="
echo ""
echo "DNS Records:"
if [ -n "$SUBDOMAIN" ]; then
    echo "  ${SUBDOMAIN}.${DOMAIN} → ${SERVER_IP}"
else
    echo "  ${DOMAIN} → ${SERVER_IP}"
    echo "  www.${DOMAIN} → ${DOMAIN}"
fi
echo ""
echo "Note: DNS may take a few minutes to propagate"
echo ""
echo "Check propagation:"
if [ -n "$SUBDOMAIN" ]; then
    echo "  dig ${SUBDOMAIN}.${DOMAIN} +short"
    echo ""
    echo "Next step:"
    echo "  ./3-setup-ssl.sh ${SUBDOMAIN}.${DOMAIN} ${CLOUDFLARE_EMAIL:-enoteware@gmail.com}"
else
    echo "  dig ${DOMAIN} +short"
    echo ""
    echo "Next step:"
    echo "  ./3-setup-ssl.sh ${DOMAIN} ${CLOUDFLARE_EMAIL:-enoteware@gmail.com}"
fi
