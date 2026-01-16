# Runbooks - Debugging and Troubleshooting

## Log Locations

### DDEV Logs

```bash
# View all container logs
ddev logs

# View specific service logs
ddev logs -s web
ddev logs -s db
```

### WordPress Debug Log

**Location:** `ehs-wordpress-local/wordpress/wp-content/debug.log`

**View:**
```bash
ddev exec "tail -f /var/www/html/wordpress/wp-content/debug.log"
```

**Enable debug mode:**
Edit `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

### PHP Error Log

**Location:** DDEV container logs

**View:**
```bash
ddev logs -s web | grep -i error
```

### Elementor Logs

**Location:** WordPress debug log (if Elementor debugging enabled)

**Enable:**
```php
define('ELEMENTOR_DEBUG', true);
```

## Common Issues and Solutions

### Issue: Site Shows Blank Page

**Symptoms:**
- White screen
- No content displayed
- Browser shows blank page

**Diagnosis:**
```bash
# Check PHP errors
ddev logs -s web

# Check WordPress debug log
ddev exec "tail -20 /var/www/html/wordpress/wp-content/debug.log"

# Check if plugins/themes are active
ddev exec "wp plugin list --status=active --path=/var/www/html/wordpress"
ddev exec "wp theme list --path=/var/www/html/wordpress"
```

**Solutions:**
1. **Activate required plugins:**
   ```bash
   ddev exec "wp plugin activate elementor elementor-pro --path=/var/www/html/wordpress"
   ```

2. **Activate theme:**
   ```bash
   ddev exec "wp theme activate hello-elementor-child --path=/var/www/html/wordpress"
   ```

3. **Clear caches:**
   ```bash
   ddev exec "wp cache flush --path=/var/www/html/wordpress"
   ```

4. **Check file permissions:**
   ```bash
   ddev exec "ls -la /var/www/html/wordpress/wp-content/themes/hello-elementor-child/"
   ```

### Issue: Elementor Templates Not Displaying

**Symptoms:**
- Templates exist but don't render
- Default WordPress templates showing instead
- Theme Builder templates not assigned

**Diagnosis:**
```bash
# List all Elementor templates
ddev exec "wp post list --post_type=elementor_library --path=/var/www/html/wordpress"

# Check template conditions
ddev exec "wp option get elementor_pro_theme_builder_conditions --path=/var/www/html/wordpress"
```

**Solutions:**
1. **Regenerate CSS:**
   ```bash
   cd ehs-wordpress-local
   ./regen-css.sh
   ```

2. **Reassign templates:**
   ```bash
   ddev exec "wp eval-file assign-theme-builder-templates.php --path=/var/www/html/wordpress"
   ```

3. **Clear Elementor cache:**
   ```bash
   ddev exec "wp cache flush --path=/var/www/html/wordpress"
   ddev exec "wp transient delete --all --path=/var/www/html/wordpress"
   ```

### Issue: Database Connection Error

**Symptoms:**
- "Error establishing database connection"
- Cannot connect to database

**Diagnosis:**
```bash
# Check if database container is running
ddev describe

# Test database connection
ddev exec "wp db check --path=/var/www/html/wordpress"
```

**Solutions:**
1. **Restart DDEV:**
   ```bash
   ddev restart
   ```

2. **Check database credentials:**
   ```bash
   cat ehs-wordpress-local/.ddev/config.yaml | grep -A 5 database
   ```

3. **Import database if missing:**
   ```bash
   ddev import-db --file=production-database.sql.gz
   ```

### Issue: Styles Not Applying

**Symptoms:**
- CSS changes not visible
- Elementor styles override theme CSS
- Button styles not working

**Diagnosis:**
```bash
# Check if CSS file exists
ddev exec "ls -la /var/www/html/wordpress/wp-content/themes/hello-elementor-child/style.css"

# Check browser console for CSS errors
# (Manual check in browser DevTools)
```

**Solutions:**
1. **Regenerate Elementor CSS:**
   ```bash
   cd ehs-wordpress-local
   ./regen-css.sh
   ```

2. **Clear browser cache:**
   - Hard refresh: Cmd+Shift+R (Mac) or Ctrl+Shift+R (Windows)
   - Or use incognito/private mode

3. **Verify CSS classes are applied:**
   - Check Elementor widget → Advanced → CSS Classes
   - Verify classes match theme CSS

4. **Check Elementor Site Settings:**
   ```bash
   ddev exec "wp eval-file clear-elementor-site-settings.php --path=/var/www/html/wordpress"
   ```

### Issue: Cannot Access Site from MacBook

**Symptoms:**
- Site works on Mac mini but not MacBook
- Connection timeout
- DNS resolution fails

**Diagnosis:**
```bash
# On MacBook, test connectivity
ping 10.112.1.56
curl -I http://10.112.1.56
```

**Solutions:**
1. **Add to `/etc/hosts`:**
   ```bash
   sudo nano /etc/hosts
   # Add: 10.112.1.56 ehs-mini.ddev.site
   ```

2. **Verify Mac mini IP:**
   ```bash
   # On Mac mini
   ifconfig | grep "inet " | grep -v 127.0.0.1
   ```

3. **Check firewall:**
   - Ensure Mac mini firewall allows connections
   - Check Docker Desktop network settings

### Issue: Template Sync Fails

**Symptoms:**
- SSH connection fails
- Templates not exported/imported
- Permission errors

**Diagnosis:**
```bash
# Test SSH connection
ssh a96c427e_1@832f87585d.nxcli.net

