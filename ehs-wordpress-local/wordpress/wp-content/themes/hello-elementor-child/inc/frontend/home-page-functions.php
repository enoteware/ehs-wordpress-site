<?php
/**
 * Home Page Helper Functions
 *
 * Functions for rendering home page sections and querying content
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get Fallback Service Icon
 *
 * Returns SVG icon URL based on service slug or title keywords
 * Uses the SVG icons created in assets/images/icons/
 *
 * @param string $post_slug The post slug to match
 * @return string URL to the fallback SVG icon
 */
function ehs_get_fallback_service_icon($post_slug) {
    $base_url = get_stylesheet_directory_uri() . '/assets/images/icons/';

    // Map service slugs/keywords to icon files
    $icon_map = array(
        // Air quality related
        'air-quality' => 'air-quality-icon.svg',
        'indoor-air' => 'air-quality-icon.svg',
        'mold' => 'air-quality-icon.svg',
        'fume-hood' => 'air-quality-icon.svg',

        // Asbestos related
        'asbestos' => 'asbestos-icon.svg',

        // Construction related
        'construction' => 'construction-icon.svg',
        'ssho' => 'construction-icon.svg',
        'caltrans' => 'construction-icon.svg',
        'safety' => 'construction-icon.svg',

        // Lead related
        'lead' => 'lead-icon.svg',

        // Federal/consulting
        'federal' => 'federal-icon.svg',
        'contracting' => 'federal-icon.svg',
        'sdvosb' => 'federal-icon.svg',

        // General consulting
        'ehs' => 'consulting-icon.svg',
        'consulting' => 'consulting-icon.svg',
        'industrial-hygiene' => 'consulting-icon.svg',
        'ergonomic' => 'consulting-icon.svg',
        'outsourcing' => 'consulting-icon.svg',
    );

    // Check for keyword matches in the slug
    foreach ($icon_map as $keyword => $icon_file) {
        if (strpos($post_slug, $keyword) !== false) {
            return $base_url . $icon_file;
        }
    }

    // Default fallback icon
    return $base_url . 'consulting-icon.svg';
}

/**
 * Get Featured Services for Homepage
 *
 * Returns an array of 6 featured services with their details
 * Services can be marked as featured via the 'service_featured' custom field
 * Falls back to most recent services if no featured services are set
 *
 * @return array Array of service data with title, excerpt, permalink, and icon
 */
function ehs_get_featured_services() {
    // Try to get services marked as featured first
    $args = array(
        'post_type' => 'services',
        'posts_per_page' => 6,
        'orderby' => 'menu_order',
        'order' => 'ASC',
        'meta_query' => array(
            array(
                'key' => 'service_featured',
                'value' => '1',
                'compare' => '='
            )
        )
    );

    $featured_query = new WP_Query($args);

    // If we don't have 6 featured services, get the most recent ones
    if ($featured_query->post_count < 6) {
        $args = array(
            'post_type' => 'services',
            'posts_per_page' => 6,
            'orderby' => 'menu_order',
            'order' => 'ASC',
        );
        $featured_query = new WP_Query($args);
    }

    $services = array();

    if ($featured_query->have_posts()) {
        while ($featured_query->have_posts()) {
            $featured_query->the_post();

            // Get excerpt first (SEO-optimized), fallback to short description
            $excerpt = get_post_field('post_excerpt', get_the_ID());
            if (empty($excerpt) || strlen(trim($excerpt)) < 20) {
                $service_short_description = get_post_meta(get_the_ID(), 'service_short_description', true);
                $excerpt = $service_short_description ? $service_short_description : '';
            }
            
            // Clean excerpt - remove ellipses and HTML entities
            if (!empty($excerpt)) {
                $excerpt = html_entity_decode($excerpt, ENT_QUOTES, 'UTF-8');
                $excerpt = rtrim($excerpt, '.…');
                $excerpt = preg_replace('/\.{2,}/', '.', $excerpt);
                $excerpt = trim($excerpt);
            }

            // Get featured image (post thumbnail) first - try large size first
            $icon_url = get_the_post_thumbnail_url(get_the_ID(), 'large');
            if (!$icon_url) {
                $icon_url = get_the_post_thumbnail_url(get_the_ID(), 'medium');
            }

            // If no featured image, try service_icon custom field
            if (!$icon_url) {
                $service_icon = get_post_meta(get_the_ID(), 'service_icon', true);
                if ($service_icon) {
                    $icon_url = wp_get_attachment_url($service_icon);
                }
            }

            // Final fallback to SVG icons if no custom icon is set
            if (!$icon_url) {
                $post_slug = get_post_field('post_name', get_the_ID());
                $icon_url = ehs_get_fallback_service_icon($post_slug);
            }

            $services[] = array(
                'title' => get_the_title(),
                'excerpt' => !empty($excerpt) ? wp_trim_words($excerpt, 20) : '',
                'permalink' => get_permalink(),
                'icon' => $icon_url,
            );
        }
        wp_reset_postdata();
    }

    return $services;
}

