<?php
/**
 * Site-wide settings: brand colours, social links, header/footer text.
 *
 * Page-scoped content lives in ACF instead. Settings here keep their
 * defaults in code, so an untouched site renders the approved design
 * without needing a single database row.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function arr_sanitize_hex( $value ) {
	$color = sanitize_hex_color( $value );
	return $color ? $color : '';
}

function arr_sanitize_url( $value ) {
	return esc_url_raw( $value );
}

function arr_sanitize_text( $value ) {
	return sanitize_text_field( $value );
}

function arr_sanitize_textarea( $value ) {
	return wp_kses_post( $value );
}

function arr_sanitize_email( $value ) {
	return sanitize_email( $value );
}

function arr_sanitize_checkbox( $value ) {
	return (bool) $value;
}

function arr_sanitize_number( $value ) {
	return absint( $value );
}

/**
 * The brand palette. Defaults are the exact values in prototype.css, so an
 * untouched setting emits nothing and the stylesheet stays authoritative.
 */
function arr_palette_tokens() {
	return array(
		'midnight'       => array( 'default' => '#0B1F3A', 'label' => 'Midnight — headers, footers, dark bands' ),
		'midnight-light' => array( 'default' => '#142c52', 'label' => 'Midnight Light — hero sidebar' ),
		'gold'           => array( 'default' => '#C89B3C', 'label' => 'Gold — accents, buttons, eyebrow labels' ),
		'gold-light'     => array( 'default' => '#ddb768', 'label' => 'Gold Light — button hover' ),
		'emerald'        => array( 'default' => '#1C6B52', 'label' => 'Emerald — category labels, checkmarks' ),
		'ivory'          => array( 'default' => '#FAFAF7', 'label' => 'Ivory — page background' ),
		'charcoal'       => array( 'default' => '#2C2C2C', 'label' => 'Charcoal — body text' ),
		'hairline'       => array( 'default' => '#e4ddd0', 'label' => 'Hairline — borders' ),
		'muted'          => array( 'default' => '#6b6459', 'label' => 'Muted — secondary text' ),
	);
}

