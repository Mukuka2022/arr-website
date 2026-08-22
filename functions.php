<?php
/**
 * ARR Theme functions
 */

if ( ! defined( 'ABSPATH' ) ) exit;

require get_template_directory() . '/inc/template-helpers.php';
require get_template_directory() . '/inc/acf-fields.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/dynamic-css.php';
require get_template_directory() . '/inc/view-counter.php';

function arr_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array(
		'height'      => 42,
		'width'       => 200,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

	register_nav_menus( array(
		'primary'  => __( 'Primary Menu', 'arr-theme' ),
		'footer_1' => __( 'Footer Column 1', 'arr-theme' ),
		'footer_2' => __( 'Footer Column 2', 'arr-theme' ),
		'footer_3' => __( 'Footer Column 3', 'arr-theme' ),
	) );

	// Card thumbnail size used across homepage/article cards
	add_image_size( 'arr-card', 500, 320, true );
}
add_action( 'after_setup_theme', 'arr_theme_setup' );

/**
 * Cache-busting version for a theme asset.
 *
 * A fixed version string means visitors keep the stylesheet their browser
 * cached the first time they came, so design changes silently never reach
 * them. The file's modification time changes on every edit and on every
 * deploy, so the URL changes exactly when the file does — and not otherwise.
 */
function arr_asset_version( $relative_path ) {
	$file = get_template_directory() . $relative_path;
	return file_exists( $file ) ? (string) filemtime( $file ) : '1.0';
}

function arr_theme_assets() {
	// Google Fonts — matches the approved brand: Playfair Display (headings) + Inter (body)
	wp_enqueue_style( 'arr-google-fonts', 'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap', array(), null );

	// Prototype stylesheet (colors, layout, components)
	wp_enqueue_style( 'arr-prototype', get_template_directory_uri() . '/assets/css/prototype.css', array(), arr_asset_version( '/assets/css/prototype.css' ) );

	// Page-specific layout (hero, category grid, article grid, membership tiers, etc.)
	wp_enqueue_style( 'arr-pages', get_template_directory_uri() . '/assets/css/pages.css', array( 'arr-prototype' ), arr_asset_version( '/assets/css/pages.css' ) );

	// Mobile nav toggle
	wp_enqueue_script( 'arr-main', get_template_directory_uri() . '/assets/js/main.js', array(), arr_asset_version( '/assets/js/main.js' ), true );

	// Small theme-level overrides / dynamic bits
	wp_enqueue_style( 'arr-theme-style', get_stylesheet_uri(), array( 'arr-prototype' ), arr_asset_version( '/style.css' ) );
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
 * Footer column fallbacks, shown until the client assigns their own menus to
 * the three Footer Column locations.
 *
 * Every entry resolves to a page that actually exists; arr_footer_link() skips
 * anything missing. The original set carried four href="#" placeholders
 * (Careers, Podcasts, FAQs, Privacy Policy) and pointed Our Team, Authors and
 * Submissions at pages that were not theirs. Add the links back here — or,
 * better, as a real menu under Appearance → Menus — once those pages exist.
 */
function arr_footer_menu_1_fallback() {
	arr_footer_link( 'about', 'About Us' );
	arr_footer_link( 'authors', 'Our Team' );
}

function arr_footer_menu_2_fallback() {
	arr_footer_link( 'articles', 'Latest Articles' );
	arr_footer_link( 'subscribe', 'Subscribe' );
}

function arr_footer_menu_3_fallback() {
	arr_footer_link( 'contact', 'Contact Us' );
	arr_footer_link( 'privacy-policy', 'Privacy Policy' );
	arr_footer_link( 'terms-conditions', 'Terms & Conditions' );
}

/**
 * Renders one footer link column from its menu location.
 */
function arr_footer_menu_column( $number ) {
	wp_nav_menu( array(
		'theme_location' => 'footer_' . $number,
		'container'      => false,
		'items_wrap'     => '%3$s',
		'depth'          => 1,
		'fallback_cb'    => 'arr_footer_menu_' . $number . '_fallback',
	) );
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
