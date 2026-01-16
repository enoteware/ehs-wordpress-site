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
    $raw_content = apply_filters('the_content', $raw_content);
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
            <?php 
            $excerpt = get_the_excerpt();
            if (!empty($excerpt)) {
                // Remove ellipses and trailing dots
                $excerpt = rtrim($excerpt, '.…');
                $excerpt = preg_replace('/\.{2,}/', '.', $excerpt);
                $excerpt = html_entity_decode($excerpt, ENT_QUOTES, 'UTF-8');
                // Ensure it ends with a period if it's a complete sentence
                if (!empty($excerpt) && !preg_match('/[.!?]$/', $excerpt)) {
                    $excerpt .= '.';
                }
            ?>
                <div class="service-hero-text"><?php echo esc_html($excerpt); ?></div>
            <?php } ?>
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
