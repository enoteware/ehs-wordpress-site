# EHS Analytical Solutions - Project Summary

**Date:** December 2025  
**Status:** Ready for Implementation

---

## Overview

This repository contains all content, documentation, and implementation guides for two separate website projects for EHS Analytical Solutions, Inc.

---

## PROJECT 1: Main Site Updates (ehsanalytical.com)

### Scope
- **4 NEW pages** to create
- **1 EXISTING page** to update  
- **Global updates** (navigation, footer, homepage, About page)

### Priority Order

1. **WEEK 1: SSHO Services Page** (HIGHEST PRIORITY)
   - URL: `/ssho-services-california/`
   - Implementation Guide: `PAGE_IMPLEMENTATION_SSHO.md`
   - Content Source: `updated-docs/1_Web_Designer_Instructions_Part1_SSHO.json`

2. **WEEK 2: Lead Compliance Plan Services Page**
   - URL: `/lead-compliance-plan-services/`
   - Content Source: `updated-docs/2_Web_Designer_Instructions_Part2_LeadCompliance.json`

3. **WEEK 3: Caltrans Construction Safety + Federal Contracting**
   - New Page: `/caltrans-construction-safety-services/`
   - New Page: `/federal-contracting-sdvosb/`
   - Update: Existing Construction Safety Consulting page
   - Content Sources:
     - `updated-docs/3_Part3_Caltrans_Construction_Safety_NEW.json`
     - `updated-docs/4_Web_Designer_Instructions_Part4_Federal_Contracting.json`

4. **WEEK 4: Global Updates**
   - Navigation menu (add 4 new service links)
   - About Us page (add federal contractor section)
   - Homepage (add 2 new service boxes)
   - Footer (update service links)
   - Technical SEO implementation
   - Content Source: `updated-docs/5_Web_Designer_Instructions_Part5_Global_Updates.json`

---

## PROJECT 2: New Micro-Site (constructionsafety.consulting)

### Scope
- **3-page standalone site** on new domain
- Separate branding (Navy blue + Safety orange)
- Focused on construction safety consulting

### Pages
1. **Homepage** - `constructionsafety.consulting/`
2. **SSHO Services** - `constructionsafety.consulting/ssho-services/`
3. **Safety Representatives** - `constructionsafety.consulting/safety-representatives/`

### Content Location
All files in: `constructionsafety_consultingwebsite/`

---

## Key Documents Created

### Implementation Guides
- **`IMPLEMENTATION_GUIDE.md`** - Complete overview of both projects, technical requirements, design specs
- **`PAGE_IMPLEMENTATION_SSHO.md`** - Detailed step-by-step guide for implementing the SSHO Services page

### Content Files
- **`updated-docs/`** - All JSON content files for PROJECT 1 (5 pages + master file)
- **`constructionsafety_consultingwebsite/`** - All content files for PROJECT 2

### Reference Documents
- **`CLIENT_REQUIREMENTS_SUMMARY.md`** - Complete client requirements and timeline
- **`IMPLEMENTATION_PLAN.md`** - Technical server details and WordPress setup

---

## Technical Stack

- **CMS:** WordPress
- **Theme:** Hello Elementor
- **Page Builder:** Elementor
- **Host:** Nexcess Managed WordPress
- **SSH:** 832f87585d.nxcli.net
- **WordPress Path:** `/home/a96c427e/832f87585d.nxcli.net/html`

---

## Quick Start Guide

### For PROJECT 1 - Main Site Updates:

1. **Start with SSHO Services Page** (Week 1 - Highest Priority)
   - Read: `PAGE_IMPLEMENTATION_SSHO.md`
   - Use content from: `updated-docs/1_Web_Designer_Instructions_Part1_SSHO.json`
   - Create page in WordPress/Elementor following the guide

2. **Continue with remaining pages** in priority order
   - Each page has a corresponding JSON file in `updated-docs/`
   - Follow the same structure as SSHO page implementation

3. **Complete global updates** (Week 4)
   - Update navigation, footer, homepage, About page
   - Implement technical SEO

### For PROJECT 2 - Micro-Site:

