<?php
/**
 * Services Order Sync – Admin page to apply ehs_services_display_order() to service posts.
 *
 * @package HelloElementorChild
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_menu', 'ehs_add_services_order_sync_page');
function ehs_add_services_order_sync_page() {
    add_options_page(
        'Services Order',
        'Services Order',
        'manage_options',
        'ehs-services-order-sync',
        'ehs_render_services_order_sync_page'
    );
}

function ehs_render_services_order_sync_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $message = '';
    if (isset($_POST['ehs_sync_services_order']) && check_admin_referer('ehs_sync_services_order_nonce')) {
        $result = ehs_sync_services_display_order();
        $message = sprintf(
            __('Updated %d service(s).', 'hello-elementor-child'),
            $result['updated']
        );
        if (!empty($result['skipped'])) {
            $message .= ' ' . __('Post IDs not found or not published:', 'hello-elementor-child') . ' ' . implode(', ', array_map('absint', $result['skipped']));
        }
        $message = '<div class="notice notice-success"><p>' . $message . '</p></div>';
    }
    $order_list = ehs_services_display_order();
    $flat = array();
    $order = 0;
    foreach ($order_list as $section) {
        foreach ($section['ids'] as $post_id) {
            $post_id = (int) $post_id;
            $post = get_post($post_id);
            $flat[] = array(
                'order'   => $order,
                'id'      => $post_id,
                'title'   => $post ? get_the_title($post) : '',
                'section' => $section['title'],
            );
            $order++;
        }
    }
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Services Order', 'hello-elementor-child'); ?></h1>
        <p><?php esc_html_e('The order and section titles are defined in the theme (ehs_services_display_order) using post IDs. Click below to apply that order to service posts: set the built-in menu_order field and service_section meta so the archive and shortcodes display services in the correct order.', 'hello-elementor-child'); ?></p>
        <?php echo $message; ?>
        <form method="post" action="">
            <?php wp_nonce_field('ehs_sync_services_order_nonce'); ?>
            <p>
                <button type="submit" name="ehs_sync_services_order" class="button button-primary"><?php esc_html_e('Sync services order', 'hello-elementor-child'); ?></button>
            </p>
        </form>
        <h2><?php esc_html_e('Order reference (by service post title)', 'hello-elementor-child'); ?></h2>
        <table class="widefat striped" style="max-width: 720px;">
            <thead>
                <tr>
                    <th><?php esc_html_e('Order', 'hello-elementor-child'); ?></th>
                    <th><?php esc_html_e('Post ID', 'hello-elementor-child'); ?></th>
                    <th><?php esc_html_e('Service post title', 'hello-elementor-child'); ?></th>
                    <th><?php esc_html_e('Section', 'hello-elementor-child'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($flat as $row) : ?>
                <tr>
                    <td><?php echo (int) $row['order']; ?></td>
                    <td><code><?php echo (int) $row['id']; ?></code></td>
                    <td><?php echo $row['title'] ? esc_html($row['title']) : '<em>' . esc_html__('(not found)', 'hello-elementor-child') . '</em>'; ?></td>
                    <td><?php echo esc_html($row['section']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}
