<?php
/**
 * Hello Elementor Child Theme Functions
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// ========================================
// PARENT THEME STYLES
// ========================================

add_action('wp_enqueue_scripts', 'hello_elementor_child_enqueue_styles');
function hello_elementor_child_enqueue_styles() {
    wp_enqueue_style(
        'hello-elementor-parent-style',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme()->get('Version')
    );
}

// ========================================
// CUSTOM POST TYPES
// ========================================

require_once get_stylesheet_directory() . '/inc/post-types/services-post-type.php';

// ========================================
// META FIELDS
// ========================================

require_once get_stylesheet_directory() . '/inc/meta-fields/services-meta-fields.php';
require_once get_stylesheet_directory() . '/inc/meta-fields/services-meta-box.php';

// ========================================
// ADMIN CUSTOMIZATIONS
// ========================================

require_once get_stylesheet_directory() . '/inc/admin/admin-columns-services.php';
require_once get_stylesheet_directory() . '/inc/admin/classic-editor.php';
require_once get_stylesheet_directory() . '/inc/admin/disable-comments.php';

// ========================================
// FRONTEND FEATURES
// ========================================

require_once get_stylesheet_directory() . '/inc/frontend/ddev-local-header-bar.php';
