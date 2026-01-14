#!/usr/bin/env python3
import json
import subprocess

def remove_typography_overrides(element):
    """Recursively remove typography overrides."""
    if not isinstance(element, dict):
        return 0

    count = 0

    # Remove from settings
    if 'settings' in element:
        settings = element['settings']
        keys_to_remove = []

        for key in list(settings.keys()):
            if 'typography' in key.lower() and key not in ['html_tag', 'header_size']:
                keys_to_remove.append(key)
                count += 1

        for key in keys_to_remove:
            del settings[key]

    # Process children
    if 'elements' in element:
        for child in element['elements']:
            count += remove_typography_overrides(child)

    return count

# Load data
with open('/tmp/elementor-data.json') as f:
    data = json.load(f)

print(f"📊 Found {len(data)} sections")

# Remove overrides
total = 0
for section in data:
    total += remove_typography_overrides(section)

print(f"✅ Removed {total} typography overrides")

# Save
with open('/tmp/elementor-data-clean.json', 'w') as f:
    json.dump(data, f, separators=(',', ':'))

print("💾 Updating page...")
json_str = json.dumps(data)
subprocess.run([
    'ddev', 'exec', 'wp', 'post', 'meta', 'update', '115', '_elementor_data', json_str,
    '--path=/var/www/html/wordpress'
], cwd='/Users/elliotnoteware/code/ehs/ehs-wordpress-local')

print("🎨 Regenerating CSS...")
subprocess.run([
    'ddev', 'exec', 'wp', 'elementor', 'flush-css',
    '--path=/var/www/html/wordpress'
], cwd='/Users/elliotnoteware/code/ehs/ehs-wordpress-local')

print("✅ Done! All headings now use global Maven Pro 700")
