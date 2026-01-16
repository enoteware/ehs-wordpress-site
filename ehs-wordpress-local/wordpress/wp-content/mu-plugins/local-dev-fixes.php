<?php
/**
 * Plugin Name: Local Development Fixes
 * Description: Fixes for local DDEV development environment - suppresses deprecation warnings and notices
 * Version: 1.3.0
 * 
 * Note: Suppresses PHP 8.3 deprecation warnings from plugins (especially iThemes Security Pro and WP Mail SMTP)
 * to prevent "headers already sent" errors in local development.
 */

// Only run in local development
if ( ! defined( 'IS_DDEV_PROJECT' ) || getenv( 'IS_DDEV_PROJECT' ) !== 'true' ) {
	return;
}

// Suppress PHP deprecation warnings, notices, and warnings early (before plugins load)
// This prevents "headers already sent" errors from plugin deprecation warnings
@error_reporting( E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE & ~E_WARNING );
@ini_set( 'display_errors', '0' );
@ini_set( 'display_startup_errors', '0' );
@ini_set( 'log_errors', '0' ); // Don't log errors to avoid cluttering logs
@ini_set( 'error_log', WP_CONTENT_DIR . '/debug.log' );

// Enable output buffering to catch any unexpected output
if ( ! @ob_get_level() ) {
	@ob_start();
}

// Suppress specific WordPress deprecation notices using error handler
set_error_handler( function( $errno, $errstr, $errfile, $errline ) {
	// Suppress PHPMailer deprecation warnings
	if ( strpos( $errstr, 'class-phpmailer.php is deprecated' ) !== false ) {
		return true; // Suppress this error
	}
	// Suppress other deprecation warnings in local dev
	if ( $errno === E_DEPRECATED || $errno === E_STRICT || $errno === E_NOTICE || $errno === E_WARNING ) {
		return true; // Suppress these errors
	}
	// Let other errors through
	return false;
}, E_ALL );

// Also suppress via WordPress filters (runs after WordPress loads)
add_filter( 'deprecated_file_trigger_error', '__return_false', 999 );
add_filter( 'deprecated_function_trigger_error', '__return_false', 999 );
add_filter( 'deprecated_argument_trigger_error', '__return_false', 999 );
add_filter( 'deprecated_hook_trigger_error', '__return_false', 999 );

/**
 * Suppress jQuery Migrate console warnings
 * jQuery Migrate is included by WordPress for compatibility but shows console warnings
 * This is harmless but can clutter the console in development
 */
add_action('wp_enqueue_scripts', function() {
    if (!is_admin()) {
        // Override jQuery Migrate's console.warn to suppress migration warnings
        wp_add_inline_script('jquery-migrate', '
            (function() {
                if (typeof console !== "undefined" && console.warn) {
                    var originalWarn = console.warn;
                    console.warn = function() {
                        // Suppress jQuery Migrate warnings
                        if (arguments[0] && typeof arguments[0] === "string" && arguments[0].indexOf("JQMIGRATE") === 0) {
                            return;
                        }
                        originalWarn.apply(console, arguments);
                    };
                }
            })();
        ', 'after');
    }
}, 999);

/**
 * Allow SVG uploads in local DDEV only (admin only).
 * This is for importing a small SVG icon set for Services.
 */
add_filter( 'upload_mimes', function( $mimes ) {
	if ( current_user_can( 'manage_options' ) ) {
		$mimes['svg']  = 'image/svg+xml';
		$mimes['svgz'] = 'image/svg+xml';
	}
	return $mimes;
} );

add_filter( 'wp_check_filetype_and_ext', function( $data, $file, $filename, $mimes ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		return $data;
	}

	$ext = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
	if ( $ext === 'svg' || $ext === 'svgz' ) {
		$data['ext']  = $ext;
		$data['type'] = 'image/svg+xml';
	}

	return $data;
}, 10, 4 );

/**
 * Self-heal Elementor Theme Builder header on local dev.
 * If the Global Header template's `_elementor_data` becomes invalid JSON, Elementor will render no header.
 * This repairs it from a known-good export checked into the repo.
 */
add_action( 'init', function() {
	// Avoid doing any work in non-web contexts.
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		return;
	}

	// Run at most once per 5 minutes.
	if ( get_transient( 'ehs_local_elementor_header_repair_lock' ) ) {
		return;
	}
	set_transient( 'ehs_local_elementor_header_repair_lock', 1, 5 * MINUTE_IN_SECONDS );

	$header_template_id = 37;
	$raw = (string) get_post_meta( $header_template_id, '_elementor_data', true );
	if ( $raw === '' ) {
		return;
	}

	json_decode( $raw, true );
	if ( json_last_error() === JSON_ERROR_NONE ) {
		return;
	}

	$export_path = ABSPATH . '../header-template-prod.json';
	if ( ! file_exists( $export_path ) ) {
		return;
	}

	$export_raw = file_get_contents( $export_path );
	if ( $export_raw === false ) {
		return;
	}

	$export_json = json_decode( $export_raw, true );
	if ( ! is_array( $export_json ) || empty( $export_json['content'] ) || ! is_array( $export_json['content'] ) ) {
		return;
	}

	$elementor_data = wp_json_encode( $export_json['content'] );
	update_post_meta( $header_template_id, '_elementor_data', wp_slash( $elementor_data ) );

	if ( class_exists( '\\Elementor\\Plugin' ) ) {
		// Best-effort cache clear for styles.
		try {
			\Elementor\Plugin::$instance->files_manager->clear_cache();
		} catch ( Throwable $e ) {
			// No-op (local-only).
		}
	}
}, 999 );
