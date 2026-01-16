<?php
/**
 * Template Name: Home Page (New Design)
 * Description: Modern biotech-inspired home page template for EHS Analytical
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<!-- ========================================
     HERO SECTION
     ======================================== -->
<section class="ehs-hero-section" style="background-image: url('<?php echo get_stylesheet_directory_uri(); ?>/assets/images/hero-background.jpg');">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>California's Leading Environmental Health & Safety Consulting Firm</h1>
        <p class="hero-subheadline">Veteran-owned. Highly credentialed. Compliance-focused.</p>
        <div class="hero-cta-group">
            <a href="/contact" class="ehs-btn ehs-btn-solid-green ehs-btn-lg">Get a Free Consultation</a>
            <a href="/services" class="ehs-btn ehs-btn-outline-white ehs-btn-lg">Explore Our Services</a>
        </div>
        <p class="hero-trust-signals">20+ Years Experience • 500+ Projects Completed • SDVOSB Certified</p>
    </div>
</section>

<!-- ========================================
     SERVICES OVERVIEW SECTION
     ======================================== -->
<section class="ehs-services-section">
    <div class="container">
        <h2>Our EHS Services</h2>
        <p style="text-align: center; font-size: 1.1rem; color: var(--ehs-dark-gray); max-width: 800px; margin: 0 auto 60px;">
            Comprehensive environmental health and safety solutions tailored to your California and federal project needs.
        </p>

        <div class="service-related__grid">
            <?php
            // Get featured services from helper function
            $featured_services = ehs_get_featured_services();

            // If no services found, display hardcoded services
            if (empty($featured_services)) {
                $featured_services = array(
                    array(
                        'title' => 'EHS Consulting Services',
                        'excerpt' => 'Expert guidance on environmental health and safety compliance, risk assessment, and program development for California businesses.',
                        'permalink' => '/services/ehs-consulting',
                        'icon' => get_stylesheet_directory_uri() . '/assets/images/icons/consulting-icon.svg'
                    ),
                    array(
                        'title' => 'Indoor Air Quality Testing',
                        'excerpt' => 'Comprehensive air quality assessments to identify contaminants, mold, and indoor pollutants affecting your workspace.',
                        'permalink' => '/services/indoor-air-quality-testing',
                        'icon' => get_stylesheet_directory_uri() . '/assets/images/icons/air-quality-icon.svg'
                    ),
                    array(
                        'title' => 'Asbestos Testing & Abatement',
                        'excerpt' => 'Professional asbestos inspection, testing, and abatement services ensuring regulatory compliance and worker safety.',
                        'permalink' => '/services/asbestos-testing-abatement',
                        'icon' => get_stylesheet_directory_uri() . '/assets/images/icons/asbestos-icon.svg'
                    ),
                    array(
                        'title' => 'Construction Safety Services (SSHO)',
                        'excerpt' => 'Dedicated Site Safety and Health Officers ensuring construction site safety and OSHA compliance on every project.',
                        'permalink' => '/services/construction-safety-ssho',
                        'icon' => get_stylesheet_directory_uri() . '/assets/images/icons/construction-icon.svg'
                    ),
                    array(
                        'title' => 'Lead Compliance Programs',
                        'excerpt' => 'Lead hazard evaluation, compliance consulting, and worker protection programs for California construction projects.',
                        'permalink' => '/services/lead-compliance-programs',
                        'icon' => get_stylesheet_directory_uri() . '/assets/images/icons/lead-icon.svg'
                    ),
                    array(
                        'title' => 'Federal Contracting Services',
                        'excerpt' => 'SDVOSB-certified EHS consulting for federal contracts, military installations, and government agencies.',
                        'permalink' => '/services/federal-contracting-services',
                        'icon' => get_stylesheet_directory_uri() . '/assets/images/icons/federal-icon.svg'
                    ),
                );
            }

            // Render service cards
            foreach ($featured_services as $service) {
                ehs_homepage_render_service_card($service);
            }
            ?>
        </div>
    </div>
</section>

<!-- ========================================
     TRUST & CREDENTIALS SECTION
     ======================================== -->
<section class="ehs-credentials-section">
    <div class="credentials-grid">
        <div class="credentials-content">
            <h2>Trusted by California's Leading Organizations</h2>
            <p>Veteran-owned with industry-leading certifications and proven expertise in environmental health and safety consulting.</p>

            <div class="metrics-row">
                <div class="metric">
                    <span class="metric-value">20+</span>
                    <span class="metric-label">Years Experience</span>
                </div>
                <div class="metric">
                    <span class="metric-value">500+</span>
                    <span class="metric-label">Projects Completed</span>
                </div>
                <div class="metric">
                    <span class="metric-value">SDVOSB</span>
                    <span class="metric-label">Certified</span>
                </div>
            </div>
        </div>

        <div class="credentials-badges">
            <?php ehs_render_certification_badges(); ?>
        </div>
    </div>
</section>

<!-- ========================================
     ABOUT SECTION
     ======================================== -->
<section class="ehs-about-section" style="background-image: url('<?php echo get_stylesheet_directory_uri(); ?>/assets/images/about-background.jpg');">
    <div class="about-overlay"></div>
    <div class="about-content">
        <h2>Who We Are</h2>

        <p>
            EHS Analytical is a California-based environmental health and safety consulting firm dedicated to protecting
            people, projects, and the environment. We provide comprehensive EHS solutions tailored to the unique needs
            of California businesses, construction projects, and federal agencies.
        </p>

        <p>
            As a Service-Disabled Veteran-Owned Small Business (SDVOSB), we bring military discipline, precision, and
            commitment to every project. Founded by veterans of the United States Marine Corps and Navy, our team
            understands the importance of rigorous safety standards and unwavering compliance.
        </p>

        <p>
            Our certified professionals hold industry-leading credentials including CIH, CSP, CHST, and PMP certifications.
            With over 20 years of experience and 500+ successful projects, we've established ourselves as trusted advisors
            to California's construction, manufacturing, healthcare, and government sectors.
        </p>

        <div style="margin-top: 40px;">
            <a href="/about" class="ehs-btn ehs-btn-solid-secondary">Meet Our Team</a>
        </div>
    </div>
</section>

<!-- ========================================
     LATEST RESOURCES SECTION
     ======================================== -->
<section class="ehs-resources-section">
    <div class="container">
        <h2>Latest Insights & Resources</h2>
        <p style="text-align: center; font-size: 1.1rem; color: var(--ehs-dark-gray); max-width: 800px; margin: 0 auto 60px;">
            Stay informed with our latest articles, industry updates, and EHS best practices.
        </p>

        <div class="article-related__grid">
            <?php
            $latest_posts = ehs_get_latest_posts(3);

            if (!empty($latest_posts)) {
                foreach ($latest_posts as $post) {
                    ehs_homepage_render_article_card($post);
                }
            } else {
                // Fallback if no posts exist
                echo '<p style="text-align: center; color: var(--ehs-dark-gray);">Check back soon for the latest updates and insights from our EHS experts.</p>';
            }
            ?>
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <a href="/blog" class="ehs-btn ehs-btn-outline">View All Articles</a>
        </div>
    </div>
</section>

<!-- ========================================
     FINAL CTA SECTION
     ======================================== -->
<?php ehs_unified_cta(); ?>

<?php
get_footer();
