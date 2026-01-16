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

add_action('wp_enqueue_scripts', 'hello_elementor_child_enqueue_styles', 20);
function hello_elementor_child_enqueue_styles() {
    wp_enqueue_style(
        'hello-elementor-parent-style',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme(get_template())->get('Version')
    );

    // Load child theme CSS with high priority to ensure it loads after Elementor CSS
    // This prevents Elementor CSS from overriding theme styles
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
require_once get_stylesheet_directory() . '/inc/post-types/credentials-post-type.php';
require_once get_stylesheet_directory() . '/inc/post-types/clients-post-type.php';
require_once get_stylesheet_directory() . '/inc/post-types/team-post-type.php';

// ========================================
// TAXONOMIES
// ========================================

require_once get_stylesheet_directory() . '/inc/taxonomies/services-taxonomies.php';

// ========================================
// META FIELDS
// ========================================

require_once get_stylesheet_directory() . '/inc/meta-fields/services-meta-fields.php';
require_once get_stylesheet_directory() . '/inc/meta-fields/services-meta-box.php';
require_once get_stylesheet_directory() . '/inc/meta-fields/credentials-meta-fields.php';
require_once get_stylesheet_directory() . '/inc/meta-fields/credentials-meta-box.php';
require_once get_stylesheet_directory() . '/inc/meta-fields/clients-meta-fields.php';
require_once get_stylesheet_directory() . '/inc/meta-fields/clients-meta-box.php';
require_once get_stylesheet_directory() . '/inc/meta-fields/team-meta-fields.php';

// ========================================
// ADMIN CUSTOMIZATIONS
// ========================================

require_once get_stylesheet_directory() . '/inc/admin/admin-columns-services.php';
require_once get_stylesheet_directory() . '/inc/admin/admin-columns-credentials.php';
require_once get_stylesheet_directory() . '/inc/admin/admin-columns-clients.php';
require_once get_stylesheet_directory() . '/inc/admin/classic-editor.php';
require_once get_stylesheet_directory() . '/inc/admin/disable-comments.php';
require_once get_stylesheet_directory() . '/inc/admin/contact-form-settings.php';
require_once get_stylesheet_directory() . '/inc/admin/contact-form-entries.php';
require_once get_stylesheet_directory() . '/inc/admin/style-guide-page.php';
require_once get_stylesheet_directory() . '/inc/admin/acf-site-options.php';

/**
 * Enqueue Admin Styles
 * Applies EHS design system to WordPress admin area
 */
add_action('admin_enqueue_scripts', 'ehs_enqueue_admin_styles');
function ehs_enqueue_admin_styles($hook) {
    // Ensure dashicons are loaded first (WordPress should do this automatically, but we'll be explicit)
    wp_enqueue_style('dashicons');
    
    wp_enqueue_style(
        'ehs-admin-styles',
        get_stylesheet_directory_uri() . '/assets/css/admin.css',
        array('dashicons'), // Make dashicons a dependency
        wp_get_theme()->get('Version')
    );
}

/**
 * Check if request is actually over HTTPS
 * Works better with DDEV and reverse proxies
 */
function ehs_is_https_request() {
    // Check if we're actually being accessed over HTTPS
    if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) === 'on') {
        return true;
    }
    if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        return true;
    }
    if (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') {
        return true;
    }
    // Check if the request URL starts with https
    if (isset($_SERVER['REQUEST_URI']) && isset($_SERVER['HTTP_HOST'])) {
        $request_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        if (strpos($request_url, 'https://') === 0) {
            return true;
        }
    }
    return false;
}

/**
 * Force HTTPS for Site URLs in Admin
 * Fixes mixed content warnings when site is accessed over HTTPS
 */
add_filter('site_url', 'ehs_force_https_site_url', 10, 4);
add_filter('home_url', 'ehs_force_https_home_url', 10, 4);
add_filter('admin_url', 'ehs_force_https_admin_url', 10, 3);
add_filter('content_url', 'ehs_force_https_content_url', 10, 2);
add_filter('plugins_url', 'ehs_force_https_plugins_url', 10, 3);
add_filter('includes_url', 'ehs_force_https_includes_url', 10, 2);
add_filter('get_site_icon_url', 'ehs_force_https_site_icon_url', 10, 1);

