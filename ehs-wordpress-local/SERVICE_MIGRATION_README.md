# Service Pages Migration Guide

This guide walks you through converting regular WordPress pages to the Services custom post type.

## Overview

The Services custom post type has been set up with:
- ✅ Custom post type registration with URL preservation
- ✅ Meta fields for service details (category, area, certifications, etc.)
- ✅ Custom template (`single-services.php`) with child theme CSS
- ✅ Reusable content block functions
- ✅ Sidebar navigation menu
- ✅ Migration scripts

## Files Created

### Theme Files
- `single-services.php` - Service post template
- `style.css` - Updated with service page CSS classes
- `inc/post-types/services-post-type.php` - Updated with URL rewrite rules
- `inc/frontend/service-content-blocks.php` - Reusable content functions
- `functions.php` - Updated to include content blocks

### Migration Scripts
- `identify-service-pages.php` - Identifies pages to convert
- `migrate-service-pages.php` - Converts pages to Services post type

## Migration Process

### Step 1: Identify Service Pages

Run the identification script to find all service pages:

**Via Browser:**
```
https://ehs-local.ddev.site/identify-service-pages.php
```

**Via WP-CLI:**
```bash
cd ehs-wordpress-local
ddev exec wp eval-file identify-service-pages.php
```

This will generate a report showing:
- Known service pages (from documentation)
- Likely service pages (matching keywords)
- Possible service pages (for manual review)
- Recommended migration list

### Step 2: Update Migration Script

Copy the recommended slug list from the identification report and update `migrate-service-pages.php`:

```php
$service_page_slugs = array(
    'ssho-services-california',
    'lead-compliance-plan-services',
    'caltrans-construction-safety-services',
    'federal-contracting-sdvosb',
    'construction-safety-consulting',
    // Add more slugs as identified
);
```

### Step 3: Test Migration with One Page

Before migrating all pages, test with a single page first:

1. Edit `migrate-service-pages.php` and comment out all slugs except one:
   ```php
   $service_page_slugs = array(
       'construction-safety-consulting', // Test with this one first
       // 'ssho-services-california',
       // ... (comment out others)
   );
   ```

2. Run the migration script:

   **Via Browser:**
   ```
   https://ehs-local.ddev.site/migrate-service-pages.php
   ```

   **Via WP-CLI:**
   ```bash
   cd ehs-wordpress-local
   ddev exec wp eval-file migrate-service-pages.php
   ```

3. Review the migrated service:
   - Go to WordPress admin: Services menu
   - Find the newly created service (will be in Draft status)
   - Review content, featured image, and metadata
   - Edit and add service meta fields:
     - Service Category
     - Service Area
     - Certifications
     - Target Audience
     - Related Services
     - Service Order

#### Optional: Auto-generate missing service meta

If your Services posts exist but are missing the service meta fields, you can generate them from the original service page content and export them to JSON:

```bash
cd ehs-wordpress-local
ddev exec wp --path=wordpress eval-file export-services-meta.php
```

This creates a JSON file under `ehs-wordpress-local/exports/services-meta/` that you can import into another environment.

To apply an export file to the current site (dry run by default):

```bash
cd ehs-wordpress-local
ddev exec wp --path=wordpress eval-file import-services-meta.php
```

To apply changes, set `EHS_SERVICES_META_APPLY=1` and (optionally) choose a specific file:

```bash
cd ehs-wordpress-local
ddev exec bash -lc 'EHS_SERVICES_META_APPLY=1 EHS_SERVICES_META_FILE=services-meta-YYYYMMDD-HHMMSS.json wp --path=wordpress eval-file import-services-meta.php'
```

#### Optional: Import and assign SVG icons

A starter SVG icon set lives at:

- `wordpress/wp-content/themes/hello-elementor-child/assets/service-icons/`

To import them into the Media Library:

```bash
cd ehs-wordpress-local
ddev exec bash -lc 'wp --path=wordpress media import wordpress/wp-content/themes/hello-elementor-child/assets/service-icons/*.svg'
```

To auto-assign icons to the known service slugs (dry run by default):

```bash
cd ehs-wordpress-local
ddev exec wp --path=wordpress eval-file assign-service-icons.php
```

To apply assignments:

```bash
cd ehs-wordpress-local
ddev exec bash -lc 'EHS_ASSIGN_ICONS_APPLY=1 wp --path=wordpress eval-file assign-service-icons.php'
```

4. Publish the service when ready

5. Test the URL:
   ```
   https://ehs-local.ddev.site/construction-safety-consulting/
   ```

6. Verify:
   - URL works and loads the service
   - Layout matches the Elementor design
   - Hero section displays correctly
   - Sidebar navigation appears
   - Content is properly formatted
   - Responsive design works on mobile

### Step 4: Flush Rewrite Rules

After creating the first service, flush rewrite rules:

