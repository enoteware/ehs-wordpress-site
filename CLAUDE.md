# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a WordPress-based project for EHS Analytical (ehsanalytical.com), a California-based environmental health and safety consulting firm. The repository manages both the WordPress site and various automation scripts for project management, Elementor template synchronization, and server migration planning.

## Development Environment

### Mac Mini Development Server Setup

**IMPORTANT:** This repository is set up to run on a Mac mini development server, accessible remotely from a MacBook.

**Mac Mini Configuration:**
- Project name: `ehs-mini` (configured in `.ddev/config.yaml`)
- URL: http://ehs-mini.ddev.site
- Local IP: 10.112.1.56 (primary), 10.112.1.27 (secondary)
- Docker Desktop running on Mac mini
- DDEV v1.24.10 with Mutagen sync for performance

**Accessing from MacBook:**

1. Add to MacBook's `/etc/hosts` file:
   ```
   10.112.1.56 ehs-mini.ddev.site
   ```

2. Access via browser:
   - Site: http://ehs-mini.ddev.site
   - Admin: http://ehs-mini.ddev.site/wp-admin
   - Credentials: `a509f58b_admin` / `EHS-Local-Dev-2024!`

3. Optional: Set up wildcard DNS with dnsmasq for all `*.ddev.site` domains

**Local Environment Indicator:**
- Orange banner at top of all pages showing:
  - "LOCAL DEVELOPMENT" warning
  - Server hostname (Mac mini)
  - Current URL
- Admin bar badge showing "LOCAL • [hostname]"
- Only displays when `IS_DDEV_PROJECT=true` environment variable is set

### Local WordPress Development (DDEV)

The main WordPress site runs in a DDEV container located in `ehs-wordpress-local/`.

**Starting the site:**
```bash
cd ehs-wordpress-local
ddev start
```

**Current DDEV Configuration:**
- Project name: `ehs-mini`
- URL: http://ehs-mini.ddev.site
- Admin: http://ehs-mini.ddev.site/wp-admin
- Credentials defined in `.ddev/config.yaml` (WP_ADMIN_USERNAME/WP_ADMIN_PASSWORD)
- Note: This differs from MacBook setup which uses `ehs-local` to avoid conflicts

**Common DDEV commands:**
```bash
ddev stop                    # Stop containers
ddev restart                 # Restart containers
ddev ssh                     # SSH into web container
ddev exec wp [command]       # Run WP-CLI commands
ddev import-db --file=dump.sql  # Import database
ddev export-db               # Export database
ddev logs                    # View logs
```

**Quick Commands:**
```bash
./regen-css.sh               # Regenerate Elementor CSS (from ehs-wordpress-local/)
```

**Working with WordPress:**
```bash
# WP-CLI commands from within container (ddev ssh first) or prefixed with ddev exec
wp plugin list
wp theme list
wp post list --post_type=services
wp user list
wp cache flush
```

### Production Server

Production site hosted on Nexcess Managed WordPress:
- SSH access via credentials in `.env` file
- SSH connection: `ssh a96c427e_1@832f87585d.nxcli.net`
- WordPress path: `/home/a96c427e/832f87585d.nxcli.net/html`
- Currently managing 24 WordPress sites on shared hosting

### Database and Uploads Management

**Database Location:**
- Production backup: `production-database.sql.gz` (root directory)
- Older backup: `ehs-wordpress-local/exports/production-database.sql`
- Always use the most recent timestamped file

**Importing Database to Mac Mini:**
```bash
cd ehs-wordpress-local
ddev import-db --file=/Volumes/nvme_ext_data/code/ehs/production-database.sql.gz
```

**Note:** If database file is gzipped but has `.sql` extension, rename it to `.sql.gz` first:
```bash
file production-database.sql  # Check if gzipped
mv production-database.sql production-database.sql.gz
ddev import-db --file=production-database.sql.gz
```

**Uploads Location:**
- Backup: `uploads-backup.tar.gz` (root directory)
- Active location: `ehs-wordpress-local/wordpress/wp-content/uploads/`

**Restoring Uploads:**
```bash
cd /Volumes/nvme_ext_data/code/ehs
tar -xzf uploads-backup.tar.gz -C ehs-wordpress-local/wordpress/wp-content/
```

