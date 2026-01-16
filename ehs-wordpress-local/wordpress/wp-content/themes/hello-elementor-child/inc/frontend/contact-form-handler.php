<?php
/**
 * Contact Form AJAX Handler
 * Processes form submissions and sends emails via Resend API
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

    // Verify reCAPTCHA v3 if enabled
    $recaptcha_secret = get_option('ehs_recaptcha_secret_key', '');
    $recaptcha_token = isset($_POST['recaptcha_token']) ? sanitize_text_field($_POST['recaptcha_token']) : '';

    if (!empty($recaptcha_secret) && !empty($recaptcha_token)) {
        $recaptcha_valid = ehs_verify_recaptcha($recaptcha_token, $recaptcha_secret);
        if (!$recaptcha_valid) {
            wp_send_json_error(array(
                'message' => 'Bot verification failed. Please try again.'
            ));
            return;
        }
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

    // Get Resend API key and settings
    $resend_api_key = get_option('ehs_resend_api_key', '');
    $resend_from_email = get_option('ehs_resend_from_email', get_option('admin_email'));
    $resend_to_email = get_option('ehs_resend_to_email', get_option('admin_email'));
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
    $email_html .= '<p style="margin-top: 20px; font-size: 12px; color: #666;">Submitted: ' . current_time('mysql') . '<br>IP Address: ' . esc_html($ip_address) . '</p>';
    $email_html .= '</body></html>';

    // Send email via Resend API
    $email_sent = ehs_send_resend_email(
        $resend_api_key,
        array(
            'from' => $resend_from_name . ' <' . $resend_from_email . '>',
            'to' => $resend_to_email,
            'reply_to' => $name . ' <' . $email . '>',
            'subject' => 'Contact Form: ' . $subject,
            'text' => $email_body,
            'html' => $email_html,
        )
    );

    if ($email_sent) {
        wp_send_json_success(array(
            'message' => 'Thank you! Your message has been sent. We\'ll get back to you soon.'
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
 * Verify reCAPTCHA v3 token
 * 
 * @param string $token reCAPTCHA token
 * @param string $secret Secret key
 * @return bool Is valid
 */
function ehs_verify_recaptcha($token, $secret) {
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    
    $response = wp_remote_post($url, array(
        'body' => array(
            'secret' => $secret,
            'response' => $token,
            'remoteip' => ehs_get_client_ip(),
        ),
        'timeout' => 10,
    ));

    if (is_wp_error($response)) {
        error_log('[EHS Contact Form] reCAPTCHA verification error: ' . $response->get_error_message());
        return false;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);

    if (isset($body['success']) && $body['success'] === true) {
        // Check score (v3 returns 0.0 to 1.0, typically > 0.5 is human)
        $score = isset($body['score']) ? floatval($body['score']) : 0;
        return $score >= 0.5;
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
