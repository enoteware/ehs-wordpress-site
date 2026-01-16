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
        wp_get_theme(get_template())->get('Version')
    );

    wp_enqueue_style(
        'hello-elementor-child-style',
        get_stylesheet_uri(),
        array('hello-elementor-parent-style'),
        wp_get_theme()->get('Version')
    );
}

// ========================================
// CUSTOM POST TYPES
// ========================================

require_once get_stylesheet_directory() . '/inc/post-types/services-post-type.php';

// ========================================
// TAXONOMIES
// ========================================

require_once get_stylesheet_directory() . '/inc/taxonomies/services-taxonomies.php';

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
require_once get_stylesheet_directory() . '/inc/admin/contact-form-settings.php';
require_once get_stylesheet_directory() . '/inc/admin/style-guide-page.php';

// ========================================
// FRONTEND FEATURES
// ========================================

require_once get_stylesheet_directory() . '/inc/frontend/ddev-local-header-bar.php';
require_once get_stylesheet_directory() . '/inc/frontend/service-content-blocks.php';
require_once get_stylesheet_directory() . '/inc/frontend/contact-form.php';
require_once get_stylesheet_directory() . '/inc/frontend/contact-form-handler.php';

/**
 * Enqueue Service ToC scripts
 */
add_action('wp_enqueue_scripts', 'ehs_enqueue_service_toc_assets');
function ehs_enqueue_service_toc_assets() {
    if (!is_singular('services')) {
        return;
    }

    wp_enqueue_script(
        'ehs-service-toc',
        get_stylesheet_directory_uri() . '/assets/js/service-toc.js',
        array(),
        wp_get_theme()->get('Version'),
        true
    );
}

/**
 * Enqueue Contact Form assets
 */
add_action('wp_enqueue_scripts', 'ehs_enqueue_contact_form_assets');
function ehs_enqueue_contact_form_assets() {
    // Enqueue CSS
    wp_enqueue_style(
        'ehs-contact-form',
        get_stylesheet_directory_uri() . '/assets/css/contact-form.css',
        array(),
        wp_get_theme()->get('Version')
    );

    // Enqueue JS (requires jQuery)
    wp_enqueue_script(
        'ehs-contact-form',
        get_stylesheet_directory_uri() . '/assets/js/contact-form.js',
        array('jquery'),
        wp_get_theme()->get('Version'),
        true
    );

    // Localize script with AJAX URL and reCAPTCHA key
    wp_localize_script('ehs-contact-form', 'ehsContactForm', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'recaptchaSiteKey' => get_option('ehs_recaptcha_site_key', ''),
    ));
}

// Force the custom single template for Services CPT even if a page template is set (e.g. Elementor Header/Footer).
add_filter('template_include', function ($template) {
    if (is_singular('services')) {
        $forced_template = locate_template('single-services.php');
        if (!empty($forced_template)) {
            return $forced_template;
        }
    }

    return $template;
}, 99);

// ========================================
// ELEMENTOR DESIGN SYSTEM INTEGRATION
// ========================================
//
// DESIGN SYSTEM ARCHITECTURE:
// ---------------------------
// This theme implements a strict separation between Elementor and theme CSS:
//
// ELEMENTOR'S ROLE:
//   - Structure: Page layout, sections, columns, widgets
//   - Content: Text, images, media placement and organization
//   - Responsive: Breakpoint management and visibility controls
//   - NO STYLING: Colors, typography, spacing handled by theme CSS
//
// THEME CSS ROLE:
//   - All visual styling: Colors, typography, spacing, effects
//   - Design system implementation: Buttons, forms, cards, containers
//   - Brand consistency: CSS variables and standardized classes
//
// INTEGRATION METHOD:
//   - Elementor Site Settings (Theme Style) have been cleared
//   - All styling applied via CSS classes in Elementor's "Advanced → CSS Classes"
//   - Elementor Style tab used only for layout (width, alignment)
//   - Style tab colors/typography left empty
//
// DOCUMENTATION:
//   - Complete Style Guide: ../style-guide.html (visual reference)
//   - Quick Reference: ../DESIGN_SYSTEM.md (developer guide)
//   - Clear Settings Script: ../clear-elementor-site-settings.php
//
// ========================================

/**
 * Disable Elementor's default colors and fonts
 * 
 * This function ensures Elementor's default color and typography schemes
 * are disabled, allowing theme CSS to have full control over styling.
 * 
 * This is a critical part of the design system architecture where:
 * - Elementor handles structure and layout only
 * - Theme CSS handles all visual styling
 * 
 * @see DESIGN_SYSTEM.md for complete design system documentation
 * @see style-guide.html for visual style guide
 */
add_action('admin_init', 'ehs_disable_elementor_defaults');
function ehs_disable_elementor_defaults() {
    if (!did_action('elementor/loaded')) {
        return;
    }
    
    // Get current Elementor settings
    $elementor_settings = get_option('elementor_settings', array());
    
    // Disable default colors and fonts to allow theme CSS control
    $elementor_settings['disable_color_schemes'] = 'yes';
    $elementor_settings['disable_typography_schemes'] = 'yes';
    
    update_option('elementor_settings', $elementor_settings);
}

