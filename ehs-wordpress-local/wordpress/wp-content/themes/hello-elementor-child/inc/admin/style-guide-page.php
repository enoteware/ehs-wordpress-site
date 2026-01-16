<?php
/**
 * Style Guide Admin Page
 * 
 * Displays the EHS Design System Style Guide in WordPress admin
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
        'edit_posts', // Capability - any user who can edit posts
        'ehs-style-guide',
        'ehs_render_style_guide_page',
        'dashicons-art', // Icon
        30 // Position
    );
}

/**
 * Render the Style Guide page
 */
function ehs_render_style_guide_page() {
    $style_guide_path = get_stylesheet_directory() . '/style-guide.html';
    
    if (!file_exists($style_guide_path)) {
        echo '<div class="wrap"><h1>Style Guide</h1><p>Style guide file not found at: ' . esc_html($style_guide_path) . '</p></div>';
        return;
    }
    
    // Get the HTML content
    $html_content = file_get_contents($style_guide_path);
    
    // Replace all asset paths with WordPress URLs
    
    // 1. Replace uploads paths (logos and media)
    // Pattern: wordpress/wp-content/wordpress/wp-content/uploads/... → wp-content/uploads/...
    $html_content = preg_replace(
        '#wordpress/wp-content/wordpress/wp-content/uploads/([^"\']+)#',
        content_url('uploads/$1'),
        $html_content
    );
    
    // Also handle direct wp-content/uploads paths
    $html_content = preg_replace(
        '#wp-content/uploads/([^"\']+)#',
        content_url('uploads/$1'),
        $html_content
    );
    
    // 2. Replace theme asset paths
    // Pattern: assets/service-icons/... → theme/assets/service-icons/...
    $theme_assets_url = get_stylesheet_directory_uri() . '/assets/';
    $html_content = str_replace(
        'assets/service-icons/',
        $theme_assets_url . 'service-icons/',
        $html_content
    );
    
    // 3. Replace square logo path (check multiple possible locations)
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
            
            // Check theme directory as last resort
            if (!$square_logo_url) {
                $square_logo_theme = get_stylesheet_directory() . '/ehs_logo_sq.svg';
                if (file_exists($square_logo_theme)) {
                    $square_logo_url = get_stylesheet_directory_uri() . '/ehs_logo_sq.svg';
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
    
    // 4. Replace any relative paths that might reference theme files
    $html_content = preg_replace(
        '#(src|href)="\.\./([^"]+)"#',
        '$1="' . get_stylesheet_directory_uri() . '/$2"',
        $html_content
    );
    
    // Extract style tag content and add to admin head
    if (preg_match('/<style[^>]*>(.*?)<\/style>/is', $html_content, $style_matches)) {
        $style_content = $style_matches[1];
        add_action('admin_head', function() use ($style_content) {
            echo '<style>' . $style_content . '</style>';
        }, 999);
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
    
    // Wrap in a div to override admin styles and provide full-width display
    echo '<div style="margin: -20px -20px 0 -20px; background: #f5f5f5; min-height: calc(100vh - 32px);">';
    echo $body_content;
    echo '</div>';
}
