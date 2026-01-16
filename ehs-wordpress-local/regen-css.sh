#!/bin/bash
# Quick command to regenerate Elementor CSS
# Usage: ./regen-css.sh

cd "$(dirname "$0")"

echo "🔄 Regenerating Elementor CSS..."
echo ""

# Run the PHP script via WP-CLI
ddev exec --service=web --dir=/var/www/html/wordpress wp eval-file /var/www/html/regen-elementor-css.php

echo ""
echo "✅ Done! Hard refresh your browser to see changes."
