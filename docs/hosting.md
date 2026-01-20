# Hosting Infrastructure

## Overview

This project uses multiple hosting environments:

| Environment | Provider | Purpose | Status |
|-------------|----------|---------|--------|
| **Production** | DigitalOcean | Live sites | Active |
| **Legacy** | Nexcess | Migrating from | Being decommissioned |
| **Local Dev** | DDEV | Development | Active |

---

## DigitalOcean (Production)

### Server Details

| Setting | Value |
|---------|-------|
| **IP Address** | `134.199.221.85` |
| **Region** | San Francisco 3 (sfo3) |
| **Droplet Size** | s-2vcpu-4gb |
| **vCPU** | 2 cores |
| **RAM** | 4GB |
| **Monthly Cost** | $24/month |

### SSH Access

**SSH Key Setup:**

The DigitalOcean server uses key-based authentication. The project uses a dedicated SSH key for DigitalOcean deployments.

**Required SSH Key:**
- **Location:** `~/.ssh/id_ed25519_do`
- **Public Key:** `~/.ssh/id_ed25519_do.pub`
- **Configuration:** Set in `.env.migration-server` as `MIGRATION_SSH_KEY=~/.ssh/id_ed25519_do`

**Connecting to Server:**

```bash
# Using the DigitalOcean key
ssh -i ~/.ssh/id_ed25519_do root@134.199.221.85

# Or add to ~/.ssh/config for easier access:
# Host ehs-do
#   HostName 134.199.221.85
#   User root
#   IdentityFile ~/.ssh/id_ed25519_do
# Then use: ssh ehs-do
```

**Initial Key Setup (if needed):**

If the key isn't already on the server, add it:

```bash
# 1. Copy your public key
cat ~/.ssh/id_ed25519_do.pub

# 2. SSH into server (using password or existing key)
ssh root@134.199.221.85

# 3. Add the key to authorized_keys
mkdir -p ~/.ssh
echo "YOUR_PUBLIC_KEY_HERE" >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
chmod 700 ~/.ssh

# 4. Test connection
exit
ssh -i ~/.ssh/id_ed25519_do root@134.199.221.85
```

### Software Stack

| Software | Version |
|----------|---------|
| **OS** | Ubuntu 22.04 LTS |
| **Web Server** | Nginx 1.18.0 |
| **PHP** | 8.1.2 |
| **Database** | MariaDB 11.4 |
| **Cache** | Redis (running) |
| **WP-CLI** | 2.12.0 |

### WordPress Sites Location

```
/var/www/DOMAIN.com/html/
```

### Common Commands

```bash
# Check server status
ssh root@134.199.221.85 'systemctl status nginx && systemctl status php8.1-fpm && systemctl status mariadb'

# Restart services
ssh root@134.199.221.85 'systemctl restart nginx'
ssh root@134.199.221.85 'systemctl restart php8.1-fpm'

# View logs
ssh root@134.199.221.85 'tail -50 /var/log/nginx/error.log'
ssh root@134.199.221.85 'tail -50 /var/log/nginx/DOMAIN_error.log'

# WordPress CLI
ssh root@134.199.221.85 'cd /var/www/DOMAIN.com/html && wp plugin list --allow-root'
ssh root@134.199.221.85 'cd /var/www/DOMAIN.com/html && wp cache flush --allow-root'
```

### SSL Certificates

SSL is managed via Let's Encrypt with Certbot:

```bash
# Install/renew SSL for a domain
ssh root@134.199.221.85
certbot --nginx -d DOMAIN.com -d www.DOMAIN.com

# Check certificate status
certbot certificates

# Force renewal
certbot renew --force-renewal
```

### Backups

**Database backup:**
```bash
ssh root@134.199.221.85
cd /var/www/DOMAIN.com/html
wp db export /root/backups/DOMAIN_$(date +%Y%m%d).sql --allow-root
```

**Files backup:**
```bash
ssh root@134.199.221.85
tar -czf /root/backups/DOMAIN_files_$(date +%Y%m%d).tar.gz /var/www/DOMAIN.com/html
```

### Environment File

Server credentials stored in `.env.migration-server`:

```bash
MIGRATION_SERVER_IP=134.199.221.85
MIGRATION_SERVER_REGION=sfo3
MIGRATION_SERVER_SIZE=s-2vcpu-4gb
MIGRATION_SSH_USER=root
MYSQL_ROOT_PASSWORD=<in-file>
```

---

## Nexcess (Legacy - Being Migrated)

### Account Details

| Setting | Value |
|---------|-------|
| **Plan** | NDS 50 Site Plan |
| **Monthly Cost** | $328/month |
| **Total Sites** | 24 |
| **SSH Host** | `832f87585d.nxcli.net` |
| **SSH User** | `a96c427e_1` |

### SSH Access

```bash
ssh a96c427e_1@832f87585d.nxcli.net
```

### WordPress Path

```
/home/a96c427e/832f87585d.nxcli.net/html/
```

### Site Inventory

**High Priority (Main Sites):**
- ehsanalytical.com - Main EHS site
- notewaredigital.com - Agency portfolio

**Medium Priority:**
- pickproslv.com
- japan.cultureroute.com

**Low Priority / Review:**
- 20+ additional sites (see full list in `NEXCESS_TO_DIGITALOCEAN_MIGRATION_PLAN.md`)

### Migration Status

