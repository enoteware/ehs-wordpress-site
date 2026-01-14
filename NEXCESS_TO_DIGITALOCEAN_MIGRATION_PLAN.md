# Nexcess to DigitalOcean Migration Plan

**Date Created:** January 2025  
**Migration Type:** WordPress Multi-Site Hosting  
**Target Savings:** $280/month ($3,360/year)

---

## Executive Summary

Migrating 24 WordPress sites from Nexcess Managed WordPress ($328/month) to DigitalOcean VPS with aaPanel ($48/month) to reduce hosting costs by 85% while maintaining full control and CLI capabilities.

**Key Metrics:**
- Current Cost: $328/month ($13.67 per site)
- Target Cost: $48/month ($2.00 per site)
- Annual Savings: $3,360
- Migration Timeline: 3-4 weeks
- Risk Level: Medium (mitigated with testing and rollback plan)

---

## Current State Analysis

### Nexcess Infrastructure

**Account Details:**
- Plan: NDS 50 Site Plan
- Monthly Cost: $328.00
- Total Sites: 24 (not utilizing full 50-site capacity)
- SSH Host: `832f87585d.nxcli.net`
- SSH User: `a96c427e_1`
- WordPress Base Path: `/home/a96c427e/832f87585d.nxcli.net/html`
- Server Type: Managed WordPress (shared resources)
- Support: Managed (included)

**Site Inventory:**

| Domain | Status | Notes | Priority |
|--------|--------|-------|----------|
| ehsanalytical.com | Active | Main EHS site, Elementor-based | HIGH |
| notewaredigital.com | Active | Agency portfolio site | HIGH |
| pickproslv.com | Active | Has staging environment | MEDIUM |
| japan.cultureroute.com | Active | Has staging environment | MEDIUM |
| accreditedlabs.co | Review | Check activity | MEDIUM |
| koniclabs.com | Review | Check activity | MEDIUM |
| gonsalvi.com | Review | Check activity | LOW |
| iepa.com | Review | Check activity | LOW |
| reflexwatch.com | Review | Check activity | LOW |
| letsgoev.com | Review | Check activity | LOW |
| old.avmw.com | Delete | "old" subdomain, likely inactive | DELETE |
| capitolstrategiesgroup.com | Review | Check activity | LOW |
| dressfresh.com | Review | Check activity | LOW |
| californiadistributorsassociation.com | Review | Check activity | LOW |
| frednoteware.com | Review | Personal site? | LOW |
| rrfmedia.com | Review | Check activity | LOW |
| alisoltanilaw.com | Review | Check activity | LOW |
| dragondigitalllc.com | Review | Check activity | LOW |
| phoenixbottles.co | Review | Check activity | LOW |
| kelseypilates.com | Review | Check activity | LOW |
| lemeeseghosal.com | Review | Check activity | LOW |
| acgautomotive.com | Review | Check activity | LOW |
| rescuepawsthailand.org | Review | Check activity | LOW |
| ibcpa.com | Review | Check activity | LOW |
| 4d20afbdc3.nxcli.net | Delete | Temporary/staging domain | DELETE |

**Technical Stack (Current):**
- WordPress: Multiple versions (need to verify)
- PHP: Managed by Nexcess (likely 8.0+)
- Database: MySQL/MariaDB (managed)
- Web Server: Nginx (managed)
- SSL: Managed certificates
- Backups: Managed (included)
- WP-CLI: Available via SSH
- Elementor: Used on ehsanalytical.com and others

**Current Capabilities:**
- ✅ SSH access with WP-CLI
- ✅ Managed updates and security
- ✅ Automated backups
- ✅ Staging environments (some sites)
- ✅ Performance optimization (managed)
- ❌ Limited CLI control over server
- ❌ High cost per site
- ❌ Shared resources (no dedicated control)

---

## Target State

### DigitalOcean Infrastructure

**VPS Specifications:**
- **Droplet Type:** Regular (Standard) - 8GB RAM
- **vCPU:** 4 cores
- **Storage:** 160GB SSD
- **Bandwidth:** 5TB/month
- **Monthly Cost:** $48.00
- **Location:** NYC3 (or closest to majority of sites)
- **OS:** Ubuntu 22.04 LTS

