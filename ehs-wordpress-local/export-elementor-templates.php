<?php
/**
 * Export Elementor Theme Builder Templates from Production
 * 
 * This script exports header and footer templates with all Elementor data
 * Run via: ssh production && php export-elementor-templates.php
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

$export_dir = __DIR__ . '/exports/elementor-templates';
if (!is_dir($export_dir)) {
    wp_mkdir_p($export_dir);
}

echo "=== Exporting Elementor Theme Builder Templates ===\n\n";

// Find header template
$header_query = new WP_Query([
    'post_type' => 'elementor_library',
    'posts_per_page' => 1,
    'meta_query' => [
        [
            'key' => '_elementor_template_type',
            'value' => 'header',
        ],
    ],
]);

$header_id = null;
if ($header_query->have_posts()) {
    $header_query->the_post();
    $header_id = get_the_ID();
    echo "Found header template (ID: $header_id)\n";
} else {
    echo "No header template found\n";
}

// Find footer template
$footer_query = new WP_Query([
    'post_type' => 'elementor_library',
    'posts_per_page' => 1,
    'meta_query' => [
        [
            'key' => '_elementor_template_type',
            'value' => 'footer',
        ],
    ],
]);

$footer_id = null;
if ($footer_query->have_posts()) {
    $footer_query->the_post();
    $footer_id = get_the_ID();
    echo "Found footer template (ID: $footer_id)\n";
} else {
    echo "No footer template found\n";
}

// Export function
function export_elementor_template($post_id, $export_dir, $type) {
    if (!$post_id) {
        return false;
    }
    
    $post = get_post($post_id);
    if (!$post) {
        echo "Error: Post $post_id not found\n";
        return false;
    }
    
    // Get all post meta
    $all_meta = get_post_meta($post_id);
    
    // Build export data
    $export_data = [
        'post' => [
            'ID' => $post->ID,
            'post_title' => $post->post_title,
            'post_name' => $post->post_name,
            'post_content' => $post->post_content,
            'post_excerpt' => $post->post_excerpt,
            'post_status' => $post->post_status,
            'post_type' => $post->post_type,
            'post_date' => $post->post_date,
            'post_date_gmt' => $post->post_date_gmt,
            'post_modified' => $post->post_modified,
            'post_modified_gmt' => $post->post_modified_gmt,
            'post_author' => $post->post_author,
        ],
        'meta' => [],
    ];
    
    // Export all meta (unserialize arrays)
    foreach ($all_meta as $key => $values) {
        if (count($values) === 1) {
            $export_data['meta'][$key] = maybe_unserialize($values[0]);
        } else {
            $export_data['meta'][$key] = array_map('maybe_unserialize', $values);
        }
    }
    
    // Save to JSON file
    $filename = $export_dir . '/' . $type . '-template.json';
    file_put_contents($filename, json_encode($export_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    
    echo "Exported $type template to: $filename\n";
    echo "  - Post title: {$post->post_title}\n";
    echo "  - Elementor data: " . (isset($export_data['meta']['_elementor_data']) ? 'Yes' : 'No') . "\n";
    
    return true;
}

// Export templates
echo "\n=== Exporting Templates ===\n";
if ($header_id) {
    export_elementor_template($header_id, $export_dir, 'header');
}
if ($footer_id) {
    export_elementor_template($footer_id, $export_dir, 'footer');
}

echo "\n=== Export Complete ===\n";
echo "Files saved to: $export_dir\n";
