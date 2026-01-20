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

/**
 * Project Timeline/Portfolio Shortcode
 *
 * Usage: [project_timeline title="Projects We've Supported"]
 *   [project year="2010" title="Deepwater Horizon" client="BP" value="$65 billion" service="Industrial hygiene"]Description here[/project]
 *   [project year="2017" title="Phillips 66 Refinery" client="Phillips 66" value="$400 million" service="Safety turnaround"]Description[/project]
 * [/project_timeline]
 *
 * Or inline: [project_timeline projects='[{"year":"2010","title":"Project","description":"Desc","client":"Client","value":"$1M","service":"Safety"}]']
 */
function ehs_project_timeline_shortcode($atts, $content = '') {
    $atts = shortcode_atts(array(
        'title' => '',
        'projects' => '',
    ), $atts, 'project_timeline');

    $projects = array();

    // Check for JSON projects attribute
    if (!empty($atts['projects'])) {
        $projects = json_decode($atts['projects'], true);
        if (!is_array($projects)) {
            $projects = array();
        }
    }

    // Parse nested [project] shortcodes from content
    if (!empty($content) && empty($projects)) {
        // Use global to collect projects from nested shortcodes
        global $ehs_project_timeline_items;
        $ehs_project_timeline_items = array();

        // Process nested shortcodes
        do_shortcode($content);

        $projects = $ehs_project_timeline_items;
        $ehs_project_timeline_items = null;
    }

    if (empty($projects)) {
        return '';
    }

    return ehs_render_project_timeline($atts['title'], $projects);
}
add_shortcode('project_timeline', 'ehs_project_timeline_shortcode');

/**
 * Individual project item shortcode (used within project_timeline)
 */
function ehs_project_shortcode($atts, $content = '') {
    global $ehs_project_timeline_items;

    $atts = shortcode_atts(array(
        'year' => '',
        'title' => '',
        'client' => '',
        'value' => '',
        'service' => '',
        'image' => '',
    ), $atts, 'project');

    if (!is_array($ehs_project_timeline_items)) {
        return '';
    }

    $ehs_project_timeline_items[] = array(
        'year' => $atts['year'],
        'title' => $atts['title'],
        'description' => trim($content),
        'client' => $atts['client'],
        'value' => $atts['value'],
        'service' => $atts['service'],
        'image' => $atts['image'],
    );

    return '';
}
add_shortcode('project', 'ehs_project_shortcode');