**After Importing Database:**
Always activate required plugins and theme:
```bash
ddev exec "wp plugin activate elementor elementor-pro advanced-custom-fields-pro wordpress-seo jet-elements jet-menu jet-tabs menu-icons code-snippets --path=/var/www/html/wordpress"
ddev exec "wp theme activate hello-elementor-child --path=/var/www/html/wordpress"
ddev exec "wp cache flush --path=/var/www/html/wordpress"
```

**Syncing Data Between MacBook and Mac Mini:**
1. Export from MacBook: `ddev export-db --file=production-database.sql.gz`
2. Copy database and uploads to Mac mini repository
3. Import on Mac mini using commands above
4. URLs are automatically correct (ehs-mini.ddev.site) in latest dumps

## Architecture

### WordPress Custom Implementation

**Theme:** hello-elementor-child (child theme of Hello Elementor)
- Location: `ehs-wordpress-local/wordpress/wp-content/themes/hello-elementor-child/`
- Main file: `functions.php` contains all custom functionality

**Custom Post Type: Services**
- Registered in `functions.php` via `ehs_register_services_post_type()`
- Hierarchical structure for organizing EHS services
- Slug: `/services/`
- Custom meta fields:
  - `service_category` - Category (Construction Safety, Environmental, etc.)
  - `service_short_description` - Brief description
  - `service_icon` - Icon image (attachment ID)
  - `service_area` - Geographic area (California/Federal/All)
  - `service_certifications` - Relevant certifications
  - `service_target_audience` - Target clients
  - `service_related_services` - Comma-separated post IDs
  - `service_featured` - Boolean for featured status
  - `service_order` - Integer for menu ordering

**Must-Use Plugins:**
- Location: `ehs-wordpress-local/wordpress/wp-content/mu-plugins/`
- `auto-login.php` - Auto-login for local development
- `local-dev-fixes.php` - Suppresses PHP deprecation warnings in local environment

**Local Environment Indicator:**
- File: `inc/frontend/ddev-local-header-bar.php`
- Orange banner at top of all pages (fixed position, z-index 999999)
- Shows server hostname and current URL
- Admin bar badge showing "LOCAL • [hostname]"
- Only displays when `IS_DDEV_PROJECT=true` environment variable is set
- Automatically included via `functions.php`

**Key WordPress Plugins:**
- Elementor Pro - Page builder
- Advanced Custom Fields Pro - Custom fields
- Yoast SEO Premium - SEO optimization
- Object Cache Pro - Performance caching
- Various Elementor add-ons (Jet Elements, Piotnet Addons, etc.)

### Task Management System

Custom Node.js scripts interact with external API to manage web development tasks and checklists.

**API Configuration:**
- Base URL: `env.AGENT_BASE_URL`
- Authentication: `AGENT_API_KEY` and `AGENT_USER_ID` from `.env`
- All requests include `x-request-id` and optional `x-idempotency-key` headers

**Key Scripts:**
```bash
node create-task-checklists.js        # Create checklists for project tasks
node add-checklists.js                # Add checklist items to tasks
node update-tasks.js                  # Update task information
node review-tasks.js                  # Review task status
node verify-checklists.js             # Verify checklist structure
node check-project-6.js               # Check Project 6 specific tasks
```

**Task Structure:**
- Tasks are organized by project (e.g., Project 6)
- Each task has multiple checklist items
- Checklist items track granular implementation steps
- Common task types: service page creation, content implementation, SEO optimization

### Elementor Template Management

Scripts for syncing Elementor Theme Builder templates between environments.

**Export templates from production:**
```bash
cd ehs-wordpress-local
./export-templates-manual.sh
```

**Import templates to local:**
```bash
cd ehs-wordpress-local
./import-elementor-templates.sh
```

**Full sync (export + import):**
```bash
cd ehs-wordpress-local
./sync-elementor-templates.sh
```

**Template sync workflow:**
1. Connects to production via SSH (uses sshpass if available)
2. Runs WP-CLI to export templates as JSON
3. Downloads JSON files to `exports/` directory
4. Imports into local DDEV site via WP-CLI
5. Assigns templates to appropriate locations (header/footer/single/archive)

