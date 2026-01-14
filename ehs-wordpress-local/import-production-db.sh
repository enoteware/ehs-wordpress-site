#!/bin/bash
set -e

# Load environment from project root
if [ -f ../.env ]; then
    source ../.env
fi

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

# SSH settings from root .env (SSH_HOSTNAME, SSH_USERNAME, SSH_PASSWORD)
SSH_CONTROL_PATH="$HOME/.ssh/cm-%r@%h:%p"
SSH_OPTS="-o PreferredAuthentications=password -o PubkeyAuthentication=no -o ControlMaster=auto -o ControlPath=$SSH_CONTROL_PATH -o ControlPersist=300"
PROD_SSH_USER="${SSH_USERNAME:-a96c427e_1}"
PROD_SSH_HOST="${SSH_HOSTNAME:-832f87585d.nxcli.net}"
PROD_SSH_PASSWORD="${SSH_PASSWORD}"

echo -e "${RED}🚨 WARNING: This will replace your local database with production data!${NC}"
echo ""
echo -e "${YELLOW}📦 Backing up current local database first...${NC}"
ddev export-db --file=/tmp/local-backup-$(date +%Y%m%d-%H%M%S).sql.gz
echo -e "${GREEN}✅ Backup saved${NC}"
echo ""

echo -e "${YELLOW}📥 Exporting production database...${NC}"

# Use sshpass if password is available in .env
if [ -n "$PROD_SSH_PASSWORD" ] && command -v sshpass &> /dev/null; then
    echo "Using sshpass for authentication"
    sshpass -p "$PROD_SSH_PASSWORD" ssh $SSH_OPTS ${PROD_SSH_USER}@${PROD_SSH_HOST} "cd /home/a96c427e/832f87585d.nxcli.net/html && wp db export - --allow-root" > /tmp/prod-db-$(date +%Y%m%d).sql
else
    echo "You may be prompted for the SSH password"
    ssh $SSH_OPTS ${PROD_SSH_USER}@${PROD_SSH_HOST} "cd /home/a96c427e/832f87585d.nxcli.net/html && wp db export - --allow-root" > /tmp/prod-db-$(date +%Y%m%d).sql
fi

if [ ! -s /tmp/prod-db-$(date +%Y%m%d).sql ]; then
    echo -e "${RED}❌ Database export failed${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Database exported ($(ls -lh /tmp/prod-db-$(date +%Y%m%d).sql | awk '{print $5}'))${NC}"
echo ""

echo -e "${YELLOW}🔧 Fixing table prefix (wpoq_ → wp_)...${NC}"
sed 's/`wpoq_/`wp_/g' /tmp/prod-db-$(date +%Y%m%d).sql > /tmp/prod-db-$(date +%Y%m%d)-fixed.sql
echo ""

echo -e "${YELLOW}📦 Importing to local DDEV...${NC}"
ddev import-db --file=/tmp/prod-db-$(date +%Y%m%d)-fixed.sql
echo ""

echo -e "${YELLOW}🔄 Replacing URLs (prod → local)...${NC}"
ddev exec wp search-replace 'https://ehsanalytical.com' 'https://ehs-local.ddev.site' --all-tables --path=/var/www/html/wordpress
echo ""

echo -e "${YELLOW}🎨 Regenerating Elementor CSS...${NC}"
ddev exec wp elementor flush-css --path=/var/www/html/wordpress
ddev exec wp cache flush --path=/var/www/html/wordpress
echo ""

echo -e "${GREEN}✅ Database import complete!${NC}"
echo ""
echo "📍 Check your local site: https://ehs-local.ddev.site/about-us/"
echo "🔐 Admin: https://ehs-local.ddev.site/wp-admin"