/**
 * Render Certification Badges
 *
 * Outputs HTML for certification badge grid
 * Pulls from credentials post type and uses featured images (PNGs/SVGs)
 * Falls back to SVG placeholders if no featured image is set
 *
 * @return void Outputs HTML directly
 */
function ehs_render_certification_badges() {
    // Get all credentials, ordered by display order
    $credentials = get_posts(array(
        'post_type' => 'credentials',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'meta_value_num',
        'meta_key' => 'credential_order',
        'order' => 'ASC',
    ));

    // Fallback badge directory for SVG placeholders
    $badge_dir = get_stylesheet_directory_uri() . '/assets/images/badges/';
    
    // Map of acronyms to fallback SVG filenames
    $fallback_images = array(
        'CIH' => 'cih-badge.svg',
        'CSP' => 'csp-badge.svg',
        'CHST' => 'chst-badge.svg',
        'PMP' => 'pmp-badge.svg',
        'SDVOSB' => 'sdvosb-badge.svg',
        'DVBE' => 'dvbe-badge.svg',
        'CUSP' => 'cusp-badge.svg',
        'IOSH' => 'iosh-badge.svg',
        'CAC' => 'cac-badge.svg',
    );

    echo '<div class="badge-grid">';
    
    if (!empty($credentials)) {
        foreach ($credentials as $credential) {
            $acronym = get_post_meta($credential->ID, 'credential_acronym', true);
            $title = get_the_title($credential->ID);
            
            // Get featured image (the PNG/SVG we assigned)
            $featured_image_id = get_post_thumbnail_id($credential->ID);
            $image_url = '';
            $image_alt = $title;
            
            if ($featured_image_id) {
                // Get the image URL - prefer medium size for badges
                $image_url = wp_get_attachment_image_url($featured_image_id, 'medium');
                if (!$image_url) {
                    $image_url = wp_get_attachment_image_url($featured_image_id, 'full');
                }
            }
            
            // Fallback to SVG placeholder if no featured image
            if (!$image_url && $acronym && isset($fallback_images[$acronym])) {
                $image_url = $badge_dir . $fallback_images[$acronym];
            }
            
            // Skip if still no image
            if (!$image_url) {
                continue;
            }
            
            echo '<div class="badge-item">';
            echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($image_alt) . '" title="' . esc_attr($title) . '" loading="lazy">';
            echo '</div>';
        }
    } else {
        // Fallback: use hardcoded badges if no credentials found
        $hardcoded_badges = array(
            array('name' => 'CIH', 'full_name' => 'Certified Industrial Hygienist', 'image' => 'cih-badge.svg'),
            array('name' => 'CSP', 'full_name' => 'Certified Safety Professional', 'image' => 'csp-badge.svg'),
            array('name' => 'CHST', 'full_name' => 'Construction Health and Safety Technician', 'image' => 'chst-badge.svg'),
            array('name' => 'PMP', 'full_name' => 'Project Management Professional', 'image' => 'pmp-badge.svg'),
            array('name' => 'SDVOSB', 'full_name' => 'Service-Disabled Veteran-Owned Small Business', 'image' => 'sdvosb-badge.svg'),
            array('name' => 'DVBE', 'full_name' => 'Disabled Veteran Business Enterprise', 'image' => 'dvbe-badge.svg'),
            array('name' => 'CUSP', 'full_name' => 'Certified Utility Safety Professional', 'image' => 'cusp-badge.svg'),
            array('name' => 'IOSH', 'full_name' => 'Institution of Occupational Safety and Health', 'image' => 'iosh-badge.svg'),
        );
        
        foreach ($hardcoded_badges as $badge) {
            $badge_path = $badge_dir . $badge['image'];
            echo '<div class="badge-item">';
            echo '<img src="' . esc_url($badge_path) . '" alt="' . esc_attr($badge['full_name']) . '" title="' . esc_attr($badge['full_name']) . '">';
            echo '</div>';
        }
    }
    
    echo '</div>';
}

