#!/usr/bin/env python3
"""
Remove individual typography overrides from Elementor pages.
Makes all headings use global kit settings instead of per-widget overrides.
"""

import json
import subprocess
import tempfile
import os

def remove_typography_overrides(element):
    """Recursively remove typography overrides from element and children."""
    if not isinstance(element, dict):
        return element

    changes_made = False

    # Remove typography overrides from settings
    if 'settings' in element:
        settings = element['settings']
        keys_to_remove = []

        # Typography keys to remove (individual overrides)
        typography_keys = [
            'typography_typography', 'typography_font_family', 'typography_font_size',
            'typography_font_weight', 'typography_text_transform', 'typography_font_style',
            'typography_text_decoration', 'typography_line_height', 'typography_letter_spacing',
            'font_family', 'font_size', 'font_weight', 'text_transform', 'font_style',
            'text_decoration', 'line_height', 'letter_spacing'
        ]

        # Check for typography overrides
        for key in list(settings.keys()):
            # Remove individual typography settings but keep structure
            if any(key.startswith(typ) or key.endswith(typ) for typ in typography_keys):
                if key not in ['html_tag', 'header_size']:  # Keep structural settings
                    keys_to_remove.append(key)
                    changes_made = True

        for key in keys_to_remove:
            del settings[key]
            print(f"  Removed override: {key}")

    # Recursively process children
    if 'elements' in element:
        for child in element['elements']:
            if remove_typography_overrides(child):
                changes_made = True

    return changes_made

def clean_page_typography(page_id):
    """Clean typography overrides from a specific page."""
    print(f"🔍 Fetching Elementor data for page {page_id}...")

    # Get current page meta using database query
    sql_query = f"SELECT meta_value FROM wpoq_postmeta WHERE post_id = {page_id} AND meta_key = '_elementor_data'"

    result = subprocess.run(
        ['ddev', 'exec', 'wp', 'db', 'query', sql_query, '--skip-column-names', '--path=/var/www/html/wordpress'],
        capture_output=True,
        text=True,
        cwd='/Users/elliotnoteware/code/ehs/ehs-wordpress-local'
    )

    if result.returncode != 0:
        print(f"❌ Error fetching page data: {result.stderr}")
        return False

    elementor_json = result.stdout.strip()

    if not elementor_json:
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
    print("\n🔧 Removing typography overrides...")
    changes_made = False
    for section in data:
        if remove_typography_overrides(section):
            changes_made = True

    if not changes_made:
        print("ℹ️  No typography overrides found")
        return True

    print("\n✅ Removed typography overrides")

    # Save cleaned data using temp file
    print("💾 Updating page...")
    cleaned_json = json.dumps(data)

    # Write to temp file
    with tempfile.NamedTemporaryFile(mode='w', suffix='.json', delete=False) as f:
        f.write(cleaned_json)
        temp_file = f.name

    try:
        # Escape single quotes for SQL
        sql_escaped = cleaned_json.replace("'", "\\'")

        # Update using SQL
        update_sql = f"UPDATE wpoq_postmeta SET meta_value = '{sql_escaped}' WHERE post_id = {page_id} AND meta_key = '_elementor_data'"

        # Write SQL to temp file for safety
        with tempfile.NamedTemporaryFile(mode='w', suffix='.sql', delete=False) as sql_file:
            sql_file.write(update_sql)
            sql_temp = sql_file.name

        update_result = subprocess.run(
            ['ddev', 'exec', 'wp', 'db', 'query', update_sql, '--path=/var/www/html/wordpress'],
            capture_output=True,
            text=True,
            cwd='/Users/elliotnoteware/code/ehs/ehs-wordpress-local'
        )

        if update_result.returncode != 0:
            print(f"❌ Error updating page: {update_result.stderr}")
            return False

    finally:
        # Clean up temp files
        os.unlink(temp_file)
        if 'sql_temp' in locals():
            os.unlink(sql_temp)

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
