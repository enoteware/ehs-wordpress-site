<?php
/**
 * Template Name: About Page
 * Description: About page template for EHS Analytical - Company story, team, veteran ownership, credentials
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
<section class="ehs-hero-section" style="background-image: url('<?php echo get_stylesheet_directory_uri(); ?>/assets/images/about-background.jpg');">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>About EHS Analytical</h1>
        <p class="hero-subheadline">Veteran-owned. Highly credentialed. California's trusted EHS partner.</p>
    </div>
</section>

<!-- ========================================
     COMPANY STORY SECTION
     ======================================== -->
<section class="ehs-services-section">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <div style="max-width: 800px; margin: 0 auto;">
            <h2>Who We Are</h2>
            <p style="font-size: 1.1rem; line-height: 1.8; color: var(--ehs-dark-gray); margin-bottom: 1.5rem;">
                EHS Analytical is a California-based environmental health and safety consulting firm dedicated to protecting
                people, projects, and the environment. We provide comprehensive EHS solutions tailored to the unique needs
                of California businesses, construction projects, and federal agencies.
            </p>
            <p style="font-size: 1.1rem; line-height: 1.8; color: var(--ehs-dark-gray); margin-bottom: 1.5rem;">
                As a Service-Disabled Veteran-Owned Small Business (SDVOSB), we bring military discipline, precision, and
                commitment to every project. Founded by veterans of the United States Marine Corps and Navy, our team
                understands the importance of rigorous safety standards and unwavering compliance.
            </p>
            <p style="font-size: 1.1rem; line-height: 1.8; color: var(--ehs-dark-gray); margin-bottom: 0;">
                Our certified professionals hold industry-leading credentials including CIH, CSP, CHST, and PMP certifications.
                With over 20 years of experience and 500+ successful projects, we've established ourselves as trusted advisors
                to California's construction, manufacturing, healthcare, and government sectors.
            </p>
        </div>
    </div>
</section>

<!-- ========================================
     VETERAN OWNERSHIP SECTION
     ======================================== -->
<section class="ehs-credentials-section">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <div class="credentials-grid">
            <div class="credentials-content">
                <h2>Veteran-Owned Excellence</h2>
                <p style="font-size: 1.1rem; line-height: 1.8; color: var(--ehs-dark-gray); margin-bottom: 30px;">
                    EHS Analytical Solutions is proudly owned and operated by disabled military veterans with decades of 
                    construction safety and environmental health experience. Our certifications provide real value to your projects.
                </p>

                <div class="metrics-row">
                    <div class="metric">
                        <span class="metric-value">SDVOSB</span>
                        <span class="metric-label">Certified</span>
                    </div>
                    <div class="metric">
                        <span class="metric-value">DVBE</span>
                        <span class="metric-label">Certified</span>
                    </div>
                    <div class="metric">
                        <span class="metric-value">20+</span>
                        <span class="metric-label">Years Experience</span>
                    </div>
                </div>

                <div style="margin-top: 40px;">
                    <h3 style="font-family: 'Maven Pro', sans-serif; font-weight: 700; font-size: 1.75rem; color: var(--ehs-navy); margin-bottom: 20px;">
                        SDVOSB Certification
                    </h3>
                    <p style="font-size: 1rem; line-height: 1.8; color: var(--ehs-dark-gray); margin-bottom: 20px;">
                        Helps federal prime contractors meet small business subcontracting goals on USACE, NAVFAC, VA, and DoD 
                        construction projects. Subcontracting with us demonstrates your commitment to supporting veteran-owned 
                        businesses and improves your past performance ratings.
                    </p>

                    <h3 style="font-family: 'Maven Pro', sans-serif; font-weight: 700; font-size: 1.75rem; color: var(--ehs-navy); margin-bottom: 20px;">
                        DVBE Certification
                    </h3>
                    <p style="font-size: 1rem; line-height: 1.8; color: var(--ehs-dark-gray); margin-bottom: 20px;">
                        Helps contractors meet DVBE participation goals on Caltrans and California state projects. Using DVBE 
                        subcontractors strengthens your bid competitiveness and supports your small business utilization plan.
                    </p>

                    <h3 style="font-family: 'Maven Pro', sans-serif; font-weight: 700; font-size: 1.75rem; color: var(--ehs-navy); margin-bottom: 20px;">
                        Veteran Leadership
                    </h3>
                    <p style="font-size: 1rem; line-height: 1.8; color: var(--ehs-dark-gray); margin-bottom: 0;">
                        Our principals include USMC and U.S. Navy veterans who understand military culture, base access protocols, 
                        and the unique requirements of working on active military installations.
                    </p>
                </div>
            </div>

            <div class="credentials-badges">
                <?php ehs_render_certification_badges(); ?>
            </div>
        </div>
    </div>
</section>

<!-- ========================================
     OUR TEAM SECTION
     ======================================== -->
<section class="ehs-services-section">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <h2>Our Team</h2>
        <p style="text-align: center; font-size: 1.1rem; color: var(--ehs-dark-gray); max-width: 800px; margin: 0 auto 60px;">
            Meet the certified professionals leading EHS Analytical Solutions.
        </p>

        <div class="team-member-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 60px; margin-bottom: 80px;">
            <?php
            // Get team members from post type
            $team_members = ehs_get_team_members();
            
            if (empty($team_members)) {
                echo '<p style="text-align: center; color: var(--ehs-dark-gray);">No team members found.</p>';
            } else {
                foreach ($team_members as $member) {
                    $post_id = $member['id'];
                    $name = $member['name'];
                    $job_title = $member['job_title'];
                    $credentials = $member['certifications'];
                    $email = $member['email'];
                    $phone = $member['phone'];
                    $bio = $member['bio'];
                    $photo = $member['photo'];
                    
                    // Get featured image if photo URL not available
                    if (empty($photo)) {
                        $photo = get_the_post_thumbnail_url($post_id, 'large');
                    }
                    
                    // Fallback to placeholder if no image
                    if (empty($photo)) {
                        $photo = get_stylesheet_directory_uri() . '/assets/images/placeholder-team.jpg';
                    }
                    
                    // Generate alt text
                    $alt_text = $name;
                    if ($job_title) {
                        $alt_text .= ', ' . $job_title;
                    }
                    $alt_text .= ' at EHS Analytical';
                    ?>
                    <div class="service-col team-member-card">
                        <div style="margin-bottom: 30px;">
                            <img src="<?php echo esc_url($photo); ?>" 
                                 alt="<?php echo esc_attr($alt_text); ?>" 
                                 class="team-member-image">
                        </div>
                        <h3 class="team-member-name"><?php echo esc_html($name); ?></h3>
                        <?php if (!empty($credentials)) : ?>
                            <p class="team-member-credentials"><?php echo esc_html($credentials); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($job_title)) : ?>
                            <p class="team-member-title"><?php echo esc_html($job_title); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($bio)) : ?>
                            <?php
                            // Split bio into paragraphs (split by double line breaks)
                            $bio_paragraphs = preg_split('/\n\s*\n/', wp_strip_all_tags($bio));
                            foreach ($bio_paragraphs as $paragraph) {
                                $paragraph = trim($paragraph);
                                if (!empty($paragraph)) {
                                    echo '<p class="team-member-bio">' . esc_html($paragraph) . '</p>';
                                }
                            }
                            ?>
                        <?php endif; ?>
                        <div class="team-member-contact">
                            <?php if (!empty($phone)) : ?>
                                <p>
                                    <strong>Phone:</strong>
                                    <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9]/', '', $phone)); ?>"><?php echo esc_html($phone); ?></a>
                                </p>
                            <?php endif; ?>
                            <?php if (!empty($email)) : ?>
                                <p style="margin-bottom: 0;">
                                    <strong>Email:</strong>
                                    <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>
</section>

<!-- ========================================
     TEAM EXPERTISE SECTION
     ======================================== -->
<section class="ehs-credentials-section">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <h2>Our Expertise</h2>
        <p style="text-align: center; font-size: 1.1rem; color: var(--ehs-dark-gray); max-width: 800px; margin: 0 auto 60px;">
            Our specialized experts include Certified Industrial Hygienists (CIH), Certified Safety Professionals (CSP), 
            Certified Utility Safety Professionals (CUSP), Construction Health Safety Technicians (CHST), Certified Asbestos 
            Consultants (CAC), Safety Engineers, Air Quality Experts, Environmental Specialists, EM 385 Site Safety Health 
            Officers (SSHO), Hazardous Waste Experts, and industry experts.
        </p>

        <div class="service-section-3col">
            <div class="service-col">
                <div class="service-col-icon">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="var(--ehs-navy)" stroke-width="2">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                </div>
                <h3>Industry Credentials</h3>
                <p>
                    Our certified professionals hold CIH, CSP, CHST, PMP, CUSP, and IOSH certifications, ensuring 
                    the highest standards of expertise in every project.
                </p>
            </div>

            <div class="service-col">
                <div class="service-col-icon">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="var(--ehs-navy)" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <h3>Proven Experience</h3>
                <p>
                    With over 20 years of experience and 500+ successful projects across California and nationwide, 
                    we've built a reputation for excellence.
                </p>
            </div>

            <div class="service-col">
                <div class="service-col-icon">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="var(--ehs-navy)" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
                <h3>Compliance Focus</h3>
                <p>
                    We specialize in OSHA, Cal/OSHA, EM 385-1-1, and all applicable safety regulations, ensuring 
                    your projects stay compliant and on schedule.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- ========================================
     FINAL CTA SECTION
     ======================================== -->
<?php ehs_unified_cta(); ?>

<?php
get_footer();