#!/bin/bash

# Nexcess to DigitalOcean Migration Script
# This script helps migrate WordPress sites from Nexcess to DigitalOcean + aaPanel

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
NEXCESS_SSH="a96c427e_1@832f87585d.nxcli.net"
NEXCESS_WP_PATH="/home/a96c427e/832f87585d.nxcli.net/html"
DO_SERVER_IP=""  # Set this after creating droplet
DO_SSH_USER="root"

# Site list (update with actual sites to migrate)
SITES=(
    "ehsanalytical.com"
    "notewaredigital.com"
    # Add more sites here
)

echo -e "${GREEN}=== Nexcess to DigitalOcean Migration Script ===${NC}\n"

# Function to check if site exists on Nexcess
check_site_exists() {
    local domain=$1
    echo -e "${YELLOW}Checking if $domain exists on Nexcess...${NC}"
    
    ssh $NEXCESS_SSH "test -d $NEXCESS_WP_PATH/$domain && echo 'EXISTS' || echo 'NOT_FOUND'"
}

# Function to export database
export_database() {
    local domain=$1
    local db_name=$(ssh $NEXCESS_SSH "cd $NEXCESS_WP_PATH/$domain && wp config get DB_NAME --allow-root 2>/dev/null || echo ''")
    
    if [ -z "$db_name" ]; then
        echo -e "${RED}Could not find database name for $domain${NC}"
        return 1
    fi
    
    echo -e "${YELLOW}Exporting database for $domain...${NC}"
    ssh $NEXCESS_SSH "cd $NEXCESS_WP_PATH/$domain && wp db export /tmp/${domain}_backup.sql --allow-root"
    
    # Download the backup
    scp $NEXCESS_SSH:/tmp/${domain}_backup.sql ./backups/${domain}_backup.sql
    
    echo -e "${GREEN}Database exported to ./backups/${domain}_backup.sql${NC}"
}

# Function to create site archive
create_site_archive() {
    local domain=$1
    echo -e "${YELLOW}Creating archive for $domain...${NC}"
    
    ssh $NEXCESS_SSH "cd $NEXCESS_WP_PATH && tar -czf /tmp/${domain}.tar.gz $domain/"
    
    # Download the archive
    scp $NEXCESS_SSH:/tmp/${domain}.tar.gz ./backups/${domain}.tar.gz
    
    echo -e "${GREEN}Archive created: ./backups/${domain}.tar.gz${NC}"
}

# Function to migrate single site
migrate_site() {
    local domain=$1
    
    echo -e "\n${GREEN}=== Migrating $domain ===${NC}\n"
    
    # Create backup directory
    mkdir -p ./backups
    
    # Check if site exists
    if ! check_site_exists $domain | grep -q "EXISTS"; then
        echo -e "${RED}Site $domain not found on Nexcess. Skipping...${NC}"
        return 1
    fi
    
    # Export database
    export_database $domain
    
    # Create site archive
    create_site_archive $domain
    
    echo -e "${GREEN}Migration data prepared for $domain${NC}"
    echo -e "${YELLOW}Next steps:${NC}"
    echo -e "  1. Create site on DigitalOcean server via aaPanel"
    echo -e "  2. Upload ./backups/${domain}.tar.gz to new server"
    echo -e "  3. Import database from ./backups/${domain}_backup.sql"
    echo -e "  4. Update wp-config.php with new database credentials"
    echo -e "  5. Update DNS to point to new server"
}

# Main execution
main() {
    if [ -z "$1" ]; then
        echo -e "${YELLOW}Usage: $0 <domain>${NC}"
        echo -e "${YELLOW}Or: $0 all (to migrate all sites in SITES array)${NC}"
        exit 1
    fi
    
    if [ "$1" == "all" ]; then
        for site in "${SITES[@]}"; do
            migrate_site "$site"
        done
    else
        migrate_site "$1"
    fi
}

main "$@"
