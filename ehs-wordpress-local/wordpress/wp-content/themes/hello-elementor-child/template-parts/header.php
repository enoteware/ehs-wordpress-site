<?php
/**
 * The template for displaying header.
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
		$header_location = $locations_manager->get_location( 'header' );
		if ( $header_location ) {
			return;
		}
	}
}
*/

$site_name = get_bloginfo( 'name' );
$tagline   = get_bloginfo( 'description', 'display' );

// Get navigation menu with mega menu walker
$header_nav_menu = wp_nav_menu( [
	'theme_location' => 'menu-1',
	'fallback_cb' => false,
	'container' => false,
	'echo' => false,
	'menu_class' => 'ehs-header-nav-menu',
	'walker' => new EHS_Mega_Menu_Walker(),
] );

// Phone number and contact URL (from Site Options)
$phone_number = ehs_get_option('phone');
$contact_url = esc_url( home_url( '/contact/' ) );
?>

<header id="site-header" class="ehs-header">
	<div class="ehs-header-container">
		<div class="ehs-header-grid">
			
			<!-- Logo Column -->
			<div class="ehs-header-logo">
				<?php
				if ( has_custom_logo() ) {
					the_custom_logo();
				} elseif ( $site_name ) {
					?>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr__( 'Home', 'hello-elementor' ); ?>" rel="home">
						<?php echo esc_html( $site_name ); ?>
					</a>
					<?php if ( $tagline ) : ?>
					<p class="site-description">
						<?php echo esc_html( $tagline ); ?>
					</p>
					<?php endif; ?>
				<?php } ?>
			</div>
			
			<!-- Navigation Column -->
			<?php if ( $header_nav_menu ) : ?>
				<nav class="ehs-header-nav" aria-label="<?php echo esc_attr__( 'Main menu', 'hello-elementor' ); ?>">
					<?php
					// PHPCS - escaped by WordPress with "wp_nav_menu"
					echo $header_nav_menu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
				</nav>
			<?php endif; ?>
			
			<!-- Contact Column -->
			<div class="ehs-header-contact">
				<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone_number ) ); ?>" class="ehs-header-phone">
					<?php echo esc_html( $phone_number ); ?>
				</a>
				<div class="ehs-header-button">
					<a href="<?php echo $contact_url; ?>" class="ehs-btn ehs-btn-solid-primary ehs-btn-md">
						Get Started
					</a>
				</div>
			</div>
			
			<!-- Mobile Toggle Button -->
			<button class="ehs-header-mobile-toggle" aria-label="<?php echo esc_attr__( 'Toggle menu', 'hello-elementor' ); ?>" aria-expanded="false" aria-controls="ehs-header-mobile-menu">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			
		</div>
		
		<!-- Mobile Menu -->
		<?php if ( $header_nav_menu ) : ?>
			<nav class="ehs-header-mobile-menu" id="ehs-header-mobile-menu" aria-label="<?php echo esc_attr__( 'Mobile menu', 'hello-elementor' ); ?>">
				<?php
				// Mobile menu - same menu but with mobile classes (no walker for mobile)
				$mobile_nav_menu = wp_nav_menu( [
					'theme_location' => 'menu-1',
					'fallback_cb' => false,
					'container' => false,
					'echo' => false,
					'menu_class' => 'ehs-header-mobile-nav-menu',
					'walker' => new EHS_Mega_Menu_Walker(), // Use same walker for consistency
				] );
				// PHPCS - escaped by WordPress with "wp_nav_menu"
				echo $mobile_nav_menu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
				<div class="ehs-header-button">
					<a href="<?php echo $contact_url; ?>" class="ehs-btn ehs-btn-solid-primary ehs-btn-md">
						Get Started
					</a>
				</div>
			</nav>
		<?php endif; ?>
		
	</div>
</header>

<script>
(function() {
	// Header scroll behavior - adds 'scrolled' class for compact header
	var header = document.getElementById('site-header');
	var scrollThreshold = 50;
	var lastScrollY = 0;
	var ticking = false;

	function updateHeader() {
		if (window.scrollY > scrollThreshold) {
			header.classList.add('scrolled');
		} else {
			header.classList.remove('scrolled');
		}
		ticking = false;
	}

	function onScroll() {
		lastScrollY = window.scrollY;
		if (!ticking) {
			window.requestAnimationFrame(updateHeader);
			ticking = true;
		}
	}

	if (header) {
		window.addEventListener('scroll', onScroll, { passive: true });
		// Initial check
		updateHeader();
	}

	// Mobile menu toggle functionality
	var toggle = document.querySelector('.ehs-header-mobile-toggle');
	var menu = document.querySelector('.ehs-header-mobile-menu');

	if (toggle && menu) {
		toggle.addEventListener('click', function() {
			var isExpanded = this.getAttribute('aria-expanded') === 'true';
			this.setAttribute('aria-expanded', !isExpanded);
			menu.classList.toggle('active');
		});

		// Close menu when clicking outside
		document.addEventListener('click', function(event) {
			if (!toggle.contains(event.target) && !menu.contains(event.target)) {
				toggle.setAttribute('aria-expanded', 'false');
				menu.classList.remove('active');
			}
		});

		// Close menu when clicking a link
		var menuLinks = menu.querySelectorAll('a');
		menuLinks.forEach(function(link) {
			link.addEventListener('click', function() {
				toggle.setAttribute('aria-expanded', 'false');
				menu.classList.remove('active');
			});
		});
	}
})();
</script>
