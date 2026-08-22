<?php
/**
 * Article view counting.
 *
 * Views are reported from the browser over the REST API rather than being
 * incremented while single.php renders. WP Super Cache serves most article
 * views as static HTML without running PHP at all, so a render-time counter
 * would miss nearly every real view and over-count only the uncached ones.
 *
 * What is stored is a single integer per post. No IP address, no user agent
 * and no identifier for the reader is ever written to the database — the
 * repeat-view guard hashes those into a transient key and nothing else.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'ARR_VIEWS_META', 'arr_view_count' );

/**
 * How long before the same reader counts again on the same article.
 */
function arr_view_window() {
	return (int) apply_filters( 'arr_view_window', 6 * HOUR_IN_SECONDS );
}

function arr_get_view_count( $post_id ) {
	return (int) get_post_meta( $post_id, ARR_VIEWS_META, true );
}

/**
 * "12.4K" above a thousand, plain digits below.
 */
function arr_format_view_count( $count ) {
	$count = (int) $count;

	if ( $count >= 1000 ) {
		return number_format( $count / 1000, 1 ) . 'K';
	}

	return number_format_i18n( $count );
}

/**
 * Every published post carries the meta, initialised to zero.
 *
 * Ordering by meta_value_num silently drops posts that have no row at all, so
 * without this a brand new article would be invisible to the Trending query
 * until its first view landed.
 */
function arr_ensure_view_meta( $post_id ) {
	if ( 'post' !== get_post_type( $post_id ) ) return;
	if ( '' === get_post_meta( $post_id, ARR_VIEWS_META, true ) ) {
		update_post_meta( $post_id, ARR_VIEWS_META, 0 );
	}
}
add_action( 'wp_insert_post', 'arr_ensure_view_meta' );

/**
 * One-off backfill for posts that existed before this feature.
 */
function arr_backfill_view_meta() {
	if ( get_option( 'arr_views_backfilled' ) ) return;

	$posts = get_posts( array(
		'numberposts' => -1,
		'post_status' => 'any',
		'fields'      => 'ids',
	) );

	foreach ( $posts as $id ) {
		arr_ensure_view_meta( $id );
	}

	update_option( 'arr_views_backfilled', 1, false );
}
add_action( 'admin_init', 'arr_backfill_view_meta' );

/**
 * Obvious crawlers, so the numbers reflect people.
 */
function arr_is_bot_request() {
	$ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? strtolower( $_SERVER['HTTP_USER_AGENT'] ) : '';
	if ( '' === $ua ) return true;

	foreach ( array( 'bot', 'crawl', 'spider', 'slurp', 'curl', 'wget', 'headless', 'preview', 'monitor', 'python-requests' ) as $needle ) {
		if ( false !== strpos( $ua, $needle ) ) return true;
	}

	return false;
}

/**
 * Transient key identifying this reader + article pair.
 *
 * Salted and hashed: the raw address never reaches storage, and the key cannot
 * be reversed back to a visitor.
 */
function arr_view_guard_key( $post_id ) {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '';
	$ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? substr( $_SERVER['HTTP_USER_AGENT'], 0, 120 ) : '';

	return 'arr_seen_' . md5( wp_salt( 'nonce' ) . '|' . $post_id . '|' . $ip . '|' . $ua );
}

function arr_register_view_route() {
	register_rest_route( 'arr/v1', '/view/(?P<id>\d+)', array(
		'methods'             => 'POST',
		'callback'            => 'arr_rest_record_view',
		'permission_callback' => '__return_true',
	) );
}
add_action( 'rest_api_init', 'arr_register_view_route' );

function arr_rest_record_view( WP_REST_Request $request ) {
	$post_id = (int) $request['id'];
	$post    = get_post( $post_id );

	if ( ! $post || 'post' !== $post->post_type || 'publish' !== $post->post_status ) {
		return new WP_REST_Response( array( 'counted' => false, 'reason' => 'not_countable' ), 404 );
	}

	$counted = false;
	$reason  = '';

	if ( arr_is_bot_request() ) {
		$reason = 'bot';
	} elseif ( is_user_logged_in() && current_user_can( 'edit_posts' ) ) {
		// Editors re-reading their own drafts shouldn't inflate the figure.
		$reason = 'editor';
	} else {
		$key = arr_view_guard_key( $post_id );

		if ( get_transient( $key ) ) {
			$reason = 'repeat';
		} else {
			set_transient( $key, 1, arr_view_window() );
			update_post_meta( $post_id, ARR_VIEWS_META, arr_get_view_count( $post_id ) + 1 );
			$counted = true;
		}
	}

	return new WP_REST_Response( array(
		'counted' => $counted,
		'reason'  => $reason,
		'views'   => arr_get_view_count( $post_id ),
	) );
}

/**
 * Load the reporter on single posts only.
 */
function arr_enqueue_view_counter() {
	if ( ! is_singular( 'post' ) ) return;

	// Don't even load the reporter for editors.
	//
	// The server-side check below cannot catch them on its own: REST only
	// authenticates a cookie when the request carries an X-WP-Nonce header, so
	// an ordinary fetch() from a logged-in admin arrives looking anonymous and
	// is_user_logged_in() returns false. Sending a nonce instead would mean
	// baking one into HTML that WP Super Cache then serves to everyone. Logged-
	// in users are always served uncached pages, so deciding here is both
	// reliable and cache-safe.
	if ( is_user_logged_in() && current_user_can( 'edit_posts' ) ) return;

	wp_enqueue_script(
		'arr-views',
		get_template_directory_uri() . '/assets/js/views.js',
		array(),
		arr_asset_version( '/assets/js/views.js' ),
		true
	);

	wp_localize_script( 'arr-views', 'arrViews', array(
		'endpoint' => esc_url_raw( rest_url( 'arr/v1/view/' . get_the_ID() ) ),
		// Seconds on the page before a view is reported.
		'delay'    => 3,
	) );
}
add_action( 'wp_enqueue_scripts', 'arr_enqueue_view_counter' );
