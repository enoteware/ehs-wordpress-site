<?php
// Create menus matching production

$local_url = 'https://ehs-local.ddev.site';

// Services menu items (from production, adjusted for local)
$services_items = [
    ['title' => 'Environmental Health and Safety (EHS) Consulting', 'url' => '/environmental-health-and-safety-ehs-consulting/'],
    ['title' => 'EHS Staff Outsourcing', 'url' => '/ehs-staff-outsourcing/'],
    ['title' => 'Construction Safety Consulting', 'url' => '/construction-safety-consulting/'],
    ['title' => 'Industrial Hygiene Services', 'url' => '/industrial-hygiene-san-diego/'],
    ['title' => 'Indoor Air Quality Assessments', 'url' => '/san-diego-indoor-air-quality-testing/'],
    ['title' => 'Mold Testing', 'url' => '/mold-testing/'],
    ['title' => 'Asbestos Testing', 'url' => '/san-diego-asbestos-testing/'],
    ['title' => 'Water Damage Assessments', 'url' => '/water-damage-assessments/'],
    ['title' => 'Fire & Smoke Assessments', 'url' => '/california-fire-and-smoke-assessments/'],
];

// Main menu items
$main_items = [
    ['title' => 'Home', 'url' => '/', 'children' => []],
    ['title' => 'Services', 'url' => '/services/', 'children' => [
        ['title' => 'Environmental Health and Safety (EHS) Consulting', 'url' => '/environmental-health-and-safety-ehs-consulting/'],
        ['title' => 'EHS Staff Outsourcing', 'url' => '/ehs-staff-outsourcing/'],
        ['title' => 'Construction Safety Consulting', 'url' => '/construction-safety-consulting/'],
        ['title' => 'Industrial Hygiene Services', 'url' => '/industrial-hygiene-san-diego/'],
        ['title' => 'Indoor Air Quality Testing', 'url' => '/san-diego-indoor-air-quality-testing/'],
        ['title' => 'Mold Testing', 'url' => '/mold-testing/'],
        ['title' => 'Asbestos Testing', 'url' => '/san-diego-asbestos-testing/'],
        ['title' => 'Water Damage Assessments', 'url' => '/water-damage-assessments/'],
        ['title' => 'Fire & Smoke Assessments', 'url' => '/california-fire-and-smoke-assessments/'],
    ]],
    ['title' => 'About Us', 'url' => '/about-us/', 'children' => []],
];

// Get or create services menu
$services_menu = wp_get_nav_menu_object('services menu');
if (!$services_menu) {
    $services_menu_id = wp_create_nav_menu('services menu');
    echo "Created services menu (ID: $services_menu_id)\n";
} else {
    $services_menu_id = $services_menu->term_id;
    // Clear existing items
    $existing = wp_get_nav_menu_items($services_menu_id);
    foreach ($existing as $item) {
        wp_delete_post($item->ID, true);
    }
    echo "Using existing services menu (ID: $services_menu_id)\n";
}

// Add services menu items
$position = 1;
foreach ($services_items as $item) {
    wp_update_nav_menu_item($services_menu_id, 0, [
        'menu-item-title' => $item['title'],
        'menu-item-url' => $local_url . $item['url'],
        'menu-item-status' => 'publish',
        'menu-item-position' => $position++,
    ]);
}
echo "Added " . count($services_items) . " items to services menu\n";

// Get or create main menu
$main_menu = wp_get_nav_menu_object('main menu');
if (!$main_menu) {
    $main_menu_id = wp_create_nav_menu('main menu');
    echo "Created main menu (ID: $main_menu_id)\n";
} else {
    $main_menu_id = $main_menu->term_id;
    $existing = wp_get_nav_menu_items($main_menu_id);
    foreach ($existing as $item) {
        wp_delete_post($item->ID, true);
    }
    echo "Using existing main menu (ID: $main_menu_id)\n";
}

// Add main menu items with children
$position = 1;
foreach ($main_items as $item) {
    $parent_id = wp_update_nav_menu_item($main_menu_id, 0, [
        'menu-item-title' => $item['title'],
        'menu-item-url' => $local_url . $item['url'],
        'menu-item-status' => 'publish',
        'menu-item-position' => $position++,
    ]);

    if (!empty($item['children'])) {
        foreach ($item['children'] as $child) {
            wp_update_nav_menu_item($main_menu_id, 0, [
                'menu-item-title' => $child['title'],
                'menu-item-url' => $local_url . $child['url'],
                'menu-item-status' => 'publish',
                'menu-item-position' => $position++,
                'menu-item-parent-id' => $parent_id,
            ]);
        }
    }
}
echo "Added main menu items\n";

// Assign services menu to header location (like production)
$locations = get_theme_mod('nav_menu_locations', []);
$locations['menu-1'] = $services_menu_id;
set_theme_mod('nav_menu_locations', $locations);
echo "Assigned services menu to header location (menu-1)\n";

echo "\n=== Menu Setup Complete ===\n";
