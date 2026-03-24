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
    // Get all credentials, then sort by canonical order (same as footer)
    $credentials = get_posts(array(
        'post_type' => 'credentials',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'title',
        'order' => 'ASC',
    ));
    $canonical_order = ehs_get_canonical_credential_order();
    $order_default   = 999999;
    usort($credentials, function ($a, $b) use ($canonical_order, $order_default) {
        $acronym_a = get_post_meta($a->ID, 'credential_acronym', true);
        $acronym_a = trim((string) $acronym_a);
        $title_a   = get_the_title($a);
        if ($acronym_a === '' && (stripos($title_a, 'Utility Safety') !== false || stripos($title_a, 'USOLN') !== false)) {
            $acronym_a = 'USOLN';
        }
        $acronym_b = get_post_meta($b->ID, 'credential_acronym', true);
        $acronym_b = trim((string) $acronym_b);
        $title_b   = get_the_title($b);
        if ($acronym_b === '' && (stripos($title_b, 'Utility Safety') !== false || stripos($title_b, 'USOLN') !== false)) {
            $acronym_b = 'USOLN';
        }
        $pos_a = array_search($acronym_a, $canonical_order, true);
        $pos_b = array_search($acronym_b, $canonical_order, true);
        $idx_a = $pos_a !== false ? $pos_a : $order_default;
        $idx_b = $pos_b !== false ? $pos_b : $order_default;
        if ($idx_a !== $idx_b) {
            return $idx_a - $idx_b;
        }
        return strcasecmp($title_a, $title_b);
    });

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
            
            // Get featured image from the Credential post type
            $featured_image_id = get_post_thumbnail_id($credential->ID);
            $image_url = '';
            $image_alt = $title;
            
            if ($featured_image_id) {
                $image_url = wp_get_attachment_image_url($featured_image_id, 'medium');
                if (!$image_url) {
                    $image_url = wp_get_attachment_image_url($featured_image_id, 'full');
                }
            }
            
            // Fallback to theme SVG placeholder only when no featured image is set
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
 * Get service post thumbnail URL by post slug
 *
 * Used so homepage service cards can display the featured image from the service post.
 *
 * @param string $slug Service post slug (e.g. 'environmental-health-and-safety-ehs-consulting')
 * @param string $size Image size (default 'medium_large')
 * @return string Thumbnail URL or empty string if no post or no thumbnail
 */
function ehs_get_service_thumbnail_by_slug($slug, $size = 'medium_large') {
    $slug = trim($slug, '/');
    if (empty($slug)) {
        return '';
    }
    $posts = get_posts(array(
        'post_type'      => 'services',
        'name'           => $slug,
        'post_status'    => 'publish',
        'posts_per_page' => 1,
    ));
    if (empty($posts)) {
        return '';
    }
    $url = get_the_post_thumbnail_url($posts[0]->ID, $size);
    return $url ? $url : '';
}

/**
 * Output service card media: post thumbnail if set, otherwise homepage media fallback
 *
 * Use on the homepage so service cards show the service post's featured image when set,
 * and fall back to Business Information → Homepage Media (or placeholder) when not.
 *
 * @param string $slug          Service post slug (e.g. 'federal-contracting-sdvosb')
 * @param string $fallback_key  Option key for ehs_homepage_media_image (e.g. 'federal_contracting_image')
 * @param string $fallback_alt  Alt text when using fallback image
 * @param string $title_attr    Optional title/alt for thumbnail (defaults to fallback_alt)
 */
function ehs_homepage_service_card_media($slug, $fallback_key, $fallback_alt, $title_attr = '') {
    $thumb = ehs_get_service_thumbnail_by_slug($slug, 'medium_large');
    $alt = $title_attr !== '' ? $title_attr : $fallback_alt;
    if ($thumb) {
        echo '<div class="service-card__image">';
        echo '<img src="' . esc_url($thumb) . '" alt="' . esc_attr($alt) . '" loading="lazy" />';
        echo '</div>';
    } else {
        echo '<div class="service-card__icon">';
        ehs_homepage_media_image($fallback_key, $fallback_alt, 'medium_large');
        echo '</div>';
    }
}

/**
 * Predefined content for homepage featured (large) cards. Keyed by post slug.
 * Preserves the unique component format: badge, title, excerpt, highlights, link text, media fallback key.
 * Order is controlled by menu_order in the template; this map only supplies copy.
 *
 * @return array<string, array{badge: string, title: string, excerpt: string, highlights: string[], link_text: string, media_key: string, media_alt: string, media_title: string}>
 */
