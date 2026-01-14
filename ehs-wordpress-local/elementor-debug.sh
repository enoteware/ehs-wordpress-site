#!/bin/bash
# Elementor debugging and diagnostics script
# Use this when experiencing Elementor issues

set -e

echo "🔍 Elementor Debugging & Diagnostics"
echo "====================================="
echo ""

# Check DDEV status
if ! ddev describe > /dev/null 2>&1; then
    echo "❌ DDEV is not running. Please start DDEV first."
    exit 1
fi

echo "📊 Elementor Information:"
echo "------------------------"
ddev exec wp plugin list | grep elementor
echo ""

echo "🔧 System Requirements:"
echo "----------------------"
ddev exec wp elementor system-info | grep -A 3 "Server Environment"
echo ""

echo "📝 Elementor Templates:"
echo "----------------------"
echo "Total templates:"
ddev exec wp post list --post_type=elementor_library --format=count
echo ""
echo "Templates by type:"
echo "  Pages:"
ddev exec wp post list --post_type=elementor_library --meta_key=_elementor_template_type --meta_value=page --format=count
echo "  Headers:"
ddev exec wp post list --post_type=elementor_library --meta_key=_elementor_template_type --meta_value=header --format=count
echo "  Footers:"
ddev exec wp post list --post_type=elementor_library --meta_key=_elementor_template_type --meta_value=footer --format=count
echo "  Sections:"
ddev exec wp post list --post_type=elementor_library --meta_key=_elementor_template_type --meta_value=section --format=count
echo ""

echo "💾 Cache Status:"
echo "---------------"
ddev exec wp cache type
echo ""

echo "⚙️  Elementor Options:"
echo "--------------------"
echo "Active experiments:"
ddev exec wp option list | grep "elementor_experiment" | head -5
echo ""

echo "🎨 CSS Generation:"
echo "-----------------"
echo "Regenerating CSS files..."
ddev exec wp elementor flush-css
echo "✅ CSS regenerated"
echo ""

echo "🧹 Clearing Caches:"
echo "------------------"
ddev exec wp cache flush
ddev exec wp transient delete --all
echo "✅ Caches cleared"
echo ""

echo "📋 Recent Error Log (if exists):"
echo "--------------------------------"
if ddev exec wp eval 'echo ini_get("error_log");' > /dev/null 2>&1; then
    ddev exec wp eval 'echo ini_get("error_log");' | xargs -I {} ddev exec cat {} 2>/dev/null | grep -i elementor | tail -10 || echo "No Elementor errors found"
else
    echo "Error log not accessible"
fi
echo ""

echo "✅ Debugging complete!"
echo ""
echo "Common fixes:"
echo "  1. Visit Elementor > Tools > Regenerate CSS & Data"
echo "  2. Check Elementor > System Info for compatibility issues"
echo "  3. Disable other plugins to check for conflicts"
echo "  4. Review browser console for JavaScript errors"
