<?php
/**
 * Clients Custom Post Type Registration
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Clients Custom Post Type
 */
function ehs_register_clients_post_type() {
    $labels = array(
        'name'                  => _x('Clients', 'Post Type General Name', 'hello-elementor-child'),
        'singular_name'         => _x('Client', 'Post Type Singular Name', 'hello-elementor-child'),
        'menu_name'             => __('Clients', 'hello-elementor-child'),
        'name_admin_bar'        => __('Client', 'hello-elementor-child'),
        'archives'              => __('Client Archives', 'hello-elementor-child'),
        'attributes'            => __('Client Attributes', 'hello-elementor-child'),
        'parent_item_colon'     => __('Parent Client:', 'hello-elementor-child'),
        'all_items'             => __('All Clients', 'hello-elementor-child'),
        'add_new_item'          => __('Add New Client', 'hello-elementor-child'),
        'add_new'               => __('Add New', 'hello-elementor-child'),
        'new_item'              => __('New Client', 'hello-elementor-child'),
        'edit_item'             => __('Edit Client', 'hello-elementor-child'),
        'update_item'           => __('Update Client', 'hello-elementor-child'),
        'view_item'             => __('View Client', 'hello-elementor-child'),
        'view_items'            => __('View Clients', 'hello-elementor-child'),
        'search_items'          => __('Search Client', 'hello-elementor-child'),
        'not_found'             => __('Not found', 'hello-elementor-child'),
        'not_found_in_trash'    => __('Not found in Trash', 'hello-elementor-child'),
        'featured_image'        => __('Client Logo', 'hello-elementor-child'),
        'set_featured_image'    => __('Set client logo', 'hello-elementor-child'),
        'remove_featured_image' => __('Remove client logo', 'hello-elementor-child'),
        'use_featured_image'    => __('Use as client logo', 'hello-elementor-child'),
        'insert_into_item'      => __('Insert into client', 'hello-elementor-child'),
        'uploaded_to_this_item' => __('Uploaded to this client', 'hello-elementor-child'),
        'items_list'            => __('Clients list', 'hello-elementor-child'),
        'items_list_navigation' => __('Clients list navigation', 'hello-elementor-child'),
        'filter_items_list'     => __('Filter clients list', 'hello-elementor-child'),
    );

    $args = array(
        'label'                 => __('Client', 'hello-elementor-child'),
        'description'           => __('EHS Analytical Client Companies and Organizations', 'hello-elementor-child'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 22,
        'menu_icon'             => 'dashicons-building',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
        'rewrite'               => array(
            'slug'                  => 'clients',
            'with_front'            => false,
            'pages'                 => true,
            'feeds'                 => true,
        ),
    );

    register_post_type('clients', $args);
}
add_action('init', 'ehs_register_clients_post_type', 0);
