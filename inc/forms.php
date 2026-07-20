<?php
/**
 * ConAir Extract Solutions — Form Handling
 *
 * Handles the "Contact Us" form (patterns/quote-form.php), which appears
 * on the homepage and on any Page using the default Page template
 * (including the Contact page). One form definition, one handler — both
 * placements post to the same endpoint.
 *
 * Sends via wp_mail() — no form plugin required. If mail is going missing
 * or landing in spam (common on shared hosting, since plain PHP mail()
 * isn't authenticated for the sending domain), see conair_configure_smtp()
 * at the bottom: define the CONAIR_SMTP_* constants in wp-config.php to
 * route through a real mailbox's SMTP instead, no plugin needed.
 *
 * @package conair-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const CONAIR_QUOTE_FORM_ACTION = 'conair_submit_quote_form';
const CONAIR_QUOTE_FORM_TO     = 'info@conair-extractsolutions.co.uk';
const CONAIR_QUOTE_FORM_FROM   = 'info@conair-extractsolutions.co.uk';

/**
 * Handles both a plain form POST (no-JS fallback, via admin-post.php) and
 * a fetch() submission from assets/js/conair-theme.js. AJAX requests get
 * a JSON response; plain POSTs get redirected back to the referring page
 * with a `?quote=sent` or `?quote=error` flag so the pattern can render a
 * status message without JavaScript.
 */
function conair_handle_quote_form_submission(): void {
	$is_ajax = ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] )
		&& 'xmlhttprequest' === strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] );

	// wp_send_json_*() calls wp_die(), which only skips the full HTML error
	// page and returns bare JSON when it thinks it's handling an AJAX
	// request (wp_doing_ajax(), which just checks this constant). That's
	// normally set by admin-ajax.php — we're on admin-post.php instead, so
	// set it ourselves for the fetch() path. Doesn't affect the plain-POST
	// fallback below, which never calls wp_send_json_*().
	if ( $is_ajax && ! defined( 'DOING_AJAX' ) ) {
		define( 'DOING_AJAX', true );
	}

	$fail = static function ( string $message ) use ( $is_ajax ): void {
		if ( $is_ajax ) {
			wp_send_json_error( [ 'message' => $message ] );
		}
		wp_safe_redirect( add_query_arg( 'quote', 'error', wp_get_referer() ?: home_url( '/' ) ) );
		exit;
	};

	if ( ! isset( $_POST['conair_quote_nonce'] )
		|| ! wp_verify_nonce( $_POST['conair_quote_nonce'], 'conair_quote_form' ) ) {
		$fail( 'Your session expired — please try again.' );
		return;
	}

	// Honeypot: a real visitor never fills this in (it's visually hidden).
	// A bot that fills every field trips it.
	if ( ! empty( $_POST['hp_website'] ) ) {
		// Pretend success so the bot doesn't learn to skip the field.
		if ( $is_ajax ) {
			wp_send_json_success( [ 'message' => 'Thanks — we will be in touch shortly.' ] );
		}
		wp_safe_redirect( add_query_arg( 'quote', 'sent', wp_get_referer() ?: home_url( '/' ) ) );
		exit;
	}

	$name  = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$phone = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';

	if ( '' === $name || '' === $phone ) {
		$fail( 'Please fill in your name and phone number.' );
		return;
	}

	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	if ( '' !== $email && ! is_email( $email ) ) {
		$fail( 'That email address doesn\'t look right — please check it.' );
		return;
	}

	$service = isset( $_POST['service'] ) ? sanitize_text_field( wp_unslash( $_POST['service'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
	$page    = wp_get_referer() ?: home_url( '/' );

	$body_lines = [
		"New enquiry from the ConAir website ({$page})",
		'',
		"Name:    {$name}",
		"Phone:   {$phone}",
		'Email:   ' . ( '' !== $email ? $email : '(not provided)' ),
		'Service: ' . ( '' !== $service ? $service : '(not specified)' ),
		'',
		'Message:',
		'' !== $message ? $message : '(none)',
	];

	$headers = [ 'Content-Type: text/plain; charset=UTF-8' ];
	$headers[] = sprintf( 'From: ConAir Extract Solutions <%s>', CONAIR_QUOTE_FORM_FROM );
	if ( '' !== $email ) {
		$headers[] = sprintf( 'Reply-To: %s <%s>', $name, $email );
	}

	$sent = wp_mail(
		CONAIR_QUOTE_FORM_TO,
		sprintf( 'New Quote Request from %s', $name ),
		implode( "\n", $body_lines ),
		$headers
	);

	if ( ! $sent ) {
		$fail( 'Sorry, something went wrong sending your request — please call us instead.' );
		return;
	}

	if ( $is_ajax ) {
		wp_send_json_success( [ 'message' => 'Thanks — we will be in touch.' ] );
	}
	wp_safe_redirect( add_query_arg( 'quote', 'sent', $page ) );
	exit;
}
add_action( 'admin_post_' . CONAIR_QUOTE_FORM_ACTION, 'conair_handle_quote_form_submission' );
add_action( 'admin_post_nopriv_' . CONAIR_QUOTE_FORM_ACTION, 'conair_handle_quote_form_submission' );

/**
 * Optional: route wp_mail() through real SMTP instead of the server's
 * default PHP mail() — no plugin required. PHP mail() often gets flagged
 * as spam because it isn't authenticated (SPF/DKIM) for the sending
 * domain. To use this, define these constants in wp-config.php with
 * credentials for the info@conair-extractsolutions.co.uk mailbox (ask
 * the hosting/email provider for SMTP host/port — usually 465 with SSL
 * or 587 with TLS):
 *
 *   define( 'CONAIR_SMTP_HOST', 'smtp.example.com' );
 *   define( 'CONAIR_SMTP_PORT', 465 );
 *   define( 'CONAIR_SMTP_SECURE', 'ssl' );  // 'ssl' or 'tls'
 *   define( 'CONAIR_SMTP_USER', 'info@conair-extractsolutions.co.uk' );
 *   define( 'CONAIR_SMTP_PASS', '...' );
 *
 * Left undefined, wp_mail() behaves exactly as WordPress ships it.
 */
function conair_configure_smtp( PHPMailer\PHPMailer\PHPMailer $phpmailer ): void {
	if ( ! defined( 'CONAIR_SMTP_HOST' ) ) {
		return;
	}

	$phpmailer->isSMTP();
	$phpmailer->Host       = CONAIR_SMTP_HOST;
	$phpmailer->Port       = defined( 'CONAIR_SMTP_PORT' ) ? CONAIR_SMTP_PORT : 587;
	$phpmailer->SMTPSecure = defined( 'CONAIR_SMTP_SECURE' ) ? CONAIR_SMTP_SECURE : 'tls';
	$phpmailer->SMTPAuth   = true;
	$phpmailer->Username   = defined( 'CONAIR_SMTP_USER' ) ? CONAIR_SMTP_USER : '';
	$phpmailer->Password   = defined( 'CONAIR_SMTP_PASS' ) ? CONAIR_SMTP_PASS : '';
}
add_action( 'phpmailer_init', 'conair_configure_smtp' );
