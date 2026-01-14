<?php
/**
 * Services Custom Post Type Registration
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Services Custom Post Type
 */
function ehs_register_services_post_type() {
    $labels = array(
        'name'                  => _x('Services', 'Post Type General Name', 'hello-elementor-child'),
        'singular_name'         => _x('Service', 'Post Type Singular Name', 'hello-elementor-child'),
        'menu_name'             => __('Services', 'hello-elementor-child'),
        'name_admin_bar'        => __('Service', 'hello-elementor-child'),
        'archives'              => __('Service Archives', 'hello-elementor-child'),
        'attributes'             => __('Service Attributes', 'hello-elementor-child'),
        'parent_item_colon'     => __('Parent Service:', 'hello-elementor-child'),
        'all_items'             => __('All Services', 'hello-elementor-child'),
        'add_new_item'          => __('Add New Service', 'hello-elementor-child'),
        'add_new'               => __('Add New', 'hello-elementor-child'),
        'new_item'              => __('New Service', 'hello-elementor-child'),
        'edit_item'             => __('Edit Service', 'hello-elementor-child'),
        'update_item'           => __('Update Service', 'hello-elementor-child'),
        'view_item'             => __('View Service', 'hello-elementor-child'),
        'view_items'            => __('View Services', 'hello-elementor-child'),
        'search_items'          => __('Search Service', 'hello-elementor-child'),
        'not_found'             => __('Not found', 'hello-elementor-child'),
        'not_found_in_trash'    => __('Not found in Trash', 'hello-elementor-child'),
        'featured_image'        => __('Featured Image', 'hello-elementor-child'),
        'set_featured_image'    => __('Set featured image', 'hello-elementor-child'),
        'remove_featured_image' => __('Remove featured image', 'hello-elementor-child'),
        'use_featured_image'    => __('Use as featured image', 'hello-elementor-child'),
        'insert_into_item'      => __('Insert into service', 'hello-elementor-child'),
        'uploaded_to_this_item' => __('Uploaded to this service', 'hello-elementor-child'),
        'items_list'            => __('Services list', 'hello-elementor-child'),
        'items_list_navigation' => __('Services list navigation', 'hello-elementor-child'),
        'filter_items_list'     => __('Filter services list', 'hello-elementor-child'),
    );

    $args = array(
        'label'                 => __('Service', 'hello-elementor-child'),
        'description'           => __('EHS Analytical Services', 'hello-elementor-child'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes'),
        'hierarchical'          => true,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 20,
        'menu_icon'             => 'dashicons-businessman',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
        'rewrite'               => array(
            'slug'                  => 'services',
            'with_front'            => false,
            'pages'                 => true,
            'feeds'                 => true,
        ),
    );

    register_post_type('services', $args);
}
add_action('init', 'ehs_register_services_post_type', 0);
