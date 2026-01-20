# Service Content Migration Guide

This document explains how to migrate service page content from the live EHS Analytical site to the dev environment.

## Overview

The migration script (`ehs-wordpress-local/wordpress/migrate-service-content-complete.php`) extracts structured content from live Elementor pages and transforms it into the dev site's design system.

## What Gets Migrated

| Content Type | Source Widget | Dev Output |
|--------------|---------------|------------|
| Section headings | `elementor-widget-heading` | `<h2 class="service-section-heading">` |
| Body text | `elementor-widget-text-editor` | `<p class="service-text">` (preserves bold/italic) |
| Bullet lists | `elementor-widget-icon-list` | `<ul class="service-content-list">` |
| Single images | `elementor-widget-image` | `<div class="service-image-container">` |
| Image galleries | `elementor-widget-gallery` | `<div class="service-image-gallery">` (2-column grid) |
| Project timelines | `elementor-widget-jet-timeline` | Uses `ehs_render_project_timeline()` component |

## Configuration

Edit the top of `migrate-service-content-complete.php`:

```php
// ===========================================
// CONFIGURATION - Edit these settings
// ===========================================
$dry_run = true;   // Set to false to actually save changes
$verbose = true;   // Show detailed output
$single_service = 'construction-safety-consulting';  // Set to null for all services
// ===========================================
```

## Service Mapping

The script contains a mapping of service slugs to live URLs and dev post IDs:

```php
$services = [
    'construction-safety-consulting' => [
        'live_url' => 'https://ehsanalytical.com/construction-safety-consulting/',
        'dev_post_id' => 3277
    ],
    'caltrans-construction-safety-services' => [
        'live_url' => 'https://ehsanalytical.com/caltrans-construction-safety-services/',
        'dev_post_id' => 3279
    ],
    // ... more services
];
```

## Running the Migration

### 1. Dry Run (Preview)
```bash
# Set $dry_run = true in the script, then:
cd ehs-wordpress-local
ddev exec "wp eval-file /var/www/html/wordpress/migrate-service-content-complete.php --path=/var/www/html/wordpress"
```

### 2. Single Service Migration
```bash
# Set $single_service = 'service-slug' and $dry_run = false
ddev exec "wp eval-file /var/www/html/wordpress/migrate-service-content-complete.php --path=/var/www/html/wordpress"
```

### 3. All Services Migration
```bash
# Set $single_service = null and $dry_run = false
ddev exec "wp eval-file /var/www/html/wordpress/migrate-service-content-complete.php --path=/var/www/html/wordpress"
```

## Output Example

```
[1/1] Migrating: construction-safety-consulting
  Live URL: https://ehsanalytical.com/construction-safety-consulting/
  Dev Post ID: 3277
  Fetching HTML...
  HTML fetched: 148089 bytes
  Extracting structured content...
  Found 7 sections
    Section 0: Construction Safety Consulting...
      Content items: 14
  Processing images...
  Processed 8/8 images
  Building dev design system HTML...
  Updating WordPress post...
  Verification:
    Sections: 12 (from 7 live)
    Paragraphs: 5
    Images: 1
    Word count: 1201
  Done.
```

## CSS Classes Used

The migration outputs content using these theme CSS classes (defined in `style.css`):

### Text Content
- `.service-section-heading` - Section headers (h2)
- `.service-text` - Body paragraphs
- `.service-content-list` - Bullet lists with gold markers
- `.service-sublist` - Nested list items

### Images
- `.service-image-container` - Single image wrapper
- `.service-image` - Single image with shadow/radius
- `.service-image-gallery` - 2-column image grid
- `.service-gallery-item` - Gallery item wrapper
- `.service-gallery-image` - Gallery image with hover effect

### Components
- Project timeline uses `ehs_render_project_timeline()` function
- Located in `inc/frontend/service-components-render.php`

## Image Handling

- Images are downloaded from live site and uploaded to dev media library
- Existing images (by filename) are reused, not duplicated
- Small icons (<50px), badges (150x150), and placeholders are skipped
- Gallery images have size suffixes removed to get full-size URLs

## Content Filtering

The script automatically skips:
- Navigation/menu widgets
- Sticky header sections
- Footer widgets
- Duplicate headings
- All-lowercase navigation headings (e.g., "our services")

## Adding New Services

1. Create the service post in WordPress admin on dev
2. Note the post ID
3. Add entry to `$services` array:
   ```php
   'new-service-slug' => [
       'live_url' => 'https://ehsanalytical.com/new-service-slug/',
       'dev_post_id' => XXXX
   ],
   ```
4. Run migration for that service

## Troubleshooting

### "Failed to fetch HTML"
- Check if live URL is accessible
- Verify no firewall blocking

### "Failed to extract content"
- Live page may use different Elementor structure
- Check selectors in `extract_main_content()`

### Missing content
- Widget type may not be handled
- Add new widget parser in `extract_structured_content()`

### Images not showing
- Check if image URL is being filtered out
- Verify media library upload succeeded

## Backup Before Migration

Always backup before running on multiple services:
```bash
cd ehs-wordpress-local
ddev export-db --file=../backup-pre-migration-$(date +%Y%m%d).sql.gz
```