**Key PHP scripts:**
- `export-elementor-templates.php` - Exports templates from production
- `import-elementor-templates.php` - Imports templates to local
- `assign-theme-builder-templates.php` - Assigns templates to locations
- `copy-meta-data.php` - Copies post meta data

### Quick CSS Regeneration

After updating Elementor templates or button styles, regenerate CSS:

```bash
cd ehs-wordpress-local
./regen-css.sh
```

This command:
- Clears Elementor files cache
- Regenerates Elementor CSS files
- Clears Elementor transients
- Clears WordPress object cache
- Runs Elementor flush-css command

**Alternative:** `ddev exec wp eval-file regen-elementor-css.php`

### Design System & Elementor Integration

**CRITICAL:** This project uses a strict separation between Elementor and theme CSS. All styling is controlled by theme CSS, not Elementor's Style settings.

**Architecture:**
- **Elementor's Role:** Structure, layout, widgets, content organization, responsive breakpoints
- **Theme CSS Role:** All visual styling (colors, typography, spacing, effects, borders, shadows)
- **Integration Method:** Apply CSS classes via Elementor's "Advanced → CSS Classes" field

**Design System Documentation:**
- **Complete Style Guide:** `ehs-wordpress-local/style-guide.html` - Visual reference with all components
- **Quick Reference:** `ehs-wordpress-local/DESIGN_SYSTEM.md` - Developer quick reference
- **Theme CSS:** `wordpress/wp-content/themes/hello-elementor-child/style.css` - All styling definitions

**How to Style Elementor Widgets:**

1. **DO NOT use Elementor's Style tab for:**
   - Colors (background, text, border)
   - Typography (font family, size, weight)
   - Spacing (padding, margin) - unless for layout only
   - Effects (shadows, borders)

2. **DO use Elementor's Style tab for:**
   - Layout properties (width, alignment, positioning)
   - Responsive visibility
   - Content organization

3. **Apply styling via CSS classes:**
   - Go to widget → Advanced → CSS Classes
   - Add theme CSS classes (e.g., `btn btn-primary btn-md`)
   - Reference `style-guide.html` or `DESIGN_SYSTEM.md` for available classes

**Available CSS Classes:**

**Buttons:**
- `.btn.btn-primary` - Navy background, green hover
- `.btn.btn-secondary` - Gold background, green hover
- `.btn.btn-outline` - Transparent with navy border
- `.btn-sm`, `.btn-md`, `.btn-lg` - Size modifiers

**Forms:**
- `.form-input` - Text/email inputs
- `.form-textarea` - Textarea fields
- `.form-select` - Select dropdowns
- `.form-label` - Form labels
- `.form-error` - Error state
- `.form-success` - Success state

**Cards:**
- `.card` - Standard card container
- `.card-title` - Card heading
- `.card-content` - Card body text

**Brand Colors (CSS Variables):**
- `var(--ehs-navy)` - #003366
- `var(--ehs-gold)` - #FFB81C
- `var(--ehs-light-gray)` - #F5F5F5
- `var(--ehs-dark-gray)` - #333333
- `var(--ehs-white)` - #FFFFFF

**Spacing Scale:**
Use these values consistently: 4px, 8px, 12px, 16px, 20px, 24px, 32px, 40px, 60px, 80px

**Typography:**
- Headings: Maven Pro, 700 weight, #003366 color
- Body: Maven Pro, 400 weight, #333333 color
- See `style-guide.html` for complete typography hierarchy

**Clearing Elementor Site Settings:**
If Elementor Site Settings need to be cleared (to ensure theme CSS takes full control):
```bash
cd ehs-wordpress-local
ddev exec wp eval-file clear-elementor-site-settings.php --path=/var/www/html/wordpress
```

**Best Practices:**
- Always reference `style-guide.html` before creating new styles
- Add new CSS classes to `style.css` with clear comments
- Document new classes in `DESIGN_SYSTEM.md`
- Never set colors/typography in Elementor Style tab
- Use CSS variables for brand colors
- Follow spacing scale values
- Test in Elementor by applying CSS classes

**Example Workflow:**
1. Need to style a button? Check `style-guide.html` for button variants
2. Add Button widget in Elementor
3. Go to Advanced → CSS Classes, add `btn btn-primary btn-md`
4. Leave Style tab colors/typography empty
5. Widget now uses theme CSS styling

