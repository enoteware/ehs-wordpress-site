<?php
/**
 * Plugin Name: Auto Login for Local Development
 * Description: Automatically logs in the admin user in local DDEV environment
 * Version: 1.4.0
 */

// Only run in local development
if ( getenv( 'IS_DDEV_PROJECT' ) !== 'true' ) {
	return;
}

/**
 * Get the first admin username (cached after first call)
 */
function ehs_get_admin_username() {
	static $admin_username = null;

	if ( $admin_username === null ) {
		$admin_users = get_users( array(
			'role' => 'administrator',
			'orderby' => 'ID',
			'order' => 'ASC',
			'number' => 1
		) );

		$admin_username = ! empty( $admin_users ) ? $admin_users[0]->user_login : false;
	}

	return $admin_username;
}

/**
 * Auto-login using determine_current_user filter
 * Only runs for auto_login requests - lets cookies work normally otherwise
 */
add_filter( 'determine_current_user', function( $user_id ) {
	// Skip if already authenticated
	if ( $user_id ) {
		return $user_id;
	}

	// Only run for explicit auto_login requests
	if ( ! isset( $_GET['auto_login'] ) || $_GET['auto_login'] !== '1' ) {
		return $user_id;
	}

	// Skip auto-login for AJAX, REST API, cron, and WP-CLI
	if ( defined( 'DOING_AJAX' ) || defined( 'REST_REQUEST' ) || defined( 'DOING_CRON' ) || defined( 'WP_CLI' ) ) {
		return $user_id;
	}

	// Skip if this is a logout request
	if ( isset( $_GET['action'] ) && $_GET['action'] === 'logout' ) {
		return $user_id;
	}

	// Get admin username
	$admin_username = ehs_get_admin_username();
	if ( ! $admin_username ) {
		return $user_id;
	}

	// Get the admin user
	$user = get_user_by( 'login', $admin_username );
	if ( ! $user ) {
		return $user_id;
	}

	// Return the user ID for authentication
	return $user->ID;
}, 1 ); // Priority 1 to run early

/**
 * Bypass password check for auto-login
 */
add_filter( 'authenticate', function( $user, $username, $password ) {
	// Only handle auto_login requests
	if ( ! isset( $_GET['auto_login'] ) || $_GET['auto_login'] !== '1' ) {
		return $user;
	}

	// Get admin username
	$admin_username = ehs_get_admin_username();
	if ( ! $admin_username ) {
		return $user;
	}

	// If username matches admin and password is empty (from auto-login), bypass check
	if ( $username === $admin_username && empty( $password ) ) {
		$admin_user = get_user_by( 'login', $admin_username );
		if ( $admin_user ) {
			return $admin_user;
		}
	}

	return $user;
}, 20, 3 ); // Priority 20 to run after default authentication

/**
 * Handle explicit one-click login request on login page
 */
add_action( 'login_init', function() {
	// Only handle explicit auto_login requests on login page
	if ( ! isset( $_GET['auto_login'] ) || $_GET['auto_login'] !== '1' ) {
		return;
	}

	// Get admin username
	$admin_username = ehs_get_admin_username();
	if ( ! $admin_username ) {
		return;
	}

	// Get the admin user
	$user = get_user_by( 'login', $admin_username );
	if ( ! $user ) {
		return;
	}

	// Clear any existing auth cookies
	wp_clear_auth_cookie();
	
	// Determine SSL - check multiple sources for DDEV
	$secure = false;
	if ( is_ssl() ) {
		$secure = true;
	} elseif ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ) {
		$secure = true;
	} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
		$secure = true;
	} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_SSL'] ) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on' ) {
		$secure = true;
	} elseif ( isset( $_SERVER['SERVER_PORT'] ) && $_SERVER['SERVER_PORT'] == 443 ) {
		$secure = true;
	}
	
	// Set auth cookies with remember=true for persistent login
	wp_set_auth_cookie( $user->ID, true, $secure );
	
	// Set current user for this request
	wp_set_current_user( $user->ID );
	
	// Update last login time
	update_user_meta( $user->ID, 'last_login', current_time( 'mysql' ) );

	// Get redirect URL
	$redirect_to = isset( $_GET['redirect_to'] ) ? esc_url_raw( $_GET['redirect_to'] ) : admin_url();
	$redirect_to = wp_validate_redirect( $redirect_to, admin_url() );
	
	// Redirect - cookies should be set by wp_set_auth_cookie
	// Remove auto_login from redirect URL so cookies work on next request
	$redirect_to = remove_query_arg( 'auto_login', $redirect_to );
	
	wp_safe_redirect( $redirect_to );
	exit;
}, 1 );

// Add one-click login button above the login form
add_filter( 'login_message', function( $message ) {
	$button = '<div style="text-align: center; margin: 0 0 20px 0; padding: 20px; background: #f0f8ff; border: 2px solid #2271b1; border-radius: 4px;">';
	$button .= '<p style="margin: 0 0 10px 0; font-weight: 600; color: #2271b1; font-size: 16px;">🚀 Local Development</p>';
	$button .= '<a href="' . esc_url( add_query_arg( 'auto_login', '1', wp_login_url() ) ) . '" class="button button-primary button-large" style="width: 100%; text-align: center; padding: 12px 24px; font-size: 14px;">One-Click Admin Login</a>';
	$button .= '<p style="margin: 10px 0 0 0; font-size: 11px; color: #666;">Instant access as admin (DDEV only)</p>';
	$button .= '</div>';

	return $button . $message;
});
