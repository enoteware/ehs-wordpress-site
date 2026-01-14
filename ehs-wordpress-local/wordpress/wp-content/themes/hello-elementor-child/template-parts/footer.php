<?php
/**
 * The template for displaying footer.
 *
 * @package HelloElementor
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Check if Elementor Theme Builder has a footer template
// If so, let Elementor handle it and don't render the default footer
if ( did_action( 'elementor/loaded' ) && class_exists( '\ElementorPro\Modules\ThemeBuilder\Module' ) ) {
	$theme_builder_module = \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'theme-builder' );
	if ( $theme_builder_module ) {
		$locations_manager = $theme_builder_module->get_locations_manager();
		$footer_location = $locations_manager->get_location( 'footer' );
		if ( $footer_location ) {
			// Elementor Theme Builder footer is active, don't render default footer
			return;
		}
	}
}

$footer_nav_menu = wp_nav_menu( [
	'theme_location' => 'menu-2',
	'fallback_cb' => false,
	'container' => false,
	'echo' => false,
] );
?>
<footer id="site-footer" class="site-footer">
	<?php if ( $footer_nav_menu ) : ?>
		<nav class="site-navigation" aria-label="<?php echo esc_attr__( 'Footer menu', 'hello-elementor' ); ?>">
			<?php
			// PHPCS - escaped by WordPress with "wp_nav_menu"
			echo $footer_nav_menu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</nav>
	<?php endif; ?>
</footer>
