<?php
/**
 * Media fallback: when an attachment's file (or a requested size) is missing from
 * uploads (e.g. after clone because uploads is gitignored, or thumbnails were never
 * generated), serve the main file or theme placeholder so featured images don't 404.
 *
 * Handles both missing main files and missing thumbnail sizes (-scaled, -150x150, etc.).
 *
 * @package HelloElementorChild
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Placeholder URL for missing attachment files (theme asset).
 *
 * @return string
 */
function ehs_get_attachment_placeholder_url() {
    return get_stylesheet_directory_uri() . '/assets/images/placeholder-blog.svg';
}

/**
 * Check if an attachment's main file exists on disk.
 *
 * @param int $attachment_id Attachment post ID.
 * @return bool True if file exists, false if missing or not an attachment.
 */
function ehs_attachment_file_exists($attachment_id) {
    if (!$attachment_id || get_post_type($attachment_id) !== 'attachment') {
        return false;
    }
    $file = get_attached_file($attachment_id);
    if (!$file) {
        return false;
    }
    return file_exists($file);
}

/**
 * Check if the file for a given attachment URL exists on disk.
 * Used to detect missing thumbnail/size files (e.g. -scaled.jpeg, -150x150.jpeg).
 *
 * @param string $url Full URL to an attachment file (any size).
 * @return bool True if the file exists.
 */
function ehs_attachment_url_file_exists($url) {
    if (empty($url)) {
        return false;
    }
    $upload_dir = wp_upload_dir();
    if (!empty($upload_dir['error'])) {
        return false;
    }
    $baseurl = $upload_dir['baseurl'];
    $basedir = $upload_dir['basedir'];
    if (strpos($url, $baseurl) !== 0) {
        return false;
    }
    $path = $basedir . substr($url, strlen($baseurl));
    return file_exists($path);
}

/**
 * If the attachment's main file is missing, return placeholder URL.
 */
add_filter('wp_get_attachment_url', 'ehs_fallback_attachment_url_to_placeholder', 10, 2);
function ehs_fallback_attachment_url_to_placeholder($url, $attachment_id) {
    if (!$url || !$attachment_id) {
        return $url;
    }
    if (ehs_attachment_file_exists($attachment_id)) {
        return $url;
    }
    return ehs_get_attachment_placeholder_url();
}

/**
 * When building image src for a size: if that size's file is missing (e.g. -scaled,
 * -150x150), use the main file if it exists, otherwise placeholder.
 * Fixes 404s when thumbnails in different sizes were never generated or not synced.
 */
add_filter('wp_get_attachment_image_src', 'ehs_fallback_attachment_image_src_to_placeholder', 10, 4);
function ehs_fallback_attachment_image_src_to_placeholder($image, $attachment_id, $size, $icon) {
    if (!$image || !is_array($image) || empty($image[0]) || !$attachment_id) {
        return $image;
    }
    $url = $image[0];
    if (ehs_attachment_url_file_exists($url)) {
        return $image;
    }
    $main_file = get_attached_file($attachment_id);
    if ($main_file && file_exists($main_file)) {
        $main_url = wp_get_attachment_url($attachment_id);
        if ($main_url && ehs_attachment_url_file_exists($main_url)) {
            $meta = wp_get_attachment_metadata($attachment_id);
            $w = isset($meta['width']) ? (int) $meta['width'] : 0;
            $h = isset($meta['height']) ? (int) $meta['height'] : 0;
            return array(
                $main_url,
                $w,
                $h,
                false,
            );
        }
    }
    $placeholder = ehs_get_attachment_placeholder_url();
    return array(
        $placeholder,
        0,
        0,
        false,
    );
}
