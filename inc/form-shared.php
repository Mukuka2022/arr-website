<?php
/**
 * Machinery shared by the theme's front-end forms.
 *
 * Both the contributor application and the advertising enquiry need the same
 * three things — a per-visitor rate limit, a post/redirect/get round trip, and
 * a status message keyed off the URL. Extracted so a fix to any of them (a
 * spam-handling change, say) applies to both rather than to whichever one
 * someone remembered.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/** How many submissions one visitor may send per form per hour. */
const ARR_FORM_LIMIT = 3;

/**
 * Rate-limit key for the current visitor and form.
 *
 * Hashed with the site's auth salt so the transient table never holds a raw IP
 * address — the key is only ever compared against itself. Scoped per form, so
 * applying to write does not use up someone's advertising enquiries.
 */
function arr_form_rate_key( $form ) {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? wp_unslash( $_SERVER['REMOTE_ADDR'] ) : 'unknown';
	return 'arr_form_' . md5( $form . '|' . $ip . '|' . wp_salt( 'auth' ) );
}

/**
 * Send the visitor back to the form with a result code in the URL.
 *
 * A redirect rather than rendering the result inline, so a refresh after
 * submitting does not resubmit the form.
 */
function arr_form_redirect( $form, $status, $referer, $anchor ) {
	$url = $referer ? $referer : home_url( '/' );
	wp_safe_redirect( add_query_arg( $form, $status, remove_query_arg( $form, $url ) ) . '#' . $anchor );
	exit;
}

/**
 * The message to show above a form after submission, if any.
 *
 * @param string $form  Query-arg name, e.g. 'contribute'.
 * @param string $thanks Success wording, which differs per form.
 * @return array{tone:string,text:string}|null
 */
function arr_form_notice( $form, $thanks ) {
	$status = isset( $_GET[ $form ] ) ? sanitize_key( wp_unslash( $_GET[ $form ] ) ) : '';

	$messages = array(
		'sent'      => array( 'ok', $thanks ),
		'invalid'   => array( 'error', __( 'Please check the required fields and try again.', 'arr-theme' ) ),
		'throttled' => array( 'error', __( 'You have sent several messages already. Please give us time to read them before sending another.', 'arr-theme' ) ),
		'failed'    => array( 'error', __( 'Something went wrong sending your message. Please email us directly at ', 'arr-theme' ) . ARR_CONTACT_EMAIL . '.' ),
		'error'     => array( 'error', __( 'Your session expired before the form was sent. Please try once more.', 'arr-theme' ) ),
	);

	if ( ! isset( $messages[ $status ] ) ) {
		return null;
	}

	return array( 'tone' => $messages[ $status ][0], 'text' => $messages[ $status ][1] );
}

/**
 * The checks every submission runs before anything is sent.
 *
 * Redirects and exits on failure, so a caller that returns has passed. The
 * honeypot answers "sent" rather than an error, so a bot learns nothing about
 * why it was refused.
 */
function arr_form_guard( $form, $nonce_action, $anchor ) {
	$referer = wp_get_referer();

	if ( ! isset( $_POST[ $nonce_action . '_nonce' ] ) || ! wp_verify_nonce( sanitize_key( $_POST[ $nonce_action . '_nonce' ] ), $nonce_action ) ) {
		arr_form_redirect( $form, 'error', $referer, $anchor );
	}

	if ( ! empty( $_POST['arr_website'] ) ) {
		arr_form_redirect( $form, 'sent', $referer, $anchor );
	}

	if ( (int) get_transient( arr_form_rate_key( $form ) ) >= ARR_FORM_LIMIT ) {
		arr_form_redirect( $form, 'throttled', $referer, $anchor );
	}

	return $referer;
}

/**
 * Count one submission against the visitor's hourly allowance.
 */
function arr_form_record( $form ) {
	$key = arr_form_rate_key( $form );
	set_transient( $key, (int) get_transient( $key ) + 1, HOUR_IN_SECONDS );
}

/**
 * Headers for a form notification.
 *
 * From an address the sending server is authorised for; the sender goes in
 * Reply-To so hitting reply in the inbox answers them directly.
 */
function arr_form_headers( $name, $email ) {
	return array(
		'From: ' . get_bloginfo( 'name' ) . ' <' . arr_mail_from() . '>',
		'Reply-To: ' . $name . ' <' . $email . '>',
	);
}
