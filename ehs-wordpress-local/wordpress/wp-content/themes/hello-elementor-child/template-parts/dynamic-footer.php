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

// Contact information (from Site Options)
$phone_number = ehs_get_option('phone');
$email = ehs_get_option('email_secondary');
$hours = ehs_get_hours();

// Address (from Site Options)
$company_name = ehs_get_option('company_name');
$address_line1 = ehs_get_option('address_line1');
$address_line2 = ehs_get_option('address_line2');
$address_city_state = ehs_get_city_state_zip();

// Social media links (from Site Options)
$social_links = ehs_get_social_links(false);

// Featured Credentials (from Site Options - relationship field to Credentials CPT)
$certifications = ehs_get_credential_cards();

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
		
		<!-- Certifications Section (from Featured Credentials in Site Options) -->
		<?php if ( ! empty( $certifications ) ) : ?>
			<div class="ehs-footer-section" style="margin-bottom: 60px;">
				<h2 class="ehs-footer-heading" style="text-align: center; margin-bottom: 40px;">Professional Affiliations & Certifications</h2>
				<div class="ehs-footer-grid" style="grid-template-columns: repeat(<?php echo min( count( $certifications ), 3 ); ?>, 1fr);">
					<?php foreach ( $certifications as $cert ) : ?>
						<div class="ehs-footer-section" style="text-align: center;">
							<?php if ( ! empty( $cert['image'] ) ) : ?>
								<?php if ( ! empty( $cert['link'] ) ) : ?>
									<a href="<?php echo esc_url( $cert['link'] ); ?>">
										<img src="<?php echo esc_url( $cert['image'] ); ?>" alt="<?php echo esc_attr( $cert['title'] ); ?>" style="max-width: 80%; height: auto; margin-bottom: 20px;" />
									</a>
								<?php else : ?>
									<img src="<?php echo esc_url( $cert['image'] ); ?>" alt="<?php echo esc_attr( $cert['title'] ); ?>" style="max-width: 80%; height: auto; margin-bottom: 20px;" />
								<?php endif; ?>
							<?php endif; ?>
							<h3 class="ehs-footer-heading" style="font-size: 1.1rem; margin-bottom: 12px;">
								<?php if ( ! empty( $cert['link'] ) ) : ?>
									<a href="<?php echo esc_url( $cert['link'] ); ?>" class="ehs-footer-link">
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
