<?php
/**
 * Credentials Archive Template
 * Displays all credentials in an organized grid layout following the design system
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get all credentials, organized by category
$all_credentials = get_posts(array(
    'post_type' => 'credentials',
    'posts_per_page' => -1,
    'orderby' => 'meta_value_num',
    'meta_key' => 'credential_order',
    'order' => 'ASC',
    'post_status' => 'publish',
));

// Organize credentials by category
$credentials_by_category = array(
    'Professional Certification' => array(),
    'Business Designation' => array(),
    'License' => array(),
    'Affiliation' => array(),
    'Other' => array(),
);

foreach ($all_credentials as $credential) {
    $category = get_post_meta($credential->ID, 'credential_category', true);
    if ($category && isset($credentials_by_category[$category])) {
        $credentials_by_category[$category][] = $credential;
    } else {
        $credentials_by_category['Other'][] = $credential;
    }
}

// Remove empty categories
$credentials_by_category = array_filter($credentials_by_category, function($creds) {
    return !empty($creds);
});
?>

<!-- ========================================
     HERO SECTION
     ======================================== -->
<section class="ehs-hero-section" style="background-image: url('<?php echo get_stylesheet_directory_uri(); ?>/assets/images/hero-background.jpg');">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Our Credentials & Certifications</h1>
        <p class="hero-subheadline">Industry-leading certifications and professional credentials demonstrating our expertise in environmental health and safety consulting.</p>
    </div>
</section>

<!-- ========================================
     CREDENTIALS GRID SECTION
     ======================================== -->
<section class="ehs-credentials-section">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 80px 20px;">
        <h2>Professional Credentials</h2>
        <p style="text-align: center; font-size: 1.1rem; color: var(--ehs-dark-gray); max-width: 800px; margin: 0 auto 60px; line-height: 1.8;">
            EHS Analytical holds industry-leading certifications, licenses, and professional designations that demonstrate our commitment to excellence and regulatory compliance.
        </p>

        <?php if (!empty($credentials_by_category)): ?>
            <?php 
            // Category display order
            $category_order = array(
                'Professional Certification',
                'Business Designation',
                'License',
                'Affiliation',
                'Other'
            );
            
            foreach ($category_order as $category_name):
                if (!isset($credentials_by_category[$category_name]) || empty($credentials_by_category[$category_name])) {
                    continue;
                }
                
                $category_credentials = $credentials_by_category[$category_name];
            ?>
                <div style="margin-bottom: 80px;">
                    <h3 style="font-family: 'Maven Pro', sans-serif; font-weight: 700; font-size: 2rem; color: var(--ehs-navy); margin-bottom: 40px; text-align: center;">
                        <?php echo esc_html($category_name); ?>
                    </h3>
                    
                    <div class="credentials-grid">
                        <?php foreach ($category_credentials as $credential): 
                            setup_postdata($credential);
                            ehs_render_credential_card($credential);
                        endforeach; 
                        wp_reset_postdata();
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php else: ?>
            <!-- No credentials found -->
            <div class="credentials-grid">
                <p style="text-align: center; color: var(--ehs-dark-gray); grid-column: 1 / -1; padding: 40px;">
                    No credentials found. Please check back soon.
                </p>
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