# Check if sshpass is installed
which sshpass

# Verify .env file has SSH credentials
cat .env | grep SSH
```

**Solutions:**
1. **Install sshpass (if needed):**
   ```bash
   brew install hudochenkov/sshpass/sshpass
   ```

2. **Test SSH manually:**
   ```bash
   ssh a96c427e_1@832f87585d.nxcli.net
   ```

3. **Check file permissions:**
   ```bash
   ls -la ehs-wordpress-local/exports/
   ```

4. **Run sync with verbose output:**
   ```bash
   cd ehs-wordpress-local
   bash -x sync-elementor-templates.sh
   ```

### Issue: WP-CLI Path Errors

**Symptoms:**
- "Error: This does not seem to be a WordPress installation"
- Commands fail with path errors

**Diagnosis:**
```bash
# Check current directory in container
ddev exec "pwd"

# Check WordPress path
ddev exec "ls -la /var/www/html/wordpress/wp-config.php"
```

**Solutions:**
1. **Always include `--path` parameter:**
   ```bash
   ddev exec "wp [command] --path=/var/www/html/wordpress"
   ```

2. **Use correct docroot:**
   - Docroot is `wordpress/`, not root
   - Always specify full path

### Issue: Database Import Fails

**Symptoms:**
- Import command fails
- Database corrupted
- Wrong file format

**Diagnosis:**
```bash
# Check file type
file production-database.sql

# Check file size
ls -lh production-database.sql*
```

**Solutions:**
1. **If file is gzipped but named `.sql`:**
   ```bash
   mv production-database.sql production-database.sql.gz
   ddev import-db --file=production-database.sql.gz
   ```

2. **If import succeeds but site is blank:**
   ```bash
   # Activate plugins and theme
   ddev exec "wp plugin activate elementor elementor-pro --path=/var/www/html/wordpress"
   ddev exec "wp theme activate hello-elementor-child --path=/var/www/html/wordpress"
   ddev exec "wp cache flush --path=/var/www/html/wordpress"
   ```

3. **Search-replace URLs:**
   ```bash
   ddev exec "wp search-replace 'https://ehsanalytical.com' 'https://ehs-mini.ddev.site' --all-tables --path=/var/www/html/wordpress"
   ```

## Performance Issues

### Slow Page Loads

**Diagnosis:**
```bash
# Check database size
ddev exec "wp db size --path=/var/www/html/wordpress"

# Check for slow queries
ddev logs -s db | grep -i slow
```

**Solutions:**
1. **Clear all caches:**
   ```bash
   ddev exec "wp cache flush --path=/var/www/html/wordpress"
   ddev exec "wp transient delete --all --path=/var/www/html/wordpress"
   ```

2. **Optimize database:**
   ```bash
   ddev exec "wp db optimize --path=/var/www/html/wordpress"
   ```

3. **Regenerate Elementor CSS:**
   ```bash
   cd ehs-wordpress-local
   ./regen-css.sh
   ```

## Known Failure Modes

### Docker Desktop Not Running

**Symptom:** `ddev start` fails or containers don't start

**Solution:**
```bash
open -a Docker
# Wait 30-60 seconds
docker info  # Verify
ddev start
```

### Mutagen Sync Issues

**Symptom:** File changes not reflected in container

**Solution:**
```bash
ddev stop
ddev start
# Or restart Mutagen sync
```

### WordPress Memory Limit

**Symptom:** "Fatal error: Allowed memory size exhausted"

**Solution:**
Add to `wp-config.php`:
```php
define('WP_MEMORY_LIMIT', '256M');
define('WP_MAX_MEMORY_LIMIT', '512M');
```

## Emergency Procedures

### Complete Reset

```bash
# Stop DDEV
ddev stop

# Remove containers
ddev remove

# Restart fresh
ddev start
ddev import-db --file=production-database.sql.gz
```

### Restore from Backup

```bash
# Restore database
ddev import-db --file=backup.sql.gz

# Restore uploads
tar -xzf uploads-backup.tar.gz -C ehs-wordpress-local/wordpress/wp-content/

# Activate everything
ddev exec "wp plugin activate --all --path=/var/www/html/wordpress"
ddev exec "wp theme activate hello-elementor-child --path=/var/www/html/wordpress"
```

## Getting Help

1. **Check logs first** (see Log Locations above)
2. **Review [CLAUDE.md](../CLAUDE.md)** for detailed guidance
3. **Check [Workflows](workflows.md)** for common procedures
4. **Review DDEV documentation:** https://ddev.readthedocs.io/
5. **Check WordPress debug log** for PHP errors
