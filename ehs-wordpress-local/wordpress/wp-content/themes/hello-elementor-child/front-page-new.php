<?php
/**
 * Template Name: Home Page (New Design 1-28-26)
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
     HERO SECTION - 40/60 split (above legacy hero)
     ======================================== -->
<?php $home_hero_image = home_url( '/wp-content/uploads/2020/07/1-EHS-Staff-Outsourcing-Sitting-down-meeting-and-handshakes-photo-Custom.jpeg' ); ?>
<section class="service-hero-split home-hero-split">
    <div class="service-hero-split__content">
        <h1 class="service-hero-split__heading">Environmental Health & Safety Solutions for California & Federal Projects</h1>
        <p class="service-hero-split__subtext hero-subheadline">Serving Manufacturing, Data Centers, Biotech, Aerospace & Government Contractors Since 2004</p>
        <div class="hero-cta-group">
            <a href="<?php echo esc_url(ehs_get_page_url('contact')); ?>" class="ehs-btn ehs-btn-solid-green ehs-btn-lg">Get a Free Consultation</a>
            <a href="<?php echo esc_url(get_post_type_archive_link('services')); ?>" class="ehs-btn ehs-btn-solid-secondary ehs-btn-lg">Explore Our Services</a>
            <a href="tel:6192883094" class="ehs-btn ehs-btn-solid-primary ehs-btn-md">Call (619) 288-3094</a>
        </div>
    </div>
    <div class="service-hero-split__media">
        <img src="<?php echo esc_url($home_hero_image); ?>" alt="EHS Analytical - Environmental Health & Safety Solutions" class="service-hero-split__img" />
    </div>
</section>

<!-- ========================================
     HERO BELOW - badges & trust signals
     ======================================== -->
<?php
$hero_below_icons = array(
    get_stylesheet_directory_uri() . '/assets/service-icons/shield-check.svg',
    get_stylesheet_directory_uri() . '/assets/service-icons/users.svg',
    get_stylesheet_directory_uri() . '/assets/service-icons/calendar.svg',
    get_stylesheet_directory_uri() . '/assets/service-icons/road.svg',
);
$hero_badges = array(
    'SDVOSB & DVBE Certified',
    'CIH, CSP, CAC, CHST & CUSP Professionals',
    '20+ Years Experience',
    'California & Nationwide',
);
?>
<div class="hero-below">
    <div class="container">
        <div class="hero-badges">
            <?php foreach ( $hero_badges as $i => $label ) : ?>
            <span class="hero-badge">
                <img src="<?php echo esc_url( $hero_below_icons[ $i ] ); ?>" alt="" class="hero-badge__icon" width="20" height="20" aria-hidden="true" />
                <?php echo esc_html( $label ); ?>
            </span>
            <?php endforeach; ?>
        </div>
        <p class="hero-trust-signals">Manufacturing • Data Centers • Biotech • Construction • Federal Contractors • Cal/OSHA Experts</p>
    </div>
</div>

<?php /*
<!-- ========================================
     HERO SECTION (legacy - full width with overlay, commented out)
     ======================================== -->
<section class="ehs-hero-section" style="background-image: url('<?php echo get_stylesheet_directory_uri(); ?>/assets/images/hero-background.jpg');">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Environmental Health & Safety Solutions for California & Federal Projects</h1>
        <p class="hero-subheadline">Serving Manufacturing, Data Centers, Biotech, Aerospace & Government Contractors Since 2004</p>
        <div class="hero-cta-group">
            <a href="<?php echo esc_url(ehs_get_page_url('contact')); ?>" class="ehs-btn ehs-btn-solid-green ehs-btn-lg">Get a Free Consultation</a>
            <a href="<?php echo esc_url(get_post_type_archive_link('services')); ?>" class="ehs-btn ehs-btn-solid-secondary ehs-btn-lg">Explore Our Services</a>
            <a href="tel:6192883094" class="ehs-btn ehs-btn-solid-primary ehs-btn-md">Call (619) 288-3094</a>
        </div>
        <div class="hero-badges">
            <span class="hero-badge">SDVOSB & DVBE Certified</span>
            <span class="hero-badge">CIH, CSP, CAC, CHST & CUSP Professionals</span>
            <span class="hero-badge">20+ Years Experience</span>
            <span class="hero-badge">California & Nationwide</span>
        </div>
        <p class="hero-trust-signals">Manufacturing • Data Centers • Biotech • Construction • Federal Contractors • Cal/OSHA Experts</p>
    </div>
</section>
*/ ?>

