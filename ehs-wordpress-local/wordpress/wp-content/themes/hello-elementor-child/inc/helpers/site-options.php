<?php
/**
 * Site Options Helper Functions
 *
 * Provides easy access to ACF site options throughout templates.
 * Falls back to default values if options not yet configured.
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Default site options (fallbacks if ACF options not set)
 */
function ehs_get_default_options() {
    return [
        // Company Info
        'company_name'      => 'EHS Analytical Solutions, Inc.',
        'tagline'           => 'Environmental Health & Safety Consulting',
        'about_description' => '',

        // Contact
        'phone'             => '(619) 288-3094',
        'email_primary'     => 'info@ehsanalytical.com',
        'email_secondary'   => 'adam@ehsanalytical.com',

        // Address
        'address_line1'     => '6755 Mira Mesa Blvd',
        'address_line2'     => 'Suite 123-249',
        'address_city'      => 'San Diego',
        'address_state'     => 'CA',
        'address_zip'       => '92121',

        // Business Hours
        'hours_text'        => '8:00 AM - 5:00 PM PST',
        'days_text'         => 'Monday - Friday',

        // Social Media
        'social_facebook'   => '',
        'social_instagram'  => '',
        'social_twitter'    => '',
        'social_linkedin'   => '',
        'social_youtube'    => '',

        // Service Area
        'service_area'      => 'Serving California and nationwide',
    ];
}

/**
 * Get a single site option value
 *
 * @param string $key Option key (e.g., 'phone', 'company_name')
 * @param mixed $default Optional override default
 * @return mixed Option value or default
 */
function ehs_get_option($key, $default = null) {
    // Try ACF option first
    if (function_exists('get_field')) {
        $value = get_field('ehs_' . $key, 'option');
        if ($value !== null && $value !== '' && $value !== false) {
            return $value;
        }
    }

    // Fall back to defaults
    $defaults = ehs_get_default_options();
    if ($default !== null) {
        return $default;
    }

    return isset($defaults[$key]) ? $defaults[$key] : '';
}

/**
 * Get phone number with optional formatting
 *
 * @param bool $link_format If true, returns tel: link format
 * @return string Phone number
 */
function ehs_get_phone($link_format = false) {
    $phone = ehs_get_option('phone');

    if ($link_format) {
        // Strip non-numeric for tel: link
        return preg_replace('/[^0-9]/', '', $phone);
    }

    return $phone;
}

/**
 * Get formatted address HTML
 *
 * @param string $format 'full', 'oneline', or 'schema' for structured data
 * @return string Formatted address
 */
function ehs_get_address($format = 'full') {
    $line1 = ehs_get_option('address_line1');
    $line2 = ehs_get_option('address_line2');
    $city  = ehs_get_option('address_city');
    $state = ehs_get_option('address_state');
    $zip   = ehs_get_option('address_zip');

    if ($format === 'oneline') {
        $parts = array_filter([$line1, $line2, "$city, $state $zip"]);
        return implode(', ', $parts);
    }

    if ($format === 'schema') {
        return sprintf(
            '<address itemscope itemtype="https://schema.org/PostalAddress">
                <span itemprop="streetAddress">%s</span>
                <span itemprop="streetAddress">%s</span>
                <span itemprop="addressLocality">%s</span>,
                <span itemprop="addressRegion">%s</span>
                <span itemprop="postalCode">%s</span>
            </address>',
            esc_html($line1),
            esc_html($line2),
            esc_html($city),
            esc_html($state),
            esc_html($zip)
        );
    }

    // Full format (default)
    $html = '<span class="ehs-address">';
    $html .= '<span class="address-line1">' . esc_html($line1) . '</span><br>';
    if ($line2) {
        $html .= '<span class="address-line2">' . esc_html($line2) . '</span><br>';
    }
    $html .= '<span class="address-city-state">' . esc_html($city) . ', ' . esc_html($state) . ' ' . esc_html($zip) . '</span>';
    $html .= '</span>';

    return $html;
}

/**
 * Get city, state, zip formatted
 *
 * @return string City, State ZIP
 */
