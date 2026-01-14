<?php
/**
 * Hello Elementor Child Theme Functions
 * 
 * @package HelloElementorChild
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Enqueue parent theme styles
 */
function hello_elementor_child_enqueue_styles() {
    wp_enqueue_style(
        'hello-elementor-parent-style',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme()->get('Version')
    );
}
add_action('wp_enqueue_scripts', 'hello_elementor_child_enqueue_styles');

/**
 * Register Services Custom Post Type
 */
function ehs_register_services_post_type() {
    $labels = array(
        'name'                  => _x('Services', 'Post Type General Name', 'hello-elementor-child'),
        'singular_name'         => _x('Service', 'Post Type Singular Name', 'hello-elementor-child'),
        'menu_name'             => __('Services', 'hello-elementor-child'),
        'name_admin_bar'        => __('Service', 'hello-elementor-child'),
        'archives'              => __('Service Archives', 'hello-elementor-child'),
        'attributes'             => __('Service Attributes', 'hello-elementor-child'),
        'parent_item_colon'     => __('Parent Service:', 'hello-elementor-child'),
        'all_items'             => __('All Services', 'hello-elementor-child'),
        'add_new_item'          => __('Add New Service', 'hello-elementor-child'),
        'add_new'               => __('Add New', 'hello-elementor-child'),
        'new_item'              => __('New Service', 'hello-elementor-child'),
        'edit_item'             => __('Edit Service', 'hello-elementor-child'),
        'update_item'           => __('Update Service', 'hello-elementor-child'),
        'view_item'             => __('View Service', 'hello-elementor-child'),
        'view_items'            => __('View Services', 'hello-elementor-child'),
        'search_items'          => __('Search Service', 'hello-elementor-child'),
        'not_found'             => __('Not found', 'hello-elementor-child'),
        'not_found_in_trash'    => __('Not found in Trash', 'hello-elementor-child'),
        'featured_image'        => __('Featured Image', 'hello-elementor-child'),
        'set_featured_image'    => __('Set featured image', 'hello-elementor-child'),
        'remove_featured_image' => __('Remove featured image', 'hello-elementor-child'),
        'use_featured_image'    => __('Use as featured image', 'hello-elementor-child'),
        'insert_into_item'      => __('Insert into service', 'hello-elementor-child'),
        'uploaded_to_this_item' => __('Uploaded to this service', 'hello-elementor-child'),
        'items_list'            => __('Services list', 'hello-elementor-child'),
        'items_list_navigation' => __('Services list navigation', 'hello-elementor-child'),
        'filter_items_list'     => __('Filter services list', 'hello-elementor-child'),
    );
    
    $args = array(
        'label'                 => __('Service', 'hello-elementor-child'),
        'description'           => __('EHS Analytical Services', 'hello-elementor-child'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes'),
        'hierarchical'          => true,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 20,
        'menu_icon'             => 'dashicons-businessman',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
        'rewrite'               => array(
            'slug'                  => 'services',
            'with_front'            => false,
            'pages'                 => true,
            'feeds'                 => true,
        ),
    );
    
    register_post_type('services', $args);
}
add_action('init', 'ehs_register_services_post_type', 0);

/**
 * Register Services Meta Fields
 */
function ehs_register_services_meta_fields() {
    $meta_fields = array(
        'service_category'      => array('type' => 'string', 'sanitize' => 'sanitize_text_field'),
        'service_short_description' => array('type' => 'string', 'sanitize' => 'sanitize_textarea_field'),
        'service_icon'          => array('type' => 'integer', 'sanitize' => 'absint'),
        'service_area'         => array('type' => 'string', 'sanitize' => 'sanitize_text_field'),
        'service_certifications' => array('type' => 'string', 'sanitize' => 'sanitize_textarea_field'),
        'service_target_audience' => array('type' => 'string', 'sanitize' => 'sanitize_textarea_field'),
        'service_related_services' => array('type' => 'string', 'sanitize' => 'sanitize_text_field'),
        'service_featured'     => array('type' => 'boolean', 'sanitize' => 'rest_sanitize_boolean'),
        'service_order'        => array('type' => 'integer', 'sanitize' => 'absint'),
    );
    
    foreach ($meta_fields as $field => $config) {
        register_post_meta('services', $field, array(
            'type'              => $config['type'],
            'single'            => true,
            'show_in_rest'      => true,
            'sanitize_callback' => $config['sanitize'],
            'auth_callback'     => function() {
                return current_user_can('edit_posts');
            },
        ));
    }
}
add_action('init', 'ehs_register_services_meta_fields');

/**
 * Add Services Meta Boxes
 */
function ehs_add_services_meta_boxes() {
    add_meta_box(
        'ehs_service_details',
        __('Service Details', 'hello-elementor-child'),
        'ehs_service_details_meta_box_callback',
        'services',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'ehs_add_services_meta_boxes');

/**
 * Service Details Meta Box Callback
 */
function ehs_service_details_meta_box_callback($post) {
    wp_nonce_field('ehs_service_meta_box', 'ehs_service_meta_box_nonce');
    
    $service_category = get_post_meta($post->ID, 'service_category', true);
    $service_short_description = get_post_meta($post->ID, 'service_short_description', true);
    $service_icon = get_post_meta($post->ID, 'service_icon', true);
    $service_area = get_post_meta($post->ID, 'service_area', true);
    $service_certifications = get_post_meta($post->ID, 'service_certifications', true);
    $service_target_audience = get_post_meta($post->ID, 'service_target_audience', true);
    $service_related_services = get_post_meta($post->ID, 'service_related_services', true);
    $service_featured = get_post_meta($post->ID, 'service_featured', true);
    $service_order = get_post_meta($post->ID, 'service_order', true);
    
    ?>
    <table class="form-table">
        <tr>
            <th><label for="service_category"><?php _e('Service Category', 'hello-elementor-child'); ?></label></th>
            <td>
                <input type="text" id="service_category" name="service_category" value="<?php echo esc_attr($service_category); ?>" class="regular-text" />
                <p class="description"><?php _e('Category for this service (e.g., Construction Safety, Environmental, Industrial Hygiene)', 'hello-elementor-child'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="service_short_description"><?php _e('Short Description', 'hello-elementor-child'); ?></label></th>
            <td>
                <textarea id="service_short_description" name="service_short_description" rows="3" class="large-text"><?php echo esc_textarea($service_short_description); ?></textarea>
                <p class="description"><?php _e('Brief description for listings and excerpts', 'hello-elementor-child'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="service_icon"><?php _e('Service Icon', 'hello-elementor-child'); ?></label></th>
            <td>
                <input type="hidden" id="service_icon" name="service_icon" value="<?php echo esc_attr($service_icon); ?>" />
                <button type="button" class="button" id="service_icon_button"><?php _e('Select Icon', 'hello-elementor-child'); ?></button>
                <button type="button" class="button" id="service_icon_remove" style="<?php echo $service_icon ? '' : 'display:none;'; ?>"><?php _e('Remove Icon', 'hello-elementor-child'); ?></button>
                <div id="service_icon_preview" style="margin-top: 10px;">
                    <?php if ($service_icon) : ?>
                        <?php echo wp_get_attachment_image($service_icon, 'thumbnail'); ?>
                    <?php endif; ?>
                </div>
                <p class="description"><?php _e('Icon image for this service', 'hello-elementor-child'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="service_area"><?php _e('Service Area', 'hello-elementor-child'); ?></label></th>
            <td>
                <select id="service_area" name="service_area" class="regular-text">
                    <option value=""><?php _e('Select Service Area', 'hello-elementor-child'); ?></option>
                    <option value="California" <?php selected($service_area, 'California'); ?>><?php _e('California', 'hello-elementor-child'); ?></option>
                    <option value="Federal" <?php selected($service_area, 'Federal'); ?>><?php _e('Federal', 'hello-elementor-child'); ?></option>
                    <option value="All" <?php selected($service_area, 'All'); ?>><?php _e('All', 'hello-elementor-child'); ?></option>
                </select>
                <p class="description"><?php _e('Geographic area this service covers', 'hello-elementor-child'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="service_certifications"><?php _e('Certifications', 'hello-elementor-child'); ?></label></th>
            <td>
                <textarea id="service_certifications" name="service_certifications" rows="3" class="large-text"><?php echo esc_textarea($service_certifications); ?></textarea>
                <p class="description"><?php _e('Relevant certifications (e.g., DVBE, SDVOSB, CIH)', 'hello-elementor-child'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="service_target_audience"><?php _e('Target Audience', 'hello-elementor-child'); ?></label></th>
            <td>
                <textarea id="service_target_audience" name="service_target_audience" rows="3" class="large-text"><?php echo esc_textarea($service_target_audience); ?></textarea>
                <p class="description"><?php _e('Who this service is for (e.g., Federal contractors, Caltrans bidders)', 'hello-elementor-child'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="service_related_services"><?php _e('Related Services', 'hello-elementor-child'); ?></label></th>
            <td>
                <?php
                $services = get_posts(array(
                    'post_type' => 'services',
                    'posts_per_page' => -1,
                    'post__not_in' => array($post->ID),
                    'orderby' => 'title',
                    'order' => 'ASC',
                ));
                $related_ids = $service_related_services ? explode(',', $service_related_services) : array();
                ?>
                <select id="service_related_services" name="service_related_services[]" multiple class="regular-text" style="height: 150px;">
                    <?php foreach ($services as $service) : ?>
                        <option value="<?php echo $service->ID; ?>" <?php selected(in_array($service->ID, $related_ids)); ?>>
                            <?php echo esc_html($service->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="description"><?php _e('Hold Ctrl/Cmd to select multiple related services', 'hello-elementor-child'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="service_featured"><?php _e('Featured Service', 'hello-elementor-child'); ?></label></th>
            <td>
                <label>
                    <input type="checkbox" id="service_featured" name="service_featured" value="1" <?php checked($service_featured, '1'); ?> />
                    <?php _e('Mark as featured service', 'hello-elementor-child'); ?>
                </label>
            </td>
        </tr>
        <tr>
            <th><label for="service_order"><?php _e('Service Order', 'hello-elementor-child'); ?></label></th>
            <td>
                <input type="number" id="service_order" name="service_order" value="<?php echo esc_attr($service_order ? $service_order : '0'); ?>" min="0" class="small-text" />
                <p class="description"><?php _e('Order for menu display (lower numbers appear first)', 'hello-elementor-child'); ?></p>
            </td>
        </tr>
    </table>
    
    <script>
    jQuery(document).ready(function($) {
        // Media uploader for service icon
        $('#service_icon_button').on('click', function(e) {
            e.preventDefault();
            var frame = wp.media({
                title: '<?php _e('Select Service Icon', 'hello-elementor-child'); ?>',
                button: {
                    text: '<?php _e('Use this icon', 'hello-elementor-child'); ?>'
                },
                multiple: false
            });
            
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $('#service_icon').val(attachment.id);
                $('#service_icon_preview').html('<img src="' + attachment.url + '" style="max-width: 150px;" />');
                $('#service_icon_remove').show();
            });
            
            frame.open();
        });
        
        $('#service_icon_remove').on('click', function(e) {
            e.preventDefault();
            $('#service_icon').val('');
            $('#service_icon_preview').html('');
            $(this).hide();
        });
    });
    </script>
    <?php
}

/**
 * Save Services Meta Box Data
 */
function ehs_save_services_meta_box($post_id) {
    // Check nonce
    if (!isset($_POST['ehs_service_meta_box_nonce']) || !wp_verify_nonce($_POST['ehs_service_meta_box_nonce'], 'ehs_service_meta_box')) {
        return;
    }
    
    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Save meta fields
    $fields = array(
        'service_category',
        'service_short_description',
        'service_icon',
        'service_area',
        'service_certifications',
        'service_target_audience',
        'service_order',
    );
    
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
    
    // Handle featured checkbox
    $featured = isset($_POST['service_featured']) ? '1' : '0';
    update_post_meta($post_id, 'service_featured', $featured);
    
    // Handle related services (multiple select)
    if (isset($_POST['service_related_services']) && is_array($_POST['service_related_services'])) {
        $related = array_map('absint', $_POST['service_related_services']);
        update_post_meta($post_id, 'service_related_services', implode(',', $related));
    } else {
        update_post_meta($post_id, 'service_related_services', '');
    }
}
add_action('save_post_services', 'ehs_save_services_meta_box');

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
