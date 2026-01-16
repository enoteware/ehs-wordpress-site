<?php
/**
 * Credentials Meta Box UI and Save Logic
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add Credentials Meta Boxes
 */
function ehs_add_credentials_meta_boxes() {
    add_meta_box(
        'ehs_credential_details',
        __('Credential Details', 'hello-elementor-child'),
        'ehs_credential_details_meta_box_callback',
        'credentials',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'ehs_add_credentials_meta_boxes');

/**
 * Credential Details Meta Box Callback
 */
function ehs_credential_details_meta_box_callback($post) {
    wp_nonce_field('ehs_credential_meta_box', 'ehs_credential_meta_box_nonce');

    $credential_acronym = get_post_meta($post->ID, 'credential_acronym', true);
    $credential_issuing_organization = get_post_meta($post->ID, 'credential_issuing_organization', true);
    $credential_date_obtained = get_post_meta($post->ID, 'credential_date_obtained', true);
    $credential_category = get_post_meta($post->ID, 'credential_category', true);
    $credential_type = get_post_meta($post->ID, 'credential_type', true);
    $credential_order = get_post_meta($post->ID, 'credential_order', true);
    $credential_featured = get_post_meta($post->ID, 'credential_featured', true);

    ?>
    <table class="form-table">
        <tr>
            <th><label for="credential_acronym"><?php _e('Acronym', 'hello-elementor-child'); ?></label></th>
            <td>
                <input type="text" id="credential_acronym" name="credential_acronym" value="<?php echo esc_attr($credential_acronym); ?>" class="regular-text" />
                <p class="description"><?php _e('Acronym or abbreviation (e.g., CIH, CSP, PMP)', 'hello-elementor-child'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="credential_issuing_organization"><?php _e('Issuing Organization', 'hello-elementor-child'); ?></label></th>
            <td>
                <input type="text" id="credential_issuing_organization" name="credential_issuing_organization" value="<?php echo esc_attr($credential_issuing_organization); ?>" class="regular-text" />
                <p class="description"><?php _e('Organization that issued this credential', 'hello-elementor-child'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="credential_date_obtained"><?php _e('Date Obtained', 'hello-elementor-child'); ?></label></th>
            <td>
                <input type="date" id="credential_date_obtained" name="credential_date_obtained" value="<?php echo esc_attr($credential_date_obtained); ?>" class="regular-text" />
                <p class="description"><?php _e('Date when this credential was obtained (optional)', 'hello-elementor-child'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="credential_category"><?php _e('Category', 'hello-elementor-child'); ?></label></th>
            <td>
                <select id="credential_category" name="credential_category" class="regular-text">
                    <option value=""><?php _e('Select Category', 'hello-elementor-child'); ?></option>
                    <option value="Professional Certification" <?php selected($credential_category, 'Professional Certification'); ?>><?php _e('Professional Certification', 'hello-elementor-child'); ?></option>
                    <option value="Business Designation" <?php selected($credential_category, 'Business Designation'); ?>><?php _e('Business Designation', 'hello-elementor-child'); ?></option>
                    <option value="License" <?php selected($credential_category, 'License'); ?>><?php _e('License', 'hello-elementor-child'); ?></option>
                    <option value="Affiliation" <?php selected($credential_category, 'Affiliation'); ?>><?php _e('Affiliation', 'hello-elementor-child'); ?></option>
                </select>
                <p class="description"><?php _e('Category classification for this credential', 'hello-elementor-child'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="credential_type"><?php _e('Type', 'hello-elementor-child'); ?></label></th>
            <td>
                <select id="credential_type" name="credential_type" class="regular-text">
                    <option value=""><?php _e('Select Type', 'hello-elementor-child'); ?></option>
                    <option value="Certification" <?php selected($credential_type, 'Certification'); ?>><?php _e('Certification', 'hello-elementor-child'); ?></option>
                    <option value="License" <?php selected($credential_type, 'License'); ?>><?php _e('License', 'hello-elementor-child'); ?></option>
                    <option value="Designation" <?php selected($credential_type, 'Designation'); ?>><?php _e('Designation', 'hello-elementor-child'); ?></option>
                    <option value="Affiliation" <?php selected($credential_type, 'Affiliation'); ?>><?php _e('Affiliation', 'hello-elementor-child'); ?></option>
                </select>
                <p class="description"><?php _e('Type of credential', 'hello-elementor-child'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="credential_order"><?php _e('Display Order', 'hello-elementor-child'); ?></label></th>
            <td>
                <input type="number" id="credential_order" name="credential_order" value="<?php echo esc_attr($credential_order ? $credential_order : '0'); ?>" min="0" class="small-text" />
                <p class="description"><?php _e('Order for display (lower numbers appear first)', 'hello-elementor-child'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="credential_featured"><?php _e('Featured Credential', 'hello-elementor-child'); ?></label></th>
            <td>
                <label>
                    <input type="checkbox" id="credential_featured" name="credential_featured" value="1" <?php checked($credential_featured, '1'); ?> />
                    <?php _e('Mark as featured credential', 'hello-elementor-child'); ?>
                </label>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Save Credentials Meta Box Data
 */
function ehs_save_credentials_meta_box($post_id) {
    // Check nonce
    if (!isset($_POST['ehs_credential_meta_box_nonce']) || !wp_verify_nonce($_POST['ehs_credential_meta_box_nonce'], 'ehs_credential_meta_box')) {
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
    $text_fields = array(
        'credential_acronym',
        'credential_issuing_organization',
        'credential_date_obtained',
        'credential_category',
        'credential_type',
        'credential_order',
    );
    
    foreach ($text_fields as $field) {
        if (isset($_POST[$field])) {
            if ($field === 'credential_order') {
                update_post_meta($post_id, $field, absint($_POST[$field]));
            } else {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            }
        }
    }

    // Handle featured checkbox
    $featured = isset($_POST['credential_featured']) ? '1' : '0';
    update_post_meta($post_id, 'credential_featured', $featured);
}
add_action('save_post_credentials', 'ehs_save_credentials_meta_box');
