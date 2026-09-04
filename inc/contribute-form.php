<?php
/**
 * Contributor application form.
 *
 * Registration is deliberately closed. Anyone can apply here, an editor reads
 * the application, and accounts are created by hand — so no unvetted account
 * ever exists, and the site presents no open signup endpoint for bots to find.
 *
 * The form is handled by the theme rather than a forms plugin because the
 * application has to keep working through hosting moves and plugin changes: a
 * plugin-built form lives in the database, and a database that does not travel
 * with the theme leaves this page blank on the next migration.
 *
 * Nothing is stored. Each application is emailed to the editorial address and
 * discarded, which keeps applicants' details out of the database entirely.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/** How many applications one visitor may send per hour. */
const ARR_CONTRIBUTE_LIMIT = 3;

/**
 * Rate-limit key for the current visitor.
 *
 * Hashed with the site's auth salt so the transient table never holds a raw IP
 * address — the key is only ever compared against itself.
 */
function arr_contribute_rate_key() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? wp_unslash( $_SERVER['REMOTE_ADDR'] ) : 'unknown';
	return 'arr_contrib_' . md5( $ip . wp_salt( 'auth' ) );
}

/**
 * Send the visitor back to the form with a result code in the URL.
 *
 * A redirect rather than rendering the result inline, so a refresh after
 * submitting does not resubmit the application.
 */
function arr_contribute_redirect( $status, $referer ) {
	$url = $referer ? $referer : home_url( '/' );
	wp_safe_redirect( add_query_arg( 'contribute', $status, remove_query_arg( 'contribute', $url ) ) . '#contribute-form' );
	exit;
}

/**
 * Validate and email one contributor application.
 */
function arr_handle_contribute_form() {
	$referer = wp_get_referer();

	if ( ! isset( $_POST['arr_contribute_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['arr_contribute_nonce'] ), 'arr_contribute' ) ) {
		arr_contribute_redirect( 'error', $referer );
	}

	// Honeypot: a field hidden from people but filled in by most form bots.
	// Answered with "sent" rather than an error so the bot learns nothing.
	if ( ! empty( $_POST['arr_website'] ) ) {
		arr_contribute_redirect( 'sent', $referer );
	}

	$key   = arr_contribute_rate_key();
	$count = (int) get_transient( $key );
	if ( $count >= ARR_CONTRIBUTE_LIMIT ) {
		arr_contribute_redirect( 'throttled', $referer );
	}

	$name    = sanitize_text_field( wp_unslash( $_POST['arr_name'] ?? '' ) );
	$email   = sanitize_email( wp_unslash( $_POST['arr_email'] ?? '' ) );
	$phone   = sanitize_text_field( wp_unslash( $_POST['arr_phone'] ?? '' ) );
	$pillar  = sanitize_text_field( wp_unslash( $_POST['arr_pillar'] ?? '' ) );
	$links   = esc_url_raw( wp_unslash( $_POST['arr_links'] ?? '' ) );
	$pitch   = sanitize_textarea_field( wp_unslash( $_POST['arr_pitch'] ?? '' ) );

	if ( ! $name || ! is_email( $email ) || ! $pitch ) {
		arr_contribute_redirect( 'invalid', $referer );
	}

	$to      = get_theme_mod( 'arr_contribute_email', ARR_CONTACT_EMAIL );
	/* translators: %s: applicant's name. */
	$subject = sprintf( __( 'Contributor application — %s', 'arr-theme' ), $name );

	$body = implode( "\n", array_filter( array(
		__( 'A new contributor application was submitted on the website.', 'arr-theme' ),
		'',
		__( 'Name:', 'arr-theme' ) . ' ' . $name,
		__( 'Email:', 'arr-theme' ) . ' ' . $email,
		$phone  ? __( 'Phone:', 'arr-theme' ) . ' ' . $phone : '',
		$pillar ? __( 'Area of interest:', 'arr-theme' ) . ' ' . $pillar : '',
		$links  ? __( 'Published work:', 'arr-theme' ) . ' ' . $links : '',
		'',
		__( 'What they would like to write about:', 'arr-theme' ),
		$pitch,
		'',
		'--',
		/* translators: %s: site name. */
		sprintf( __( 'Sent from the %s contributor form.', 'arr-theme' ), get_bloginfo( 'name' ) ),
	), 'strlen' ) );

	// From an address the sending server is actually authorised for — see
	// arr_mail_from(), which falls back to the site's own hostname until a
	// relay is configured. The applicant goes in Reply-To, so hitting reply in
	// the inbox answers them directly.
	$headers = array(
		'From: ' . get_bloginfo( 'name' ) . ' <' . arr_mail_from() . '>',
		'Reply-To: ' . $name . ' <' . $email . '>',
	);

	$sent = wp_mail( $to, $subject, $body, $headers );

	set_transient( $key, $count + 1, HOUR_IN_SECONDS );

	arr_contribute_redirect( $sent ? 'sent' : 'failed', $referer );
}
add_action( 'admin_post_nopriv_arr_contribute', 'arr_handle_contribute_form' );
add_action( 'admin_post_arr_contribute', 'arr_handle_contribute_form' );

/**
 * The message to show above the form after a submission, if any.
 *
 * @return array{tone:string,text:string}|null
 */
function arr_contribute_notice() {
	$status = isset( $_GET['contribute'] ) ? sanitize_key( wp_unslash( $_GET['contribute'] ) ) : '';

	$messages = array(
		'sent'      => array( 'ok',    __( 'Thank you — your application has reached the editorial desk. We reply to every applicant, usually within a week.', 'arr-theme' ) ),
		'invalid'   => array( 'error', __( 'Please check your name, email address and the note about what you would like to write.', 'arr-theme' ) ),
		'throttled' => array( 'error', __( 'You have sent several applications already. Please give us time to read them before sending another.', 'arr-theme' ) ),
		'failed'    => array( 'error', __( 'Something went wrong sending your application. Please email us directly at ', 'arr-theme' ) . ARR_CONTACT_EMAIL . '.' ),
		'error'     => array( 'error', __( 'Your session expired before the form was sent. Please try once more.', 'arr-theme' ) ),
	);

	if ( ! isset( $messages[ $status ] ) ) {
		return null;
	}

	return array( 'tone' => $messages[ $status ][0], 'text' => $messages[ $status ][1] );
}