function ehs_force_https_site_url($url, $path, $scheme, $blog_id) {
    if (is_admin() && ehs_is_https_request()) {
        return str_replace('http://', 'https://', $url);
    }
    return $url;
}

function ehs_force_https_home_url($url, $path, $scheme, $blog_id) {
    if (is_admin() && ehs_is_https_request()) {
        return str_replace('http://', 'https://', $url);
    }
    return $url;
}

function ehs_force_https_admin_url($url, $path, $scheme) {
    if (is_admin() && ehs_is_https_request()) {
        return str_replace('http://', 'https://', $url);
    }
    return $url;
}

function ehs_force_https_content_url($url, $path) {
    if (is_admin() && ehs_is_https_request()) {
        return str_replace('http://', 'https://', $url);
    }
    return $url;
}

function ehs_force_https_plugins_url($url, $path, $plugin) {
    if (is_admin() && ehs_is_https_request()) {
        return str_replace('http://', 'https://', $url);
    }
    return $url;
}

function ehs_force_https_includes_url($url, $path) {
    if (is_admin() && ehs_is_https_request()) {
        return str_replace('http://', 'https://', $url);
    }
    return $url;
}

function ehs_force_https_site_icon_url($url) {
    if (is_admin() && ehs_is_https_request() && $url && strpos($url, 'http://') === 0) {
        return str_replace('http://', 'https://', $url);
    }
    return $url;
}

/**
 * Fix Favicon URLs to Use HTTPS
 * Specifically fixes favicon mixed content warnings
 */
add_action('admin_head', 'ehs_fix_favicon_https', 1);
function ehs_fix_favicon_https() {
    if (!ehs_is_https_request()) {
        return;
    }
    
    // Remove existing favicon links that might be HTTP
    echo '<script>
        (function() {
            var links = document.querySelectorAll("link[rel*=\'icon\']");
            links.forEach(function(link) {
                if (link.href && link.href.indexOf("http://") === 0) {
                    link.remove();
                }
            });
        })();
    </script>' . "\n";
    
    // Get site icon URL and force HTTPS
    $site_icon_id = get_option('site_icon');
    if ($site_icon_id) {
        $site_icon_url = get_site_icon_url();
        if ($site_icon_url) {
            // Ensure HTTPS
            if (strpos($site_icon_url, 'http://') === 0) {
                $site_icon_url = str_replace('http://', 'https://', $site_icon_url);
            }
            // Output favicon links
            echo '<link rel="icon" href="' . esc_url($site_icon_url) . '" sizes="32x32" />' . "\n";
            echo '<link rel="icon" href="' . esc_url($site_icon_url) . '" sizes="192x192" />' . "\n";
        }
    }
}

// ========================================
// HELPER FUNCTIONS
// ========================================

require_once get_stylesheet_directory() . '/inc/helpers/site-options.php';

// ========================================
// FRONTEND FEATURES
// ========================================

require_once get_stylesheet_directory() . '/inc/frontend/ddev-local-header-bar.php';
require_once get_stylesheet_directory() . '/inc/frontend/service-content-blocks.php';
require_once get_stylesheet_directory() . '/inc/frontend/contact-form.php';
require_once get_stylesheet_directory() . '/inc/frontend/contact-form-handler.php';
require_once get_stylesheet_directory() . '/inc/frontend/home-page-functions.php';
require_once get_stylesheet_directory() . '/inc/frontend/credential-cards.php';

// ========================================
// MEGA MENU
// ========================================

/**
 * Custom Walker for Mega Menu
 * Converts WordPress menu structure into mega menu format
 * 
 * Menu Structure:
 * - Level 0: Top-level menu items
 * - Level 1: Column headers in mega menu (becomes .mega-menu-column-title)
 * - Level 2: Links within each column (becomes .sub-menu items)
 */
class EHS_Mega_Menu_Walker extends Walker_Nav_Menu {
    
