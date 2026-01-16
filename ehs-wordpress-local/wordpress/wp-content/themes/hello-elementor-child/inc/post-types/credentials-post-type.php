<?php
/**
 * Credentials Custom Post Type Registration
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Credentials Custom Post Type
 */
function ehs_register_credentials_post_type() {
    $labels = array(
        'name'                  => _x('Credentials', 'Post Type General Name', 'hello-elementor-child'),
        'singular_name'         => _x('Credential', 'Post Type Singular Name', 'hello-elementor-child'),
        'menu_name'             => __('Credentials', 'hello-elementor-child'),
        'name_admin_bar'        => __('Credential', 'hello-elementor-child'),
        'archives'              => __('Credential Archives', 'hello-elementor-child'),
        'attributes'             => __('Credential Attributes', 'hello-elementor-child'),
        'parent_item_colon'     => __('Parent Credential:', 'hello-elementor-child'),
        'all_items'             => __('All Credentials', 'hello-elementor-child'),
        'add_new_item'          => __('Add New Credential', 'hello-elementor-child'),
        'add_new'               => __('Add New', 'hello-elementor-child'),
        'new_item'              => __('New Credential', 'hello-elementor-child'),
        'edit_item'             => __('Edit Credential', 'hello-elementor-child'),
        'update_item'           => __('Update Credential', 'hello-elementor-child'),
        'view_item'             => __('View Credential', 'hello-elementor-child'),
        'view_items'            => __('View Credentials', 'hello-elementor-child'),
        'search_items'          => __('Search Credential', 'hello-elementor-child'),
        'not_found'             => __('Not found', 'hello-elementor-child'),
        'not_found_in_trash'    => __('Not found in Trash', 'hello-elementor-child'),
        'featured_image'        => __('Featured Image', 'hello-elementor-child'),
        'set_featured_image'    => __('Set featured image', 'hello-elementor-child'),
        'remove_featured_image' => __('Remove featured image', 'hello-elementor-child'),
        'use_featured_image'    => __('Use as featured image', 'hello-elementor-child'),
        'insert_into_item'      => __('Insert into credential', 'hello-elementor-child'),
        'uploaded_to_this_item' => __('Uploaded to this credential', 'hello-elementor-child'),
        'items_list'            => __('Credentials list', 'hello-elementor-child'),
        'items_list_navigation' => __('Credentials list navigation', 'hello-elementor-child'),
        'filter_items_list'     => __('Filter credentials list', 'hello-elementor-child'),
    );

    $args = array(
        'label'                 => __('Credential', 'hello-elementor-child'),
        'description'           => __('EHS Analytical Credentials, Certifications, and Designations', 'hello-elementor-child'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 21,
        'menu_icon'             => 'dashicons-awards',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
        'rewrite'               => array(
            'slug'                  => 'credentials',
            'with_front'            => false,
            'pages'                 => true,
            'feeds'                 => true,
        ),
    );

    register_post_type('credentials', $args);
}
add_action('init', 'ehs_register_credentials_post_type', 0);
