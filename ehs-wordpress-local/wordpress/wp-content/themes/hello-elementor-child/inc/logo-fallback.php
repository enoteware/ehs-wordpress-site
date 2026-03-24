<?php
/**
 * Logo fallback: ensure EHS logo SVGs exist in uploads so URLs don't 404.
 * Copies theme logos to wp-content/uploads when missing (e.g. final-logo.svg, final-logo-vertical.svg, ehs_logo_sq.svg).
 *
 * @package HelloElementorChild
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Ensure uploads directory exists and copy theme logo into it if the destination file is missing.
 *
 * @param string $uploads_subpath Path under wp-content/uploads (e.g. '2019/11/final-logo.svg').
 * @param string $theme_filename Filename in theme assets/images/logos/ (e.g. 'final-logo.svg').
 * @return bool True if file exists or was copied, false on failure.
 */
function ehs_ensure_logo_in_uploads($uploads_subpath, $theme_filename) {
    $upload_file = WP_CONTENT_DIR . '/uploads/' . $uploads_subpath;
    if (file_exists($upload_file)) {
        return true;
    }
    $theme_file = get_stylesheet_directory() . '/assets/images/logos/' . $theme_filename;
    if (!file_exists($theme_file)) {
        return false;
    }
    $upload_dir = dirname($upload_file);
    if (!wp_mkdir_p($upload_dir)) {
        return false;
    }
    $copied = @copy($theme_file, $upload_file);
    return $copied;
}

/**
 * Run logo fallback once per request (only copy when missing).
 */
add_action('init', 'ehs_ensure_logos_in_uploads', 5);
function ehs_ensure_logos_in_uploads() {
    ehs_ensure_logo_in_uploads('2019/11/final-logo.svg', 'final-logo.svg');
    ehs_ensure_logo_in_uploads('2019/09/final-logo-vertical.svg', 'final-logo-vertical.svg');
    ehs_ensure_logo_in_uploads('2019/11/ehs_logo_sq.svg', 'ehs_logo_sq.svg');
    ehs_ensure_logo_in_uploads('2019/09/ehs_logo_sq.svg', 'ehs_logo_sq.svg');
    ehs_ensure_logo_in_uploads('ehs_logo_sq.svg', 'ehs_logo_sq.svg');
}
