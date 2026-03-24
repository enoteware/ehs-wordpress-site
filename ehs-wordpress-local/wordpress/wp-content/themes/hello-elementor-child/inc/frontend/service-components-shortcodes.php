<?php
/**
 * Service Components Shortcodes
 *
 * Shortcode handlers for service page components
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Service Video Shortcode
 *
 * Usage: [service_video url="https://youtube.com/watch?v=..." caption="Optional caption"]
 *
 * @param array $atts Shortcode attributes
 * @return string HTML markup
 */
function ehs_service_video_shortcode($atts) {
    $atts = shortcode_atts(array(
        'url' => '',
        'caption' => '',
        'thumbnail' => '',
    ), $atts, 'service_video');

    if (empty($atts['url'])) {
        return '';
    }

    $component = array(
        'type' => 'video',
        'video_url' => $atts['url'],
        'video_caption' => $atts['caption'],
        'video_thumbnail' => !empty($atts['thumbnail']) ? absint($atts['thumbnail']) : 0,
    );

    return ehs_render_service_video($component);
}
add_shortcode('service_video', 'ehs_service_video_shortcode');

/**
 * Service Checklist Shortcode
 *
 * Usage: [service_checklist title="Our Services" items="Item 1|Item 2|Item 3"]
 * Or: [service_checklist title="Our Services"]Item 1|Item 2|Item 3[/service_checklist]
 *
 * @param array $atts Shortcode attributes
 * @param string $content Shortcode content (optional)
 * @return string HTML markup
 */
function ehs_service_checklist_shortcode($atts, $content = '') {
    $atts = shortcode_atts(array(
        'title' => '',
        'items' => '',
    ), $atts, 'service_checklist');

    // Get items from content if not in attributes
    $items_string = !empty($content) ? $content : $atts['items'];
    
    if (empty($items_string)) {
        return '';
    }

    // Split by pipe or newline
    $items = preg_split('/[\|\\n]+/', $items_string);
    $items = array_filter(array_map('trim', $items));

    if (empty($items)) {
        return '';
    }

    $component = array(
        'type' => 'checklist',
        'checklist_title' => $atts['title'],
        'checklist_items' => $items,
    );

    return ehs_render_service_checklist($component);
}
add_shortcode('service_checklist', 'ehs_service_checklist_shortcode');

/**
 * Service Timeline Shortcode
 *
 * Usage: [service_timeline title="Our Process" steps='[{"step":"Step 1","description":"Desc 1"},{"step":"Step 2","description":"Desc 2"}]']
 *
 * @param array $atts Shortcode attributes
 * @return string HTML markup
 */
function ehs_service_timeline_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title' => '',
        'steps' => '',
    ), $atts, 'service_timeline');

    if (empty($atts['steps'])) {
        return '';
    }

    // Decode JSON steps
    $steps = json_decode($atts['steps'], true);
    if (!is_array($steps)) {
        return '';
    }

    $component = array(
        'type' => 'timeline',
        'timeline_title' => $atts['title'],
        'timeline_items' => $steps,
    );

    return ehs_render_service_timeline($component);
}
add_shortcode('service_timeline', 'ehs_service_timeline_shortcode');