function ehs_get_city_state_zip() {
    return sprintf(
        '%s, %s %s',
        ehs_get_option('address_city'),
        ehs_get_option('address_state'),
        ehs_get_option('address_zip')
    );
}

/**
 * Get social media links as array
 *
 * @param bool $only_populated If true, only returns links that have URLs
 * @return array Social media links
 */
function ehs_get_social_links($only_populated = true) {
    $links = [
        'facebook'  => ehs_get_option('social_facebook'),
        'instagram' => ehs_get_option('social_instagram'),
        'twitter'   => ehs_get_option('social_twitter'),
        'linkedin'  => ehs_get_option('social_linkedin'),
        'youtube'   => ehs_get_option('social_youtube'),
    ];

    if ($only_populated) {
        return array_filter($links, function($url) {
            return !empty($url) && $url !== '#';
        });
    }

    return $links;
}

/**
 * Get business hours formatted
 *
 * @param string $format 'full' (Mon-Fri 8-5), 'hours' (8-5), 'days' (Mon-Fri)
 * @return string Formatted hours
 */
function ehs_get_hours($format = 'full') {
    $hours = ehs_get_option('hours_text');
    $days  = ehs_get_option('days_text');

    if ($format === 'hours') {
        return $hours;
    }

    if ($format === 'days') {
        return $days;
    }

    return $days . ' ' . $hours;
}

/**
 * Get featured credentials from ACF relationship field
 *
 * @return array Array of credential post objects
 */
function ehs_get_featured_credentials_from_acf() {
    if (!function_exists('get_field')) {
        return [];
    }

    $credentials = get_field('ehs_featured_credentials', 'option');

    if (!$credentials || !is_array($credentials)) {
        return [];
    }

    return $credentials;
}

/**
 * Get formatted credential card data for display
 *
 * @return array Array of credential data with title, description, image, link
 */
function ehs_get_credential_cards() {
    $credentials = ehs_get_featured_credentials_from_acf();
    $cards = [];

    foreach ($credentials as $credential) {
        if (!is_a($credential, 'WP_Post')) {
            continue;
        }

        $cards[] = [
            'title'       => get_the_title($credential),
            'description' => get_the_excerpt($credential) ?: wp_trim_words(get_the_content(null, false, $credential), 30),
            'image'       => get_the_post_thumbnail_url($credential, 'medium'),
            'link'        => get_permalink($credential),
        ];
    }

    return $cards;
}

/**
 * Output phone link HTML
 *
 * @param array $args Optional args: class, icon, text
 */
function ehs_phone_link($args = []) {
    $defaults = [
        'class' => 'ehs-phone-link',
        'icon'  => true,
        'text'  => null, // null uses phone number as text
    ];
    $args = wp_parse_args($args, $defaults);

    $phone = ehs_get_option('phone');
    $phone_link = ehs_get_phone(true);
    $text = $args['text'] !== null ? $args['text'] : $phone;

    $icon_html = $args['icon'] ? '<span class="phone-icon" aria-hidden="true"></span>' : '';

    printf(
        '<a href="tel:%s" class="%s">%s%s</a>',
        esc_attr($phone_link),
        esc_attr($args['class']),
        $icon_html,
        esc_html($text)
    );
}

/**
 * Output email link HTML
 *
 * @param string $which 'primary' or 'secondary'
 * @param array $args Optional args: class, icon, subject
 */
function ehs_email_link($which = 'primary', $args = []) {
    $defaults = [
        'class'   => 'ehs-email-link',
        'icon'    => true,
        'subject' => '',
    ];
    $args = wp_parse_args($args, $defaults);

    $email_key = $which === 'secondary' ? 'email_secondary' : 'email_primary';
    $email = ehs_get_option($email_key);

    $href = 'mailto:' . $email;
    if ($args['subject']) {
        $href .= '?subject=' . rawurlencode($args['subject']);
    }

    $icon_html = $args['icon'] ? '<span class="email-icon" aria-hidden="true"></span>' : '';

    printf(
        '<a href="%s" class="%s">%s%s</a>',
        esc_attr($href),
        esc_attr($args['class']),
        $icon_html,
        esc_html($email)
    );
}
