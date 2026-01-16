<?php
/**
 * Credentials Meta Fields Registration
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Credentials Meta Fields
 */
function ehs_register_credentials_meta_fields() {
    $meta_fields = array(
        'credential_acronym' => array('type' => 'string', 'sanitize' => 'sanitize_text_field'),
        'credential_issuing_organization' => array('type' => 'string', 'sanitize' => 'sanitize_text_field'),
        'credential_date_obtained' => array('type' => 'string', 'sanitize' => 'sanitize_text_field'),
        'credential_category' => array('type' => 'string', 'sanitize' => 'sanitize_text_field'),
        'credential_type' => array('type' => 'string', 'sanitize' => 'sanitize_text_field'),
        'credential_order' => array('type' => 'integer', 'sanitize' => 'absint'),
        'credential_featured' => array('type' => 'boolean', 'sanitize' => 'rest_sanitize_boolean'),
    );

    foreach ($meta_fields as $field => $config) {
        register_post_meta('credentials', $field, array(
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
add_action('init', 'ehs_register_credentials_meta_fields');
