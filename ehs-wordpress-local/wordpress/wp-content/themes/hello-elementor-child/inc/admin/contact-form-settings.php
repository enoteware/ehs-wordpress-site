<?php
/**
 * Contact Form Settings Page
 * Admin interface for configuring Resend API and reCAPTCHA
 * 
 * @package HelloElementorChild
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add settings page to admin menu
 */
function ehs_add_contact_form_settings_page() {
    add_options_page(
        'Contact Form Settings',
        'Contact Form',
        'manage_options',
        'ehs-contact-form-settings',
        'ehs_render_contact_form_settings_page'
    );
}
add_action('admin_menu', 'ehs_add_contact_form_settings_page');

/**
 * Render settings page
 */
function ehs_render_contact_form_settings_page() {
    // Save settings
    if (isset($_POST['ehs_contact_form_settings']) && check_admin_referer('ehs_contact_form_settings_nonce')) {
        update_option('ehs_resend_api_key', sanitize_text_field($_POST['ehs_resend_api_key']));
        update_option('ehs_resend_from_email', sanitize_email($_POST['ehs_resend_from_email']));
        update_option('ehs_resend_to_email', sanitize_email($_POST['ehs_resend_to_email']));
        update_option('ehs_resend_bcc_email', sanitize_email($_POST['ehs_resend_bcc_email']));
        update_option('ehs_resend_from_name', sanitize_text_field($_POST['ehs_resend_from_name']));
        update_option('ehs_turnstile_site_key', sanitize_text_field($_POST['ehs_turnstile_site_key']));
        update_option('ehs_turnstile_secret_key', sanitize_text_field($_POST['ehs_turnstile_secret_key']));
        
        echo '<div class="notice notice-success"><p>Settings saved successfully!</p></div>';
    }

    // Get current settings
    $resend_api_key = get_option('ehs_resend_api_key', '');
    $resend_from_email = get_option('ehs_resend_from_email', get_option('admin_email'));
    $resend_to_email = get_option('ehs_resend_to_email', get_option('admin_email'));
    $resend_bcc_email = get_option('ehs_resend_bcc_email', '');
    $resend_from_name = get_option('ehs_resend_from_name', get_bloginfo('name'));
    $turnstile_site_key = get_option('ehs_turnstile_site_key', '');
    $turnstile_secret_key = get_option('ehs_turnstile_secret_key', '');
    ?>
    <div class="wrap">
        <h1>Contact Form Settings</h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('ehs_contact_form_settings_nonce'); ?>
            
            <h2>Resend API Configuration</h2>
            <p>Configure your Resend API settings for email delivery. Get your API key from <a href="https://resend.com/api-keys" target="_blank">resend.com/api-keys</a></p>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="ehs_resend_api_key">Resend API Key</label>
                    </th>
                    <td>
                        <input 
                            type="text" 
                            id="ehs_resend_api_key" 
                            name="ehs_resend_api_key" 
                            value="<?php echo esc_attr($resend_api_key); ?>" 
                            class="regular-text"
                            placeholder="re_xxxxxxxxxxxxx"
                        />
                        <p class="description">Your Resend API key (starts with "re_")</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="ehs_resend_from_email">From Email</label>
                    </th>
                    <td>
                        <input 
                            type="email" 
                            id="ehs_resend_from_email" 
                            name="ehs_resend_from_email" 
                            value="<?php echo esc_attr($resend_from_email); ?>" 
                            class="regular-text"
                        />
                        <p class="description">Email address to send from (must be verified in Resend)</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="ehs_resend_from_name">From Name</label>
                    </th>
                    <td>
                        <input 
                            type="text" 
                            id="ehs_resend_from_name" 
                            name="ehs_resend_from_name" 
                            value="<?php echo esc_attr($resend_from_name); ?>" 
                            class="regular-text"
                        />
                        <p class="description">Display name for sent emails</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="ehs_resend_to_email">To Email</label>
                    </th>
                    <td>
                        <input 
                            type="email" 
                            id="ehs_resend_to_email" 
                            name="ehs_resend_to_email" 
                            value="<?php echo esc_attr($resend_to_email); ?>" 
                            class="regular-text"
                        />
                        <p class="description">Email address to receive contact form submissions</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="ehs_resend_bcc_email">BCC Email</label>
                    </th>
                    <td>
                        <input
                            type="email"
                            id="ehs_resend_bcc_email"
                            name="ehs_resend_bcc_email"
                            value="<?php echo esc_attr($resend_bcc_email); ?>"
                            class="regular-text"
                        />
                        <p class="description">Optional: Send a blind copy to this address</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Test Email</th>
                    <td>
                        <button type="button" id="ehs-test-resend" class="button button-secondary">Send Test Email</button>
                        <span id="ehs-test-resend-status" style="margin-left: 10px;"></span>
                        <p class="description">Send a test email to verify your Resend API configuration (saves settings first)</p>
                    </td>
                </tr>
            </table>

            <h2>Cloudflare Turnstile Configuration (Recommended)</h2>
            <p>Add privacy-friendly bot protection using Cloudflare Turnstile. Get your keys from <a href="https://dash.cloudflare.com/?to=/:account/turnstile" target="_blank">Cloudflare Dashboard</a></p>

            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="ehs_turnstile_site_key">Site Key</label>
                    </th>
                    <td>
                        <input
                            type="text"
                            id="ehs_turnstile_site_key"
                            name="ehs_turnstile_site_key"
                            value="<?php echo esc_attr($turnstile_site_key); ?>"
                            class="regular-text"
                            placeholder="0x4AAAAAAxxxxxxxxxxxxxx"
                        />
                        <p class="description">Turnstile Site Key (public)</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="ehs_turnstile_secret_key">Secret Key</label>
                    </th>
                    <td>
                        <input
                            type="text"
                            id="ehs_turnstile_secret_key"
                            name="ehs_turnstile_secret_key"
                            value="<?php echo esc_attr($turnstile_secret_key); ?>"
                            class="regular-text"
                            placeholder="0x4AAAAAAxxxxxxxxxxxxxx"
                        />
                        <p class="description">Turnstile Secret Key (private)</p>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <input type="submit" name="ehs_contact_form_settings" class="button button-primary" value="Save Settings" />
            </p>
        </form>

        <hr>

        <h2>Usage</h2>
        <h3>Shortcode</h3>
        <p>Add the contact form anywhere using the shortcode:</p>
        <code>[ehs_contact_form]</code>
        
        <h3>PHP Function</h3>
        <p>Use in templates:</p>
        <pre><code>&lt;?php
echo ehs_render_contact_form(array(
    'show_phone' => true,
    'show_company' => false,
    'submit_text' => 'Send Message'
));
?&gt;</code></pre>

        <h3>Modal Window</h3>
        <p>To use in a modal, create a modal structure and include the form:</p>
        <pre><code>&lt;div id="contact-modal" class="ehs-modal-overlay"&gt;
    &lt;div class="ehs-modal"&gt;
        &lt;div class="ehs-modal-header"&gt;
            &lt;h3 class="ehs-modal-title"&gt;Contact Us&lt;/h3&gt;
            &lt;button class="ehs-modal-close" onclick="ehsCloseContactModal('contact-modal')"&gt;&times;&lt;/button&gt;
        &lt;/div&gt;
        &lt;div class="ehs-modal-body"&gt;
            &lt;?php echo ehs_render_contact_form(); ?&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;

&lt;!-- Trigger button --&gt;
&lt;button onclick="ehsOpenContactModal('contact-modal')"&gt;Open Contact Form&lt;/button&gt;</code></pre>
    </div>

    <script>
    jQuery(document).ready(function($) {
        $('#ehs-test-resend').on('click', function() {
            var $btn = $(this);
            var $status = $('#ehs-test-resend-status');

            $btn.prop('disabled', true).text('Sending...');
            $status.html('');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'ehs_test_resend_email',
                    nonce: '<?php echo wp_create_nonce("ehs_test_resend_nonce"); ?>',
                    api_key: $('#ehs_resend_api_key').val(),
                    from_email: $('#ehs_resend_from_email').val(),
                    from_name: $('#ehs_resend_from_name').val(),
                    to_email: $('#ehs_resend_to_email').val()
                },
                success: function(response) {
                    if (response.success) {
                        $status.html('<span style="color: green;">✓ ' + response.data.message + '</span>');
                    } else {
                        $status.html('<span style="color: red;">✗ ' + response.data.message + '</span>');
                    }
                },
                error: function() {
                    $status.html('<span style="color: red;">✗ Request failed</span>');
                },
                complete: function() {
                    $btn.prop('disabled', false).text('Send Test Email');
                }
            });
        });
    });
    </script>
    <?php
}

