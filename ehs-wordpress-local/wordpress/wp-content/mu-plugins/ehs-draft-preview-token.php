<?php
/**
 * Plugin Name: EHS Draft Preview Token
 * Description: Signed, expiring, per-post-revocable public previews for WordPress drafts.
 * Version: 1.0.0
 *
 * Install EHS_DRAFT_PREVIEW_SECRET in wp-config.php from a secret outside the
 * web root. Generate URLs with `wp ehs-draft-preview url`.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'EHS_DRAFT_PREVIEW_SECRET' ) || EHS_DRAFT_PREVIEW_SECRET === '' ) {
	return;
}

function ehs_draft_preview_revision_key(): string {
	return '_ehs_draft_preview_revision';
}

function ehs_draft_preview_get_or_create_revision( int $post_id ): string {
	$revision = (string) get_post_meta( $post_id, ehs_draft_preview_revision_key(), true );
	if ( preg_match( '/^[a-f0-9]{64}$/', $revision ) === 1 ) {
		return $revision;
	}

	$revision = bin2hex( random_bytes( 32 ) );
	update_post_meta( $post_id, ehs_draft_preview_revision_key(), $revision );
	return $revision;
}

function ehs_draft_preview_rotate_revision( int $post_id ): string {
	$revision = bin2hex( random_bytes( 32 ) );
	update_post_meta( $post_id, ehs_draft_preview_revision_key(), $revision );
	return $revision;
}

function ehs_draft_preview_sign( int $post_id, int $expiry, string $revision ): string {
	return hash_hmac( 'sha256', $post_id . '|' . $expiry . '|' . $revision, EHS_DRAFT_PREVIEW_SECRET );
}

function ehs_draft_preview_verify_token( int $post_id, int $expiry, string $revision, string $token ): bool {
	if ( $post_id < 1 || $expiry <= time() || preg_match( '/^[a-f0-9]{64}$/', $revision ) !== 1 || preg_match( '/^[a-f0-9]{64}$/', $token ) !== 1 ) {
		return false;
	}

	return hash_equals( ehs_draft_preview_sign( $post_id, $expiry, $revision ), $token );
}

function ehs_draft_preview_positive_query_int( string $key ): int {
	if ( ! isset( $_GET[ $key ] ) || is_array( $_GET[ $key ] ) ) {
		return 0;
	}
	$value = (string) wp_unslash( $_GET[ $key ] );
	return ctype_digit( $value ) && (int) $value > 0 ? (int) $value : 0;
}

function ehs_draft_preview_string_query_arg( string $key ): string {
	return isset( $_GET[ $key ] ) && ! is_array( $_GET[ $key ] ) ? sanitize_text_field( wp_unslash( (string) $_GET[ $key ] ) ) : '';
}

function ehs_draft_preview_is_html_request(): bool {
	return empty( $_GET['feed'] ) && empty( $_GET['embed'] ) && ! ( defined( 'REST_REQUEST' ) && REST_REQUEST );
}

function ehs_draft_preview_disable_caches(): void {
	foreach ( [ 'DONOTCACHEPAGE', 'DONOTCACHEDB', 'DONOTMINIFY' ] as $constant ) {
		if ( ! defined( $constant ) ) {
			define( $constant, true );
		}
	}
}

function ehs_draft_preview_send_protected_headers(): void {
	nocache_headers();
	header( 'Cache-Control: private, no-store, no-cache, max-age=0, must-revalidate', true );
	header( 'Pragma: no-cache', true );
	header( 'Referrer-Policy: no-referrer', true );
	header( 'X-Robots-Tag: noindex, nofollow', true );
}

/** Returns the valid preview post ID for this request, otherwise 0. */
function ehs_draft_preview_get_request_post_id(): int {
	static $cached = null;
	if ( $cached !== null ) {
		return $cached;
	}

	$cached = 0;
	if ( ehs_draft_preview_string_query_arg( 'ehs_draft_preview' ) !== '1' || ! ehs_draft_preview_is_html_request() ) {
		return $cached;
	}

	$post_id = ehs_draft_preview_positive_query_int( 'p' );
	$expiry  = ehs_draft_preview_positive_query_int( 'e' );
	$revision = ehs_draft_preview_string_query_arg( 'r' );
	$token = ehs_draft_preview_string_query_arg( 't' );
	if ( ! ehs_draft_preview_verify_token( $post_id, $expiry, $revision, $token ) ) {
		return $cached;
	}

	$post = get_post( $post_id );
	if ( ! $post || $post->post_type !== 'post' || $post->post_status !== 'draft' ) {
		return $cached;
	}
	if ( ! hash_equals( ehs_draft_preview_get_or_create_revision( $post_id ), $revision ) ) {
		return $cached;
	}

	$cached = $post_id;
	ehs_draft_preview_disable_caches();
	return $cached;
}

