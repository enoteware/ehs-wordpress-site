<?php
/**
 * Contact Form AJAX Handler
 * Processes form submissions, stores entries, and sends emails via Resend API
 *
 * @package HelloElementorChild
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * AJAX handler for contact form submission
 */
function ehs_handle_contact_form_submission() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'ehs_contact_form_nonce')) {
        wp_send_json_error(array(
            'message' => 'Security check failed. Please refresh the page and try again.'
        ));
        return;
    }

    // Check honeypot field (bots will fill this)
    if (!empty($_POST['website'])) {
        // Bot detected - silently fail
        wp_send_json_success(array(
            'message' => 'Thank you! Your message has been sent.'
        ));
        return;
    }

    // Verify Cloudflare Turnstile if enabled
    $turnstile_secret = get_option('ehs_turnstile_secret_key', '');
    $turnstile_token = isset($_POST['turnstile_token']) ? sanitize_text_field($_POST['turnstile_token']) : '';
    $turnstile_verified = false;

    if (!empty($turnstile_secret)) {
        if (empty($turnstile_token)) {
            wp_send_json_error(array(
                'message' => 'Please complete the verification challenge.'
            ));
            return;
        }

        $turnstile_valid = ehs_verify_turnstile($turnstile_token, $turnstile_secret);
        if (!$turnstile_valid) {
            wp_send_json_error(array(
                'message' => 'Bot verification failed. Please try again.'
            ));
            return;
        }
        $turnstile_verified = true;
    }

    // Sanitize and validate input
    $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
    $company = isset($_POST['company']) ? sanitize_text_field($_POST['company']) : '';
    $subject = isset($_POST['subject']) ? sanitize_text_field($_POST['subject']) : '';
    $message = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';

    // Validate required fields
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        wp_send_json_error(array(
            'message' => 'Please fill in all required fields.'
        ));
        return;
    }

    // Validate email format
    if (!is_email($email)) {
        wp_send_json_error(array(
            'message' => 'Please enter a valid email address.'
        ));
        return;
    }

    // Rate limiting - prevent spam
    $ip_address = ehs_get_client_ip();
    $rate_limit_key = 'ehs_contact_form_' . md5($ip_address);
    $submission_count = get_transient($rate_limit_key);

    if ($submission_count && $submission_count >= 3) {
        wp_send_json_error(array(
            'message' => 'Too many submissions. Please try again later.'
        ));
        return;
    }

    // Increment rate limit counter
    set_transient($rate_limit_key, ($submission_count ? $submission_count + 1 : 1), 3600); // 1 hour

    // Store form entry in database
    $entry_id = ehs_store_contact_form_entry(array(
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'company' => $company,
        'subject' => $subject,
        'message' => $message,
        'ip_address' => $ip_address,
        'turnstile_verified' => $turnstile_verified ? 1 : 0,
    ));

    // Get Resend API key and settings
    $resend_api_key = get_option('ehs_resend_api_key', '');
    $resend_from_email = get_option('ehs_resend_from_email', get_option('admin_email'));
    $resend_to_email = get_option('ehs_resend_to_email', get_option('admin_email'));
    $resend_bcc_email = get_option('ehs_resend_bcc_email', '');
    $resend_from_name = get_option('ehs_resend_from_name', get_bloginfo('name'));

    if (empty($resend_api_key)) {
        // Log error but don't expose to user
        error_log('[EHS Contact Form] Resend API key not configured');
        wp_send_json_error(array(
            'message' => 'Email service is not configured. Please contact the site administrator.'
        ));
        return;
    }

    // Build email content
    $email_body = "New contact form submission from " . get_bloginfo('name') . "\n\n";
    $email_body .= "Name: " . $name . "\n";
    $email_body .= "Email: " . $email . "\n";
    if (!empty($phone)) {
        $email_body .= "Phone: " . $phone . "\n";
    }
    if (!empty($company)) {
        $email_body .= "Company: " . $company . "\n";
    }
    $email_body .= "Subject: " . $subject . "\n\n";
    $email_body .= "Message:\n" . $message . "\n\n";
    $email_body .= "---\n";
    $email_body .= "Submitted: " . current_time('mysql') . "\n";
    $email_body .= "IP Address: " . $ip_address . "\n";
    $email_body .= "Entry ID: " . ($entry_id ? $entry_id : 'N/A') . "\n";
    $email_body .= "Turnstile Verified: " . ($turnstile_verified ? 'Yes' : 'No') . "\n";

    // HTML email version
    $email_html = '<html><body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">';
    $email_html .= '<h2 style="color: #003366;">New Contact Form Submission</h2>';
    $email_html .= '<table style="width: 100%; max-width: 600px; border-collapse: collapse;">';
    $email_html .= '<tr><td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Name:</strong></td><td style="padding: 8px; border-bottom: 1px solid #eee;">' . esc_html($name) . '</td></tr>';
    $email_html .= '<tr><td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Email:</strong></td><td style="padding: 8px; border-bottom: 1px solid #eee;"><a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a></td></tr>';
    if (!empty($phone)) {
        $email_html .= '<tr><td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Phone:</strong></td><td style="padding: 8px; border-bottom: 1px solid #eee;">' . esc_html($phone) . '</td></tr>';
    }
    if (!empty($company)) {
        $email_html .= '<tr><td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Company:</strong></td><td style="padding: 8px; border-bottom: 1px solid #eee;">' . esc_html($company) . '</td></tr>';
    }
    $email_html .= '<tr><td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Subject:</strong></td><td style="padding: 8px; border-bottom: 1px solid #eee;">' . esc_html($subject) . '</td></tr>';
    $email_html .= '<tr><td colspan="2" style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Message:</strong><br>' . nl2br(esc_html($message)) . '</td></tr>';
    $email_html .= '</table>';
    $email_html .= '<p style="margin-top: 20px; font-size: 12px; color: #666;">Submitted: ' . current_time('mysql') . '<br>IP Address: ' . esc_html($ip_address) . '<br>Entry ID: ' . ($entry_id ? $entry_id : 'N/A') . '<br>Turnstile Verified: ' . ($turnstile_verified ? 'Yes' : 'No') . '</p>';
    $email_html .= '</body></html>';

    // Send email via Resend API
    $email_data = array(
        'from' => $resend_from_name . ' <' . $resend_from_email . '>',
        'to' => $resend_to_email,
        'reply_to' => $name . ' <' . $email . '>',
        'subject' => 'Contact Form: ' . $subject,
        'text' => $email_body,
        'html' => $email_html,
    );

    if (!empty($resend_bcc_email)) {
        $email_data['bcc'] = $resend_bcc_email;
    }

    $email_sent = ehs_send_resend_email($resend_api_key, $email_data);

    if ($email_sent) {
        wp_send_json_success(array(
            'message' => 'Thank you for contacting EHS Analytical! We have received your message and will respond within 1 business day.'
        ));
    } else {
        error_log('[EHS Contact Form] Failed to send email via Resend API');
        wp_send_json_error(array(
            'message' => 'Sorry, there was an error sending your message. Please try again later or contact us directly.'
        ));
    }
}
add_action('wp_ajax_ehs_submit_contact_form', 'ehs_handle_contact_form_submission');
add_action('wp_ajax_nopriv_ehs_submit_contact_form', 'ehs_handle_contact_form_submission');

