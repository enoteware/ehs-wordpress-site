<?php
/**
 * ACF Business Information Page
 *
 * Registers the Business Information admin page and all field groups
 * for centralized business information management.
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register ACF Options Page
 */
function ehs_register_options_page() {
    if (!function_exists('acf_add_options_page')) {
        return;
    }

    acf_add_options_page([
        'page_title'    => 'Business Information',
        'menu_title'    => 'Business Information',
        'menu_slug'     => 'ehs-site-options',
        'capability'    => 'edit_posts',
        'redirect'      => false,
        'icon_url'      => 'dashicons-admin-settings',
        'position'      => 80,
        'update_button' => 'Save Information',
        'updated_message' => 'Business information saved.',
    ]);
}
add_action('acf/init', 'ehs_register_options_page');

/**
 * Register ACF Field Groups for Site Options
 */
function ehs_register_site_options_fields() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    // Company Information
    acf_add_local_field_group([
        'key' => 'group_ehs_company_info',
        'title' => 'Company Information',
        'fields' => [
            [
                'key' => 'field_ehs_company_name',
                'label' => 'Company Name',
                'name' => 'ehs_company_name',
                'type' => 'text',
                'default_value' => 'EHS Analytical Solutions, Inc.',
                'placeholder' => 'Enter company name',
            ],
            [
                'key' => 'field_ehs_tagline',
                'label' => 'Tagline',
                'name' => 'ehs_tagline',
                'type' => 'text',
                'placeholder' => 'Short company tagline',
            ],
            [
                'key' => 'field_ehs_about_description',
                'label' => 'About Description',
                'name' => 'ehs_about_description',
                'type' => 'textarea',
                'rows' => 4,
                'instructions' => 'Brief company description for use across the site.',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'ehs-site-options',
                ],
            ],
        ],
        'menu_order' => 0,
    ]);

    // Contact Information
    acf_add_local_field_group([
        'key' => 'group_ehs_contact',
        'title' => 'Contact Information',
        'fields' => [
            [
                'key' => 'field_ehs_phone',
                'label' => 'Phone Number',
                'name' => 'ehs_phone',
                'type' => 'text',
                'default_value' => '(619) 288-3094',
                'placeholder' => '(xxx) xxx-xxxx',
            ],
            [
                'key' => 'field_ehs_email_primary',
                'label' => 'Primary Email',
                'name' => 'ehs_email_primary',
                'type' => 'email',
                'default_value' => 'info@ehsanalytical.com',
                'instructions' => 'Main contact email (shown on contact page)',
            ],
            [
                'key' => 'field_ehs_email_secondary',
                'label' => 'Secondary Email',
                'name' => 'ehs_email_secondary',
                'type' => 'email',
                'default_value' => 'adam@ehsanalytical.com',
                'instructions' => 'Alternative contact email (shown in footer)',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'ehs-site-options',
                ],
            ],
        ],
        'menu_order' => 1,
    ]);

    // Address
    acf_add_local_field_group([
        'key' => 'group_ehs_address',
        'title' => 'Business Address',
        'fields' => [
            [
                'key' => 'field_ehs_address_line1',
                'label' => 'Street Address',
                'name' => 'ehs_address_line1',
                'type' => 'text',
                'default_value' => '6755 Mira Mesa Blvd',
            ],
            [
                'key' => 'field_ehs_address_line2',
                'label' => 'Suite/Unit',
                'name' => 'ehs_address_line2',
                'type' => 'text',
                'default_value' => 'Suite 123-249',
            ],
            [
                'key' => 'field_ehs_address_city',
                'label' => 'City',
                'name' => 'ehs_address_city',
                'type' => 'text',
                'default_value' => 'San Diego',
                'wrapper' => ['width' => '40'],
            ],
            [
                'key' => 'field_ehs_address_state',
                'label' => 'State',
                'name' => 'ehs_address_state',
                'type' => 'text',
                'default_value' => 'CA',
                'wrapper' => ['width' => '20'],
            ],
            [
                'key' => 'field_ehs_address_zip',
                'label' => 'ZIP Code',
                'name' => 'ehs_address_zip',
                'type' => 'text',
                'default_value' => '92121',
                'wrapper' => ['width' => '40'],
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'ehs-site-options',
                ],
            ],
        ],
        'menu_order' => 2,
    ]);

    // Business Hours
    acf_add_local_field_group([
        'key' => 'group_ehs_hours',
        'title' => 'Business Hours',
        'fields' => [
            [
                'key' => 'field_ehs_days_text',
                'label' => 'Days Open',
                'name' => 'ehs_days_text',
                'type' => 'text',
                'default_value' => 'Monday - Friday',
                'placeholder' => 'e.g., Monday - Friday',
                'wrapper' => ['width' => '50'],
            ],
            [
                'key' => 'field_ehs_hours_text',
                'label' => 'Hours',
                'name' => 'ehs_hours_text',
                'type' => 'text',
                'default_value' => '8:00 AM - 5:00 PM PST',
                'placeholder' => 'e.g., 8:00 AM - 5:00 PM PST',
                'wrapper' => ['width' => '50'],
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'ehs-site-options',
                ],
            ],
        ],
        'menu_order' => 3,
    ]);

    // Social Media
    acf_add_local_field_group([
        'key' => 'group_ehs_social',
        'title' => 'Social Media Links',
        'fields' => [
            [
                'key' => 'field_ehs_social_facebook',
                'label' => 'Facebook URL',
                'name' => 'ehs_social_facebook',
                'type' => 'url',
                'placeholder' => 'https://facebook.com/yourpage',
            ],
            [
                'key' => 'field_ehs_social_instagram',
                'label' => 'Instagram URL',
                'name' => 'ehs_social_instagram',
                'type' => 'url',
                'placeholder' => 'https://instagram.com/yourhandle',
            ],
            [
                'key' => 'field_ehs_social_twitter',
                'label' => 'Twitter/X URL',
                'name' => 'ehs_social_twitter',
                'type' => 'url',
                'placeholder' => 'https://twitter.com/yourhandle',
            ],
            [
                'key' => 'field_ehs_social_linkedin',
                'label' => 'LinkedIn URL',
                'name' => 'ehs_social_linkedin',
                'type' => 'url',
                'placeholder' => 'https://linkedin.com/company/yourcompany',
            ],
            [
                'key' => 'field_ehs_social_youtube',
                'label' => 'YouTube URL',
                'name' => 'ehs_social_youtube',
                'type' => 'url',
                'placeholder' => 'https://youtube.com/@yourchannel',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'ehs-site-options',
                ],
            ],
        ],
        'menu_order' => 4,
    ]);

    // Service Area
    acf_add_local_field_group([
        'key' => 'group_ehs_service_area',
        'title' => 'Service Area',
        'fields' => [
            [
                'key' => 'field_ehs_service_area',
                'label' => 'Service Area Description',
                'name' => 'ehs_service_area',
                'type' => 'textarea',
                'rows' => 3,
                'default_value' => 'Serving California and nationwide',
                'instructions' => 'Geographic coverage description (e.g., "Serving California and nationwide")',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'ehs-site-options',
                ],
            ],
        ],
        'menu_order' => 5,
    ]);

    // Featured Credentials
    acf_add_local_field_group([
        'key' => 'group_ehs_featured_credentials',
        'title' => 'Featured Credentials',
        'fields' => [
            [
                'key' => 'field_ehs_featured_credentials',
                'label' => 'Select Credentials to Feature',
                'name' => 'ehs_featured_credentials',
                'type' => 'relationship',
                'post_type' => ['credentials'],
                'filters' => ['search'],
                'elements' => ['featured_image'],
                'min' => 0,
                'max' => 6,
                'return_format' => 'object',
                'instructions' => 'Select credentials to display in the footer. Drag to reorder.',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'ehs-site-options',
                ],
            ],
        ],
        'menu_order' => 6,
    ]);
}
add_action('acf/init', 'ehs_register_site_options_fields');