    /**
     * Start the list before the elements are added
     */
    function start_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        
        if ($depth === 0) {
            // First level submenu - create mega menu container
            $output .= "\n$indent<div class=\"mega-menu\">\n";
            $output .= "$indent\t<div class=\"mega-menu-content\">\n";
        } elseif ($depth === 1) {
            // Depth 1 items are column headers - the column div is created in start_el
            // WordPress calls start_lvl(depth=2) before the first depth 2 child
            // So we need to open the <ul> here for depth 2 children
            $output .= "\n$indent<ul class=\"sub-menu\">\n";
        } elseif ($depth === 2) {
            // Depth 2 - these are already inside the ul from depth 1's start_lvl
            // No need to open another ul here
            $output .= "";
        } else {
            // Deeper levels - standard ul
            $output .= "\n$indent<ul class=\"sub-menu\">\n";
        }
    }
    
    /**
     * End the list of after the elements are added
     */
    function end_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        
        if ($depth === 0) {
            // Close mega menu container
            $output .= "$indent\t</div>\n";
            $output .= "$indent</div>\n";
        } elseif ($depth === 1) {
            // Depth 1: Close the <ul> that was opened in start_lvl for depth 2
            $output .= "$indent</ul>\n";
        } elseif ($depth === 2) {
            // Depth 2: No ul to close here (it's closed in end_lvl for depth 1)
            $output .= "";
        } else {
            // Close standard ul
            $output .= "$indent</ul>\n";
        }
    }
    
    /**
     * Start the element output
     */
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';
        
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;
        
        // WordPress should automatically add menu-item-has-children class
        // But ensure it's in the classes array if this item has children
        // Check by looking for the class that WordPress adds
        if (!in_array('menu-item-has-children', $classes)) {
            // Check if item has children by querying menu items
            $has_children = false;
            if ($depth === 0) {
                // Only check for top-level items
                $menu_items = wp_get_nav_menu_items(wp_get_nav_menu_object($args->menu)->term_id);
                foreach ($menu_items as $menu_item) {
                    if ($menu_item->menu_item_parent == $item->ID) {
                        $has_children = true;
                        break;
                    }
                }
            }
            if ($has_children) {
                $classes[] = 'menu-item-has-children';
            }
        }
        
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';
        
        $id = apply_filters('nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';
        
        if ($depth === 1) {
            // Second level items become column headers in mega menu
            // Output column div and title, but NOT the ul (that comes from start_lvl for depth 2)
            $output .= $indent . '<div class="mega-menu-column">';
            $output .= '<h4 class="mega-menu-column-title">' . esc_html($item->title) . '</h4>';
            // The <ul class="sub-menu"> will be opened by start_lvl when depth === 2
        } elseif ($depth === 2) {
            // Third level items - links within columns (these are the actual service links)
            $output .= $indent . '<li>';
            $attributes = ! empty($item->attr_title) ? ' title="'  . esc_attr($item->attr_title) .'"' : '';
            $attributes .= ! empty($item->target)     ? ' target="' . esc_attr($item->target     ) .'"' : '';
            $attributes .= ! empty($item->xfn)        ? ' rel="'    . esc_attr($item->xfn        ) .'"' : '';
            $attributes .= ! empty($item->url)        ? ' href="'   . esc_attr($item->url        ) .'"' : '';

            // Determine service icon based on title and URL
            $service_icon = $this->get_service_icon($item->title, $item->url);

            $output .= '<a' . $attributes .'>';
            $output .= $service_icon;
            $output .= '<span class="service-link-text">' . esc_html($item->title) . '</span>';
            $output .= '</a>';
        } else {
            // First level - standard menu item
            $output .= $indent . '<li' . $id . $class_names .'>';

            $attributes = ! empty($item->attr_title) ? ' title="'  . esc_attr($item->attr_title) .'"' : '';
            $attributes .= ! empty($item->target)     ? ' target="' . esc_attr($item->target     ) .'"' : '';
            $attributes .= ! empty($item->xfn)        ? ' rel="'    . esc_attr($item->xfn        ) .'"' : '';
            $attributes .= ! empty($item->url)        ? ' href="'   . esc_attr($item->url        ) .'"' : '';

            // Determine icon class based on menu item title/slug
            $icon_html = '';
            $title_lower = strtolower($item->title);
            if (strpos($title_lower, 'service') !== false) {
                $icon_html = '<span class="nav-icon nav-icon-services"></span>';
            } elseif (strpos($title_lower, 'about') !== false) {
                $icon_html = '<span class="nav-icon nav-icon-about"></span>';
            } elseif (strpos($title_lower, 'contact') !== false) {
                $icon_html = '<span class="nav-icon nav-icon-contact"></span>';
            } elseif (strpos($title_lower, 'resource') !== false || strpos($title_lower, 'blog') !== false) {
                $icon_html = '<span class="nav-icon nav-icon-resources"></span>';
            } elseif (strpos($title_lower, 'insight') !== false) {
                $icon_html = '<span class="nav-icon nav-icon-insights"></span>';
            }

            $item_output = isset($args->before) ? $args->before : '';
            $item_output .= '<a' . $attributes .'>';
            $item_output .= $icon_html;
            $item_output .= (isset($args->link_before) ? $args->link_before : '') . apply_filters('the_title', $item->title, $item->ID) . (isset($args->link_after) ? $args->link_after : '');
            $item_output .= '</a>';
            $item_output .= isset($args->after) ? $args->after : '';

            $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
        }
    }
    
    /**
     * End the element output
     */
    function end_el(&$output, $item, $depth = 0, $args = null) {
        if ($depth === 1) {
            // Close mega menu column (ul is closed by end_lvl for depth 2)
            $output .= '</div>';
        } elseif ($depth === 2) {
            // Close third level item (li)
            $output .= "</li>\n";
        } else {
            // Close standard menu item
            $output .= "</li>\n";
        }
    }

    /**
     * Get service icon based on title - stroke-width 2 to match header icons
     * First tries to get icon from service post type, then falls back to keyword matching
     */
    function get_service_icon($title, $url = '') {
        // First, try to find the service post and get its icon
        $service_icon = $this->get_service_icon_from_post($title, $url);
        if ($service_icon) {
            return $service_icon;
        }

        // Fall back to keyword matching
        $title_lower = strtolower($title);

        // Icon SVGs with stroke-width="2" matching phone/nav icons
        $icons = array(
            // Consulting & Staff
            'consulting' => '<svg class="service-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>',
            'outsourcing' => '<svg class="service-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
            'staff' => '<svg class="service-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',

            // Testing & Assessment
            'air quality' => '<svg class="service-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.59 4.59A2 2 0 1 1 11 8H2m10.59 11.41A2 2 0 1 0 14 16H2m15.73-8.27A2.5 2.5 0 1 1 19.5 12H2"></path></svg>',
            'indoor' => '<svg class="service-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.59 4.59A2 2 0 1 1 11 8H2m10.59 11.41A2 2 0 1 0 14 16H2m15.73-8.27A2.5 2.5 0 1 1 19.5 12H2"></path></svg>',
            'mold testing' => '<svg class="service-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 2v6m6-6v6"></path><path d="M3 10h18"></path><path d="M5 10v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V10"></path><circle cx="9" cy="15" r="1"></circle><circle cx="15" cy="15" r="1"></circle><path d="M12 10v5"></path></svg>',
            'mold' => '<svg class="service-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>',
            'asbestos' => '<svg class="service-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>',
            'water' => '<svg class="service-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path></svg>',
            'fire' => '<svg class="service-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"></path></svg>',
            'smoke' => '<svg class="service-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"></path></svg>',

            // Construction Safety
            'ssho' => '<svg class="service-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 10V6a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v4"></path><path d="M4 10h16"></path><path d="M6 10v9a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1v-9"></path><path d="M6 10a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2"></path><line x1="9" y1="14" x2="9" y2="16"></line><line x1="15" y1="14" x2="15" y2="16"></line></svg>',
            'construction' => '<svg class="service-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 10V6a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v4"></path><path d="M4 10h16"></path><path d="M6 10v9a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1v-9"></path><path d="M6 10a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2"></path><line x1="9" y1="14" x2="9" y2="16"></line><line x1="15" y1="14" x2="15" y2="16"></line></svg>',
            'lead' => '<svg class="service-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>',
            'caltrans' => '<svg class="service-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path><line x1="4" y1="22" x2="4" y2="15"></line></svg>',
            'safety' => '<svg class="service-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><polyline points="9 12 11 14 15 10"></polyline></svg>',

            // Federal Services
            'federal' => '<svg class="service-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"></path><path d="M5 21V7l8-4v18"></path><path d="M19 21V11l-6-4"></path><path d="M9 9v.01"></path><path d="M9 12v.01"></path><path d="M9 15v.01"></path><path d="M9 18v.01"></path></svg>',
            'contract' => '<svg class="service-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"></path><path d="M5 21V7l8-4v18"></path><path d="M19 21V11l-6-4"></path><path d="M9 9v.01"></path><path d="M9 12v.01"></path><path d="M9 15v.01"></path><path d="M9 18v.01"></path></svg>',
            'government' => '<svg class="service-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"></path><path d="M5 21V7l8-4v18"></path><path d="M19 21V11l-6-4"></path><path d="M9 9v.01"></path><path d="M9 12v.01"></path><path d="M9 15v.01"></path><path d="M9 18v.01"></path></svg>',

            // Environmental
            'environmental' => '<svg class="service-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 22c1.25-1.25 2.5-2 4-2 3 0 3 2 6 2s3-2 6-2c1.5 0 2.75.75 4 2"></path><path d="M12 2c-3 4-5 8-5 12"></path><path d="M12 2c3 4 5 8 5 12"></path><path d="M12 2v12"></path></svg>',
            'compliance' => '<svg class="service-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>',
            'training' => '<svg class="service-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c0 2 2 3 6 3s6-1 6-3v-5"></path></svg>',
            'audit' => '<svg class="service-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
        );

        // Check each keyword against title
        foreach ($icons as $keyword => $svg) {
            if (strpos($title_lower, $keyword) !== false) {
                return $svg;
            }
        }

        // Default icon - arrow right (for any unmatched service)
        return '<svg class="service-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>';
    }

    /**
     * Get service icon from service post type
     * Looks up service post by title or URL and returns its SVG icon
     * 
     * @param string $title Menu item title
     * @param string $url Menu item URL (optional)
     * @return string|false SVG markup or false if not found
     */
    function get_service_icon_from_post($title, $url = '') {
        // Try to find service post by title first using WP_Query (replaces deprecated get_page_by_title)
        // WP_Query doesn't have a direct 'title' parameter, so we query by exact title match
        global $wpdb;
        
        $service_id = $wpdb->get_var($wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts} 
            WHERE post_type = 'services' 
            AND post_status = 'publish' 
            AND post_title = %s 
            LIMIT 1",
            $title
        ));
        
        $service = $service_id ? get_post($service_id) : null;
        
        // If not found by title and URL provided, try to extract slug from URL
        if (!$service && !empty($url)) {
            $url_path = parse_url($url, PHP_URL_PATH);
            $url_path = trim($url_path, '/');
            if (!empty($url_path)) {
                $service = get_page_by_path($url_path, OBJECT, 'services');
            }
        }

        // If still not found, try searching by slug derived from title
        if (!$service) {
            $slug = sanitize_title($title);
            $service = get_page_by_path($slug, OBJECT, 'services');
        }

        if (!$service) {
            return false;
        }

        // Get service_icon meta field (attachment ID)
        $icon_id = get_post_meta($service->ID, 'service_icon', true);
        if (!$icon_id) {
            return false;
        }

        // Get attachment file path
        $icon_path = get_attached_file($icon_id);
        if (!$icon_path || !file_exists($icon_path)) {
            return false;
        }

        // Check if it's an SVG file
        $file_ext = strtolower(pathinfo($icon_path, PATHINFO_EXTENSION));
        if ($file_ext !== 'svg') {
            // If it's not an SVG, we could return the image URL, but for menu we want SVG
            // So return false to fall back to keyword matching
            return false;
        }

        // Read SVG file content
        $svg_content = file_get_contents($icon_path);
        if (!$svg_content) {
            return false;
        }

        // Add service-icon class and ensure proper attributes
        // Remove any existing class attributes and add our class
        $svg_content = preg_replace('/class=["\'][^"\']*["\']/', '', $svg_content);
        
        // Ensure stroke-width is 2 to match other menu icons
        $svg_content = preg_replace('/stroke-width=["\']?[^"\'\s>]+["\']?/i', 'stroke-width="2"', $svg_content);
        
        // Add class and ensure fill is none
        $svg_content = preg_replace('/<svg\s+/', '<svg class="service-icon" ', $svg_content);
        $svg_content = preg_replace('/<svg([^>]*)>/', '<svg$1 fill="none">', $svg_content);
        
        // Ensure stroke is currentColor
        if (strpos($svg_content, 'stroke=') === false) {
            $svg_content = preg_replace('/<svg([^>]*)>/', '<svg$1 stroke="currentColor">', $svg_content);
        } else {
            $svg_content = preg_replace('/stroke=["\'][^"\']*["\']/i', 'stroke="currentColor"', $svg_content);
        }

        return $svg_content;
    }
}

