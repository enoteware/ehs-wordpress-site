#!/bin/bash
#
# Import WordPress site to DigitalOcean
# Usage: ./2-import-to-digitalocean.sh DOMAIN
#

set -e

if [ -z "$1" ]; then
    echo "Usage: $0 DOMAIN"
    echo "Example: $0 ehsanalytical.com"
    exit 1
fi

DOMAIN=$1
BACKUP_DIR="./backups/${DOMAIN}"

# Load server credentials
if [ -f ../.env.migration-server ]; then
    source ../.env.migration-server
else
    echo "Error: .env.migration-server file not found"
    exit 1
fi

# Find latest backup files
DB_FILE=$(ls -t ${BACKUP_DIR}/*.sql.gz | head -1)
FILES_ARCHIVE=$(ls -t ${BACKUP_DIR}/*_files_*.tar.gz | head -1)

if [ -z "$DB_FILE" ] || [ -z "$FILES_ARCHIVE" ]; then
    echo "Error: Backup files not found in ${BACKUP_DIR}"
    echo "Run ./1-export-from-nexcess.sh ${DOMAIN} first"
    exit 1
fi

echo "=== Importing ${DOMAIN} to DigitalOcean ==="
echo "Database: ${DB_FILE}"
echo "Files: ${FILES_ARCHIVE}"
echo "Server: ${MIGRATION_SERVER_IP}"

# Generate database credentials
DB_NAME=$(echo ${DOMAIN} | sed 's/\./_/g' | cut -c1-16)
DB_USER="${DB_NAME}_user"
DB_PASS=$(openssl rand -base64 16 | tr -d '=+/' | cut -c1-16)

echo ""
echo "Database: ${DB_NAME}"
echo "DB User: ${DB_USER}"
echo "DB Pass: ${DB_PASS}"

# Upload files to server
echo ""
echo "=== Uploading files to server ==="
scp ${DB_FILE} root@${MIGRATION_SERVER_IP}:/tmp/
scp ${FILES_ARCHIVE} root@${MIGRATION_SERVER_IP}:/tmp/

# Import on server
ssh root@${MIGRATION_SERVER_IP} << EOF
set -e

echo "=== Setting up ${DOMAIN} ==="

# Create directory
mkdir -p /var/www/${DOMAIN}/html
cd /var/www/${DOMAIN}/html

# Extract WordPress files
echo "Extracting files..."
tar -xzf /tmp/$(basename ${FILES_ARCHIVE})

# Create database
echo "Creating database..."
mysql -u root << SQL
CREATE DATABASE IF NOT EXISTS ${DB_NAME};
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';
FLUSH PRIVILEGES;
SQL

# Import database
echo "Importing database..."
gunzip < /tmp/$(basename ${DB_FILE}) | mysql -u root ${DB_NAME}

# Update wp-config.php
echo "Updating wp-config.php..."
wp config set DB_NAME '${DB_NAME}' --allow-root
wp config set DB_USER '${DB_USER}' --allow-root
wp config set DB_PASSWORD '${DB_PASS}' --allow-root
wp config set DB_HOST 'localhost' --allow-root

# Update site URLs (will be temporary until DNS points here)
wp search-replace 'https://${DOMAIN}' 'http://${MIGRATION_SERVER_IP}' --skip-columns=guid --allow-root || true
wp search-replace 'http://${DOMAIN}' 'http://${MIGRATION_SERVER_IP}' --skip-columns=guid --allow-root || true

# Set permissions
chown -R www-data:www-data /var/www/${DOMAIN}
chmod -R 755 /var/www/${DOMAIN}

# Create Nginx config
cat > /etc/nginx/sites-available/${DOMAIN} << 'NGINX'
server {
    listen 80;
    server_name ${DOMAIN} www.${DOMAIN} ${MIGRATION_SERVER_IP};
    root /var/www/${DOMAIN}/html;
    index index.php index.html;

    # Logs
    access_log /var/log/nginx/${DOMAIN}_access.log;
    error_log /var/log/nginx/${DOMAIN}_error.log;

    # WordPress permalinks
    location / {
        try_files \$uri \$uri/ /index.php?\$args;
    }

    # PHP processing
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    # Deny access to sensitive files
    location ~ /\.ht {
        deny all;
    }

    location = /favicon.ico {
        log_not_found off;
        access_log off;
    }

    location = /robots.txt {
        allow all;
        log_not_found off;
        access_log off;
    }

    # Cache static files
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires max;
        log_not_found off;
    }
}
NGINX

# Enable site
ln -sf /etc/nginx/sites-available/${DOMAIN} /etc/nginx/sites-enabled/

# Test and reload Nginx
nginx -t && systemctl reload nginx

# Cleanup
rm -f /tmp/$(basename ${DB_FILE}) /tmp/$(basename ${FILES_ARCHIVE})

echo ""
echo "✓ Import complete!"
echo "  Site: http://${MIGRATION_SERVER_IP}"
echo "  Admin: http://${MIGRATION_SERVER_IP}/wp-admin"
echo ""
echo "Database credentials saved to /root/.${DOMAIN}_db_creds"
echo "DB_NAME=${DB_NAME}" > /root/.${DOMAIN}_db_creds
echo "DB_USER=${DB_USER}" >> /root/.${DOMAIN}_db_creds
echo "DB_PASS=${DB_PASS}" >> /root/.${DOMAIN}_db_creds

EOF

echo ""
echo "=== Migration Summary ==="
echo "✓ Site imported successfully"
echo "  Preview: http://${MIGRATION_SERVER_IP}"
echo "  Domain: ${DOMAIN}"
echo ""
echo "Next steps:"
echo "  1. Test the site at http://${MIGRATION_SERVER_IP}"
echo "  2. When ready, run: ./3-setup-ssl.sh ${DOMAIN}"
echo "  3. Update DNS to point to ${MIGRATION_SERVER_IP}"
