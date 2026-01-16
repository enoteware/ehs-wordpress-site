# Architecture Overview

## System Architecture

### WordPress Site (Main Application)

**Location:** `ehs-wordpress-local/wordpress/`

**Stack:**
- WordPress 6.x
- PHP 8.3
- MariaDB 10.11
- Nginx (via DDEV)
- Elementor Pro (page builder)
- Hello Elementor Child Theme (custom theme)

**Key Components:**

1. **Custom Theme** (`wp-content/themes/hello-elementor-child/`)
   - Custom post types (Services)
   - Custom meta fields
   - Contact form handler
   - Local dev indicators
   - Design system CSS

2. **Must-Use Plugins** (`wp-content/mu-plugins/`)
   - Auto-login for local dev
   - PHP deprecation warning suppression

3. **Elementor Templates**
   - Theme Builder templates (header, footer, single, archive)
   - Synced between production and local via scripts

### Task Management System

**Location:** Root directory (`*.js` files)

**Purpose:** Interact with external API to manage web development tasks and checklists

**Key Scripts:**
- `create-task-checklists.js` - Create checklists for project tasks
- `add-checklists.js` - Add checklist items to tasks
- `update-tasks.js` - Update task information
- `review-tasks.js` - Review task status
- `verify-checklists.js` - Verify checklist structure

**API Integration:**
- Base URL: `env.AGENT_BASE_URL`
- Authentication: `AGENT_API_KEY` and `AGENT_USER_ID`
- Headers: `x-request-id`, `x-idempotency-key`

### Elementor Template Synchronization

**Location:** `ehs-wordpress-local/`

**Scripts:**
- `export-templates-manual.sh` - Export from production
- `import-elementor-templates.sh` - Import to local
- `sync-elementor-templates.sh` - Full sync workflow

**Workflow:**
1. SSH to production server
2. Export templates via WP-CLI
3. Download JSON files
4. Import to local DDEV site
5. Assign templates to locations

### Migration Planning

**Location:** Root directory and `migration-scripts/`

**Purpose:** Plan and execute migration of 24 WordPress sites from Nexcess to DigitalOcean

**Key Files:**
- `NEXCESS_TO_DIGITALOCEAN_MIGRATION_PLAN.md` - Comprehensive plan
- `migration-scripts/` - Automation scripts
- `clients.json` - Site inventory

## Data Flow

### WordPress Development Flow

```
Production Server (Nexcess)
    ↓ (SSH + WP-CLI export)
Template JSON Files
    ↓ (sync scripts)
Local DDEV Environment
    ↓ (development)
Git Repository
    ↓ (deployment)
Production Server
```

### Task Management Flow

```
Content Source (JSON files)
    ↓ (Node.js scripts)
External API (Task Management)
    ↓ (checklist items)
WordPress Implementation
    ↓ (verification)
Task Completion
```

## Main Modules

### 1. Services Post Type

**File:** `inc/post-types/services-post-type.php`

**Features:**
- Hierarchical structure
- Custom meta fields (category, area, certifications, etc.)
- Admin columns customization
- Taxonomy support

### 2. Contact Form Handler

**Files:**
- `inc/frontend/contact-form-handler.php`
- `inc/form-actions/resend-api-action.php`

**Features:**
- Elementor Pro form integration
- Resend API integration
- Custom validation
- Settings page

### 3. Design System

**File:** `style.css`

**Features:**
- CSS variables for brand colors
- Button variants
- Form styling
- Card components
- Typography scale

### 4. Local Development Indicators

**File:** `inc/frontend/ddev-local-header-bar.php`

**Features:**
- Orange banner on all pages
- Server hostname display
- Admin bar badge
- Only active in DDEV environment

## Dependencies

### WordPress Plugins
- Elementor Pro
- Advanced Custom Fields Pro
- Yoast SEO Premium
- Object Cache Pro
- Jet Elements, Jet Menu, Jet Tabs
- Menu Icons
- Code Snippets

### Development Tools
- DDEV (local development)
- WP-CLI (WordPress management)
- Node.js (task scripts)
- SSH (production access)

## Environment Configuration

### Local (DDEV)
- Project name: `ehs-mini`
- URL: http://ehs-mini.ddev.site
- Database: MariaDB in container
- File system: Mutagen sync

### Production (Nexcess)
- SSH: `a96c427e_1@832f87585d.nxcli.net`
- Path: `/home/a96c427e/832f87585d.nxcli.net/html`
- Shared hosting (24 sites)

## Security Considerations

- Environment variables stored in `.env` (gitignored)
- DDEV certificates in `.ddev/traefik/certs/` (gitignored)
- Database backups excluded from git
- SSH keys managed outside repository
