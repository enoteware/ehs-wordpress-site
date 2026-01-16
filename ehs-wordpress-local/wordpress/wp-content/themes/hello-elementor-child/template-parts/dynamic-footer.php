<?php
/**
 * The template for displaying dynamic footer.
 * This is used when Hello Elementor's header/footer experiment is active.
 *
 * @package HelloElementor
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Note: Elementor Theme Builder check removed - using PHP template instead
// If you need to re-enable Elementor templates, uncomment the check below:
/*
if ( did_action( 'elementor/loaded' ) && class_exists( '\ElementorPro\Modules\ThemeBuilder\Module' ) ) {
	$theme_builder_module = \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'theme-builder' );
	if ( $theme_builder_module ) {
		$locations_manager = $theme_builder_module->get_locations_manager();
		$footer_location = $locations_manager->get_location( 'footer' );
		if ( $footer_location ) {
			return;
		}
	}
}
*/

$site_name = get_bloginfo( 'name' );
$tagline   = get_bloginfo( 'description', 'display' );

// Contact information
$phone_number = '(619) 288-3094';
$email = 'adam@ehsanalytical.com';
$hours = '8:00 am - 5:00pm PST';

// Address
$company_name = 'EHS Analytical Solutions, Inc.';
$address_line1 = '6755 Mira Mesa Blvd';
$address_line2 = 'Suite 123-249';
$address_city_state = 'San Diego, CA 92121';

// Social media links (update with actual URLs if needed)
$social_links = [
	'facebook' => '#',
	'instagram' => '#',
	'twitter' => '#',
];

// Certifications
$certifications = [
	[
		'title' => 'Certified Safety Professional® (CSP®)',
		'description' => 'The premiere certification in the safety profession covers a wide range of safety, health, and environmental (SH&E) disciplines and is earned by individuals who demonstrate competency in the prevention of harm to individuals in the workplace.',
		'image' => 'csp_certification_v3-05-11.jpg',
		'link' => '',
	],
	[
		'title' => 'Project Management Professional',
		'description' => 'Wildfire smoke and cleanup presents hazards that employers and workers in affected regions must understand. Smoke from wildfires contains chemicals, gases and fine particles that can harm health. Hazards continue even after fires have been extinguished and cleanup work begins.',
		'image' => 'Sceau_certification_v3-05-1.png',
		'link' => esc_url( home_url( '/industrial-hygiene/' ) ),
	],
	[
		'title' => 'Indoor Air Quality (IAQ)',
		'description' => 'Smoke is made up of a complex mixture of gases and fine particles produced when wood and other organic materials burn. The biggest health threat from smoke is from fine particles.',
		'image' => 'IAQ_final_certification_v3-05-1.jpg',
		'link' => esc_url( home_url( '/fire-and-smoke-assessments/' ) ),
	],
];

// Footer navigation menu
$footer_nav_menu = wp_nav_menu( [
	'theme_location' => 'menu-2',
	'fallback_cb' => false,
	'container' => false,
	'echo' => false,
	'menu_class' => 'ehs-footer-nav',
] );
?>

