<?php
/**
 * Prints the colour overrides as CSS custom properties.
 *
 * Three layers resolve in the stylesheet itself, not here:
 *   section override  →  palette token  →  hardcoded value in prototype.css
 * A value is only emitted when it is actually set and differs from the
 * approved default, so an untouched site prints nothing at all and the
 * stylesheets stay authoritative.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Per-section background overrides: ACF field name => CSS custom property.
 * Fields that don't belong to the current page simply return empty.
 */
function arr_section_color_map() {
	return array(
		// Homepage
		'hero_bg_color'           => '--hero-bg',
		'cat_strip_bg_color'      => '--cat-strip-bg',
		'cta_band_bg_color'       => '--cta-band-bg',
		'ads_bg_color'            => '--ad-band-bg',
		'caricature_bg_color'     => '--caricature-bg',
		// About
		'about_banner_bg_color'   => '--banner-bg',
		'about_intro_bg_color'    => '--about-intro-bg',
		'vm_bg_color'             => '--vm-bg',
		'story_bg_color'          => '--about-story-bg',
		'pillars_bg_color'        => '--about-pillars-bg',
		'values_bg_color'         => '--values-bg',
		'team_bg_color'           => '--about-team-bg',
		// Subscribe
		'sub_hero_bg_color'       => '--signup-band-bg',
		'membership_bg_color'     => '--membership-bg',
		'why_bg_color'            => '--why-bg',
		// Contact
		'contact_banner_bg_color' => '--banner-bg',
		'contact_bg_color'        => '--contact-bg',
		// Articles
		'articles_banner_bg_color' => '--banner-bg',
		// Editor's Notes
		'notes_banner_bg_color'   => '--banner-bg',
		// Contribute
		'contribute_banner_bg_color' => '--banner-bg',
		'contribute_bg_color'     => '--contribute-bg',
		// Authors
		'authors_banner_bg_color' => '--banner-bg',
	);
}

function arr_dynamic_css() {
	$declarations = array();

	// Palette tokens — skipped unless the client has changed them.
	foreach ( arr_palette_tokens() as $token => $meta ) {
		$value = get_theme_mod( 'arr_color_' . str_replace( '-', '_', $token ), $meta['default'] );
		$value = sanitize_hex_color( $value );

		if ( $value && strtolower( $value ) !== strtolower( $meta['default'] ) ) {
			$declarations[ '--' . $token ] = $value;
		}
	}

	// Header and footer are site-wide, so they come from the Customizer.
	foreach ( array(
		'arr_header_bg_color' => '--header-bg',
		'arr_footer_bg_color' => '--footer-bg',
	) as $mod => $property ) {
		$value = sanitize_hex_color( get_theme_mod( $mod, '' ) );
		if ( $value ) {
			$declarations[ $property ] = $value;
		}
	}

	$footer_logo_height = absint( get_theme_mod( 'arr_footer_logo_height', 38 ) );
	if ( $footer_logo_height && 38 !== $footer_logo_height ) {
		$declarations['--footer-logo-height'] = $footer_logo_height . 'px';
	}

	// Per-section overrides on the page being viewed.
	if ( is_singular() ) {
		foreach ( arr_section_color_map() as $field => $property ) {
			$value = sanitize_hex_color( arr_field( $field, '' ) );
			if ( $value ) {
				$declarations[ $property ] = $value;
			}
		}
	}

	if ( empty( $declarations ) ) {
		return;
	}

	$css = '';
	foreach ( $declarations as $property => $value ) {
		$css .= $property . ':' . $value . ';';
	}

	printf(
		'<style id="arr-dynamic-css">:root{%s}</style>' . "\n",
		$css
	);
}
add_action( 'wp_head', 'arr_dynamic_css', 20 );