/**
 * Enqueue Mega Menu JavaScript
 */
add_action('wp_enqueue_scripts', 'ehs_enqueue_mega_menu_assets');
function ehs_enqueue_mega_menu_assets() {
    wp_enqueue_script(
        'ehs-mega-menu',
        get_stylesheet_directory_uri() . '/assets/js/mega-menu.js',
        array(),
        wp_get_theme()->get('Version'),
        true
    );
}

/**
 * Enqueue ToC scripts for services and blog posts
 */
add_action('wp_enqueue_scripts', 'ehs_enqueue_service_toc_assets');
function ehs_enqueue_service_toc_assets() {
    // Load TOC script for services and single blog posts
    if (!is_singular('services') && !is_singular('post')) {
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
 * Priority 21 ensures CSS loads AFTER child theme styles (priority 20)
 */
add_action('wp_enqueue_scripts', 'ehs_enqueue_contact_form_assets', 21);
function ehs_enqueue_contact_form_assets() {
    // Enqueue CSS - depends on child theme styles to prevent flash/shift
    wp_enqueue_style(
        'ehs-contact-form',
        get_stylesheet_directory_uri() . '/assets/css/contact-form.css',
        array('hello-elementor-child-style'),
        wp_get_theme()->get('Version')
    );

    // Enqueue Cloudflare Turnstile script if configured
    $turnstile_site_key = get_option('ehs_turnstile_site_key', '');
    if (!empty($turnstile_site_key)) {
        wp_enqueue_script(
            'cloudflare-turnstile',
            'https://challenges.cloudflare.com/turnstile/v0/api.js',
            array(),
            null,
            true
        );
    }

    // Enqueue JS (requires jQuery)
    wp_enqueue_script(
        'ehs-contact-form',
        get_stylesheet_directory_uri() . '/assets/js/contact-form.js',
        array('jquery'),
        wp_get_theme()->get('Version'),
        true
    );

    // Localize script with AJAX URL
    wp_localize_script('ehs-contact-form', 'ehsContactForm', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
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

// ========================================
// BLOG POST HELPERS
// ========================================

/**
 * Calculate estimated reading time for a post
 *
 * @param int|null $post_id Post ID (optional, defaults to current post)
 * @return int Reading time in minutes (minimum 1)
 */
function ehs_get_reading_time($post_id = null) {
    $post_id = $post_id ? $post_id : get_the_ID();
    $content = get_post_field('post_content', $post_id);
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200);
    return max(1, $reading_time);
}

// ========================================
// DISABLE EMOJI CONVERSION FOR FOOTER SVGs
// ========================================

/**
 * Prevent WordPress from converting SVGs to emoji images in footer
 * This ensures footer contact icons render as SVGs, not emoji images
 */
add_filter('wp_kses_allowed_html', 'ehs_allow_svg_in_footer', 10, 2);
function ehs_allow_svg_in_footer($allowed, $context) {
    if ($context === 'post' || $context === 'page') {
        $allowed['svg'] = array(
            'xmlns' => array(),
            'viewbox' => array(),
            'fill' => array(),
            'stroke' => array(),
            'stroke-width' => array(),
            'stroke-linecap' => array(),
            'stroke-linejoin' => array(),
            'width' => array(),
            'height' => array(),
        );
        $allowed['path'] = array(
            'd' => array(),
        );
    }
    return $allowed;
}

/**
 * Disable emoji conversion for footer template output
 */
add_action('template_redirect', 'ehs_disable_emoji_for_footer');
function ehs_disable_emoji_for_footer() {
    // Only disable emoji on footer output
    if (is_admin() || wp_doing_ajax()) {
        return;
    }
    
    // Remove emoji conversion filters temporarily when rendering footer
    remove_filter('the_content', 'wp_staticize_emoji');
    remove_filter('the_excerpt', 'wp_staticize_emoji');
    remove_filter('widget_text_content', 'wp_staticize_emoji');
}

