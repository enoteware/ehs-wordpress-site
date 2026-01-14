# Quick Elementor Template Export Guide

Since automated SSH isn't working, here's the fastest way to export and import templates:

## Quick Method (5 minutes)

### 1. SSH into Production
```bash
ssh a96c427e_1@832f87585d.nxcli.net
cd /home/a96c427e/832f87585d.nxcli.net/html
```

### 2. Find Template IDs
```bash
# List all Elementor templates
wp post list --post_type=elementor_library --format=table

# Or find specific ones
wp post list --post_type=elementor_library --meta_key=_elementor_template_type --meta_value=header --format=ids
wp post list --post_type=elementor_library --meta_key=_elementor_template_type --meta_value=footer --format=ids
```

### 3. Export Templates (Replace HEADER_ID and FOOTER_ID)
```bash
HEADER_ID=<your_header_id>
FOOTER_ID=<your_footer_id>

# Export header
wp post get $HEADER_ID --format=json > /tmp/header.json
wp post meta list $HEADER_ID --format=json > /tmp/header-meta.json

# Export footer  
wp post get $FOOTER_ID --format=json > /tmp/footer.json
wp post meta list $FOOTER_ID --format=json > /tmp/footer-meta.json
```

### 4. Download Files to Local
```bash
# From your local machine (in a new terminal)
cd /Users/elliotnoteware/code/ehs/ehs-wordpress-local
mkdir -p exports/elementor-templates

scp a96c427e_1@832f87585d.nxcli.net:/tmp/header.json exports/elementor-templates/header-template.json
scp a96c427e_1@832f87585d.nxcli.net:/tmp/footer.json exports/elementor-templates/footer-template.json
scp a96c427e_1@832f87585d.nxcli.net:/tmp/header-meta.json exports/elementor-templates/header-meta.json
scp a96c427e_1@832f87585d.nxcli.net:/tmp/footer-meta.json exports/elementor-templates/footer-meta.json
```

### 5. Import into Local Dev
```bash
cd /Users/elliotnoteware/code/ehs/ehs-wordpress-local
ddev wp eval-file import-elementor-templates.php
```

## Alternative: Use PHP Export Script

If you prefer, upload the PHP export script to production:

```bash
# From local machine
scp ehs-wordpress-local/export-elementor-templates.php a96c427e_1@832f87585d.nxcli.net:/tmp/

# Then SSH and run:
ssh a96c427e_1@832f87585d.nxcli.net
cd /home/a96c427e/832f87585d.nxcli.net/html
php /tmp/export-elementor-templates.php

# Download the exported files
# From local machine
scp a96c427e_1@832f87585d.nxcli.net:/home/a96c427e/832f87585d.nxcli.net/html/exports/elementor-templates/* ehs-wordpress-local/exports/elementor-templates/
```

Then import:
```bash
cd ehs-wordpress-local
ddev wp eval-file import-elementor-templates.php
```
