# EHS Analytical Website - Implementation Plan

## Server Details
- **Host:** Nexcess Managed WordPress
- **SSH:** 832f87585d.nxcli.net
- **WordPress Path:** `/home/a96c427e/832f87585d.nxcli.net/html`
- **Theme:** Hello Elementor (active)
- **Page Builder:** Elementor

## Current Site Structure
- **URL:** https://ehsanalytical.com
- **Design:** Navy Blue + Gold/Yellow accents, professional/industrial
- **Service Pages:** Two-column layout (sidebar menu + content)
- **Hero Sections:** Full-width background images with centered titles

## Implementation Approach

### Phase 1: SSHO Services Page (Week 1 - HIGHEST Priority)
**File:** `websiteupdates/1_Web_Designer_Instructions_Part1_SSHO.docx`
**URL:** `/ssho-services-california/`
**Target:** Federal contractors needing SSHO for military construction

Steps:
1. Create new page in WordPress
2. Use Elementor to build page matching existing service page template
3. Add content from Part 1 instructions
4. Add to navigation menu (after Construction Safety)
5. Update sidebar service menu on all service pages

### Phase 2: Lead Compliance Plan Services (Week 2 - HIGH Priority)
**File:** `websiteupdates/2_Web_Designer_Instructions_Part2_LeadCompliance.docx`
**URL:** `/lead-compliance-plan-services/`
**Target:** Caltrans bidders needing Lead Compliance Plans

Steps:
1. Create new page in WordPress
2. Build with Elementor
3. Add cross-link to/from Caltrans Construction Safety page
4. Update navigation and sidebar menus

### Phase 3: Caltrans + Federal Contracting (Week 3 - MEDIUM-HIGH)
**Files:** 
- `websiteupdates/3_Part3_Caltrans_Construction_Safety_NEW.txt`
- `websiteupdates/4_Web_Designer_Instructions_Part4_Federal_Contracting.docx`

**New Pages:**
- `/caltrans-construction-safety-services/`
- `/federal-contracting-sdvosb/`

**Updates:**
- Add 4 sections to existing Construction Safety Consulting page

### Phase 4: Global Updates (Week 4 - MEDIUM)
**File:** `websiteupdates/5_Web_Designer_Instructions_Part5_Global_Updates.docx`

Updates:
- Navigation menu (4 new items)
- About Us page (federal contractor section)
- Homepage (2 new service boxes)
- Footer (update service links)
- Technical SEO (meta descriptions, schema markup)

## WordPress Access Methods

### Option 1: WP-CLI (Recommended for bulk operations)
```bash
ssh a96c427e_1@832f87585d.nxcli.net
cd 832f87585d.nxcli.net/html
wp page create --post_title="SSHO Services California" --post_status=draft
```

### Option 2: Direct File Editing
- Edit theme files directly via SSH
- Modify Elementor JSON data in database

### Option 3: WP-Admin
- Access via https://ehsanalytical.com/wp-admin
- Use Elementor visual editor

## Next Steps
1. ✅ SSH access confirmed
2. ✅ Site structure analyzed
3. ⏭️ Extract content from .docx files
4. ⏭️ Create first page (SSHO Services)
5. ⏭️ Test and iterate

## Notes
- All content is copy/paste ready from Adam's files
- Internal linking between pages is critical
- Maintain existing design consistency
- SDVOSB/DVBE certifications should be prominent
