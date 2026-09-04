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
 *
 * Nonce, honeypot and rate limiting live in inc/form-shared.php.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Validate and email one contributor application.
 */
function arr_handle_contribute_form() {
	$referer = arr_form_guard( 'contribute', 'arr_contribute', 'contribute-form' );

	$name   = sanitize_text_field( wp_unslash( $_POST['arr_name'] ?? '' ) );
	$email  = sanitize_email( wp_unslash( $_POST['arr_email'] ?? '' ) );
	$phone  = sanitize_text_field( wp_unslash( $_POST['arr_phone'] ?? '' ) );
	$pillar = sanitize_text_field( wp_unslash( $_POST['arr_pillar'] ?? '' ) );
	$links  = esc_url_raw( wp_unslash( $_POST['arr_links'] ?? '' ) );
	$pitch  = sanitize_textarea_field( wp_unslash( $_POST['arr_pitch'] ?? '' ) );

	if ( ! $name || ! is_email( $email ) || ! $pitch ) {
		arr_form_redirect( 'contribute', 'invalid', $referer, 'contribute-form' );
	}

	$to = get_theme_mod( 'arr_contribute_email', ARR_CONTACT_EMAIL );

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

	$sent = wp_mail(
		$to,
		/* translators: %s: applicant's name. */
		sprintf( __( 'Contributor application — %s', 'arr-theme' ), $name ),
		$body,
		arr_form_headers( $name, $email )
	);

	arr_form_record( 'contribute' );
	arr_form_redirect( 'contribute', $sent ? 'sent' : 'failed', $referer, 'contribute-form' );
}
add_action( 'admin_post_nopriv_arr_contribute', 'arr_handle_contribute_form' );
add_action( 'admin_post_arr_contribute', 'arr_handle_contribute_form' );

/**
 * The message to show above the contributor form after a submission.
 */
function arr_contribute_notice() {
	return arr_form_notice( 'contribute', __( 'Thank you — your application has reached the editorial desk. We reply to every applicant, usually within a week.', 'arr-theme' ) );
}
