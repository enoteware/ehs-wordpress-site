<?php
/**
 * DDEV Local Environment Header Bar
 * Displays a persistent orange warning bar at the top of all pages when running in DDEV environment
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * DDEV Local Environment Header Bar
 * Displays a persistent orange warning bar at the top of all pages when running in DDEV environment
 */
function ehs_ddev_local_header_bar() {
    // Only show in DDEV environment
    if (getenv('IS_DDEV_PROJECT') !== 'true') {
        return;
    }

    // Output the header bar HTML
    ?>
    <div id="ehs-ddev-local-header-bar" style="display: none;">
        <div class="ehs-ddev-header-content">
            <strong>LOCAL DEVELOPMENT</strong>
        </div>
    </div>
    <?php
}

/**
 * Output DDEV header bar CSS styles
 */
function ehs_ddev_local_header_bar_styles() {
    // Only load in DDEV environment
    if (getenv('IS_DDEV_PROJECT') !== 'true') {
        return;
    }

    // Output CSS directly
    ?>
    <style id="ehs-ddev-local-header-bar-styles">
        #ehs-ddev-local-header-bar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: #ff9800;
            color: #ffffff;
            text-align: center;
            padding: 12px 20px;
            z-index: 9999;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
        }
        /* Ensure Elementor header/footer can appear above DDEV bar if needed */
        .elementor-location-header,
        .elementor-location-footer,
        [data-elementor-type="header"],
        [data-elementor-type="footer"] {
            position: relative;
            z-index: 10000;
        }
        .ehs-ddev-header-content {
            max-width: 100%;
            margin: 0 auto;
        }
        body.admin-bar #ehs-ddev-local-header-bar {
            top: 32px;
        }
        @media screen and (max-width: 782px) {
            body.admin-bar #ehs-ddev-local-header-bar {
                top: 46px;
            }
        }
    </style>
    <?php
}

/**
 * Output DDEV header bar JavaScript
 */
function ehs_ddev_local_header_bar_scripts() {
    // Only load in DDEV environment
    if (getenv('IS_DDEV_PROJECT') !== 'true') {
        return;
    }

    // Output JavaScript directly
    ?>
    <script id="ehs-ddev-local-header-bar-scripts">
        (function() {
            function initDdevHeaderBar() {
                var headerBar = document.getElementById("ehs-ddev-local-header-bar");
                if (headerBar) {
                    headerBar.style.display = "block";
                    var barHeight = headerBar.offsetHeight;
                    var body = document.body;

                    // Account for WordPress admin bar if present
                    var adminBarHeight = 0;
                    var adminBar = document.getElementById("wpadminbar");
                    if (adminBar) {
                        adminBarHeight = adminBar.offsetHeight;
                    }

                    // Adjust body padding to account for fixed header bar
                    var totalOffset = barHeight + adminBarHeight;
                    body.style.paddingTop = totalOffset + "px";
                }
            }

            // Run on DOM ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initDdevHeaderBar);
            } else {
                initDdevHeaderBar();
            }
        })();
    </script>
    <?php
}

// Output styles in head on both frontend and admin
add_action('wp_head', 'ehs_ddev_local_header_bar_styles');
add_action('admin_head', 'ehs_ddev_local_header_bar_styles');

// Output scripts in footer on both frontend and admin
add_action('wp_footer', 'ehs_ddev_local_header_bar_scripts');
add_action('admin_footer', 'ehs_ddev_local_header_bar_scripts');

// Output header bar HTML on both frontend and admin
add_action('wp_footer', 'ehs_ddev_local_header_bar');
add_action('admin_footer', 'ehs_ddev_local_header_bar');
