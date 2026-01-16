<?php
/**
 * Team Members Custom Post Type Registration
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Team Members Custom Post Type
 */
function ehs_register_team_post_type() {
    $labels = array(
        'name'                  => _x('Team Members', 'Post Type General Name', 'hello-elementor-child'),
        'singular_name'         => _x('Team Member', 'Post Type Singular Name', 'hello-elementor-child'),
        'menu_name'             => __('Team', 'hello-elementor-child'),
        'name_admin_bar'        => __('Team Member', 'hello-elementor-child'),
        'archives'              => __('Team Archives', 'hello-elementor-child'),
        'attributes'            => __('Team Member Attributes', 'hello-elementor-child'),
        'parent_item_colon'     => __('Parent Team Member:', 'hello-elementor-child'),
        'all_items'             => __('All Team Members', 'hello-elementor-child'),
        'add_new_item'          => __('Add New Team Member', 'hello-elementor-child'),
        'add_new'               => __('Add New', 'hello-elementor-child'),
        'new_item'              => __('New Team Member', 'hello-elementor-child'),
        'edit_item'             => __('Edit Team Member', 'hello-elementor-child'),
        'update_item'           => __('Update Team Member', 'hello-elementor-child'),
        'view_item'             => __('View Team Member', 'hello-elementor-child'),
        'view_items'            => __('View Team Members', 'hello-elementor-child'),
        'search_items'          => __('Search Team Members', 'hello-elementor-child'),
        'not_found'             => __('Not found', 'hello-elementor-child'),
        'not_found_in_trash'    => __('Not found in Trash', 'hello-elementor-child'),
        'featured_image'        => __('Photo', 'hello-elementor-child'),
        'set_featured_image'    => __('Set photo', 'hello-elementor-child'),
        'remove_featured_image' => __('Remove photo', 'hello-elementor-child'),
        'use_featured_image'    => __('Use as photo', 'hello-elementor-child'),
        'insert_into_item'      => __('Insert into team member', 'hello-elementor-child'),
        'uploaded_to_this_item' => __('Uploaded to this team member', 'hello-elementor-child'),
        'items_list'            => __('Team members list', 'hello-elementor-child'),
        'items_list_navigation' => __('Team members list navigation', 'hello-elementor-child'),
        'filter_items_list'     => __('Filter team members list', 'hello-elementor-child'),
    );

    $args = array(
        'label'                 => __('Team Member', 'hello-elementor-child'),
        'description'           => __('EHS Analytical Team Members', 'hello-elementor-child'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 22,
        'menu_icon'             => 'dashicons-groups',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => 'team',
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
        'rewrite'               => array(
            'slug'       => 'team',
            'with_front' => false,
            'pages'      => true,
            'feeds'      => false,
        ),
    );

    register_post_type('team', $args);
}
add_action('init', 'ehs_register_team_post_type', 0);

/**
 * Flush rewrite rules on theme activation
 */
function ehs_team_rewrite_flush() {
    ehs_register_team_post_type();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'ehs_team_rewrite_flush');
