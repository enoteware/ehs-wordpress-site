<?php
declare(strict_types=1);

define( 'ABSPATH', __DIR__ . '/' );
define( 'EHS_DRAFT_PREVIEW_SECRET', 'test-preview-secret' );
define( 'HOUR_IN_SECONDS', 3600 );
define( 'DAY_IN_SECONDS', 86400 );

$hooks = [];
function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ) { global $hooks; $hooks[ $hook ][] = $callback; }
function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) { global $hooks; $hooks[ $hook ][] = $callback; }
function sanitize_text_field( $value ) { return (string) $value; }
function wp_unslash( $value ) { return $value; }
function get_post_meta( $post_id, $key, $single = false ) { global $meta; return $meta[ $post_id ][ $key ] ?? ''; }
function update_post_meta( $post_id, $key, $value ) { global $meta; $meta[ $post_id ][ $key ] = $value; return true; }
function wp_generate_password( $length, $special_chars = true, $extra_special_chars = true ) { return str_repeat( 'a', $length ); }

$meta = [];
require dirname( __DIR__ ) . '/ehs-wordpress-local/wordpress/wp-content/mu-plugins/ehs-draft-preview-token.php';

function expect( bool $condition, string $message ): void {
	if ( ! $condition ) {
		fwrite( STDERR, "FAILED: {$message}\n" );
		exit( 1 );
	}
}

$post_id = 3588;
$revision = ehs_draft_preview_get_or_create_revision( $post_id );
$expiry = time() + HOUR_IN_SECONDS;
$token = ehs_draft_preview_sign( $post_id, $expiry, $revision );

expect( preg_match( '/^[a-f0-9]{64}$/', $revision ) === 1, 'revision must be a 256-bit hex string' );
expect( ehs_draft_preview_verify_token( $post_id, $expiry, $revision, $token ), 'valid token must verify' );
expect( ! ehs_draft_preview_verify_token( $post_id, $expiry - DAY_IN_SECONDS, $revision, $token ), 'expired token must fail' );
expect( ! ehs_draft_preview_verify_token( $post_id, $expiry, str_repeat( 'b', 64 ), $token ), 'rotated revision must invalidate prior token' );
expect( ! ehs_draft_preview_verify_token( $post_id + 1, $expiry, $revision, $token ), 'token must be post-bound' );

fwrite( STDOUT, "PASS\n" );
