<?php
/**
 * Services Meta Box UI and Save Logic
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

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

    $service_short_description = get_post_meta($post->ID, 'service_short_description', true);
    $service_icon = get_post_meta($post->ID, 'service_icon', true);
    $service_related_services = get_post_meta($post->ID, 'service_related_services', true);
    $service_featured = get_post_meta($post->ID, 'service_featured', true);
    $service_order = get_post_meta($post->ID, 'service_order', true);

    ?>
    <table class="form-table">
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

    // Save meta fields with appropriate sanitization
    // Textarea fields
    $textarea_fields = array(
        'service_short_description',
    );
    
    foreach ($textarea_fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_textarea_field($_POST[$field]));
        }
    }
    
    // Text/select fields
    $text_fields = array(
        'service_icon',
        'service_order',
    );
    
    foreach ($text_fields as $field) {
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
