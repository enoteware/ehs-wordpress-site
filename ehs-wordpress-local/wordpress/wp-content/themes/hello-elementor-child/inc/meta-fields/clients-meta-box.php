<?php
/**
 * Clients Meta Box UI and Save Logic
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add Clients Meta Boxes
 */
function ehs_add_clients_meta_boxes() {
    add_meta_box(
        'ehs_client_details',
        __('Client Details', 'hello-elementor-child'),
        'ehs_client_details_meta_box_callback',
        'clients',
        'normal',
        'high'
    );

    add_meta_box(
        'ehs_client_logo',
        __('Client Logo', 'hello-elementor-child'),
        'ehs_client_logo_meta_box_callback',
        'clients',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'ehs_add_clients_meta_boxes');

/**
 * Client Details Meta Box Callback
 */
function ehs_client_details_meta_box_callback($post) {
    wp_nonce_field('ehs_client_meta_box', 'ehs_client_meta_box_nonce');

    // Get all meta values
    $client_website = get_post_meta($post->ID, 'client_website', true);
    $client_industry = get_post_meta($post->ID, 'client_industry', true);
    $client_location = get_post_meta($post->ID, 'client_location', true);
    $client_since = get_post_meta($post->ID, 'client_since', true);
    $client_contact_name = get_post_meta($post->ID, 'client_contact_name', true);
    $client_contact_email = get_post_meta($post->ID, 'client_contact_email', true);
    $client_contact_phone = get_post_meta($post->ID, 'client_contact_phone', true);
    $client_services_used = get_post_meta($post->ID, 'client_services_used', true);
    $client_testimonial = get_post_meta($post->ID, 'client_testimonial', true);
    $client_status = get_post_meta($post->ID, 'client_status', true);
    $client_featured = get_post_meta($post->ID, 'client_featured', true);
    $client_display_order = get_post_meta($post->ID, 'client_display_order', true);
    $client_show_on_homepage = get_post_meta($post->ID, 'client_show_on_homepage', true);

    ?>
    <style>
        .ehs-meta-section { margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #eee; }
        .ehs-meta-section:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
        .ehs-meta-section h4 { margin: 0 0 15px 0; color: #003366; font-size: 14px; }
    </style>

    <!-- Company Information -->
    <div class="ehs-meta-section">
        <h4><?php _e('Company Information', 'hello-elementor-child'); ?></h4>
        <table class="form-table">
            <tr>
                <th><label for="client_website"><?php _e('Website URL', 'hello-elementor-child'); ?></label></th>
                <td>
                    <input type="url" id="client_website" name="client_website" value="<?php echo esc_url($client_website); ?>" class="regular-text" placeholder="https://example.com" />
                    <p class="description"><?php _e('Client company website URL', 'hello-elementor-child'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="client_industry"><?php _e('Industry', 'hello-elementor-child'); ?></label></th>
                <td>
                    <select id="client_industry" name="client_industry" class="regular-text">
                        <option value=""><?php _e('Select Industry', 'hello-elementor-child'); ?></option>
                        <option value="Biotechnology" <?php selected($client_industry, 'Biotechnology'); ?>><?php _e('Biotechnology', 'hello-elementor-child'); ?></option>
                        <option value="Pharmaceutical" <?php selected($client_industry, 'Pharmaceutical'); ?>><?php _e('Pharmaceutical', 'hello-elementor-child'); ?></option>
                        <option value="Healthcare" <?php selected($client_industry, 'Healthcare'); ?>><?php _e('Healthcare', 'hello-elementor-child'); ?></option>
                        <option value="Manufacturing" <?php selected($client_industry, 'Manufacturing'); ?>><?php _e('Manufacturing', 'hello-elementor-child'); ?></option>
                        <option value="Construction" <?php selected($client_industry, 'Construction'); ?>><?php _e('Construction', 'hello-elementor-child'); ?></option>
                        <option value="Education" <?php selected($client_industry, 'Education'); ?>><?php _e('Education', 'hello-elementor-child'); ?></option>
                        <option value="Government" <?php selected($client_industry, 'Government'); ?>><?php _e('Government', 'hello-elementor-child'); ?></option>
                        <option value="Energy" <?php selected($client_industry, 'Energy'); ?>><?php _e('Energy', 'hello-elementor-child'); ?></option>
                        <option value="Technology" <?php selected($client_industry, 'Technology'); ?>><?php _e('Technology', 'hello-elementor-child'); ?></option>
                        <option value="Real Estate" <?php selected($client_industry, 'Real Estate'); ?>><?php _e('Real Estate', 'hello-elementor-child'); ?></option>
                        <option value="Food & Beverage" <?php selected($client_industry, 'Food & Beverage'); ?>><?php _e('Food & Beverage', 'hello-elementor-child'); ?></option>
                        <option value="Agriculture" <?php selected($client_industry, 'Agriculture'); ?>><?php _e('Agriculture', 'hello-elementor-child'); ?></option>
                        <option value="Other" <?php selected($client_industry, 'Other'); ?>><?php _e('Other', 'hello-elementor-child'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="client_location"><?php _e('Location', 'hello-elementor-child'); ?></label></th>
                <td>
                    <input type="text" id="client_location" name="client_location" value="<?php echo esc_attr($client_location); ?>" class="regular-text" placeholder="City, State" />
                    <p class="description"><?php _e('Client location (e.g., San Diego, CA)', 'hello-elementor-child'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="client_since"><?php _e('Client Since', 'hello-elementor-child'); ?></label></th>
                <td>
                    <input type="text" id="client_since" name="client_since" value="<?php echo esc_attr($client_since); ?>" class="small-text" placeholder="2020" />
                    <p class="description"><?php _e('Year became a client (optional)', 'hello-elementor-child'); ?></p>
                </td>
            </tr>
        </table>
    </div>

    <!-- Contact Information -->
    <div class="ehs-meta-section">
        <h4><?php _e('Contact Information', 'hello-elementor-child'); ?></h4>
        <table class="form-table">
            <tr>
                <th><label for="client_contact_name"><?php _e('Contact Name', 'hello-elementor-child'); ?></label></th>
                <td>
                    <input type="text" id="client_contact_name" name="client_contact_name" value="<?php echo esc_attr($client_contact_name); ?>" class="regular-text" />
                    <p class="description"><?php _e('Primary contact person', 'hello-elementor-child'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="client_contact_email"><?php _e('Contact Email', 'hello-elementor-child'); ?></label></th>
                <td>
                    <input type="email" id="client_contact_email" name="client_contact_email" value="<?php echo esc_attr($client_contact_email); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th><label for="client_contact_phone"><?php _e('Contact Phone', 'hello-elementor-child'); ?></label></th>
                <td>
                    <input type="text" id="client_contact_phone" name="client_contact_phone" value="<?php echo esc_attr($client_contact_phone); ?>" class="regular-text" />
                </td>
            </tr>
        </table>
    </div>

    <!-- Services & Testimonial -->
    <div class="ehs-meta-section">
        <h4><?php _e('Services & Testimonial', 'hello-elementor-child'); ?></h4>
        <table class="form-table">
            <tr>
                <th><label for="client_services_used"><?php _e('Services Used', 'hello-elementor-child'); ?></label></th>
                <td>
                    <textarea id="client_services_used" name="client_services_used" rows="3" class="large-text"><?php echo esc_textarea($client_services_used); ?></textarea>
                    <p class="description"><?php _e('List of EHS services provided to this client (comma-separated)', 'hello-elementor-child'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="client_testimonial"><?php _e('Testimonial', 'hello-elementor-child'); ?></label></th>
                <td>
                    <textarea id="client_testimonial" name="client_testimonial" rows="4" class="large-text"><?php echo esc_textarea($client_testimonial); ?></textarea>
                    <p class="description"><?php _e('Client testimonial quote (optional)', 'hello-elementor-child'); ?></p>
                </td>
            </tr>
        </table>
    </div>

    <!-- Display Settings -->
    <div class="ehs-meta-section">
        <h4><?php _e('Display Settings', 'hello-elementor-child'); ?></h4>
        <table class="form-table">
            <tr>
                <th><label for="client_status"><?php _e('Status', 'hello-elementor-child'); ?></label></th>
                <td>
                    <select id="client_status" name="client_status" class="regular-text">
                        <option value="active" <?php selected($client_status, 'active'); ?>><?php _e('Active Client', 'hello-elementor-child'); ?></option>
                        <option value="past" <?php selected($client_status, 'past'); ?>><?php _e('Past Client', 'hello-elementor-child'); ?></option>
                        <option value="prospect" <?php selected($client_status, 'prospect'); ?>><?php _e('Prospect', 'hello-elementor-child'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="client_display_order"><?php _e('Display Order', 'hello-elementor-child'); ?></label></th>
                <td>
                    <input type="number" id="client_display_order" name="client_display_order" value="<?php echo esc_attr($client_display_order ? $client_display_order : '0'); ?>" min="0" class="small-text" />
                    <p class="description"><?php _e('Order for display (lower numbers appear first)', 'hello-elementor-child'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="client_featured"><?php _e('Featured', 'hello-elementor-child'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" id="client_featured" name="client_featured" value="1" <?php checked($client_featured, '1'); ?> />
                        <?php _e('Mark as featured client', 'hello-elementor-child'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th><label for="client_show_on_homepage"><?php _e('Homepage', 'hello-elementor-child'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" id="client_show_on_homepage" name="client_show_on_homepage" value="1" <?php checked($client_show_on_homepage, '1'); ?> />
                        <?php _e('Show on homepage client section', 'hello-elementor-child'); ?>
                    </label>
                </td>
            </tr>
        </table>
    </div>
    <?php
}

/**
 * Client Logo Meta Box Callback
 */
function ehs_client_logo_meta_box_callback($post) {
    $client_logo = get_post_meta($post->ID, 'client_logo', true);
    $logo_url = $client_logo ? wp_get_attachment_image_url($client_logo, 'medium') : '';

    ?>
    <div id="client-logo-preview" style="margin-bottom: 10px;">
        <?php if ($logo_url) : ?>
            <img src="<?php echo esc_url($logo_url); ?>" style="max-width: 100%; height: auto;" />
        <?php endif; ?>
    </div>
    <input type="hidden" id="client_logo" name="client_logo" value="<?php echo esc_attr($client_logo); ?>" />
    <button type="button" class="button" id="upload-client-logo"><?php _e('Upload Logo', 'hello-elementor-child'); ?></button>
    <button type="button" class="button" id="remove-client-logo" <?php echo !$client_logo ? 'style="display:none;"' : ''; ?>><?php _e('Remove', 'hello-elementor-child'); ?></button>

    <script>
    jQuery(document).ready(function($) {
        var mediaUploader;

        $('#upload-client-logo').on('click', function(e) {
            e.preventDefault();

            if (mediaUploader) {
                mediaUploader.open();
                return;
            }

            mediaUploader = wp.media({
                title: '<?php _e('Select Client Logo', 'hello-elementor-child'); ?>',
                button: { text: '<?php _e('Use as Logo', 'hello-elementor-child'); ?>' },
                multiple: false
            });

            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#client_logo').val(attachment.id);
                $('#client-logo-preview').html('<img src="' + attachment.url + '" style="max-width: 100%; height: auto;" />');
                $('#remove-client-logo').show();
            });

            mediaUploader.open();
        });

        $('#remove-client-logo').on('click', function(e) {
            e.preventDefault();
            $('#client_logo').val('');
            $('#client-logo-preview').html('');
            $(this).hide();
        });
    });
    </script>
    <?php
}

/**
 * Save Clients Meta Box Data
 */
function ehs_save_clients_meta_box($post_id) {
    // Check nonce
    if (!isset($_POST['ehs_client_meta_box_nonce']) || !wp_verify_nonce($_POST['ehs_client_meta_box_nonce'], 'ehs_client_meta_box')) {
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

    // Save text fields
    $text_fields = array(
        'client_industry',
        'client_location',
        'client_since',
        'client_contact_name',
        'client_contact_phone',
        'client_status',
    );

    foreach ($text_fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }

    // Save URL field
    if (isset($_POST['client_website'])) {
        update_post_meta($post_id, 'client_website', esc_url_raw($_POST['client_website']));
    }

    // Save email field
    if (isset($_POST['client_contact_email'])) {
        update_post_meta($post_id, 'client_contact_email', sanitize_email($_POST['client_contact_email']));
    }

    // Save textarea fields
    $textarea_fields = array('client_services_used', 'client_testimonial');
    foreach ($textarea_fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_textarea_field($_POST[$field]));
        }
    }

    // Save integer fields
    if (isset($_POST['client_logo'])) {
        update_post_meta($post_id, 'client_logo', absint($_POST['client_logo']));
    }
    if (isset($_POST['client_display_order'])) {
        update_post_meta($post_id, 'client_display_order', absint($_POST['client_display_order']));
    }

    // Handle checkboxes
    $client_featured = isset($_POST['client_featured']) ? '1' : '0';
    update_post_meta($post_id, 'client_featured', $client_featured);

    $client_show_on_homepage = isset($_POST['client_show_on_homepage']) ? '1' : '0';
    update_post_meta($post_id, 'client_show_on_homepage', $client_show_on_homepage);
}
add_action('save_post_clients', 'ehs_save_clients_meta_box');
