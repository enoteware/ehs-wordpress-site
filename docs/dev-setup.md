# Development Setup

## Prerequisites

### Required Software

- **Docker Desktop** - For DDEV containers
- **DDEV** v1.24.10+ - Local WordPress development
- **Node.js** - For task management scripts
- **Git** - Version control
- **SSH client** - For production server access

### Optional Tools

- **sshpass** - For automated SSH connections
- **dnsmasq** - For wildcard DNS on MacBook

## Environment Variables

Create `.env` file in repository root (gitignored):

```bash
# Production SSH Access
SSH_HOSTNAME=832f87585d.nxcli.net
SSH_PORT=22
SSH_USERNAME=a96c427e_1
SSH_PASSWORD=<password>

# API Keys
PEXELS_API_KEY=<key>
AGENT_API_KEY=<key>
AGENT_USER_ID=<uuid>
AGENT_BASE_URL=https://app.noteware.dev/api/agent
```

## Local Development Setup

### 1. Clone Repository

```bash
git clone <repository-url>
cd ehs
```

### 2. Start DDEV Environment

```bash
cd ehs-wordpress-local
ddev start
```

**First-time setup:**
- DDEV will download WordPress if needed
- Creates database container
- Sets up Nginx configuration
- Generates SSL certificates

### 3. Import Database (if needed)

```bash
# If you have a database backup
ddev import-db --file=/path/to/production-database.sql.gz

# Activate required plugins and theme
ddev exec "wp plugin activate elementor elementor-pro advanced-custom-fields-pro wordpress-seo jet-elements jet-menu jet-tabs menu-icons code-snippets --path=/var/www/html/wordpress"
ddev exec "wp theme activate hello-elementor-child --path=/var/www/html/wordpress"
ddev exec "wp cache flush --path=/var/www/html/wordpress"
```

### 4. Restore Uploads (if needed)

```bash
cd /Volumes/nvme_ext_data/code/ehs
tar -xzf uploads-backup.tar.gz -C ehs-wordpress-local/wordpress/wp-content/
```

### 5. Access Local Site

- **Site:** http://ehs-mini.ddev.site
- **Admin:** http://ehs-mini.ddev.site/wp-admin
- **Credentials:** Defined in `.ddev/config.yaml`

## Mac Mini Remote Access Setup

### From MacBook

1. **Add to `/etc/hosts`:**
   ```bash
   sudo nano /etc/hosts
   # Add: 10.112.1.56 ehs-mini.ddev.site
   ```

2. **Access site:**
   - Site: http://ehs-mini.ddev.site
   - Admin: http://ehs-mini.ddev.site/wp-admin

3. **Optional - Wildcard DNS:**
   ```bash
   # Install dnsmasq
   brew install dnsmasq
   
   # Configure for *.ddev.site
   echo 'address=/.ddev.site/10.112.1.56' >> /usr/local/etc/dnsmasq.conf
   ```

## Node.js Scripts Setup

### Install Dependencies (if needed)

```bash
# Check if package.json exists
ls package.json

# If dependencies needed, install:
npm install
```

### Run Task Management Scripts

```bash
# Create task checklists
node create-task-checklists.js

# Add checklists to tasks
node add-checklists.js

# Update tasks
node update-tasks.js

# Review tasks
node review-tasks.js
```

## Common Commands

### DDEV Commands

```bash
ddev start              # Start containers
ddev stop               # Stop containers
ddev restart            # Restart containers
ddev ssh                # SSH into web container
ddev logs               # View logs
ddev exec wp [command]  # Run WP-CLI commands
```

### WordPress CLI

```bash
# From within container (ddev ssh) or prefixed with ddev exec
wp plugin list
wp theme list
wp post list --post_type=services
wp cache flush
```

### Elementor CSS Regeneration

```bash
cd ehs-wordpress-local
./regen-css.sh
```

### Template Synchronization

```bash
cd ehs-wordpress-local
./sync-elementor-templates.sh
```

## Troubleshooting

### Docker Desktop Not Running

```bash
open -a Docker
# Wait 30-60 seconds
docker info  # Verify
```

### Database Import Issues

1. Check file type:
   ```bash
   file production-database.sql
   ```

2. If gzipped but named `.sql`:
   ```bash
   mv production-database.sql production-database.sql.gz
   ddev import-db --file=production-database.sql.gz
   ```

3. If site shows blank after import:
   ```bash
   ddev exec "wp plugin activate elementor elementor-pro --path=/var/www/html/wordpress"
   ddev exec "wp theme activate hello-elementor-child --path=/var/www/html/wordpress"
   ddev exec "wp cache flush --path=/var/www/html/wordpress"
   ```

### Cannot Access from MacBook

1. Verify Mac mini IP:
   ```bash
   ifconfig | grep "inet " | grep -v 127.0.0.1
   ```

2. Test connectivity:
   ```bash
   ping 10.112.1.56
   curl -I http://10.112.1.56
   ```

### Site Shows Cached Content

```bash
ddev exec "wp cache flush --path=/var/www/html/wordpress"
ddev exec "wp eval-file /var/www/html/regen-elementor-css.php --path=/var/www/html/wordpress"
# Hard refresh browser: Cmd+Shift+R
```

## Production Server Access

### SSH Connection

```bash
ssh a96c427e_1@832f87585d.nxcli.net
cd /home/a96c427e/832f87585d.nxcli.net/html
```

### WP-CLI on Production

```bash
ssh a96c427e_1@832f87585d.nxcli.net "cd /home/a96c427e/832f87585d.nxcli.net/html && wp [command] --allow-root"
```

## Next Steps

- Review [Architecture Overview](architecture.md)
- Check [Workflows](workflows.md) for common tasks
- See [CLAUDE.md](../CLAUDE.md) for detailed guidance
