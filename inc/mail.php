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
 * Falls back to the editorial address. Whatever is used here has to be on a
 * domain whose DNS carries this site's SPF and DKIM records, or receiving
 * servers have no way to tell the message is genuine.
 */
function arr_mail_from() {
	$from = get_theme_mod( 'arr_mail_from', ARR_CONTACT_EMAIL );
	return is_email( $from ) ? $from : ARR_CONTACT_EMAIL;
}

/**
 * Late priority so this beats a plugin that sets the same filter casually at
 * the default, while still losing to anything that deliberately sets its own
 * From header on an individual message.
 */
add_filter( 'wp_mail_from', 'arr_mail_from', 20 );

add_filter( 'wp_mail_from_name', function () {
	return get_theme_mod( 'arr_mail_from_name', get_bloginfo( 'name' ) );
}, 20 );
