<?php
/**
 * Service Components Meta Fields Registration
 *
 * Registers meta field for storing service page components (videos, checklists, timelines)
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Service Components Meta Field
 */
function ehs_register_services_components_meta_fields() {
    // Register service_components meta field
    register_post_meta('services', 'service_components', array(
        'type'              => 'string',
        'single'            => true,
        'show_in_rest'      => true,
        'sanitize_callback' => 'ehs_sanitize_service_components',
        'auth_callback'     => function() {
            return current_user_can('edit_posts');
        },
    ));
}
add_action('init', 'ehs_register_services_components_meta_fields');

/**
 * Sanitize service components JSON data
 *
 * @param string $value Raw JSON string
 * @return string Sanitized JSON string
 */
function ehs_sanitize_service_components($value) {
    if (empty($value)) {
        return '';
    }

    // Decode JSON to validate structure
    $decoded = json_decode($value, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        // Invalid JSON, return empty
        return '';
    }

    if (!is_array($decoded)) {
        return '';
    }

    // Sanitize each component
    $sanitized = array();
    foreach ($decoded as $component) {
        if (!isset($component['type']) || !is_string($component['type'])) {
            continue;
        }

        $type = sanitize_text_field($component['type']);
        $sanitized_component = array('type' => $type);

        switch ($type) {
            case 'video':
                if (isset($component['video_url'])) {
                    $sanitized_component['video_url'] = esc_url_raw($component['video_url']);
                }
                if (isset($component['video_caption'])) {
                    $sanitized_component['video_caption'] = sanitize_textarea_field($component['video_caption']);
                }
                if (isset($component['video_thumbnail'])) {
                    $sanitized_component['video_thumbnail'] = absint($component['video_thumbnail']);
                }
                break;

            case 'checklist':
                if (isset($component['checklist_title'])) {
                    $sanitized_component['checklist_title'] = sanitize_text_field($component['checklist_title']);
                }
                if (isset($component['checklist_items']) && is_array($component['checklist_items'])) {
                    $sanitized_component['checklist_items'] = array_map('sanitize_text_field', $component['checklist_items']);
                }
                break;

            case 'timeline':
                if (isset($component['timeline_title'])) {
                    $sanitized_component['timeline_title'] = sanitize_text_field($component['timeline_title']);
                }
                if (isset($component['timeline_items']) && is_array($component['timeline_items'])) {
                    $sanitized_items = array();
                    foreach ($component['timeline_items'] as $item) {
                        if (is_array($item)) {
                            $sanitized_items[] = array(
                                'step' => isset($item['step']) ? sanitize_text_field($item['step']) : '',
                                'description' => isset($item['description']) ? sanitize_textarea_field($item['description']) : '',
                            );
                        }
                    }
                    $sanitized_component['timeline_items'] = $sanitized_items;
                }
                break;
        }

        $sanitized[] = $sanitized_component;
    }

    // Return as JSON string
    return wp_json_encode($sanitized);
}