<!-- ========================================
     MISSION SECTION
     ======================================== -->
<section class="ehs-mission-section">
    <div class="container">
        <h2>Our Mission</h2>
        <p>Our mission is to help our clients reduce their EHS operational costs, meet compliance objectives, reduce risk and liability, and achieve long-term efficiencies and solutions. Our safety consultants do the heavy lifting which allows our clients to concentrate on their primary business functions and technologies.</p>
    </div>
</section>

<!-- ========================================
     SERVICES OVERVIEW SECTION
     ======================================== -->
<?php
// Featured and additional services by post ID only (see docs/services-display-order.md).
$featured_card_ids = array(3286, 3275, 3277, 3269, 3285, 3273); // EHS Consulting, Federal, Construction Safety, SSHO, Industrial Hygiene, Caltrans
$additional_ids = array(3283, 3271, 3284); // Mold Testing, Lead Compliance Plan, Indoor Air Quality
$all_ordered = get_posts(array(
    'post_type'      => 'services',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
));
$featured_ids_lookup = array_flip($featured_card_ids);
$additional_ids_lookup = array_flip($additional_ids);
// First 6 posts in menu_order whose ID is in featured set.
$featured_posts = array();
foreach ($all_ordered as $p) {
    if (!isset($featured_ids_lookup[(int) $p->ID])) {
        continue;
    }
    $featured_posts[] = $p;
    if (count($featured_posts) >= 6) {
        break;
    }
}
$featured_ids_used = array();
foreach ($featured_posts as $p) {
    $featured_ids_used[(int) $p->ID] = true;
}
// Up to 3 additional posts in menu_order whose ID is in additional set and not in featured.
$additional_posts = array();
foreach ($all_ordered as $p) {
    $id = (int) $p->ID;
    if (isset($featured_ids_used[$id]) || !isset($additional_ids_lookup[$id])) {
        continue;
    }
    $additional_posts[] = $p;
    if (count($additional_posts) >= 3) {
        break;
    }
}
?>
<section class="ehs-services-section">
    <div class="container">
        <h2>Comprehensive Safety & Environmental Services</h2>
        <p style="text-align: center; font-size: 1.1rem; color: var(--ehs-dark-gray); max-width: 800px; margin: 0 auto 60px;">
            CIH and CSP certified EHS consulting for manufacturing, data centers, biotech, pharmaceutical, aerospace, and construction industries throughout California and nationwide. SDVOSB/DVBE certified for federal and state government projects.
        </p>

        <!-- FEATURED SERVICES - Same 6 cards, order by menu_order -->
        <div class="featured-services-grid">
            <?php
            foreach ($featured_posts as $service) {
                $id = (int) $service->ID;
                $slug = $service->post_name;
                $url = get_permalink($service->ID);
                if ($id === 3286) {
            ?><a href="<?php echo esc_url($url); ?>" class="service-card service-card--featured">
                <?php ehs_homepage_service_card_media($slug, 'ehs_consulting_image', 'EHS Consulting icon', 'Environmental Health & Safety Consulting'); ?>
                <div class="service-card__content">
                    <div class="service-card__badge">Cal/OSHA Experts</div>
                    <h3 class="service-card__title">Environmental Health & Safety Consulting<?php echo ehs_service_card_order_badge($service); ?></h3>
                    <p class="service-card__excerpt">Comprehensive EHS program development, compliance audits, and safety management for manufacturing, biotech, pharmaceutical, and aerospace industries throughout California.</p>
                    <ul class="service-card__highlights">
                        <li>Cal/OSHA compliance experts</li>
                        <li>Safety program development</li>
                        <li>CIH & CSP certified</li>
                        <li>Manufacturing & biotech focus</li>
                    </ul>
                    <span class="service-card__link">Learn More &rarr;</span>
                </div>
            </a><?php
                } elseif ($id === 3275) {
            ?><a href="<?php echo esc_url($url); ?>" class="service-card service-card--featured">
                <?php ehs_homepage_service_card_media($slug, 'federal_contracting_image', 'Federal Contracting Services icon', 'Federal Contracting Services'); ?>
                <div class="service-card__content">
                    <div class="service-card__badge">SDVOSB Certified</div>
                    <h3 class="service-card__title">Federal Contracting Services<?php echo ehs_service_card_order_badge($service); ?></h3>
                    <p class="service-card__excerpt">SDVOSB-certified environmental health and safety services for federal contractors. Help meet small business subcontracting goals on USACE, NAVFAC, and DoD projects.</p>
                    <ul class="service-card__highlights">
                        <li>SDVOSB certification verified</li>
                        <li>Meet subcontracting goals</li>
                        <li>All federal agencies</li>
                        <li>Nationwide coverage</li>
                    </ul>
                    <span class="service-card__link">View Federal Contracting Services &rarr;</span>
                </div>
            </a><?php
                } elseif ($id === 3277) {
            ?><a href="<?php echo esc_url($url); ?>" class="service-card service-card--featured">
                <?php ehs_homepage_service_card_media($slug, 'construction_safety_image', 'Construction Safety Consulting icon', 'Construction Safety Consulting'); ?>
                <div class="service-card__content">
                    <div class="service-card__badge">Cal/OSHA Compliant</div>
                    <h3 class="service-card__title">Construction Safety Consulting<?php echo ehs_service_card_order_badge($service); ?></h3>
                    <p class="service-card__excerpt">Dedicated on-site safety professionals for construction projects. Safety program development, daily inspections, training, and Cal/OSHA compliance for California and federal projects.</p>
                    <ul class="service-card__highlights">
                        <li>On-site safety officers</li>
                        <li>Safety program development</li>
                        <li>Cal/OSHA compliance</li>
                        <li>Federal & state projects</li>
                    </ul>
                    <span class="service-card__link">View Construction Safety Services &rarr;</span>
                </div>
            </a><?php
                } elseif ($id === 3269) {
            ?><a href="<?php echo esc_url($url); ?>" class="service-card service-card--featured">
                <?php ehs_homepage_service_card_media($slug, 'ssho_image', 'SSHO Services icon', 'SSHO Services - Federal Military'); ?>
                <div class="service-card__content">
                    <div class="service-card__badge">EM 385-1-1 Experts</div>
                    <h3 class="service-card__title">SSHO Services - Federal Military<?php echo ehs_service_card_order_badge($service); ?></h3>
                    <p class="service-card__excerpt">Site Safety and Health Officers for USACE, NAVFAC, and DoD construction projects. Full EM 385-1-1 compliance and government liaison support.</p>
                    <ul class="service-card__highlights">
                        <li>EM 385-1-1 compliance experts</li>
                        <li>SDVOSB helps meet small business goals</li>
                        <li>CIH & CSP certified professionals</li>
                        <li>California & nationwide coverage</li>
                    </ul>
                    <span class="service-card__link">Learn More About SSHO Services &rarr;</span>
                </div>
            </a><?php
                } elseif ($id === 3285) {
            ?><a href="<?php echo esc_url($url); ?>" class="service-card service-card--featured">
                <?php ehs_homepage_service_card_media($slug, 'industrial_hygiene_image', 'Industrial Hygiene Services icon', 'Industrial Hygiene Services'); ?>
                <div class="service-card__content">
                    <div class="service-card__badge">CIH Certified</div>
                    <h3 class="service-card__title">Industrial Hygiene Services<?php echo ehs_service_card_order_badge($service); ?></h3>
                    <p class="service-card__excerpt">CIH-certified industrial hygiene assessments, exposure monitoring, air quality testing, and compliance programs for industrial facilities, biotech, pharma, and manufacturing.</p>
                    <ul class="service-card__highlights">
                        <li>CIH certified professionals</li>
                        <li>Exposure assessments</li>
                        <li>Air quality monitoring</li>
                        <li>Compliance programs</li>
                    </ul>
                    <span class="service-card__link">View Industrial Hygiene Services &rarr;</span>
                </div>
            </a><?php
                } elseif ($id === 3273) {
            ?><a href="<?php echo esc_url($url); ?>" class="service-card service-card--featured">
                <?php ehs_homepage_service_card_media($slug, 'caltrans_image', 'Caltrans Construction Safety Services icon', 'Caltrans Construction Safety Services'); ?>
                <div class="service-card__content">
                    <div class="service-card__badge">DVBE #2017031</div>
                    <h3 class="service-card__title">Caltrans Construction Safety Services<?php echo ehs_service_card_order_badge($service); ?></h3>
                    <p class="service-card__excerpt">DVBE-certified safety services for Caltrans highway and bridge construction. Safety Representatives, Lead Compliance Plans, and Work Area Monitoring.</p>
                    <ul class="service-card__highlights">
                        <li>All 12 Caltrans districts served</li>
                        <li>DVBE #2017031 certification</li>
                        <li>Lead Compliance Plan experts</li>
                        <li>Bridge rehabilitation specialists</li>
                    </ul>
                    <span class="service-card__link">Learn More About Caltrans Services &rarr;</span>
                </div>
            </a><?php
                }
            }
            ?>
        </div>

        <!-- ADDITIONAL SERVICES - Same 3 cards, order by menu_order -->
        <h3 style="margin-top: 80px; margin-bottom: 40px; text-align: center; font-size: 1.8rem;">Additional Environmental & Safety Services</h3>
        <div class="service-related__grid">
            <?php
            foreach ($additional_posts as $service) {
                $id = (int) $service->ID;
                $slug = $service->post_name;
                $url = get_permalink($service->ID);
                if ($id === 3283) {
            ?><a href="<?php echo esc_url($url); ?>" class="service-card">
                <?php ehs_homepage_service_card_media($slug, 'environmental_testing_image', 'Environmental Testing icon', 'Environmental Testing Services'); ?>
                <div class="service-card__content">
                    <h3 class="service-card__title">Environmental Testing Services<?php echo ehs_service_card_order_badge($service); ?></h3>
                    <p class="service-card__excerpt">Mold testing, asbestos testing, indoor air quality assessments, water damage evaluations, and fire/smoke damage assessments.</p>
                    <span class="service-card__link">Learn More &rarr;</span>
                </div>
            </a><?php
                } elseif ($id === 3271) {
            ?><a href="<?php echo esc_url($url); ?>" class="service-card">
                <?php ehs_homepage_service_card_media($slug, 'lead_compliance_image', 'Lead Compliance Plan Services icon', 'Lead Compliance Plan Services'); ?>
                <div class="service-card__content">
                    <h3 class="service-card__title">Lead Compliance Plan Services<?php echo ehs_service_card_order_badge($service); ?></h3>
                    <p class="service-card__excerpt">Comprehensive Lead Compliance Plans for Caltrans bridge projects and construction involving lead-containing paint. Cal/OSHA 1532.1 compliant, CIH oversight.</p>
                    <span class="service-card__link">Learn More About Lead Compliance &rarr;</span>
                </div>
            </a><?php
                } elseif ($id === 3284) {
            ?><a href="<?php echo esc_url($url); ?>" class="service-card">
                <?php ehs_homepage_service_card_media($slug, 'environmental_testing_image', 'Indoor Air Quality Testing icon', 'Indoor Air Quality Testing'); ?>
                <div class="service-card__content">
                    <h3 class="service-card__title">Indoor Air Quality Testing<?php echo ehs_service_card_order_badge($service); ?></h3>
                    <p class="service-card__excerpt">Indoor air quality assessments, ventilation evaluations, and contaminant testing for offices, schools, and commercial buildings in San Diego and California.</p>
                    <span class="service-card__link">Learn More &rarr;</span>
                </div>
            </a><?php
                }
            }
            ?>
        </div>
    </div>
