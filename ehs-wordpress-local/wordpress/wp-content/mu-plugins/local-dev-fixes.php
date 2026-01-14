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
