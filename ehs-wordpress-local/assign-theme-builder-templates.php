<?php
/**
 * Assign Elementor Theme Builder Templates
 * 
 * This script assigns header and footer templates to Theme Builder locations
 * Run via: ddev wp eval-file assign-theme-builder-templates.php
 */

// Load WordPress
require_once __DIR__ . '/wordpress/wp-load.php';

if (!defined('ABSPATH')) {
    die('WordPress not loaded');
}

// Check if Elementor Pro is active
if (!class_exists('\ElementorPro\Plugin')) {
    die("Error: Elementor Pro is not active\n");
}

echo "=== Assigning Elementor Theme Builder Templates ===\n\n";

// Find header and footer templates
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

$header_id = null;
$footer_id = null;

if ($header_query->have_posts()) {
    $header_query->the_post();
    $header_id = get_the_ID();
    echo "Found header template (ID: $header_id)\n";
} else {
    echo "No header template found\n";
}

if ($footer_query->have_posts()) {
    $footer_query->the_post();
    $footer_id = get_the_ID();
    echo "Found footer template (ID: $footer_id)\n";
} else {
    echo "No footer template found\n";
}

if (!$header_id && !$footer_id) {
    die("Error: No templates found to assign\n");
}

// Get Theme Builder module
$module = \ElementorPro\Plugin::instance()->modules_manager->get_modules('theme-builder');
if (!$module) {
    die("Error: Theme Builder module not found\n");
}

$conditions_manager = $module->get_conditions_manager();
$locations_manager = $module->get_locations_manager();

// Get current conditions
$theme_builder_conditions = get_option('elementor_pro_theme_builder_conditions', []);

// Assign templates
if ($header_id) {
    echo "\nAssigning header template...\n";
    
    // Set conditions for header (general/entire site)
    // Elementor stores conditions as string format: "include/general"
    $conditions_string = 'include/general';
    
    update_post_meta($header_id, '_elementor_conditions', $conditions_string);
    
    // Update theme builder conditions option (array format for option)
    $theme_builder_conditions['header'] = [
        $header_id => $conditions_string,
    ];
    
    echo "✓ Header template assigned (ID: $header_id)\n";
}

if ($footer_id) {
    echo "\nAssigning footer template...\n";
    
    // Set conditions for footer (general/entire site)
    // Elementor stores conditions as string format: "include/general"
    $conditions_string = 'include/general';
    
    update_post_meta($footer_id, '_elementor_conditions', $conditions_string);
    
    // Update theme builder conditions option (array format for option)
    $theme_builder_conditions['footer'] = [
        $footer_id => $conditions_string,
    ];
    
    echo "✓ Footer template assigned (ID: $footer_id)\n";
}

// Save the conditions option
update_option('elementor_pro_theme_builder_conditions', $theme_builder_conditions);
echo "\n✓ Theme Builder conditions option updated\n";

// Clear Elementor cache
if (class_exists('\Elementor\Plugin')) {
    \Elementor\Plugin::$instance->files_manager->clear_cache();
    echo "\n✓ Elementor cache cleared\n";
}

echo "\n=== Assignment Complete ===\n";
echo "Templates should now be active in Theme Builder.\n";
