# WordPress Migration Scripts
## Nexcess → DigitalOcean

Automated scripts for migrating 24 WordPress sites from Nexcess to DigitalOcean VPS.

## Prerequisites

- ✓ DigitalOcean server running (IP: 134.199.221.85)
- ✓ LEMP stack installed (Nginx, MySQL, PHP 8.1, Redis)
- ✓ WP-CLI installed on both servers
- ✓ SSH access to both Nexcess and DigitalOcean
- ✓ Firewall configured (UFW)
- ✓ Fail2Ban active

## Quick Start

### One-Command Migration

```bash
cd migration-scripts
chmod +x *.sh
./quick-migrate.sh DOMAIN.com [email@example.com]
```

### Step-by-Step Migration

**From Nexcess:**
```bash
# 1. Export from Nexcess
./1-export-from-nexcess.sh DOMAIN.com

# 2. Import to DigitalOcean
./2-import-to-digitalocean.sh DOMAIN.com

# 3. Update DNS (point A record to 134.199.221.85)

# 4. Setup SSL (after DNS propagates)
./3-setup-ssl.sh DOMAIN.com email@example.com
```

**From Local DDEV:**
```bash
# Migrate local DDEV site to DigitalOcean
./import-ddev-to-digitalocean.sh DDEV_PROJECT_NAME DOMAIN [DDEV_PATH]

# Example:
./import-ddev-to-digitalocean.sh ehs-mini dev.ehsanalytical.com
./import-ddev-to-digitalocean.sh ehs-mini dev.ehsanalytical.com ../ehs-wordpress-local
```

**Quick Deploy (for iterative development):**
```bash
# Theme only - fastest (~5 seconds)
./quick-deploy-to-do.sh

# All wp-content files (~1-2 minutes)
./quick-deploy-to-do.sh --full

# Database only
./quick-deploy-to-do.sh --db

# Everything (files + database)
./quick-deploy-to-do.sh --all
```

**SSH Key Setup Helper:**
```bash
# Check if SSH key is configured and get setup instructions
./add-ssh-key-to-server.sh
```

## Migration Process

### Phase 1: Export from Nexcess

The export script:
- Connects to Nexcess via SSH
- Finds WordPress installation directory
- Exports database using WP-CLI
- Creates compressed archive of files (excludes cache)
- Downloads to local `backups/DOMAIN/` directory

### Phase 2: Import to DigitalOcean

The import script:
- Uploads backup files to DO server
- Creates new database with unique credentials
- Extracts WordPress files to `/var/www/DOMAIN/html`
- Updates wp-config.php with new database credentials
- Creates Nginx configuration
- Updates site URLs for testing
- Sets proper permissions

### Phase 3: SSL Setup

The SSL script:
- Checks DNS is pointing to new server
- Installs Let's Encrypt SSL certificate
- Configures Nginx for HTTPS
- Updates WordPress URLs to HTTPS
- Forces SSL in wp-config.php

### DDEV to DigitalOcean Migration

The `import-ddev-to-digitalocean.sh` script:
- Exports database from local DDEV project
- Creates wp-content archive from DDEV
- Uploads files to DigitalOcean server
- Backs up existing site on server
- Imports database and wp-content
- Updates URLs from DDEV domain to production domain
- Sets proper file permissions
- Clears all caches (WordPress, Elementor, Nginx)
- Automatically detects WordPress path on server

**Requirements:**
- DDEV project must be running
- WordPress site must already exist on DigitalOcean server
- Database credentials must be configured in wp-config.php

## File Structure

```
migration-scripts/
├── 1-export-from-nexcess.sh      # Export site from Nexcess
├── 2-import-to-digitalocean.sh   # Import to DO server
├── 3-setup-ssl.sh                # Setup SSL certificate
├── import-ddev-to-digitalocean.sh # Import from local DDEV
├── quick-deploy-to-do.sh         # Fast iterative deploys to DO
├── quick-migrate.sh              # All-in-one migration
├── setup-cloudflare-dns.sh       # Automate Cloudflare DNS setup
├── list-nexcess-sites.sh         # List all sites on Nexcess
└── backups/                      # Backup storage
    └── DOMAIN.com/
        ├── DOMAIN_YYYYMMDD_HHMMSS.sql.gz
        └── DOMAIN_files_YYYYMMDD_HHMMSS.tar.gz
```

