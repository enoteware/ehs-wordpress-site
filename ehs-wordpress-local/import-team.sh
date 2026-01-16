#!/bin/bash
# Import Team Members from About Page
# Creates team posts from team member data extracted from the about page

cd "$(dirname "$0")"

echo "👥 Team Member Import Tool"
echo "=========================="
echo ""

# Check if --apply flag is provided
if [[ "$1" == "--apply" ]]; then
    echo "⚠️  APPLYING CHANGES - This will create/update team posts"
    echo ""
    ddev exec "APPLY_CHANGES=1 wp eval-file import-team-from-about.php --path=/var/www/html/wordpress"
else
    echo "🔍 DRY RUN MODE - No changes will be made"
    echo "Run with --apply to make actual changes"
    echo ""
    ddev exec wp eval-file import-team-from-about.php --path=/var/www/html/wordpress
fi
