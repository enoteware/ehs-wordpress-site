<?php
/**
 * Single Credential Template
 *
 * Template for displaying individual credential posts
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

    // Get credential meta fields
    $acronym = get_post_meta(get_the_ID(), 'credential_acronym', true);
    $issuing_org = get_post_meta(get_the_ID(), 'credential_issuing_organization', true);
    $date_obtained = get_post_meta(get_the_ID(), 'credential_date_obtained', true);
    $category = get_post_meta(get_the_ID(), 'credential_category', true);
    $type = get_post_meta(get_the_ID(), 'credential_type', true);
    $featured = get_post_meta(get_the_ID(), 'credential_featured', true);

    // Get featured image for hero background
    $hero_image = get_the_post_thumbnail_url(get_the_ID(), 'full');
    $featured_image = get_the_post_thumbnail(get_the_ID(), 'medium', array('class' => 'credential-single-logo'));

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

    <!-- Credential Hero Section -->
    <section class="service-hero" style="background-image: url('<?php echo esc_url($hero_image ? $hero_image : get_stylesheet_directory_uri() . '/assets/images/hero-background.jpg'); ?>');">
        <div class="service-hero-content">
            <?php if ($acronym) : ?>
                <div style="display: inline-block; background: var(--ehs-gold); color: var(--ehs-navy); font-family: 'Maven Pro', sans-serif; font-weight: 700; font-size: 1.5rem; padding: 12px 24px; border-radius: 8px; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 1px;">
                    <?php echo esc_html($acronym); ?>
                </div>
            <?php endif; ?>
            <h1><?php the_title(); ?></h1>
            <?php if (has_excerpt()) : ?>
                <div class="service-hero-text"><?php the_excerpt(); ?></div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Credential Content Layout -->
    <div class="service-container">
        <div class="service-layout">

            <!-- Credential Sidebar -->
            <aside class="service-sidebar">
                <!-- Credential Logo Card -->
                <?php if ($featured_image) : ?>
                    <div class="service-meta-card" style="margin-bottom: 30px;">
                        <div class="service-meta-card__content" style="text-align: center; padding: 30px;">
                            <?php echo $featured_image; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Credential Details Card -->
                <div class="service-meta-card">
                    <div class="service-meta-card__content">
                        <h3 style="font-family: 'Maven Pro', sans-serif; font-weight: 700; font-size: 1.25rem; color: var(--ehs-navy); margin-bottom: 20px;">
                            Credential Details
                        </h3>
                        
                        <?php if ($acronym) : ?>
                            <div style="margin-bottom: 20px;">
                                <div class="service-meta-card__label">Acronym</div>
                                <div class="service-meta-card__value" style="font-weight: 700; font-size: 1.1rem; color: var(--ehs-navy);">
                                    <?php echo esc_html($acronym); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($issuing_org) : ?>
                            <div style="margin-bottom: 20px;">
                                <div class="service-meta-card__label">Issuing Organization</div>
                                <div class="service-meta-card__value">
                                    <?php echo esc_html($issuing_org); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($category) : ?>
                            <div style="margin-bottom: 20px;">
                                <div class="service-meta-card__label">Category</div>
                                <div class="service-meta-card__value">
                                    <?php if ($category_class) : ?>
                                        <span class="credential-card__category <?php echo esc_attr($category_class); ?>" style="display: inline-block;">
                                            <?php echo esc_html($category); ?>
                                        </span>
                                    <?php else : ?>
                                        <?php echo esc_html($category); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($type) : ?>
                            <div style="margin-bottom: 20px;">
                                <div class="service-meta-card__label">Type</div>
                                <div class="service-meta-card__value">
                                    <?php echo esc_html($type); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($date_obtained) : ?>
                            <div style="margin-bottom: 20px;">
                                <div class="service-meta-card__label">Date Obtained</div>
                                <div class="service-meta-card__value">
                                    <?php echo esc_html(date_i18n(get_option('date_format'), strtotime($date_obtained))); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($featured) : ?>
                            <div style="margin-bottom: 20px;">
                                <div class="service-meta-card__label">Status</div>
                                <div class="service-meta-card__value">
                                    <span style="color: var(--ehs-gold); font-weight: 600;">
                                        <span class="dashicons dashicons-star-filled" style="font-size: 16px; width: 16px; height: 16px; vertical-align: middle;"></span>
                                        Featured Credential
                                    </span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Back to Credentials Link -->
                <div style="margin-top: 30px;">
                    <a href="/credentials/" class="ehs-btn ehs-btn-outline-primary ehs-btn-md" style="display: block; text-align: center; text-decoration: none;">
                        ← View All Credentials
                    </a>
                </div>
            </aside>

            <!-- Credential Main Content -->
            <main class="service-content">
                <!-- Main Content -->
                <div class="service-section">
                    <?php 
                    $content = get_the_content();
                    $content = apply_filters('the_content', $content);
                    echo $content;
                    ?>
                </div>

                <!-- Related Credentials (same category) -->
                <?php
                if ($category) {
                    $related_credentials = get_posts(array(
                        'post_type' => 'credentials',
                        'posts_per_page' => 3,
                        'post__not_in' => array(get_the_ID()),
                        'meta_query' => array(
                            array(
                                'key' => 'credential_category',
                                'value' => $category,
                                'compare' => '='
                            )
                        ),
                        'orderby' => 'meta_value_num',
                        'meta_key' => 'credential_order',
                        'order' => 'ASC',
                    ));

                    if (!empty($related_credentials)) :
                ?>
                    <div class="service-section" style="margin-top: 60px;">
                        <h2 style="font-family: 'Maven Pro', sans-serif; font-weight: 700; font-size: 2rem; color: var(--ehs-navy); margin-bottom: 40px;">
                            Related Credentials
                        </h2>
                        <div class="credentials-grid">
                            <?php foreach ($related_credentials as $related) : 
                                ehs_render_credential_card($related);
                            endforeach; 
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
