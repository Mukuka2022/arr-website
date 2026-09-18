<?php
/**
 * Contributors: the writers' role, and what they may do.
 *
 * ARR's writers use WordPress's Contributor role, which submits work for
 * review rather than publishing it. That is what the Editorial Standards page
 * commits to — editorial leadership decides what is published — and what the
 * Contribute page promises applicants. The Author role, which the original demo
 * accounts had, publishes straight to the live site with nobody in between.
 *
 * Contributors cannot upload images by default, so one capability is added
 * here: upload_files. Everything else about the role is unchanged — they still
 * cannot publish, and cannot edit a piece once it has gone live.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Let Contributors upload images.
 *
 * Granted through the user_has_cap filter rather than by writing the capability
 * into the role with add_cap(). add_cap() stores the change in the database,
 * where it outlives the theme: switch themes, or delete this function, and
 * contributors silently keep upload rights nobody remembers granting. As a
 * filter the permission exists exactly as long as this code does, and it
 * applies on every site the theme runs on without a separate step.
 */
function arr_contributor_upload_cap( $allcaps, $caps, $args, $user ) {
	if ( $user instanceof WP_User && in_array( 'contributor', (array) $user->roles, true ) ) {
		$allcaps['upload_files'] = true;
	}
	return $allcaps;
}
add_filter( 'user_has_cap', 'arr_contributor_upload_cap', 10, 4 );

/**
 * Images only, for anyone who cannot publish.
 *
 * upload_files on its own allows every file type WordPress accepts — PDFs,
 * Word documents, audio, video. The request was for contributors to add
 * pictures to their articles, so that is all they get.
 */
function arr_contributor_upload_mimes( $mimes ) {
	if ( current_user_can( 'publish_posts' ) ) {
		return $mimes;
	}

	return array_filter( $mimes, function ( $type ) {
		return 0 === strpos( $type, 'image/' );
	} );
}
add_filter( 'upload_mimes', 'arr_contributor_upload_mimes' );

/**
 * Contributors see only the images they uploaded.
 *
 * With upload_files, WordPress shows the whole media library to anyone who can
 * upload — every image any contributor or editor has ever added. The Editorial
 * Standards page requires permission for every third-party image, which is a
 * reason not to let one writer casually reuse another's pictures, or browse
 * images attached to pieces still in review. Editors keep the full library.
 *
 * Two filters, because the library has two views: the grid and the picker in
 * the editor query over AJAX, and the list view at Media → Library does not.
 */
function arr_contributor_own_media_ajax( $query ) {
	if ( ! current_user_can( 'edit_others_posts' ) ) {
		$query['author'] = get_current_user_id();
	}
	return $query;
}
add_filter( 'ajax_query_attachments_args', 'arr_contributor_own_media_ajax' );

function arr_contributor_own_media_list( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() || current_user_can( 'edit_others_posts' ) ) {
		return;
	}

	global $pagenow;
	if ( 'upload.php' === $pagenow ) {
		$query->set( 'author', get_current_user_id() );
	}
}
add_action( 'pre_get_posts', 'arr_contributor_own_media_list' );

/* ---------- /contributor/ instead of /author/ ---------- */

/** The address base for a writer's page: /contributor/amara-chukwu/. */
const ARR_CONTRIBUTOR_BASE = 'contributor';

/**
 * Bumped when the base changes, so the rules are rebuilt once, not per request.
 * 2: version 1 flushed with the old structure still cached (see below), so it
 * stored /author/ rules under the new version number and never flushed again.
 */
const ARR_CONTRIBUTOR_REWRITE_VERSION = 2;

/**
 * Setting author_base on its own is not enough. WP_Rewrite derives the author
 * address pattern from the base once and caches it in author_structure, and by
 * the time init runs it has usually been computed already — so changing only
 * the base left every link reading /author/ and /contributor/ returning 404.
 * The pattern is set directly alongside the base. With plain permalinks there
 * is no pretty pattern to set, so it is left alone there.
 */
function arr_contributor_author_base() {
	global $wp_rewrite;
	$wp_rewrite->author_base = ARR_CONTRIBUTOR_BASE;

	if ( $wp_rewrite->using_permalinks() ) {
		$wp_rewrite->author_structure = $wp_rewrite->front . ARR_CONTRIBUTOR_BASE . '/%author%';
	}
}
add_action( 'init', 'arr_contributor_author_base' );

function arr_flush_contributor_rewrites() {
	if ( (int) get_option( 'arr_contributor_rewrite_version' ) === ARR_CONTRIBUTOR_REWRITE_VERSION ) {
		return;
	}
	flush_rewrite_rules( false );
	update_option( 'arr_contributor_rewrite_version', ARR_CONTRIBUTOR_REWRITE_VERSION );
}
add_action( 'init', 'arr_flush_contributor_rewrites', 20 );

/**
 * Send the old /author/… addresses to their /contributor/… equivalents.
 *
 * Anything already shared or indexed under /author/ would otherwise 404. Only
 * runs on a 404, so it costs nothing on every other request, and only matches
 * a path that begins with the old base — never a post that happens to mention
 * the word.
 */
function arr_redirect_old_author_urls() {
	if ( ! is_404() ) {
		return;
	}

	$home_path = trim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );
	$path      = trim( (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ), PHP_URL_PATH ), '/' );

	if ( $home_path && 0 === strpos( $path, $home_path . '/' ) ) {
		$path = substr( $path, strlen( $home_path ) + 1 );
	}

	if ( 0 !== strpos( $path, 'author/' ) ) {
		return;
	}

	$rest = substr( $path, strlen( 'author/' ) );
	wp_safe_redirect( home_url( '/' . ARR_CONTRIBUTOR_BASE . '/' . $rest . '/' ), 301 );
	exit;
}
add_action( 'template_redirect', 'arr_redirect_old_author_urls' );
