<?php
/**
 * The template for displaying footer.
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
				
				<!-- Social Media Icons (from Site Options) -->
				<div class="ehs-footer-social">
					<?php if ( ! empty( $social_links['facebook'] ) ) : ?>
					<a href="<?php echo esc_url( $social_links['facebook'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="ehs-footer-social-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
					</a>
					<?php endif; ?>
					<?php if ( ! empty( $social_links['instagram'] ) ) : ?>
					<a href="<?php echo esc_url( $social_links['instagram'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="ehs-footer-social-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
					</a>
					<?php endif; ?>
					<?php if ( ! empty( $social_links['twitter'] ) ) : ?>
					<a href="<?php echo esc_url( $social_links['twitter'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Twitter/X" class="ehs-footer-social-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4l11.733 16h4.267l-11.733 -16z"></path><path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772"></path></svg>
					</a>
					<?php endif; ?>
					<?php if ( ! empty( $social_links['linkedin'] ) ) : ?>
					<a href="<?php echo esc_url( $social_links['linkedin'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn" class="ehs-footer-social-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg>
					</a>
					<?php endif; ?>
					<?php if ( ! empty( $social_links['youtube'] ) ) : ?>
					<a href="<?php echo esc_url( $social_links['youtube'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="YouTube" class="ehs-footer-social-link">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"></path><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon></svg>
					</a>
					<?php endif; ?>
				</div>
			</div>
			
			<!-- Contact Information Column -->
			<div class="ehs-footer-section">
				<h4 class="ehs-footer-heading">Get in touch</h4>
				<ul class="ehs-footer-contact">
					<li>
						<span class="ehs-footer-contact-icon">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
						</span>
						<span class="ehs-footer-contact-text">
							<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone_number ) ); ?>">
								<?php echo esc_html( $phone_number ); ?>
							</a>
						</span>
					</li>
					<li>
						<span class="ehs-footer-contact-icon">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
						</span>
						<span class="ehs-footer-contact-text">
							<a href="mailto:<?php echo esc_attr( $email ); ?>">
								<?php echo esc_html( $email ); ?>
							</a>
						</span>
					</li>
					<li>
						<span class="ehs-footer-contact-icon">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
						</span>
						<span class="ehs-footer-contact-text"><?php echo esc_html( $hours ); ?></span>
					</li>
				</ul>
			</div>
			
			<!-- Address Column -->
			<div class="ehs-footer-section">
				<h4 class="ehs-footer-heading">Our address</h4>
				<div class="ehs-footer-address-wrapper">
					<span class="ehs-footer-contact-icon">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
					</span>
					<p class="ehs-footer-address">
						<strong><?php echo esc_html( $company_name ); ?></strong><br />
						<?php echo esc_html( $address_line1 ); ?><br />
						<?php echo esc_html( $address_line2 ); ?><br />
						<?php echo esc_html( $address_city_state ); ?>
					</p>
				</div>
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