/**
 * Store contact form entry in database
 *
 * @param array $data Form data
 * @return int|false Entry ID on success, false on failure
 */
function ehs_store_contact_form_entry($data) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ehs_contact_form_entries';

    // Ensure table exists
    ehs_create_contact_form_entries_table();

    $result = $wpdb->insert(
        $table_name,
        array(
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'company' => $data['company'],
            'subject' => $data['subject'],
            'message' => $data['message'],
            'ip_address' => $data['ip_address'],
            'turnstile_verified' => $data['turnstile_verified'],
            'created_at' => current_time('mysql'),
        ),
        array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s')
    );

    if ($result === false) {
        error_log('[EHS Contact Form] Failed to store entry: ' . $wpdb->last_error);
        return false;
    }

    return $wpdb->insert_id;
}

/**
 * Create contact form entries table
 */
function ehs_create_contact_form_entries_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'ehs_contact_form_entries';
    $charset_collate = $wpdb->get_charset_collate();

    // Check if table already exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name) {
        return;
    }

    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL DEFAULT '',
        email VARCHAR(255) NOT NULL DEFAULT '',
        phone VARCHAR(50) NOT NULL DEFAULT '',
        company VARCHAR(255) NOT NULL DEFAULT '',
        subject VARCHAR(255) NOT NULL DEFAULT '',
        message TEXT NOT NULL,
        ip_address VARCHAR(45) NOT NULL DEFAULT '',
        turnstile_verified TINYINT(1) NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY email (email),
        KEY created_at (created_at)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