/**
 * Get Latest Blog Posts
 *
 * Returns an array of the 3 most recent blog posts
 * Excludes services post type
 *
 * @param int $count Number of posts to retrieve (default 3)
 * @return array Array of post data with title, excerpt, permalink, and thumbnail
 */
function ehs_get_latest_posts($count = 3) {
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => $count,
        'orderby' => 'date',
        'order' => 'DESC',
        'post_status' => 'publish'
    );

    $posts_query = new WP_Query($args);
    $posts = array();

    if ($posts_query->have_posts()) {
        while ($posts_query->have_posts()) {
            $posts_query->the_post();

            // Get featured image (post thumbnail) - try large size first, fallback to medium
            $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'large');
            if (!$thumbnail) {
                $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'medium');
            }
            // Fallback to a placeholder if no featured image is set
            if (!$thumbnail) {
                $thumbnail = get_stylesheet_directory_uri() . '/assets/images/placeholder-blog.svg';
            }

            $posts[] = array(
                'title' => get_the_title(),
                'excerpt' => wp_trim_words(get_the_excerpt(), 20),
                'permalink' => get_permalink(),
                'thumbnail' => $thumbnail,
                'date' => get_the_date(),
            );
        }
        wp_reset_postdata();
    }

    return $posts;
}

/**
 * Render Homepage Service Card (Array Version)
 *
 * Outputs HTML for a single service card using array data
 * Note: Different from ehs_render_service_card() which uses WP_Post objects
 *
 * @param array $service Service data array with keys: title, excerpt, permalink, icon
 * @return void Outputs HTML directly
 */
function ehs_homepage_render_service_card($service) {
    // Ensure we always have an icon (fallback to default if needed)
    if (empty($service['icon'])) {
        $service['icon'] = get_stylesheet_directory_uri() . '/assets/images/icons/consulting-icon.svg';
    }
    ?>
    <a href="<?php echo esc_url($service['permalink']); ?>" class="service-card">
        <div class="service-card__icon">
            <img src="<?php echo esc_url($service['icon']); ?>"
                 alt="<?php echo esc_attr($service['title']); ?> icon"
                 loading="lazy">
        </div>
        <div class="service-card__content">
            <h3 class="service-card__title"><?php echo esc_html($service['title']); ?></h3>
            <p class="service-card__excerpt"><?php echo esc_html($service['excerpt']); ?></p>
            <span class="service-card__link">Learn More &rarr;</span>
        </div>
    </a>
    <?php
}

/**
 * Render Homepage Article Card (Array Version)
 *
 * Outputs HTML for a single article/blog post card using array data
 * Note: Different from ehs_render_article_card() which uses WP_Post objects
 *
 * @param array $post Post data array with keys: title, excerpt, permalink, thumbnail, date
 * @return void Outputs HTML directly
 */
function ehs_homepage_render_article_card($post) {
    ?>
    <div class="article-card">
        <div class="article-card__image">
            <img src="<?php echo esc_url($post['thumbnail']); ?>"
                 alt="Featured image for <?php echo esc_attr($post['title']); ?>"
                 loading="lazy">
        </div>
        <div class="article-card__content">
            <span class="article-card__date"><?php echo esc_html($post['date']); ?></span>
            <h3 class="article-card__title"><?php echo esc_html($post['title']); ?></h3>
            <p class="article-card__excerpt"><?php echo esc_html($post['excerpt']); ?></p>
            <a href="<?php echo esc_url($post['permalink']); ?>" class="article-card__link">Read More &rarr;</a>
        </div>
    </div>
    <?php
}
