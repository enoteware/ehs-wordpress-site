<?php
/**
 * Services Meta Fields Registration
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Services Meta Fields
 */
function ehs_register_services_meta_fields() {
    $meta_fields = array(
        'service_short_description' => array('type' => 'string', 'sanitize' => 'sanitize_textarea_field'),
        'service_icon'          => array('type' => 'integer', 'sanitize' => 'absint'),
        'service_related_services' => array('type' => 'string', 'sanitize' => 'sanitize_text_field'),
        'service_featured'     => array('type' => 'boolean', 'sanitize' => 'rest_sanitize_boolean'),
        'service_order'        => array('type' => 'integer', 'sanitize' => 'absint'),
    );

    foreach ($meta_fields as $field => $config) {
        register_post_meta('services', $field, array(
            'type'              => $config['type'],
            'single'            => true,
            'show_in_rest'      => true,
            'sanitize_callback' => $config['sanitize'],
            'auth_callback'     => function() {
                return current_user_can('edit_posts');
            },
        ));
    }
}
add_action('init', 'ehs_register_services_meta_fields');
