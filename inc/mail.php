<?php
/**
 * Outgoing mail identity.
 *
 * WordPress defaults the From address to wordpress@<site domain>, which on a
 * temporary host is wordpress@arrafricanrenaissancereview.kinsta.cloud — a
 * mailbox nobody reads, on a domain that means nothing to the reader.
 *
 * Worse, plugins that default their From to the admin address end up sending
 * mail claiming to come from a gmail.com address. Gmail publishes a DMARC
 * policy telling receiving servers to reject exactly that, so those messages
 * are refused or filed as spam no matter which sending service is used behind
 * them. The From address must always be on a domain the site controls.
 *
 * This sets one identity for everything WordPress sends. Plugins that set
 * their own From header still win — those are changed in the plugin's own
 * settings, not here.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * The address outgoing mail is sent from.
 *
 * The configured address is only used once something is actually authorised to
 * send for its domain. Claiming to be editor@arreview.africa from a server that
 * domain's DNS has never heard of fails SPF, and a receiving server treats that
 * worse than mail from the site's own hostname — so before the relay is set up,
 * using the nicer-looking address would make delivery worse, not better.
 *
 * Two things make the configured address safe to use:
 *
 *   - an SMTP relay is configured, so mail leaves through a provider the
 *     domain's DNS authorises; or
 *   - the address is already on the site's own domain, which the web host
 *     signs for us.
 *
 * Otherwise mail goes out from the site's own hostname, which the host is
 * authorised for. The editorial address still reaches the reader as Reply-To,
 * so replies land in the right inbox either way. This resolves itself the
 * moment the relay is configured; nothing needs changing here.
 */
function arr_mail_from() {
	$configured = get_theme_mod( 'arr_mail_from', ARR_CONTACT_EMAIL );

	if ( ! is_email( $configured ) ) {
		$configured = ARR_CONTACT_EMAIL;
	}

	if ( function_exists( 'arr_smtp_configured' ) && arr_smtp_configured() ) {
		return $configured;
	}

	$site_host = strtolower( (string) wp_parse_url( home_url(), PHP_URL_HOST ) );
	$site_host = preg_replace( '/^www\./', '', $site_host );
	$from_host = strtolower( (string) substr( strrchr( $configured, '@' ), 1 ) );

	if ( $from_host === $site_host ) {
		return $configured;
	}

	return 'noreply@' . $site_host;
}

/**
 * Whether arr_mail_from() is currently substituting the site's hostname for
 * the configured address. Used by the Email Delivery screen to explain itself.
 */
function arr_mail_from_is_substituted() {
	return arr_mail_from() !== get_theme_mod( 'arr_mail_from', ARR_CONTACT_EMAIL );
}

/**
 * Make sure replies still reach a person.
 *
 * When the From address has been substituted for the site hostname, nothing is
 * reading noreply@ — so the editorial address is added as Reply-To. Skipped if
 * the message already sets its own Reply-To, which the contributor form does to
 * point replies at the applicant.
 */
function arr_add_reply_to( $args ) {
	if ( ! arr_mail_from_is_substituted() ) {
		return $args;
	}

	$headers = $args['headers'];
	if ( is_string( $headers ) ) {
		$headers = array_filter( preg_split( '/\r\n|\r|\n/', $headers ) );
	}
	if ( ! is_array( $headers ) ) {
		$headers = array();
	}

	foreach ( $headers as $header ) {
		if ( stripos( (string) $header, 'reply-to:' ) === 0 ) {
			return $args;
		}
	}

	$headers[]       = 'Reply-To: ' . get_theme_mod( 'arr_mail_from', ARR_CONTACT_EMAIL );
	$args['headers'] = $headers;

	return $args;
}
add_filter( 'wp_mail', 'arr_add_reply_to' );

/**
 * Late priority so this beats a plugin that sets the same filter casually at
 * the default, while still losing to anything that deliberately sets its own
 * From header on an individual message.
 */
add_filter( 'wp_mail_from', 'arr_mail_from', 20 );

add_filter( 'wp_mail_from_name', function () {
	return get_theme_mod( 'arr_mail_from_name', get_bloginfo( 'name' ) );
}, 20 );
