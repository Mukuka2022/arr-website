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
require get_template_directory() . '/inc/login-branding.php';
require get_template_directory() . '/inc/admin-branding.php';
require get_template_directory() . '/inc/security.php';
require get_template_directory() . '/inc/form-shared.php';
require get_template_directory() . '/inc/contribute-form.php';
require get_template_directory() . '/inc/advertise-form.php';
require get_template_directory() . '/inc/mail.php';
require get_template_directory() . '/inc/smtp.php';
require get_template_directory() . '/inc/caricatures.php';
require get_template_directory() . '/inc/menu-builder.php';

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

	// Lets WordPress scale YouTube/Vimeo embeds to the column width. Without
	// this an embed renders at its own fixed size and overflows on a phone.
	add_theme_support( 'responsive-embeds' );

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
	$items = array(
		home_url( '/' )          => __( 'Home', 'arr-theme' ),
		home_url( '/analysis/' ) => __( 'Analysis', 'arr-theme' ),
		home_url( '/ideas/' )    => __( 'Ideas', 'arr-theme' ),
		home_url( '/brief/' )    => __( 'ARR Brief', 'arr-theme' ),
		home_url( '/authors/' )  => __( 'Authors', 'arr-theme' ),
		home_url( '/about/' )    => __( 'About', 'arr-theme' ),
	);

	// Matches wp_nav_menu's markup, so one set of styles covers both and the
	// fallback cannot quietly look different from the real menu.
	echo '<ul class="nav-list">';
	foreach ( $items as $url => $label ) {
		printf( '<li class="menu-item"><a href="%s">%s</a></li>', esc_url( $url ), esc_html( $label ) );
	}
	echo '</ul>';
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
 * Bumped whenever a category is added to arr_default_categories(). The stored
 * value is compared against this on load, so the terms are created exactly
 * once per change and never re-created afterwards.
 */
const ARR_CATEGORY_SET_VERSION = 3;

/** Slugs of the two grouping categories, which hold children but no articles. */
const ARR_IDEAS_CATEGORY = 'ideas';
const ARR_BRIEF_CATEGORY = 'arr-brief';

/**
 * The categories the site ships with, as parent => children.
 *
 * Three kinds of thing live in one taxonomy, separated by depth:
 *
 *   - Top-level with no children — the editorial pillars and Sports. These are
 *     the subject areas, and they are what the ANALYSIS menu, the homepage
 *     strip and the Analysis page list.
 *   - "Ideas" — a grouping whose children are the strands of thought ARR
 *     covers.
 *   - "ARR Brief" — a grouping whose children are the recurring formats of the
 *     newsletter.
 *
 * Hierarchy rather than a second taxonomy because the client manages all of it
 * in one familiar place (Posts → Categories), and WordPress already gives
 * parent/child archives for free. The cost is that the two groupings would
 * otherwise appear beside the pillars in every list — which is why
 * arr_pillar_categories() filters to top level and excludes them by slug.
 */
function arr_default_categories() {
	return array(
		'Governance, Leadership & Public Institutions'        => array(),
		'Technology, Cybersecurity & Digital Transformation'  => array(),
		'Economics, Enterprise & Sustainable Development'     => array(),
		'Faith, Ethics & Society'                             => array(),
		'Science, Education & Knowledge'                      => array(),
		'Africa and the World'                                => array(),
		'History, Culture & Civilisation'                     => array(),
		'Personal Development'                                => array(),
		'Sports'                                              => array(),

		'Ideas' => array(
			'African/Zambian Thought',
			'Philosophy',
			'Political Thought',
			'Economic Thought',
			'Knowledge & Intellectualism',
			'African Intellectual History',
			'Ideas That Changed Africa',
			"Ideas for Africa's Future",
			'Civilisation & Modernity',
			'Decolonisation of Knowledge',
			'African Futures',
		),

		'ARR Brief' => array(
			'The ARR Brief',
			'This Week in Africa',
			'5 Things to Know',
			'The ARR Question',
			'The Week Ahead',
			'Policy Watch',
			"Editor's Note",
		),
	);
}

/**
 * Create a category if it is missing, and return its ID either way.
 *
 * term_exists() is scoped to the parent, so a child may share a name with a
 * category elsewhere in the tree without either being mistaken for the other.
 */
function arr_ensure_category( $name, $parent = 0, $slug = '' ) {
	$existing = term_exists( $name, 'category', $parent ? $parent : null );
	if ( $existing ) {
		return (int) ( is_array( $existing ) ? $existing['term_id'] : $existing );
	}

	$args = array( 'parent' => $parent );
	if ( $slug ) {
		$args['slug'] = $slug;
	}

	$created = wp_insert_term( $name, 'category', $args );

	return is_wp_error( $created ) ? 0 : (int) $created['term_id'];
}

function arr_register_default_categories() {
	foreach ( arr_default_categories() as $name => $children ) {
		// The two groupings get fixed slugs, because the templates find them
		// by slug and the client is free to rename the visible label.
		$slug = '';
		if ( 'Ideas' === $name ) {
			$slug = ARR_IDEAS_CATEGORY;
		} elseif ( 'ARR Brief' === $name ) {
			$slug = ARR_BRIEF_CATEGORY;
		}

		$parent_id = arr_ensure_category( $name, 0, $slug );

		if ( ! $parent_id ) {
			continue;
		}

		foreach ( $children as $child ) {
			arr_ensure_category( $child, $parent_id );
		}
	}
}
add_action( 'after_switch_theme', 'arr_register_default_categories' );

/**
 * Create any categories added since the last run.
 *
 * The activation hook above only fires when the theme is switched on, and this
 * theme has been live since before Sports existed — so on the real site that
 * hook will never run again and the category would simply never appear.
 *
 * Guarded by a version number rather than a per-term check: one autoloaded
 * option comparison per request, no term lookups, and a category the client
 * deliberately deletes stays deleted until the shipped set actually changes.
 */
function arr_ensure_default_categories() {
	if ( (int) get_option( 'arr_category_set_version' ) === ARR_CATEGORY_SET_VERSION ) {
		return;
	}

	arr_register_default_categories();
	update_option( 'arr_category_set_version', ARR_CATEGORY_SET_VERSION );
}
add_action( 'init', 'arr_ensure_default_categories' );
