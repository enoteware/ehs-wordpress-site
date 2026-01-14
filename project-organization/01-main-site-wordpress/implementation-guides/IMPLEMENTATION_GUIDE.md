# EHS Analytical Solutions - Complete Implementation Guide

**Date:** December 2025  
**Client:** EHS Analytical Solutions, Inc.  
**Site:** https://ehsanalytical.com (WordPress + Elementor)

---

## Project Overview

### PROJECT 1: Main Site Updates (ehsanalytical.com)
- **4 NEW pages** to create
- **1 EXISTING page** to update
- **Global updates** (navigation, footer, homepage, About page)

### PROJECT 2: New Micro-Site (constructionsafety.consulting)
- **3-page standalone site**
- Separate domain and branding

---

## Technical Stack

- **CMS:** WordPress
- **Theme:** Hello Elementor
- **Page Builder:** Elementor
- **Host:** Nexcess Managed WordPress
- **SSH:** 832f87585d.nxcli.net
- **WordPress Path:** `/home/a96c427e/832f87585d.nxcli.net/html`

---

## Implementation Priority & Timeline

### WEEK 1: SSHO Services Page (HIGHEST PRIORITY)
**URL:** `/ssho-services-california/`  
**File:** `updated-docs/1_Web_Designer_Instructions_Part1_SSHO.json`

### WEEK 2: Lead Compliance Plan Services Page
**URL:** `/lead-compliance-plan-services/`  
**File:** `updated-docs/2_Web_Designer_Instructions_Part2_LeadCompliance.json`

### WEEK 3: Caltrans Construction Safety + Federal Contracting
**URLs:** 
- `/caltrans-construction-safety-services/` (NEW)
- `/federal-contracting-sdvosb/` (NEW)
- Update existing Construction Safety Consulting page

**Files:**
- `updated-docs/3_Part3_Caltrans_Construction_Safety_NEW.json`
- `updated-docs/4_Web_Designer_Instructions_Part4_Federal_Contracting.json`

### WEEK 4: Global Updates
**File:** `updated-docs/5_Web_Designer_Instructions_Part5_Global_Updates.json`

- Navigation menu updates
- About Us page updates
- Homepage updates
- Footer updates
- Technical SEO implementation

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

### Layout Patterns
- **Hero Section:** Full-width background, H1 heading, subheading, 1-2 CTA buttons
- **3-Column Benefits:** Desktop 3 columns, Tablet 2 columns, Mobile 1 column
- **FAQ Section:** Accordion or Q&A format
- **CTA Sections:** Full-width background, centered text, prominent buttons

---

## Critical Internal Linking

These cross-links are **ESSENTIAL** for SEO:

1. **Caltrans Construction Safety page** → Link to Lead Compliance Plan page
2. **Lead Compliance Plan page** → Link to Caltrans Construction Safety page
3. **SSHO Services page** → Link to Federal Contracting page
4. **All service pages** → Link to main Construction Safety Consulting page

---

## Navigation Menu Structure

**Services (dropdown menu):**
- Environmental Health and Safety (EHS) Consulting (EXISTING)
- EHS Staff Outsourcing (EXISTING)
- Construction Safety Consulting (EXISTING)
- **SSHO Services** ← NEW (add after Construction Safety)
- **Lead Compliance Plan Services** ← NEW (add after SSHO)
- **Caltrans Construction Safety Services** ← NEW (add after Lead Compliance)
- Industrial Hygiene Services (EXISTING)
- Indoor Air Quality Testing (EXISTING)
- Mold Testing (EXISTING)
- Asbestos Testing (EXISTING)
- Water Damage Assessments (EXISTING)
- Fire & Smoke Assessments (EXISTING)
- **Federal Contracting Services** ← NEW (add at end)

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

## SEO Requirements

### Technical SEO
- Unique `<title>` tag for each page (from JSON files)
- Unique meta description for each page (from JSON files)
- Descriptive alt text for all images
- Structured data (LocalBusiness schema, Service schema)
- Canonical URLs
- Open Graph tags for social sharing

### Target Keywords
- "SSHO services California"
- "SDVOSB SSHO"
- "Caltrans Lead Compliance Plan"
- "DVBE safety services"
- "Caltrans Safety Representative"
- "USACE SSHO"
- "NAVFAC construction safety"

---

## Implementation Checklist (Per Page)

- [ ] Create page in WordPress
- [ ] Set URL slug (from JSON technical.url)
- [ ] Set page title (from JSON technical.pageTitle)
- [ ] Set meta description (from JSON technical.metaDescription)
- [ ] Build page structure in Elementor
- [ ] Add hero section with H1 heading
- [ ] Add all content sections from JSON
- [ ] Add internal links to related pages
- [ ] Add CTA buttons with contact info
- [ ] Add images with descriptive alt text
- [ ] Add structured data (JSON-LD)
- [ ] Test responsive design (mobile, tablet, desktop)
- [ ] Validate accessibility
- [ ] Check page speed
- [ ] Add to navigation menu
- [ ] Update sidebar service menu

---

## Content File Locations

### PROJECT 1 Files
All in `/updated-docs/`:
- `1_Web_Designer_Instructions_Part1_SSHO.json`
- `2_Web_Designer_Instructions_Part2_LeadCompliance.json`
- `3_Part3_Caltrans_Construction_Safety_NEW.json`
- `4_Web_Designer_Instructions_Part4_Federal_Contracting.json`
- `5_Web_Designer_Instructions_Part5_Global_Updates.json`
- `ALL_PAGES_MASTER.json` (contains all 5 pages)

### PROJECT 2 Files
All in `/constructionsafety_consultingwebsite/`:
- `MASTER_IMPLEMENTATION_GUIDE.txt`
- `Page1_Homepage_Content.txt`
- `Page2_SSHO_Services_Content.txt`
- `Page3_Safety_Representatives_Content.txt`
- `Technical_SEO_Specifications.txt`
- `Internal_Linking_Strategy.txt`
- `Design_and_Branding_Guide.txt`

---

## Next Steps

1. **Start with SSHO Services Page** (Week 1 - Highest Priority)
2. Parse JSON content and create Elementor-ready structure
3. Implement page following existing site design patterns
4. Test and iterate
5. Move to next page in priority order

---

## Contact

**Adam Fillmore**  
CIH, CSP, CAC, PMP  
President & Director of EHS Services  
EHS Analytical Solutions, Inc.  
Phone: (619) 288-3094  
Email: adam@ehsanalytical.com
