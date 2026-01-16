# Credential Logo Sources & Official Downloads

## Current Status

All 9 credentials now have placeholder SVG badges attached as featured images. These are custom-designed placeholders following the EHS brand colors (navy #003366, gold #FFB81C).

## Official Logo Sources

For production use, you should replace these placeholders with official logos from the issuing organizations. Here are the official sources:

### 1. CIH - Certified Industrial Hygienist
- **Issuer:** American Board of Industrial Hygiene (ABIH) / Board for Global EHS Credentialing (BGC)
- **Source:** Credly digital badges (for credential holders)
- **URL:** https://www.credly.com/org/board-for-global-ehs-credentialing/badge/certified-industrial-hygienist-cih-cp-comprehensive-practice
- **Note:** Requires login as credential holder to download official badge

### 2. CSP - Certified Safety Professional
- **Issuer:** Board of Certified Safety Professionals (BCSP)
- **Source:** BCSP "My Profile" → "My Badge(s)" via Credly
- **URL:** https://www.bcsp.org/bcsp-news/about/bcsp-news/bcsp-highlighting-credential-holders-achievements-with-digital-badges
- **Brand Guide:** https://fliphtml5.com/pbcyp/qzjp/Marketing_Branding_Style_Guide_2025/
- **Note:** BCSP provides credential logos by request to their designer

### 3. CHST - Construction Health and Safety Technician
- **Issuer:** Board of Certified Safety Professionals (BCSP)
- **Source:** Same as CSP - BCSP Credly badges
- **Note:** Available to credential holders via BCSP Credly account

### 4. PMP - Project Management Professional
- **Issuer:** Project Management Institute (PMI)
- **Source:** Credly digital badges (for credential holders)
- **URL:** https://www.pmi.org/certifications/certification-resources/digital-badges
- **Note:** PMI issues badges via Credly; download requires login as credential holder

### 5. CAC - Certified Asbestos Consultant
- **Issuer:** State of California (Cal/OSHA/DIR)
- **Source:** No official logo/badge provided
- **URL:** https://www.dir.ca.gov/databases/doshcaccsst/caccsst_query_1.html
- **Note:** Cal/OSHA does not provide official badges. Use custom design without state seals.

### 6. CUSP - Certified Utility Safety Professional
- **Issuer:** Utility Safety & Ops Leadership Network (USOLN)
- **Source:** Contact USOLN directly
- **Note:** May need to request logo from USOLN

### 7. IOSH - Institution of Occupational Safety and Health
- **Issuer:** Institution of Occupational Safety and Health
- **Source:** IOSH member resources
- **Note:** Available to IOSH members; contact IOSH for logo usage permissions

### 8. SDVOSB - Service-Disabled Veteran-Owned Small Business
- **Issuer:** U.S. Small Business Administration (SBA)
- **Source:** SBA Brand Guide - Digital Icons
- **URL:** https://www.sba.gov/brand/for-partners/sba-certified-businesses
- **Download:** "Download icons (.zip)" link on SBA page
- **Note:** Only for active SBA-certified businesses

### 9. DVBE - Disabled Veteran Business Enterprise
- **Issuer:** State of California (DGS)
- **Source:** No official logo provided
- **URL:** https://www.dgs.ca.gov/PD/Services/Page-Content/Procurement-Division-Services-List-Folder/Apply-for-or-Re-apply-as-Small-Business-Disabled-Veteran-Business-Enterprise
- **Note:** DGS does not provide official badges. Use custom design without state seals.

## Usage Guidelines

### For Credential Holders:
1. **Log in to Credly** (for CIH, CSP, CHST, PMP)
2. **Download badge images** from your Credly account
3. **Follow usage guidelines** from issuing organization
4. **Upload to WordPress** as featured images

### For Organizations:
1. **Request permission** from issuing organization
2. **Follow brand guidelines** strictly
3. **Do not modify** official logos
4. **Link to verification** when possible

### Important Restrictions:
- **CIH, CSP, CHST, PMP:** Restricted to credential holders only
- **SDVOSB:** Only for active SBA-certified businesses
- **CAC, DVBE:** No official logos; use custom designs
- **All logos:** Subject to trademark and usage restrictions

## Current Placeholder Badges

The current SVG badges in `/assets/images/badges/` are:
- Custom-designed placeholders
- Following EHS brand colors
- Suitable for development/testing
- Should be replaced with official logos for production

## Replacing Placeholder Badges

To replace a placeholder badge:

1. **Download official logo** from source above (if available)
2. **Upload to WordPress Media Library**
3. **Go to Credential post** in WordPress admin
4. **Set Featured Image** to the new logo
5. **Or use WP-CLI:**
   ```bash
   ddev exec wp media import /path/to/logo.png --post_id=3360 --featured_image --path=/var/www/html/wordpress
   ```

## File Locations

- **Placeholder SVGs:** `wordpress/wp-content/themes/hello-elementor-child/assets/images/badges/`
- **Uploaded Media:** WordPress Media Library
- **Featured Images:** Set on each credential post
