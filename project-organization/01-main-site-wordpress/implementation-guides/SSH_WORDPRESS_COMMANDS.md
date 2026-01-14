# SSH WordPress Commands - Main Site Updates

**Host:** 832f87585d.nxcli.net  
**WordPress Path:** `/home/a96c427e/832f87585d.nxcli.net/html`  
**SSH User:** `a96c427e_1`

---

## Quick SSH Connection

```bash
ssh a96c427e_1@832f87585d.nxcli.net
cd 832f87585d.nxcli.net/html
```

---

## WP-CLI Commands for New Pages

### 1. Create SSHO Services Page (Week 1 - HIGHEST PRIORITY)

```bash
# Connect via SSH first, then:
cd 832f87585d.nxcli.net/html

# Create the page
wp post create --post_type=page \
  --post_title="SSHO Services California" \
  --post_name="ssho-services-california" \
  --post_status=draft \
  --post_content="<!-- Content will be added via Elementor -->"

# Get the page ID (save this for later)
wp post list --post_type=page --name=ssho-services-california --field=ID
```

**After creating page:**
1. Log into WordPress admin: https://ehsanalytical.com/wp-admin
2. Edit the page with Elementor
3. Use content from: `PAGE_IMPLEMENTATION_SSHO.md`
4. Set meta title and description (use Yoast SEO or similar)

---

### 2. Create Lead Compliance Plan Services Page (Week 2)

```bash
wp post create --post_type=page \
  --post_title="Lead Compliance Plan Services" \
  --post_name="lead-compliance-plan-services" \
  --post_status=draft \
  --post_content="<!-- Content will be added via Elementor -->"
```

---

### 3. Create Caltrans Construction Safety Services Page (Week 3)

```bash
wp post create --post_type=page \
  --post_title="Caltrans Construction Safety Services" \
  --post_name="caltrans-construction-safety-services" \
  --post_status=draft \
  --post_content="<!-- Content will be added via Elementor -->"
```

---

### 4. Create Federal Contracting Services Page (Week 3)

```bash
wp post create --post_type=page \
  --post_title="Federal Contracting Services" \
  --post_name="federal-contracting-sdvosb" \
  --post_status=draft \
  --post_content="<!-- Content will be added via Elementor -->"
```

---

## Update Existing Page

### Update Construction Safety Consulting Page (Week 3)

```bash
# Find the existing page ID
wp post list --post_type=page --name=construction-safety-consulting --field=ID

# Update the page (add note about new sections)
wp post update [PAGE_ID] --post_content="<!-- Add 4 new sections via Elementor -->"
```

**New sections to add:**
1. Markets We Serve
2. Why SDVOSB/DVBE Matters
3. Compliance Expertise
4. Geographic Coverage

---

## Navigation Menu Updates (Week 4)

### Get Current Menu ID

```bash
wp menu list
```

### Add New Menu Items

```bash
# Get menu ID first (from menu list above)
MENU_ID=2  # Replace with actual menu ID

# Get page IDs for new pages
SSHO_PAGE_ID=$(wp post list --post_type=page --name=ssho-services-california --field=ID)
LEAD_PAGE_ID=$(wp post list --post_type=page --name=lead-compliance-plan-services --field=ID)
CALTRANS_PAGE_ID=$(wp post list --post_type=page --name=caltrans-construction-safety-services --field=ID)
FEDERAL_PAGE_ID=$(wp post list --post_type=page --name=federal-contracting-sdvosb --field=ID)

# Add SSHO Services (after Construction Safety)
wp menu item add-post $MENU_ID $SSHO_PAGE_ID \
  --title="SSHO Services" \
  --parent-id=[CONSTRUCTION_SAFETY_MENU_ITEM_ID]

# Add Lead Compliance Plan Services
wp menu item add-post $MENU_ID $LEAD_PAGE_ID \
  --title="Lead Compliance Plan Services" \
  --parent-id=[SSHO_MENU_ITEM_ID]

# Add Caltrans Construction Safety Services
wp menu item add-post $MENU_ID $CALTRANS_PAGE_ID \
  --title="Caltrans Construction Safety Services" \
  --parent-id=[LEAD_MENU_ITEM_ID]

# Add Federal Contracting Services (at end)
wp menu item add-post $MENU_ID $FEDERAL_PAGE_ID \
  --title="Federal Contracting Services"
```

**Note:** Menu item IDs need to be found by listing menu items:
```bash
wp menu item list $MENU_ID
```

---

## Update Meta Titles and Descriptions

### Using WP-CLI (if Yoast SEO is installed)

