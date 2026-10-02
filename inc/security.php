<?php
/**
 * Hardening that belongs with the theme.
 *
 * Deliberately narrow: this closes off username discovery and nothing else.
 * Firewalling, rate limiting and malware scanning are the host's and the
 * security plugin's job, and duplicating them here would only make the site
 * harder to reason about.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Block the ?author=N probe.
 *
 * Requesting /?author=1 makes WordPress redirect to that user's archive,
 * revealing their login slug. Walking N from 1 upwards therefore enumerates
 * every account — including administrators who have never published and so
 * have no archive of their own. That hands an attacker half of each set of
 * credentials before they start guessing.
 *
 * The pretty /author/{slug}/ archives are left working: this site lists its
 * writers on purpose, and those pages are part of the design.
 */
function arr_block_author_enumeration() {
	if ( is_admin() || is_user_logged_in() ) {
		return;
	}

	if ( ! isset( $_GET['author'] ) ) {
		return;
	}

	wp_safe_redirect( home_url( '/' ), 301 );
	exit;
}
// Priority 0: ahead of redirect_canonical, which is what performs the leak.
add_action( 'template_redirect', 'arr_block_author_enumeration', 0 );

/**
 * Close the REST user routes to anonymous callers.
 *
 * /wp-json/wp/v2/users lists every account's slug without any authentication.
 * Logged-in users keep the routes, because the block editor relies on them for
 * the author dropdown — dropping them outright would break the editor.
 *
 * The theme's own Authors page uses get_users() in PHP, not REST, so it is
 * unaffected.
 */
function arr_restrict_rest_user_routes( $endpoints ) {
	if ( is_user_logged_in() ) {
		return $endpoints;
	}

	unset( $endpoints['/wp/v2/users'] );
	unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );

	return $endpoints;
}
add_filter( 'rest_endpoints', 'arr_restrict_rest_user_routes' );

/**
 * Stop the login form saying which half of the credentials was wrong.
 *
 * WordPress otherwise distinguishes "unknown username" from "wrong password",
 * which confirms an account exists.
 */
function arr_generic_login_error() {
	return __( 'Those details were not recognised. Please try again.', 'arr-theme' );
}
add_filter( 'login_errors', 'arr_generic_login_error' );

/**
 * Drop the WordPress version from the page source and from asset URLs.
 *
 * Not a vulnerability by itself, but it lets scanners skip straight to exploits
 * matching this exact version.
 */
remove_action( 'wp_head', 'wp_generator' );

function arr_remove_version_query( $src ) {
	if ( $src && strpos( $src, 'ver=' . get_bloginfo( 'version' ) ) !== false ) {
		$src = remove_query_arg( 'ver', $src );
	}
	return $src;
}
add_filter( 'style_loader_src', 'arr_remove_version_query', 9999 );
add_filter( 'script_loader_src', 'arr_remove_version_query', 9999 );

/**
 * Switch off XML-RPC.
 *
 * This is the one that matters. xmlrpc.php answers unauthenticated POSTs and
 * offers system.multicall, which packs many method calls into a single HTTP
 * request. An attacker can therefore try hundreds of username and password
 * pairs in one request — which walks straight past any protection that counts
 * failed logins per request, because it only ever sees one.
 *
 * Hiding wp-login.php does nothing about this. Scanners find xmlrpc.php by
 * asking for it, not by following links, and the endpoint accepts credentials
 * whatever the login page is called.
 *
 * Nothing on this site uses XML-RPC: there is no Jetpack, and publishing is
 * done through wp-admin. The cost of turning it off is the WordPress mobile
 * app and trackbacks, neither of which is in use here.
 */
add_filter( 'xmlrpc_enabled', '__return_false' );

/**
 * Drop the amplification method and pingbacks outright.
 *
 * xmlrpc_enabled above closes the authenticated methods, but leaving
 * system.multicall registered keeps the batching machinery in place. Removing
 * the methods is the belt to that braces.
 */
function arr_strip_xmlrpc_methods( $methods ) {
	unset(
		$methods['system.multicall'],
		$methods['pingback.ping'],
		$methods['pingback.extensions.getPingbacks']
	);

	return $methods;
}
add_filter( 'xmlrpc_methods', 'arr_strip_xmlrpc_methods' );

/**
 * Stop advertising the endpoint in response headers.
 *
 * Cosmetic next to the above, but there is no reason to publish a pointer to
 * a service the site does not use.
 */
function arr_remove_pingback_header( $headers ) {
	unset( $headers['X-Pingback'] );
	return $headers;
}
add_filter( 'wp_headers', 'arr_remove_pingback_header' );
