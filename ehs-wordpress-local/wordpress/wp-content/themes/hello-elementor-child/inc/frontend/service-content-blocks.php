<?php
/**
 * Service Content Block Functions
 * 
 * Reusable functions for common service page sections
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Output service hero section
 * 
 * @param string $title Hero title
 * @param string $subtitle Hero subtitle
 * @param string $text Hero text content
 * @param string $image_url Background image URL
 */
function ehs_service_hero($title = '', $subtitle = '', $text = '', $image_url = '') {
    if (empty($title)) {
        $title = get_the_title();
    }
    if (empty($image_url)) {
        $image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
    }
    ?>
    <section class="service-hero" style="background-image: url('<?php echo esc_url($image_url); ?>');">
        <div class="service-hero-content">
            <h1><?php echo esc_html($title); ?></h1>
            <?php if ($subtitle) : ?>
                <div class="service-hero-subtitle"><?php echo esc_html($subtitle); ?></div>
            <?php endif; ?>
            <?php if ($text) : ?>
                <div class="service-hero-text"><?php echo wp_kses_post($text); ?></div>
            <?php endif; ?>
        </div>
    </section>
    <?php
}

/**
 * Output three-column "Why Choose Us" section
 * 
 * @param array $columns Array of column data with keys: icon, title, text
 * @param string $section_title Optional section title
 */