```bash
# SSHO Services Page
wp post meta update $SSHO_PAGE_ID _yoast_wpseo_title "SSHO Services California | Site Safety Health Officer | SDVOSB"
wp post meta update $SSHO_PAGE_ID _yoast_wpseo_metadesc "SDVOSB-certified SSHO services for federal military construction in California. EM 385-1-1 compliant Site Safety Health Officers. USACE, NAVFAC, VA projects."

# Lead Compliance Plan Services
wp post meta update $LEAD_PAGE_ID _yoast_wpseo_title "[Title from JSON]"
wp post meta update $LEAD_PAGE_ID _yoast_wpseo_metadesc "[Description from JSON]"

# Continue for other pages...
```

### Manual Method (via WordPress Admin)
1. Edit page in WordPress admin
2. Scroll to Yoast SEO section (or similar SEO plugin)
3. Set Focus Keyphrase, SEO Title, Meta Description
4. Use values from JSON files in `updated-docs/`

---

## Update Homepage (Week 4)

```bash
# Get homepage ID
wp post list --post_type=page --post_status=publish --field=ID,post_title | grep -i home

# Or find by slug
HOME_PAGE_ID=$(wp post list --post_type=page --name=home --field=ID)

# Note: Homepage updates should be done via Elementor
# Add 2 new service boxes as specified in Part 5 content
```

---

## Update About Us Page (Week 4)

```bash
# Find About Us page
ABOUT_PAGE_ID=$(wp post list --post_type=page --name=about --field=ID)

# Note: Add federal contractor section via Elementor
# Content from: updated-docs/5_Web_Designer_Instructions_Part5_Global_Updates.json
```

---

## Update Footer (Week 4)

Footer updates typically require:
1. Editing theme files (via SSH)
2. Or using WordPress Customizer
3. Or using Elementor Pro (if footer is built with Elementor)

**Location of footer files:**
```bash
# Check active theme
wp theme list --status=active

# Footer files usually in:
# wp-content/themes/[theme-name]/footer.php
# or
# wp-content/themes/[theme-name]/template-parts/footer.php
```

---

## Useful WP-CLI Commands

### List All Pages
```bash
wp post list --post_type=page --format=table
```

### Get Page by Slug
```bash
wp post get $(wp post list --post_type=page --name=ssho-services-california --field=ID)
```

### Update Page Status (Draft → Publish)
```bash
wp post update $PAGE_ID --post_status=publish
```

### Search and Replace Content
```bash
wp search-replace "old text" "new text" --dry-run  # Test first
wp search-replace "old text" "new text"  # Actually replace
```

### Export/Import (Backup)
```bash
# Export all content
wp export --dir=/tmp/

# Import from file
wp import /path/to/export.xml
```

---

## Elementor-Specific Notes

Since the site uses Elementor:
1. **Pages must be created first** (via WP-CLI above)
2. **Content is added via Elementor** (visual editor)
3. **Elementor data is stored in post meta** (`_elementor_data`)

### View Elementor Data
```bash
# Get Elementor JSON data for a page
wp post meta get $PAGE_ID _elementor_data
```

### Update Elementor Data (Advanced)
```bash
# This is complex - better to use Elementor editor
# Elementor stores JSON in _elementor_data post meta
```

---

## Workflow Summary

### For Each New Page:

1. **Create page via WP-CLI** (commands above)
2. **Set meta title/description** (via WP-CLI or admin)
3. **Edit page in WordPress admin** → Open with Elementor
4. **Build page structure** using content from:
   - `PAGE_IMPLEMENTATION_SSHO.md` (for SSHO page)
   - JSON files in `updated-docs/` (for other pages)
5. **Add internal links** (critical for SEO)
6. **Test responsive design**
7. **Publish page**
8. **Add to navigation menu** (Week 4)

---

## Troubleshooting

### Can't connect via SSH?
- Verify SSH credentials
- Check if SSH access is enabled on Nexcess account
- Try: `ssh -v a96c427e_1@832f87585d.nxcli.net` for verbose output

### WP-CLI not found?
- WP-CLI should be available on Nexcess
- Try: `which wp` to find location
- May need to use full path: `/usr/local/bin/wp` or similar

### Permission denied?
- Check file permissions: `ls -la`
- May need to use `sudo` (if allowed)
- Contact Nexcess support if needed

---

## Next Steps

1. ✅ Connect via SSH
2. ✅ Create SSHO Services page (Week 1)
3. ✅ Add content via Elementor
4. ✅ Continue with remaining pages
5. ✅ Update navigation and global elements (Week 4)