**Management Panel:**
- **Panel:** aaPanel (free, open-source)
- **Web Server:** Nginx
- **PHP:** 8.1, 8.2, 8.3 (multiple versions)
- **Database:** MySQL 8.0 or MariaDB 10.11
- **SSL:** Let's Encrypt (free, automated)
- **Backups:** Automated via aaPanel + custom scripts
- **Monitoring:** Server monitoring via aaPanel + optional external tools

**CLI Tools Available:**
- ✅ `doctl` - DigitalOcean CLI (server management)
- ✅ `wp` - WP-CLI (WordPress management)
- ✅ `aaPanel CLI` - Bash scripts for site management
- ✅ Full root access for automation

**Target Capabilities:**
- ✅ Full server control
- ✅ CLI automation for all operations
- ✅ Cost-effective ($2/site vs $13.67/site)
- ✅ Scalable (can upgrade droplet if needed)
- ✅ Self-managed (you control updates/security)
- ⚠️ Manual backups (automated but self-configured)
- ⚠️ Self-managed security updates

---

## Cost Analysis

### Current Costs (Nexcess)

| Item | Monthly Cost | Annual Cost |
|------|-------------|-------------|
| NDS 50 Site Plan | $328.00 | $3,936.00 |
| **Total** | **$328.00** | **$3,936.00** |
| **Cost per Site (24 sites)** | **$13.67** | **$164.00** |

### Target Costs (DigitalOcean + aaPanel)