function ehs_service_why_choose_us($columns = array(), $section_title = 'Why Choose Us') {
    if (empty($columns)) {
        return;
    }
    ?>
    <div class="service-section">
        <?php if ($section_title) : ?>
            <h2><?php echo esc_html($section_title); ?></h2>
        <?php endif; ?>
        <div class="service-section-3col">
            <?php foreach ($columns as $column) : ?>
                <div class="service-col">
                    <?php if (!empty($column['icon'])) : ?>
                        <div class="service-col-icon">
                            <?php if (filter_var($column['icon'], FILTER_VALIDATE_URL)) : ?>
                                <img src="<?php echo esc_url($column['icon']); ?>" alt="<?php echo esc_attr($column['title']); ?>">
                            <?php else : ?>
                                <?php echo wp_kses_post($column['icon']); ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <h3><?php echo esc_html($column['title']); ?></h3>
                    <p><?php echo wp_kses_post($column['text']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

/**
 * Output service offerings list/grid
 * 
 * @param array $services Array of service data with keys: title, description
 * @param string $section_title Optional section title
 */
function ehs_service_list($services = array(), $section_title = 'Our Services') {
    if (empty($services)) {
        return;
    }
    ?>
    <div class="service-section">
        <?php if ($section_title) : ?>
            <h2><?php echo esc_html($section_title); ?></h2>
        <?php endif; ?>
        <div class="service-list">
            <?php foreach ($services as $service) : ?>
                <div class="service-list-item">
                    <h4><?php echo esc_html($service['title']); ?></h4>
                    <p><?php echo wp_kses_post($service['description']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

/**
 * Output FAQ accordion section
 * 
 * @param array $faqs Array of FAQ data with keys: question, answer
 * @param string $section_title Optional section title
 */
function ehs_service_faq($faqs = array(), $section_title = 'Frequently Asked Questions') {
    if (empty($faqs)) {
        return;
    }
    ?>
    <div class="service-section service-faq">
        <?php if ($section_title) : ?>
            <h2><?php echo esc_html($section_title); ?></h2>
        <?php endif; ?>
        <?php foreach ($faqs as $index => $faq) : ?>
            <div class="service-faq-item">
                <button class="service-faq-question" data-faq-id="faq-<?php echo esc_attr($index); ?>">
                    <?php echo esc_html($faq['question']); ?>
                </button>
                <div class="service-faq-answer" id="faq-<?php echo esc_attr($index); ?>">
                    <p><?php echo wp_kses_post($faq['answer']); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const faqQuestions = document.querySelectorAll('.service-faq-question');
        
        faqQuestions.forEach(function(question) {
            question.addEventListener('click', function() {
                const faqId = this.getAttribute('data-faq-id');
                const answer = document.getElementById(faqId);
                
                // Toggle active class
                this.classList.toggle('active');
                answer.classList.toggle('active');
            });
        });
    });
    </script>
    <?php
}

/**
 * Output unified call-to-action section (consistent across all pages)
 * 
 * This function provides a consistent CTA that matches across all pages.
 * Use this instead of custom CTA implementations for consistency.
 * 
 * @param string $title Optional custom title (defaults to standard CTA title)
 * @param string $text Optional custom text (defaults to standard CTA text)
 * @param bool $show_phone_button Whether to show phone button (default: true)
 */
function ehs_unified_cta($title = '', $text = '', $show_phone_button = true) {
    // Default unified CTA content
    if (empty($title)) {
        $title = 'Ready to Work with California\'s Leading EHS Firm?';
    }
    if (empty($text)) {
        $text = 'Contact us today to discuss your project needs with our certified EHS professionals.';
    }
    
    $contact_url = home_url('/contact/');
    $phone_number = ehs_get_option('phone');
    $phone_link = ehs_get_phone(true);
    ?>
    <section class="service-cta">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <h2><?php echo esc_html($title); ?></h2>
            <p><?php echo wp_kses_post($text); ?></p>
            <div class="service-cta-buttons">
                <a href="<?php echo esc_url($contact_url); ?>" class="ehs-btn ehs-btn-solid-secondary ehs-btn-lg">Contact Us Today</a>
                <?php if ($show_phone_button && $phone_number) : ?>
                    <a href="tel:<?php echo esc_attr($phone_link); ?>" class="ehs-btn ehs-btn-outline-white ehs-btn-lg">
                        <span style="font-size: 1.2rem; font-weight: 700;"><?php echo esc_html($phone_number); ?></span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php
}

/**
 * Output call-to-action section (legacy function - use ehs_unified_cta instead)
 * 
 * @deprecated Use ehs_unified_cta() for consistency across all pages
 * @param string $title CTA title
 * @param string $text CTA text
 * @param string $button_text Button text
 * @param string $button_url Button URL
 */
function ehs_service_cta($title = '', $text = '', $button_text = 'Get a Free Quote', $button_url = '') {
    // For backward compatibility, call unified CTA with defaults
    if (empty($title)) {
        $title = 'Ready to Get Started?';
    }
    if (empty($text)) {
        $text = 'Contact us today for a free consultation and quote. Our team of certified professionals is ready to help with your project.';
    }
    if (empty($button_url)) {
        $button_url = home_url('/contact/');
    }
    
    // Use unified CTA but allow custom button text
    ?>
    <section class="service-cta">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <h2><?php echo esc_html($title); ?></h2>
            <p><?php echo wp_kses_post($text); ?></p>
            <div class="service-cta-buttons">
                <a href="<?php echo esc_url($button_url); ?>" class="ehs-btn ehs-btn-solid-secondary ehs-btn-lg"><?php echo esc_html($button_text); ?></a>
                <?php
                $phone_number = ehs_get_option('phone');
                $phone_link = ehs_get_phone(true);
                if ($phone_number) :
                ?>
                    <a href="tel:<?php echo esc_attr($phone_link); ?>" class="ehs-btn ehs-btn-outline-white ehs-btn-lg">
                        <span style="font-size: 1.2rem; font-weight: 700;"><?php echo esc_html($phone_number); ?></span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php
}

/**
 * Get SVG icon for service meta cards
 * 
 * @param string $type Icon type: 'category', 'area', 'certifications'
 * @return string SVG markup
 */
function ehs_service_meta_icon($type = '') {
    $icons = array(
        'category' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>',
        'area' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>',
        'certifications' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>',
    );
    
    if (isset($icons[$type])) {
        return $icons[$type];
    }
    
    return '';
}

/**
 * Output service meta cards (Category, Area, Certifications)
 * Displays as horizontal rectangular cards
 * 
 * @param int $post_id Current post ID (optional, defaults to current post)
 */
function ehs_service_meta_cards($post_id = 0) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $service_category_terms = get_the_terms($post_id, 'service_category');
    $service_area_terms = get_the_terms($post_id, 'service_area');
    $service_certification_terms = get_the_terms($post_id, 'service_certification');

    $service_category = (!is_wp_error($service_category_terms) && !empty($service_category_terms)) ? implode(', ', wp_list_pluck($service_category_terms, 'name')) : '';
    $service_area = (!is_wp_error($service_area_terms) && !empty($service_area_terms)) ? implode(', ', wp_list_pluck($service_area_terms, 'name')) : '';
    $service_certifications = (!is_wp_error($service_certification_terms) && !empty($service_certification_terms)) ? implode(', ', wp_list_pluck($service_certification_terms, 'name')) : '';

    if (!$service_category && !$service_area && !$service_certifications) {
        return;
    }
    ?>
    <div class="service-meta-cards">
        <?php if ($service_category) : ?>
        <div class="service-meta-card">
            <div class="service-meta-card__icon">
                <?php echo ehs_service_meta_icon('category'); ?>
            </div>
            <div class="service-meta-card__content">
                <div class="service-meta-card__label">Category</div>
                <div class="service-meta-card__value"><?php echo esc_html($service_category); ?></div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($service_area) : ?>
        <div class="service-meta-card">
            <div class="service-meta-card__icon">
                <?php echo ehs_service_meta_icon('area'); ?>
            </div>
            <div class="service-meta-card__content">
                <div class="service-meta-card__label">Service Area</div>
                <div class="service-meta-card__value"><?php echo esc_html($service_area); ?></div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($service_certifications) : ?>
        <div class="service-meta-card">
            <div class="service-meta-card__icon">
                <?php echo ehs_service_meta_icon('certifications'); ?>
            </div>
            <div class="service-meta-card__content">
                <div class="service-meta-card__label">Certifications</div>
                <div class="service-meta-card__value"><?php echo esc_html($service_certifications); ?></div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Output service sidebar navigation menu
 * Lists all published services with current service highlighted
 */
function ehs_service_sidebar_menu() {
    $current_id = get_the_ID();
    
    // Get all published services, ordered by service_order meta field
    $services = get_posts(array(
        'post_type' => 'services',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'meta_value_num',
        'meta_key' => 'service_order',
        'order' => 'ASC',
    ));
    
    if (empty($services)) {
        return;
    }
    ?>
    <nav class="service-sidebar-nav">
        <ul class="service-sidebar-menu">
            <?php foreach ($services as $service) : ?>
                <li>
                    <a href="<?php echo esc_url(get_permalink($service->ID)); ?>" 
                       class="<?php echo ($service->ID === $current_id) ? 'current' : ''; ?>">
                        <?php echo esc_html($service->post_title); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <?php
}

/**
 * Output generic service section with custom content
 * 
 * @param string $title Section title
 * @param string $content Section content (HTML allowed)
 * @param string $layout Optional layout class (e.g., 'service-section-3col')
 */
function ehs_service_section($title = '', $content = '', $layout = '') {
    ?>
    <div class="service-section <?php echo esc_attr($layout); ?>">
        <?php if ($title) : ?>
            <h2><?php echo esc_html($title); ?></h2>
        <?php endif; ?>
        <?php if ($content) : ?>
            <div class="service-section-content">
                <?php echo wp_kses_post($content); ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Generate Table of Contents data from post content headings
 *
 * Parses H2, H3, H4 elements from content and returns structured array
 *
 * @param string $content Post content HTML
 * @return array Array of heading data: [['id' => 'slug', 'text' => 'Heading Text', 'level' => 2], ...]
 */
function ehs_service_toc_generate($content = '') {
    if (empty($content)) {
        return array();
    }

    $toc_data = array();
    $used_ids = array();

    // Use regex to extract headings (more reliable than DOMDocument for malformed HTML)
    preg_match_all('/<h([2-4])([^>]*)>(.+?)<\/h[2-4]>/is', $content, $matches, PREG_SET_ORDER);

    if (empty($matches)) {
        return array();
    }

    foreach ($matches as $match) {
        $level = (int) $match[1];
        $heading_text = wp_strip_all_tags($match[3]);
        $heading_text = html_entity_decode($heading_text, ENT_QUOTES, 'UTF-8');
        $heading_text = trim($heading_text);

        if (empty($heading_text)) {
            continue;
        }

        // Generate slug from heading text
        $base_id = sanitize_title($heading_text);
        if (empty($base_id)) {
            $base_id = 'section';
        }

        // Handle duplicate IDs
        $id = $base_id;
        $counter = 1;
        while (in_array($id, $used_ids)) {
            $id = $base_id . '-' . $counter;
            $counter++;
        }
        $used_ids[] = $id;

        $toc_data[] = array(
            'id' => $id,
            'text' => $heading_text,
            'level' => $level,
            'original' => $match[0], // Store original HTML for replacement
        );
    }

    return $toc_data;
}

/**
 * Inject ID attributes into content headings for ToC linking
 *
 * @param string $content Post content HTML
 * @param array $toc_data ToC data from ehs_service_toc_generate()
 * @return string Modified content with ID attributes
 */
function ehs_service_toc_inject_ids($content, $toc_data) {
    if (empty($content) || empty($toc_data)) {
        return $content;
    }

    foreach ($toc_data as $item) {
        $original = $item['original'];
        $id = $item['id'];
        $level = $item['level'];

        // Check if heading already has an id attribute
        if (preg_match('/\sid=["\'][^"\']*["\']/', $original)) {
            // Replace existing id
            $new_heading = preg_replace(
                '/(<h' . $level . ')([^>]*)\sid=["\'][^"\']*["\']([^>]*)>/is',
                '$1$2 id="' . esc_attr($id) . '"$3>',
                $original
            );
        } else {
            // Add new id attribute
            $new_heading = preg_replace(
                '/(<h' . $level . ')([^>]*)>/is',
                '$1$2 id="' . esc_attr($id) . '">',
                $original
            );
        }

        // Replace in content (only first occurrence)
        $pos = strpos($content, $original);
        if ($pos !== false) {
            $content = substr_replace($content, $new_heading, $pos, strlen($original));
        }
    }

    return $content;
}

/**
 * Output sticky Table of Contents sidebar
 *
 * Falls back to original service sidebar menu if no headings found
 *
 * @param array $toc_data ToC data from ehs_service_toc_generate()
 */
function ehs_service_toc_sidebar($toc_data = array()) {
    if (empty($toc_data)) {
        // Fallback to original service sidebar menu
        ehs_service_sidebar_menu();
        return;
    }
    ?>
    <nav class="service-toc" aria-label="<?php esc_attr_e('Table of Contents', 'hello-elementor-child'); ?>">
        <div class="service-toc__header">
            <span class="service-toc__title"><?php esc_html_e('On This Page', 'hello-elementor-child'); ?></span>
        </div>
        <ul class="service-toc__list" id="service-toc-list" role="list">
            <?php foreach ($toc_data as $item) : ?>
                <li class="service-toc__item service-toc__item--level-<?php echo esc_attr($item['level']); ?>">
                    <a href="#<?php echo esc_attr($item['id']); ?>"
                       class="service-toc__link"
                       data-toc-id="<?php echo esc_attr($item['id']); ?>">
                        <?php echo esc_html($item['text']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <?php
}

/**
 * Render a single service card
 *
 * @param WP_Post $service Service post object
 * @return string HTML markup for service card
 */
function ehs_render_service_card($service) {
    if (!$service || !is_a($service, 'WP_Post')) {
        return '';
    }

    // Get excerpt first (SEO-optimized), fallback to short description
    $excerpt = get_post_field('post_excerpt', $service->ID);
    if (empty($excerpt) || strlen(trim($excerpt)) < 20) {
        $short_desc = get_post_meta($service->ID, 'service_short_description', true);
        $excerpt = $short_desc ? $short_desc : '';
    }
    
    // Clean excerpt - remove ellipses and HTML entities
    if (!empty($excerpt)) {
        $excerpt = html_entity_decode($excerpt, ENT_QUOTES, 'UTF-8');
        $excerpt = rtrim($excerpt, '.…');
        $excerpt = preg_replace('/\.{2,}/', '.', $excerpt);
        $excerpt = trim($excerpt);
    }
    
    $thumbnail = get_the_post_thumbnail_url($service->ID, 'large');
    $icon_id = get_post_meta($service->ID, 'service_icon', true);

    ob_start();
    ?>
    <article class="service-card">
        <?php if ($thumbnail) : ?>
            <div class="service-card__image">
                <img src="<?php echo esc_url($thumbnail); ?>"
                     alt="<?php echo esc_attr($service->post_title); ?>"
                     loading="lazy" />
            </div>
        <?php elseif ($icon_id) : ?>
            <div class="service-card__icon">
                <?php echo wp_get_attachment_image($icon_id, 'thumbnail'); ?>
            </div>
        <?php endif; ?>
        <div class="service-card__content">
            <h3 class="service-card__title">
                <?php echo esc_html($service->post_title); ?>
            </h3>
            <?php if (!empty($excerpt)) : ?>
                <p class="service-card__excerpt">
                    <?php echo esc_html(wp_trim_words($excerpt, 20)); ?>
                </p>
            <?php endif; ?>
            <a href="<?php echo esc_url(get_permalink($service->ID)); ?>"
               class="service-card__link">
                <?php esc_html_e('Learn More', 'hello-elementor-child'); ?>
                <span class="screen-reader-text"><?php printf(esc_html__('about %s', 'hello-elementor-child'), $service->post_title); ?></span>
            </a>
        </div>
    </article>
    <?php
    return ob_get_clean();
}

/**
 * Output service cards grid
 *
 * Generic function to render service cards from an array of service posts
 *
 * @param array $services Array of WP_Post service objects
 * @param string $section_title Section heading text
 * @param string $section_id Optional section ID for aria-labelledby
 * @return string HTML markup
 */
function ehs_render_service_cards_grid($services, $section_title = '', $section_id = '') {
    if (empty($services)) {
        return '';
    }

    if (empty($section_id)) {
        $section_id = 'service-cards-' . uniqid();
    }

    ob_start();
    ?>
    <section class="service-related" aria-labelledby="<?php echo esc_attr($section_id); ?>">
        <div class="service-related__container">
            <?php if ($section_title) : ?>
                <h2 id="<?php echo esc_attr($section_id); ?>" class="service-related__title">
                    <?php echo esc_html($section_title); ?>
                </h2>
            <?php endif; ?>
            <div class="service-related__grid">
                <?php foreach ($services as $service) : ?>
                    <?php echo ehs_render_service_card($service); ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

/**
 * Output related services card grid
 *
 * Displays related services based on service_related_services meta field
 *
 * @param int $post_id Current post ID (optional, defaults to current post)
 * @param string $section_title Section heading text
 */
function ehs_service_related_cards($post_id = 0, $section_title = 'Related Services') {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $related_ids_string = get_post_meta($post_id, 'service_related_services', true);
    if (empty($related_ids_string)) {
        return;
    }

    $related_ids = array_filter(array_map('absint', explode(',', $related_ids_string)));
    if (empty($related_ids)) {
        return;
    }

    $related_services = get_posts(array(
        'post_type' => 'services',
        'post__in' => $related_ids,
        'posts_per_page' => count($related_ids),
        'orderby' => 'post__in',
        'post_status' => 'publish',
    ));

    if (empty($related_services)) {
        return;
    }

    echo ehs_render_service_cards_grid($related_services, $section_title, 'related-services-heading');
}

/**
 * Service Cards Shortcode
 *
 * Usage examples:
 * [service_cards] - Display all services
 * [service_cards ids="123,456,789"] - Display specific services by ID
 * [service_cards category="construction-safety"] - Display services by category slug
 * [service_cards title="Our Services" count="6" orderby="title" order="ASC"]
 * [service_cards featured="1"] - Display only featured services
 *
 * @param array $atts Shortcode attributes
 * @return string HTML markup
 */
function ehs_service_cards_shortcode($atts) {
    $atts = shortcode_atts(array(
        'ids' => '',              // Comma-separated service IDs
        'category' => '',         // Service category slug
        'area' => '',             // Service area slug
        'featured' => '',         // Show only featured (1) or non-featured (0)
        'count' => -1,            // Number of services to display (-1 for all)
        'orderby' => 'menu_order', // Order by: menu_order, title, date, meta_value
        'order' => 'ASC',         // Order direction: ASC, DESC
        'title' => '',            // Section title (empty to hide)
        'exclude' => '',          // Comma-separated IDs to exclude
    ), $atts, 'service_cards');

    $args = array(
        'post_type' => 'services',
        'posts_per_page' => intval($atts['count']),
        'post_status' => 'publish',
        'orderby' => $atts['orderby'],
        'order' => strtoupper($atts['order']),
    );

    // Handle specific IDs
    if (!empty($atts['ids'])) {
        $ids = array_filter(array_map('absint', explode(',', $atts['ids'])));
        if (!empty($ids)) {
            $args['post__in'] = $ids;
            $args['orderby'] = 'post__in';
        }
    }

    // Handle category filter
    if (!empty($atts['category'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'service_category',
                'field' => 'slug',
                'terms' => sanitize_text_field($atts['category']),
            ),
        );
    }

    // Handle area filter
    if (!empty($atts['area'])) {
        if (isset($args['tax_query'])) {
            $args['tax_query']['relation'] = 'AND';
        } else {
            $args['tax_query'] = array();
        }
        $args['tax_query'][] = array(
            'taxonomy' => 'service_area',
            'field' => 'slug',
            'terms' => sanitize_text_field($atts['area']),
        );
    }

    // Handle featured filter
    if ($atts['featured'] !== '') {
        $args['meta_query'] = array(
            array(
                'key' => 'service_featured',
                'value' => $atts['featured'] === '1' ? '1' : '',
                'compare' => $atts['featured'] === '1' ? '=' : '!=',
            ),
        );
    }

    // Handle orderby meta_value
    if ($atts['orderby'] === 'meta_value') {
        $args['meta_key'] = 'service_order';
    }

    // Handle exclude
    if (!empty($atts['exclude'])) {
        $exclude_ids = array_filter(array_map('absint', explode(',', $atts['exclude'])));
        if (!empty($exclude_ids)) {
            $args['post__not_in'] = $exclude_ids;
        }
    }

    $services = get_posts($args);

    if (empty($services)) {
        return '';
    }

    $section_id = 'service-cards-' . uniqid();
    return ehs_render_service_cards_grid($services, $atts['title'], $section_id);
}
add_shortcode('service_cards', 'ehs_service_cards_shortcode');

/**
 * Render a single article card
 *
 * @param WP_Post $post Post object
 * @return string HTML markup for article card
 */
function ehs_render_article_card($post) {
    if (!$post || !is_a($post, 'WP_Post')) {
        return '';
    }

    $excerpt = has_excerpt($post->ID) ? get_the_excerpt($post->ID) : wp_trim_words(strip_shortcodes($post->post_content), 20);
    $thumbnail = get_the_post_thumbnail_url($post->ID, 'large');
    $date = get_the_date('', $post->ID);
    $author = get_the_author_meta('display_name', $post->post_author);
    $categories = get_the_category($post->ID);
    $category_name = !empty($categories) ? $categories[0]->name : '';

    ob_start();
    ?>
    <article class="article-card">
        <?php if ($thumbnail) : ?>
            <div class="article-card__image">
                <a href="<?php echo esc_url(get_permalink($post->ID)); ?>">
                    <img src="<?php echo esc_url($thumbnail); ?>"
                         alt="<?php echo esc_attr($post->post_title); ?>"
                         loading="lazy" />
                </a>
            </div>
        <?php endif; ?>
        <div class="article-card__content">
            <?php if ($category_name) : ?>
                <div class="article-card__category">
                    <?php echo esc_html($category_name); ?>
                </div>
            <?php endif; ?>
            <h3 class="article-card__title">
                <a href="<?php echo esc_url(get_permalink($post->ID)); ?>">
                    <?php echo esc_html($post->post_title); ?>
                </a>
            </h3>
            <?php if ($excerpt) : ?>
                <p class="article-card__excerpt">
                    <?php echo esc_html($excerpt); ?>
                </p>
            <?php endif; ?>
            <div class="article-card__meta">
                <?php if ($date) : ?>
                    <span class="article-card__date">
                        <?php echo esc_html($date); ?>
                    </span>
                <?php endif; ?>
                <?php if ($author) : ?>
                    <span class="article-card__author">
                        <?php echo esc_html($author); ?>
                    </span>
                <?php endif; ?>
            </div>
            <a href="<?php echo esc_url(get_permalink($post->ID)); ?>"
               class="article-card__link">
                <?php esc_html_e('Read More', 'hello-elementor-child'); ?>
                <span class="screen-reader-text"><?php printf(esc_html__('Read more about %s', 'hello-elementor-child'), $post->post_title); ?></span>
            </a>
        </div>
    </article>
    <?php
    return ob_get_clean();
}

/**
 * Output article cards grid
 *
 * Generic function to render article cards from an array of post objects
 *
 * @param array $posts Array of WP_Post objects
 * @param string $section_title Section heading text
 * @param string $section_id Optional section ID for aria-labelledby
 * @return string HTML markup
 */
function ehs_render_article_cards_grid($posts, $section_title = '', $section_id = '') {
    if (empty($posts)) {
        return '';
    }

    if (empty($section_id)) {
        $section_id = 'article-cards-' . uniqid();
    }

    ob_start();
    ?>
    <section class="article-related" aria-labelledby="<?php echo esc_attr($section_id); ?>">
        <div class="article-related__container">
            <?php if ($section_title) : ?>
                <h2 id="<?php echo esc_attr($section_id); ?>" class="article-related__title">
                    <?php echo esc_html($section_title); ?>
                </h2>
            <?php endif; ?>
            <div class="article-related__grid">
                <?php foreach ($posts as $post) : ?>
                    <?php echo ehs_render_article_card($post); ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

/**
 * Article Cards Shortcode
 *
 * Usage examples:
 * [article_cards] - Display all articles
 * [article_cards ids="123,456,789"] - Display specific articles by ID
 * [article_cards category="news"] - Display articles by category slug
 * [article_cards title="Latest Articles" count="6" orderby="date" order="DESC"]
 * [article_cards tag="featured"] - Display articles by tag
 *
 * @param array $atts Shortcode attributes
 * @return string HTML markup
 */
function ehs_article_cards_shortcode($atts) {
    $atts = shortcode_atts(array(
        'ids' => '',              // Comma-separated post IDs
        'category' => '',         // Category slug
        'tag' => '',              // Tag slug
        'count' => -1,            // Number of articles to display (-1 for all)
        'orderby' => 'date',      // Order by: date, title, menu_order, rand
        'order' => 'DESC',        // Order direction: ASC, DESC
        'title' => '',            // Section title (empty to hide)
        'exclude' => '',          // Comma-separated IDs to exclude
        'author' => '',           // Author ID or login
        'offset' => 0,            // Number of posts to skip
    ), $atts, 'article_cards');

    $args = array(
        'post_type' => 'post',
        'posts_per_page' => intval($atts['count']),
        'post_status' => 'publish',
        'orderby' => $atts['orderby'],
        'order' => strtoupper($atts['order']),
        'offset' => intval($atts['offset']),
    );

    // Handle specific IDs
    if (!empty($atts['ids'])) {
        $ids = array_filter(array_map('absint', explode(',', $atts['ids'])));
        if (!empty($ids)) {
            $args['post__in'] = $ids;
            $args['orderby'] = 'post__in';
        }
    }

    // Handle category filter
    if (!empty($atts['category'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'category',
                'field' => 'slug',
                'terms' => sanitize_text_field($atts['category']),
            ),
        );
    }

    // Handle tag filter
    if (!empty($atts['tag'])) {
        if (isset($args['tax_query'])) {
            $args['tax_query']['relation'] = 'AND';
        } else {
            $args['tax_query'] = array();
        }
        $args['tax_query'][] = array(
            'taxonomy' => 'post_tag',
            'field' => 'slug',
            'terms' => sanitize_text_field($atts['tag']),
        );
    }

    // Handle author filter
    if (!empty($atts['author'])) {
        if (is_numeric($atts['author'])) {
            $args['author'] = intval($atts['author']);
        } else {
            $args['author_name'] = sanitize_text_field($atts['author']);
        }
    }

    // Handle exclude
    if (!empty($atts['exclude'])) {
        $exclude_ids = array_filter(array_map('absint', explode(',', $atts['exclude'])));
        if (!empty($exclude_ids)) {
            $args['post__not_in'] = $exclude_ids;
        }
    }

    $posts = get_posts($args);

    if (empty($posts)) {
        return '';
    }

    $section_id = 'article-cards-' . uniqid();
    return ehs_render_article_cards_grid($posts, $atts['title'], $section_id);
}
add_shortcode('article_cards', 'ehs_article_cards_shortcode');