### Elementor CLI Commands

Elementor integrates with WP-CLI for command-line operations. All commands work via DDEV.

**Core Elementor CLI Commands:**
```bash
# Clear and regenerate all CSS files
ddev exec wp elementor flush-css

# Replace URLs across all Elementor data (faster than search-replace)
ddev exec wp elementor replace-urls --from=https://oldsite.com --to=https://newsite.com

# Sync Elementor template library
ddev exec wp elementor sync-library

# Update Elementor database structure
ddev exec wp elementor update-db

# Import/Export Kit (templates + settings)
ddev exec wp elementor import-kit /path/to/kit.zip
ddev exec wp elementor export-kit --output=/path/to/export.zip
```

**Production Server Commands:**
```bash
# Via SSH on production
ssh a96c427e_1@832f87585d.nxcli.net "cd /home/a96c427e/832f87585d.nxcli.net/html && wp elementor flush-css --allow-root"

# Replace URLs on production (use with caution)
ssh a96c427e_1@832f87585d.nxcli.net "cd /home/a96c427e/832f87585d.nxcli.net/html && wp elementor replace-urls --from=old.com --to=new.com --allow-root"
```

**Common Debugging Commands:**
```bash
# Regenerate CSS after template changes
ddev exec wp elementor flush-css

# Clear Elementor cache
ddev exec wp cache flush
ddev exec wp transient delete --all

# Check Elementor version
ddev exec wp plugin list | grep elementor

# Get Elementor system info
ddev exec wp elementor system-info

# List all Elementor templates
ddev exec wp post list --post_type=elementor_library --posts_per_page=-1
```

**Template-Specific Commands:**
```bash
# Export specific template by ID
ddev exec wp post get <template-id> --format=json > template-backup.json

# Delete template
ddev exec wp post delete <template-id> --force

# Duplicate template
ddev exec wp post duplicate <template-id>

# List templates by type
ddev exec wp post list --post_type=elementor_library --meta_key=elementor_template_type --meta_value=page
```

### Elementor APIs and Hooks

**PHP Hooks for Custom Development:**

```php
// Register custom widgets
add_action('elementor/widgets/register', function($widgets_manager) {
    require_once(__DIR__ . '/widgets/custom-widget.php');
    $widgets_manager->register(new \Custom_Widget());
});

// Add custom widget category
add_action('elementor/elements/categories_registered', function($elements_manager) {
    $elements_manager->add_category(
        'ehs-custom',
        [
            'title' => 'EHS Custom Widgets',
            'icon' => 'fa fa-plug'
        ]
    );
});

// Modify element output before render
add_action('elementor/frontend/before_render', function($element) {
    // Modify element settings or output
    if ($element->get_name() === 'button') {
        // Custom logic for buttons
    }
});

// Add custom CSS before Elementor CSS
add_action('elementor/frontend/before_enqueue_styles', function() {
    wp_enqueue_style('custom-elementor-styles', get_stylesheet_directory_uri() . '/elementor-custom.css');
});
```

**Elementor Pro Forms API:**

```php
// Custom form validation
add_action('elementor_pro/forms/validation', function($field, $record, $ajax_handler) {
    if ('custom_field' === $field['id']) {
        if (empty($field['value'])) {
            $ajax_handler->add_error($field['id'], 'This field is required');
        }
    }
}, 10, 3);

// Custom form action after submit
add_action('elementor_pro/forms/new_record', function($record, $handler) {
    $form_name = $record->get_form_settings('form_name');
    $fields = $record->get_formatted_data();

    // Custom logic (e.g., send to CRM, custom email, etc.)
}, 10, 2);

// Add custom webhook
add_action('elementor_pro/forms/actions/register', function($form_actions_registrar) {
    require_once(__DIR__ . '/form-actions/custom-webhook.php');
    $form_actions_registrar->register(new \Custom_Webhook());
});
```

**Dynamic Tags API:**

```php
// Register custom dynamic tag
add_action('elementor/dynamic_tags/register', function($dynamic_tags_manager) {
    require_once(__DIR__ . '/dynamic-tags/custom-tag.php');
    $dynamic_tags_manager->register(new \Custom_Dynamic_Tag());
});
```

