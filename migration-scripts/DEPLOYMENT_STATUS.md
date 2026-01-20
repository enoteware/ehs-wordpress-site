# Deployment Status

## ✅ dev.ehsanalytical.com - LIVE

**Deployed:** January 20, 2026
**URL:** https://dev.ehsanalytical.com
**Server:** 134.199.221.85 (DigitalOcean SFO3)

### Server Stack
| Component | Version |
|-----------|---------|
| OS | Ubuntu 22.04 LTS |
| Web Server | Nginx 1.18 |
| Database | MariaDB 10.6.22 |
| PHP | 8.1.2-FPM |
| WP-CLI | 2.12.0 |
| SSL | Let's Encrypt (auto-renewal via Certbot) |

### Deployment Details
- **WordPress Path:** `/var/www/dev.ehsanalytical.com/html/`
- **Database:** `dev_ehsanalytical`
- **DB User:** `wp_ehs`
- **Nginx Config:** `/etc/nginx/sites-available/dev.ehsanalytical.com`
- **SSL Cert:** `/etc/letsencrypt/live/dev.ehsanalytical.com/`
- **Cloudflare:** Proxied (orange cloud)

### Credentials
- **SSH:** `ssh -i ~/.ssh/id_ed25519_do root@134.199.221.85`
- **WP Admin:** https://dev.ehsanalytical.com/wp-admin
- **DB Password:** See `.env.migration-server` (MYSQL variable)

---

## Quick Commands

### Deploy Theme Changes (Fastest)
```bash
cd migration-scripts
./quick-deploy-to-do.sh
```

### Deploy All wp-content
```bash
./quick-deploy-to-do.sh --full
```

### Deploy Database Only
```bash
./quick-deploy-to-do.sh --db
```

### Deploy Everything
```bash
./quick-deploy-to-do.sh --all
```

### SSH to Server
```bash
ssh -i ~/.ssh/id_ed25519_do root@134.199.221.85
```

### Server-Side WP-CLI
```bash
ssh -i ~/.ssh/id_ed25519_do root@134.199.221.85 "cd /var/www/dev.ehsanalytical.com/html && wp cache flush --allow-root"
```

---

## Maintenance

### Clear Caches
```bash
ssh -i ~/.ssh/id_ed25519_do root@134.199.221.85 "cd /var/www/dev.ehsanalytical.com/html && wp cache flush --allow-root && wp elementor flush-css --allow-root"
```

### Check Nginx Status
```bash
ssh -i ~/.ssh/id_ed25519_do root@134.199.221.85 "nginx -t && systemctl status nginx"
```

### View Error Logs
```bash
ssh -i ~/.ssh/id_ed25519_do root@134.199.221.85 "tail -50 /var/log/nginx/dev.ehsanalytical.com_error.log"
```

### Renew SSL (auto, but manual if needed)
```bash
ssh -i ~/.ssh/id_ed25519_do root@134.199.221.85 "certbot renew --dry-run"
```

---

## Related Documentation

- [Migration Scripts README](README.md)
- [SSH Key Setup Guide](../docs/DIGITALOCEAN_SSH_SETUP.md)
- [Full Migration Plan](../NEXCESS_TO_DIGITALOCEAN_MIGRATION_PLAN.md)
