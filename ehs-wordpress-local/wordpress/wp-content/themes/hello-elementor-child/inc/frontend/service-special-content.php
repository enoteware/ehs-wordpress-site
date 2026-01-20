<?php
/**
 * Service Special Content Rendering
 *
 * Functions to render accordions and YouTube videos on service pages
 *
 * @package HelloElementorChild
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Display service accordions on single service page
 *
 * @param int $post_id The service post ID
 */
function ehs_render_service_accordions($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $accordions = get_field('service_accordions', $post_id);

    if (empty($accordions)) {
        return;
    }

    echo '<div class="service-accordions-container">';

    foreach ($accordions as $index => $accordion) {
        $accordion_id = 'accordion-' . $post_id . '-' . $index;
        $title = isset($accordion['accordion_title']) ? sanitize_text_field($accordion['accordion_title']) : '';
        $items = isset($accordion['accordion_items']) ? $accordion['accordion_items'] : '';

        if (empty($title)) {
            continue;
        }

        echo '<div class="service-accordion">';
        echo '<button class="accordion-header" aria-expanded="false" aria-controls="' . esc_attr($accordion_id) . '">';
        echo '<span class="accordion-title">' . esc_html($title) . '</span>';
        echo '<span class="accordion-icon" aria-hidden="true">+</span>';
        echo '</button>';

        echo '<div id="' . esc_attr($accordion_id) . '" class="accordion-content" role="region" aria-labelledby="accordion-header-' . esc_attr($accordion_id) . '">';
        echo '<div class="accordion-inner">';

        // Parse items - support both newline-separated and pipe-separated formats
        $item_list = array_filter(array_map('trim', preg_split('/[\r\n|]+/', $items)));

        if (!empty($item_list)) {
            echo '<ul class="accordion-items">';
            foreach ($item_list as $item) {
                if (!empty($item)) {
                    echo '<li>' . esc_html($item) . '</li>';
                }
            }
            echo '</ul>';
        } else {
            // Fallback: if no items parsed, display as paragraph
            echo '<p>' . esc_html($items) . '</p>';
        }

        echo '</div>';
        echo '</div>';
        echo '</div>';
    }

    echo '</div>';
}

/**
 * Display service YouTube video on single service page
 *
 * @param int $post_id The service post ID
 */
function ehs_render_service_youtube_video($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $video_url = get_field('service_youtube_video', $post_id);

    if (empty($video_url)) {
        return;
    }

    // Extract YouTube video ID from various URL formats
    $video_id = ehs_extract_youtube_video_id($video_url);

    if (empty($video_id)) {
        return;
    }

    $embed_url = 'https://www.youtube.com/embed/' . esc_attr($video_id) . '?modestbranding=1&rel=0';

    echo '<div class="service-video-container">';
    echo '<div class="service-video-wrapper">';
    echo '<iframe
        class="service-video"
        width="560"
        height="315"
        src="' . esc_url($embed_url) . '"
        title="Service Video"
        frameborder="0"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
        allowfullscreen
        loading="lazy">
    </iframe>';
    echo '</div>';
    echo '</div>';
}

/**
 * Extract YouTube video ID from various URL formats
 *
 * Supports:
 * - https://www.youtube.com/watch?v=dQw4w9WgXcQ
 * - https://youtu.be/dQw4w9WgXcQ
 * - https://www.youtube.com/embed/dQw4w9WgXcQ
 *
 * @param string $url The YouTube URL
 * @return string The video ID or empty string if not found
 */
function ehs_extract_youtube_video_id($url) {
    if (empty($url)) {
        return '';
    }

    // Remove whitespace
    $url = trim($url);

    // Pattern 1: youtube.com/watch?v=ID
    if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/', $url, $matches)) {
        return $matches[1];
    }

    // Pattern 2: youtu.be/ID
    if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]{11})/', $url, $matches)) {
        return $matches[1];
    }

    // Pattern 3: youtube.com/embed/ID
    if (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/', $url, $matches)) {
        return $matches[1];
    }

    // Pattern 4: Just the ID (11 characters)
    if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $url)) {
        return $url;
    }

    return '';
}

/**
 * Shortcode: [service_accordions]
 * Displays service accordions
 */
function ehs_service_accordions_shortcode($atts) {
    ob_start();
    ehs_render_service_accordions();
    return ob_get_clean();
}
add_shortcode('service_accordions', 'ehs_service_accordions_shortcode');