**JavaScript API (Frontend):**

```javascript
// Wait for Elementor frontend to load
jQuery(window).on('elementor/frontend/init', function() {
    // Add custom handlers
    elementorFrontend.hooks.addAction('frontend/element_ready/button.default', function($scope) {
        // Custom button logic
        $scope.find('.elementor-button').on('click', function(e) {
            // Custom click handler
        });
    });
});

// Access Elementor settings
var elementorSettings = elementorFrontend.config;

// Run code on specific widget
elementorFrontend.hooks.addAction('frontend/element_ready/heading.default', function($scope) {
    console.log('Heading widget loaded');
});
```

**Editor JavaScript API:**

```javascript
// Only runs in Elementor editor
elementor.hooks.addAction('panel/open_editor/widget', function(panel, model, view) {
    // Runs when widget settings panel opens
    console.log('Widget type:', model.get('widgetType'));
});

// Listen for changes
elementor.channels.editor.on('change', function() {
    console.log('Editor content changed');
});
```

### Elementor Workflow Automation

**Combined Sync + CSS Regeneration:**
Create `ehs-wordpress-local/elementor-sync-full.sh`:
```bash
#!/bin/bash
# Full Elementor sync with CSS regeneration

echo "Starting full Elementor sync..."

# Sync templates from production
./sync-elementor-templates.sh

# Regenerate CSS
echo "Regenerating CSS..."
ddev exec wp elementor flush-css

# Clear all caches
echo "Clearing caches..."
ddev exec wp cache flush
ddev exec wp transient delete --all

echo "Sync complete!"
```

**Elementor Debug Mode:**
Add to `wp-config.php` for debugging:
```php
define('ELEMENTOR_DEBUG', true);  // Enable debug mode
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

**Performance Optimization:**
```bash
# Regenerate CSS for production (minified)
ddev exec wp elementor flush-css --network

# Clear font cache
ddev exec wp option delete elementor_remote_info_library

# Clear Elementor experiments cache
ddev exec wp option delete elementor_experiment-*
```

**Troubleshooting Commands:**
```bash
# Fix broken templates (regenerate CSS and cache)
ddev exec wp elementor flush-css && ddev exec wp cache flush

# Reset Elementor settings to default
ddev exec wp option delete elementor_*

# Check for conflicting plugins
ddev exec wp plugin list --status=active

# Verify Elementor requirements
ddev exec wp elementor system-info | grep -A 10 "Server Environment"

# Check for JavaScript errors in templates
ddev exec wp post list --post_type=elementor_library --fields=ID,post_title | while read id title; do
    echo "Checking: $title"
    ddev exec wp post get $id --field=meta | grep -i "error" || echo "OK"
done
```

### Elementor Template Structure

**Template Post Meta Fields:**
- `_elementor_data` - JSON structure of page layout
- `_elementor_template_type` - Type: page, section, widget, header, footer
- `_elementor_edit_mode` - Edit mode: builder or wp-editor
- `_elementor_version` - Elementor version used
- `_elementor_conditions` - Display conditions for Theme Builder templates
- `_elementor_css` - Generated CSS for the template

**Querying Templates Programmatically:**
```php
// Get all header templates
$headers = get_posts([
    'post_type' => 'elementor_library',
    'meta_key' => '_elementor_template_type',
    'meta_value' => 'header',
    'posts_per_page' => -1
]);

// Get template data
$template_id = 123;
$elementor_data = get_post_meta($template_id, '_elementor_data', true);
$template_data = json_decode($elementor_data, true);
```

**Accessing Template Conditions:**
```bash
# List all Theme Builder assignments
ddev exec wp option get elementor_pro_theme_builder_conditions