/**
 * Register Dashboard Widget for Business Information
 */
function ehs_add_site_options_dashboard_widget() {
    wp_add_dashboard_widget(
        'ehs_site_options_widget',
        'Business Information',
        'ehs_site_options_widget_content'
    );
}
add_action('wp_dashboard_setup', 'ehs_add_site_options_dashboard_widget');

/**
 * Dashboard Widget Content
 */
function ehs_site_options_widget_content() {
    // Get current values
    $phone = ehs_get_option('phone');
    $email_primary = ehs_get_option('email_primary');
    $email_secondary = ehs_get_option('email_secondary');
    $address = ehs_get_address('oneline');
    $hours = ehs_get_hours();
    $social_links = ehs_get_social_links();
    ?>
    <style>
        .ehs-dashboard-widget { margin: -12px; }
        .ehs-dashboard-widget table { width: 100%; border-collapse: collapse; }
        .ehs-dashboard-widget th { text-align: left; padding: 10px 12px; background: #f6f7f7; border-bottom: 1px solid #dcdcde; font-weight: 600; width: 120px; }
        .ehs-dashboard-widget td { padding: 10px 12px; border-bottom: 1px solid #dcdcde; }
        .ehs-dashboard-widget tr:last-child th,
        .ehs-dashboard-widget tr:last-child td { border-bottom: none; }
        .ehs-dashboard-widget .social-icons { display: flex; gap: 8px; flex-wrap: wrap; }
        .ehs-dashboard-widget .social-icon { display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: #2271b1; color: #fff; border-radius: 4px; text-decoration: none; }
        .ehs-dashboard-widget .social-icon:hover { background: #135e96; }
        .ehs-dashboard-widget .social-icon svg { width: 16px; height: 16px; }
        .ehs-dashboard-widget .edit-link { display: inline-block; margin-top: 15px; padding: 8px 16px; background: #2271b1; color: #fff; text-decoration: none; border-radius: 4px; font-weight: 500; }
        .ehs-dashboard-widget .edit-link:hover { background: #135e96; color: #fff; }
        .ehs-dashboard-widget .no-value { color: #999; font-style: italic; }
    </style>
    <div class="ehs-dashboard-widget">
        <table>
            <tr>
                <th>Phone</th>
                <td><a href="tel:<?php echo esc_attr(preg_replace('/[^0-9]/', '', $phone)); ?>"><?php echo esc_html($phone); ?></a></td>
            </tr>
            <tr>
                <th>Primary Email</th>
                <td><a href="mailto:<?php echo esc_attr($email_primary); ?>"><?php echo esc_html($email_primary); ?></a></td>
            </tr>
            <tr>
                <th>Secondary Email</th>
                <td><a href="mailto:<?php echo esc_attr($email_secondary); ?>"><?php echo esc_html($email_secondary); ?></a></td>
            </tr>
            <tr>
                <th>Address</th>
                <td><?php echo esc_html($address); ?></td>
            </tr>
            <tr>
                <th>Hours</th>
                <td><?php echo esc_html($hours); ?></td>
            </tr>
            <tr>
                <th>Social</th>
                <td>
                    <?php if (!empty($social_links)) : ?>
                        <div class="social-icons">
                            <?php if (!empty($social_links['facebook'])) : ?>
                                <a href="<?php echo esc_url($social_links['facebook']); ?>" target="_blank" class="social-icon" title="Facebook">
                                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($social_links['instagram'])) : ?>
                                <a href="<?php echo esc_url($social_links['instagram']); ?>" target="_blank" class="social-icon" title="Instagram">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1.5" fill="currentColor"/></svg>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($social_links['linkedin'])) : ?>
                                <a href="<?php echo esc_url($social_links['linkedin']); ?>" target="_blank" class="social-icon" title="LinkedIn">
                                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($social_links['twitter'])) : ?>
                                <a href="<?php echo esc_url($social_links['twitter']); ?>" target="_blank" class="social-icon" title="Twitter/X">
                                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M4 4l11.733 16h4.267l-11.733-16zM4 20l6.768-6.768m2.46-2.46l6.772-6.772"/></svg>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($social_links['youtube'])) : ?>
                                <a href="<?php echo esc_url($social_links['youtube']); ?>" target="_blank" class="social-icon" title="YouTube">
                                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48" fill="#fff"/></svg>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php else : ?>
                        <span class="no-value">No social links configured</span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
        <div style="padding: 12px; text-align: center; border-top: 1px solid #dcdcde; margin-top: 0;">
            <a href="<?php echo esc_url(admin_url('admin.php?page=ehs-site-options')); ?>" class="edit-link">
                Edit Business Information
            </a>
        </div>
    </div>
    <?php
}
