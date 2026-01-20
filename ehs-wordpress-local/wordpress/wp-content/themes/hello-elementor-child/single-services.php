<?php
/**
 * Single Service Template
 *
 * Template for displaying individual service posts
 * Uses child theme CSS classes instead of Elementor
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

while (have_posts()) : the_post();

    // Get service meta fields
    $service_short_description = get_post_meta(get_the_ID(), 'service_short_description', true);

    // Get featured image for hero background
    $hero_image = get_the_post_thumbnail_url(get_the_ID(), 'full');

    // Generate ToC from content headings
    $raw_content = get_the_content();
    // Service content is stored as pre-formatted HTML. Running `wpautop` here can
    // corrupt complex markup (e.g., timelines) by injecting stray <p></p> tags.
    // Apply standard content filters while temporarily disabling wpautop.
    $wpautop_priority = has_filter('the_content', 'wpautop');
    $shortcode_unautop_priority = has_filter('the_content', 'shortcode_unautop');
    if ($wpautop_priority !== false) {
        remove_filter('the_content', 'wpautop', $wpautop_priority);
    }
    if ($shortcode_unautop_priority !== false) {
        remove_filter('the_content', 'shortcode_unautop', $shortcode_unautop_priority);
    }
    $raw_content = apply_filters('the_content', $raw_content);
    if ($shortcode_unautop_priority !== false) {
        add_filter('the_content', 'shortcode_unautop', $shortcode_unautop_priority);
    }
    if ($wpautop_priority !== false) {
        add_filter('the_content', 'wpautop', $wpautop_priority);
    }
    $toc_data = ehs_service_toc_generate($raw_content);
    $content_with_ids = ehs_service_toc_inject_ids($raw_content, $toc_data);
    ?>

    <!-- Service Hero Section -->
    <section class="service-hero" style="background-image: url('<?php echo esc_url($hero_image ? $hero_image : ''); ?>');">
        <div class="service-hero-content">
            <h1><?php the_title(); ?></h1>
            <?php if ($service_short_description) : ?>
                <div class="service-hero-subtitle"><?php echo esc_html($service_short_description); ?></div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Service Content Layout -->
    <div class="service-container">
        <div class="service-layout">

            <!-- Service Sidebar with ToC -->
            <aside class="service-sidebar">
                <!-- Service Meta Cards -->
                <?php ehs_service_meta_cards(); ?>
                
                <!-- Table of Contents -->
                <?php ehs_service_toc_sidebar($toc_data); ?>
            </aside>

            <!-- Service Main Content -->
            <main class="service-content">
                <!-- Main Content -->
                <div class="service-section">
                    <?php echo $content_with_ids; ?>
                </div>

                <!-- Service Special Content - Accordions -->
                <?php
                ob_start();
                ehs_render_service_accordions();
                $accordions_output = ob_get_clean();
                if (!empty($accordions_output)) :
                ?>
                    <div class="service-section">
                        <?php echo $accordions_output; ?>
                    </div>
                <?php endif; ?>

                <!-- Service Special Content - Video -->
                <?php
                ob_start();
                ehs_render_service_youtube_video();
                $video_output = ob_get_clean();
                if (!empty($video_output)) :
                ?>
                    <div class="service-section">
                        <?php echo $video_output; ?>
                    </div>
                <?php endif; ?>

                <!-- Service Components (from meta field) -->
                <?php 
                $components_output = ehs_render_service_components();
                if (!empty($components_output)) :
                ?>
                    <div class="service-section">
                        <?php echo $components_output; ?>
                    </div>
                <?php endif; ?>

                <!-- Related Services Cards -->
                <?php ehs_service_related_cards(); ?>

                <!-- Call to Action -->
                <?php ehs_service_cta(); ?>

            </main>

        </div>
    </div>

    <?php
endwhile;

get_footer();
