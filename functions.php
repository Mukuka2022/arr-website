<?php
/**
 * ARR Theme functions
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function arr_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'arr-theme' ),
		'footer'  => __( 'Footer Menu', 'arr-theme' ),
	) );

	// Card thumbnail size used across homepage/article cards
	add_image_size( 'arr-card', 500, 320, true );
}
add_action( 'after_setup_theme', 'arr_theme_setup' );

function arr_theme_assets() {
	// Google Fonts — matches the approved brand: Playfair Display (headings) + Inter (body)
	wp_enqueue_style( 'arr-google-fonts', 'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap', array(), null );

	// Prototype stylesheet (colors, layout, components)
	wp_enqueue_style( 'arr-prototype', get_template_directory_uri() . '/assets/css/prototype.css', array(), '1.0' );

	// Page-specific layout (hero, category grid, article grid, membership tiers, etc.)
	wp_enqueue_style( 'arr-pages', get_template_directory_uri() . '/assets/css/pages.css', array( 'arr-prototype' ), '1.0' );

	// Mobile nav toggle
	wp_enqueue_script( 'arr-main', get_template_directory_uri() . '/assets/js/main.js', array(), '1.0', true );

	// Small theme-level overrides / dynamic bits
	wp_enqueue_style( 'arr-theme-style', get_stylesheet_uri(), array( 'arr-prototype' ), '1.0' );
}
add_action( 'wp_enqueue_scripts', 'arr_theme_assets' );

/**
 * Fallback menu if no "primary" menu has been created yet in
 * Appearance → Menus, so the site never looks broken out of the box.
 */
function arr_fallback_menu() {
	echo '<a href="' . esc_url( home_url( '/articles/' ) ) . '">Latest</a>';
	echo '<a href="' . esc_url( home_url( '/about/' ) ) . '">About</a>';
	echo '<a href="' . esc_url( home_url( '/subscribe/' ) ) . '">Subscribe</a>';
}

/**
 * Simple reading-time estimate (~200 words/min) for the current post in the loop.
 */
function arr_reading_time() {
	$content = get_post_field( 'post_content', get_the_ID() );
	$word_count = str_word_count( wp_strip_all_tags( $content ) );
	return max( 1, ceil( $word_count / 200 ) );
}

/**
 * Auto-register any ACF field groups placed in /acf-json. Once ACF is
 * installed and active, the field groups appear automatically in
 * wp-admin — no manual "Import" step needed.
 */
add_filter( 'acf/settings/load_json', function( $paths ) {
	$paths[] = get_stylesheet_directory() . '/acf-json';
	return $paths;
} );

/**
 * Register the 7 editorial pillars as default categories on theme activation.
 * Safe to run more than once — wp_insert_category will just skip existing ones.
 */
function arr_register_default_categories() {
	$pillars = array(
		'Governance, Leadership & Public Institutions',
		'Technology, Cybersecurity & Digital Transformation',
		'Economics, Enterprise & Sustainable Development',
		'Faith, Ethics & Society',
		'Science, Education & Knowledge',
		'Africa and the World',
		'History, Culture & Civilisation',
	);
	foreach ( $pillars as $pillar ) {
		if ( ! term_exists( $pillar, 'category' ) ) {
			wp_insert_term( $pillar, 'category' );
		}
	}
}
add_action( 'after_switch_theme', 'arr_register_default_categories' );