## Configuration

**Important:** See [DigitalOcean SSH Setup Guide](../docs/DIGITALOCEAN_SSH_SETUP.md) for SSH key configuration.

Required environment files in parent directory:

**`.env`** - Nexcess credentials
```bash
SSH_HOSTNAME=832f87585d.nxcli.net
SSH_USERNAME=a96c427e_1
SSH_PASSWORD=<password>
```

**`.env.migration-server`** - DigitalOcean server info

See `docs/.env.migration-server.example` for a template. Required variables:

```bash
MIGRATION_SERVER_IP=134.199.221.85
MIGRATION_SERVER_REGION=sfo3
MIGRATION_SERVER_SIZE=s-2vcpu-4gb
MIGRATION_SSH_USER=root
MIGRATION_SSH_KEY=~/.ssh/id_ed25519_do  # Must use DigitalOcean key
MYSQL_ROOT_PASSWORD=your-secure-password
```

**Critical:** The `MIGRATION_SSH_KEY` must be set to `~/.ssh/id_ed25519_do` (the DigitalOcean-specific key). This key must be authorized on the server.

**Quick SSH Setup:**
1. See [DigitalOcean SSH Setup Guide](../docs/DIGITALOCEAN_SSH_SETUP.md) for complete instructions
2. Quick test: `ssh -i ~/.ssh/id_ed25519_do root@134.199.221.85`

## Site Prioritization

From `nexcess-migration-plan.md`:

### High Priority (Migrate First)
- ehsanalytical.com - Main EHS site
- notewaredigital.com - Agency site

### Medium Priority
- pickproslv.com
- japan.cultureroute.com

### Review Before Migrating
- All other sites (check with client first)

## Testing Checklist

After migration, verify:

- [ ] Homepage loads correctly
- [ ] Admin panel accessible
- [ ] Permalinks working
- [ ] Images loading
- [ ] Forms submitting
- [ ] Plugins active
- [ ] Theme displaying correctly
- [ ] SSL certificate valid
- [ ] Email functionality
- [ ] Contact forms
- [ ] Search functionality
- [ ] Mobile responsiveness

## DNS Update Process

1. **Before SSL Setup:**
   ```bash
   # Check current DNS
   dig DOMAIN.com @8.8.8.8

   # Update A record to: 134.199.221.85
   # Update CNAME for www to: @
   ```

2. **Wait for propagation:**
   ```bash
   # Check propagation
   dig DOMAIN.com @8.8.8.8

   # Should return: 134.199.221.85
   ```

3. **Run SSL setup:**
   ```bash
   ./3-setup-ssl.sh DOMAIN.com email@example.com
   ```

## Troubleshooting

### Database Import Fails

```bash
# SSH into DO server
ssh root@134.199.221.85

# Manually import
gunzip < /tmp/DOMAIN.sql.gz | mysql -u root DATABASE_NAME
```

### Nginx Config Errors

```bash
# Test config
ssh root@134.199.221.85 'nginx -t'

# View error log
ssh root@134.199.221.85 'tail -50 /var/log/nginx/DOMAIN_error.log'
```

### SSL Certificate Fails

```bash
# Ensure DNS is pointing to server
dig DOMAIN.com @8.8.8.8

# Manually request cert
ssh root@134.199.221.85
certbot --nginx -d DOMAIN.com -d www.DOMAIN.com
```

### Site Shows Old Content

```bash
# Clear WordPress cache
ssh root@134.199.221.85
cd /var/www/DOMAIN.com/html
wp cache flush --allow-root

# Clear Nginx cache
systemctl reload nginx
```

## Cost Savings

- **Current:** $328/month (Nexcess - 24 sites)
- **Target:** $48/month (DigitalOcean - 8GB droplet)
- **Savings:** $280/month ($3,360/year)

## Server Resources

**Current Usage (4GB droplet):**
- Can handle: 10-15 sites
- Upgrade to 8GB when needed

**8GB Droplet (final target):**
- Can handle: 20-30 sites comfortably
- Plenty of room for growth

## Support

- DigitalOcean server: 134.199.221.85
- SSH: `ssh root@134.199.221.85`
- Server location: San Francisco 3 (sfo3)
- Monitoring: DigitalOcean dashboard
