<?php
/**
 * Shared template helpers.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Read an ACF field, falling back to the approved prototype copy.
 *
 * The function_exists guard means every page still renders the approved
 * design if the ACF plugin is ever deactivated.
 */
function arr_field( $name, $fallback = '', $post_id = null ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $fallback;
	}

	$value = ( null === $post_id ) ? get_field( $name ) : get_field( $name, $post_id );

	if ( is_string( $value ) ) {
		$value = trim( $value );
	}

	if ( '' === $value || null === $value || false === $value || array() === $value ) {
		return $fallback;
	}

	return $value;
}

/**
 * Resolve a section background colour: per-page ACF field first, then an
 * optional site-wide Customizer setting. Empty means "inherit the palette".
 */
function arr_section_color( $field, $theme_mod = null ) {
	$color = arr_field( $field, '' );

	if ( ! $color && $theme_mod ) {
		$color = get_theme_mod( $theme_mod, '' );
	}

	return sanitize_hex_color( $color ) ? $color : '';
}

/**
 * Split a one-per-line textarea into a clean array.
 */
function arr_lines_to_list( $text ) {
	if ( ! $text ) {
		return array();
	}

	$lines = array_map( 'trim', preg_split( '/\r\n|\r|\n/', $text ) );

	return array_values( array_filter( $lines, 'strlen' ) );
}

/**
 * Social links that the client has actually filled in. Anything left blank
 * is omitted rather than rendered as a dead "#" link.
 */
function arr_social_links() {
	$platforms = array(
		'x'         => array( 'label' => 'X',         'glyph' => '𝕏',  'mod' => 'arr_social_x' ),
		'linkedin'  => array( 'label' => 'LinkedIn',  'glyph' => 'in', 'mod' => 'arr_social_linkedin' ),
		'facebook'  => array( 'label' => 'Facebook',  'glyph' => 'f',  'mod' => 'arr_social_facebook' ),
		'youtube'   => array( 'label' => 'YouTube',   'glyph' => '▶',  'mod' => 'arr_social_youtube' ),
		'instagram' => array( 'label' => 'Instagram', 'glyph' => 'ig', 'mod' => 'arr_social_instagram' ),
	);

	$links = array();

	foreach ( $platforms as $key => $platform ) {
		$url = get_theme_mod( $platform['mod'], '' );
		if ( $url ) {
			$links[] = array(
				'url'      => $url,
				'label'    => $platform['label'],
				'glyph'    => $platform['glyph'],
				'external' => true,
			);
		}
	}

	$email = get_theme_mod( 'arr_social_email', '' );
	if ( $email ) {
		$links[] = array(
			'url'      => 'mailto:' . $email,
			'label'    => __( 'Email', 'arr-theme' ),
			'glyph'    => '✉',
			'external' => false,
		);
	}

	return $links;
}
