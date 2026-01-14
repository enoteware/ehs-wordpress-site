<?php
/**
 * Disable Comments (Globally)
 * Completely disables comments functionality across all post types site-wide
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Remove comments support from all post types
 */
add_action('init', function() {
    $post_types = get_post_types();
    foreach ($post_types as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
}, 100);

/**
 * Close comments on all posts (global)
 */
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);

/**
 * Hide existing comments
 */
add_filter('comments_array', '__return_empty_array', 10, 2);

/**
 * Remove comments page from admin menu
 */
add_action('admin_menu', function() {
    remove_menu_page('edit-comments.php');
});

/**
 * Remove comments meta box from all post types
 */
add_action('admin_menu', function() {
    $post_types = get_post_types();
    foreach ($post_types as $post_type) {
        remove_meta_box('commentstatusdiv', $post_type, 'normal');
        remove_meta_box('commentsdiv', $post_type, 'normal');
    }
});

/**
 * Remove comments from admin bar
 */
add_action('admin_bar_menu', function($wp_admin_bar) {
    $wp_admin_bar->remove_node('comments');
}, 999);

/**
 * Redirect comments page in admin to dashboard
 */
add_action('admin_init', function() {
    global $pagenow;
    if ($pagenow === 'edit-comments.php') {
        wp_safe_redirect(admin_url());
        exit;
    }
});

/**
 * Remove comments column from all post type lists
 */
add_action('admin_init', function() {
    $post_types = get_post_types();
    foreach ($post_types as $post_type) {
        add_filter("manage_{$post_type}_posts_columns", function($columns) {
            unset($columns['comments']);
            return $columns;
        });
    }
});

/**
 * Remove comments link from admin menu (Discussion settings)
 */
add_action('admin_menu', function() {
    remove_submenu_page('options-general.php', 'options-discussion.php');
}, 999);
