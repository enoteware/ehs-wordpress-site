<?php
/**
 * Plugin Name: Auto Login for Local Development
 * Description: Automatically logs in the admin user in local DDEV environment
 * Version: 1.1.0
 */

// Only run in local development
if ( ! defined( 'IS_DDEV_PROJECT' ) || getenv( 'IS_DDEV_PROJECT' ) !== 'true' ) {
	return;
}

// Get admin credentials from environment variables
$admin_username = getenv( 'WP_ADMIN_USERNAME' ) ?: 'a509f58b_admin';
$admin_password = getenv( 'WP_ADMIN_PASSWORD' ) ?: 'EHS-Local-Dev-2024!';

// Auto-login hook - runs early on template_redirect
add_action( 'template_redirect', function() use ( $admin_username, $admin_password ) {
	// Skip auto-login for AJAX, REST API, cron, and WP-CLI
	if ( defined( 'DOING_AJAX' ) || defined( 'REST_REQUEST' ) || defined( 'DOING_CRON' ) || defined( 'WP_CLI' ) ) {
		return;
	}
	
	// Skip if already logged in
	if ( is_user_logged_in() ) {
		return;
	}
	
	// Skip if this is a logout request
	if ( isset( $_GET['action'] ) && $_GET['action'] === 'logout' ) {
		return;
	}
	
	// Skip if this is a login form submission (let normal login work)
	if ( isset( $_POST['log'] ) && isset( $_POST['pwd'] ) ) {
		return;
	}
	
	// Skip if user explicitly logged out (check for logout nonce)
	if ( isset( $_GET['loggedout'] ) && $_GET['loggedout'] === 'true' ) {
		return;
	}
	
	// Attempt to log in the admin user
	$user = get_user_by( 'login', $admin_username );
	
	if ( $user && wp_check_password( $admin_password, $user->user_pass, $user->ID ) ) {
		// Clear any existing auth cookies first
		wp_clear_auth_cookie();
		
		// Set the current user and auth cookie
		wp_set_current_user( $user->ID, $admin_username );
		wp_set_auth_cookie( $user->ID, true, is_ssl() );
		
		// Update last login time
		update_user_meta( $user->ID, 'last_login', current_time( 'mysql' ) );
		
		// If we're on the login page, redirect to admin dashboard
		if ( is_admin() || ( isset( $_SERVER['REQUEST_URI'] ) && strpos( $_SERVER['REQUEST_URI'], '/wp-login.php' ) !== false ) ) {
			wp_safe_redirect( admin_url() );
			exit;
		}
	}
}, 1 ); // Priority 1 to run early
