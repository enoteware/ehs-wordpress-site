<?php
/**
 * Blog/Insights Home Template
 * Displays blog posts in a modern card-based layout
 * Used for the Posts page (set in Reading Settings)
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Page title and description
$archive_title = 'Insights';
$archive_description = 'Expert perspectives on environmental health, safety compliance, and industry best practices from our certified professionals.';

// Get featured post (most recent sticky or first post)
$featured_post = null;
$sticky_posts = get_option('sticky_posts');
if (!empty($sticky_posts)) {
    $sticky_query = new WP_Query(array(
        'post__in' => $sticky_posts,
        'posts_per_page' => 1,
        'ignore_sticky_posts' => 1,
    ));
    if ($sticky_query->have_posts()) {
        $sticky_query->the_post();
        $featured_post = get_post();
        wp_reset_postdata();
    }
}
?>

<!-- ========================================
     HERO SECTION
     ======================================== -->
<section class="ehs-insights-hero" style="background-image: url('<?php echo get_stylesheet_directory_uri(); ?>/assets/images/hero-background.jpg');">
    <div class="ehs-insights-hero-overlay"></div>
    <div class="ehs-insights-hero-content">
        <h1><?php echo esc_html($archive_title); ?></h1>
        <?php if ($archive_description): ?>
            <p class="ehs-insights-hero-subtitle"><?php echo wp_kses_post($archive_description); ?></p>
        <?php endif; ?>
    </div>
</section>

<?php if ($featured_post): ?>
<!-- ========================================
     FEATURED POST SECTION
     ======================================== -->
<section class="ehs-featured-post-section">
    <div class="container">
        <div class="ehs-featured-post">
            <?php if (has_post_thumbnail($featured_post->ID)): ?>
                <div class="ehs-featured-post-image">
                    <a href="<?php echo get_permalink($featured_post->ID); ?>">
                        <?php echo get_the_post_thumbnail($featured_post->ID, 'large'); ?>
                    </a>
                </div>
            <?php endif; ?>
            <div class="ehs-featured-post-content">
                <span class="ehs-featured-label">Featured</span>
                <?php
                $categories = get_the_category($featured_post->ID);
                if (!empty($categories)):
                ?>
                    <span class="ehs-post-category"><?php echo esc_html($categories[0]->name); ?></span>
                <?php endif; ?>
                <h2 class="ehs-featured-post-title">
                    <a href="<?php echo get_permalink($featured_post->ID); ?>"><?php echo get_the_title($featured_post->ID); ?></a>
                </h2>
                <p class="ehs-featured-post-excerpt">
                    <?php echo wp_trim_words(get_the_excerpt($featured_post->ID), 40, '...'); ?>
                </p>
                <div class="ehs-featured-post-meta">
                    <span class="ehs-post-date">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        <?php echo get_the_date('', $featured_post->ID); ?>
                    </span>
                    <span class="ehs-post-read-time">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <?php echo ehs_get_reading_time($featured_post->ID); ?> min read
                    </span>
                </div>
                <a href="<?php echo get_permalink($featured_post->ID); ?>" class="ehs-btn ehs-btn-solid-primary ehs-btn-md">
                    Read Article
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px; margin-left: 8px;"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ========================================
     POSTS GRID SECTION
     ======================================== -->
<section class="ehs-posts-section">
    <div class="container">
        <?php if ($featured_post): ?>
            <h2 class="ehs-posts-section-title">Latest Articles</h2>
        <?php endif; ?>

        <?php if (have_posts()): ?>
            <div class="ehs-posts-grid">
                <?php
                while (have_posts()):
                    the_post();
                    // Skip the featured post
                    if ($featured_post && get_the_ID() === $featured_post->ID) {
                        continue;
                    }
                ?>
                    <article class="ehs-post-card">
                        <?php if (has_post_thumbnail()): ?>
                            <div class="ehs-post-card-image">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium_large'); ?>
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="ehs-post-card-image ehs-post-card-image-placeholder">
                                <a href="<?php the_permalink(); ?>">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 18h6"></path>
                                        <path d="M10 22h4"></path>
                                        <path d="M15.09 14c.18-.98.65-1.74 1.41-2.5A4.65 4.65 0 0 0 18 8 6 6 0 0 0 6 8c0 1 .23 2.23 1.5 3.5A4.61 4.61 0 0 1 8.91 14"></path>
                                    </svg>
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="ehs-post-card-content">
                            <?php
                            $categories = get_the_category();
                            if (!empty($categories)):
                            ?>
                                <span class="ehs-post-category"><?php echo esc_html($categories[0]->name); ?></span>
                            <?php endif; ?>

                            <h3 class="ehs-post-card-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>

                            <p class="ehs-post-card-excerpt">
                                <?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?>
                            </p>

                            <div class="ehs-post-card-meta">
                                <span class="ehs-post-date">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                    <?php echo get_the_date(); ?>
                                </span>
                            </div>

                            <a href="<?php the_permalink(); ?>" class="ehs-post-card-link">
                                Read More
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                            </a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <!-- Pagination -->
            <div class="ehs-pagination">
                <?php
                echo paginate_links(array(
                    'prev_text' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg> Previous',
                    'next_text' => 'Next <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>',
                    'type' => 'list',
                ));
                ?>
            </div>

        <?php else: ?>
            <div class="ehs-no-posts">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 18h6"></path>
                    <path d="M10 22h4"></path>
                    <path d="M15.09 14c.18-.98.65-1.74 1.41-2.5A4.65 4.65 0 0 0 18 8 6 6 0 0 0 6 8c0 1 .23 2.23 1.5 3.5A4.61 4.61 0 0 1 8.91 14"></path>
                </svg>
                <h2>No Insights Found</h2>
                <p>We're working on new content. Check back soon for expert insights on environmental health and safety.</p>
                <a href="<?php echo home_url('/'); ?>" class="ehs-btn ehs-btn-solid-primary ehs-btn-md">Return Home</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ========================================
     NEWSLETTER/CTA SECTION
     ======================================== -->
<section class="ehs-insights-cta">
    <div class="container">
        <div class="ehs-insights-cta-content">
            <h2>Stay Informed on EHS Best Practices</h2>
            <p>Get the latest insights on environmental health, safety regulations, and compliance strategies delivered to your inbox.</p>
            <a href="<?php echo home_url('/contact/'); ?>" class="ehs-btn ehs-btn-solid-secondary ehs-btn-lg">
                Contact Our Experts
            </a>
        </div>
    </div>
</section>

<?php
get_footer();

/**
 * Calculate estimated reading time for a post
 */
if (!function_exists('ehs_get_reading_time')) {
    function ehs_get_reading_time($post_id = null) {
        $post_id = $post_id ? $post_id : get_the_ID();
        $content = get_post_field('post_content', $post_id);
        $word_count = str_word_count(strip_tags($content));
        $reading_time = ceil($word_count / 200); // Average reading speed
        return max(1, $reading_time);
    }
}
