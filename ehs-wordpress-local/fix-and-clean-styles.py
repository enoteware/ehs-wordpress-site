#!/usr/bin/env python3
"""
Remove all custom styles from Elementor page data
Fixes JSON issues and removes styling while keeping structure
"""

import json
import subprocess
import sys
import re

PAGE_ID = 92

# Style keys to remove
STYLE_KEYS = [
    # Typography
    'typography_typography', 'typography_font_family', 'typography_font_size',
    'typography_font_weight', 'typography_text_transform', 'typography_font_style',
    'typography_text_decoration', 'typography_line_height', 'typography_letter_spacing',
    'font_family', 'font_size', 'font_weight', 'text_transform', 'font_style',
    'text_decoration', 'line_height', 'letter_spacing',
    
    # Colors
    'title_color', 'text_color', 'color', 'background_color',
    'button_background_color', 'button_text_color', 'button_background_hover_color',
    'button_hover_text_color', 'border_color', 'icon_color',
    
    # Borders
    'border_border', 'border_width', 'border_radius',
    'button_border_border', 'button_border_width', 'button_border_radius',
    
    # Shadows
    'box_shadow_box_shadow_type', 'box_shadow_box_shadow',
    'button_box_shadow_box_shadow_type', 'button_box_shadow_box_shadow',
    'text_shadow_text_shadow_type', 'text_shadow_text_shadow',
    
    # Background styling
    'background_overlay_color', 'background_overlay_opacity',
    'background_overlay_color_stop', 'background_overlay_color_b',
    'background_overlay_color_b_stop', 'background_overlay_gradient_angle',
    
    # Widget-specific styling
    'weight', 'width', 'gap', 'icon_size', 'space_between',
]

# Keys to keep (structural)
KEEP_KEYS = [
    'html_tag', 'header_size', 'widgetType', 'elType', 'id', 'elements', 'isInner',
    '_column_size', '_inline_size', 'structure', 'content_position', 'column_position',
    'align', 'align_mobile', 'link', 'title', 'text', 'editor',
    'background_background', 'background_image', 'background_video_link',
    'background_video_fallback', 'background_slideshow_gallery',
    'background_position', 'background_size', 'background_repeat',
    'custom_height', 'height', 'animation', '_animation', '_animation_delay',
    'margin', 'padding', 'padding_tablet', 'padding_mobile',
    'margin_tablet', 'margin_mobile', '_margin', '_padding',
    '_margin_tablet', '_padding_tablet', '_margin_mobile', '_padding_mobile',
]

def remove_custom_styles(element, depth=0):
    """Recursively remove custom styles from element"""
    if not isinstance(element, dict):
        return element, 0
    
    removed = 0
    
    # Process settings
    if 'settings' in element and isinstance(element['settings'], dict):
        settings = element['settings']
        keys_to_remove = []
        
        for key in list(settings.keys()):
            # Remove style keys
            if key in STYLE_KEYS:
                keys_to_remove.append(key)
                removed += 1
            # Remove style-related prefixes
            elif (key.startswith('typography_') or 
                  key.startswith('button_') or 
                  key.startswith('text_') or 
                  key.startswith('icon_')):
                if key not in KEEP_KEYS:
                    keys_to_remove.append(key)
                    removed += 1
        
        for key in keys_to_remove:
            del settings[key]
            if depth < 2:
                print(f"  Removed: {key}")
    
    # Recursively process children
    if 'elements' in element and isinstance(element['elements'], list):
        for i, child in enumerate(element['elements']):
            child_cleaned, child_removed = remove_custom_styles(child, depth + 1)
            element['elements'][i] = child_cleaned
            removed += child_removed
    
    return element, removed

def fix_json_values(obj):
    """Fix malformed JSON values"""
    if isinstance(obj, dict):
        fixed = {}
        for key, value in obj.items():
            fixed[key] = fix_json_values(value)
        return fixed
    elif isinstance(obj, list):
        return [fix_json_values(item) for item in obj]
    elif isinstance(obj, str):
        # Fix values like "015" -> "15"
        if re.match(r'^0+\d+$', obj) and len(obj) > 1:
            return str(int(obj))
    return obj

# Get Elementor data
print(f"🔍 Fetching Elementor data for page {PAGE_ID}...")
result = subprocess.run(
    ['ddev', 'exec', 'wp', 'post', 'meta', 'get', str(PAGE_ID), '_elementor_data', '--path=/var/www/html/wordpress'],
    cwd='/Volumes/nvme_ext_data/code/ehs/ehs-wordpress-local',
    capture_output=True,
    text=True
)

if result.returncode != 0:
    print(f"❌ Error: {result.stderr}")
    sys.exit(1)

elementor_json = result.stdout.strip()

if not elementor_json or elementor_json == 'false':
    print(f"❌ No Elementor data found")
    sys.exit(1)

# Parse JSON
try:
    data = json.loads(elementor_json)
