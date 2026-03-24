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

// All services ordered by built-in menu_order; group by service_section for section headers (set via Settings > Services Order sync).
$all_services = get_posts(array(
    'post_type'      => 'services',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
));
$by_section = array();
$section_order = array();
foreach (ehs_services_display_order() as $row) {
    $section_order[] = $row['title'];
}
foreach ($all_services as $service) {
    $section = get_post_meta($service->ID, 'service_section', true);
    if ($section === '') {
        $section = _x('Other Services', 'Archive section for services with no service_section', 'hello-elementor-child');
    }
    if (!isset($by_section[$section])) {
        $by_section[$section] = array();
        if (!in_array($section, $section_order, true)) {
            $section_order[] = $section;
        }
    }
    $by_section[$section][] = $service;
}
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

        <?php if (!empty($by_section)): ?>
            <?php foreach ($section_order as $section_title): if (empty($by_section[$section_title])) { continue; } $services = $by_section[$section_title]; ?>
                <div class="service-category-section">
                    <h3 class="service-category-title">
                        <?php echo esc_html($section_title); ?>
                    </h3>
                    <div class="service-related__grid">
                        <?php foreach ($services as $service):
                            setup_postdata($service);
                            echo ehs_render_service_card($service);
                        endforeach;
                        wp_reset_postdata();
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="service-archive-empty">
                No services found. Please check back soon.
            </p>
        <?php endif; ?>
    </div>
</section>

<!-- ========================================
     CTA SECTION
     ======================================== -->
<?php ehs_unified_cta(); ?>

<?php
get_footer();