# Domain Reference - Special Domains

**Date:** January 2025  
**Purpose:** Quick reference for all special domains mentioned in the project

---

## Special Domains

### 1. Main Site (Existing)
**Domain:** `ehsanalytical.com`  
**Type:** WordPress site (existing)  
**Location:** Nexcess Managed WordPress  
**SSH:** 832f87585d.nxcli.net  
**Status:** Active - receiving updates

---

### 2. Micro-Site (New - PROJECT 2)
**Domain:** `constructionsafety.consulting`  
**Type:** Static HTML site (new micro-site)  
**Hosting:** Vercel (or similar static hosting)  
**Status:** Ready for deployment  
**Purpose:** 3-page focused micro-site for construction safety consulting

**Pages:**
- Homepage: `constructionsafety.consulting/`
- SSHO Services: `constructionsafety.consulting/ssho-services/`
- Safety Representatives: `constructionsafety.consulting/safety-representatives/`

**Deployment Location:**
- `project-organization/02-micro-site-vercel/deployed-site/constructionsafety-microsite/`

**Deploy with:**
```bash
cd project-organization/02-micro-site-vercel/deployed-site/constructionsafety-microsite
vercel
```

---

### 3. Redirect Domain (Old → New)
**Old Domain:** `onsitesafety.consulting`  
**New Domain:** `constructionsafety.consulting`  
**Redirect Type:** 301 (Permanent)  
**Purpose:** Redirect old domain to new micro-site domain

**Setup Required:**
- Set up 301 redirect from `onsitesafety.consulting` → `constructionsafety.consulting`
- Can be done at domain registrar or hosting provider
- See: `02-micro-site-vercel/source-content/constructionsafety_consultingwebsite/Technical_SEO_Specifications.txt`

---

## Domain Setup Instructions

### For constructionsafety.consulting:

1. **Purchase Domain:**
   - Buy `constructionsafety.consulting` from domain registrar
   - Estimated cost: ~$12/year

2. **Add to Vercel:**
   - Deploy site to Vercel first
   - Go to Project Settings → Domains
   - Add `constructionsafety.consulting`
   - Follow DNS configuration instructions from Vercel

3. **Set Up SSL:**
   - Vercel automatically provides SSL certificate
   - HTTPS will be enabled automatically

4. **Set Up 301 Redirect:**
   - Configure redirect from `onsitesafety.consulting` → `constructionsafety.consulting`
   - Options:
     - DNS-level redirect (at domain registrar) - Recommended
     - Server-level redirect (if hosting both domains)
     - Vercel redirect rules (if both domains point to Vercel)

---

## Domain References in Documents

### Main Documentation:
- `project-organization/README.md` - Mentions `constructionsafety.consulting`
- `project-organization/02-micro-site-vercel/deployed-site/constructionsafety-microsite/README.md` - Full domain setup instructions

### Source Content Files:
- `02-micro-site-vercel/source-content/constructionsafety_consultingwebsite/MASTER_IMPLEMENTATION_GUIDE.txt` - Domain setup instructions
- `02-micro-site-vercel/source-content/constructionsafety_consultingwebsite/Technical_SEO_Specifications.txt` - Domain and redirect setup
- `02-micro-site-vercel/source-content/constructionsafety_consultingwebsite/FINAL_PACKAGE_SUMMARY_ConstructionSafety.txt` - Complete domain information

### Implementation Guides:
- `01-main-site-wordpress/implementation-guides/CLIENT_REQUIREMENTS_SUMMARY.md` - Mentions both domains
- `01-main-site-wordpress/implementation-guides/PROJECT_SUMMARY.md` - Domain references

---

## Future Domain Considerations

According to `FINAL_PACKAGE_SUMMARY_ConstructionSafety.txt`, after `constructionsafety.consulting` is launched and performing well, consider these additional domains:

- `leadcomplianceplans.com` (February)
- `caltransleadcomplianceplan.com` (March)

**Note:** These are future considerations, not current requirements.

---

## Quick Reference

| Domain | Type | Status | Purpose |
|--------|------|--------|---------|
| `ehsanalytical.com` | Main site | ✅ Active | WordPress site receiving updates |
| `constructionsafety.consulting` | Micro-site | ⏭️ Ready to deploy | 3-page static site on Vercel |
| `onsitesafety.consulting` | Redirect | ⏭️ Needs setup | 301 redirect to constructionsafety.consulting |

---

## Next Steps for Domains

1. ✅ **Main site** - Already active, no domain changes needed
2. ⏭️ **Micro-site** - Purchase `constructionsafety.consulting` domain
3. ⏭️ **Redirect** - Set up 301 redirect from `onsitesafety.consulting`
4. ⏭️ **Deploy** - Deploy micro-site to Vercel and configure domain

---

## Domain Setup Checklist

- [ ] Purchase `constructionsafety.consulting` domain
- [ ] Deploy site to Vercel
- [ ] Add domain to Vercel project
- [ ] Configure DNS records
- [ ] Verify SSL certificate (automatic with Vercel)
- [ ] Set up 301 redirect from `onsitesafety.consulting`
- [ ] Test redirect works correctly
- [ ] Submit sitemap to Google Search Console
- [ ] Set up Google Analytics

---

**All domain information is documented in the source content files and implementation guides.**
