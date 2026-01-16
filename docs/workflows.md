# Development Workflows

## Common Development Tasks

### Creating a New Service Page

1. Check task checklist structure in relevant JS file
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

### Syncing Elementor Templates from Production

```bash
cd ehs-wordpress-local
ddev start                           # Ensure local site is running
./sync-elementor-templates.sh       # Run sync script
# Enter SSH password when prompted (or uses sshpass)
```

**What it does:**
- Exports templates from production via SSH
- Downloads JSON files to `exports/` directory
- Imports templates into local DDEV site
- Assigns templates to appropriate locations

### Regenerating Elementor CSS

After updating Elementor templates or button styles:

```bash
cd ehs-wordpress-local
./regen-css.sh
```

**What it does:**
- Clears Elementor files cache
- Regenerates Elementor CSS files
- Clears Elementor transients
- Clears WordPress object cache
- Runs Elementor flush-css command

### Working with Services Post Type

```bash
# List all services
ddev exec "wp post list --post_type=services --path=/var/www/html/wordpress"

# View meta for specific service
ddev exec "wp post meta list <ID> --path=/var/www/html/wordpress"

# Create new service
ddev exec "wp post create --post_type=services --post_title='New Service' --post_status=draft --path=/var/www/html/wordpress"
```

### Database Operations

#### Export from Production

```bash
ssh a96c427e_1@832f87585d.nxcli.net "cd /home/a96c427e/832f87585d.nxcli.net/html && wp db export - --allow-root" > prod-backup.sql
```

#### Import to Local

```bash
cd ehs-wordpress-local
ddev import-db --file=../prod-backup.sql

# Search-replace URLs after import
ddev exec "wp search-replace 'https://ehsanalytical.com' 'https://ehs-mini.ddev.site' --all-tables --path=/var/www/html/wordpress"

# Activate plugins and theme
ddev exec "wp plugin activate elementor elementor-pro advanced-custom-fields-pro wordpress-seo jet-elements jet-menu jet-tabs menu-icons code-snippets --path=/var/www/html/wordpress"
ddev exec "wp theme activate hello-elementor-child --path=/var/www/html/wordpress"
ddev exec "wp cache flush --path=/var/www/html/wordpress"
```

### Task Management Workflows

#### Create Task Checklists

```bash
node create-task-checklists.js
```

Creates checklists for project tasks based on content source files.

#### Add Checklists to Tasks

```bash
node add-checklists.js
```

Adds checklist items to existing tasks via API.

#### Update Task Information

```bash
node update-tasks.js
```

Updates task details, status, and metadata.

#### Review Task Status

```bash
node review-tasks.js
```

Reviews and reports on task completion status.

## Build and Test

### WordPress Development

**No build step required** - WordPress is interpreted PHP.

**Testing:**
- Manual browser testing
- Elementor preview mode
- Mobile responsive testing
- Cross-browser testing

### Node.js Scripts

**No build step** - Direct execution with Node.js.

**Testing:**
- Run scripts with test data
- Verify API responses
- Check error handling

## Linting and Code Quality

### PHP

WordPress follows WordPress Coding Standards. No automated linting configured.

**Manual checks:**
- Follow WordPress coding standards
- Use proper escaping functions
- Sanitize user input
- Validate data

### JavaScript

**No linting configured** - Scripts are straightforward Node.js.

**Best practices:**
- Use async/await for API calls
- Handle errors properly
- Include idempotency keys for API requests

## Release Process

### Local Development → Production

1. **Develop locally** in DDEV environment
2. **Test thoroughly** on local site
3. **Commit changes** to git
4. **Push to repository**
5. **Deploy to production** (manual via SSH or automated)

### Template Deployment

1. **Export templates** from local (if needed)
2. **Import to production** via SSH + WP-CLI
3. **Regenerate CSS** on production
4. **Clear caches**

### Database Changes

1. **Test locally** with imported production data
2. **Export local database** if needed
3. **Apply changes** via WP-CLI or migration scripts
4. **Backup production** before changes
5. **Apply to production** via SSH

## Common Git Workflows

### Making Changes

```bash
# Create feature branch
git checkout -b feature/new-service-page

# Make changes
# ... edit files ...

# Stage changes
git add .

# Commit
git commit -m "Add new service page"

# Push
git push origin feature/new-service-page
```

### Syncing with Remote

```bash
# Pull latest changes
git pull origin main

# If conflicts, resolve and:
git add .
git commit -m "Resolve merge conflicts"
```

## Elementor-Specific Workflows

### Styling Elementor Widgets

**CRITICAL:** Use theme CSS, not Elementor Style settings.

1. **Add widget** in Elementor
2. **Go to Advanced → CSS Classes**
3. **Add theme CSS classes** (e.g., `btn btn-primary btn-md`)
4. **Leave Style tab empty** (colors, typography, etc.)
5. **Reference style-guide.html** for available classes

### Creating New Templates

1. **Create template** in Elementor Theme Builder
2. **Build layout** using Elementor widgets
3. **Apply CSS classes** via Advanced tab
4. **Set display conditions**
5. **Export template** (if needed for backup)
6. **Test on local** before production

## Debugging Workflows

See [Runbooks](runbooks.md) for detailed debugging procedures.

### Quick Debugging

```bash
# Check DDEV logs
ddev logs

# Check WordPress debug log
ddev exec "tail -f /var/www/html/wordpress/wp-content/debug.log"

# Clear all caches
ddev exec "wp cache flush --path=/var/www/html/wordpress"
ddev exec "wp transient delete --all --path=/var/www/html/wordpress"
```

## Performance Optimization

### Elementor CSS

```bash
# Regenerate CSS
cd ehs-wordpress-local
./regen-css.sh
```

### WordPress Cache

```bash
# Clear object cache
ddev exec "wp cache flush --path=/var/www/html/wordpress"

# Clear transients
ddev exec "wp transient delete --all --path=/var/www/html/wordpress"
```

### Database Optimization

```bash
# Optimize database
ddev exec "wp db optimize --path=/var/www/html/wordpress"

# Repair database (if needed)
ddev exec "wp db repair --path=/var/www/html/wordpress"
```
