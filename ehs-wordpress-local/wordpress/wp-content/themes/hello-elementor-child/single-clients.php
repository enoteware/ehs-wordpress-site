<?php
/**
 * Single Client Template
 *
 * Template for displaying individual client posts
 * Uses child theme CSS classes following the design system
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

while (have_posts()) : the_post();

    // Get client meta fields
    $website = get_post_meta(get_the_ID(), 'client_website', true);
    $logo_id = get_post_meta(get_the_ID(), 'client_logo', true);
    $industry = get_post_meta(get_the_ID(), 'client_industry', true);
    $location = get_post_meta(get_the_ID(), 'client_location', true);
    $since = get_post_meta(get_the_ID(), 'client_since', true);
    $contact_name = get_post_meta(get_the_ID(), 'client_contact_name', true);
    $services_used = get_post_meta(get_the_ID(), 'client_services_used', true);
    $testimonial = get_post_meta(get_the_ID(), 'client_testimonial', true);
    $status = get_post_meta(get_the_ID(), 'client_status', true);
    $featured = get_post_meta(get_the_ID(), 'client_featured', true);

    // Get logo image
    $logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'medium') : '';
    if (!$logo_url && has_post_thumbnail()) {
        $logo_url = get_the_post_thumbnail_url(get_the_ID(), 'medium');
    }

    // Industry badge colors
    $industry_colors = array(
        'Biotechnology' => '#0073aa',
        'Pharmaceutical' => '#8e44ad',
        'Healthcare' => '#27ae60',
        'Manufacturing' => '#e67e22',
        'Construction' => '#d35400',
        'Education' => '#3498db',
        'Government' => '#34495e',
        'Energy' => '#f39c12',
        'Technology' => '#1abc9c',
        'Real Estate' => '#95a5a6',
        'Food & Beverage' => '#e74c3c',
        'Agriculture' => '#2ecc71',
    );
    $industry_color = isset($industry_colors[$industry]) ? $industry_colors[$industry] : '#003366';

    // Status labels
    $status_labels = array(
        'active' => 'Active Client',
        'past' => 'Past Client',
        'prospect' => 'Prospective Client',
    );
    $status_label = isset($status_labels[$status]) ? $status_labels[$status] : 'Client';
    ?>

    <!-- Client Hero Section -->
    <section class="service-hero" style="background: linear-gradient(135deg, var(--ehs-navy) 0%, #001a33 100%);">
        <div class="service-hero-content">
            <?php if ($logo_url) : ?>
                <div style="display: inline-block; background: white; padding: 20px 30px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.2);">
                    <img src="<?php echo esc_url($logo_url); ?>" alt="<?php the_title_attribute(); ?> Logo" style="max-height: 80px; max-width: 250px; width: auto; height: auto; object-fit: contain;">
                </div>
            <?php endif; ?>
            <h1><?php the_title(); ?></h1>
            <?php if ($industry) : ?>
                <div style="margin-top: 20px;">
                    <span style="display: inline-block; background: <?php echo esc_attr($industry_color); ?>; color: white; padding: 8px 20px; border-radius: 30px; font-family: 'Maven Pro', sans-serif; font-weight: 600; font-size: 0.95rem;">
                        <?php echo esc_html($industry); ?>
                    </span>
                    <?php if ($location) : ?>
                        <span style="display: inline-block; background: rgba(255,255,255,0.15); color: white; padding: 8px 20px; border-radius: 30px; font-family: 'Maven Pro', sans-serif; font-weight: 500; font-size: 0.95rem; margin-left: 10px;">
                            <span class="dashicons dashicons-location" style="font-size: 16px; width: 16px; height: 16px; vertical-align: middle; margin-right: 5px;"></span>
                            <?php echo esc_html($location); ?>
                        </span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Client Content Layout -->
    <div class="service-container">
        <div class="service-layout">

            <!-- Client Sidebar -->
            <aside class="service-sidebar">
                <!-- Client Details Card -->
                <div class="service-meta-card">
                    <div class="service-meta-card__content">
                        <h3 style="font-family: 'Maven Pro', sans-serif; font-weight: 700; font-size: 1.25rem; color: var(--ehs-navy); margin-bottom: 20px;">
                            Client Information
                        </h3>

                        <?php if ($status) : ?>
                            <div style="margin-bottom: 20px;">
                                <div class="service-meta-card__label">Status</div>
                                <div class="service-meta-card__value">
                                    <?php
                                    $status_color = $status === 'active' ? '#00a32a' : ($status === 'past' ? '#d63638' : '#2271b1');
                                    ?>
                                    <span style="display: inline-block; background: <?php echo esc_attr($status_color); ?>; color: white; padding: 4px 12px; border-radius: 4px; font-size: 0.85rem; font-weight: 600;">
                                        <?php echo esc_html($status_label); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($industry) : ?>
                            <div style="margin-bottom: 20px;">
                                <div class="service-meta-card__label">Industry</div>
                                <div class="service-meta-card__value" style="font-weight: 600; color: var(--ehs-navy);">
                                    <?php echo esc_html($industry); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($location) : ?>
                            <div style="margin-bottom: 20px;">
                                <div class="service-meta-card__label">Location</div>
                                <div class="service-meta-card__value">
                                    <?php echo esc_html($location); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($since) : ?>
                            <div style="margin-bottom: 20px;">
                                <div class="service-meta-card__label">Client Since</div>
                                <div class="service-meta-card__value">
                                    <?php echo esc_html($since); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($featured) : ?>
                            <div style="margin-bottom: 20px;">
                                <div class="service-meta-card__value">
                                    <span style="color: var(--ehs-gold); font-weight: 600;">
                                        <span class="dashicons dashicons-star-filled" style="font-size: 16px; width: 16px; height: 16px; vertical-align: middle;"></span>
                                        Featured Client
                                    </span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($website) : ?>
                    <!-- Website Link Card -->
                    <div class="service-meta-card" style="margin-top: 20px;">
                        <div class="service-meta-card__content" style="text-align: center;">
                            <a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener noreferrer" class="ehs-btn ehs-btn-solid-primary ehs-btn-md" style="display: block; text-decoration: none;">
                                <span class="dashicons dashicons-external" style="font-size: 18px; width: 18px; height: 18px; vertical-align: middle; margin-right: 8px;"></span>
                                Visit Website
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Back to Clients Link -->
                <div style="margin-top: 30px;">
                    <a href="/clients/" class="ehs-btn ehs-btn-outline-primary ehs-btn-md" style="display: block; text-align: center; text-decoration: none;">
                        ← View All Clients
                    </a>
                </div>
            </aside>

            <!-- Client Main Content -->
            <main class="service-content">
                <!-- About Section -->
                <div class="service-section">
                    <h2 style="font-family: 'Maven Pro', sans-serif; font-weight: 700; font-size: 2rem; color: var(--ehs-navy); margin-bottom: 30px;">
                        About <?php the_title(); ?>
                    </h2>
                    <?php
                    $content = get_the_content();
                    if ($content) {
                        $content = apply_filters('the_content', $content);
                        echo $content;
                    } else {
                        echo '<p style="color: #666; font-style: italic;">Company description coming soon.</p>';
                    }
                    ?>
                </div>

                <?php if ($services_used) : ?>
                    <!-- Services Provided Section -->
                    <div class="service-section" style="margin-top: 50px; padding: 40px; background: var(--ehs-light-gray); border-radius: 12px;">
                        <h3 style="font-family: 'Maven Pro', sans-serif; font-weight: 700; font-size: 1.5rem; color: var(--ehs-navy); margin-bottom: 20px;">
                            <span class="dashicons dashicons-clipboard" style="font-size: 24px; width: 24px; height: 24px; vertical-align: middle; margin-right: 10px; color: var(--ehs-gold);"></span>
                            Services Provided
                        </h3>
                        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                            <?php
                            $services_array = array_map('trim', explode(',', $services_used));
                            foreach ($services_array as $service) :
                                if (!empty($service)) :
                            ?>
                                <span style="display: inline-block; background: white; color: var(--ehs-navy); padding: 8px 16px; border-radius: 6px; font-family: 'Maven Pro', sans-serif; font-weight: 500; font-size: 0.9rem; border: 1px solid #ddd;">
                                    <?php echo esc_html($service); ?>
                                </span>
                            <?php
                                endif;
                            endforeach;
                            ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($testimonial) : ?>
                    <!-- Testimonial Section -->
                    <div class="service-section" style="margin-top: 50px;">
                        <div style="background: linear-gradient(135deg, var(--ehs-navy) 0%, #001a33 100%); padding: 50px; border-radius: 16px; position: relative;">
                            <div style="position: absolute; top: 20px; left: 30px; font-size: 80px; color: var(--ehs-gold); opacity: 0.3; font-family: Georgia, serif; line-height: 1;">"</div>
                            <blockquote style="position: relative; z-index: 1; margin: 0; padding: 0 0 0 20px;">
                                <p style="color: white; font-size: 1.25rem; line-height: 1.8; font-style: italic; margin-bottom: 20px;">
                                    <?php echo esc_html($testimonial); ?>
                                </p>
                                <?php if ($contact_name) : ?>
                                    <footer style="color: var(--ehs-gold); font-family: 'Maven Pro', sans-serif; font-weight: 600;">
                                        — <?php echo esc_html($contact_name); ?><?php if ($location) echo ', ' . esc_html($location); ?>
                                    </footer>
                                <?php endif; ?>
                            </blockquote>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Related Clients (same industry) -->
                <?php
                if ($industry) {
                    $related_clients = get_posts(array(
                        'post_type' => 'clients',
                        'posts_per_page' => 3,
                        'post__not_in' => array(get_the_ID()),
                        'meta_query' => array(
                            array(
                                'key' => 'client_industry',
                                'value' => $industry,
                                'compare' => '='
                            )
                        ),
                        'orderby' => 'meta_value_num',
                        'meta_key' => 'client_display_order',
                        'order' => 'ASC',
                    ));

                    if (!empty($related_clients)) :
                ?>
                    <div class="service-section" style="margin-top: 60px;">
                        <h2 style="font-family: 'Maven Pro', sans-serif; font-weight: 700; font-size: 2rem; color: var(--ehs-navy); margin-bottom: 40px;">
                            More <?php echo esc_html($industry); ?> Clients
                        </h2>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
                            <?php foreach ($related_clients as $related) :
                                $rel_logo_id = get_post_meta($related->ID, 'client_logo', true);
                                $rel_logo_url = $rel_logo_id ? wp_get_attachment_image_url($rel_logo_id, 'medium') : '';
                                if (!$rel_logo_url) {
                                    $rel_logo_url = get_the_post_thumbnail_url($related->ID, 'medium');
                                }
                                $rel_industry = get_post_meta($related->ID, 'client_industry', true);
                                $rel_location = get_post_meta($related->ID, 'client_location', true);
                            ?>
                                <a href="<?php echo get_permalink($related->ID); ?>" style="text-decoration: none; display: block;">
                                    <div style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); transition: transform 0.2s, box-shadow 0.2s; border: 1px solid #eee;">
                                        <?php if ($rel_logo_url) : ?>
                                            <div style="height: 60px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                                                <img src="<?php echo esc_url($rel_logo_url); ?>" alt="<?php echo esc_attr($related->post_title); ?>" style="max-height: 60px; max-width: 150px; object-fit: contain;">
                                            </div>
                                        <?php endif; ?>
                                        <h4 style="font-family: 'Maven Pro', sans-serif; font-weight: 700; font-size: 1.1rem; color: var(--ehs-navy); margin: 0 0 10px 0;">
                                            <?php echo esc_html($related->post_title); ?>
                                        </h4>
                                        <?php if ($rel_location) : ?>
                                            <p style="color: #666; font-size: 0.9rem; margin: 0;">
                                                <span class="dashicons dashicons-location" style="font-size: 14px; width: 14px; height: 14px; vertical-align: middle;"></span>
                                                <?php echo esc_html($rel_location); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            <?php endforeach;
                            wp_reset_postdata();
                            ?>
                        </div>
                    </div>
                <?php endif; } ?>

                <!-- Call to Action -->
                <?php ehs_unified_cta(); ?>

            </main>

        </div>
    </div>

    <?php
endwhile;

get_footer();
