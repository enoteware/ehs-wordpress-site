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
 * Create a signed token used to reject instant and stale form replays.
 *
 * @return string
 */
function ehs_contact_form_create_token() {
    $timestamp = time();
    $signature = hash_hmac('sha256', (string) $timestamp, wp_salt('nonce'));
    return $timestamp . '.' . $signature;
}

/**
 * Require the form to be between two seconds and one hour old.
 *
 * @param string $token Signed form token.
 * @return bool
 */
function ehs_contact_form_verify_token($token) {
    $parts = explode('.', sanitize_text_field($token), 2);
    if (count($parts) !== 2 || !ctype_digit($parts[0])) {
        return false;
    }

    $timestamp = (int) $parts[0];
    $age = time() - $timestamp;
    $expected = hash_hmac('sha256', (string) $timestamp, wp_salt('nonce'));

    return $age >= 2 && $age <= HOUR_IN_SECONDS && hash_equals($expected, $parts[1]);
}

/**
 * AJAX handler for contact form submission
 */
function ehs_handle_contact_form_submission() {
    $content_length = isset($_SERVER['CONTENT_LENGTH']) ? absint($_SERVER['CONTENT_LENGTH']) : 0;
    if ($content_length > 32768) {
        wp_send_json_error(array(
            'message' => 'The request is too large. Please shorten the message and try again.'
        ), 413);
    }

    $origin = isset($_SERVER['HTTP_ORIGIN']) ? esc_url_raw(wp_unslash($_SERVER['HTTP_ORIGIN']), array('https')) : '';
    if ($origin && !in_array(strtolower(untrailingslashit($origin)), array('https://ehsanalytical.com', 'https://www.ehsanalytical.com'), true)) {
        wp_send_json_error(array(
            'message' => 'This form cannot be submitted from that website.'
        ), 403);
    }

    // Verify nonce
    $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
    if (!$nonce || !wp_verify_nonce($nonce, 'ehs_contact_form_nonce')) {
        wp_send_json_error(array(
            'message' => 'Security check failed. Please refresh the page and try again.'
        ), 403);
    }

    // Check honeypot field (bots will fill this)
    if (!empty($_POST['website'])) {
        // Bot detected - silently fail
        wp_send_json_success(array(
            'message' => 'Thank you! Your message has been sent.'
        ));
    }

    $form_token = isset($_POST['form_token']) ? wp_unslash($_POST['form_token']) : '';
    if (!ehs_contact_form_verify_token($form_token)) {
        wp_send_json_error(array(
            'message' => 'The form expired or was submitted too quickly. Please reload the page and try again.'
        ), 400);
    }

    // Verify Cloudflare Turnstile if enabled
    $turnstile_secret = get_option('ehs_turnstile_secret_key', '');
    $turnstile_site_key = get_option('ehs_turnstile_site_key', '');
    $turnstile_token = isset($_POST['turnstile_token']) ? sanitize_text_field(wp_unslash($_POST['turnstile_token'])) : '';
    $turnstile_verified = false;

    if (empty($turnstile_secret) || empty($turnstile_site_key)) {
        error_log('[EHS Contact Form] Turnstile is not configured');
        wp_send_json_error(array(
            'message' => 'The form is temporarily unavailable. Please try again later.'
        ), 503);
    }

    if (empty($turnstile_token)) {
        wp_send_json_error(array(
            'message' => 'Please complete the verification challenge.'
        ), 400);
    }

    $turnstile_valid = ehs_verify_turnstile($turnstile_token, $turnstile_secret);
    if (!$turnstile_valid) {
        wp_send_json_error(array(
            'message' => 'Bot verification failed. Please try again.'
        ), 400);
    }
    $turnstile_verified = true;

    // Sanitize and validate input
    $name_raw = isset($_POST['name']) ? wp_unslash($_POST['name']) : '';
    $email_raw = isset($_POST['email']) ? wp_unslash($_POST['email']) : '';
    $phone_raw = isset($_POST['phone']) ? wp_unslash($_POST['phone']) : '';
    $company_raw = isset($_POST['company']) ? wp_unslash($_POST['company']) : '';
    $subject_raw = isset($_POST['subject']) ? wp_unslash($_POST['subject']) : '';
    $message_raw = isset($_POST['message']) ? wp_unslash($_POST['message']) : '';
    $name = sanitize_text_field($name_raw);
    $email = sanitize_email($email_raw);
    $phone = sanitize_text_field($phone_raw);
    $company = sanitize_text_field($company_raw);
    $subject = sanitize_text_field($subject_raw);
    $message = sanitize_textarea_field($message_raw);

    if (strlen($name_raw) > 100 || strlen($email_raw) > 254 || strlen($phone_raw) > 40 || strlen($company_raw) > 150 || strlen($subject_raw) > 150 || strlen($message_raw) > 5000) {
        wp_send_json_error(array(
            'message' => 'One or more fields are too long. Please shorten them and try again.'
        ), 400);
    }

    // Validate required fields
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        wp_send_json_error(array(
            'message' => 'Please fill in all required fields.'
        ), 400);
    }

    // Validate email format
    if (!is_email($email)) {
        wp_send_json_error(array(
            'message' => 'Please enter a valid email address.'
        ), 400);
    }

    // Rate limiting - prevent spam
    $ip_address = ehs_get_client_ip();
    $rate_limit_key = 'ehs_contact_form_' . substr(hash_hmac('sha256', $ip_address, wp_salt('nonce')), 0, 32);
    $submission_count = get_transient($rate_limit_key);

    if ($submission_count && $submission_count >= 3) {
        wp_send_json_error(array(
            'message' => 'Too many submissions. Please try again later.'
        ), 429);
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
    if ($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($table_name))) === $table_name) {
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

    if (isset($email_data['attachments']) && !empty($email_data['attachments'])) {
        $body['attachments'] = $email_data['attachments'];
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
            'idempotency_key' => wp_generate_uuid4(),
        ),
        'timeout' => 10,
    ));

    if (is_wp_error($response)) {
        error_log('[EHS Contact Form] Turnstile verification error: ' . $response->get_error_message());
        return false;
    }

    if (wp_remote_retrieve_response_code($response) !== 200) {
        error_log('[EHS Contact Form] Turnstile verification returned a non-200 response');
        return false;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);

    if (!is_array($body)) {
        return false;
    }

    $hostname = isset($body['hostname']) ? strtolower(sanitize_text_field($body['hostname'])) : '';
    $action = isset($body['action']) ? sanitize_text_field($body['action']) : '';
    if (!empty($body['success']) && in_array($hostname, array('ehsanalytical.com', 'www.ehsanalytical.com'), true) && $action === 'contact_submit') {
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
    $ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
}
