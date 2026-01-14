<?php
/**
 * Admin Columns for Services Post Type
 * Adds custom columns with icon previews, color-coded badges, and sortable fields
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add custom columns to Services admin list
 */
add_filter('manage_services_posts_columns', 'ehs_services_custom_columns');
function ehs_services_custom_columns($columns) {
    $new_columns = array();

    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;

        // Insert custom columns after title
        if ($key === 'title') {
            $new_columns['service_icon'] = __('Icon', 'hello-elementor-child');
            $new_columns['service_category'] = __('Category', 'hello-elementor-child');
            $new_columns['service_area'] = __('Area', 'hello-elementor-child');
            $new_columns['service_featured'] = __('Featured', 'hello-elementor-child');
            $new_columns['service_order'] = __('Order', 'hello-elementor-child');
        }
    }

    // Remove comments column (handled by disable-comments)
    unset($new_columns['comments']);

    return $new_columns;
}

/**
 * Populate custom column content
 */
add_action('manage_services_posts_custom_column', 'ehs_services_column_content', 10, 2);
function ehs_services_column_content($column_name, $post_id) {
    switch ($column_name) {
        case 'service_icon':
            $icon_id = get_post_meta($post_id, 'service_icon', true);
            if ($icon_id) {
                echo wp_get_attachment_image($icon_id, array(40, 40), false, array(
                    'style' => 'border-radius: 3px; border: 1px solid #ddd;'
                ));
            } else {
                echo '<span style="color: #999;">—</span>';
            }
            break;

        case 'service_category':
            $category = get_post_meta($post_id, 'service_category', true);
            echo $category ? esc_html($category) : '<span style="color: #999;">—</span>';
            break;

        case 'service_area':
            $area = get_post_meta($post_id, 'service_area', true);
            if ($area) {
                $badge_colors = array(
                    'California' => '#2271b1',
                    'Federal' => '#d63638',
                    'All' => '#00a32a',
                );
                $color = isset($badge_colors[$area]) ? $badge_colors[$area] : '#999';
                echo '<span style="background: ' . esc_attr($color) . '; color: white; padding: 2px 8px; border-radius: 3px; font-size: 11px; font-weight: 600;">' . esc_html($area) . '</span>';
            } else {
                echo '<span style="color: #999;">—</span>';
            }
            break;

        case 'service_featured':
            $featured = get_post_meta($post_id, 'service_featured', true);
            if ($featured) {
                echo '<span class="dashicons dashicons-star-filled" style="color: #f0ad4e;" title="Featured"></span>';
            } else {
                echo '<span style="color: #ddd;">—</span>';
            }
            break;

        case 'service_order':
            $order = get_post_meta($post_id, 'service_order', true);
            echo $order ? esc_html($order) : '<span style="color: #999;">0</span>';
            break;
    }
}

/**
 * Make custom columns sortable
 */
add_filter('manage_edit-services_sortable_columns', 'ehs_services_sortable_columns');
function ehs_services_sortable_columns($columns) {
    $columns['service_category'] = 'service_category';
    $columns['service_area'] = 'service_area';
    $columns['service_featured'] = 'service_featured';
    $columns['service_order'] = 'service_order';
    return $columns;
}

/**
 * Handle sorting for custom columns
 */
add_action('pre_get_posts', 'ehs_services_column_orderby');
function ehs_services_column_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    // Only for Services post type
    if ($query->get('post_type') !== 'services') {
        return;
    }

    $orderby = $query->get('orderby');
    $meta_fields = array('service_category', 'service_area', 'service_featured', 'service_order');

    if (in_array($orderby, $meta_fields)) {
        $query->set('meta_key', $orderby);

        // Use numeric ordering for order and featured
        if (in_array($orderby, array('service_order', 'service_featured'))) {
            $query->set('orderby', 'meta_value_num');
        } else {
            $query->set('orderby', 'meta_value');
        }
    }
}

/**
 * Add inline styles for better column display
 */
add_action('admin_head-edit.php', 'ehs_services_admin_column_styles');
function ehs_services_admin_column_styles() {
    global $post_type;

    if ('services' !== $post_type) {
        return;
    }
    ?>
    <style>
        .column-service_icon { width: 60px; text-align: center; }
        .column-service_category { width: 150px; }
        .column-service_area { width: 100px; text-align: center; }
        .column-service_featured { width: 80px; text-align: center; }
        .column-service_order { width: 60px; text-align: center; }
    </style>
    <?php
}
