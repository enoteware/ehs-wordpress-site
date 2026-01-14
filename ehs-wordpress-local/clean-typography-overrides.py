#!/usr/bin/env python3
"""
Remove individual typography overrides from Elementor pages.
Makes all headings use global kit settings instead of per-widget overrides.
"""

import json
import sys
import subprocess

def remove_typography_overrides(element):
    """Recursively remove typography overrides from element and children."""
    if not isinstance(element, dict):
        return element

    changes_made = False

    # Remove typography overrides from settings
    if 'settings' in element:
        settings = element['settings']
        keys_to_remove = []

        # Find all typography-related keys
        for key in list(settings.keys()):
            if any(typ in key for typ in ['typography_', 'font_family', 'font_size', 'font_weight',
                                           'text_transform', 'font_style', 'text_decoration',
                                           'line_height', 'letter_spacing']):
                # Keep only if it's a custom value we explicitly want
                # Remove individual overrides
                if key not in ['html_tag']:  # Keep structural settings
                    keys_to_remove.append(key)
                    changes_made = True

        for key in keys_to_remove:
            del settings[key]

    # Recursively process children
    if 'elements' in element:
        for child in element['elements']:
            if remove_typography_overrides(child):
                changes_made = True

    return changes_made

def clean_page_typography(page_id):
    """Clean typography overrides from a specific page."""
    print(f"🔍 Fetching Elementor data for page {page_id}...")

    # Get current page meta
    result = subprocess.run(
        ['ddev', 'exec', 'wp', 'post', 'meta', 'get', str(page_id), '_elementor_data', '--path=/var/www/html/wordpress'],
        capture_output=True,
        text=True,
        cwd='/Users/elliotnoteware/code/ehs/ehs-wordpress-local'
    )

    if result.returncode != 0:
        print(f"❌ Error fetching page data: {result.stderr}")
        return False

    elementor_json = result.stdout.strip()

    if not elementor_json or elementor_json == 'false':
        print(f"❌ No Elementor data found for page {page_id}")
        return False

    # Parse JSON
    try:
        data = json.loads(elementor_json)
    except json.JSONDecodeError as e:
        print(f"❌ Error parsing JSON: {e}")
        return False

    print(f"📊 Found {len(data)} sections")

    # Remove typography overrides
    changes_made = False
    for section in data:
        if remove_typography_overrides(section):
            changes_made = True

    if not changes_made:
        print("ℹ️  No typography overrides found")
        return True

    print("✅ Removed typography overrides")

    # Save cleaned data
    print("💾 Updating page...")
    cleaned_json = json.dumps(data, separators=(',', ':'))

    # Use wp post meta update
    update_result = subprocess.run(
        ['ddev', 'exec', 'wp', 'post', 'meta', 'update', str(page_id), '_elementor_data', cleaned_json, '--path=/var/www/html/wordpress'],
        capture_output=True,
        text=True,
        cwd='/Users/elliotnoteware/code/ehs/ehs-wordpress-local'
    )

    if update_result.returncode != 0:
        print(f"❌ Error updating page: {update_result.stderr}")
        return False

    print("✅ Page updated successfully")
    return True

if __name__ == '__main__':
    page_id = 115  # About Us page

    if clean_page_typography(page_id):
        print("\n🎨 Regenerating CSS...")
        subprocess.run(
            ['ddev', 'exec', 'wp', 'elementor', 'flush-css', '--path=/var/www/html/wordpress'],
            cwd='/Users/elliotnoteware/code/ehs/ehs-wordpress-local'
        )

        print("🧹 Clearing caches...")
        subprocess.run(
            ['ddev', 'exec', 'wp', 'cache', 'flush', '--path=/var/www/html/wordpress'],
            cwd='/Users/elliotnoteware/code/ehs/ehs-wordpress-local'
        )

        print("\n✅ Typography cleanup complete!")
        print(f"   All headings now use global kit settings: Maven Pro 700")
        print(f"   Visit: https://ehs-local.ddev.site/about-us/")
    else:
        sys.exit(1)
