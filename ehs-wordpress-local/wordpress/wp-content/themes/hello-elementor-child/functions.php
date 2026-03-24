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
// ENABLE SVG UPLOADS
// ========================================

add_filter('upload_mimes', 'ehs_allow_svg_upload');
function ehs_allow_svg_upload($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';
    return $mimes;
}

add_filter('wp_check_filetype_and_ext', 'ehs_fix_svg_mime_type', 10, 5);
function ehs_fix_svg_mime_type($data, $file, $filename, $mimes, $real_mime = null) {
    if (isset($data['ext']) && $data['ext'] === 'svg') {
        $data['type'] = 'image/svg+xml';
        $data['ext'] = 'svg';
    }
    return $data;
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
    // Use filemtime for cache busting to ensure latest CSS loads
    wp_enqueue_style(
        'hello-elementor-child-style',
        get_stylesheet_uri(),
        array('hello-elementor-parent-style'),
        filemtime(get_stylesheet_directory() . '/style.css')
    );

    // Enqueue accordion JavaScript
    wp_enqueue_script(
        'ehs-service-accordions',
        get_stylesheet_directory_uri() . '/assets/js/service-accordions.js',
        array(),
        wp_get_theme()->get('Version'),
        true
    );

    // Enqueue project timeline JavaScript
    wp_enqueue_script(
        'ehs-project-timeline',
        get_stylesheet_directory_uri() . '/assets/js/project-timeline.js',
        array(),
        wp_get_theme()->get('Version'),
        true
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
require_once get_stylesheet_directory() . '/inc/meta-fields/services-components-meta-fields.php';
require_once get_stylesheet_directory() . '/inc/meta-fields/services-components-meta-box.php';
require_once get_stylesheet_directory() . '/inc/meta-fields/credentials-meta-fields.php';
require_once get_stylesheet_directory() . '/inc/meta-fields/credentials-meta-box.php';
require_once get_stylesheet_directory() . '/inc/meta-fields/clients-meta-fields.php';
require_once get_stylesheet_directory() . '/inc/meta-fields/clients-meta-box.php';
require_once get_stylesheet_directory() . '/inc/meta-fields/team-meta-fields.php';

// ========================================
// LOGO FALLBACK (ensure logo SVGs in uploads to avoid 404)
// ========================================

require_once get_stylesheet_directory() . '/inc/logo-fallback.php';

// ========================================
// MEDIA FALLBACK (placeholder when attachment file missing from uploads)
// ========================================

require_once get_stylesheet_directory() . '/inc/media-fallback.php';

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
require_once get_stylesheet_directory() . '/inc/admin/services-order-sync.php';
require_once get_stylesheet_directory() . '/inc/admin/acf-site-options.php';
require_once get_stylesheet_directory() . '/inc/admin/acf-service-special-content.php';

// ========================================
// FRONTEND RENDERING
// ========================================

require_once get_stylesheet_directory() . '/inc/frontend/service-special-content.php';
require_once get_stylesheet_directory() . '/inc/content-icons.php';

/**
 * Ensure project timeline has progress element for scroll-fill animation.
 * project-timeline.js requires .project-timeline__progress inside the timeline;
 * inject it when missing so the timeline animates on scroll.
 */
add_filter('the_content', 'ehs_ensure_project_timeline_progress', 5);
function ehs_ensure_project_timeline_progress($content) {
    if (strpos($content, 'project-timeline__line') === false) {
        return $content;
    }
    if (strpos($content, 'project-timeline__progress') !== false) {
        return $content;
    }
    $progress = '<div class="project-timeline__progress" aria-hidden="true"></div>';
    $content = preg_replace(
        '/(<div\s+class="[^"]*project-timeline__line[^"]*"[^>]*>)/',
        '$1' . $progress,
        $content
    );
    return $content;
}

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
// FRONT PAGE SEO (from Business Information → Homepage SEO; overrides Yoast on homepage so client can edit in one place)
// ========================================
add_filter('pre_get_document_title', 'ehs_front_page_document_title', 10, 1);
function ehs_front_page_document_title($title) {
    if (!is_front_page()) {
        return $title;
    }
    $t = ehs_get_option('homepage_seo_title');
    return $t ? $t : $title;
}

add_action('wp_head', 'ehs_front_page_meta_description', 5);
function ehs_front_page_meta_description() {
    if (!is_front_page()) {
        return;
    }
    $d = ehs_get_option('homepage_seo_description');
    if ($d) {
        echo '<meta name="description" content="' . esc_attr($d) . '">' . "\n";
    }
}

add_filter('wpseo_opengraph_title', function ($title) {
    if (!is_front_page()) {
        return $title;
    }
    $t = ehs_get_option('homepage_seo_title');
    return $t ? $t : $title;
}, 20);
add_filter('wpseo_opengraph_desc', function ($desc) {
    if (!is_front_page()) {
        return $desc;
    }
    $d = ehs_get_option('homepage_seo_description');
    return $d ? $d : $desc;
}, 20);
add_filter('wpseo_twitter_title', function ($title) {
    if (!is_front_page()) {
        return $title;
    }
    $t = ehs_get_option('homepage_seo_title');
    return $t ? $t : $title;
}, 20);
add_filter('wpseo_twitter_description', function ($desc) {
    if (!is_front_page()) {
        return $desc;
    }
    $d = ehs_get_option('homepage_seo_description');
    return $d ? $d : $desc;
}, 20);

// Lead Compliance Plan service page SEO (PHP template at /lead-compliance-plan-services/)
add_filter('pre_get_document_title', 'ehs_lead_compliance_plan_document_title', 10, 1);
function ehs_lead_compliance_plan_document_title($title) {
    if (!is_singular('services')) {
        return $title;
    }
    if (get_post_field('post_name', get_queried_object_id()) !== 'lead-compliance-plan-services') {
        return $title;
    }
    return 'Lead Compliance Plan Services | DVBE CIH Experts | Caltrans Approved';
}

add_action('wp_head', 'ehs_lead_compliance_plan_meta_description', 6);
function ehs_lead_compliance_plan_meta_description() {
    if (!is_singular('services')) {
        return;
    }
    if (get_post_field('post_name', get_queried_object_id()) !== 'lead-compliance-plan-services') {
        return;
    }
    echo '<meta name="description" content="DVBE-certified Lead Compliance Plan development for Caltrans bridge projects. CIH experts in Cal/OSHA 1532.1 compliance, Work Area Monitoring, and lead exposure assessment. 100+ plans completed across all 12 California districts.">' . "\n";
}

// ========================================
// FRONTEND FEATURES
// ========================================

require_once get_stylesheet_directory() . '/inc/frontend/ddev-local-header-bar.php';
require_once get_stylesheet_directory() . '/inc/frontend/service-content-blocks.php';
require_once get_stylesheet_directory() . '/inc/frontend/service-components-render.php';
require_once get_stylesheet_directory() . '/inc/frontend/service-components-shortcodes.php';
require_once get_stylesheet_directory() . '/inc/frontend/contact-form.php';
require_once get_stylesheet_directory() . '/inc/frontend/contact-form-handler.php';
require_once get_stylesheet_directory() . '/inc/wp-mail-resend.php';
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
        
        // Add has-mega-menu for Services (top-level item with children) so mega menu layout applies
        if ($depth === 0 && in_array('menu-item-has-children', $classes)) {
            $title_lower = strtolower($item->title);
            $url = isset($item->url) ? strtolower($item->url) : '';
            if (strpos($title_lower, 'service') !== false || strpos($url, '/services') !== false) {
                $classes[] = 'has-mega-menu';
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
            $output .= '<h4 class="mega-menu-column-title">' . nl2br(esc_html($item->title)) . '</h4>';
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
 * Canonical 5-column mega menu structure (per DEVELOPER_IMPLEMENTATION_GUIDE.md Part 1).
 * Order and titles are fixed; services are mapped into these columns only.
 */
function ehs_mega_menu_canonical_columns() {
    return array(
        "EHS\nConsulting", /* two-line title for even column headers */
        'Construction Safety',
        'Industrial Hygiene',
        'Environmental Testing',
        'Specialized Services',
    );
}

/**
 * Map a service (by title/slug) to one of the 5 canonical column indices (0-4).
 * Returns column index or null if not in nav (e.g. excluded).
 *
 * @param string $title Service post title
 * @param string $slug  Service post slug
 * @return int|null 0-4 for column index, or null to exclude
 */
function ehs_mega_menu_service_to_column($title, $slug) {
    $t = strtolower($title);
    $s = strtolower($slug);

    // Exclude from nav (per guide)
    if (strpos($t, 'ergonomic') !== false || strpos($t, 'fume hood') !== false ||
        strpos($s, 'ergonomic') !== false || strpos($s, 'fume-hood') !== false) {
        return null;
    }

    // Column 1: EHS Consulting — EHS Consulting, EHS Staff Outsourcing
    if (strpos($t, 'ehs consulting') !== false || strpos($s, 'ehs-consulting') !== false) {
        return 0;
    }
    if (strpos($t, 'staff outsourcing') !== false || strpos($t, 'ehs staff') !== false ||
        strpos($s, 'staff-outsourcing') !== false || strpos($s, 'ehs-staff') !== false) {
        return 0;
    }

    // Column 2: Construction Safety — Construction Safety Consulting, SSHO, Caltrans, Federal Contracting
    if (strpos($t, 'construction safety') !== false || strpos($s, 'construction-safety') !== false) {
        return 1;
    }
    if (strpos($t, 'ssho') !== false || strpos($t, 'federal military') !== false ||
        strpos($s, 'ssho') !== false || strpos($s, 'federal-military') !== false) {
        return 1;
    }
    if (strpos($t, 'caltrans') !== false || strpos($s, 'caltrans') !== false) {
        return 1;
    }
    if (strpos($t, 'federal contracting') !== false || strpos($s, 'federal-contracting') !== false) {
        return 1;
    }

    // Column 3: Industrial Hygiene — Industrial Hygiene Services, Lead Compliance Plans
    if (strpos($t, 'industrial hygiene') !== false || strpos($s, 'industrial-hygiene') !== false) {
        return 2;
    }
    if (strpos($t, 'lead compliance') !== false || strpos($s, 'lead-compliance') !== false) {
        return 2;
    }

    // Column 4: Environmental Testing — Mold Testing, Asbestos Testing, Indoor Air Quality
    if (strpos($t, 'mold testing') !== false || strpos($s, 'mold-testing') !== false) {
        return 3;
    }
    if (strpos($t, 'asbestos') !== false || strpos($s, 'asbestos') !== false) {
        return 3;
    }
    if (strpos($t, 'indoor air quality') !== false || strpos($s, 'indoor-air-quality') !== false ||
        strpos($t, 'iaq') !== false) {
        return 3;
    }

    // Column 5: Specialized Services — Water Damage Assessments, Fire & Smoke Assessments
    if (strpos($t, 'water damage') !== false || strpos($s, 'water-damage') !== false) {
        return 4;
    }
    if (strpos($t, 'fire') !== false && strpos($t, 'smoke') !== false) {
        return 4;
    }
    if (strpos($t, 'fire & smoke') !== false || strpos($t, 'fire and smoke') !== false) {
        return 4;
    }
    if (strpos($s, 'fire') !== false && strpos($s, 'smoke') !== false) {
        return 4;
    }
    if (strpos($t, 'smoke assessment') !== false) {
        return 4;
    }

    // Unmapped services go to Specialized Services (column 5) per guide
    return 4;
}

/**
 * Canonical services display order: section titles and post IDs in the order they appear in the mega menu.
 * This is the single source of truth for "services menu order". Sync applies it to the built-in menu_order post field + service_section meta.
 * Ergonomic / Fume Hood are excluded (not listed).
 *
 * @return array<int, array{title: string, ids: int[]}>
 */
function ehs_services_display_order() {
    return array(
        array(
            'title' => 'EHS Consulting',
            'ids' => array(3286, 3287), // Environmental Health and Safety EHS Consulting, EHS Staff Outsourcing
        ),
        array(
            'title' => 'Construction Safety',
            'ids' => array(3277, 3269, 3273, 3275), // Construction Safety, SSHO, Caltrans, Federal Contracting
        ),
        array(
            'title' => 'Industrial Hygiene',
            'ids' => array(3285, 3271), // Industrial Hygiene San Diego, Lead Compliance Plan Services
        ),
        array(
            'title' => 'Environmental Testing',
            'ids' => array(3283, 3282, 3284), // Mold Testing, Asbestos Testing, Indoor Air Quality
        ),
        array(
            'title' => 'Specialized Services',
            'ids' => array(3280, 3279), // Water Damage Assessments, Fire and Smoke Assessments
        ),
    );
}

/**
 * Apply the canonical services display order to service posts.
 * Sets the built-in menu_order post field (running index) and service_section meta for each post found by ID.
 * Run on-demand via admin "Sync order" or WP-CLI; not automatic.
 *
 * @return array{updated: int, skipped: int[]} Count of posts updated and list of IDs not found or not published.
 */
function ehs_sync_services_display_order() {
    $order = 0;
    $updated = 0;
    $skipped = array();
    foreach (ehs_services_display_order() as $section) {
        $title = $section['title'];
        foreach ($section['ids'] as $post_id) {
            $post_id = (int) $post_id;
            if ($post_id <= 0) {
                continue;
            }
            $post = get_post($post_id);
            if (!$post || $post->post_type !== 'services' || $post->post_status !== 'publish') {
                $skipped[] = $post_id;
                continue;
            }
            wp_update_post(array(
                'ID'         => $post->ID,
                'menu_order' => $order,
            ));
            update_post_meta($post->ID, 'service_section', $title);
            $order++;
            $updated++;
        }
    }
    return array('updated' => $updated, 'skipped' => $skipped);
}

/**
 * Ensure Services mega menu has exactly 5 columns, 1 row, no duplicates.
 * Replaces WP menu children under Services with the canonical 5 columns and mapped service links.
 */
add_filter('wp_nav_menu_objects', 'ehs_fill_services_mega_menu', 10, 2);
function ehs_fill_services_mega_menu($items, $args = null) {
    // Only adjust the primary header menu
    if (empty($items) || !is_object($args) || !isset($args->theme_location) || $args->theme_location !== 'menu-1') {
        return $items;
    }

    // Locate the top-level Services item
    $services_parent = null;
    foreach ($items as $item) {
        if ($item->menu_item_parent == 0 && (stripos($item->title, 'service') !== false || (isset($item->url) && stripos($item->url, '/services') !== false))) {
            $services_parent = $item;
            break;
        }
    }

    if (!$services_parent) {
        return $items;
    }

    $canonical_columns = ehs_mega_menu_canonical_columns();

    // Collect IDs of all items that are UNDER Services (children + descendants only; keep Services parent)
    $under_services_ids = array();
    foreach ($items as $item) {
        if ($item->menu_item_parent == $services_parent->ID) {
            $under_services_ids[$item->ID] = true;
        }
    }
    foreach ($items as $item) {
        if (isset($under_services_ids[$item->menu_item_parent])) {
            $under_services_ids[$item->ID] = true;
        }
    }

    // Keep Services parent and all other top-level items; remove only Services' children and descendants
    $items = array_filter($items, function ($item) use ($under_services_ids) {
        return !isset($under_services_ids[$item->ID]);
    });
    $items = array_values($items);

    $next_id = -1;
    $max_order = !empty($items) ? max(wp_list_pluck($items, 'menu_order')) : 0;

    $make_item = function ($args) use (&$next_id, &$max_order) {
        $obj = new stdClass();
        $obj->ID = $obj->db_id = $next_id--;
        $obj->menu_item_parent = isset($args['parent']) ? $args['parent'] : 0;
        $obj->object_id = isset($args['object_id']) ? $args['object_id'] : 0;
        $obj->object = isset($args['object']) ? $args['object'] : 'custom';
        $obj->type = isset($args['type']) ? $args['type'] : 'custom';
        $obj->type_label = isset($args['type_label']) ? $args['type_label'] : 'Custom Link';
        $obj->title = isset($args['title']) ? $args['title'] : '';
        $obj->url = isset($args['url']) ? $args['url'] : '';
        $obj->target = '';
        $obj->attr_title = '';
        $obj->description = '';
        $obj->classes = isset($args['classes']) ? $args['classes'] : array();
        $obj->xfn = '';
        $obj->status = '';
        $obj->menu_order = ++$max_order;
        return $obj;
    };

    // Create exactly 5 column headers in order
    $column_parent_ids = array();
    foreach ($canonical_columns as $i => $title) {
        $col = $make_item(array('parent' => $services_parent->ID, 'title' => $title));
        $items[] = $col;
        $column_parent_ids[$i] = $col->ID;
    }

    // Fetch all published services (built-in menu_order; supports page-attributes)
    $services = get_posts(array(
        'post_type'      => 'services',
        'post_status'    => 'publish',
        'numberposts'    => -1,
        'orderby'        => array('menu_order' => 'ASC', 'title' => 'ASC'),
        'suppress_filters' => false,
    ));

    $seen_urls = array();
    $column_children = array_fill(0, 5, array());

    foreach ($services as $service) {
        $title = get_the_title($service);
        $slug = $service->post_name;
        $url = get_permalink($service);
        $url_key = untrailingslashit(strtolower($url));

        if (isset($seen_urls[$url_key])) {
            continue;
        }
        $col_index = ehs_mega_menu_service_to_column($title, $slug);
        if ($col_index === null) {
            continue;
        }
        $seen_urls[$url_key] = true;
        $column_children[$col_index][] = array(
            'title' => $title,
            'url'   => $url,
            'id'    => $service->ID,
        );
    }

    // Append child items per column (preserve order within column)
    foreach ($column_children as $col_index => $children) {
        $parent_id = $column_parent_ids[$col_index];
        foreach ($children as $c) {
            $items[] = $make_item(array(
                'parent'     => $parent_id,
                'title'      => $c['title'],
                'url'        => $c['url'],
                'object'     => 'services',
                'object_id'  => $c['id'],
                'type'       => 'post_type',
                'type_label' => __('Service', 'hello-elementor-child'),
            ));
        }
    }

    // Remove Ergonomic Evaluations and Fume Hood Certifications from nav (even if in WP menu)
    $items = array_filter($items, function ($item) {
        $title = isset($item->title) ? strtolower($item->title) : '';
        $url  = isset($item->url) ? strtolower($item->url) : '';
        if (strpos($title, 'ergonomic') !== false || strpos($title, 'fume hood') !== false) {
            return false;
        }
        if (strpos($url, 'ergonomic') !== false || strpos($url, 'fume-hood') !== false) {
            return false;
        }
        return true;
    });
    $items = array_values($items);

    // Ensure Services parent has menu-item-has-children so walker/CSS/JS apply mega menu.
    // The walker checks wp_get_nav_menu_items() (DB only); our columns have negative IDs and
    // are not in the DB, so the walker would not add the class. Set it here on the filtered list.
    foreach ($items as $item) {
        if ($item->ID === $services_parent->ID) {
            $item->classes = isset($item->classes) && is_array($item->classes) ? $item->classes : array();
            if (!in_array('menu-item-has-children', $item->classes)) {
                $item->classes[] = 'menu-item-has-children';
            }
            if (!in_array('has-mega-menu', $item->classes)) {
                $item->classes[] = 'has-mega-menu';
            }
            break;
        }
    }

    return $items;
}

/**
 * Remove outdated "Need a Construction Safety Consultant?" from nav (per Navigation Menu Discrepancies task).
 * Runs after ehs_fill_services_mega_menu so it applies to the final menu (including top-level custom links).
 */
add_filter('wp_nav_menu_objects', 'ehs_remove_construction_consultant_landing_from_nav', 20, 2);
function ehs_remove_construction_consultant_landing_from_nav($items, $args = null) {
    if (empty($items) || !is_object($args) || !isset($args->theme_location) || $args->theme_location !== 'menu-1') {
        return $items;
    }
    $outdated_title = 'Need a Construction Safety Consultant?';
    $outdated_slugs = array('need-a-construction-safety-consultant', 'construction-safety-consultant');
    $remove_ids = array();
    foreach ($items as $item) {
        $title_match = isset($item->title) && stripos($item->title, $outdated_title) !== false;
        $url = isset($item->url) ? strtolower($item->url) : '';
        $slug_match = false;
        foreach ($outdated_slugs as $slug) {
            if (strpos($url, $slug) !== false) {
                $slug_match = true;
                break;
            }
        }
        if ($title_match || $slug_match) {
            $remove_ids[$item->ID] = true;
        }
    }
    // Also remove any item whose parent is being removed
    foreach ($items as $item) {
        if (isset($item->menu_item_parent) && isset($remove_ids[$item->menu_item_parent])) {
            $remove_ids[$item->ID] = true;
        }
    }
    return array_values(array_filter($items, function ($item) use ($remove_ids) {
        return !isset($remove_ids[$item->ID]);
    }));
}

/**
 * Point nav menu About link to /about/ (mega menu and header nav).
 * Menu item may be stored as about-us in WP; we output /about/ for consistency.
 */
add_filter('wp_nav_menu_objects', 'ehs_nav_menu_about_canonical_url', 25, 2);
function ehs_nav_menu_about_canonical_url($items, $args = null) {
	if (empty($items)) {
		return $items;
	}
	$about_url = function_exists('ehs_get_page_url') ? ehs_get_page_url('about') : home_url('/about/');
	foreach ($items as $item) {
		if (empty($item->url)) {
			continue;
		}
		$path = trim(parse_url($item->url, PHP_URL_PATH), '/');
		if (in_array(strtolower($path), array('about-us', 'about'), true)) {
			$item->url = $about_url;
		}
	}
	return $items;
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

/**
 * Ensure singular service pages always use theme PHP header/footer (not Elementor Theme Builder).
 * Task: Template Inconsistency – avoids old nav/footer/copyright on these URLs:
 * - /environmental-health-and-safety-ehs-consulting/
 * - /ehs-staff-outsourcing/
 * - /industrial-hygiene-san-diego/
 * - /san-diego-indoor-air-quality-testing/
 */
add_filter('elementor/theme/get_location_templates/template_id', function ($template_id, $location) {
    if (is_singular('services') && in_array($location, array('header', 'footer'), true)) {
        return 0;
    }
    return $template_id;
}, 10, 2);

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
        $allowed['span'] = array(
            'class' => array(),
            'aria-hidden' => array(),
        );
        $allowed['svg'] = array(
            'xmlns' => array(),
            'viewbox' => array(),
            'viewBox' => array(),
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
        $allowed['circle'] = array(
            'cx' => array(),
            'cy' => array(),
            'r' => array(),
        );
        $allowed['polyline'] = array(
            'points' => array(),
        );
        $allowed['rect'] = array(
            'x' => array(),
            'y' => array(),
            'width' => array(),
            'height' => array(),
            'rx' => array(),
            'ry' => array(),
        );
    }
    return $allowed;
}

/**
 * Redirect legacy fire/smoke URL to canonical (per Navigation Menu Discrepancies task).
 */
add_action('template_redirect', 'ehs_redirect_california_fire_smoke', 5);
function ehs_redirect_california_fire_smoke() {
    if (is_admin() || wp_doing_ajax()) {
        return;
    }
    $request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    $path = trim(parse_url($request_uri, PHP_URL_PATH), '/');
    if (strtolower($path) === 'california-fire-and-smoke-assessments') {
        wp_safe_redirect(home_url('/fire-and-smoke-assessment/'), 301);
        exit;
    }
}

/**
 * Redirect legacy /industrial-hygiene/ to canonical service URL.
 * Task: Broken/Inconsistent Internal Links - all links use /industrial-hygiene-san-diego/.
 */
add_action('template_redirect', 'ehs_redirect_industrial_hygiene_legacy', 5);
function ehs_redirect_industrial_hygiene_legacy() {
	if (is_admin() || wp_doing_ajax()) {
		return;
	}
	$request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
	$path = trim(parse_url($request_uri, PHP_URL_PATH), '/');
	if (strtolower($path) === 'industrial-hygiene') {
		wp_safe_redirect(home_url('/industrial-hygiene-san-diego/'), 301);
		exit;
	}
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