</section>

<!-- ========================================
     WHY CHOOSE US SECTION
     ======================================== -->
<section class="why-choose-us-section" style="padding: 80px 32px;">
    <div class="container">
        <h2 style="text-align: center; margin-bottom: 10px;">Why Choose Us</h2>
        <p style="text-align: center; font-size: 1.1rem; color: var(--ehs-dark-gray); margin: 0 auto 40px;">The &ldquo;EHS Analytical Solutions&rdquo; Advantage</p>
        <div class="why-choose-us-grid">
            <div class="why-column">
                <div class="why-column-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                </div>
                <h3>Industry Expertise</h3>
                <p>We provide our clients professionally certified EHS consultants &amp; staff solutions who are experts in manufacturing, data centers, biotech, construction, pharmaceutical, aerospace, and government contracting. Our industry experts provide EHS cost effective solutions and value by limiting your regulatory risk &amp; liability and by reducing EHS operational costs. We help our clients meet complex regulatory requirements and improve safety performance.</p>
            </div>
            <div class="why-column">
                <div class="why-column-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                </div>
                <h3>Professional Credentials</h3>
                <p>Our experts include Certified Industrial Hygienists (CIH), Certified Safety professionals (CSP), Certified Utility Safety Professionals (CUSP), Construction Health and Safety Technicians (CHST), Certified Asbestos Consultants (CAC), Safety Engineers, Air Quality Experts, Environmental Specialists, and industry safety experts.</p>
            </div>
            <div class="why-column">
                <div class="why-column-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                </div>
                <h3>Experienced Team</h3>
                <p>Our EHS consultants are friendly, enjoyable to work with and have proven experience. Most of our staff have over 15 years of professional experience.</p>
            </div>
            <div class="why-column">
                <div class="why-column-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                </div>
                <h3>Geographic Coverage</h3>
                <p>We are available to work anywhere throughout the United States and are based out of San Diego. We will provide you efficient, high quality and affordable support.</p>
            </div>
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
<section class="ehs-about-section">
    <div class="about-overlay"></div>
    <div class="about-content">
        <h2>Who We Are</h2>
        <p>
            EHS Analytical Solutions is a California-based environmental health and safety consulting firm dedicated to protecting people, projects, and the environment. Since 2004, we've provided comprehensive EHS solutions tailored to the unique needs of California's manufacturing, data centers, aerospace industries, and construction projects.
        </p>
        <p>
            As a Service-Disabled Veteran-Owned Small Business (SDVOSB), we bring military discipline, precision, and commitment to every project. Founded by veterans of the United States Marine Corps and Navy, our team understands the importance of rigorous safety standards and unwavering compliance.
        </p>
        <p>
            Our certified professionals hold industry-leading credentials including CIH, CSP, CHST, and PMP certifications.
            With over 20 years of experience and 500+ successful projects, we've established ourselves as trusted advisors
            to California's construction, manufacturing, healthcare, and government sectors.
        </p>
        <div class="about-stats">
            <div class="stat">
                <div class="stat-value">100+</div>
                <div class="stat-label">Lead Compliance Plans Completed</div>
            </div>
            <div class="stat">
                <div class="stat-value">12</div>
                <div class="stat-label">Caltrans Districts Served</div>
            </div>
            <div class="stat">
                <div class="stat-value">15+</div>
                <div class="stat-label">Years Federal Projects</div>
            </div>
            <div class="stat">
                <div class="stat-value">20+</div>
                <div class="stat-label">Military Installations</div>
            </div>
        </div>
        <div style="margin-top: 40px;">
            <?php
            $about_page = get_page_by_path('about') ?: get_page_by_path('about-us');
            $about_url = $about_page ? get_permalink($about_page) : ehs_get_page_url('about');
            ?>
            <a href="<?php echo esc_url($about_url); ?>" class="ehs-btn ehs-btn-solid-secondary">Meet Our Team</a>
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
                echo '<p style="text-align: center; color: var(--ehs-dark-gray);">Check back soon for the latest updates and insights from our EHS experts.</p>';
            }
            ?>
        </div>
        <div style="text-align: center; margin-top: 40px;">
            <a href="<?php echo esc_url(ehs_get_page_url('insights')); ?>" class="ehs-btn ehs-btn-outline">View All Articles</a>
        </div>
    </div>
</section>

<!-- ========================================
     FINAL CTA SECTION (Homepage-specific)
     ======================================== -->
<section class="service-cta">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <h2>Ready to Partner with California's Leading EHS Consulting Firm?</h2>
        <p>Whether you need safety consulting for manufacturing operations, construction site safety management, industrial hygiene assessments, or federal contracting support, our certified professionals deliver expert solutions throughout California and nationwide.</p>
        <div class="service-cta-buttons">
            <a href="<?php echo esc_url(ehs_get_page_url('contact')); ?>" class="ehs-btn ehs-btn-solid-secondary ehs-btn-lg">Request a Quote</a>
            <a href="tel:6192883094" class="ehs-btn ehs-btn-outline-white ehs-btn-lg">Call (619) 288-3094</a>
        </div>
        <p style="margin-top: 20px; font-size: 1rem; color: rgba(255,255,255,0.9);">
            We respond to all inquiries within 24 hours.
        </p>
    </div>
</section>

<?php
get_footer();
