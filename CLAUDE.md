# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a WordPress-based project for EHS Analytical (ehsanalytical.com), a California-based environmental health and safety consulting firm. The repository manages both the WordPress site and various automation scripts for project management, Elementor template synchronization, and server migration planning.

## Development Environment

### Local WordPress Development (DDEV)

The main WordPress site runs in a DDEV container located in `ehs-wordpress-local/`.

**Starting the site:**
```bash
cd ehs-wordpress-local
ddev start
```

**Accessing the site:**
- URL: https://ehs-local.ddev.site
- Admin: https://ehs-local.ddev.site/wp-admin
- Credentials defined in `.ddev/config.yaml` (WP_ADMIN_USERNAME/WP_ADMIN_PASSWORD)

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
- Orange "LOCAL DEVELOPMENT" header bar displayed when `IS_DDEV_PROJECT=true`
- Implemented via hooks in `functions.php`

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
