<?php
/**
 * Template Name: Contact Page
 * Description: Contact page template with contact info, form, and certifications
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
        <h1>Contact Us</h1>
        <p class="hero-subheadline">Get in touch with California's leading EHS consulting firm.</p>
    </div>
</section>

<!-- ========================================
     CONTACT INFORMATION SECTION
     ======================================== -->
<section class="ehs-services-section">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 40px; margin-bottom: 60px;">
            <!-- Phone (from Site Options) -->
            <div class="service-col" style="text-align: center;">
                <div class="service-col-icon">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="var(--ehs-navy)" stroke-width="2">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                    </svg>
                </div>
                <h3>Phone</h3>
                <p style="font-size: 1.1rem; font-weight: 600; color: var(--ehs-navy); margin: 10px 0;">
                    <a href="tel:<?php echo esc_attr( ehs_get_phone(true) ); ?>" style="color: var(--ehs-navy); text-decoration: none;">
                        <?php echo esc_html( ehs_get_option('phone') ); ?>
                    </a>
                </p>
                <p style="font-size: 0.95rem; color: var(--ehs-dark-gray);">
                    <?php echo esc_html( ehs_get_hours() ); ?>
                </p>
            </div>

            <!-- Email (from Site Options) -->
            <div class="service-col" style="text-align: center;">
                <div class="service-col-icon">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="var(--ehs-navy)" stroke-width="2">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                </div>
                <h3>Email</h3>
                <p style="font-size: 1.1rem; font-weight: 600; color: var(--ehs-navy); margin: 10px 0;">
                    <a href="mailto:<?php echo esc_attr( ehs_get_option('email_primary') ); ?>" style="color: var(--ehs-navy); text-decoration: none;">
                        <?php echo esc_html( ehs_get_option('email_primary') ); ?>
                    </a>
                </p>
                <p style="font-size: 0.95rem; color: var(--ehs-dark-gray);">
                    We typically respond within 24 hours
                </p>
            </div>

            <!-- Location (from Site Options) -->
            <div class="service-col" style="text-align: center;">
                <div class="service-col-icon">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="var(--ehs-navy)" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                </div>
                <h3>Location</h3>
                <p style="font-size: 1rem; font-weight: 600; color: var(--ehs-navy); margin: 10px 0;">
                    <?php echo esc_html( ehs_get_option('address_city') ); ?>, <?php echo esc_html( ehs_get_option('address_state') ); ?>
                </p>
                <p style="font-size: 0.95rem; color: var(--ehs-dark-gray);">
                    <?php echo esc_html( ehs_get_option('service_area') ); ?>
                </p>
            </div>
        </div>
    </div>
</section>

<!-- ========================================
     CONTACT FORM SECTION
     ======================================== -->
<section class="ehs-credentials-section">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: start;">
            <!-- Contact Form -->
            <div>
                <h2 style="font-family: 'Maven Pro', sans-serif; text-align: left; margin-bottom: 30px;">
                    Send Us a Message
                </h2>
                <p style="font-size: 1rem; line-height: 1.8; color: var(--ehs-dark-gray); margin-bottom: 40px;">
                    Fill out the form below and we'll get back to you as soon as possible. For urgent matters,
                    please call us directly at <a href="tel:<?php echo esc_attr( ehs_get_phone(true) ); ?>" style="color: var(--ehs-navy); font-weight: 600;"><?php echo esc_html( ehs_get_option('phone') ); ?></a>.
                </p>
                <?php echo ehs_render_contact_form(array(
                    'show_name' => true,
                    'show_phone' => true,
                    'show_company' => true,
                    'submit_text' => 'Send Message',
                )); ?>
            </div>

            <!-- Certifications & Info -->
            <div>
                <h2 style="font-family: 'Maven Pro', sans-serif; text-align: left; margin-bottom: 30px;">
                    Why Choose EHS Analytical?
                </h2>
                
                <div style="margin-bottom: 40px;">
                    <h3 style="font-family: 'Maven Pro', sans-serif; font-weight: 700; font-size: 1.25rem; color: var(--ehs-navy); margin-bottom: 15px;">
                        Service-Disabled Veteran-Owned
                    </h3>
                    <p style="font-size: 1rem; line-height: 1.8; color: var(--ehs-dark-gray); margin-bottom: 0;">
                        As a certified SDVOSB and DVBE, we help contractors meet their small business participation goals 
                        while delivering the highest quality EHS services in the industry.
                    </p>
                </div>

                <div style="margin-bottom: 40px;">
                    <h3 style="font-family: 'Maven Pro', sans-serif; font-weight: 700; font-size: 1.25rem; color: var(--ehs-navy); margin-bottom: 15px;">
                        Industry-Leading Credentials
                    </h3>
                    <p style="font-size: 1rem; line-height: 1.8; color: var(--ehs-dark-gray); margin-bottom: 20px;">
                        Our team holds CIH, CSP, CHST, PMP, CUSP, and IOSH certifications, ensuring expert-level 
                        expertise on every project.
                    </p>
                    <div class="badge-grid" style="grid-template-columns: repeat(3, 1fr); gap: 15px;">
                        <?php 
                        // Show a subset of badges
                        $key_badges = array('CIH', 'CSP', 'CHST', 'PMP', 'SDVOSB', 'DVBE');
                        $all_badges = array(
                            'CIH' => array('name' => 'CIH', 'full_name' => 'Certified Industrial Hygienist', 'image' => 'cih-badge.svg'),
                            'CSP' => array('name' => 'CSP', 'full_name' => 'Certified Safety Professional', 'image' => 'csp-badge.svg'),
                            'CHST' => array('name' => 'CHST', 'full_name' => 'Construction Health and Safety Technician', 'image' => 'chst-badge.svg'),
                            'PMP' => array('name' => 'PMP', 'full_name' => 'Project Management Professional', 'image' => 'pmp-badge.svg'),
                            'SDVOSB' => array('name' => 'SDVOSB', 'full_name' => 'Service-Disabled Veteran-Owned Small Business', 'image' => 'sdvosb-badge.svg'),
                            'DVBE' => array('name' => 'DVBE', 'full_name' => 'Disabled Veteran Business Enterprise', 'image' => 'dvbe-badge.svg'),
                        );
                        $badge_dir = get_stylesheet_directory_uri() . '/assets/images/badges/';
                        foreach ($key_badges as $badge_key):
                            if (isset($all_badges[$badge_key])):
                                $badge = $all_badges[$badge_key];
                        ?>
                            <div class="badge-item">
                                <img src="<?php echo esc_url($badge_dir . $badge['image']); ?>" 
                                     alt="<?php echo esc_attr($badge['full_name']); ?>" 
                                     title="<?php echo esc_attr($badge['full_name']); ?>">
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>

                <div style="margin-bottom: 40px;">
                    <h3 style="font-family: 'Maven Pro', sans-serif; font-weight: 700; font-size: 1.25rem; color: var(--ehs-navy); margin-bottom: 15px;">
                        Proven Track Record
                    </h3>
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        <li style="padding: 10px 0; border-bottom: 1px solid #e0e0e0; font-size: 1rem; color: var(--ehs-dark-gray);">
                            <strong style="color: var(--ehs-navy);">20+ Years</strong> of experience
                        </li>
                        <li style="padding: 10px 0; border-bottom: 1px solid #e0e0e0; font-size: 1rem; color: var(--ehs-dark-gray);">
                            <strong style="color: var(--ehs-navy);">500+ Projects</strong> completed successfully
                        </li>
                        <li style="padding: 10px 0; border-bottom: 1px solid #e0e0e0; font-size: 1rem; color: var(--ehs-dark-gray);">
                            <strong style="color: var(--ehs-navy);">California & Federal</strong> project expertise
                        </li>
                        <li style="padding: 10px 0; font-size: 1rem; color: var(--ehs-dark-gray);">
                            <strong style="color: var(--ehs-navy);">OSHA, Cal/OSHA, EM 385-1-1</strong> compliance specialists
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========================================
     RESPONSIVE ADJUSTMENTS
     ======================================== -->
<style>
@media (max-width: 992px) {
    .ehs-services-section .container > div[style*="grid-template-columns: repeat(3, 1fr)"] {
        grid-template-columns: 1fr !important;
        gap: 30px !important;
    }
    
    .ehs-credentials-section .container > div[style*="grid-template-columns: 1fr 1fr"] {
        grid-template-columns: 1fr !important;
        gap: 40px !important;
    }
    
    .badge-grid {
        grid-template-columns: repeat(3, 1fr) !important;
    }
}

@media (max-width: 768px) {
    .badge-grid {
        grid-template-columns: repeat(2, 1fr) !important;
    }
}
</style>

<?php
get_footer();