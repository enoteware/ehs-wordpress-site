<?php
/**
 * Style Guide Admin Page
 *
 * Displays the EHS Design System Style Guide in WordPress admin.
 * Source of truth: theme style-guide/ folder (single location, deploys with theme).
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add Style Guide menu item to WordPress admin
 */
add_action('admin_menu', 'ehs_add_style_guide_menu');
function ehs_add_style_guide_menu() {
    add_menu_page(
        'EHS Style Guide',
        'Style Guide',
        'edit_posts',
        'ehs-style-guide',
        'ehs_render_style_guide_page',
        'dashicons-art',
        30
    );
}

/**
 * Render the Style Guide page
 */
function ehs_render_style_guide_page() {
    $style_guide_path = get_stylesheet_directory() . '/style-guide/style-guide.html';
    if (!file_exists($style_guide_path)) {
        echo '<div class="wrap"><h1>Style Guide</h1><p>Style guide not found at: <code>' . esc_html($style_guide_path) . '</code></p></div>';
        return;
    }

    $html_content = file_get_contents($style_guide_path);

    $theme_logos_url = get_stylesheet_directory_uri() . '/assets/images/logos/';
    $theme_logos_dir = get_stylesheet_directory() . '/assets/images/logos/';
    $theme_uri = get_stylesheet_directory_uri();
    $style_guide_uri = $theme_uri . '/style-guide';

    // 1. docs paths: ../wordpress/wp-content/... → content URL
    $html_content = preg_replace_callback(
        '#\.\./wordpress/wp-content/(?:wordpress/wp-content/)?uploads/([^"\']+)#',
        function ($m) use ($theme_logos_url, $theme_logos_dir) {
            $subpath = $m[1];
            $upload_file = WP_CONTENT_DIR . '/uploads/' . $subpath;
            $basename = basename($subpath);
            if (!file_exists($upload_file) && preg_match('#^(2019/11/final-logo\.svg|2019/09/final-logo-vertical\.svg)$#', $subpath) && file_exists($theme_logos_dir . $basename)) {
                return esc_url($theme_logos_url . $basename);
            }
            return esc_url(content_url('uploads/' . $subpath));
        },
        $html_content
    );
    // 2. wordpress/wp-content/... (theme or legacy paths)
    $html_content = preg_replace_callback(
        '#wordpress/wp-content/wordpress/wp-content/uploads/([^"\']+)#',
        function ($m) use ($theme_logos_url, $theme_logos_dir) {
            $subpath = $m[1];
            $upload_file = WP_CONTENT_DIR . '/uploads/' . $subpath;
            $basename = basename($subpath);
            if (!file_exists($upload_file) && preg_match('#^(2019/11/final-logo\.svg|2019/09/final-logo-vertical\.svg)$#', $subpath) && file_exists($theme_logos_dir . $basename)) {
                return esc_url($theme_logos_url . $basename);
            }
            return esc_url(content_url('uploads/' . $subpath));
        },
        $html_content
    );
    $html_content = preg_replace_callback(
        '#(src|href)="wp-content/uploads/([^"\']+)"#',
        function ($m) use ($theme_logos_url, $theme_logos_dir) {
            $subpath = $m[2];
            $upload_file = WP_CONTENT_DIR . '/uploads/' . $subpath;
            if (!file_exists($upload_file) && preg_match('#^(2019/11/final-logo\.svg|2019/09/final-logo-vertical\.svg)$#', $subpath) && file_exists($theme_logos_dir . basename($subpath))) {
                return $m[1] . '="' . esc_url($theme_logos_url . basename($subpath)) . '"';
            }
            return $m[1] . '="' . esc_url(content_url('uploads/' . $subpath)) . '"';
        },
        $html_content
    );
    // 3. style-guide-assets/ → theme style-guide/style-guide-assets/ URL
    $html_content = str_replace('style-guide-assets/', $style_guide_uri . '/style-guide-assets/', $html_content);
    
    // 4. Theme asset paths: assets/service-icons/... → theme URL
    $theme_assets_url = get_stylesheet_directory_uri() . '/assets/';
    $html_content = str_replace(
        'assets/service-icons/',
        $theme_assets_url . 'service-icons/',
        $html_content
    );
    
    // 5. Square logo path (check multiple possible locations)
    $square_logo_url = null;
    
    // Check WordPress media library (search for attachment with filename)
    $square_logo_attachment = get_posts(array(
        'post_type' => 'attachment',
        'post_mime_type' => 'image/svg+xml',
        'posts_per_page' => 1,
        'meta_query' => array(
            array(
                'key' => '_wp_attached_file',
                'value' => 'ehs_logo_sq',
                'compare' => 'LIKE'
            )
        )
    ));
    
    if (!empty($square_logo_attachment)) {
        $square_logo_url = wp_get_attachment_url($square_logo_attachment[0]->ID);
    } else {
        // Check root directory
        $square_logo_file = ABSPATH . 'ehs_logo_sq.svg';
        if (file_exists($square_logo_file)) {
            $square_logo_url = home_url('/ehs_logo_sq.svg');
        } else {
            // Check uploads directory (various possible locations)
            $upload_dirs = array(
                WP_CONTENT_DIR . '/uploads/ehs_logo_sq.svg',
                WP_CONTENT_DIR . '/uploads/2019/11/ehs_logo_sq.svg',
                WP_CONTENT_DIR . '/uploads/2019/09/ehs_logo_sq.svg',
            );
            foreach ($upload_dirs as $upload_path) {
                if (file_exists($upload_path)) {
                    $square_logo_url = content_url(str_replace(WP_CONTENT_DIR, '', $upload_path));
                    break;
                }
            }
            
            // Check theme directory (logos folder first, then theme root)
            if (!$square_logo_url) {
                $square_logo_in_logos = get_stylesheet_directory() . '/assets/images/logos/ehs_logo_sq.svg';
                if (file_exists($square_logo_in_logos)) {
                    $square_logo_url = get_stylesheet_directory_uri() . '/assets/images/logos/ehs_logo_sq.svg';
                } else {
                    $square_logo_theme = get_stylesheet_directory() . '/ehs_logo_sq.svg';
                    if (file_exists($square_logo_theme)) {
                        $square_logo_url = get_stylesheet_directory_uri() . '/ehs_logo_sq.svg';
                    }
                }
            }
        }
    }
    
    if ($square_logo_url) {
        $html_content = str_replace(
            '../ehs_logo_sq.svg',
            $square_logo_url,
            $html_content
        );
        // Also replace if it's just the filename
        $html_content = preg_replace(
            '#(src|href)="([^"]*)?ehs_logo_sq\.svg"#',
            '$1="' . $square_logo_url . '"',
            $html_content
        );
    }
    
    // 6. Other relative paths ../... → theme URL
    $html_content = preg_replace(
        '#(src|href)="\.\./([^"]+)"#',
        '$1="' . $theme_uri . '/$2"',
        $html_content
    );
    
    // Extract all style tag content – output in page (admin_head already ran before this callback)
    $style_output = '';
    if (preg_match_all('/<style[^>]*>(.*?)<\/style>/is', $html_content, $style_matches, PREG_SET_ORDER)) {
        foreach ($style_matches as $style_match) {
            $style_output .= $style_match[1];
        }
    }

    // Extract body content (everything between <body> and </body>)
    $body_content = $html_content;
    if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $html_content, $matches)) {
        $body_content = $matches[1];
    }

    // Extract and append script content
    if (preg_match_all('/<script[^>]*>(.*?)<\/script>/is', $html_content, $script_matches, PREG_SET_ORDER)) {
        foreach ($script_matches as $script_match) {
            $body_content .= '<script>' . $script_match[1] . '</script>';
        }
    }

    // Wrap in a div and inject styles so they apply (admin_head runs before this page, so we output CSS here)
    echo '<div class="ehs-style-guide-wrap" style="margin: -20px -20px 0 -20px; background: #f5f5f5; min-height: calc(100vh - 32px);">';
    if ($style_output !== '') {
        echo '<style id="ehs-style-guide-css">' . $style_output . '</style>';
    }
    echo $body_content;
    echo '</div>';
}