| Item | Monthly Cost | Annual Cost |
|------|-------------|-------------|
| DigitalOcean 8GB Droplet | $48.00 | $576.00 |
| aaPanel | $0.00 (free) | $0.00 |
| SSL Certificates | $0.00 (Let's Encrypt) | $0.00 |
| Backups (optional external) | $0-10.00 | $0-120.00 |
| **Total** | **$48-58.00** | **$576-696.00** |
| **Cost per Site (24 sites)** | **$2.00-2.42** | **$24.00-29.00** |

### Savings Breakdown

| Metric | Value |
|--------|-------|
| Monthly Savings | $270-280 |
| Annual Savings | $3,240-3,360 |
| Cost Reduction | 85% |
| Payback Period | Immediate (no setup fees) |

### Resource Capacity

**8GB Droplet Capacity:**
- **System Overhead:** ~1.5GB RAM
- **Available for Sites:** ~6.5GB RAM
- **Per Site Allocation:** ~130MB RAM (for 50 sites)
- **Storage per Site:** ~3GB average (160GB / 50 sites)
- **Realistic Capacity:** 50-70 small brochure sites comfortably

**For 24 Sites:**
- RAM Usage: ~3-4GB (50% headroom)
- Storage Usage: ~50-70GB (plenty of space)
- CPU Usage: Minimal (brochure sites are mostly static)
- **Verdict:** More than sufficient, room to grow

---

## Migration Phases

### Phase 1: Pre-Migration Audit (Week 1)

**Objectives:**
- Identify which sites to migrate vs. delete
- Document current site configurations
- Create backup strategy
- Set up test environment

**Tasks:**

1. **Site Audit** (Days 1-2)
   ```bash
   # Run site audit script
   ./audit-sites.sh
   ```
   - Check site accessibility
   - Identify offline/inactive sites
   - Document WordPress versions
   - List plugins/themes per site
   - Check for staging environments

2. **Client Communication** (Days 2-3)
   - Contact clients for inactive sites
   - Get approval for deletions
   - Notify active clients of migration (optional)
   - Document final site list

3. **Backup Creation** (Days 3-4)
   - Full site backups (files + databases)
   - Download to local storage
   - Verify backup integrity
   - Store backups securely

4. **Test Environment Setup** (Days 4-5)
   - Create DigitalOcean test droplet
   - Install aaPanel
   - Test migration process with 1-2 sites
   - Document any issues

**Deliverables:**
- Site audit report (`site-audit-results-YYYYMMDD.txt`)
- Final site list (migrate vs. delete)
- Backup verification report
- Test migration documentation

---

### Phase 2: VPS Setup & Configuration (Week 2)

**Objectives:**
- Set up production DigitalOcean droplet
- Install and configure aaPanel
- Configure security and monitoring
- Set up backup automation

**Tasks:**

1. **DigitalOcean Droplet Creation** (Day 1)
   ```bash
   # Using doctl CLI
   doctl compute droplet create wp-migration-server \
     --size s-4vcpu-8gb \
     --image ubuntu-22-04-x64 \
     --region nyc3 \
     --ssh-keys YOUR_SSH_KEY_ID \
     --enable-monitoring \
     --enable-ipv6
   ```
   - Create 8GB droplet
   - Configure SSH keys
   - Enable monitoring
   - Set up firewall rules

2. **Initial Server Setup** (Day 1)
   ```bash
   # SSH into new server
   ssh root@YOUR_DROPLET_IP
   
   # Update system
   apt update && apt upgrade -y
   
   # Create non-root user
   adduser wpadmin
   usermod -aG sudo wpadmin
   ```
   - System updates
   - Create admin user
   - Configure SSH security
   - Set up firewall (UFW)

3. **aaPanel Installation** (Day 2)
   ```bash
   # Install aaPanel
   wget -O install.sh http://www.aapanel.com/script/install-ubuntu_6.0_en.sh
   sudo bash install.sh
   ```
   - Install aaPanel
   - Configure admin credentials
   - Install LAMP/LEMP stack
   - Configure PHP versions (8.1, 8.2, 8.3)
   - Set up MySQL/MariaDB

4. **Security Configuration** (Day 2-3)
   - Configure firewall rules
   - Set up fail2ban
   - Configure SSH key-only access
   - Set up SSL certificates (Let's Encrypt)
   - Configure automatic security updates

5. **Backup System Setup** (Day 3)
   - Configure aaPanel backup settings
   - Set up automated daily backups
   - Configure backup retention (7-30 days)
   - Test backup/restore process
   - Optional: Set up off-server backups (S3, etc.)

6. **Monitoring Setup** (Day 3-4)
   - Configure aaPanel monitoring
   - Set up email alerts
   - Configure resource usage alerts
   - Optional: Set up external monitoring (UptimeRobot, etc.)

7. **WP-CLI Installation** (Day 4)
   ```bash
   # Install WP-CLI globally
   curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
   chmod +x wp-cli.phar
   sudo mv wp-cli.phar /usr/local/bin/wp
   ```
   - Install WP-CLI
   - Test WP-CLI functionality
   - Configure for multi-site usage

**Deliverables:**
- Production droplet running
- aaPanel configured and accessible
- Security hardened
- Backup system operational
- Monitoring active

---

### Phase 3: Site Migration (Week 3)

**Objectives:**
- Migrate all active sites to new server
- Test each site thoroughly
- Update DNS records
- Monitor for issues

**Migration Process (Per Site):**

1. **Pre-Migration Checklist**
   - [ ] Site backup verified
   - [ ] Database credentials documented
   - [ ] Plugin/theme list documented
   - [ ] Custom configurations noted

2. **Create Site on New Server** (via aaPanel)
   - Create site in aaPanel
   - Set up database
   - Configure PHP version
   - Set up SSL certificate
   - Configure domain (point to new IP temporarily)

3. **Transfer Files**
   ```bash
   # From Nexcess server
   ssh a96c427e_1@832f87585d.nxcli.net
   cd /home/a96c427e/832f87585d.nxcli.net/html
   tar -czf /tmp/SITE_DOMAIN.tar.gz SITE_DOMAIN/
   
   # Download to local
   scp a96c427e_1@832f87585d.nxcli.net:/tmp/SITE_DOMAIN.tar.gz ./
   
   # Upload to new server
   scp SITE_DOMAIN.tar.gz root@NEW_SERVER_IP:/tmp/
   
   # On new server, extract
   ssh root@NEW_SERVER_IP
   cd /www/wwwroot/SITE_DOMAIN
   tar -xzf /tmp/SITE_DOMAIN.tar.gz
   ```

4. **Export/Import Database**
   ```bash
   # On Nexcess (export)
   ssh a96c427e_1@832f87585d.nxcli.net
   cd /home/a96c427e/832f87585d.nxcli.net/html/SITE_DOMAIN
   wp db export /tmp/SITE_DOMAIN_db.sql --allow-root
   
   # Download
   scp a96c427e_1@832f87585d.nxcli.net:/tmp/SITE_DOMAIN_db.sql ./
   
   # Upload to new server
   scp SITE_DOMAIN_db.sql root@NEW_SERVER_IP:/tmp/
   
   # On new server (import)
   ssh root@NEW_SERVER_IP
   cd /www/wwwroot/SITE_DOMAIN
   wp db import /tmp/SITE_DOMAIN_db.sql --allow-root
   ```

5. **Update WordPress Configuration**
   ```bash
   # Update site URL
   wp search-replace 'https://SITE_DOMAIN.com' 'https://SITE_DOMAIN.com' --allow-root
   
   # Update database credentials in wp-config.php
   # (usually done via aaPanel, but verify)
   
   # Update file permissions
   chown -R www:www /www/wwwroot/SITE_DOMAIN
   ```

6. **Post-Migration Testing**
   - [ ] Site loads correctly
   - [ ] All pages accessible
   - [ ] Images/media loading
   - [ ] Forms working (if any)
   - [ ] SSL certificate active
   - [ ] Admin login works
   - [ ] Plugins functioning
   - [ ] No PHP errors in logs

7. **DNS Update**
   - Update A record to point to new server IP
   - Wait for DNS propagation (can take 24-48 hours)
   - Monitor site during transition

**Migration Priority:**

1. **High Priority (Migrate First - Days 1-3)**
   - ehsanalytical.com
   - notewaredigital.com

2. **Medium Priority (Days 4-7)**
   - pickproslv.com
   - japan.cultureroute.com
   - Other active client sites

3. **Low Priority (Days 8-10)**
   - Review sites (migrate if needed)
   - Archive/delete inactive sites

**Automation Script:**
Use `migrate-sites.sh` script for bulk operations:
```bash
./migrate-sites.sh ehsanalytical.com
./migrate-sites.sh all  # For all sites in SITES array
```

**Deliverables:**
- All active sites migrated
- All sites tested and verified
- DNS updated for all sites
- Migration documentation completed

---

### Phase 4: Post-Migration & Cleanup (Week 4)

**Objectives:**
- Monitor all sites for issues
- Optimize server performance
- Clean up old Nexcess account
- Document new setup

**Tasks:**

1. **Monitoring Period** (Days 1-3)
   - Monitor site uptime
   - Check error logs daily
   - Monitor resource usage
   - Check backup success
   - Address any issues immediately

2. **Performance Optimization** (Days 3-4)
   - Configure caching (Redis/Memcached)
   - Optimize PHP-FPM settings
   - Configure Nginx caching
   - Optimize database
   - Set up CDN if needed (Cloudflare)

3. **Documentation** (Days 4-5)
   - Document server setup
   - Create runbook for common tasks
   - Document backup/restore procedures
   - Create monitoring dashboard
   - Document CLI commands

4. **Nexcess Cleanup** (Days 5-7)
   - Delete migrated sites from Nexcess
   - Cancel Nexcess plan
   - Download final backups
   - Archive old backups

5. **Client Communication** (Day 7)
   - Notify clients of completion
   - Provide new support contact info
   - Update any documentation

**Deliverables:**
- All sites stable and optimized
- Complete documentation
- Nexcess account closed
- Cost savings realized

---

## Risk Assessment & Mitigation

### High-Risk Items

| Risk | Impact | Probability | Mitigation |
|------|--------|-------------|------------|
| Site downtime during migration | High | Medium | Use DNS TTL reduction, migrate during low-traffic hours, keep Nexcess active during transition |
| Data loss during transfer | High | Low | Create full backups before migration, verify backups, test restore process |
| SSL certificate issues | Medium | Low | Use Let's Encrypt automation, test SSL before DNS switch |
| Performance degradation | Medium | Low | Monitor resources, optimize PHP-FPM, configure caching |
| Security vulnerabilities | High | Medium | Follow security hardening guide, enable fail2ban, regular updates |

### Medium-Risk Items

| Risk | Impact | Probability | Mitigation |
|------|--------|-------------|------------|
| Plugin compatibility issues | Medium | Medium | Test plugins before migration, keep WordPress updated |
| Database connection errors | Medium | Low | Verify database credentials, test connections |
| File permission issues | Low | Medium | Use correct ownership (www:www), set proper permissions (755/644) |

### Rollback Plan

**If Critical Issues Arise:**

1. **Immediate Rollback (Within 24 hours)**
   - Revert DNS to Nexcess server
   - Sites remain accessible on old server
   - Investigate issues on new server
   - Fix issues before re-attempting migration

2. **Partial Rollback (Specific Sites)**
   - Revert DNS for affected sites only
   - Keep other sites on new server
   - Fix issues for affected sites
   - Re-migrate when ready

3. **Full Rollback (Last Resort)**
   - Revert all DNS to Nexcess
   - Restore from backups if needed
   - Keep Nexcess active for 30 days post-migration
   - Re-evaluate migration strategy

**Rollback Procedures:**
- DNS changes can be reverted immediately
- Nexcess sites remain untouched until deletion
- Full backups available for restore
- No data loss risk

---

## Technical Specifications

### Server Requirements

**Minimum (Current 24 Sites):**
- RAM: 8GB (sufficient)
- CPU: 4 vCPU (sufficient)
- Storage: 160GB (sufficient)
- Bandwidth: 5TB/month (more than enough)

**Recommended (Future Growth):**
- RAM: 16GB (if adding 20+ more sites)
- CPU: 8 vCPU (for high-traffic sites)
- Storage: 320GB (for media-heavy sites)

### Software Stack

**Operating System:**
- Ubuntu 22.04 LTS (recommended)
- Or Ubuntu 20.04 LTS (alternative)

**Web Server:**
- Nginx 1.22+ (via aaPanel)

**PHP:**
- PHP 8.1, 8.2, 8.3 (multiple versions)
- PHP-FPM with optimized pool settings

**Database:**
- MySQL 8.0 or MariaDB 10.11
- Optimized for WordPress

**SSL:**
- Let's Encrypt (free, automated renewal)

**Backup:**
- aaPanel automated backups
- Optional: External backup (S3, Backblaze, etc.)

### Security Hardening

**Firewall:**
- UFW (Uncomplicated Firewall)
- Allow only: SSH (22), HTTP (80), HTTPS (443)
- Block all other ports

**SSH:**
- Key-based authentication only
- Disable password authentication
- Change default port (optional)

**Fail2ban:**
- Protect against brute force attacks
- Configure for SSH and web server

**Updates:**
- Automatic security updates enabled
- Regular manual updates for major versions

**Monitoring:**
- Server resource monitoring
- Uptime monitoring
- Error log monitoring
- Email alerts for critical issues

---

## CLI Tools & Automation

### Available CLI Tools

**DigitalOcean CLI (`doctl`):**
```bash
# Install
brew install doctl  # macOS
# or
wget https://github.com/digitalocean/doctl/releases/download/v1.xx.x/doctl-1.xx.x-darwin-amd64.tar.gz

# Authenticate
doctl auth init

# Manage droplets
doctl compute droplet list
doctl compute droplet create ...
doctl compute droplet delete ...
```

**WP-CLI:**
```bash
# Already installed on server
wp --info
wp plugin list
wp theme list
wp db export backup.sql
```

**aaPanel CLI (Bash Scripts):**
```bash
# Site management via aaPanel API or direct commands
bt site add domain.com
bt wp install domain.com
bt ssl domain.com
```

### Automation Scripts

**Created Scripts:**
1. `audit-sites.sh` - Check site status
2. `migrate-sites.sh` - Migrate individual sites
3. (To be created) `bulk-migrate.sh` - Migrate all sites
4. (To be created) `monitor-sites.sh` - Monitor all sites
5. (To be created) `backup-all.sh` - Backup all sites

**Future Automation:**
- Automated daily backups
- Automated security updates
- Automated SSL renewal
- Automated site health checks
- Automated performance monitoring

---

## Timeline Summary

| Phase | Duration | Key Activities |
|-------|----------|----------------|
| **Phase 1: Audit** | Week 1 | Site audit, client communication, backups, test setup |
| **Phase 2: VPS Setup** | Week 2 | Create droplet, install aaPanel, configure security |
| **Phase 3: Migration** | Week 3 | Migrate all sites, test, update DNS |
| **Phase 4: Cleanup** | Week 4 | Monitor, optimize, document, close Nexcess |
| **Total** | **4 weeks** | Complete migration |

**Critical Path:**
- Week 1: Must complete audit before migration
- Week 2: Must complete VPS setup before migration
- Week 3: Migration can be done in parallel (multiple sites)
- Week 4: Monitoring period before closing Nexcess

---

## Success Criteria

### Migration Success

- [ ] All active sites migrated successfully
- [ ] All sites accessible and functioning
- [ ] No data loss
- [ ] SSL certificates active on all sites
- [ ] DNS updated and propagated
- [ ] Performance equal or better than Nexcess
- [ ] Backup system operational
- [ ] Monitoring active
- [ ] Documentation complete

### Cost Savings Realized

- [ ] Nexcess plan cancelled
- [ ] DigitalOcean billing active
- [ ] Monthly cost reduced to ~$48
- [ ] Annual savings of $3,360 achieved

### Operational Readiness

- [ ] Team trained on new setup
- [ ] CLI tools configured and tested
- [ ] Backup/restore procedures documented
- [ ] Monitoring and alerting configured
- [ ] Support procedures established

---

## Post-Migration Support

### Daily Tasks
- Monitor server resources
- Check backup success
- Review error logs

### Weekly Tasks
- Review site performance
- Check for WordPress/plugin updates
- Review security logs
- Optimize databases

### Monthly Tasks
- Review costs and usage
- Plan for scaling if needed
- Review and update documentation
- Security audit

### Emergency Procedures
- Site down: Check server status, review logs, restore from backup if needed
- High resource usage: Identify culprit, optimize or upgrade
- Security breach: Isolate affected site, review logs, patch vulnerabilities

---

## Next Steps

### Immediate Actions (This Week)

1. **Run Site Audit**
   ```bash
   cd /Users/elliotnoteware/code/ehs
   ./audit-sites.sh
   ```

2. **Review Audit Results**
   - Identify sites to delete
   - Identify sites to migrate
   - Get client approvals

3. **Create DigitalOcean Account**
   - Sign up at digitalocean.com
   - Install `doctl` CLI
   - Generate SSH keys

4. **Create Test Droplet**
   - Test aaPanel installation
   - Test migration process with 1 site
   - Document any issues

### Week 1 Actions

1. Complete site audit
2. Get client approvals for deletions
3. Create full backups of all sites
4. Set up test environment
5. Test migration process

### Week 2 Actions

1. Create production droplet
2. Install and configure aaPanel
3. Set up security and monitoring
4. Configure backup system
5. Prepare migration scripts

---

## Resources & Documentation

### Internal Documentation
- `nexcess-migration-plan.md` - This document
- `audit-sites.sh` - Site audit script
- `migrate-sites.sh` - Site migration script
- `site-audit-checklist.md` - Audit checklist

### External Resources
- [DigitalOcean Documentation](https://docs.digitalocean.com/)
- [aaPanel Documentation](https://doc.aapanel.com/)
- [WP-CLI Handbook](https://wp-cli.org/)
- [WordPress Migration Guide](https://wordpress.org/support/article/moving-wordpress/)

### Support Contacts
- DigitalOcean Support: support@digitalocean.com
- aaPanel Community: https://forum.aapanel.com/
- WordPress Support: https://wordpress.org/support/

---

## Appendix

### A. Site List with Details

(To be populated after audit)

### B. Migration Checklist Template

```markdown
## Site: [DOMAIN]

### Pre-Migration
- [ ] Backup created and verified
- [ ] Database credentials documented
- [ ] Plugin list documented
- [ ] Theme documented
- [ ] Custom configurations noted

### Migration
- [ ] Site created in aaPanel
- [ ] Files transferred
- [ ] Database imported
- [ ] wp-config.php updated
- [ ] File permissions set
- [ ] SSL certificate installed

### Testing
- [ ] Site loads correctly
- [ ] All pages accessible
- [ ] Images loading
- [ ] Forms working
- [ ] Admin login works
- [ ] No errors in logs

### DNS
- [ ] DNS updated
- [ ] Propagation verified
- [ ] SSL working on new server
```

### C. Common Commands Reference

**Server Management:**
```bash
# Check server resources
htop
df -h
free -h

# Check Nginx status
systemctl status nginx

# Check PHP-FPM status
systemctl status php-fpm

# Check MySQL status
systemctl status mysql
```

**WordPress Management:**
```bash
# Update all plugins
wp plugin update --all --allow-root

# Update WordPress core
wp core update --allow-root

# Optimize database
wp db optimize --allow-root

# Export database
wp db export backup.sql --allow-root
```

**Backup Management:**
```bash
# Create manual backup via aaPanel CLI
# Or use WP-CLI
wp db export /backups/site_backup_$(date +%Y%m%d).sql --allow-root
```

---

**Document Version:** 1.0  
**Last Updated:** January 2025  
**Owner:** Elliot Noteware  
**Status:** Planning Phase
