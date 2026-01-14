# EHS Analytical Solutions - Project Organization

**Date:** January 2025  
**Status:** Organized and Ready for Implementation

---

## Folder Structure

```
project-organization/
├── 01-main-site-wordpress/          # PROJECT 1: Main site updates (ehsanalytical.com)
│   ├── content-source/              # All content files for WordPress pages
│   │   ├── updated-docs/           # JSON files (structured content)
│   │   └── original-files/         # Original .docx and .txt files
│   ├── implementation-guides/       # Step-by-step implementation guides
│   └── ssh-commands/               # SSH/WordPress CLI commands
│
├── 02-micro-site-vercel/           # PROJECT 2: Micro-site (constructionsafety.consulting)
│   ├── source-content/             # Original content files
│   └── deployed-site/              # Ready-to-deploy static HTML files
│
└── 03-archive/                      # Old files and drafts
    ├── old-html-files/             # Previous HTML drafts
    ├── old-content/                # Old content files and zips
    └── emails/                     # Original email requests
```

---

## PROJECT 1: Main Site Updates (WordPress)

### Content Source Files

**Location:** `01-main-site-wordpress/content-source/`

#### JSON Files (updated-docs/)
- `1_Web_Designer_Instructions_Part1_SSHO.json` - SSHO Services page
- `2_Web_Designer_Instructions_Part2_LeadCompliance.json` - Lead Compliance page
- `3_Part3_Caltrans_Construction_Safety_NEW.json` - Caltrans Safety page
- `4_Web_Designer_Instructions_Part4_Federal_Contracting.json` - Federal Contracting page
- `5_Web_Designer_Instructions_Part5_Global_Updates.json` - Global updates
- `ALL_PAGES_MASTER.json` - All pages in one file

#### Original Files (original-files/)
- `.docx` files - Original Word documents
- `.txt` files - Text versions of content
- `MASTER_SUMMARY_For_Web_Developer.txt` - Overview document

### Implementation Guides

**Location:** `01-main-site-wordpress/implementation-guides/`

- `PAGE_IMPLEMENTATION_SSHO.md` - Detailed SSHO page guide
- `IMPLEMENTATION_GUIDE.md` - Complete implementation overview
- `SSH_WORDPRESS_COMMANDS.md` - WordPress CLI commands

---

## PROJECT 2: Micro-Site (Vercel)

### Source Content

**Location:** `02-micro-site-vercel/source-content/`

Original content files for the 3-page micro-site:
- `Page1_Homepage_Content.txt`
- `Page2_SSHO_Services_Content.txt`
- `Page3_Safety_Representatives_Content.txt`
- Design and branding guides
- Technical SEO specifications

### Deployed Site

**Location:** `02-micro-site-vercel/deployed-site/`

Ready-to-deploy static HTML files:
- `index.html` - Homepage
- `ssho-services.html` - SSHO Services page
- `safety-representatives.html` - Safety Representatives page
- `styles.css` - Stylesheet
- `script.js` - JavaScript
- `vercel.json` - Deployment config
- `README.md` - Deployment instructions

**Deploy with:**
```bash
cd 02-micro-site-vercel/deployed-site
vercel
```

---

## Archive

**Location:** `03-archive/`

Old files, drafts, and previous versions:
- `old-html-files/` - Previous HTML drafts
- `old-content/` - Old content files and zips
- `emails/` - Original email requests

---

## Quick Reference

### Main Site (WordPress) - Start Here
1. Read: `01-main-site-wordpress/implementation-guides/PAGE_IMPLEMENTATION_SSHO.md`
2. Use content from: `01-main-site-wordpress/content-source/updated-docs/1_Web_Designer_Instructions_Part1_SSHO.json`
3. Follow: `01-main-site-wordpress/implementation-guides/SSH_WORDPRESS_COMMANDS.md`

### Micro-Site (Vercel) - Deploy
1. Navigate to: `02-micro-site-vercel/deployed-site/`
2. Deploy: `vercel`
3. Add domain: `constructionsafety.consulting`

---

## Content Verification

✅ **SSHO Services Page** - Content verified from JSON  
✅ **Micro-Site Pages** - Content verified from source files  
⏭️ **Remaining Pages** - Need to verify JSON completeness

**Note:** Some JSON files (Part 2, 4, 5) appear incomplete. Use original .docx/.txt files in `original-files/` if JSON is missing content.

---

## Special Domains Reference

**See:** `DOMAINS_REFERENCE.md` for complete domain information

**Quick Summary:**
- **Main Site:** `ehsanalytical.com` (WordPress - existing)
- **Micro-Site:** `constructionsafety.consulting` (Vercel - new)
- **Redirect:** `onsitesafety.consulting` → `constructionsafety.consulting` (301 redirect needed)

---

## Next Steps

1. ✅ Folder structure organized
2. ✅ Domain information documented (see `DOMAINS_REFERENCE.md`)
3. ⏭️ Verify all JSON files have complete content
4. ⏭️ Complete WordPress page implementations
5. ⏭️ Deploy micro-site to Vercel
