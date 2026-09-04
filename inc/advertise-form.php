<?php
/**
 * Advertising enquiry form.
 *
 * Same shape as the contributor form and for the same reason — it must survive
 * hosting moves, and nothing about an enquiry needs storing on the website.
 * Enquiries go to the editorial address unless a separate advertising inbox is
 * set in the Customizer, since most small publications start with one inbox and
 * split later.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function arr_handle_advertise_form() {
	$referer = arr_form_guard( 'advertise', 'arr_advertise', 'advertise-form' );

	$name     = sanitize_text_field( wp_unslash( $_POST['arr_name'] ?? '' ) );
	$company  = sanitize_text_field( wp_unslash( $_POST['arr_company'] ?? '' ) );
	$email    = sanitize_email( wp_unslash( $_POST['arr_email'] ?? '' ) );
	$phone    = sanitize_text_field( wp_unslash( $_POST['arr_phone'] ?? '' ) );
	$website  = esc_url_raw( wp_unslash( $_POST['arr_url'] ?? '' ) );
	$interest = sanitize_text_field( wp_unslash( $_POST['arr_interest'] ?? '' ) );
	$message  = sanitize_textarea_field( wp_unslash( $_POST['arr_message'] ?? '' ) );

	if ( ! $name || ! is_email( $email ) || ! $message ) {
		arr_form_redirect( 'advertise', 'invalid', $referer, 'advertise-form' );
	}

	$to = get_theme_mod( 'arr_advertise_email', get_theme_mod( 'arr_contribute_email', ARR_CONTACT_EMAIL ) );

	$body = implode( "\n", array_filter( array(
		__( 'A new advertising enquiry was submitted on the website.', 'arr-theme' ),
		'',
		__( 'Name:', 'arr-theme' ) . ' ' . $name,
		$company ? __( 'Organisation:', 'arr-theme' ) . ' ' . $company : '',
		__( 'Email:', 'arr-theme' ) . ' ' . $email,
		$phone   ? __( 'Phone:', 'arr-theme' ) . ' ' . $phone : '',
		$website ? __( 'Website:', 'arr-theme' ) . ' ' . $website : '',
		$interest ? __( 'Interested in:', 'arr-theme' ) . ' ' . $interest : '',
		'',
		__( 'Message:', 'arr-theme' ),
		$message,
		'',
		'--',
		/* translators: %s: site name. */
		sprintf( __( 'Sent from the %s advertising form.', 'arr-theme' ), get_bloginfo( 'name' ) ),
	), 'strlen' ) );

	$sent = wp_mail(
		$to,
		/* translators: %s: enquirer's organisation or name. */
		sprintf( __( 'Advertising enquiry — %s', 'arr-theme' ), $company ? $company : $name ),
		$body,
		arr_form_headers( $name, $email )
	);

	arr_form_record( 'advertise' );
	arr_form_redirect( 'advertise', $sent ? 'sent' : 'failed', $referer, 'advertise-form' );
}
add_action( 'admin_post_nopriv_arr_advertise', 'arr_handle_advertise_form' );
add_action( 'admin_post_arr_advertise', 'arr_handle_advertise_form' );

function arr_advertise_notice() {
	return arr_form_notice( 'advertise', __( 'Thank you — your enquiry has reached us. We reply to every enquiry, usually within two working days.', 'arr-theme' ) );
}