1. Go to WordPress admin: Settings → Permalinks
2. Click "Save Changes" (don't change anything)
3. This flushes the rewrite rules and ensures URLs work

### Step 5: Migrate Remaining Pages

Once you've verified the first migration works:

1. Uncomment all slugs in `migrate-service-pages.php`
2. Run the migration script again
3. Review each migrated service in WordPress admin
4. Add meta fields for each service
5. Publish services when ready

### Step 6: Update Navigation Menus

After migrating services, update navigation menus:

1. Go to WordPress admin: Appearance → Menus
2. Find service page menu items
3. Remove old page links
4. Add new Services post links
5. Save menu

**Service Menu Order (from documentation):**
- Environmental Health and Safety (EHS) Consulting
- EHS Staff Outsourcing
- Construction Safety Consulting
- SSHO Services ← NEW
- Lead Compliance Plan Services ← NEW
- Caltrans Construction Safety Services ← NEW
- Industrial Hygiene Services
- Indoor Air Quality Testing
- Mold Testing
- Asbestos Testing
- Water Damage Assessments
- Fire & Smoke Assessments
- Federal Contracting Services ← NEW

### Step 7: Verify All URLs

Test each migrated service URL to ensure:
- URL works (no 404 errors)
- Content displays correctly
- Layout matches design
- Sidebar navigation works
- Links to other services work

### Step 8: Clean Up Old Pages

After verifying all services work correctly:

1. The migration script automatically sets old pages to Draft status
2. Review old pages in WordPress admin: Pages → All Pages
3. Look for pages with "-old" suffix in slug
4. Verify services are working
5. Delete old page posts when confident

## CSS Classes Reference

The following CSS classes are available for service pages:

### Layout Classes
- `.service-hero` - Full-width hero section with background image
- `.service-container` - Main container (max-width: 1200px)
- `.service-layout` - Two-column flex layout
- `.service-sidebar` - Sidebar navigation (280px wide, sticky)
- `.service-content` - Main content area

### Section Classes
- `.service-section` - Generic section wrapper
- `.service-section-3col` - Three-column grid layout
- `.service-col` - Individual column in 3-col layout
- `.service-list` - Two-column service list grid
- `.service-list-item` - Individual service list item

### Component Classes
- `.service-faq` - FAQ section wrapper
- `.service-faq-item` - Individual FAQ item
- `.service-faq-question` - FAQ question button
- `.service-faq-answer` - FAQ answer content
- `.service-cta` - Call-to-action section
- `.service-cta-button` - CTA button

### Sidebar Classes
- `.service-sidebar-menu` - Sidebar navigation list
- `.service-sidebar-menu a.current` - Current/active service link

## Content Block Functions

Use these functions in your service templates or content:

```php
// Hero section
ehs_service_hero($title, $subtitle, $text, $image_url);

// Three-column section
ehs_service_why_choose_us($columns, $section_title);

// Service list/grid
ehs_service_list($services, $section_title);

// FAQ accordion
ehs_service_faq($faqs, $section_title);

// Call-to-action
ehs_service_cta($title, $text, $button_text, $button_url);

// Sidebar navigation
ehs_service_sidebar_menu();

// Generic section
ehs_service_section($title, $content, $layout);
```

## Troubleshooting

### URLs Not Working (404 Errors)

1. Flush rewrite rules:
   - Go to Settings → Permalinks
   - Click "Save Changes"

2. Check post status:
   - Ensure service is Published (not Draft)

3. Verify slug:
   - Check service post_name matches expected URL

### Layout Not Displaying Correctly

1. Check template file:
   - Ensure `single-services.php` exists in child theme root

2. Verify CSS:
   - Check `style.css` has service page styles

3. Clear cache:
   - Clear browser cache
   - Clear WordPress cache
   - Clear Object Cache Pro (if enabled)

### Sidebar Not Showing

1. Check if services exist:
   - Need at least one published service

2. Verify function is called:
   - `ehs_service_sidebar_menu()` in template

3. Check service_order meta:
   - Set service_order for proper sorting

### Content Not Migrating

1. Check original page exists:
   - Verify page slug is correct

2. Review migration script output:
   - Look for error messages

3. Check post content:
   - May need manual cleanup of Elementor shortcodes

## Next Steps After Migration

1. **Add Service Meta Fields**
   - Category (Construction Safety, Environmental, etc.)
   - Service Area (California, Federal, All)
   - Certifications (DVBE, SDVOSB, CIH, CSP)
   - Target Audience
   - Related Services
   - Service Order (for sidebar menu sorting)

2. **Optimize Content**
   - Remove Elementor shortcodes if present
   - Add proper heading hierarchy (H2, H3)
   - Format content with service CSS classes
   - Add FAQ sections using `ehs_service_faq()`
   - Add CTA sections using `ehs_service_cta()`

3. **SEO Optimization**
   - Verify meta titles and descriptions (Yoast SEO)
   - Add alt text to images
   - Check internal linking
   - Test mobile responsiveness

4. **Test Cross-Linking**
   - Caltrans Construction Safety → Lead Compliance Plan
   - Lead Compliance Plan → Caltrans Construction Safety
   - SSHO Services → Federal Contracting
   - All services → Construction Safety Consulting

## Support

For issues or questions:
1. Review this README
2. Check WordPress error logs
3. Test in DDEV local environment first
4. Verify all files are in place
5. Check browser console for JavaScript errors