1. **Review content files** in `constructionsafety_consultingwebsite/`
2. **Set up domain:** constructionsafety.consulting
3. **Create 3 pages** using provided content
4. **Apply branding:** Navy blue (#003366) + Safety orange (#FF6600)

---

## Critical Requirements

### Internal Linking (ESSENTIAL for SEO)
- Caltrans Construction Safety page → Link to Lead Compliance Plan page
- Lead Compliance Plan page → Link to Caltrans Construction Safety page
- SSHO Services page → Link to Federal Contracting page
- All service pages → Link to main Construction Safety Consulting page

### Navigation Menu Updates
Add 4 new items to Services dropdown:
1. **SSHO Services** (after Construction Safety Consulting)
2. **Lead Compliance Plan Services** (after SSHO Services)
3. **Caltrans Construction Safety Services** (after Lead Compliance)
4. **Federal Contracting Services** (at end of menu)

### SEO Requirements
- Unique meta title and description for each page (provided in JSON)
- Descriptive alt text for all images
- Structured data (JSON-LD) for LocalBusiness and Service schemas
- Mobile-responsive design
- Fast page load times (< 3 seconds)

---

## Company Information (Use Consistently)

- **Company:** EHS Analytical Solutions, Inc.
- **Phone:** (619) 288-3094
- **Email:** adam@ehsanalytical.com
- **Location:** San Diego, California
- **Service Area:** California (statewide) and Nationwide for federal projects
- **Certifications:** DVBE #2017031, SDVOSB, CIH, CSP
- **Founded:** Veteran-owned (USMC & Navy)

---

## Design Requirements

### Color Scheme
- **Primary:** Navy blue (#003366) for headings, navigation
- **Accent:** Professional blue/teal for CTAs
- **Background:** White with light gray sections (#F5F5F5)
- **Text:** Dark gray (#333333)

### Typography
- **Headings:** Bold, professional sans-serif (Montserrat, Roboto, Open Sans)
- **Body:** Clean, readable sans-serif (16px minimum, 1.6 line-height)

---

## Content Format

All content is provided in **JSON format** for easy programmatic parsing:
- Individual page files: `1_Web_Designer_Instructions_Part1_SSHO.json`, etc.
- Master file: `ALL_PAGES_MASTER.json` (contains all 5 pages)

Each JSON file contains:
- `technical` - URL, page title, meta description, H1, keywords
- `sections` - All page content organized by section type
- `navigation` - Menu placement information

---

## Next Steps

1. ✅ Review all documentation
2. ✅ Understand project scope and priorities
3. ⏭️ **START IMPLEMENTATION:** Begin with SSHO Services page (Week 1)
4. ⏭️ Follow implementation guides for each page
5. ⏭️ Complete global updates (Week 4)
6. ⏭️ Begin PROJECT 2 (micro-site) after PROJECT 1 is complete

---

## Contact

**Adam Fillmore**  
CIH, CSP, CAC, PMP  
President & Director of EHS Services  
EHS Analytical Solutions, Inc.  
Phone: (619) 288-3094  
Email: adam@ehsanalytical.com

---

## File Structure

```
/ehs/
├── IMPLEMENTATION_GUIDE.md          # Complete implementation overview
├── PAGE_IMPLEMENTATION_SSHO.md      # SSHO page step-by-step guide
├── PROJECT_SUMMARY.md               # This file
├── CLIENT_REQUIREMENTS_SUMMARY.md   # Client requirements
├── IMPLEMENTATION_PLAN.md           # Technical details
│
├── updated-docs/                    # PROJECT 1 content files
│   ├── 1_Web_Designer_Instructions_Part1_SSHO.json
│   ├── 2_Web_Designer_Instructions_Part2_LeadCompliance.json
│   ├── 3_Part3_Caltrans_Construction_Safety_NEW.json
│   ├── 4_Web_Designer_Instructions_Part4_Federal_Contracting.json
│   ├── 5_Web_Designer_Instructions_Part5_Global_Updates.json
│   ├── ALL_PAGES_MASTER.json
│   ├── README_FOR_WEB_DEVELOPER.txt
│   └── CLAUDE_CODE_PROMPT_FOR_WEB_DEVELOPER.txt
│
└── constructionsafety_consultingwebsite/  # PROJECT 2 content files
    ├── MASTER_IMPLEMENTATION_GUIDE.txt
    ├── Page1_Homepage_Content.txt
    ├── Page2_SSHO_Services_Content.txt
    ├── Page3_Safety_Representatives_Content.txt
    ├── Technical_SEO_Specifications.txt
    ├── Internal_Linking_Strategy.txt
    ├── Design_and_Branding_Guide.txt
    └── FINAL_PACKAGE_SUMMARY_ConstructionSafety.txt
```

---

**Ready to begin implementation! Start with `PAGE_IMPLEMENTATION_SSHO.md` for the highest priority page.**