add_filter( 'redirect_canonical', static function( $redirect_url ) {
	return isset( $_GET['ehs_draft_preview'] ) && (string) $_GET['ehs_draft_preview'] === '1' ? false : $redirect_url;
}, 10, 1 );

add_filter( 'do_redirect_guess_404_permalink', static function( $redirect ) {
	return isset( $_GET['ehs_draft_preview'] ) && (string) $_GET['ehs_draft_preview'] === '1' ? false : $redirect;
} );

add_action( 'pre_get_posts', static function( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	$post_id = ehs_draft_preview_get_request_post_id();
	if ( ! $post_id ) {
		return;
	}
	$query->set( 'post_type', 'post' );
	$query->set( 'p', $post_id );
	$query->set( 'post_status', [ 'publish', 'draft' ] );
} );

add_filter( 'the_posts', static function( $posts, $query ) {
	$post_id = ehs_draft_preview_get_request_post_id();
	if ( ! $post_id || ! $query->is_main_query() ) {
		return $posts;
	}
	foreach ( $posts as $index => $post ) {
		if ( (int) $post->ID === $post_id ) {
			$preview_post = clone $post;
			$preview_post->post_status = 'publish';
			$posts[ $index ] = $preview_post;
			break;
		}
	}
	return $posts;
}, 10, 2 );

add_action( 'send_headers', static function() {
	if ( ehs_draft_preview_get_request_post_id() ) {
		ehs_draft_preview_send_protected_headers();
	}
}, 0 );

add_action( 'template_redirect', static function() {
	if ( ehs_draft_preview_get_request_post_id() ) {
		ehs_draft_preview_send_protected_headers();
		return;
	}
	if ( ehs_draft_preview_string_query_arg( 'ehs_draft_preview' ) === '1' ) {
		wp_die( esc_html__( 'This preview link is invalid or has expired.', 'ehs-draft-preview' ), esc_html__( 'Preview unavailable', 'ehs-draft-preview' ), [ 'response' => 403 ] );
	}
}, 0 );

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	/** Generates or revokes EHS bearer preview URLs for draft blog posts. */
	class EHS_Draft_Preview_CLI {
		public function url( array $args, array $assoc_args ): void {
			$post_id = (int) ( $args[0] ?? 0 );
			$post = get_post( $post_id );
			if ( ! $post || $post->post_type !== 'post' || $post->post_status !== 'draft' ) {
				WP_CLI::error( 'Post must be an existing draft blog post.' );
			}
			$days = isset( $assoc_args['days'] ) ? (int) $assoc_args['days'] : 14;
			if ( $days < 1 || $days > 30 ) {
				WP_CLI::error( '--days must be between 1 and 30.' );
			}
			$expiry = time() + $days * DAY_IN_SECONDS;
			$revision = ehs_draft_preview_get_or_create_revision( $post_id );
			$url = add_query_arg( [
				'ehs_draft_preview' => '1', 'p' => $post_id, 'e' => $expiry, 'r' => $revision,
				't' => ehs_draft_preview_sign( $post_id, $expiry, $revision ),
			], home_url( '/' ) );
			WP_CLI::line( $url );
			WP_CLI::line( 'Expires: ' . gmdate( 'Y-m-d H:i:s', $expiry ) . ' UTC' );
		}

		public function revoke( array $args ): void {
			$post_id = (int) ( $args[0] ?? 0 );
			$post = get_post( $post_id );
			if ( ! $post || $post->post_type !== 'post' || $post->post_status !== 'draft' ) {
				WP_CLI::error( 'Post must be an existing draft blog post.' );
			}
			ehs_draft_preview_rotate_revision( $post_id );
			WP_CLI::success( 'All existing preview URLs for this post are revoked.' );
		}
	}
	WP_CLI::add_command( 'ehs-draft-preview', 'EHS_Draft_Preview_CLI' );
}
