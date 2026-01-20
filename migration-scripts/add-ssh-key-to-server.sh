#!/bin/bash
#
# Helper script to add SSH key to DigitalOcean server
# This script provides instructions and can help verify the key is added
#

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Configuration
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

if [ -f "${SCRIPT_DIR}/../.env.migration-server" ]; then
    source "${SCRIPT_DIR}/../.env.migration-server"
else
    echo -e "${RED}Error: .env.migration-server not found${NC}"
    exit 1
fi

SERVER_IP="${MIGRATION_SERVER_IP}"
SSH_USER="${MIGRATION_SSH_USER:-root}"
SSH_KEY="${MIGRATION_SSH_KEY:-~/.ssh/id_ed25519_do}"

# Expand tilde
SSH_KEY="${SSH_KEY/#\~/$HOME}"

echo -e "${GREEN}╔═══════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║  Add SSH Key to DigitalOcean Server               ║${NC}"
echo -e "${GREEN}╚═══════════════════════════════════════════════════╝${NC}"
echo ""
echo "Server: ${SERVER_IP}"
echo "SSH Key: ${SSH_KEY}"
echo ""

# Check if key exists
if [ ! -f "$SSH_KEY" ]; then
    echo -e "${RED}Error: SSH key not found: $SSH_KEY${NC}"
    exit 1
fi

# Display public key
echo -e "${YELLOW}Your public key to add:${NC}"
echo ""
cat "${SSH_KEY}.pub"
echo ""
echo ""

# Test current connection
echo -e "${YELLOW}Testing current SSH connection...${NC}"
if ssh -i "$SSH_KEY" -o ConnectTimeout=5 -o StrictHostKeyChecking=no "${SSH_USER}@${SERVER_IP}" "echo 'Connection successful'" 2>/dev/null; then
    echo -e "${GREEN}✓ SSH key is already working!${NC}"
    exit 0
else
    echo -e "${RED}✗ SSH key is not authorized on the server${NC}"
    echo ""
fi

echo -e "${YELLOW}To add this key to the server, choose one method:${NC}"
echo ""
echo -e "${GREEN}Method 1: Via DigitalOcean Console (Recommended)${NC}"
echo "1. Go to: https://cloud.digitalocean.com/droplets/544676631"
echo "2. Click 'Settings' → 'SSH Keys'"
echo "3. Ensure 'elliot-macbook-do' (ID: 53407380) is enabled"
echo "4. The key will be automatically added to the server"
echo ""
echo -e "${GREEN}Method 2: Manual Addition (if you have another way in)${NC}"
echo "If you can access the server via password or another key:"
echo ""
echo "  ssh ${SSH_USER}@${SERVER_IP}"
echo ""
echo "Then on the server, run:"
echo ""
echo "  mkdir -p ~/.ssh"
echo "  echo '$(cat "${SSH_KEY}.pub")' >> ~/.ssh/authorized_keys"
echo "  chmod 600 ~/.ssh/authorized_keys"
echo "  chmod 700 ~/.ssh"
echo ""
echo -e "${YELLOW}After adding the key, run this script again to verify.${NC}"
echo ""
