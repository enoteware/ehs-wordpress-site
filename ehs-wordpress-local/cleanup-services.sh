#!/bin/bash
# Cleanup Service SEO Fields
# Removes duplicate text, ellipses, and ensures all SEO fields are populated

cd "$(dirname "$0")"

echo "🧹 Service SEO Cleanup Tool"
echo "==========================="
echo ""

# Check if --apply flag is provided
if [[ "$1" == "--apply" ]]; then
    echo "⚠️  APPLYING CHANGES - This will update service posts"
    echo ""
    ddev exec "APPLY_CHANGES=1 wp eval-file cleanup-service-seo-fields.php --path=/var/www/html/wordpress"
else
    echo "🔍 DRY RUN MODE - No changes will be made"
    echo "Run with --apply to make actual changes"
    echo ""
    ddev exec wp eval-file cleanup-service-seo-fields.php --path=/var/www/html/wordpress
fi
