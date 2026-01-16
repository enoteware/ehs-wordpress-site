<?php
/**
 * Admin Columns for Clients Post Type
 * Adds custom columns with logo previews, color-coded badges, and sortable fields
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add custom columns to Clients admin list
 */
add_filter('manage_clients_posts_columns', 'ehs_clients_custom_columns');
function ehs_clients_custom_columns($columns) {
    $new_columns = array();

    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;

        // Insert custom columns after title
        if ($key === 'title') {
            $new_columns['client_logo_col'] = __('Logo', 'hello-elementor-child');
            $new_columns['client_industry'] = __('Industry', 'hello-elementor-child');
            $new_columns['client_location'] = __('Location', 'hello-elementor-child');
            $new_columns['client_website'] = __('Website', 'hello-elementor-child');
            $new_columns['client_status'] = __('Status', 'hello-elementor-child');
            $new_columns['client_featured'] = __('Featured', 'hello-elementor-child');
            $new_columns['client_display_order'] = __('Order', 'hello-elementor-child');
        }
    }

    // Remove comments column
    unset($new_columns['comments']);

    return $new_columns;
}

/**
 * Populate custom column content
 */
add_action('manage_clients_posts_custom_column', 'ehs_clients_column_content', 10, 2);
function ehs_clients_column_content($column_name, $post_id) {
    switch ($column_name) {
        case 'client_logo_col':
            $logo_id = get_post_meta($post_id, 'client_logo', true);
            if ($logo_id) {
                echo wp_get_attachment_image($logo_id, array(50, 50), false, array(
                    'style' => 'border-radius: 4px; border: 1px solid #ddd; object-fit: contain; background: #fff; padding: 2px;'
                ));
            } elseif (has_post_thumbnail($post_id)) {
                echo get_the_post_thumbnail($post_id, array(50, 50), array(
                    'style' => 'border-radius: 4px; border: 1px solid #ddd; object-fit: contain; background: #fff; padding: 2px;'
                ));
            } else {
                echo '<span style="color: #999;">—</span>';
            }
            break;

        case 'client_industry':
            $industry = get_post_meta($post_id, 'client_industry', true);
            if ($industry) {
                $badge_colors = array(
                    'Biotechnology' => '#0073aa',
                    'Pharmaceutical' => '#8e44ad',
                    'Healthcare' => '#27ae60',
                    'Manufacturing' => '#e67e22',
                    'Construction' => '#d35400',
                    'Education' => '#3498db',
                    'Government' => '#34495e',
                    'Energy' => '#f39c12',
                    'Technology' => '#1abc9c',
                    'Real Estate' => '#95a5a6',
                    'Food & Beverage' => '#e74c3c',
                    'Agriculture' => '#2ecc71',
                    'Other' => '#7f8c8d',
                );
                $color = isset($badge_colors[$industry]) ? $badge_colors[$industry] : '#999';
                echo '<span style="background: ' . esc_attr($color) . '; color: white; padding: 2px 8px; border-radius: 3px; font-size: 11px; font-weight: 600;">' . esc_html($industry) . '</span>';
            } else {
                echo '<span style="color: #999;">—</span>';
            }
            break;

        case 'client_location':
            $location = get_post_meta($post_id, 'client_location', true);
            if ($location) {
                echo '<span style="color: #555;">' . esc_html($location) . '</span>';
            } else {
                echo '<span style="color: #999;">—</span>';
            }
            break;

        case 'client_website':
            $website = get_post_meta($post_id, 'client_website', true);
            if ($website) {
                $display_url = preg_replace('#^https?://(www\.)?#', '', $website);
                $display_url = rtrim($display_url, '/');
                echo '<a href="' . esc_url($website) . '" target="_blank" style="color: #0073aa; text-decoration: none;" title="' . esc_attr($website) . '">' . esc_html($display_url) . ' <span class="dashicons dashicons-external" style="font-size: 14px; vertical-align: middle;"></span></a>';
            } else {
                echo '<span style="color: #999;">—</span>';
            }
            break;

        case 'client_status':
            $status = get_post_meta($post_id, 'client_status', true);
            if ($status) {
                $status_colors = array(
                    'active' => '#00a32a',
                    'past' => '#d63638',
                    'prospect' => '#2271b1',
                );
                $status_labels = array(
                    'active' => 'Active',
                    'past' => 'Past',
                    'prospect' => 'Prospect',
                );
                $color = isset($status_colors[$status]) ? $status_colors[$status] : '#999';
                $label = isset($status_labels[$status]) ? $status_labels[$status] : ucfirst($status);
                echo '<span style="background: ' . esc_attr($color) . '; color: white; padding: 2px 8px; border-radius: 3px; font-size: 11px; font-weight: 600;">' . esc_html($label) . '</span>';
            } else {
                echo '<span style="color: #999;">—</span>';
            }
            break;

        case 'client_featured':
            $featured = get_post_meta($post_id, 'client_featured', true);
            if ($featured) {
                echo '<span class="dashicons dashicons-star-filled" style="color: #f0ad4e;" title="Featured"></span>';
            } else {
                echo '<span style="color: #ddd;">—</span>';
            }
            break;

        case 'client_display_order':
            $order = get_post_meta($post_id, 'client_display_order', true);
            echo $order ? esc_html($order) : '<span style="color: #999;">0</span>';
            break;
    }
}

/**
 * Make custom columns sortable
 */
add_filter('manage_edit-clients_sortable_columns', 'ehs_clients_sortable_columns');
function ehs_clients_sortable_columns($columns) {
    $columns['client_industry'] = 'client_industry';
    $columns['client_status'] = 'client_status';
    $columns['client_featured'] = 'client_featured';
    $columns['client_display_order'] = 'client_display_order';
    return $columns;
}

/**
 * Handle sorting for custom columns
 */
add_action('pre_get_posts', 'ehs_clients_column_orderby');
function ehs_clients_column_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    // Only for Clients post type
    if ($query->get('post_type') !== 'clients') {
        return;
    }

    $orderby = $query->get('orderby');
    $meta_fields = array('client_industry', 'client_status', 'client_featured', 'client_display_order');

    if (in_array($orderby, $meta_fields)) {
        $query->set('meta_key', $orderby);

        // Use numeric ordering for order and featured
        if (in_array($orderby, array('client_display_order', 'client_featured'))) {
            $query->set('orderby', 'meta_value_num');
        } else {
            $query->set('orderby', 'meta_value');
        }
    }
}

/**
 * Add inline styles for better column display
 */
add_action('admin_head-edit.php', 'ehs_clients_admin_column_styles');
function ehs_clients_admin_column_styles() {
    global $post_type;

    if ('clients' !== $post_type) {
        return;
    }
    ?>
    <style>
        .column-client_logo_col { width: 70px; text-align: center; }
        .column-client_industry { width: 140px; }
        .column-client_location { width: 150px; }
        .column-client_website { width: 180px; }
        .column-client_status { width: 100px; }
        .column-client_featured { width: 80px; text-align: center; }
        .column-client_display_order { width: 60px; text-align: center; }
    </style>
    <?php
}
