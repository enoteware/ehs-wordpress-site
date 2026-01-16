<?php
/**
 * Single Blog Post Template
 * Displays individual blog posts with a clean reading experience
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get post data
$categories = get_the_category();
$tags = get_the_tags();
$reading_time = ehs_get_reading_time();

// Generate ToC from content headings (reuse service TOC functions)
$raw_content = get_the_content();
$raw_content = apply_filters('the_content', $raw_content);
$toc_data = ehs_service_toc_generate($raw_content);
$content_with_ids = ehs_service_toc_inject_ids($raw_content, $toc_data);

// Get related posts (same category)
$related_posts = array();
if (!empty($categories)) {
    $related_query = new WP_Query(array(
        'category__in' => wp_list_pluck($categories, 'term_id'),
        'post__not_in' => array(get_the_ID()),
        'posts_per_page' => 3,
        'orderby' => 'rand',
    ));
    if ($related_query->have_posts()) {
        $related_posts = $related_query->posts;
    }
    wp_reset_postdata();
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('ehs-single-post'); ?>>

    <!-- ========================================
         POST HEADER
         ======================================== -->
    <header class="ehs-post-header">
        <div class="ehs-post-header-overlay"></div>
        <div class="ehs-post-header-content">
            <?php if (!empty($categories)): ?>
                <div class="ehs-post-categories">
                    <?php foreach ($categories as $category): ?>
                        <a href="<?php echo get_category_link($category->term_id); ?>" class="ehs-post-category-link">
                            <?php echo esc_html($category->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <h1 class="ehs-post-title"><?php the_title(); ?></h1>

            <div class="ehs-post-meta">
                <span class="ehs-post-author">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    <?php the_author(); ?>
                </span>
                <span class="ehs-post-date">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    <?php echo get_the_date(); ?>
                </span>
                <span class="ehs-post-read-time">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    <?php echo $reading_time; ?> min read
                </span>
            </div>
        </div>
    </header>

    <!-- ========================================
         FEATURED IMAGE
         ======================================== -->
    <?php if (has_post_thumbnail()): ?>
        <div class="ehs-post-featured-image">
            <div class="container">
                <?php the_post_thumbnail('full'); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- ========================================
         POST CONTENT WITH TOC SIDEBAR
         ======================================== -->
    <div class="ehs-post-content-wrapper">
        <div class="container">
            <div class="ehs-post-layout <?php echo !empty($toc_data) ? 'ehs-post-layout--has-toc' : ''; ?>">

                <!-- TOC Sidebar -->
                <?php if (!empty($toc_data)): ?>
                    <aside class="ehs-post-sidebar">
                        <?php ehs_service_toc_sidebar($toc_data); ?>
                    </aside>
                <?php endif; ?>

                <!-- Main Content -->
                <div class="ehs-post-main">
                    <div class="ehs-post-content">
                        <?php
                        // Output content with IDs injected for TOC linking
                        echo $content_with_ids;
                        ?>
                    </div>

                    <!-- Tags -->
                    <?php if (!empty($tags)): ?>
                        <div class="ehs-post-tags">
                            <span class="ehs-post-tags-label">Topics:</span>
                            <?php foreach ($tags as $tag): ?>
                                <a href="<?php echo get_tag_link($tag->term_id); ?>" class="ehs-post-tag">
                                    <?php echo esc_html($tag->name); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Share -->
                    <div class="ehs-post-share">
                        <span class="ehs-post-share-label">Share this article:</span>
                        <div class="ehs-post-share-links">
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode(get_permalink()); ?>&title=<?php echo urlencode(get_the_title()); ?>" target="_blank" rel="noopener noreferrer" class="ehs-share-link ehs-share-linkedin" aria-label="Share on LinkedIn">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" target="_blank" rel="noopener noreferrer" class="ehs-share-link ehs-share-twitter" aria-label="Share on Twitter">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4l11.733 16h4.267l-11.733 -16z"></path><path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772"></path></svg>
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" target="_blank" rel="noopener noreferrer" class="ehs-share-link ehs-share-facebook" aria-label="Share on Facebook">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                            </a>
                            <a href="mailto:?subject=<?php echo urlencode(get_the_title()); ?>&body=<?php echo urlencode(get_permalink()); ?>" class="ehs-share-link ehs-share-email" aria-label="Share via Email">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- ========================================
         AUTHOR BIO
         ======================================== -->
    <?php if (get_the_author_meta('description')): ?>
        <section class="ehs-author-section">
            <div class="container">
                <div class="ehs-author-box">
                    <div class="ehs-author-avatar">
                        <?php echo get_avatar(get_the_author_meta('ID'), 120); ?>
                    </div>
                    <div class="ehs-author-info">
                        <h3 class="ehs-author-name">
                            <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>">
                                <?php the_author(); ?>
                            </a>
                        </h3>
                        <p class="ehs-author-bio"><?php the_author_meta('description'); ?></p>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- ========================================
         RELATED POSTS
         ======================================== -->
    <?php if (!empty($related_posts)): ?>
        <section class="ehs-related-posts-section">
            <div class="container">
                <h2 class="ehs-related-posts-title">Related Insights</h2>
                <div class="ehs-posts-grid">
                    <?php foreach ($related_posts as $related): ?>
                        <article class="ehs-post-card">
                            <?php if (has_post_thumbnail($related->ID)): ?>
                                <div class="ehs-post-card-image">
                                    <a href="<?php echo get_permalink($related->ID); ?>">
                                        <?php echo get_the_post_thumbnail($related->ID, 'medium_large'); ?>
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="ehs-post-card-image ehs-post-card-image-placeholder">
                                    <a href="<?php echo get_permalink($related->ID); ?>">
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
                                $related_categories = get_the_category($related->ID);
                                if (!empty($related_categories)):
                                ?>
                                    <span class="ehs-post-category"><?php echo esc_html($related_categories[0]->name); ?></span>
                                <?php endif; ?>

                                <h3 class="ehs-post-card-title">
                                    <a href="<?php echo get_permalink($related->ID); ?>"><?php echo get_the_title($related->ID); ?></a>
                                </h3>

                                <p class="ehs-post-card-excerpt">
                                    <?php echo wp_trim_words(get_the_excerpt($related->ID), 20, '...'); ?>
                                </p>

                                <a href="<?php echo get_permalink($related->ID); ?>" class="ehs-post-card-link">
                                    Read More
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- ========================================
         POST NAVIGATION
         ======================================== -->
    <nav class="ehs-post-navigation">
        <div class="container">
            <?php
            $prev_post = get_previous_post();
            $next_post = get_next_post();
            ?>

            <?php if ($prev_post): ?>
                <a href="<?php echo get_permalink($prev_post->ID); ?>" class="ehs-post-nav-link ehs-post-nav-prev">
                    <span class="ehs-post-nav-label">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        Previous Article
                    </span>
                    <span class="ehs-post-nav-title"><?php echo get_the_title($prev_post->ID); ?></span>
                </a>
            <?php else: ?>
                <div class="ehs-post-nav-placeholder"></div>
            <?php endif; ?>

            <?php if ($next_post): ?>
                <a href="<?php echo get_permalink($next_post->ID); ?>" class="ehs-post-nav-link ehs-post-nav-next">
                    <span class="ehs-post-nav-label">
                        Next Article
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </span>
                    <span class="ehs-post-nav-title"><?php echo get_the_title($next_post->ID); ?></span>
                </a>
            <?php endif; ?>
        </div>
    </nav>

</article>

<!-- ========================================
     CTA SECTION
     ======================================== -->
<section class="ehs-insights-cta">
    <div class="container">
        <div class="ehs-insights-cta-content">
            <h2>Need Expert EHS Guidance?</h2>
            <p>Our certified professionals can help you navigate complex environmental health and safety regulations.</p>
            <a href="<?php echo home_url('/contact/'); ?>" class="ehs-btn ehs-btn-solid-secondary ehs-btn-lg">
                Schedule a Consultation
            </a>
        </div>
    </div>
</section>

<?php
get_footer();
