<?php
/**
 * Force Classic Editor (Globally)
 * Disables the block editor (Gutenberg) for all post types site-wide
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Disable block editor (Gutenberg) for all post types
 * Uses high priority (100) to override other plugins
 */
add_filter('use_block_editor_for_post_type', '__return_false', 100);

/**
 * Disable Gutenberg for all post types
 * Backup hook for older WordPress/Gutenberg versions
 */
add_filter('gutenberg_can_edit_post_type', '__return_false', 100);

/**
 * Remove block editor from admin menu
 * Prevents "Try Gutenberg" prompts and panels
 */
add_action('admin_menu', function() {
    remove_action('try_gutenberg_panel', 'wp_try_gutenberg_panel');
}, 999);
