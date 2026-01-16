<?php
/**
 * Resend API Form Action for Elementor Pro Forms
 * 
 * Sends form submissions to Resend API with bot protection
 * 
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Resend API Form Action
 */
add_action('elementor_pro/forms/actions/register', function($form_actions_registrar) {
    require_once(__DIR__ . '/class-resend-api-action.php');
    $form_actions_registrar->register(new \EHS_Resend_API_Action());
});
