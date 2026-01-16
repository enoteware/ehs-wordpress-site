# Quick Commands Reference

## CSS Regeneration

After updating Elementor templates or button styles:

```bash
./regen-css.sh
```

Or directly:
```bash
ddev exec wp eval-file regen-elementor-css.php
```

## Button Updates

Update a specific button in Elementor templates:

```bash
# Find a button
ddev exec wp eval-file find-button-a477ddb.php

# Update a button
ddev exec wp eval-file update-button-a477ddb.php
```

## Header Template

Fix/restore header template:

```bash
ddev exec wp eval-file fix-header-final.php
```