/**
 * AJAX handler for test email
 */
function ehs_handle_test_resend_email() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Unauthorized'));
        return;
    }

    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'ehs_test_resend_nonce')) {
        wp_send_json_error(array('message' => 'Security check failed'));
        return;
    }

    $api_key = sanitize_text_field($_POST['api_key'] ?? '');
    $from_email = sanitize_email($_POST['from_email'] ?? '');
    $from_name = sanitize_text_field($_POST['from_name'] ?? get_bloginfo('name'));
    $to_email = sanitize_email($_POST['to_email'] ?? '');

    if (empty($api_key)) {
        wp_send_json_error(array('message' => 'API key is required'));
        return;
    }

    if (empty($from_email) || empty($to_email)) {
        wp_send_json_error(array('message' => 'From and To email addresses are required'));
        return;
    }

    // Send test email via Resend API
    $response = wp_remote_post('https://api.resend.com/emails', array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type' => 'application/json',
        ),
        'body' => json_encode(array(
            'from' => $from_name . ' <' . $from_email . '>',
            'to' => array($to_email),
            'subject' => 'EHS Analytical - Test Email',
            'html' => '<h2>Test Email from EHS Analytical Contact Form</h2><p>This is a test email to verify your Resend API configuration is working correctly.</p><p>Sent at: ' . current_time('mysql') . '</p>',
            'text' => "Test Email from EHS Analytical Contact Form\n\nThis is a test email to verify your Resend API configuration is working correctly.\n\nSent at: " . current_time('mysql'),
        )),
        'timeout' => 15,
    ));

    if (is_wp_error($response)) {
        wp_send_json_error(array('message' => 'API Error: ' . $response->get_error_message()));
        return;
    }

    $status_code = wp_remote_retrieve_response_code($response);
    $body = json_decode(wp_remote_retrieve_body($response), true);

    if ($status_code === 200) {
        wp_send_json_success(array('message' => 'Test email sent to ' . $to_email));
    } else {
        $error_msg = isset($body['message']) ? $body['message'] : 'Unknown error (HTTP ' . $status_code . ')';
        wp_send_json_error(array('message' => $error_msg));
    }
}
add_action('wp_ajax_ehs_test_resend_email', 'ehs_handle_test_resend_email');