function arr_customize_register( $wp_customize ) {

	$wp_customize->add_panel( 'arr_theme', array(
		'title'       => __( 'ARR Theme Settings', 'arr-theme' ),
		'description' => __( 'Colours, social links and the text used across every page. Page-specific content is edited on each page itself.', 'arr-theme' ),
		'priority'    => 30,
	) );

	/* ---------- Brand colours ---------- */

	$wp_customize->add_section( 'arr_palette', array(
		'title'       => __( 'Brand Colours', 'arr-theme' ),
		'description' => __( 'These recolour the whole site at once. Individual sections can override them on their own page.', 'arr-theme' ),
		'panel'       => 'arr_theme',
		'priority'    => 10,
	) );

	foreach ( arr_palette_tokens() as $token => $meta ) {
		$id = 'arr_color_' . str_replace( '-', '_', $token );

		$wp_customize->add_setting( $id, array(
			'default'           => $meta['default'],
			'sanitize_callback' => 'arr_sanitize_hex',
			'transport'         => 'refresh',
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $id, array(
			'label'   => $meta['label'],
			'section' => 'arr_palette',
		) ) );
	}

	/* ---------- Global section colours ---------- */

	$wp_customize->add_section( 'arr_section_colors', array(
		'title'       => __( 'Header & Footer Colours', 'arr-theme' ),
		'description' => __( 'Leave blank to use the Midnight brand colour.', 'arr-theme' ),
		'panel'       => 'arr_theme',
		'priority'    => 20,
	) );

	foreach ( array(
		'arr_header_bg_color' => __( 'Header background', 'arr-theme' ),
		'arr_footer_bg_color' => __( 'Footer background', 'arr-theme' ),
	) as $id => $label ) {
		$wp_customize->add_setting( $id, array(
			'default'           => '',
			'sanitize_callback' => 'arr_sanitize_hex',
			'transport'         => 'refresh',
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $id, array(
			'label'   => $label,
			'section' => 'arr_section_colors',
		) ) );
	}

	/* ---------- Social links ---------- */

	$wp_customize->add_section( 'arr_social', array(
		'title'       => __( 'Social Media Links', 'arr-theme' ),
		'description' => __( 'Leave a field blank to hide that icon in the footer.', 'arr-theme' ),
		'panel'       => 'arr_theme',
		'priority'    => 30,
	) );

	foreach ( array(
		'arr_social_x'         => __( 'X (Twitter) URL', 'arr-theme' ),
		'arr_social_linkedin'  => __( 'LinkedIn URL', 'arr-theme' ),
		'arr_social_facebook'  => __( 'Facebook URL', 'arr-theme' ),
		'arr_social_youtube'   => __( 'YouTube URL', 'arr-theme' ),
		'arr_social_instagram' => __( 'Instagram URL', 'arr-theme' ),
	) as $id => $label ) {
		$wp_customize->add_setting( $id, array(
			'default'           => '',
			'sanitize_callback' => 'arr_sanitize_url',
		) );

		$wp_customize->add_control( $id, array(
			'label'   => $label,
			'section' => 'arr_social',
			'type'    => 'url',
		) );
	}

	$wp_customize->add_setting( 'arr_social_email', array(
		'default'           => '',
		'sanitize_callback' => 'arr_sanitize_email',
	) );

	$wp_customize->add_control( 'arr_social_email', array(
		'label'   => __( 'Contact email address', 'arr-theme' ),
		'section' => 'arr_social',
		'type'    => 'email',
	) );

	/* ---------- Header ---------- */

	$wp_customize->add_section( 'arr_header_settings', array(
		'title'    => __( 'Header', 'arr-theme' ),
		'panel'    => 'arr_theme',
		'priority' => 40,
	) );

	$wp_customize->add_setting( 'arr_brand_name', array(
		'default'           => 'ARR',
		'sanitize_callback' => 'arr_sanitize_text',
	) );
	$wp_customize->add_control( 'arr_brand_name', array(
		'label'       => __( 'Brand name beside the logo', 'arr-theme' ),
		'description' => __( 'The large text next to the logo, in the header and footer. Keep it short — long names crowd the navigation.', 'arr-theme' ),
		'section'     => 'arr_header_settings',
		'type'        => 'text',
	) );

	$wp_customize->add_setting( 'arr_brand_tagline', array(
		'default'           => '',
		'sanitize_callback' => 'arr_sanitize_text',
	) );
	$wp_customize->add_control( 'arr_brand_tagline', array(
		'label'       => __( 'Small text under the brand name', 'arr-theme' ),
		'description' => __( 'Leave blank to use the site title.', 'arr-theme' ),
		'section'     => 'arr_header_settings',
		'type'        => 'text',
	) );

	$wp_customize->add_setting( 'arr_header_cta_show', array(
		'default'           => true,
		'sanitize_callback' => 'arr_sanitize_checkbox',
	) );
	$wp_customize->add_control( 'arr_header_cta_show', array(
		'label'   => __( 'Show the header button', 'arr-theme' ),
		'section' => 'arr_header_settings',
		'type'    => 'checkbox',
	) );

	$wp_customize->add_setting( 'arr_header_cta_text', array(
		'default'           => 'Contribute',
		'sanitize_callback' => 'arr_sanitize_text',
	) );
	$wp_customize->add_control( 'arr_header_cta_text', array(
		'label'   => __( 'Header button label', 'arr-theme' ),
		'section' => 'arr_header_settings',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'arr_header_cta_link', array(
		'default'           => '',
		'sanitize_callback' => 'arr_sanitize_url',
	) );
	$wp_customize->add_control( 'arr_header_cta_link', array(
		'label'       => __( 'Header button link', 'arr-theme' ),
		'description' => __( 'Leave blank to link to the Subscribe page.', 'arr-theme' ),
		'section'     => 'arr_header_settings',
		'type'        => 'url',
	) );

	/* ---------- Footer ---------- */

	$wp_customize->add_section( 'arr_footer_settings', array(
		'title'       => __( 'Footer', 'arr-theme' ),
		'description' => __( 'Footer links are managed under Appearance → Menus, in the three Footer Column locations.', 'arr-theme' ),
		'panel'       => 'arr_theme',
		'priority'    => 50,
	) );

	$wp_customize->add_setting( 'arr_footer_tagline', array(
		'default'           => "Shaping Africa's Intellectual Renaissance. Independent. Research-driven. Unapologetically African.",
		'sanitize_callback' => 'arr_sanitize_textarea',
	) );
	$wp_customize->add_control( 'arr_footer_tagline', array(
		'label'   => __( 'Footer tagline', 'arr-theme' ),
		'section' => 'arr_footer_settings',
		'type'    => 'textarea',
	) );

	// Note: get_theme_mod() runs string values through sprintf(), so tokens
	// must not use % as their delimiter.
	$wp_customize->add_setting( 'arr_footer_copyright', array(
		'default'           => '&copy; {year} {site}. All rights reserved.',
		'sanitize_callback' => 'arr_sanitize_textarea',
	) );
	$wp_customize->add_control( 'arr_footer_copyright', array(
		'label'       => __( 'Copyright line', 'arr-theme' ),
		'description' => __( 'Use {year} for the current year and {site} for the site title.', 'arr-theme' ),
		'section'     => 'arr_footer_settings',
		'type'        => 'text',
	) );

	foreach ( array(
		'arr_footer_col1_heading' => array( 'default' => 'Company',   'label' => __( 'Column 1 heading', 'arr-theme' ) ),
		'arr_footer_col2_heading' => array( 'default' => 'Resources', 'label' => __( 'Column 2 heading', 'arr-theme' ) ),
		'arr_footer_col3_heading' => array( 'default' => 'Support',   'label' => __( 'Column 3 heading', 'arr-theme' ) ),
	) as $id => $meta ) {
		$wp_customize->add_setting( $id, array(
			'default'           => $meta['default'],
			'sanitize_callback' => 'arr_sanitize_text',
		) );
		$wp_customize->add_control( $id, array(
			'label'   => $meta['label'],
			'section' => 'arr_footer_settings',
			'type'    => 'text',
		) );
	}

	$wp_customize->add_setting( 'arr_footer_logo_height', array(
		'default'           => 38,
		'sanitize_callback' => 'arr_sanitize_number',
	) );
	$wp_customize->add_control( 'arr_footer_logo_height', array(
		'label'       => __( 'Footer logo height (px)', 'arr-theme' ),
		'section'     => 'arr_footer_settings',
		'type'        => 'number',
		'input_attrs' => array( 'min' => 16, 'max' => 120 ),
	) );

	/* ---------- Articles, archives, errors ---------- */

	$wp_customize->add_section( 'arr_post_settings', array(
		'title'       => __( 'Articles, Archives & Errors', 'arr-theme' ),
		'description' => __( 'Wording used on category archives, single articles, search results and the 404 page.', 'arr-theme' ),
		'panel'       => 'arr_theme',
		'priority'    => 60,
	) );

	foreach ( array(
		'arr_archive_eyebrow'    => array( 'default' => 'Category',                      'label' => __( 'Archive eyebrow label', 'arr-theme' ) ),
		'arr_archive_all_pill'   => array( 'default' => 'All',                           'label' => __( '"All" filter pill label', 'arr-theme' ) ),
		'arr_archive_empty_text' => array( 'default' => 'No articles here yet.',         'label' => __( 'Empty archive message', 'arr-theme' ) ),
		'arr_404_eyebrow'        => array( 'default' => '404',                           'label' => __( '404 eyebrow', 'arr-theme' ) ),
		'arr_404_title'          => array( 'default' => 'Page not found',                'label' => __( '404 heading', 'arr-theme' ) ),
		'arr_404_text'           => array( 'default' => 'The page you were looking for has moved or no longer exists.', 'label' => __( '404 message', 'arr-theme' ) ),
		'arr_404_button_text'    => array( 'default' => 'Back to Home',                  'label' => __( '404 button label', 'arr-theme' ) ),
		'arr_search_eyebrow'     => array( 'default' => 'Search',                        'label' => __( 'Search eyebrow', 'arr-theme' ) ),
		'arr_search_empty_text'  => array( 'default' => 'No articles matched your search. Try a different term.', 'label' => __( 'No search results message', 'arr-theme' ) ),
	) as $id => $meta ) {
		$wp_customize->add_setting( $id, array(
			'default'           => $meta['default'],
			'sanitize_callback' => 'arr_sanitize_text',
		) );
		$wp_customize->add_control( $id, array(
			'label'   => $meta['label'],
			'section' => 'arr_post_settings',
			'type'    => 'text',
		) );
	}

	$wp_customize->add_setting( 'arr_single_author_bio', array(
		'default'           => 'Contributor, African Renaissance Review',
		'sanitize_callback' => 'arr_sanitize_text',
	) );
	$wp_customize->add_control( 'arr_single_author_bio', array(
		'label'       => __( 'Fallback author bio', 'arr-theme' ),
		'description' => __( 'Shown when an author has not written a bio in their profile.', 'arr-theme' ),
		'section'     => 'arr_post_settings',
		'type'        => 'text',
	) );
}
add_action( 'customize_register', 'arr_customize_register' );
