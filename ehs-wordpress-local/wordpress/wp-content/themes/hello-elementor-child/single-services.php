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

    $is_lead_compliance_plan = (get_post_field('post_name', get_the_ID()) === 'lead-compliance-plan-services');
    $is_industrial_hygiene = (get_post_field('post_name', get_the_ID()) === 'industrial-hygiene-san-diego');
    $is_ssho = (get_post_field('post_name', get_the_ID()) === 'ssho-services-california');

    // Get service meta fields
    $service_short_description = get_post_meta(get_the_ID(), 'service_short_description', true);

    // Get featured image for hero background; fallback to theme image when missing or file not on disk
    $hero_image = get_the_post_thumbnail_url(get_the_ID(), 'full');
    $hero_fallback = get_stylesheet_directory_uri() . '/assets/images/hero-background.jpg';
    if (empty($hero_image) || (function_exists('ehs_attachment_url_file_exists') && !ehs_attachment_url_file_exists($hero_image))) {
        $hero_image = $hero_fallback;
    }

    // Content: PHP template for Lead Compliance Plan, else post content
    if ($is_lead_compliance_plan) {
        ob_start();
        get_template_part('template-parts/service-content', 'lead-compliance-plan');
        $raw_content = ob_get_clean();
    } else {
        $raw_content = get_the_content();
        // Service content is stored as pre-formatted HTML. Running `wpautop` here can
        // corrupt complex markup (e.g., timelines) by injecting stray <p></p> tags.
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
    }
    $toc_data = ehs_service_toc_generate($raw_content);
    $content_with_ids = ehs_service_toc_inject_ids($raw_content, $toc_data);
    ?>

    <?php /*
    <!-- Service Hero Section (commented out for now) -->
    <section class="service-hero" style="background-image: url('<?php echo esc_url($hero_image ? $hero_image : ''); ?>');">
        <div class="service-hero-content">
            <h1><?php the_title(); ?></h1>
            <?php if ($service_short_description) : ?>
                <div class="service-hero-subtitle"><?php echo esc_html($service_short_description); ?></div>
            <?php endif; ?>
            <?php if ($is_lead_compliance_plan) : ?>
                <div class="service-hero-stat">100+ Lead Compliance Plans Completed Across All 12 Caltrans Districts</div>
                <div class="service-hero-text">Our Certified Industrial Hygienists (CIH #9695CP) have completed over 100 Lead Compliance Plans for Caltrans bridge rehabilitation projects across all 12 California districts. We specialize in Cal/OSHA Title 8 Section 1532.1 compliance and Caltrans Special Provisions Section 7-1.02K9 requirements for lead-containing coatings work.</div>
            <?php endif; ?>
        </div>
    </section>
    */ ?>

    <!-- Service Hero Split (40/60: navy full-width left, photo right) -->
    <section class="service-hero-split">
        <div class="service-hero-split__content">
            <h2 class="service-hero-split__heading"><?php the_title(); ?></h2>
            <?php if ($service_short_description) : ?>
                <div class="service-hero-split__subtext"><?php echo esc_html($service_short_description); ?></div>
            <?php endif; ?>
        </div>
        <div class="service-hero-split__media">
            <?php if ($hero_image) : ?>
                <img src="<?php echo esc_url($hero_image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" class="service-hero-split__img" />
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

                <!-- Service Special Content - Accordions (skipped for Lead Compliance Plan; content in PHP template) -->
                <?php
                if (!$is_lead_compliance_plan) {
                    ob_start();
                    ehs_render_service_accordions();
                    $accordions_output = ob_get_clean();
                    if (!empty($accordions_output)) :
                    ?>
                        <div class="service-section">
                            <?php echo $accordions_output; ?>
                        </div>
                    <?php
                    endif;
                }
                ?>

                <!-- Service Special Content - Video (skipped for Industrial Hygiene; video is in post content) -->
                <?php
                if (!$is_lead_compliance_plan && !$is_industrial_hygiene) {
                    ob_start();
                    ehs_render_service_youtube_video();
                    $video_output = ob_get_clean();
                    if (!empty($video_output)) :
                    ?>
                        <div class="service-section">
                            <?php echo $video_output; ?>
                        </div>
                    <?php
                    endif;
                }
                ?>

                <!-- Service Components (from meta field; skipped for Lead Compliance Plan) -->
                <?php
                if (!$is_lead_compliance_plan) {
                    $components_output = ehs_render_service_components();
                    if (!empty($components_output)) :
                    ?>
                        <div class="service-section">
                            <?php echo $components_output; ?>
                        </div>
                    <?php
                    endif;
                }
                ?>

                <!-- Matching Credential Cards (from checklist items) -->
                <?php 
                $matching_credentials_output = ehs_render_service_matching_credentials();
                if (!empty($matching_credentials_output)) :
                    echo $matching_credentials_output;
                endif; 
                ?>

                <!-- Related Services Cards -->
                <?php ehs_service_related_cards(); ?>

                <!-- Call to Action (Lead Compliance Plan has CTA in PHP template) -->
                <?php
                if (!$is_lead_compliance_plan) {
                    if ($is_ssho) {
                        $ssho_cta_title = 'Bidding a Federal Project Requiring an SSHO?';
                        $ssho_cta_text = 'Whether you\'re pursuing a USACE flood control project, NAVFAC military construction, or VA facility renovation, we provide senior-level SSHO professionals backed by SDVOSB certification that helps you meet subcontracting goals.<br><br><strong>Why Federal Contractors Choose Us:</strong><br>✓ Senior-level SSHOs with proven performance on $100M+ projects<br>✓ Expert technical backup from CIH and CSP professionals<br>✓ SDVOSB certification helping you meet participation goals<br>✓ Nationwide coverage with multi-state project experience<br>✓ Track record of seamless execution and client satisfaction<br><br><em>We respond to all SSHO inquiries within 24 hours and can mobilize nationwide within 1-2 weeks of contract award.</em>';
                        ehs_service_cta($ssho_cta_title, $ssho_cta_text);
                    } else {
                        ehs_service_cta();
                    }
                }
                ?>

                <!-- SSHO page: footer-style address block (canonical source: ehs_get_footer_address_data) -->
                <?php
                if ( $is_ssho ) :
                    $addr = ehs_get_footer_address_data();
                ?>
                    <div class="service-section service-address-block">
                        <h3 class="service-address-block__heading">Our address</h3>
                        <p class="service-address-block__address">
                            <?php if ( ! empty( $addr['company_name'] ) ) : ?><strong><?php echo esc_html( $addr['company_name'] ); ?></strong><br /><?php endif; ?>
                            <?php echo esc_html( $addr['line1'] ); ?><br />
                            <?php if ( ! empty( $addr['line2'] ) ) : ?><?php echo esc_html( $addr['line2'] ); ?><br /><?php endif; ?>
                            <?php echo esc_html( $addr['city_state'] ); ?>
                        </p>
                    </div>
                <?php endif; ?>

            </main>

        </div>
    </div>

    <?php
endwhile;

get_footer();