# Update template conditions via WP-CLI
ddev exec wp option patch update elementor_pro_theme_builder_conditions header "include/general"
```

## Migration Planning

Active planning for migrating 24 WordPress sites from Nexcess ($328/month) to DigitalOcean VPS ($48/month).

**Migration documentation:**
- `NEXCESS_TO_DIGITALOCEAN_MIGRATION_PLAN.md` - Comprehensive migration plan
- `nexcess-migration-plan.md` - Condensed migration notes
- `site-audit-checklist.md` - Pre-migration site audit checklist

**Migration scripts:**
```bash
./audit-sites.sh           # Audit all sites on Nexcess
./migrate-sites.sh         # Execute migration (when ready)
```

**Site inventory tracked in:**
- `clients.json` - Client and site information
- Migration plan documents list all 24 domains with priority levels

## Project Organization

Reference documentation in `project-organization/`:
- `BRANDING_GUIDE.md` - Brand colors, fonts, voice guidelines
- `CONTENT_VERIFICATION.md` - Content review checklist
- `DOMAINS_REFERENCE.md` - Domain management reference
- `README.md` - Project organization overview

Subdirectories:
- `01-main-site-wordpress/` - Main WordPress site documentation
- `02-micro-site-vercel/` - Micro-site configurations
- `03-archive/` - Archived project materials

## Common Development Workflows

### Creating a New Service Page

1. Check task checklist structure in relevant JS file (e.g., `create-task-checklists.js`)
2. Review content source JSON file if provided
3. In WordPress admin, create new Services post
4. Fill in custom meta fields (category, area, certifications, etc.)
5. Build page layout in Elementor matching existing service page template
6. Implement two-column layout with sidebar navigation
7. Add to main navigation menu
8. Update sidebar service menus on related pages
9. Configure Yoast SEO (meta title, description, focus keyword)
10. Optimize images with alt text
11. Preview on desktop and mobile
12. Get client approval
13. Publish

### Syncing Changes from Production

When production site has template updates needed locally:

```bash
cd ehs-wordpress-local
ddev start                           # Ensure local site is running
./sync-elementor-templates.sh       # Run sync script
# Enter SSH password when prompted (or uses sshpass)
```

### Working with Services Post Type

```bash
ddev ssh
wp post list --post_type=services   # List all services
wp post meta list <ID>               # View meta for specific service
wp post create --post_type=services --post_title="New Service" --post_status=draft
```

### Database Operations

```bash
# Export production database (via SSH)
ssh a96c427e_1@832f87585d.nxcli.net "cd /home/a96c427e/832f87585d.nxcli.net/html && wp db export - --allow-root" > prod-backup.sql

# Import to local
cd ehs-wordpress-local
ddev import-db --file=../prod-backup.sql

# Search-replace URLs after import
ddev exec wp search-replace 'https://ehsanalytical.com' 'https://ehs-local.ddev.site' --all-tables
```

## Technical Stack

- **Platform:** WordPress 6.x
- **PHP:** 8.3
- **Database:** MariaDB 10.11
- **Web Server:** Nginx (via DDEV)
- **Local Dev:** DDEV (Docker-based)
- **Page Builder:** Elementor Pro
- **Theme:** Hello Elementor + Custom Child Theme
- **Cache:** Object Cache Pro (production), W3 Total Cache
- **Node.js:** Used for task management scripts
- **Bash:** Used for migration and sync scripts

## Environment Variables

Required in `.env` file (root and `ehs-wordpress-local/`):

```bash
# Production SSH Access
SSH_HOSTNAME=832f87585d.nxcli.net
SSH_PORT=22
SSH_USERNAME=a96c427e_1
SSH_PASSWORD=<password>

