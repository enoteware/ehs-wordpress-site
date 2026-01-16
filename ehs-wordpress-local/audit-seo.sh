#!/bin/bash
# SEO Fields Audit Script
# Audits and fills missing SEO fields for all custom post types

cd "$(dirname "$0")"

echo "🔍 SEO Fields Audit Tool"
echo "========================"
echo ""

# Check if --apply flag is provided
if [[ "$1" == "--apply" ]]; then
    echo "⚠️  APPLYING CHANGES - This will update your posts"
    echo ""
    ddev exec "APPLY_CHANGES=1 wp eval-file audit-and-fill-seo-fields.php --path=/var/www/html/wordpress"
else
    echo "🔍 DRY RUN MODE - No changes will be made"
    echo "Run with --apply to make actual changes"
    echo ""
    ddev exec wp eval-file audit-and-fill-seo-fields.php --path=/var/www/html/wordpress
fi