Actively migrating to DigitalOcean. See:
- [NEXCESS_TO_DIGITALOCEAN_MIGRATION_PLAN.md](../NEXCESS_TO_DIGITALOCEAN_MIGRATION_PLAN.md)
- [migration-scripts/README.md](../migration-scripts/README.md)

---

## Local Development (DDEV)

### Configuration

| Setting | Value |
|---------|-------|
| **Project Name** | ehs-mini |
| **URL** | http://ehs-mini.ddev.site |
| **Admin URL** | http://ehs-mini.ddev.site/wp-admin |
| **Mac Mini IP** | 10.112.1.56 |

### Stack

| Software | Version |
|----------|---------|
| **DDEV** | v1.24.10 |
| **PHP** | 8.3 |
| **Database** | MariaDB 10.11 |
| **Web Server** | Nginx |
| **Sync** | Mutagen |

### Commands

```bash
cd ehs-wordpress-local

# Start/stop
ddev start
ddev stop
ddev restart

# SSH into container
ddev ssh

# Run WP-CLI (note: --path required)
ddev exec "wp plugin list --path=/var/www/html/wordpress"
ddev exec "wp cache flush --path=/var/www/html/wordpress"

# Import database
ddev import-db --file=/path/to/backup.sql.gz

# Export database
ddev export-db --file=backup.sql.gz
```

### Remote Access (from MacBook)

Add to MacBook's `/etc/hosts`:
```
10.112.1.56 ehs-mini.ddev.site
```

---

## Migration Scripts

Located in `migration-scripts/`:

| Script | Purpose |
|--------|---------|
| `1-export-from-nexcess.sh` | Export site from Nexcess |
| `2-import-to-digitalocean.sh` | Import to DO server |
| `3-setup-ssl.sh` | Setup SSL certificate |
| `import-ddev-to-digitalocean.sh` | Deploy from local DDEV |
| `quick-migrate.sh` | All-in-one migration |
| `list-nexcess-sites.sh` | List all Nexcess sites |

### Quick Migration

```bash
cd migration-scripts

# From Nexcess to DigitalOcean
./quick-migrate.sh DOMAIN.com email@example.com

# From local DDEV to DigitalOcean
./import-ddev-to-digitalocean.sh ehs-mini dev.ehsanalytical.com
```

---

## DNS Management

### Current Registrars

DNS is managed through various registrars depending on the domain. Common pattern:

1. Point A record to DigitalOcean: `134.199.221.85`
2. Set up www CNAME to `@`
3. Wait for propagation (up to 48 hours)
4. Run SSL setup

### Check DNS

```bash
# Check current DNS
dig DOMAIN.com @8.8.8.8

# Check propagation
dig DOMAIN.com @8.8.8.8 +short
# Should return: 134.199.221.85
```

---

## Cost Summary

| Provider | Monthly | Annual | Notes |
|----------|---------|--------|-------|
| Nexcess (current) | $328 | $3,936 | 24 sites, being migrated |
| DigitalOcean (target) | $48 | $576 | After upgrade to 8GB |
| **Savings** | **$280** | **$3,360** | 85% reduction |

---

## Monitoring

### DigitalOcean Dashboard

Access monitoring at: https://cloud.digitalocean.com/droplets

Monitors:
- CPU usage
- Memory usage
- Disk I/O
- Bandwidth

### Manual Health Checks

```bash
# Server resources
ssh root@134.199.221.85 'htop'
ssh root@134.199.221.85 'df -h'
ssh root@134.199.221.85 'free -h'

# Service status
ssh root@134.199.221.85 'systemctl status nginx php8.1-fpm mariadb redis-server'

# Check WordPress site health
ssh root@134.199.221.85 'cd /var/www/DOMAIN.com/html && wp site health --allow-root'
```

---

## Security

### Firewall (UFW)

```bash
# Check status
ssh root@134.199.221.85 'ufw status'

# Allowed ports: 22 (SSH), 80 (HTTP), 443 (HTTPS)
```

### Fail2Ban

Active for brute force protection on SSH and web server.

```bash
# Check status
ssh root@134.199.221.85 'fail2ban-client status'
ssh root@134.199.221.85 'fail2ban-client status sshd'
```

### SSH Security

- Key-based authentication recommended
- Password authentication enabled (consider disabling)
- Default port 22

---

## Troubleshooting

### Site Not Loading

1. Check DNS is pointing to correct IP
2. Check Nginx status: `systemctl status nginx`
3. Check PHP-FPM: `systemctl status php8.1-fpm`
4. Check error logs: `tail -50 /var/log/nginx/DOMAIN_error.log`

### Database Connection Error

1. Check MariaDB status: `systemctl status mariadb`
2. Verify wp-config.php credentials
3. Test connection: `mysql -u USER -p DATABASE`

### SSL Issues

1. Verify DNS is pointing to server
2. Check certificate: `certbot certificates`
3. Renew if needed: `certbot renew`
4. Check Nginx SSL config: `nginx -t`

### Performance Issues

1. Check server resources: `htop`, `free -h`, `df -h`
2. Check for runaway processes
3. Clear caches: `wp cache flush --allow-root`
4. Consider upgrading droplet size

---

## Related Documentation

- [Development Setup](dev-setup.md)
- [Architecture Overview](architecture.md)
- [Migration Plan](../NEXCESS_TO_DIGITALOCEAN_MIGRATION_PLAN.md)
- [Migration Scripts README](../migration-scripts/README.md)
