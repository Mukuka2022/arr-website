<?php
/**
 * Caricatures — a content type of their own.
 *
 * Cartoons were originally two hand-set slots on the homepage. That gave them
 * no page of their own, nothing to share, no archive, and it meant the
 * homepage only changed when somebody remembered to change it. As its own type
 * each cartoon gets a URL, the archive builds itself, and the homepage shows
 * whatever was published most recently without anyone maintaining it.
 *
 * Not an ordinary post in a category: a cartoon is an image with a caption, and
 * putting them in Posts would lay them out as articles and mix them into the
 * Articles listing, the RSS feed and search results alongside the writing.
 *
 * The cartoon itself is the featured image. The editor holds the caption — the
 * note on what the cartoon is commenting on — and the artist is an ACF field.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

const ARR_CARICATURE_TYPE = 'arr_caricature';

/**
 * Bumped when the registration below changes in a way that affects URLs, so
 * the rewrite rules are flushed once on the next load. Flushing on every
 * request is expensive; never flushing means new URLs 404 until someone
 * happens to re-save the permalink settings.
 */
const ARR_CARICATURE_REWRITE_VERSION = 1;

function arr_register_caricature_type() {
	register_post_type( ARR_CARICATURE_TYPE, array(
		'labels' => array(
			'name'               => __( 'Caricatures', 'arr-theme' ),
			'singular_name'      => __( 'Caricature', 'arr-theme' ),
			'add_new'            => __( 'Add New', 'arr-theme' ),
			'add_new_item'       => __( 'Add New Caricature', 'arr-theme' ),
			'edit_item'          => __( 'Edit Caricature', 'arr-theme' ),
			'new_item'           => __( 'New Caricature', 'arr-theme' ),
			'view_item'          => __( 'View Caricature', 'arr-theme' ),
			'search_items'       => __( 'Search Caricatures', 'arr-theme' ),
			'not_found'          => __( 'No caricatures yet', 'arr-theme' ),
			'not_found_in_trash' => __( 'No caricatures in the bin', 'arr-theme' ),
			'featured_image'     => __( 'The cartoon', 'arr-theme' ),
			'set_featured_image' => __( 'Upload the cartoon', 'arr-theme' ),
			'menu_name'          => __( 'Caricatures', 'arr-theme' ),
		),
		'public'        => true,
		'menu_position' => 6, // Directly under Posts, where editors will look.
		'menu_icon'     => 'dashicons-art',
		'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		'has_archive'   => false, // The listing is a page template — see template-caricatures.php.
		'rewrite'       => array( 'slug' => 'caricature', 'with_front' => false ),
		'show_in_rest'  => true,
		'taxonomies'    => array(),
	) );
}
add_action( 'init', 'arr_register_caricature_type' );

/**
 * Flush rewrite rules once after the post type changes.
 *
 * Registered on init at a later priority so the post type above exists by the
 * time the rules are rebuilt.
 */
function arr_flush_caricature_rewrites() {
	if ( (int) get_option( 'arr_caricature_rewrite_version' ) === ARR_CARICATURE_REWRITE_VERSION ) {
		return;
	}

	flush_rewrite_rules( false );
	update_option( 'arr_caricature_rewrite_version', ARR_CARICATURE_REWRITE_VERSION );
}
add_action( 'init', 'arr_flush_caricature_rewrites', 20 );

/**
 * The most recently published caricatures, newest first.
 *
 * @return WP_Post[]
 */
function arr_get_caricatures( $count = 2 ) {
	return get_posts( array(
		'post_type'        => ARR_CARICATURE_TYPE,
		'posts_per_page'   => $count,
		'post_status'      => 'publish',
		'no_found_rows'    => true,
	) );
}

/**
 * Alt text for a cartoon: credits the artist when one is set.
 */
function arr_caricature_alt( $post_id ) {
	$artist = arr_field( 'caricature_artist', '', $post_id );

	return $artist
		/* translators: %s: cartoonist's name. */
		? sprintf( __( 'Editorial cartoon by %s', 'arr-theme' ), $artist )
		: __( 'Editorial cartoon', 'arr-theme' );
}
