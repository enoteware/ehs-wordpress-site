<?php
/**
 * Admin Columns for Credentials Post Type
 * Adds custom columns with thumbnail previews, color-coded badges, and sortable fields
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add custom columns to Credentials admin list
 */
add_filter('manage_credentials_posts_columns', 'ehs_credentials_custom_columns');
function ehs_credentials_custom_columns($columns) {
    $new_columns = array();

    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;

        // Insert custom columns after title
        if ($key === 'title') {
            $new_columns['featured_image'] = __('Thumbnail', 'hello-elementor-child');
            $new_columns['credential_acronym'] = __('Acronym', 'hello-elementor-child');
            $new_columns['credential_issuing_organization'] = __('Issuing Organization', 'hello-elementor-child');
            $new_columns['credential_category'] = __('Category', 'hello-elementor-child');
            $new_columns['credential_date_obtained'] = __('Date Obtained', 'hello-elementor-child');
            $new_columns['credential_featured'] = __('Featured', 'hello-elementor-child');
            $new_columns['credential_order'] = __('Order', 'hello-elementor-child');
        }
    }

    // Remove comments column (handled by disable-comments)
    unset($new_columns['comments']);

    return $new_columns;
}

/**
 * Populate custom column content
 */
add_action('manage_credentials_posts_custom_column', 'ehs_credentials_column_content', 10, 2);
function ehs_credentials_column_content($column_name, $post_id) {
    switch ($column_name) {
        case 'featured_image':
            $thumb_id = get_post_thumbnail_id($post_id);
            if ($thumb_id) {
                echo wp_get_attachment_image($thumb_id, array(40, 40), false, array(
                    'style' => 'border-radius: 3px; border: 1px solid #ddd; object-fit: cover;'
                ));
            } else {
                echo '<span style="color: #999;">—</span>';
            }
            break;

        case 'credential_acronym':
            $acronym = get_post_meta($post_id, 'credential_acronym', true);
            if ($acronym) {
                echo '<span style="background: #FFB81C; color: #003366; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 700; display: inline-block;">' . esc_html($acronym) . '</span>';
            } else {
                echo '<span style="color: #999;">—</span>';
            }
            break;

        case 'credential_issuing_organization':
            $org = get_post_meta($post_id, 'credential_issuing_organization', true);
            if ($org) {
                echo esc_html($org);
            } else {
                echo '<span style="color: #999;">—</span>';
            }
            break;

        case 'credential_category':
            $category = get_post_meta($post_id, 'credential_category', true);
            if ($category) {
                $badge_colors = array(
                    'Professional Certification' => '#2271b1',
                    'Business Designation' => '#d63638',
                    'License' => '#00a32a',
                    'Affiliation' => '#826eb4',
                );
                $color = isset($badge_colors[$category]) ? $badge_colors[$category] : '#999';
                echo '<span style="background: ' . esc_attr($color) . '; color: white; padding: 2px 8px; border-radius: 3px; font-size: 11px; font-weight: 600;">' . esc_html($category) . '</span>';
            } else {
                echo '<span style="color: #999;">—</span>';
            }
            break;

        case 'credential_date_obtained':
            $date = get_post_meta($post_id, 'credential_date_obtained', true);
            if ($date) {
                echo esc_html(date_i18n(get_option('date_format'), strtotime($date)));
            } else {
                echo '<span style="color: #999;">—</span>';
            }
            break;

        case 'credential_featured':
            $featured = get_post_meta($post_id, 'credential_featured', true);
            if ($featured) {
                echo '<span class="dashicons dashicons-star-filled" style="color: #f0ad4e;" title="Featured"></span>';
            } else {
                echo '<span style="color: #ddd;">—</span>';
            }
            break;

        case 'credential_order':
            $order = get_post_meta($post_id, 'credential_order', true);
            echo $order ? esc_html($order) : '<span style="color: #999;">0</span>';
            break;
    }
}

/**
 * Make custom columns sortable
 */
add_filter('manage_edit-credentials_sortable_columns', 'ehs_credentials_sortable_columns');
function ehs_credentials_sortable_columns($columns) {
    $columns['credential_featured'] = 'credential_featured';
    $columns['credential_order'] = 'credential_order';
    $columns['credential_date_obtained'] = 'credential_date_obtained';
    return $columns;
}

/**
 * Handle sorting for custom columns
 */
add_action('pre_get_posts', 'ehs_credentials_column_orderby');
function ehs_credentials_column_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    // Only for Credentials post type
    if ($query->get('post_type') !== 'credentials') {
        return;
    }

    $orderby = $query->get('orderby');
    $meta_fields = array('credential_featured', 'credential_order', 'credential_date_obtained');

    if (in_array($orderby, $meta_fields)) {
        $query->set('meta_key', $orderby);

        // Use numeric ordering for order and featured
        if (in_array($orderby, array('credential_order', 'credential_featured'))) {
            $query->set('orderby', 'meta_value_num');
        } else {
            $query->set('orderby', 'meta_value');
        }
    }
}

/**
 * Add inline styles for better column display
 */
add_action('admin_head-edit.php', 'ehs_credentials_admin_column_styles');
function ehs_credentials_admin_column_styles() {
    global $post_type;

    if ('credentials' !== $post_type) {
        return;
    }
    ?>
    <style>
        .column-featured_image { width: 70px; text-align: center; }
        .column-credential_acronym { width: 100px; }
        .column-credential_issuing_organization { width: 200px; }
        .column-credential_category { width: 180px; }
        .column-credential_date_obtained { width: 120px; }
        .column-credential_featured { width: 80px; text-align: center; }
        .column-credential_order { width: 60px; text-align: center; }
    </style>
    <?php
}