# API Keys
PEXELS_API_KEY=<key>
AGENT_API_KEY=<key>
AGENT_USER_ID=<uuid>
AGENT_BASE_URL=https://app.noteware.dev/api/agent
```

## Important File Locations

- **WordPress Root:** `ehs-wordpress-local/wordpress/`
- **Custom Theme:** `ehs-wordpress-local/wordpress/wp-content/themes/hello-elementor-child/`
- **Plugins:** `ehs-wordpress-local/wordpress/wp-content/plugins/`
- **MU Plugins:** `ehs-wordpress-local/wordpress/wp-content/mu-plugins/`
- **DDEV Config:** `ehs-wordpress-local/.ddev/config.yaml`
- **Template Exports:** `ehs-wordpress-local/exports/`
- **Task Scripts:** Root directory (`create-task-checklists.js`, etc.)
- **Migration Docs:** Root directory (markdown files)

## Key Considerations

### Service Page Template Structure

All service pages follow consistent two-column layout:
- Main content area with hero section, full-width background
- Sidebar with service navigation menu
- Color scheme: Navy blue (#003366) with gold/yellow accents
- Typography: All headings use Maven Pro font, 700 weight (bold)
- Mobile responsive design required
- Cross-linking between related services
- SEO optimization via Yoast

### Template Synchronization

- Always test template imports in local environment before production
- Templates stored as JSON in `exports/` directory
- Theme Builder templates include: headers, footers, single post layouts, archive layouts
- Assignment of templates to locations handled by `assign-theme-builder-templates.php`

### DDEV Environment Detection

Code can detect DDEV environment via:
- PHP: `getenv('IS_DDEV_PROJECT') === 'true'`
- Used to display local environment warnings
- Used to enable auto-login in local dev

### SSH Connection Management

- Scripts use SSH connection multiplexing to avoid repeated password prompts
- Control socket: `~/.ssh/cm-%r@%h:%p`
- Persists for 300 seconds
- Can use sshpass for automation if installed and SSH_PASSWORD is set

### Task Checklist System

- Task IDs map to specific implementation requirements
- Each checklist item represents atomic work unit
- Items include content review, WordPress operations, SEO setup, testing, approval
- System uses idempotency keys to prevent duplicate operations
- API returns structured task data with checklist relationships

## Troubleshooting

### Mac Mini DDEV Setup Issues

**Docker Desktop Not Running:**
```bash
open -a Docker
# Wait 30-60 seconds for Docker to initialize
docker info  # Verify Docker is ready
```

**macOS Keychain Access Issues (when SSH'd in):**
```bash
# Option 1: Unlock keychain
security -v unlock-keychain ~/Library/Keychains/login.keychain-db

# Option 2: Logout of Docker Hub (for public images)
docker logout
```

**DDEV Project Name Conflicts:**
```bash
# If changing project name from ehs-local to ehs-mini:
ddev stop --unlist ehs-local
ddev start
```

**Database Import Issues:**

1. Check file type first:
   ```bash
   file production-database.sql
   ```

2. If file is gzipped but named `.sql`:
   ```bash
   mv production-database.sql production-database.sql.gz
   ddev import-db --file=production-database.sql.gz
   ```

3. If database import succeeds but site shows blank/default WordPress:
   ```bash
   # Activate plugins and theme
   ddev exec "wp plugin activate elementor elementor-pro advanced-custom-fields-pro --path=/var/www/html/wordpress"
   ddev exec "wp theme activate hello-elementor-child --path=/var/www/html/wordpress"
   ddev exec "wp cache flush --path=/var/www/html/wordpress"
   ```

**Uploads Not Showing:**
```bash
# Verify uploads directory exists and has content
ls -la ehs-wordpress-local/wordpress/wp-content/uploads/
# Should show years 2019-2026 and other directories

# If missing, extract from backup
tar -xzf uploads-backup.tar.gz -C ehs-wordpress-local/wordpress/wp-content/
```

**Cannot Access Site from MacBook:**

1. Verify Mac mini IP address:
   ```bash
   ifconfig | grep "inet " | grep -v 127.0.0.1
   ```

2. Add to MacBook's `/etc/hosts`:
   ```bash
   sudo nano /etc/hosts
   # Add: 10.112.1.56 ehs-mini.ddev.site
   ```

3. Test connectivity:
   ```bash
   ping 10.112.1.56
   curl -I http://10.112.1.56
   ```

**Site Shows Old/Cached Content:**
```bash
ddev exec "wp cache flush --path=/var/www/html/wordpress"
ddev exec "wp eval-file /var/www/html/regen-elementor-css.php --path=/var/www/html/wordpress"
# Hard refresh browser: Cmd+Shift+R (Mac) or Ctrl+Shift+R (Windows)
```

### WP-CLI Path Issues

All WP-CLI commands must include `--path=/var/www/html/wordpress` because docroot is set to `wordpress/`:

```bash
# Correct:
ddev exec "wp cache flush --path=/var/www/html/wordpress"

# Incorrect (will fail):
ddev exec "wp cache flush"
```

### Docker Desktop Data Location

Mac mini Docker Desktop is configured to use external NVMe storage. If Docker fails to start, verify data location is accessible:
```bash
ls -la /Volumes/data/docker  # Or wherever Docker is configured
```