<footer id="site-footer" class="ehs-footer">
	<div class="ehs-footer-container">
		
		<!-- Certifications Section -->
		<?php if ( ! empty( $certifications ) ) : ?>
			<div class="ehs-footer-section" style="margin-bottom: 60px;">
				<h2 class="ehs-footer-heading" style="text-align: center; margin-bottom: 40px;">Professional Affiliations & Certifications</h2>
				<div class="ehs-footer-grid" style="grid-template-columns: repeat(3, 1fr);">
					<?php foreach ( $certifications as $cert ) : 
						$image_url = content_url( 'uploads/2019/11/' . $cert['image'] );
					?>
						<div class="ehs-footer-section" style="text-align: center;">
							<?php if ( ! empty( $cert['link'] ) ) : ?>
								<a href="<?php echo esc_url( $cert['link'] ); ?>" target="_blank" rel="nofollow noopener noreferrer">
									<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $cert['title'] ); ?>" style="max-width: 80%; height: auto; margin-bottom: 20px;" />
								</a>
							<?php else : ?>
								<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $cert['title'] ); ?>" style="max-width: 80%; height: auto; margin-bottom: 20px;" />
							<?php endif; ?>
							<h3 class="ehs-footer-heading" style="font-size: 1.1rem; margin-bottom: 12px;">
								<?php if ( ! empty( $cert['link'] ) ) : ?>
									<a href="<?php echo esc_url( $cert['link'] ); ?>" target="_blank" rel="nofollow noopener noreferrer" class="ehs-footer-link">
										<?php echo esc_html( $cert['title'] ); ?>
									</a>
								<?php else : ?>
									<?php echo esc_html( $cert['title'] ); ?>
								<?php endif; ?>
							</h3>
							<p class="ehs-footer-text" style="font-size: 0.95rem;"><?php echo esc_html( $cert['description'] ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
		
		<!-- Main Footer Content -->
		<div class="ehs-footer-grid">
			
			<!-- Logo and Tagline Column -->
			<div class="ehs-footer-section">
				<div class="ehs-footer-logo">
					<?php
					if ( has_custom_logo() ) {
						the_custom_logo();
					} else {
						?>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
							<?php echo esc_html( $site_name ); ?>
						</a>
						<?php
					}
					?>
				</div>
				<?php if ( $tagline ) : ?>
					<p class="ehs-footer-text"><?php echo esc_html( $tagline ); ?></p>
				<?php endif; ?>
				
				<!-- Social Media Icons -->
				<div style="margin-top: 20px;">
					<a href="<?php echo esc_url( $social_links['facebook'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook" style="color: rgba(255, 255, 255, 0.71); font-size: 25px; margin-right: 30px; text-decoration: none; transition: color 0.3s ease;">
						<span class="dashicons dashicons-facebook-alt"></span>
					</a>
					<a href="<?php echo esc_url( $social_links['instagram'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram" style="color: rgba(255, 255, 255, 0.71); font-size: 25px; margin-right: 30px; text-decoration: none; transition: color 0.3s ease;">
						<span class="dashicons dashicons-instagram"></span>
					</a>
					<a href="<?php echo esc_url( $social_links['twitter'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Twitter" style="color: rgba(255, 255, 255, 0.71); font-size: 25px; text-decoration: none; transition: color 0.3s ease;">
						<span class="dashicons dashicons-twitter"></span>
					</a>
				</div>
			</div>
			
			<!-- Contact Information Column -->
			<div class="ehs-footer-section">
				<h4 class="ehs-footer-heading">Get in touch</h4>
				<ul class="ehs-footer-contact">
					<li>
						<span class="ehs-footer-contact-icon">📞</span>
						<span class="ehs-footer-contact-text">
							<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone_number ) ); ?>">
								<?php echo esc_html( $phone_number ); ?>
							</a>
						</span>
					</li>
					<li>
						<span class="ehs-footer-contact-icon">✉️</span>
						<span class="ehs-footer-contact-text">
							<a href="mailto:<?php echo esc_attr( $email ); ?>">
								<?php echo esc_html( $email ); ?>
							</a>
						</span>
					</li>
					<li>
						<span class="ehs-footer-contact-icon">🕐</span>
						<span class="ehs-footer-contact-text"><?php echo esc_html( $hours ); ?></span>
					</li>
				</ul>
			</div>
			
			<!-- Address Column -->
			<div class="ehs-footer-section">
				<h4 class="ehs-footer-heading">Our address</h4>
				<p class="ehs-footer-address">
					<strong><?php echo esc_html( $company_name ); ?></strong><br />
					<?php echo esc_html( $address_line1 ); ?><br />
					<?php echo esc_html( $address_line2 ); ?><br />
					<?php echo esc_html( $address_city_state ); ?>
				</p>
			</div>
			
		</div>
		
		<!-- Footer Navigation -->
		<?php if ( $footer_nav_menu ) : ?>
			<div class="ehs-footer-divider"></div>
			<nav class="ehs-footer-nav" aria-label="<?php echo esc_attr__( 'Footer menu', 'hello-elementor' ); ?>">
				<?php
				// PHPCS - escaped by WordPress with "wp_nav_menu"
				echo $footer_nav_menu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</nav>
		<?php endif; ?>
		
		<!-- Copyright -->
		<div class="ehs-footer-divider"></div>
		<div class="ehs-footer-copyright">
			<?php
			$copyright_year = date( 'Y' );
			printf(
				esc_html__( '%1$s © %2$s All Rights Reserved', 'hello-elementor' ),
				esc_html( $site_name ),
				esc_html( $copyright_year )
			);
			?>
		</div>
		
	</div>
</footer>
