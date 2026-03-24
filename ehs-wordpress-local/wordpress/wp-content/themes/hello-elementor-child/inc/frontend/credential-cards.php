<?php
/**
 * Credential Cards Rendering Functions
 *
 * Functions for rendering credential cards following the design system
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render Credential Card
 *
 * Outputs HTML for a single credential card following the design system
 *
 * @param int|WP_Post $credential Post ID or WP_Post object
 * @return void Outputs HTML directly
 */
function ehs_render_credential_card($credential) {
    if (is_numeric($credential)) {
        $credential = get_post($credential);
    }
    
    if (!$credential || $credential->post_type !== 'credentials') {
        return;
    }
    
    $post_id = $credential->ID;
    $title = get_the_title($post_id);
    $permalink = get_permalink($post_id);
    $excerpt = get_the_excerpt($post_id);
    $featured_image = get_the_post_thumbnail($post_id, 'medium', array('class' => 'credential-card__image-img'));
    
    // Get meta fields
    $acronym = get_post_meta($post_id, 'credential_acronym', true);
    $issuing_org = get_post_meta($post_id, 'credential_issuing_organization', true);
    $category = get_post_meta($post_id, 'credential_category', true);
    
    // Map category to CSS class
    $category_class = '';
    if ($category) {
        $category_lower = strtolower($category);
        if (strpos($category_lower, 'professional') !== false) {
            $category_class = 'credential-card__category--professional';
        } elseif (strpos($category_lower, 'business') !== false) {
            $category_class = 'credential-card__category--business';
        } elseif (strpos($category_lower, 'license') !== false) {
            $category_class = 'credential-card__category--license';
        } elseif (strpos($category_lower, 'affiliation') !== false) {
            $category_class = 'credential-card__category--affiliation';
        }
    }
    
    ?>
    <article class="credential-card">
        <?php if ($featured_image) : ?>
            <div class="credential-card__image">
                <?php echo $featured_image; ?>
            </div>
        <?php else : ?>
            <div class="credential-card__image">
                <div style="color: #999; font-size: 0.875rem; text-align: center; padding: 20px;">
                    <?php echo esc_html($acronym ? $acronym : 'Logo'); ?>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="credential-card__content">
            <?php if ($acronym) : ?>
                <span class="credential-card__acronym"><?php echo esc_html($acronym); ?></span>
            <?php endif; ?>
            
            <h3 class="credential-card__title">
                <?php echo esc_html($title); ?>
            </h3>
            
            <?php if ($issuing_org) : ?>
                <p class="credential-card__issuing-org"><?php echo esc_html($issuing_org); ?></p>
            <?php endif; ?>
            
            <?php if ($category && $category_class) : ?>
                <span class="credential-card__category <?php echo esc_attr($category_class); ?>">
                    <?php echo esc_html($category); ?>
                </span>
            <?php endif; ?>
            
            <?php if ($excerpt) : ?>
                <p class="credential-card__excerpt"><?php echo esc_html($excerpt); ?></p>
            <?php endif; ?>
            
            <a href="<?php echo esc_url($permalink); ?>" class="credential-card__link">
                Learn More
            </a>
        </div>
    </article>
    <?php
}

/**
 * Normalize credential card input to array shape (title, description, image, link).
 *
 * @param array|WP_Post $card Card data array or credential post.
 * @return array|null Card array or null if invalid.
 */
function ehs_credential_card_simple_normalize($card) {
    if (is_a($card, 'WP_Post')) {
        if ($card->post_type !== 'credentials') {
            return null;
        }
        return [
            'title'       => get_the_title($card),
            'description' => get_the_excerpt($card) ?: wp_trim_words(get_the_content(null, false, $card), 30),
            'image'       => get_the_post_thumbnail_url($card, 'medium'),
            'link'        => get_permalink($card),
        ];
    }
    if (is_array($card) && isset($card['title'])) {
        return [
            'title'       => isset($card['title']) ? $card['title'] : '',
            'description' => isset($card['description']) ? $card['description'] : '',
            'image'       => isset($card['image']) ? $card['image'] : '',
            'link'        => isset($card['link']) ? $card['link'] : '',
        ];
    }
    return null;
}

/**
 * Render simple credential card (footer and other compact contexts).
 *
 * Outputs HTML for a single credential with optional image, title, description.
 * Accepts card array (title, description, image, link) or WP_Post.
 *
 * @param array|WP_Post $card Card data or credential post.
 * @param array         $args Optional. e.g. show_description (default true).
 * @return void Outputs HTML directly
 */
