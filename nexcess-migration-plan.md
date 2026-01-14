# Nexcess to DigitalOcean Migration Plan

**Current Setup:**
- Nexcess NDS 50 Site Plan: $328/month
- Total Sites: 24 sites listed (not 50)
- Target: DigitalOcean 8GB + aaPanel (~$48/month)

**Potential Savings:** ~$280/month ($3,360/year)

---

## Site Inventory

### Active Sites (Keep & Migrate)

| Domain | Notes | Priority |
|--------|-------|----------|
| ehsanalytical.com | Main EHS site (active project) | HIGH |
| notewaredigital.com | Your agency site | HIGH |
| pickproslv.com | Has staging environment | MEDIUM |
| japan.cultureroute.com | Has staging environment | MEDIUM |
| | | |

### Sites to Review/Delete

| Domain | Reason to Review | Action |
|--------|------------------|--------|
| accreditedlabs.co | Check if active | ? |
| koniclabs.com | Check if active | ? |
| gonsalvi.com | Check if active | ? |
| iepa.com | Check if active | ? |
| reflexwatch.com | Check if active | ? |
| letsgoev.com | Check if active | ? |
| old.avmw.com | "old" subdomain - likely inactive | DELETE |
| capitolstrategiesgroup.com | Check if active | ? |
| dressfresh.com | Check if active | ? |
| californiadistributorsassociation.com | Check if active | ? |
| frednoteware.com | Personal site? | ? |
| rrfmedia.com | Check if active | ? |
| alisoltanilaw.com | Check if active | ? |
| dragondigitalllc.com | Check if active | ? |
| phoenixbottles.co | Check if active | ? |
| kelseypilates.com | Check if active | ? |
| lemeeseghosal.com | Check if active | ? |
| acgautomotive.com | Check if active | ? |
| rescuepawsthailand.org | Check if active | ? |
| ibcpa.com | Check if active | ? |
| 4d20afbdc3.nxcli.net | Temporary/staging domain | DELETE |

---

## Migration Strategy

### Phase 1: Site Audit (Week 1)
1. Check each site's traffic/activity
2. Identify which sites are actually needed
3. Get client approval for deletions
4. Document final site list

### Phase 2: VPS Setup (Week 1-2)
1. Create DigitalOcean 8GB Droplet
2. Install aaPanel
3. Configure PHP, MySQL, Nginx
4. Set up SSL (Let's Encrypt)
5. Configure backups
6. Set up monitoring

### Phase 3: Migration (Week 2-3)
1. Migrate high-priority sites first
2. Test each site thoroughly
3. Update DNS
4. Monitor for issues

### Phase 4: Cleanup (Week 3-4)
1. Delete unused sites from Nexcess
2. Cancel Nexcess plan
3. Document new setup

---

## Resource Planning

### If keeping 10-15 sites:
- 8GB Droplet is MORE than enough
- Can easily handle 30-40 more sites
- Cost: ~$48/month

### If keeping 20-24 sites:
- 8GB Droplet still sufficient
- Monitor resources
- Cost: ~$48/month

---

## Next Steps

1. **Audit Sites**: Check which are actually needed
2. **Get Client Approvals**: For any deletions
3. **Set Up Test Environment**: Create DO droplet and test migration
4. **Migrate High-Priority Sites**: Start with ehsanalytical.com
5. **Bulk Migrate**: Use scripts for remaining sites