except json.JSONDecodeError as e:
    print(f"❌ JSON parse error: {e}")
    print("   Attempting to fix common issues...")
    
    # Fix unescaped quotes in titles/text (e.g., "The "EHS" Advantage" -> "The \"EHS\" Advantage")
    # This is a simple fix - find patterns like :"text "word" text" and escape inner quotes
    import re
    def fix_unescaped_quotes(match):
        content = match.group(1)
        # Escape quotes that are inside the value but not at the start/end
        content = content.replace('"', '\\"')
        return f':"{content}"'
    
    # Fix pattern: :"text "word" text"
    elementor_json = re.sub(r':"([^"]*"[^"]*)"([,}])', lambda m: f':"{m.group(1).replace(chr(34), chr(92)+chr(34))}"{m.group(2)}', elementor_json)
    
    # Fix other common issues
    elementor_json = elementor_json.replace('"bottom":"015"', '"bottom":"15"')
    elementor_json = elementor_json.replace('"The "EHS Analytical Solutions" Advantage"', '"The \\"EHS Analytical Solutions\\" Advantage"')
    
    try:
        data = json.loads(elementor_json)
        print("✅ Fixed JSON errors")
    except json.JSONDecodeError as e2:
        print(f"❌ Still has errors: {e2}")
        print("   Trying alternative fix method...")
        
        # More aggressive fix - use WP-CLI to get and fix via database
        # For now, let's try to manually fix the known issue
        elementor_json = elementor_json.replace('"title":"The "EHS', '"title":"The \\"EHS')
        elementor_json = elementor_json.replace('Solutions" Advantage"', 'Solutions\\" Advantage"')
        
        try:
            data = json.loads(elementor_json)
            print("✅ Fixed with alternative method")
        except json.JSONDecodeError as e3:
            print(f"❌ Cannot fix JSON automatically: {e3}")
            print("   You may need to fix this manually in Elementor editor")
            sys.exit(1)

print(f"📊 Found {len(data)} sections\n")

# Fix JSON values first
print("🔧 Fixing malformed values...")
data = fix_json_values(data)

# Remove custom styles
print("🔧 Removing custom styles...\n")
cleaned_data = []
total_removed = 0

for section in data:
    cleaned_section, removed = remove_custom_styles(section)
    cleaned_data.append(cleaned_section)
    total_removed += removed

print(f"\n✅ Removed {total_removed} custom style settings\n")

# Save cleaned data
print("💾 Updating page...")
cleaned_json = json.dumps(cleaned_data, separators=(',', ':'))

# Write to a file in the DDEV container
temp_file = f'/tmp/elementor-{PAGE_ID}-cleaned.json'

# Write file inside container
write_result = subprocess.run(
    ['ddev', 'exec', 'bash', '-c', f'cat > {temp_file}'],
    cwd='/Volumes/nvme_ext_data/code/ehs/ehs-wordpress-local',
    input=cleaned_json,
    text=True,
    capture_output=True
)

if write_result.returncode != 0:
    print(f"❌ Error writing temp file: {write_result.stderr}")
    sys.exit(1)

# Create PHP script to update
update_script = f'''<?php
$data = file_get_contents("{temp_file}");
$data = json_decode($data, true);
if (json_last_error() !== JSON_ERROR_NONE) {{
    echo "JSON Error: " . json_last_error_msg() . "\\n";
    exit(1);
}}
$result = update_post_meta({PAGE_ID}, "_elementor_data", json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
if ($result) {{
    echo "Updated successfully\\n";
}} else {{
    echo "Update failed\\n";
    exit(1);
}}
'''

php_file = f'/tmp/update-elementor-{PAGE_ID}.php'
php_write = subprocess.run(
    ['ddev', 'exec', 'bash', '-c', f'cat > {php_file}'],
    cwd='/Volumes/nvme_ext_data/code/ehs/ehs-wordpress-local',
    input=update_script,
    text=True,
    capture_output=True
)

if php_write.returncode != 0:
    print(f"❌ Error writing PHP file: {php_write.stderr}")
    sys.exit(1)

# Run the update script
update_result = subprocess.run(
    ['ddev', 'exec', 'wp', 'eval-file', php_file, '--path=/var/www/html/wordpress'],
    cwd='/Volumes/nvme_ext_data/code/ehs/ehs-wordpress-local',
    capture_output=True,
    text=True
)

if update_result.returncode != 0:
    print(f"❌ Error updating: {update_result.stderr}")
    sys.exit(1)

print("✅ Page updated successfully\n")

# Regenerate CSS
print("🎨 Regenerating Elementor CSS...")
css_result = subprocess.run(
    ['ddev', 'exec', 'wp', 'elementor', 'flush-css', '--path=/var/www/html/wordpress'],
    cwd='/Volumes/nvme_ext_data/code/ehs/ehs-wordpress-local',
    capture_output=True,
    text=True
)

if css_result.returncode == 0:
    print("✅ CSS regenerated\n")
else:
    print("⚠️  CSS regeneration had issues\n")

print("✨ Done! Theme CSS will now control all styling.\n")
print("📝 Next steps:\n")
print("   1. Review the page in Elementor editor\n")
print("   2. Apply CSS classes via Advanced → CSS Classes where needed\n")
print("   3. Reference style-guide.html for available CSS classes\n")
print("   4. Buttons will automatically use theme button styles\n")