/**
 * Send email via Resend API
 *
 * @param string $api_key Resend API key
 * @param array $email_data Email data
 * @return bool Success status
 */
function ehs_send_resend_email($api_key, $email_data) {
    $url = 'https://api.resend.com/emails';

    $body = array(
        'from' => $email_data['from'],
        'to' => is_array($email_data['to']) ? $email_data['to'] : array($email_data['to']),
        'subject' => $email_data['subject'],
        'html' => $email_data['html'],
    );

    if (isset($email_data['text'])) {
        $body['text'] = $email_data['text'];
    }

    if (isset($email_data['reply_to'])) {
        $body['reply_to'] = $email_data['reply_to'];
    }

    if (isset($email_data['bcc']) && !empty($email_data['bcc'])) {
        $body['bcc'] = is_array($email_data['bcc']) ? $email_data['bcc'] : array($email_data['bcc']);
    }

    $response = wp_remote_post($url, array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type' => 'application/json',
        ),
        'body' => json_encode($body),
        'timeout' => 15,
    ));

    if (is_wp_error($response)) {
        error_log('[EHS Contact Form] Resend API Error: ' . $response->get_error_message());
        return false;
    }

    $status_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);

    if ($status_code === 200) {
        return true;
    } else {
        error_log('[EHS Contact Form] Resend API Error: HTTP ' . $status_code . ' - ' . $response_body);
        return false;
    }
}

/**
 * Verify Cloudflare Turnstile token
 *
 * @param string $token Turnstile token
 * @param string $secret Secret key
 * @return bool Is valid
 */
function ehs_verify_turnstile($token, $secret) {
    $url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    $response = wp_remote_post($url, array(
        'body' => array(
            'secret' => $secret,
            'response' => $token,
            'remoteip' => ehs_get_client_ip(),
        ),
        'timeout' => 10,
    ));

    if (is_wp_error($response)) {
        error_log('[EHS Contact Form] Turnstile verification error: ' . $response->get_error_message());
        return false;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);

    if (isset($body['success']) && $body['success'] === true) {
        return true;
    }

    // Log error codes if verification failed
    if (isset($body['error-codes']) && !empty($body['error-codes'])) {
        error_log('[EHS Contact Form] Turnstile error codes: ' . implode(', ', $body['error-codes']));
    }

    return false;
}

/**
 * Get client IP address
 *
 * @return string IP address
 */
function ehs_get_client_ip() {
    $ip_keys = array(
        'HTTP_CF_CONNECTING_IP', // Cloudflare
        'HTTP_X_REAL_IP',        // Nginx proxy
        'HTTP_X_FORWARDED_FOR',  // Proxy
        'REMOTE_ADDR',           // Standard
    );

    foreach ($ip_keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = $_SERVER[$key];
            // Handle comma-separated IPs (X-Forwarded-For)
            if (strpos($ip, ',') !== false) {
                $ip = trim(explode(',', $ip)[0]);
            }
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }

    return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
}