function ehs_render_credential_card_simple($card, $args = []) {
    $data = ehs_credential_card_simple_normalize($card);
    if ($data === null) {
        return;
    }

    $args = wp_parse_args($args, [
        'show_description' => true,
    ]);

    $title = $data['title'];
    $description = $data['description'];
    $image = $data['image'];
    $link = $data['link'];
    ?>
    <article class="ehs-credential-card-simple">
        <?php if (!empty($image)) : ?>
            <div class="ehs-credential-card-simple__image">
                <?php if (!empty($link)) : ?>
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy" />
                    </a>
                <?php else : ?>
                    <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy" />
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="ehs-credential-card-simple__content">
            <h3 class="ehs-credential-card-simple__title">
                <?php if (!empty($link)) : ?>
                    <a href="<?php echo esc_url($link); ?>" class="ehs-credential-card-simple__link"><?php echo esc_html($title); ?></a>
                <?php else : ?>
                    <?php echo esc_html($title); ?>
                <?php endif; ?>
            </h3>
            <?php if ($args['show_description'] && !empty($description)) : ?>
                <p class="ehs-credential-card-simple__description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </div>
    </article>
    <?php
}

/**
 * Render Credentials Grid
 *
 * Outputs a grid of credential cards
 *
 * @param array $args Query arguments for WP_Query
 * @return void Outputs HTML directly
 */
function ehs_render_credentials_grid($args = array()) {
    $defaults = array(
        'post_type' => 'credentials',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'meta_value_num',
        'meta_key' => 'credential_order',
        'order' => 'ASC',
    );
    
    $query_args = wp_parse_args($args, $defaults);
    $credentials_query = new WP_Query($query_args);
    
    if (!$credentials_query->have_posts()) {
        return;
    }
    
    ?>
    <div class="credentials-grid">
        <?php
        while ($credentials_query->have_posts()) {
            $credentials_query->the_post();
            ehs_render_credential_card(get_post());
        }
        wp_reset_postdata();
        ?>
    </div>
    <?php
}

/**
 * Get Featured Credentials
 *
 * Returns an array of featured credential posts
 *
 * @param int $number Number of credentials to return
 * @return array Array of credential post objects
 */
function ehs_get_featured_credentials($number = 6) {
    $args = array(
        'post_type' => 'credentials',
        'posts_per_page' => $number,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'credential_featured',
                'value' => '1',
                'compare' => '='
            )
        ),
        'orderby' => 'meta_value_num',
        'meta_key' => 'credential_order',
        'order' => 'ASC',
    );
    
    $featured_query = new WP_Query($args);
    
    // If we don't have enough featured credentials, get the most recent ones
    if ($featured_query->post_count < $number) {
        $args = array(
            'post_type' => 'credentials',
            'posts_per_page' => $number,
            'post_status' => 'publish',
            'orderby' => 'meta_value_num',
            'meta_key' => 'credential_order',
            'order' => 'ASC',
        );
        $featured_query = new WP_Query($args);
    }
    
    $credentials = array();
    
    if ($featured_query->have_posts()) {
        while ($featured_query->have_posts()) {
            $featured_query->the_post();
            $credentials[] = get_post();
        }
        wp_reset_postdata();
    }
    
    return $credentials;
}

/**
 * Credentials Grid Shortcode
 *
 * Usage: [credentials_grid posts_per_page="9" featured="true"]
 *
 * @param array $atts Shortcode attributes
 * @return string HTML output
 */
function ehs_credentials_grid_shortcode($atts) {
    $atts = shortcode_atts(array(
        'posts_per_page' => -1,
        'featured' => false,
        'category' => '',
        'orderby' => 'meta_value_num',
        'meta_key' => 'credential_order',
        'order' => 'ASC',
    ), $atts);
    
    $args = array(
        'post_type' => 'credentials',
        'posts_per_page' => intval($atts['posts_per_page']),
        'post_status' => 'publish',
        'orderby' => $atts['orderby'],
        'order' => $atts['order'],
    );
    
    // Add meta key if using meta_value_num
    if ($atts['orderby'] === 'meta_value_num') {
        $args['meta_key'] = $atts['meta_key'];
    }
    
    // Filter by featured
    if ($atts['featured'] === 'true' || $atts['featured'] === '1') {
        $args['meta_query'] = array(
            array(
                'key' => 'credential_featured',
                'value' => '1',
                'compare' => '='
            )
        );
    }
    
    // Filter by category
    if (!empty($atts['category'])) {
        if (!isset($args['meta_query'])) {
            $args['meta_query'] = array();
        }
        $args['meta_query'][] = array(
            'key' => 'credential_category',
            'value' => sanitize_text_field($atts['category']),
            'compare' => '='
        );
        $args['meta_query']['relation'] = 'AND';
    }
    
    ob_start();
    ehs_render_credentials_grid($args);
    return ob_get_clean();
}
add_shortcode('credentials_grid', 'ehs_credentials_grid_shortcode');
