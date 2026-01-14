<?php
/**
 * The template for displaying header.
 *
 * @package HelloElementor
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Check if Elementor Theme Builder has a header template
// If so, let Elementor handle it and don't render the default header
if ( did_action( 'elementor/loaded' ) && class_exists( '\ElementorPro\Modules\ThemeBuilder\Module' ) ) {
	$theme_builder_module = \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'theme-builder' );
	if ( $theme_builder_module ) {
		$locations_manager = $theme_builder_module->get_locations_manager();
		$header_location = $locations_manager->get_location( 'header' );
		if ( $header_location ) {
			// Elementor Theme Builder header is active, don't render default header
			return;
		}
	}
}

$site_name = get_bloginfo( 'name' );
$tagline   = get_bloginfo( 'description', 'display' );
$header_nav_menu = wp_nav_menu( [
	'theme_location' => 'menu-1',
	'fallback_cb' => false,
	'container' => false,
	'echo' => false,
] );
?>

<header id="site-header" class="site-header">

	<div class="site-branding">
		<?php
		if ( has_custom_logo() ) {
			the_custom_logo();
		} elseif ( $site_name ) {
			?>
			<div class="site-title">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr__( 'Home', 'hello-elementor' ); ?>" rel="home">
					<?php echo esc_html( $site_name ); ?>
				</a>
			</div>
			<?php if ( $tagline ) : ?>
			<p class="site-description">
				<?php echo esc_html( $tagline ); ?>
			</p>
			<?php endif; ?>
		<?php } ?>
	</div>

	<?php if ( $header_nav_menu ) : ?>
		<nav class="site-navigation" aria-label="<?php echo esc_attr__( 'Main menu', 'hello-elementor' ); ?>">
			<?php
			// PHPCS - escaped by WordPress with "wp_nav_menu"
			echo $header_nav_menu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</nav>
	<?php endif; ?>
</header>