function ehs_homepage_featured_card_content() {
    return array(
        'environmental-health-and-safety-ehs-consulting' => array(
            'badge'       => 'Cal/OSHA Experts',
            'title'       => 'Environmental Health & Safety Consulting',
            'excerpt'     => 'Comprehensive EHS program development, compliance audits, and safety management for manufacturing, biotech, pharmaceutical, and aerospace industries throughout California.',
            'highlights'  => array('Cal/OSHA compliance experts', 'Safety program development', 'CIH & CSP certified', 'Manufacturing & biotech focus'),
            'link_text'   => 'Learn More &rarr;',
            'media_key'   => 'ehs_consulting_image',
            'media_alt'   => 'EHS Consulting icon',
            'media_title' => 'Environmental Health & Safety Consulting',
        ),
        'federal-contracting-sdvosb' => array(
            'badge'       => 'SDVOSB Certified',
            'title'       => 'Federal Contracting Services',
            'excerpt'     => 'SDVOSB-certified environmental health and safety services for federal contractors. Help meet small business subcontracting goals on USACE, NAVFAC, and DoD projects.',
            'highlights'  => array('SDVOSB certification verified', 'Meet subcontracting goals', 'All federal agencies', 'Nationwide coverage'),
            'link_text'   => 'View Federal Contracting Services &rarr;',
            'media_key'   => 'federal_contracting_image',
            'media_alt'   => 'Federal Contracting Services icon',
            'media_title' => 'Federal Contracting Services',
        ),
        'federal-contracting' => array(
            'badge'       => 'SDVOSB Certified',
            'title'       => 'Federal Contracting Services',
            'excerpt'     => 'SDVOSB-certified environmental health and safety services for federal contractors. Help meet small business subcontracting goals on USACE, NAVFAC, and DoD projects.',
            'highlights'  => array('SDVOSB certification verified', 'Meet subcontracting goals', 'All federal agencies', 'Nationwide coverage'),
            'link_text'   => 'View Federal Contracting Services &rarr;',
            'media_key'   => 'federal_contracting_image',
            'media_alt'   => 'Federal Contracting Services icon',
            'media_title' => 'Federal Contracting Services',
        ),
        'construction-safety-consulting' => array(
            'badge'       => 'Cal/OSHA Compliant',
            'title'       => 'Construction Safety Consulting',
            'excerpt'     => 'Dedicated on-site safety professionals for construction projects. Safety program development, daily inspections, training, and Cal/OSHA compliance for California and federal projects.',
            'highlights'  => array('On-site safety officers', 'Safety program development', 'Cal/OSHA compliance', 'Federal & state projects'),
            'link_text'   => 'View Construction Safety Services &rarr;',
            'media_key'   => 'construction_safety_image',
            'media_alt'   => 'Construction Safety Consulting icon',
            'media_title' => 'Construction Safety Consulting',
        ),
        'ssho-services-california' => array(
            'badge'       => 'EM 385-1-1 Experts',
            'title'       => 'SSHO Services - Federal Military',
            'excerpt'     => 'Site Safety and Health Officers for USACE, NAVFAC, and DoD construction projects. Full EM 385-1-1 compliance and government liaison support.',
            'highlights'  => array('EM 385-1-1 compliance experts', 'SDVOSB helps meet small business goals', 'CIH & CSP certified professionals', 'California & nationwide coverage'),
            'link_text'   => 'Learn More About SSHO Services &rarr;',
            'media_key'   => 'ssho_image',
            'media_alt'   => 'SSHO Services icon',
            'media_title' => 'SSHO Services - Federal Military',
        ),
        'industrial-hygiene-san-diego' => array(
            'badge'       => 'CIH Certified',
            'title'       => 'Industrial Hygiene Services',
            'excerpt'     => 'CIH-certified industrial hygiene assessments, exposure monitoring, air quality testing, and compliance programs for industrial facilities, biotech, pharma, and manufacturing.',
            'highlights'  => array('CIH certified professionals', 'Exposure assessments', 'Air quality monitoring', 'Compliance programs'),
            'link_text'   => 'View Industrial Hygiene Services &rarr;',
            'media_key'   => 'industrial_hygiene_image',
            'media_alt'   => 'Industrial Hygiene Services icon',
            'media_title' => 'Industrial Hygiene Services',
        ),
        'caltrans-construction-safety-services' => array(
            'badge'       => 'DVBE #2017031',
            'title'       => 'Caltrans Construction Safety Services',
            'excerpt'     => 'DVBE-certified safety services for Caltrans highway and bridge construction. Safety Representatives, Lead Compliance Plans, and Work Area Monitoring.',
            'highlights'  => array('All 12 Caltrans districts served', 'DVBE #2017031 certification', 'Lead Compliance Plan experts', 'Bridge rehabilitation specialists'),
            'link_text'   => 'Learn More About Caltrans Services &rarr;',
            'media_key'   => 'caltrans_image',
            'media_alt'   => 'Caltrans Construction Safety Services icon',
            'media_title' => 'Caltrans Construction Safety Services',
        ),
    );
}

/**
 * Predefined content for homepage additional (smaller) cards. Keyed by post slug.
 *
 * @return array<string, array{title: string, excerpt: string, link_text: string, media_key: string, media_alt: string, media_title: string}>
 */
