<?php
/**
 * Import Elementor Theme Builder Templates into Local Dev
 * 
 * This script imports previously exported templates
 * Run via: ddev wp eval-file import-elementor-templates.php
 */

// Load WordPress
require_once __DIR__ . '/wordpress/wp-load.php';

if (!defined('ABSPATH')) {
    die('WordPress not loaded');
}

// Check if Elementor is active
if (!did_action('elementor/loaded')) {
    die("Error: Elementor is not active\n");
}

// Find the import directory
// When run via ddev wp eval-file, we need to find the files in the host filesystem
// Try multiple possible locations
$possible_dirs = [
    __DIR__ . '/exports/elementor-templates',
    dirname(dirname(__DIR__)) . '/ehs-wordpress-local/exports/elementor-templates',
    '/var/www/html/exports/elementor-templates',
    dirname(__DIR__) . '/exports/elementor-templates',
];

$import_dir = null;
foreach ($possible_dirs as $dir) {
    if (is_dir($dir)) {
        $import_dir = $dir;
        break;
    }
}

if (!$import_dir) {
    die("Error: Import directory not found. Tried: " . implode(', ', $possible_dirs) . "\n");
}

echo "Using import directory: $import_dir\n";

echo "=== Importing Elementor Theme Builder Templates ===\n\n";

// Import function
function import_elementor_template($import_dir, $type) {
    $filename = $import_dir . '/' . $type . '-template.json';
    
    if (!file_exists($filename)) {
        echo "Warning: $type template file not found: $filename\n";
        return false;
    }
    
    $json = file_get_contents($filename);
    $data = json_decode($json, true);
    
    if (!$data || !isset($data['post'])) {
        echo "Error: Invalid template file format\n";
        return false;
    }
    
    $post_data = $data['post'];
    $meta_data = $data['meta'] ?? [];
    
    // Check if template already exists
    $existing = get_posts([
        'post_type' => 'elementor_library',
        'meta_query' => [
            [
                'key' => '_elementor_template_type',
                'value' => $type,
            ],
        ],
        'posts_per_page' => 1,
    ]);
    
    if (!empty($existing)) {
        $post_id = $existing[0]->ID;
        echo "Updating existing $type template (ID: $post_id)\n";
        
        // Update post
        $post_data['ID'] = $post_id;
        wp_update_post($post_data);
    } else {
        // Create new post
        unset($post_data['ID']);
        $post_id = wp_insert_post($post_data);
        
        if (is_wp_error($post_id)) {
            echo "Error creating $type template: " . $post_id->get_error_message() . "\n";
            return false;
        }
        
        echo "Created new $type template (ID: $post_id)\n";
    }
    
    // Import all meta
    echo "Importing metadata...\n";
    foreach ($meta_data as $key => $value) {
        // Skip some WordPress internal meta
        if (strpos($key, '_edit_') === 0) {
            continue;
        }

        // IMPORTANT: WordPress strips backslashes from meta values during save.
        // For _elementor_data (JSON with escaped chars like \n), we must use wp_slash()
        // to preserve the backslashes. Without this, "\n" becomes just "n" and breaks JSON.
        if ($key === '_elementor_data' && is_string($value)) {
            $value = wp_slash($value);
        }

        // Handle single vs multiple values
        if (is_array($value) && isset($value[0]) && !is_array($value[0])) {
            // Multiple values
            delete_post_meta($post_id, $key);
            foreach ($value as $val) {
                add_post_meta($post_id, $key, $val);
            }
        } else {
            // Single value
            update_post_meta($post_id, $key, $value);
        }
    }
    
    // Ensure critical Elementor meta is set
    update_post_meta($post_id, '_elementor_template_type', $type);
    update_post_meta($post_id, '_elementor_edit_mode', 'builder');
    
    // Clear Elementor cache
    if (class_exists('\Elementor\Plugin')) {
        \Elementor\Plugin::$instance->files_manager->clear_cache();
    }
    
    echo "✓ $type template imported successfully (ID: $post_id)\n";
    echo "  - Title: {$post_data['post_title']}\n";
    echo "  - Elementor data: " . (isset($meta_data['_elementor_data']) ? 'Yes' : 'No') . "\n";
    
    return $post_id;
}

// Import templates
$header_id = import_elementor_template($import_dir, 'header');
$footer_id = import_elementor_template($import_dir, 'footer');

echo "\n=== Import Complete ===\n";

if ($header_id) {
    echo "Header template ID: $header_id\n";
}
if ($footer_id) {
    echo "Footer template ID: $footer_id\n";
}

echo "\nNext steps:\n";
echo "1. Go to WordPress admin: Templates > Theme Builder\n";
echo "2. Verify header and footer templates are active\n";
echo "3. Clear Elementor cache if needed\n";
