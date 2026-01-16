<?php
/**
 * Team Member Meta Fields
 *
 * Adds custom meta fields for team member information.
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Team Member Meta Box
 */
function ehs_team_meta_box() {
    add_meta_box(
        'ehs_team_details',
        __('Team Member Details', 'hello-elementor-child'),
        'ehs_team_meta_box_callback',
        'team',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'ehs_team_meta_box');

/**
 * Team Member Meta Box Callback
 */
function ehs_team_meta_box_callback($post) {
    wp_nonce_field('ehs_team_meta_box', 'ehs_team_meta_box_nonce');

    $job_title      = get_post_meta($post->ID, '_ehs_team_job_title', true);
    $email          = get_post_meta($post->ID, '_ehs_team_email', true);
    $phone          = get_post_meta($post->ID, '_ehs_team_phone', true);
    $linkedin       = get_post_meta($post->ID, '_ehs_team_linkedin', true);
    $certifications = get_post_meta($post->ID, '_ehs_team_certifications', true);
    $display_order  = get_post_meta($post->ID, '_ehs_team_display_order', true);
    ?>
    <style>
        .ehs-meta-row { margin-bottom: 15px; }
        .ehs-meta-row label { display: block; font-weight: 600; margin-bottom: 5px; }
        .ehs-meta-row input[type="text"],
        .ehs-meta-row input[type="email"],
        .ehs-meta-row input[type="url"],
        .ehs-meta-row input[type="number"],
        .ehs-meta-row textarea { width: 100%; max-width: 500px; }
        .ehs-meta-row textarea { min-height: 80px; }
        .ehs-meta-row .description { color: #666; font-size: 12px; margin-top: 3px; }
        .ehs-meta-columns { display: flex; gap: 30px; flex-wrap: wrap; }
        .ehs-meta-column { flex: 1; min-width: 300px; }
    </style>

    <div class="ehs-meta-columns">
        <div class="ehs-meta-column">
            <div class="ehs-meta-row">
                <label for="ehs_team_job_title"><?php esc_html_e('Job Title', 'hello-elementor-child'); ?></label>
                <input type="text" id="ehs_team_job_title" name="ehs_team_job_title" value="<?php echo esc_attr($job_title); ?>" placeholder="e.g., Senior Safety Consultant">
            </div>

            <div class="ehs-meta-row">
                <label for="ehs_team_email"><?php esc_html_e('Email Address', 'hello-elementor-child'); ?></label>
                <input type="email" id="ehs_team_email" name="ehs_team_email" value="<?php echo esc_attr($email); ?>" placeholder="name@ehsanalytical.com">
            </div>

            <div class="ehs-meta-row">
                <label for="ehs_team_phone"><?php esc_html_e('Phone Number', 'hello-elementor-child'); ?></label>
                <input type="text" id="ehs_team_phone" name="ehs_team_phone" value="<?php echo esc_attr($phone); ?>" placeholder="(619) 288-3094">
                <p class="description"><?php esc_html_e('Direct phone or extension', 'hello-elementor-child'); ?></p>
            </div>
        </div>

        <div class="ehs-meta-column">
            <div class="ehs-meta-row">
                <label for="ehs_team_linkedin"><?php esc_html_e('LinkedIn URL', 'hello-elementor-child'); ?></label>
                <input type="url" id="ehs_team_linkedin" name="ehs_team_linkedin" value="<?php echo esc_url($linkedin); ?>" placeholder="https://linkedin.com/in/username">
            </div>

            <div class="ehs-meta-row">
                <label for="ehs_team_certifications"><?php esc_html_e('Certifications', 'hello-elementor-child'); ?></label>
                <textarea id="ehs_team_certifications" name="ehs_team_certifications" placeholder="CSP, CIH, PMP (one per line or comma-separated)"><?php echo esc_textarea($certifications); ?></textarea>
                <p class="description"><?php esc_html_e('Professional certifications and designations', 'hello-elementor-child'); ?></p>
            </div>

            <div class="ehs-meta-row">
                <label for="ehs_team_display_order"><?php esc_html_e('Display Order', 'hello-elementor-child'); ?></label>
                <input type="number" id="ehs_team_display_order" name="ehs_team_display_order" value="<?php echo esc_attr($display_order); ?>" min="0" max="999" style="width: 80px;">
                <p class="description"><?php esc_html_e('Lower numbers appear first (default: 0)', 'hello-elementor-child'); ?></p>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Save Team Member Meta
 */
function ehs_save_team_meta($post_id) {
    // Security checks
    if (!isset($_POST['ehs_team_meta_box_nonce'])) {
        return;
    }
    if (!wp_verify_nonce($_POST['ehs_team_meta_box_nonce'], 'ehs_team_meta_box')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save fields
    $fields = [
        'ehs_team_job_title'      => '_ehs_team_job_title',
        'ehs_team_email'          => '_ehs_team_email',
        'ehs_team_phone'          => '_ehs_team_phone',
        'ehs_team_linkedin'       => '_ehs_team_linkedin',
        'ehs_team_certifications' => '_ehs_team_certifications',
        'ehs_team_display_order'  => '_ehs_team_display_order',
    ];

    foreach ($fields as $field_name => $meta_key) {
        if (isset($_POST[$field_name])) {
            $value = sanitize_text_field($_POST[$field_name]);

            // Special handling for URL
            if ($field_name === 'ehs_team_linkedin') {
                $value = esc_url_raw($_POST[$field_name]);
            }

            // Special handling for email
            if ($field_name === 'ehs_team_email') {
                $value = sanitize_email($_POST[$field_name]);
            }

            // Special handling for textarea
            if ($field_name === 'ehs_team_certifications') {
                $value = sanitize_textarea_field($_POST[$field_name]);
            }

            // Special handling for number
            if ($field_name === 'ehs_team_display_order') {
                $value = absint($_POST[$field_name]);
            }

            update_post_meta($post_id, $meta_key, $value);
        }
    }
}
add_action('save_post_team', 'ehs_save_team_meta');

/**
 * Add custom columns to Team admin list
 */
function ehs_team_admin_columns($columns) {
    $new_columns = [];
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'title') {
            $new_columns['job_title'] = __('Job Title', 'hello-elementor-child');
            $new_columns['email'] = __('Email', 'hello-elementor-child');
            $new_columns['display_order'] = __('Order', 'hello-elementor-child');
        }
    }
    return $new_columns;
}
add_filter('manage_team_posts_columns', 'ehs_team_admin_columns');

/**
 * Populate custom columns in Team admin list
 */
function ehs_team_admin_column_content($column, $post_id) {
    switch ($column) {
        case 'job_title':
            echo esc_html(get_post_meta($post_id, '_ehs_team_job_title', true) ?: '—');
            break;
        case 'email':
            $email = get_post_meta($post_id, '_ehs_team_email', true);
            echo $email ? '<a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a>' : '—';
            break;
        case 'display_order':
            echo esc_html(get_post_meta($post_id, '_ehs_team_display_order', true) ?: '0');
            break;
    }
}
add_action('manage_team_posts_custom_column', 'ehs_team_admin_column_content', 10, 2);

/**
 * Make display_order column sortable
 */
function ehs_team_sortable_columns($columns) {
    $columns['display_order'] = 'display_order';
    return $columns;
}
add_filter('manage_edit-team_sortable_columns', 'ehs_team_sortable_columns');

/**
 * Handle sorting by display_order
 */
function ehs_team_orderby_columns($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    if ($query->get('post_type') !== 'team') {
        return;
    }

    if ($query->get('orderby') === 'display_order') {
        $query->set('meta_key', '_ehs_team_display_order');
        $query->set('orderby', 'meta_value_num');
    }
}
add_action('pre_get_posts', 'ehs_team_orderby_columns');

/**
 * Helper function to get all team members
 *
 * @param array $args Optional query arguments
 * @return array Array of team member data
 */
function ehs_get_team_members($args = []) {
    $defaults = [
        'post_type'      => 'team',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'meta_key'       => '_ehs_team_display_order',
        'orderby'        => 'meta_value_num',
        'order'          => 'ASC',
    ];

    $query_args = wp_parse_args($args, $defaults);
    $query = new WP_Query($query_args);
    $members = [];

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();

            $members[] = [
                'id'             => $post_id,
                'name'           => get_the_title(),
                'bio'            => get_the_content(),
                'excerpt'        => get_the_excerpt(),
                'photo'          => get_the_post_thumbnail_url($post_id, 'large'),
                'job_title'      => get_post_meta($post_id, '_ehs_team_job_title', true),
                'email'          => get_post_meta($post_id, '_ehs_team_email', true),
                'phone'          => get_post_meta($post_id, '_ehs_team_phone', true),
                'linkedin'       => get_post_meta($post_id, '_ehs_team_linkedin', true),
                'certifications' => get_post_meta($post_id, '_ehs_team_certifications', true),
                'url'            => get_permalink($post_id),
            ];
        }
        wp_reset_postdata();
    }

    return $members;
}
