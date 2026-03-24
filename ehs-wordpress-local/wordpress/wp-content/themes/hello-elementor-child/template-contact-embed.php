<?php
/**
 * Template Name: Contact Embed (minimal, no chrome)
 * Description: Minimal embeddable contact form for microsites. No header/footer. Use with page slug contact-embed.
 *
 * Deploy to: wp-content/themes/hello-elementor-child/template-contact-embed.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// --- Source allowlist (plan §1) ---
$contact_embed_sources = array(
	'industrialhygiene'  => 'Industrial Hygiene',
	'constructionsafety' => 'Construction Safety',
	'caltranslead'       => 'Caltrans Lead',
);

$contact_embed_source_slug  = isset( $_GET['source'] ) ? sanitize_text_field( wp_unslash( $_GET['source'] ) ) : '';
$contact_embed_source_valid = $contact_embed_source_slug && isset( $contact_embed_sources[ $contact_embed_source_slug ] );
$contact_embed_source_slug  = $contact_embed_source_valid ? $contact_embed_source_slug : '';
$contact_embed_source_name  = $contact_embed_source_slug ? $contact_embed_sources[ $contact_embed_source_slug ] : '';

// Optional success redirect (allowlisted microsite domains only)
$contact_embed_redirect_allowed_hosts = array( 'industrialhygiene.consulting', 'constructionsafety.consulting', 'www.industrialhygiene.consulting', 'www.constructionsafety.consulting' );
$contact_embed_redirect_url = '';
$contact_embed_redirect_raw = '';
if ( $_SERVER['REQUEST_METHOD'] === 'POST' && ! empty( $_POST['contact_redirect'] ) ) {
	$contact_embed_redirect_raw = esc_url_raw( wp_unslash( $_POST['contact_redirect'] ), array( 'https' ) );
} elseif ( ! empty( $_GET['redirect'] ) ) {
	$contact_embed_redirect_raw = esc_url_raw( wp_unslash( $_GET['redirect'] ), array( 'https' ) );
}
if ( $contact_embed_redirect_raw && wp_parse_url( $contact_embed_redirect_raw, PHP_URL_SCHEME ) === 'https' ) {
	$host = wp_parse_url( $contact_embed_redirect_raw, PHP_URL_HOST );
	if ( $host && in_array( strtolower( $host ), $contact_embed_redirect_allowed_hosts, true ) ) {
		$contact_embed_redirect_url = $contact_embed_redirect_raw;
	}
}

$contact_embed_success = false;
$contact_embed_error   = '';
$contact_embed_is_ajax = false;

// --- POST handling: verify nonce, Turnstile, send mail, then show success (plan §3, §4, §5, §7) ---
if ( $_SERVER['REQUEST_METHOD'] === 'POST' && ! empty( $_POST['contact_embed_nonce'] ) ) {
	$contact_embed_is_ajax = ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) === 'xmlhttprequest' ) || ! empty( $_POST['_ajax'] ) || ( isset( $_GET['ajax'] ) && sanitize_text_field( wp_unslash( $_GET['ajax'] ) ) === '1' );
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['contact_embed_nonce'] ) ), 'contact_embed_submit' ) ) {
		$contact_embed_error = __( 'Security check failed. Please try again.', 'hello-elementor-child' );
	} else {
		// Turnstile server-side verification
		$turnstile_token = isset( $_POST['cf-turnstile-response'] ) ? sanitize_text_field( wp_unslash( $_POST['cf-turnstile-response'] ) ) : '';
		$turnstile_secret = defined( 'EHS_TURNSTILE_SECRET_KEY' ) ? EHS_TURNSTILE_SECRET_KEY : get_option( 'ehs_turnstile_secret_key', '' );
		$turnstile_ok     = false;
		if ( $turnstile_token && $turnstile_secret ) {
			$verify = wp_remote_post(
				'https://challenges.cloudflare.com/turnstile/v0/siteverify',
				array(
					'body' => array(
						'secret'   => $turnstile_secret,
						'response' => $turnstile_token,
					),
					'timeout' => 15,
				)
			);
			if ( ! is_wp_error( $verify ) ) {
				$body = json_decode( wp_remote_retrieve_body( $verify ), true );
				$turnstile_ok = ! empty( $body['success'] );
			}
		} else {
			// Allow form to work without Turnstile if keys not configured (dev fallback)
			$turnstile_ok = true;
		}
		if ( ! $turnstile_ok ) {
			$contact_embed_error = __( 'Please complete the verification check and try again.', 'hello-elementor-child' );
		} else {
			$name     = isset( $_POST['contact_name'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_name'] ) ) : '';
			$email    = isset( $_POST['contact_email'] ) ? sanitize_email( wp_unslash( $_POST['contact_email'] ) ) : '';
			$phone    = isset( $_POST['contact_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_phone'] ) ) : '';
			$company  = isset( $_POST['contact_company'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_company'] ) ) : '';
			$project  = isset( $_POST['contact_project'] ) ? sanitize_textarea_field( wp_unslash( $_POST['contact_project'] ) ) : '';
			$source   = isset( $_POST['contact_source'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_source'] ) ) : '';
			if ( $source && ! isset( $contact_embed_sources[ $source ] ) ) {
				$source = '';
			}
			$source_name = $source ? $contact_embed_sources[ $source ] : '';
			if ( ! $name || ! $email || ! $phone || ! $project ) {
				$contact_embed_error = __( 'Please fill in all required fields.', 'hello-elementor-child' );
			} elseif ( ! is_email( $email ) ) {
				$contact_embed_error = __( 'Please enter a valid email address.', 'hello-elementor-child' );
			} else {
				// Subject: [Source Name] Request a Quote or Request a Quote
				$email_subject = $source_name ? sprintf( '[%s] %s', $source_name, __( 'Request a Quote', 'hello-elementor-child' ) ) : __( 'Request a Quote', 'hello-elementor-child' );
				// Body prefix: Lead Source: [source].consulting
				$body_prefix = $source ? "Lead Source: {$source}.consulting\n\n" : '';
				$email_body  = $body_prefix . "Name: {$name}\nEmail: {$email}\nPhone: {$phone}\n" . ( $company ? "Company: {$company}\n" : '' ) . "\nProject Details:\n{$project}";
				$to          = 'adam@ehsanalytical.com';
				$headers     = array( 'Content-Type: text/plain; charset=UTF-8', 'Reply-To: ' . $name . ' <' . $email . '>' );
				$sent        = wp_mail( $to, $email_subject, $email_body, $headers );
				if ( $sent ) {
					if ( function_exists( 'ehs_contact_embed_save_submission' ) ) {
						ehs_contact_embed_save_submission( array(
							'name'         => $name,
							'email'        => $email,
							'phone'        => $phone,
							'company'      => $company,
							'project'      => $project,
							'source'       => $source,
							'source_name'  => $source_name,
						) );
					}
					$contact_embed_success = true;
				} else {
					$contact_embed_error = __( 'Sorry, we could not send your message. Please try again later.', 'hello-elementor-child' );
				}
			}
		}
	}
}

// --- AJAX: return JSON and exit before any HTML ---
if ( $contact_embed_is_ajax ) {
	if ( $contact_embed_success ) {
		status_header( 200 );
		header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
		echo wp_json_encode( array( 'success' => true ) );
		exit;
	}
	status_header( 400 );
	header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
	echo wp_json_encode( array( 'success' => false, 'error' => $contact_embed_error ) );
	exit;
}

$embed_post_url = ehs_get_page_url( 'contact-embed' ) . ( $contact_embed_source_slug ? '?source=' . $contact_embed_source_slug : '' );
$thank_you_url = $contact_embed_redirect_url ? $contact_embed_redirect_url : ehs_get_page_url( 'thank-you' );
$turnstile_site_key = defined( 'EHS_TURNSTILE_SITE_KEY' ) ? EHS_TURNSTILE_SITE_KEY : get_option( 'ehs_turnstile_site_key', '' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo esc_html( $contact_embed_source_name ? $contact_embed_source_name . ' - ' : '' ); ?><?php esc_html_e( 'Request a Quote', 'hello-elementor-child' ); ?> | <?php bloginfo( 'name' ); ?></title>
	<style>
		.ehs-embed { margin: 0; padding: 1rem 0; }
		.ehs-embed label { display: block; margin-top: 0.75rem; margin-bottom: 0.25rem; }
		.ehs-embed input[type="text"],
		.ehs-embed input[type="email"],
		.ehs-embed input[type="tel"],
		.ehs-embed textarea { width: 100%; box-sizing: border-box; }
		.ehs-embed textarea { min-height: 6rem; resize: vertical; }
		.ehs-embed .cf-turnstile { margin: 0.75rem 0; }
		.ehs-embed button[type="submit"] { margin-top: 0.75rem; }
		.ehs-embed .error { margin-top: 0.5rem; }
		.ehs-embed .success { margin: 1rem 0; padding: 0.5rem 0; }
	</style>
	<?php if ( $turnstile_site_key ) : ?>
	<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
	<?php endif; ?>
</head>
<body class="contact-embed">
	<div class="ehs-embed">
		<?php if ( $contact_embed_success ) : ?>
			<p class="success"><?php esc_html_e( "Thank you! We've received your message.", 'hello-elementor-child' ); ?></p>
			<p><?php esc_html_e( 'Redirecting…', 'hello-elementor-child' ); ?></p>
			<script>
				setTimeout(function(){ window.location.href = <?php echo wp_json_encode( $thank_you_url ); ?>; }, 1500);
			</script>
		<?php else : ?>
			<form method="post" action="" id="contact-embed-form" data-action="<?php echo esc_url( $embed_post_url ); ?>">
				<?php wp_nonce_field( 'contact_embed_submit', 'contact_embed_nonce' ); ?>
				<input type="hidden" name="_ajax" value="1">
				<input type="hidden" name="contact_source" value="<?php echo esc_attr( $contact_embed_source_slug ); ?>">
				<?php if ( $contact_embed_redirect_url ) : ?>
				<input type="hidden" name="contact_redirect" value="<?php echo esc_attr( $contact_embed_redirect_url ); ?>">
				<?php endif; ?>
				<?php if ( $contact_embed_error ) : ?>
					<p class="error"><?php echo esc_html( $contact_embed_error ); ?></p>
				<?php endif; ?>
				<label for="contact_name"><?php esc_html_e( 'Name', 'hello-elementor-child' ); ?> <span class="required">*</span></label>
				<input type="text" id="contact_name" name="contact_name" required value="<?php echo esc_attr( isset( $_POST['contact_name'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_name'] ) ) : '' ); ?>">
				<label for="contact_email"><?php esc_html_e( 'Email', 'hello-elementor-child' ); ?> <span class="required">*</span></label>
				<input type="email" id="contact_email" name="contact_email" required value="<?php echo esc_attr( isset( $_POST['contact_email'] ) ? sanitize_email( wp_unslash( $_POST['contact_email'] ) ) : '' ); ?>">
				<label for="contact_phone"><?php esc_html_e( 'Phone', 'hello-elementor-child' ); ?> <span class="required">*</span></label>
				<input type="tel" id="contact_phone" name="contact_phone" required value="<?php echo esc_attr( isset( $_POST['contact_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_phone'] ) ) : '' ); ?>">
				<label for="contact_company"><?php esc_html_e( 'Company', 'hello-elementor-child' ); ?></label>
				<input type="text" id="contact_company" name="contact_company" value="<?php echo esc_attr( isset( $_POST['contact_company'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_company'] ) ) : '' ); ?>">
				<label for="contact_project"><?php esc_html_e( 'Project Details', 'hello-elementor-child' ); ?> <span class="required">*</span></label>
				<textarea id="contact_project" name="contact_project" required><?php echo esc_textarea( isset( $_POST['contact_project'] ) ? wp_unslash( $_POST['contact_project'] ) : '' ); ?></textarea>
				<?php if ( $turnstile_site_key ) : ?>
					<div class="cf-turnstile" data-sitekey="<?php echo esc_attr( $turnstile_site_key ); ?>"></div>
				<?php endif; ?>
				<button type="submit"><?php esc_html_e( 'Submit Request', 'hello-elementor-child' ); ?></button>
			</form>
			<script>
			(function() {
				var form = document.getElementById('contact-embed-form');
				if (!form) return;
				var embedUrl = form.getAttribute('data-action') || form.action || (window.location.origin + window.location.pathname + window.location.search);
				var wrapper = form.closest('.ehs-embed');
				var submitBtn = form.querySelector('button[type="submit"]');
				var btnDefaultText = submitBtn ? submitBtn.textContent : 'Submit Request';

				function showThankYou() {
					var ty = document.createElement('div');
					ty.className = 'success';
					ty.style.padding = '1rem 0';
					var h3 = document.createElement('h3');
					h3.style.margin = '0 0 0.5rem';
					h3.textContent = 'Thank You!';
					var p1 = document.createElement('p');
					p1.style.margin = '0';
					p1.textContent = "We've received your inquiry and will respond within 24 hours.";
					var p2 = document.createElement('p');
					p2.style.margin = '1rem 0 0';
					p2.style.fontSize = '0.95rem';
					p2.textContent = 'You can also reach us at (619) 288-3094 or ';
					var a = document.createElement('a');
					a.href = 'mailto:adam@ehsanalytical.com';
					a.textContent = 'adam@ehsanalytical.com';
					p2.appendChild(a);
					ty.appendChild(h3);
					ty.appendChild(p1);
					ty.appendChild(p2);
					if (wrapper) { wrapper.innerHTML = ''; wrapper.appendChild(ty); }
				}

				function showError(msg) {
					var err = document.createElement('p');
					err.className = 'error';
					err.textContent = msg || 'Something went wrong. Please try again or contact us at (619) 288-3094 or adam@ehsanalytical.com';
					form.insertBefore(err, form.firstChild);
					if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = btnDefaultText; }
				}

				form.addEventListener('submit', function(e) {
					e.preventDefault();
					if (submitBtn) { submitBtn.disabled = true; submitBtn.textContent = 'Sending...'; }
					var existingError = form.querySelector('.error');
					if (existingError) existingError.remove();

					fetch(embedUrl, { method: 'POST', body: new FormData(form), mode: 'cors', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
						.then(function(r) { return r.json().then(function(data) { return { ok: r.ok, data: data }; }).catch(function() { return { ok: r.ok, data: {} }; }); })
						.then(function(result) {
							if (result.ok && result.data && result.data.success) {
								showThankYou();
							} else {
								showError(result.data && result.data.error ? result.data.error : 'Submission failed. Please try again.');
							}
						})
						.catch(function() {
							showError('There was an error. Please call (619) 288-3094 or email adam@ehsanalytical.com');
						});
				});
			})();
			</script>
		<?php endif; ?>
	</div>
</body>
</html>
