<?php
/**
 * Clients Meta Fields Registration
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Clients Meta Fields
 */
function ehs_register_clients_meta_fields() {
    $meta_fields = array(
        // Basic Info
        'client_website' => array('type' => 'string', 'sanitize' => 'esc_url_raw'),
        'client_logo' => array('type' => 'integer', 'sanitize' => 'absint'), // Attachment ID

        // Company Details
        'client_industry' => array('type' => 'string', 'sanitize' => 'sanitize_text_field'),
        'client_location' => array('type' => 'string', 'sanitize' => 'sanitize_text_field'),
        'client_since' => array('type' => 'string', 'sanitize' => 'sanitize_text_field'), // Year became client

        // Contact Info
        'client_contact_name' => array('type' => 'string', 'sanitize' => 'sanitize_text_field'),
        'client_contact_email' => array('type' => 'string', 'sanitize' => 'sanitize_email'),
        'client_contact_phone' => array('type' => 'string', 'sanitize' => 'sanitize_text_field'),

        // Relationship Details
        'client_services_used' => array('type' => 'string', 'sanitize' => 'sanitize_textarea_field'), // Comma-separated service IDs or names
        'client_testimonial' => array('type' => 'string', 'sanitize' => 'sanitize_textarea_field'),

        // Display Settings
        'client_status' => array('type' => 'string', 'sanitize' => 'sanitize_text_field'), // Active, Past, Prospect
        'client_featured' => array('type' => 'boolean', 'sanitize' => 'rest_sanitize_boolean'),
        'client_display_order' => array('type' => 'integer', 'sanitize' => 'absint'),
        'client_show_on_homepage' => array('type' => 'boolean', 'sanitize' => 'rest_sanitize_boolean'),
    );

    foreach ($meta_fields as $field => $config) {
        register_post_meta('clients', $field, array(
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
add_action('init', 'ehs_register_clients_meta_fields');