function ehs_homepage_additional_card_content() {
    return array(
        'environmental-health-and-safety-ehs-consulting' => array(
            'title'       => 'Environmental Health & Safety Consulting',
            'excerpt'     => 'Comprehensive EHS program development, compliance audits, and strategic safety management for California businesses.',
            'link_text'   => 'Learn More &rarr;',
            'media_key'   => 'ehs_consulting_image',
            'media_alt'   => 'EHS Consulting icon',
            'media_title' => 'Environmental Health & Safety Consulting',
        ),
        'mold-testing' => array(
            'title'       => 'Environmental Testing Services',
            'excerpt'     => 'Mold testing, asbestos testing, indoor air quality assessments, water damage evaluations, and fire/smoke damage assessments.',
            'link_text'   => 'Learn More &rarr;',
            'media_key'   => 'environmental_testing_image',
            'media_alt'   => 'Environmental Testing icon',
            'media_title' => 'Environmental Testing Services',
        ),
        'lead-compliance-plan-services' => array(
            'title'       => 'Lead Compliance Plan Services',
            'excerpt'     => 'Comprehensive Lead Compliance Plans for Caltrans bridge projects and construction involving lead-containing paint. Cal/OSHA 1532.1 compliant, CIH oversight.',
            'link_text'   => 'Learn More About Lead Compliance &rarr;',
            'media_key'   => 'lead_compliance_image',
            'media_alt'   => 'Lead Compliance Plan Services icon',
            'media_title' => 'Lead Compliance Plan Services',
        ),
    );
}

/**
 * Render one homepage featured (large) card. Uses predefined content for this slug so format is preserved.
 * Order is determined by the template (menu_order); this only outputs the card HTML.
 *
 * @param WP_Post $post Service post (used for permalink and slug lookup)
 */
function ehs_render_homepage_featured_card_ordered($post) {
    if (!$post || !is_a($post, 'WP_Post')) {
        return;
    }
    $slug = $post->post_name;
    $permalink = get_permalink($post->ID);
    $content_map = ehs_homepage_featured_card_content();
    $c = isset($content_map[$slug]) ? $content_map[$slug] : null;
    if (!$c) {
        $c = array(
            'badge'       => get_post_meta($post->ID, 'service_section', true) ?: '',
            'title'       => $post->post_title,
            'excerpt'     => get_post_meta($post->ID, 'service_short_description', true) ?: wp_trim_words(get_the_excerpt($post), 25),
            'highlights'  => array(),
            'link_text'   => __('Learn More', 'hello-elementor-child') . ' &rarr;',
            'media_key'   => 'ehs_consulting_image',
            'media_alt'   => $post->post_title . ' icon',
            'media_title' => $post->post_title,
        );
    }
    ?>
    <a href="<?php echo esc_url($permalink); ?>" class="service-card service-card--featured">
        <?php ehs_homepage_service_card_media($slug, $c['media_key'], $c['media_alt'], $c['media_title']); ?>
        <div class="service-card__content">
            <?php if (!empty($c['badge'])) : ?>
                <div class="service-card__badge"><?php echo esc_html($c['badge']); ?></div>
            <?php endif; ?>
            <h3 class="service-card__title"><?php echo esc_html($c['title']); ?></h3>
            <p class="service-card__excerpt"><?php echo esc_html($c['excerpt']); ?></p>
            <ul class="service-card__highlights">
                <?php foreach (isset($c['highlights']) ? $c['highlights'] : array() as $h) : ?>
                    <li><?php echo esc_html($h); ?></li>
                <?php endforeach; ?>
            </ul>
            <span class="service-card__link"><?php echo esc_html($c['link_text']); ?></span>
        </div>
    </a>
    <?php
}

/**
 * Render one homepage additional (smaller) card. Uses predefined content for this slug so format is preserved.
 *
 * @param WP_Post $post Service post (used for permalink and slug lookup)
 */
function ehs_render_homepage_additional_card_ordered($post) {
    if (!$post || !is_a($post, 'WP_Post')) {
        return;
    }
    $slug = $post->post_name;
    $permalink = get_permalink($post->ID);
    $content_map = ehs_homepage_additional_card_content();
    $c = isset($content_map[$slug]) ? $content_map[$slug] : null;
    if (!$c) {
        $c = array(
            'title'       => $post->post_title,
            'excerpt'     => get_post_meta($post->ID, 'service_short_description', true) ?: wp_trim_words(get_the_excerpt($post), 20),
            'link_text'   => __('Learn More', 'hello-elementor-child') . ' &rarr;',
            'media_key'   => 'ehs_consulting_image',
            'media_alt'   => $post->post_title . ' icon',
            'media_title' => $post->post_title,
        );
    }
    ?>
    <a href="<?php echo esc_url($permalink); ?>" class="service-card">
        <?php ehs_homepage_service_card_media($slug, $c['media_key'], $c['media_alt'], $c['media_title']); ?>
        <div class="service-card__content">
            <h3 class="service-card__title"><?php echo esc_html($c['title']); ?></h3>
            <p class="service-card__excerpt"><?php echo esc_html($c['excerpt']); ?></p>
            <span class="service-card__link"><?php echo esc_html($c['link_text']); ?></span>
        </div>
    </a>
    <?php
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
