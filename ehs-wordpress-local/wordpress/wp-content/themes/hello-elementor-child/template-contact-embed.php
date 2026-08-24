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
$contact_embed_redirect_allowed_hosts = array( 'industrialhygiene.consulting', 'constructionsafety.consulting', 'caltransleadcomplianceplan.com', 'www.industrialhygiene.consulting', 'www.constructionsafety.consulting', 'www.caltransleadcomplianceplan.com' );
$contact_embed_redirect_url = '';
$contact_embed_redirect_raw = '';
$contact_embed_method = isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) ) : 'GET';

if ( 'POST' === $contact_embed_method && ! empty( $_POST['contact_redirect'] ) ) {
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
$contact_embed_status  = 400;

// --- POST handling: verify nonce, Turnstile, send mail, then show success (plan §3, §4, §5, §7) ---
if ( 'POST' === $contact_embed_method ) {
	$contact_embed_is_ajax = ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) === 'xmlhttprequest' ) || ! empty( $_POST['_ajax'] ) || ( isset( $_GET['ajax'] ) && sanitize_text_field( wp_unslash( $_GET['ajax'] ) ) === '1' );
	$content_length = isset( $_SERVER['CONTENT_LENGTH'] ) ? absint( $_SERVER['CONTENT_LENGTH'] ) : 0;
	$request_origin = function_exists( 'ehs_contact_embed_request_origin' ) ? ehs_contact_embed_request_origin() : '';
	$nonce          = isset( $_POST['contact_embed_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_embed_nonce'] ) ) : '';

	if ( $content_length > 32768 ) {
		$contact_embed_status = 413;
		$contact_embed_error  = __( 'The request is too large. Please shorten the project details and try again.', 'hello-elementor-child' );
	} elseif ( $request_origin && ( ! function_exists( 'ehs_contact_embed_origin_is_allowed' ) || ! ehs_contact_embed_origin_is_allowed( $request_origin ) ) ) {
		$contact_embed_status = 403;
		$contact_embed_error  = __( 'This form cannot be submitted from that website.', 'hello-elementor-child' );
	} elseif ( ! $nonce || ! wp_verify_nonce( $nonce, 'contact_embed_submit' ) ) {
		$contact_embed_status = 403;
		$contact_embed_error = __( 'Security check failed. Please try again.', 'hello-elementor-child' );
	} elseif ( ! empty( $_POST['contact_website'] ) ) {
		// Honeypot submissions get a harmless success response and no mail or database write.
		$contact_embed_success = true;
	} elseif ( ! function_exists( 'ehs_contact_embed_verify_form_token' ) || ! ehs_contact_embed_verify_form_token( isset( $_POST['contact_form_token'] ) ? wp_unslash( $_POST['contact_form_token'] ) : '' ) ) {
		$contact_embed_error = __( 'The form expired or was submitted too quickly. Please reload the page and try again.', 'hello-elementor-child' );
	} else {
		$rate_limit = function_exists( 'ehs_contact_embed_check_rate_limit' ) ? ehs_contact_embed_check_rate_limit() : new WP_Error( 'form_unavailable' );
		if ( is_wp_error( $rate_limit ) ) {
			$contact_embed_status = 'rate_limited' === $rate_limit->get_error_code() ? 429 : 503;
			$contact_embed_error  = 'rate_limited' === $rate_limit->get_error_code()
				? $rate_limit->get_error_message()
				: __( 'The form is temporarily unavailable. Please try again later.', 'hello-elementor-child' );
		}

		// Turnstile server-side verification
		$turnstile_token = isset( $_POST['cf-turnstile-response'] ) ? sanitize_text_field( wp_unslash( $_POST['cf-turnstile-response'] ) ) : '';
		$turnstile_secret = defined( 'EHS_TURNSTILE_SECRET_KEY' ) ? EHS_TURNSTILE_SECRET_KEY : get_option( 'ehs_turnstile_secret_key', '' );
		$turnstile_ok     = false;
		if ( ! $contact_embed_error && $turnstile_token && $turnstile_secret ) {
			$verify = wp_remote_post(
				'https://challenges.cloudflare.com/turnstile/v0/siteverify',
				array(
					'body' => array(
						'secret'          => $turnstile_secret,
						'response'        => $turnstile_token,
						'remoteip'        => function_exists( 'ehs_contact_embed_client_ip' ) ? ehs_contact_embed_client_ip() : '',
						'idempotency_key' => wp_generate_uuid4(),
					),
					'timeout' => 10,
				)
			);
			if ( ! is_wp_error( $verify ) && 200 === wp_remote_retrieve_response_code( $verify ) ) {
				$body                        = json_decode( wp_remote_retrieve_body( $verify ), true );
				$turnstile_hostname          = isset( $body['hostname'] ) ? strtolower( sanitize_text_field( $body['hostname'] ) ) : '';
				$request_origin_hostname      = $request_origin ? strtolower( (string) wp_parse_url( $request_origin, PHP_URL_HOST ) ) : '';
				$turnstile_allowed_hostnames = array(
					'ehsanalytical.com',
					'www.ehsanalytical.com',
					'industrialhygiene.consulting',
					'www.industrialhygiene.consulting',
					'constructionsafety.consulting',
					'www.constructionsafety.consulting',
					'caltransleadcomplianceplan.com',
					'www.caltransleadcomplianceplan.com',
				);
				$turnstile_ok = ! empty( $body['success'] )
					&& in_array( $turnstile_hostname, $turnstile_allowed_hostnames, true )
					&& ( ! $request_origin_hostname || $request_origin_hostname === $turnstile_hostname )
					&& ( empty( $body['action'] ) || 'contact_submit' === $body['action'] );
			}
		}
		if ( ! $contact_embed_error && ! $turnstile_ok ) {
			$contact_embed_error = __( 'Please complete the verification check and try again.', 'hello-elementor-child' );
		}

		if ( ! $contact_embed_error ) {
			$name_raw    = isset( $_POST['contact_name'] ) ? wp_unslash( $_POST['contact_name'] ) : '';
			$email_raw   = isset( $_POST['contact_email'] ) ? wp_unslash( $_POST['contact_email'] ) : '';
			$phone_raw   = isset( $_POST['contact_phone'] ) ? wp_unslash( $_POST['contact_phone'] ) : '';
			$company_raw = isset( $_POST['contact_company'] ) ? wp_unslash( $_POST['contact_company'] ) : '';
			$project_raw = isset( $_POST['contact_project'] ) ? wp_unslash( $_POST['contact_project'] ) : '';
			$name        = sanitize_text_field( $name_raw );
			$email       = sanitize_email( $email_raw );
			$phone       = sanitize_text_field( $phone_raw );
			$company     = sanitize_text_field( $company_raw );
			$project     = sanitize_textarea_field( $project_raw );
			$source   = isset( $_POST['contact_source'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_source'] ) ) : '';
			if ( $source && ! isset( $contact_embed_sources[ $source ] ) ) {
				$source = '';
			}
			$source_name = $source ? $contact_embed_sources[ $source ] : '';
			if ( strlen( $name_raw ) > 100 || strlen( $email_raw ) > 254 || strlen( $phone_raw ) > 40 || strlen( $company_raw ) > 150 || strlen( $project_raw ) > 5000 ) {
				$contact_embed_error = __( 'One or more fields are too long. Please shorten them and try again.', 'hello-elementor-child' );
			} elseif ( ! $name || ! $email || ! $phone || ! $project ) {
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
	status_header( $contact_embed_status );
	if ( 429 === $contact_embed_status ) {
		header( 'Retry-After: 600' );
	}
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
		.ehs-embed .ehs-hp { position: absolute !important; left: -10000px !important; width: 1px !important; height: 1px !important; overflow: hidden !important; }
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
				<input type="hidden" name="contact_form_token" value="<?php echo esc_attr( function_exists( 'ehs_contact_embed_create_form_token' ) ? ehs_contact_embed_create_form_token() : '' ); ?>">
				<div class="ehs-hp" aria-hidden="true">
					<label for="contact_website"><?php esc_html_e( 'Website', 'hello-elementor-child' ); ?></label>
					<input type="text" id="contact_website" name="contact_website" value="" tabindex="-1" autocomplete="off">
				</div>
				<?php if ( $contact_embed_redirect_url ) : ?>
				<input type="hidden" name="contact_redirect" value="<?php echo esc_attr( $contact_embed_redirect_url ); ?>">
				<?php endif; ?>
				<?php if ( $contact_embed_error ) : ?>
					<p class="error"><?php echo esc_html( $contact_embed_error ); ?></p>
				<?php endif; ?>
				<label for="contact_name"><?php esc_html_e( 'Name', 'hello-elementor-child' ); ?> <span class="required">*</span></label>
				<input type="text" id="contact_name" name="contact_name" maxlength="100" required value="<?php echo esc_attr( isset( $_POST['contact_name'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_name'] ) ) : '' ); ?>">
				<label for="contact_email"><?php esc_html_e( 'Email', 'hello-elementor-child' ); ?> <span class="required">*</span></label>
				<input type="email" id="contact_email" name="contact_email" maxlength="254" required value="<?php echo esc_attr( isset( $_POST['contact_email'] ) ? sanitize_email( wp_unslash( $_POST['contact_email'] ) ) : '' ); ?>">
				<label for="contact_phone"><?php esc_html_e( 'Phone', 'hello-elementor-child' ); ?> <span class="required">*</span></label>
				<input type="tel" id="contact_phone" name="contact_phone" maxlength="40" required value="<?php echo esc_attr( isset( $_POST['contact_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_phone'] ) ) : '' ); ?>">
				<label for="contact_company"><?php esc_html_e( 'Company', 'hello-elementor-child' ); ?></label>
				<input type="text" id="contact_company" name="contact_company" maxlength="150" value="<?php echo esc_attr( isset( $_POST['contact_company'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_company'] ) ) : '' ); ?>">
				<label for="contact_project"><?php esc_html_e( 'Project Details', 'hello-elementor-child' ); ?> <span class="required">*</span></label>
				<textarea id="contact_project" name="contact_project" maxlength="5000" required><?php echo esc_textarea( isset( $_POST['contact_project'] ) ? wp_unslash( $_POST['contact_project'] ) : '' ); ?></textarea>
				<?php if ( $turnstile_site_key ) : ?>
					<div class="cf-turnstile" data-sitekey="<?php echo esc_attr( $turnstile_site_key ); ?>" data-action="contact_submit"></div>
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

					fetch(embedUrl, { method: 'POST', body: new FormData(form), mode: 'cors', credentials: 'omit' })
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
