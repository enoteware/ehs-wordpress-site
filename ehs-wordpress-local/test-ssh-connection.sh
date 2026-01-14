#!/bin/bash

# Test SSH connection to production server
# This script helps diagnose SSH authentication issues

set -e

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}=== SSH Connection Test ===${NC}\n"

# Test SSH config alias
SSH_ALIAS="ehs-prod"
SSH_DIRECT="a96c427e_1@832f87585d.nxcli.net"
WP_PATH="/home/a96c427e/832f87585d.nxcli.net/html"

echo -e "${YELLOW}Testing SSH config alias: $SSH_ALIAS${NC}"
if ssh -o ConnectTimeout=5 -o BatchMode=yes $SSH_ALIAS "echo 'Connection successful'" 2>/dev/null; then
    echo -e "${GREEN}✓ SSH config alias works${NC}"
    USE_ALIAS=true
else
    echo -e "${RED}✗ SSH config alias failed${NC}"
    USE_ALIAS=false
fi

echo ""
echo -e "${YELLOW}Testing direct connection: $SSH_DIRECT${NC}"
if ssh -o ConnectTimeout=5 -o BatchMode=yes $SSH_DIRECT "echo 'Connection successful'" 2>/dev/null; then
    echo -e "${GREEN}✓ Direct connection works${NC}"
    USE_DIRECT=true
else
    echo -e "${RED}✗ Direct connection failed (may require password)${NC}"
    USE_DIRECT=false
fi

echo ""
if [ "$USE_ALIAS" = true ]; then
    echo -e "${GREEN}Testing WP-CLI access via SSH alias...${NC}"
    ssh $SSH_ALIAS "cd $WP_PATH && wp --info" 2>&1 | head -5
elif [ "$USE_DIRECT" = true ]; then
    echo -e "${GREEN}Testing WP-CLI access via direct connection...${NC}"
    ssh $SSH_DIRECT "cd $WP_PATH && wp --info" 2>&1 | head -5
else
    echo -e "${RED}No working SSH connection found${NC}"
    echo -e "${YELLOW}Try connecting manually:${NC}"
    echo -e "  ssh $SSH_ALIAS"
    echo -e "  or"
    echo -e "  ssh $SSH_DIRECT"
    exit 1
fi

echo ""
echo -e "${GREEN}=== Connection Test Complete ===${NC}"
