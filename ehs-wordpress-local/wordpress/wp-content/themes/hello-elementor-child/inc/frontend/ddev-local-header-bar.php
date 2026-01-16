<?php
/**
 * DDEV Local Environment Indicator
 * Shows a "LOCAL" badge in the WordPress admin bar when running in DDEV.
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Check if we're running in DDEV.
 */
function ehs_is_ddev_environment() {
    if (getenv('IS_DDEV_PROJECT') !== 'true') {
        return false;
    }

    return true;
}

/**
 * Add a LOCAL badge to the WP admin bar (front + wp-admin).
 */
function ehs_ddev_admin_bar_local_badge($wp_admin_bar) {
    if (!ehs_is_ddev_environment()) {
        return;
    }

    if (!is_user_logged_in() || !is_admin_bar_showing()) {
        return;
    }

    // Get server hostname
    $hostname = gethostname();
    $display_text = 'LOCAL';

    // Add hostname if available
    if ($hostname && $hostname !== 'localhost') {
        $display_text .= ' • ' . $hostname;
    }

    $wp_admin_bar->add_node(array(
        'id'    => 'ehs-env-local',
        'parent' => 'top-secondary',
        'title' => $display_text,
        'href'  => admin_url(),
        'meta'  => array(
            'class' => 'ehs-env-local-badge',
            'title' => 'Local development environment (DDEV) running on ' . $hostname,
        ),
    ));
}

/**
 * Output styles for the admin bar badge.
 */
function ehs_ddev_admin_bar_local_badge_styles() {
    if (!ehs_is_ddev_environment()) {
        return;
    }

    if (!is_user_logged_in() || !is_admin_bar_showing()) {
        return;
    }

    ?>
    <style id="ehs-ddev-admin-bar-local-badge-styles">
        #wpadminbar .ehs-env-local-badge > .ab-item {
            background: #ff9800;
            color: #ffffff;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        #wpadminbar .ehs-env-local-badge > .ab-item:hover,
        #wpadminbar .ehs-env-local-badge.hover > .ab-item {
            background: #e68900;
            color: #ffffff;
        }
    </style>
    <?php
}

/**
 * Display a prominent local development banner at the top of the page.
 */
function ehs_ddev_local_banner() {
    if (!ehs_is_ddev_environment()) {
        return;
    }

    $hostname = gethostname();
    $server_info = $hostname ?: 'Unknown Server';
    ?>
    <div id="ehs-local-dev-banner" style="position: fixed; top: 0; left: 0; right: 0; z-index: 999999; background: linear-gradient(135deg, #ff6b35 0%, #ff9800 100%); color: #fff; padding: 8px 20px; text-align: center; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, sans-serif; font-size: 13px; font-weight: 600; letter-spacing: 0.5px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); border-bottom: 2px solid rgba(0,0,0,0.1);">
        ⚠️ <strong>LOCAL DEVELOPMENT</strong> • Running on: <code style="background: rgba(0,0,0,0.2); padding: 2px 8px; border-radius: 3px; font-family: 'Monaco', 'Courier New', monospace;"><?php echo esc_html($server_info); ?></code> • URL: <code style="background: rgba(0,0,0,0.2); padding: 2px 8px; border-radius: 3px; font-family: 'Monaco', 'Courier New', monospace;"><?php echo esc_html(home_url()); ?></code>
    </div>
    <style>
        body { padding-top: 38px !important; }
        #wpadminbar { top: 38px !important; }
    </style>
    <?php
}

add_action('admin_bar_menu', 'ehs_ddev_admin_bar_local_badge', 1000);
add_action('wp_head', 'ehs_ddev_admin_bar_local_badge_styles');
add_action('admin_head', 'ehs_ddev_admin_bar_local_badge_styles');
add_action('wp_body_open', 'ehs_ddev_local_banner', 1);
add_action('wp_footer', 'ehs_ddev_local_banner', 1);
