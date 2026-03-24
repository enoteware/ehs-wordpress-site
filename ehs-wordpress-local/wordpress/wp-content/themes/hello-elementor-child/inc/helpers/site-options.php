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

        // Homepage SEO (used for title, meta description, and link-share preview when Yoast doesn’t expose editable fields)
        'homepage_seo_title'       => 'Environmental Health & Safety Solutions | California & Federal Projects | Since 2004',
        'homepage_seo_description' => 'CIH and CSP certified EHS consulting for manufacturing, data centers, biotech, aerospace, and construction. California and nationwide. SDVOSB/DVBE for federal and state government projects.',
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
 * Canonical footer address: single source for "Our address" in all footers.
 * Format: Company name, line1, line2 (if set), city state zip.
 * Use ehs_render_footer_address() in templates so copy stays consistent.
 *
 * @return array Associative array: company_name, line1, line2 (may be empty), city_state.
 */
function ehs_get_footer_address_data() {
    return [
        'company_name' => ehs_get_option('company_name'),
        'line1'        => ehs_get_option('address_line1'),
        'line2'        => ehs_get_option('address_line2'),
        'city_state'   => ehs_get_city_state_zip(),
    ];
}

/**
 * Render the canonical footer address block (same output in footer.php and dynamic-footer.php).
 *
 * @param bool $with_icon Whether to output the location pin icon wrapper. Default true.
 */
function ehs_render_footer_address( $with_icon = true ) {
    $addr = ehs_get_footer_address_data();
    if ( $with_icon ) {
        echo '<div class="ehs-footer-address-wrapper">';
        echo '<span class="ehs-footer-contact-icon">';
        echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>';
        echo '</span>';
    }
    echo '<p class="ehs-footer-address">';
    echo '<strong>' . esc_html( $addr['company_name'] ) . '</strong><br />';
    echo esc_html( $addr['line1'] ) . '<br />';
    if ( ! empty( $addr['line2'] ) ) {
        echo esc_html( $addr['line2'] ) . '<br />';
    }
    echo esc_html( $addr['city_state'] );
    echo '</p>';
    if ( $with_icon ) {
        echo '</div>';
    }
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
 * Canonical display order for credential badges (footer, About page).
 * Order per client spec: CIH, CSP, CAC, CHST, PMP, SMS, CIT, CUSP, DVBE, SDVOSB, IOSH, USOLN.
 *
 * @return array Ordered array of credential acronyms
 */
function ehs_get_canonical_credential_order() {
    return array( 'CIH', 'CSP', 'CAC', 'CHST', 'PMP', 'SMS', 'CIT', 'CUSP', 'DVBE', 'SDVOSB', 'IOSH', 'USOLN' );
}

/**
 * Get all credentials as card data for display (e.g. footer).
 * Ordered by canonical credential order (ehs_get_canonical_credential_order); credentials not in list appear last.
 *
 * @return array Array of credential data with title, description, image, link, acronym
 */
function ehs_get_all_credential_cards() {
    $credentials = get_posts([
        'post_type'      => 'credentials',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'title',
        'order'          => 'ASC',
    ]);

    $canonical_order = ehs_get_canonical_credential_order();
    $order_default   = 999999;

    $cards = [];
    foreach ($credentials as $credential) {
        if (!is_a($credential, 'WP_Post')) {
            continue;
        }
        $title   = get_the_title($credential);
        $acronym = get_post_meta($credential->ID, 'credential_acronym', true);
        $acronym = trim((string) $acronym);
        if ($acronym === '' && (stripos($title, 'Utility Safety') !== false || stripos($title, 'USOLN') !== false)) {
            $acronym = 'USOLN';
        }
        $cards[] = [
            'title'       => $title,
            'description' => get_the_excerpt($credential) ?: wp_trim_words(get_the_content(null, false, $credential), 30),
            'image'       => get_the_post_thumbnail_url($credential, 'medium'),
            'link'        => get_permalink($credential),
            'acronym'     => $acronym,
        ];
    }

    usort($cards, function ($a, $b) use ($canonical_order, $order_default) {
        $pos_a = array_search($a['acronym'], $canonical_order, true);
        $pos_b = array_search($b['acronym'], $canonical_order, true);
        $idx_a = $pos_a !== false ? $pos_a : $order_default;
        $idx_b = $pos_b !== false ? $pos_b : $order_default;
        if ($idx_a !== $idx_b) {
            return $idx_a - $idx_b;
        }
        return strcasecmp($a['title'], $b['title']);
    });

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
 * Get homepage media image URL from Media Library (ACF option).
 *
 * @param string $key Option name without 'ehs_home_' prefix (e.g. 'ssho_image', 'usace_logo')
 * @param string $size Image size (default 'medium_large')
 * @return string URL or empty string
 */
function ehs_get_homepage_media_url($key, $size = 'medium_large') {
    if (!function_exists('get_field')) {
        return '';
    }
    $id = get_field('ehs_home_' . $key, 'option');
    if (!$id || !is_numeric($id)) {
        return '';
    }
    $url = wp_get_attachment_image_url((int) $id, $size);
    return $url ? $url : '';
}

/**
 * Output homepage media image HTML (img or placeholder div if not set).
 *
 * @param string $key Option name without 'ehs_home_' prefix
 * @param string $alt Alt text for image
 * @param string $size Image size (default 'medium_large')
 * @param array  $attr Optional extra attributes for img
 * @return void Outputs HTML
 */
function ehs_homepage_media_image($key, $alt = '', $size = 'medium_large', $attr = []) {
    if (!function_exists('get_field')) {
        echo '<div class="service-card__icon service-card__icon--placeholder" aria-hidden="true"><span class="placeholder-text">Select image in Business Information → Homepage Media</span></div>';
        return;
    }
    $id = get_field('ehs_home_' . $key, 'option');
    if ($id && is_numeric($id)) {
        $defaults = array('alt' => $alt, 'loading' => 'lazy');
        echo wp_get_attachment_image((int) $id, $size, false, array_merge($defaults, $attr));
    } else {
        echo '<div class="service-card__icon service-card__icon--placeholder" aria-hidden="true"><span class="placeholder-text">Select image in Business Information → Homepage Media</span></div>';
    }
}

/**
 * Output homepage media image URL for use in img src (e.g. with inline styles).
 * Returns empty string if not set; template can hide or show placeholder.
 *
 * @param string $key Option name without 'ehs_home_' prefix
 * @param string $size Image size (default 'medium_large')
 * @return string URL or empty string
 */
function ehs_homepage_media_src($key, $size = 'medium_large') {
    return ehs_get_homepage_media_url($key, $size);
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

/**
 * Get permalink for a page by slug (avoids hardcoded URLs; survives slug changes).
 *
 * @param string $slug Page slug (e.g. 'contact', 'insights', 'thank-you').
 * @return string URL for the page, or home_url fallback if page not found.
 */
function ehs_get_page_url($slug) {
    $page = get_page_by_path($slug);
    return $page ? get_permalink($page) : home_url('/' . $slug . '/');
}

/**
 * Get permalink for a service post by slug (post_type 'services').
 *
 * @param string $slug Service post slug (e.g. 'industrial-hygiene-san-diego').
 * @return string URL for the service, or home_url fallback if not found.
 */
function ehs_get_service_url($slug) {
    $posts = get_posts([
        'name'           => $slug,
        'post_type'      => 'services',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
    ]);
    return $posts ? get_permalink($posts[0]) : home_url('/' . $slug . '/');
}
