# Simple Local WordPress Setup (Without DDEV)

This guide shows how to run WordPress locally using PHP's built-in server and a local MySQL database.

## Prerequisites

1. **PHP 8.3** installed locally
   ```bash
   php -v  # Should show 8.3.x
   ```

2. **MySQL/MariaDB** installed locally
   ```bash
   mysql --version
   ```

3. **Composer** (for WP-CLI if needed)
   ```bash
   composer --version
   ```

## Quick Setup

### 1. Create Local Database

```bash
mysql -u root -p
```

```sql
CREATE DATABASE ehs_local;
CREATE USER 'ehs_user'@'localhost' IDENTIFIED BY 'local_password';
GRANT ALL PRIVILEGES ON ehs_local.* TO 'ehs_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 2. Configure WordPress

Create `wp-config-local.php` in `ehs-wordpress-local/wordpress/`:

```php
<?php
define('DB_NAME', 'ehs_local');
define('DB_USER', 'ehs_user');
define('DB_PASSWORD', 'local_password');
define('DB_HOST', 'localhost');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');

// Security keys (generate at https://api.wordpress.org/secret-key/1.1/salt/)
define('AUTH_KEY',         'put your unique phrase here');
define('SECURE_AUTH_KEY',  'put your unique phrase here');
define('LOGGED_IN_KEY',    'put your unique phrase here');
define('NONCE_KEY',        'put your unique phrase here');
define('AUTH_SALT',        'put your unique phrase here');
define('SECURE_AUTH_SALT', 'put your unique phrase here');
define('LOGGED_IN_SALT',   'put your unique phrase here');
define('NONCE_SALT',       'put your unique phrase here');

define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

$table_prefix = 'wp_';

if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

require_once ABSPATH . 'wp-settings.php';
```

### 3. Start PHP Server

```bash
cd ehs-wordpress-local/wordpress
php -S localhost:8000
```

### 4. Access WordPress

- **Site:** http://localhost:8000
- **Admin:** http://localhost:8000/wp-admin

## Database Import/Export

### Import Production Database

```bash
# Extract if gzipped
gunzip production-database.sql.gz

# Import
mysql -u ehs_user -p ehs_local < production-database.sql

# Search-replace URLs
mysql -u ehs_user -p ehs_local -e "UPDATE wp_options SET option_value='http://localhost:8000' WHERE option_name='siteurl' OR option_name='home';"
```

### Export Database

```bash
mysqldump -u ehs_user -p ehs_local > local-backup.sql
```

## WP-CLI Setup

Install WP-CLI globally:

```bash
curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
chmod +x wp-cli.phar
sudo mv wp-cli.phar /usr/local/bin/wp
```

Use with path:

```bash
cd ehs-wordpress-local/wordpress
wp --path=. plugin list
wp --path=. cache flush
```

## Limitations vs DDEV

1. **No automatic URL management** - Must manually update URLs in database
2. **No SSL** - Only HTTP (localhost:8000)
3. **Manual database management** - No `ddev import-db` convenience
4. **No container isolation** - PHP/MySQL versions must match your system
5. **No automatic WP-CLI path handling** - Must specify `--path=.` every time
6. **No production-like environment** - PHP built-in server ≠ Nginx

## When to Use Each

### Use Simple PHP Server When:
- Quick testing/debugging
- Simple theme development
- No database sync needed
- Minimal plugin usage

### Use DDEV When:
- Working with Elementor Pro/ACF Pro
- Need production-like environment
- Frequent database syncs
- Multiple developers
- Need SSL/HTTPS locally
- Want automated workflows

## Migration Scripts

Your existing scripts that use `ddev exec` will need modification:

**Before (DDEV):**
```bash
ddev exec "wp elementor flush-css --path=/var/www/html/wordpress"
```

**After (Simple):**
```bash
cd ehs-wordpress-local/wordpress
wp --path=. elementor flush-css
```

## Recommendation

For this project, **DDEV is still recommended** because:
1. Elementor Pro requires proper environment
2. Frequent production database syncs
3. WP-CLI integration is cleaner
4. Matches production stack (Nginx, PHP 8.3, MariaDB 10.11)

But if you want simplicity for quick edits, the PHP server approach works fine!
