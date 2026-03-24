<?php
/**
 * WordPress wp_mail() → Resend API integration
 *
 * When Resend API key is configured (Settings → Contact Form), all WordPress
 * emails (password reset, admin notifications, etc.) are sent via Resend
 * instead of PHP mail(), fixing "email could not be sent" on hosts that
 * don't support PHP mail.
 *
 * @package HelloElementorChild
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Short-circuit wp_mail() and send via Resend when API key is set.
 *
 * @param null|bool $return Existing short-circuit return (null = not short-circuited)
 * @param array    $atts   wp_mail arguments: to, subject, message, headers, attachments, embeds
 * @return null|bool Null to let default run; true/false to short-circuit and indicate success/failure
 */
function ehs_pre_wp_mail_resend($return, $atts) {
    $api_key = get_option('ehs_resend_api_key', '');
    if (empty($api_key)) {
        return $return;
    }

    $resend_from_email = get_option('ehs_resend_from_email', get_option('admin_email'));
    $resend_from_name  = get_option('ehs_resend_from_name', get_bloginfo('name'));

    $to      = $atts['to'];
    $subject = $atts['subject'];
    $message = $atts['message'];
    $headers = $atts['headers'];
    $attachments = isset($atts['attachments']) ? $atts['attachments'] : array();

    // Normalize to array
    if (!is_array($to)) {
        $to = array_map('trim', explode(',', $to));
    }
    $to = array_filter($to, 'is_email');
    if (empty($to)) {
        error_log('[EHS wp_mail Resend] No valid recipient');
        return false;
    }

    // Parse headers for From, Reply-To, Content-Type
    $from_email = $resend_from_email;
    $from_name  = $resend_from_name;
    $reply_to   = '';
    $is_html    = false;

    if (!empty($headers)) {
        if (!is_array($headers)) {
            $headers = explode("\n", str_replace("\r\n", "\n", $headers));
        }
        foreach ($headers as $header) {
            if (stripos($header, 'From:') === 0) {
                $value = trim(substr($header, 5));
                if (preg_match('/^(.+)\s+<([^>]+)>$/', $value, $m)) {
                    $from_name  = trim($m[1], ' "');
                    $from_email = trim($m[2]);
                } elseif (is_email($value)) {
                    $from_email = $value;
                }
            } elseif (stripos($header, 'Reply-To:') === 0) {
                $reply_to = trim(substr($header, 9));
            } elseif (stripos($header, 'Content-Type:') === 0) {
                $is_html = (stripos($header, 'text/html') !== false);
            }
        }
    }

    $email_data = array(
        'from'    => $from_name . ' <' . $from_email . '>',
        'to'      => $to,
        'subject' => $subject,
    );

    if ($is_html) {
        $email_data['html'] = $message;
        $email_data['text'] = wp_strip_all_tags($message);
    } else {
        $email_data['text'] = $message;
        $email_data['html'] = '<html><body style="font-family: sans-serif; white-space: pre-wrap;">' . esc_html($message) . '</body></html>';
    }

    if (!empty($reply_to) && is_email(trim($reply_to))) {
        $email_data['reply_to'] = $reply_to;
    }

    // Attachments (Resend: array of { filename, content } with base64 content)
    if (!empty($attachments)) {
        $email_data['attachments'] = ehs_resend_build_attachments($attachments);
    }

    if (!function_exists('ehs_send_resend_email')) {
        error_log('[EHS wp_mail Resend] ehs_send_resend_email not available');
        return $return;
    }

    $sent = ehs_send_resend_email($api_key, $email_data);
    if (!$sent) {
        error_log('[EHS wp_mail Resend] Resend API send failed for: ' . $subject);
    }
    return $sent;
}

/**
 * Build Resend attachments array from file paths.
 *
 * @param array $paths File paths
 * @return array Resend attachments format
 */
function ehs_resend_build_attachments($paths) {
    $out = array();
    foreach ($paths as $path) {
        if (!is_readable($path)) {
            continue;
        }
        $content = file_get_contents($path);
        if ($content === false) {
            continue;
        }
        $out[] = array(
            'filename' => basename($path),
            'content'  => base64_encode($content),
        );
    }
    return $out;
}

add_filter('pre_wp_mail', 'ehs_pre_wp_mail_resend', 10, 2);
