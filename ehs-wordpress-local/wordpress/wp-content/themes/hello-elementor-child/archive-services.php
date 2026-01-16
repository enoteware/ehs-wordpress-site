<?php
/**
 * Services Archive Template
 * Displays all services in an organized grid layout
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get all services, organized by category
$service_categories = get_terms(array(
    'taxonomy' => 'service_category',
    'hide_empty' => true,
));

// Get all services
$all_services = get_posts(array(
    'post_type' => 'services',
    'posts_per_page' => -1,
    'orderby' => 'menu_order',
    'order' => 'ASC',
    'post_status' => 'publish',
));
?>

<!-- ========================================
     HERO SECTION
     ======================================== -->
<section class="ehs-hero-section" style="background-image: url('<?php echo get_stylesheet_directory_uri(); ?>/assets/images/hero-background.jpg');">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Our EHS Services</h1>
        <p class="hero-subheadline">Comprehensive environmental health and safety solutions for California and federal projects.</p>
    </div>
</section>

<!-- ========================================
     SERVICES GRID SECTION
     ======================================== -->
<section class="ehs-services-section">
    <div class="service-container">
        <h2>Our EHS Services</h2>
        <p class="service-archive-intro">
            Comprehensive environmental health and safety solutions tailored to your California and federal project needs.
        </p>

        <?php if (!empty($service_categories) && !is_wp_error($service_categories)): ?>
            <!-- Services organized by category -->
            <?php foreach ($service_categories as $category): 
                // Get services in this category
                $category_services = get_posts(array(
                    'post_type' => 'services',
                    'posts_per_page' => -1,
                    'orderby' => 'menu_order',
                    'order' => 'ASC',
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'service_category',
                            'field' => 'term_id',
                            'terms' => $category->term_id,
                        ),
                    ),
                ));

                if (empty($category_services)) {
                    continue;
                }
            ?>
                <div class="service-category-section">
                    <h3 class="service-category-title">
                        <?php echo esc_html($category->name); ?>
                    </h3>
                    <?php if ($category->description): ?>
                        <p class="service-category-description">
                            <?php echo esc_html($category->description); ?>
                        </p>
                    <?php endif; ?>

                    <div class="service-related__grid">
                        <?php foreach ($category_services as $service): 
                            setup_postdata($service);
                            echo ehs_render_service_card($service);
                        endforeach; 
                        wp_reset_postdata();
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php
            // Get services without a category
            $uncategorized_services = array();
            foreach ($all_services as $service) {
                $service_cats = wp_get_post_terms($service->ID, 'service_category');
                if (empty($service_cats) || is_wp_error($service_cats)) {
                    $uncategorized_services[] = $service;
                }
            }

            if (!empty($uncategorized_services)):
            ?>
                <div class="service-category-section">
                    <h3 class="service-category-title">
                        Other Services
                    </h3>
                    <div class="service-related__grid">
                        <?php foreach ($uncategorized_services as $service): 
                            setup_postdata($service);
                            echo ehs_render_service_card($service);
                        endforeach; 
                        wp_reset_postdata();
                        ?>
                    </div>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- No categories - show all services in one grid -->
            <div class="service-related__grid">
                <?php 
                if (!empty($all_services)):
                    foreach ($all_services as $service): 
                        setup_postdata($service);
                        echo ehs_render_service_card($service);
                    endforeach; 
                    wp_reset_postdata();
                else:
                ?>
                    <p class="service-archive-empty">
                        No services found. Please check back soon.
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ========================================
     CTA SECTION
     ======================================== -->
<?php ehs_unified_cta(); ?>

<?php
get_footer();