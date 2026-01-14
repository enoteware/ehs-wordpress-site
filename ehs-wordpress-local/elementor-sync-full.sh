#!/bin/bash
# Full Elementor sync with CSS regeneration and cache clearing
# This script combines template sync with performance optimization

set -e  # Exit on error

echo "🚀 Starting full Elementor sync..."
echo ""

# Check if DDEV is running
if ! ddev describe > /dev/null 2>&1; then
    echo "❌ DDEV is not running. Starting DDEV..."
    ddev start
fi

# Sync templates from production
echo "📥 Syncing templates from production..."
if [ -f "./sync-elementor-templates.sh" ]; then
    ./sync-elementor-templates.sh
else
    echo "⚠️  Warning: sync-elementor-templates.sh not found, skipping template sync"
fi

echo ""
echo "🎨 Regenerating Elementor CSS..."
ddev exec wp elementor flush-css

echo ""
echo "🧹 Clearing all caches..."
ddev exec wp cache flush
ddev exec wp transient delete --all

echo ""
echo "🔍 Verifying Elementor status..."
echo "Elementor version:"
ddev exec wp plugin list | grep elementor

echo ""
echo "✅ Sync complete!"
echo ""
echo "Next steps:"
echo "  - Visit https://ehs-local.ddev.site to verify changes"
echo "  - Check Elementor > Tools > Regenerate CSS if issues persist"
echo "  - Review template assignments in Elementor > Theme Builder"
