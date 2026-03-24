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
 * Show menu_order next to post title in Services list (edit.php?post_type=services) for sanity when ordering.
 */
add_filter('the_title', 'ehs_services_admin_title_with_menu_order', 10, 2);
function ehs_services_admin_title_with_menu_order($title, $post_id = null) {
    if (!is_admin() || empty($post_id)) {
        return $title;
    }
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || $screen->id !== 'edit-services') {
        return $title;
    }
    $post = get_post($post_id);
    if (!$post || $post->post_type !== 'services') {
        return $title;
    }
    $order = (int) $post->menu_order;
    return $title . ' <span class="ehs-menu-order-badge" style="color:#646970; font-weight:normal; font-size:12px;">(' . $order . ')</span>';
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
            $new_columns['featured_image'] = __('Thumbnail', 'hello-elementor-child');
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
            $terms = get_the_terms($post_id, 'service_category');
            if (!is_wp_error($terms) && !empty($terms)) {
                echo esc_html(implode(', ', wp_list_pluck($terms, 'name')));
            } else {
                echo '<span style="color: #999;">—</span>';
            }
            break;

        case 'service_area':
            $terms = get_the_terms($post_id, 'service_area');
            $area = (!is_wp_error($terms) && !empty($terms)) ? implode(', ', wp_list_pluck($terms, 'name')) : '';
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
            $post_obj = get_post($post_id);
            $order = $post_obj ? (int) $post_obj->menu_order : 0;
            echo $order ? esc_html($order) : '<span style="color: #999;">0</span>';
            break;
    }
}

/**
 * Make custom columns sortable
 */
add_filter('manage_edit-services_sortable_columns', 'ehs_services_sortable_columns');
function ehs_services_sortable_columns($columns) {
    $columns['service_featured'] = 'service_featured';
    $columns['service_order'] = 'menu_order'; // Built-in post field
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

    if ($query->get('post_type') !== 'services') {
        return;
    }

    $orderby = $query->get('orderby');
    if ($orderby === 'menu_order') {
        $query->set('orderby', 'menu_order');
        return;
    }
    if ($orderby === 'service_featured') {
        $query->set('meta_key', 'service_featured');
        $query->set('orderby', 'meta_value_num');
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
        .column-featured_image { width: 70px; text-align: center; }
        .column-service_icon { width: 60px; text-align: center; }
        .column-service_category { width: 150px; }
        .column-service_area { width: 100px; text-align: center; }
        .column-service_featured { width: 80px; text-align: center; }
        .column-service_order { width: 60px; text-align: center; }
    </style>
    <?php
}
