# Elementor Theme Builder Template Sync Guide

This guide explains how to export header and footer templates from production and import them into local development.

## Method 1: Automated Script (Requires SSH Access)

### Prerequisites

1. **SSH Configuration**: An SSH config entry has been added for `ehs-prod` host. Test connection:
   ```bash
   cd ehs-wordpress-local
   ./test-ssh-connection.sh
   ```

2. **SSH Authentication**: The script supports:
   - SSH config alias (`ehs-prod`) - recommended
   - Direct connection with password (requires interactive session)
   - SSH key authentication (if configured)

### Running the Sync

```bash
cd ehs-wordpress-local
./sync-elementor-templates.sh
```

This script will:
1. Test SSH connection to production
2. Find header/footer templates in production
3. Export them with all Elementor data
4. Import them into local dev

### Troubleshooting SSH Issues

If you get "too many authentication failures":
1. Test connection manually: `ssh ehs-prod`
2. If password is required, run the sync script interactively (it will prompt)
3. Or configure SSH keys for passwordless access
4. Use the test script: `./test-ssh-connection.sh`

## Method 2: Manual Export via WP-CLI (Recommended)

### Step 1: Export from Production

SSH into production:
```bash
ssh a96c427e_1@832f87585d.nxcli.net
cd /home/a96c427e/832f87585d.nxcli.net/html
```

Find template IDs:
```bash
# List all Elementor templates
wp post list --post_type=elementor_library --format=table

# Find header template ID
wp post list --post_type=elementor_library --meta_key=_elementor_template_type --meta_value=header --field=ID

# Find footer template ID
wp post list --post_type=elementor_library --meta_key=_elementor_template_type --meta_value=footer --field=ID
```

Export templates (replace HEADER_ID and FOOTER_ID):
```bash
# Export header template
HEADER_ID=<your_header_id>
wp post get $HEADER_ID --format=json > /tmp/header-template.json
wp post meta list $HEADER_ID --format=json > /tmp/header-meta.json

# Export footer template
FOOTER_ID=<your_footer_id>
wp post get $FOOTER_ID --format=json > /tmp/footer-template.json
wp post meta list $FOOTER_ID --format=json > /tmp/footer-meta.json
```

Download the files:
```bash
# From your local machine
scp a96c427e_1@832f87585d.nxcli.net:/tmp/header-template.json ehs-wordpress-local/exports/elementor-templates/
scp a96c427e_1@832f87585d.nxcli.net:/tmp/footer-template.json ehs-wordpress-local/exports/elementor-templates/
scp a96c427e_1@832f87585d.nxcli.net:/tmp/header-meta.json ehs-wordpress-local/exports/elementor-templates/
scp a96c427e_1@832f87585d.nxcli.net:/tmp/footer-meta.json ehs-wordpress-local/exports/elementor-templates/
```

### Step 2: Import into Local Dev

```bash
cd ehs-wordpress-local
ddev wp eval-file import-elementor-templates.php
```

## Method 3: Using PHP Export Script on Production

Upload the export script to production:
```bash
scp ehs-wordpress-local/export-elementor-templates.php a96c427e_1@832f87585d.nxcli.net:/tmp/
```

SSH into production and run:
```bash
ssh a96c427e_1@832f87585d.nxcli.net
cd /home/a96c427e/832f87585d.nxcli.net/html
php /tmp/export-elementor-templates.php
```

Download the exported files:
```bash
scp a96c427e_1@832f87585d.nxcli.net:/home/a96c427e/832f87585d.nxcli.net/html/exports/elementor-templates/* ehs-wordpress-local/exports/elementor-templates/
```

Then import:
```bash
cd ehs-wordpress-local
ddev wp eval-file import-elementor-templates.php
```

## Method 4: Manual WordPress Admin Export (Simplest)

1. **In Production WordPress Admin:**
   - Go to Templates > Theme Builder
   - Click on the header template
   - Click "Export Template" (if available in Elementor)
   - Or use Tools > Export > Custom Post Types > Elementor Library

2. **Import into Local Dev:**
   - Go to WordPress Admin > Tools > Import
   - Upload the exported file
   - Or use WP-CLI: `ddev wp import <exported-file.xml>`

## Verification

After importing, verify templates are active:

```bash
# Check templates in local dev
ddev wp post list --post_type=elementor_library --format=table

# Check if header/footer are set
ddev wp post meta list <template_id> | grep _elementor_template_type
```

Or in WordPress Admin:
- Go to Templates > Theme Builder
- Verify header and footer templates are listed and active

## Troubleshooting

### Templates not showing in Theme Builder
- Clear Elementor cache: `ddev wp elementor flush-css`
- Regenerate CSS: Go to Elementor > Tools > Regenerate CSS & Data

### Elementor data missing
- The `_elementor_data` meta field contains the template design
- Make sure it was exported and imported correctly
- You may need to manually copy this field from production

### Template not rendering
- Check that template type meta is set: `_elementor_template_type` = 'header' or 'footer'
- Verify template is published
- Check Elementor > Settings > Advanced > CSS Print Method
