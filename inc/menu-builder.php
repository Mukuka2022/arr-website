<?php
/**
 * One-click builder for the primary navigation.
 *
 * The approved menu is six top-level items with twenty-odd children beneath
 * them, and every child has to point at the right category archive or page.
 * Assembling that by hand in Appearance → Menus is twenty minutes of dragging
 * with several easy ways to get it subtly wrong — and it has to be redone on
 * every environment, because menus live in the database and do not travel with
 * the theme.
 *
 * So the structure is described here in code, where it can be reviewed, and
 * built on request. It is a deliberate action behind a confirmation, never
 * automatic: rebuilding replaces the menu's contents, and doing that silently
 * to a menu the client had since adjusted would be its own kind of bug.
 *
 * Afterwards the menu is an ordinary WordPress menu. Nothing here keeps
 * managing it, and anything the client changes by hand simply stays changed.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/** The menu created if no primary menu is assigned yet. */
const ARR_MENU_NAME = 'Main Header';

/**
 * The approved structure, resolved at build time.
 *
 * Children given as a category slug follow the category tree, so the eleven
 * Ideas strands do not have to be restated here and cannot drift out of step
 * with Posts → Categories.
 *
 * @return array
 */
function arr_menu_structure() {
	return array(
		array(
			'label' => __( 'Home', 'arr-theme' ),
			'url'   => home_url( '/' ),
		),
		array(
			'label'    => __( 'Analysis', 'arr-theme' ),
			'template' => 'template-categories.php',
			'children' => 'pillars',
		),
		array(
			'label'    => __( 'Ideas', 'arr-theme' ),
			'template' => 'template-ideas.php',
			'children' => ARR_IDEAS_CATEGORY,
		),
		array(
			'label'    => __( 'ARR Brief', 'arr-theme' ),
			'template' => 'template-brief.php',
			'children' => ARR_BRIEF_CATEGORY,
		),
		array(
			'label'    => __( 'Authors', 'arr-theme' ),
			'template' => 'template-authors.php',
			'children' => array(
				array( 'label' => __( 'All Authors', 'arr-theme' ),          'template' => 'template-authors.php' ),
				array( 'label' => __( 'Become a Contributor', 'arr-theme' ), 'template' => 'template-contribute.php' ),
			),
		),
		array(
			'label'    => __( 'About', 'arr-theme' ),
			'page'     => 'about',
			'children' => array(
				array( 'label' => __( 'Who We Are', 'arr-theme' ), 'page'     => 'about' ),
				array( 'label' => __( 'Editorial', 'arr-theme' ),  'template' => 'template-editorial.php' ),
			),
		),
		array(
			'label'    => __( 'Newsletter', 'arr-theme' ),
			'template' => 'template-subscribe.php',
		),
		array(
			'label'    => __( 'Advertise', 'arr-theme' ),
			'template' => 'template-advertise.php',
		),
		array(
			'label'    => __( 'Contact', 'arr-theme' ),
			'template' => 'template-contact.php',
		),
	);
}

/**
 * Resolve one structure entry to the arguments wp_update_nav_menu_item wants.
 *
 * Returns null when the target does not exist — a page the client has not
 * created yet is skipped rather than added as a link into a 404.
 */
function arr_menu_item_args( $entry ) {
	if ( isset( $entry['url'] ) ) {
		return array(
			'menu-item-title' => $entry['label'],
			'menu-item-url'   => $entry['url'],
			'menu-item-type'  => 'custom',
		);
	}

	$page = null;

	if ( isset( $entry['template'] ) ) {
		$found = get_posts( array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'meta_key'       => '_wp_page_template',
			'meta_value'     => $entry['template'],
			'orderby'        => 'ID',
			'order'          => 'ASC',
		) );
		$page = $found ? $found[0] : null;
	} elseif ( isset( $entry['page'] ) ) {
		$page = get_page_by_path( $entry['page'] );
	}

	if ( ! $page || 'publish' !== $page->post_status ) {
		return null;
	}

	return array(
		'menu-item-title'     => $entry['label'],
		'menu-item-object'    => 'page',
		'menu-item-object-id' => $page->ID,
		'menu-item-type'      => 'post_type',
	);
}

/**
 * Build (or rebuild) the primary menu. Returns a human-readable report.
 */
function arr_build_primary_menu() {
	$locations = get_nav_menu_locations();
	$menu_id   = ! empty( $locations['primary'] ) ? (int) $locations['primary'] : 0;

	if ( ! $menu_id ) {
		$existing = wp_get_nav_menu_object( ARR_MENU_NAME );
		$menu_id  = $existing ? (int) $existing->term_id : (int) wp_create_nav_menu( ARR_MENU_NAME );

		if ( is_wp_error( $menu_id ) || ! $menu_id ) {
			return array( 'ok' => false, 'lines' => array( __( 'Could not create the menu.', 'arr-theme' ) ) );
		}

		$locations['primary'] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );
	}

	// Cleared first so a rebuild is a rebuild, not an append. Without this,
	// running it twice silently doubles every item.
	foreach ( wp_get_nav_menu_items( $menu_id ) as $old ) {
		wp_delete_post( $old->ID, true );
	}

	$lines    = array();
	$position = 0;

	foreach ( arr_menu_structure() as $entry ) {
		$args = arr_menu_item_args( $entry );

		if ( ! $args ) {
			/* translators: %s: menu item label. */
			$lines[] = sprintf( __( 'Skipped "%s" — its page does not exist yet.', 'arr-theme' ), $entry['label'] );
			continue;
		}

		$position++;
		$args['menu-item-status']   = 'publish';
		$args['menu-item-position'] = $position;

		$parent_id = wp_update_nav_menu_item( $menu_id, 0, $args );

		if ( is_wp_error( $parent_id ) ) {
			continue;
		}

		$children = isset( $entry['children'] ) ? $entry['children'] : array();
		$added    = 0;

		// A string means "follow the category tree"; an array is an explicit list.
		if ( is_string( $children ) ) {
			$terms = ( 'pillars' === $children )
				? arr_pillar_categories( 0, false )
				: arr_child_categories( $children );

			foreach ( $terms as $term ) {
				$position++;
				wp_update_nav_menu_item( $menu_id, 0, array(
					'menu-item-title'     => $term->name,
					'menu-item-object'    => 'category',
					'menu-item-object-id' => $term->term_id,
					'menu-item-type'      => 'taxonomy',
					'menu-item-parent-id' => $parent_id,
					'menu-item-status'    => 'publish',
					'menu-item-position'  => $position,
				) );
				$added++;
			}
		} else {
			foreach ( $children as $child ) {
				$child_args = arr_menu_item_args( $child );
				if ( ! $child_args ) {
					/* translators: %s: menu item label. */
					$lines[] = sprintf( __( 'Skipped "%s" — its page does not exist yet.', 'arr-theme' ), $child['label'] );
					continue;
				}
				$position++;
				$child_args['menu-item-parent-id'] = $parent_id;
				$child_args['menu-item-status']    = 'publish';
				$child_args['menu-item-position']  = $position;
				wp_update_nav_menu_item( $menu_id, 0, $child_args );
				$added++;
			}
		}

		$lines[] = $added
			/* translators: 1: menu item label, 2: number of child items. */
			? sprintf( __( 'Added "%1$s" with %2$d items beneath it.', 'arr-theme' ), $entry['label'], $added )
			/* translators: %s: menu item label. */
			: sprintf( __( 'Added "%s".', 'arr-theme' ), $entry['label'] );
	}

	return array( 'ok' => true, 'lines' => $lines );
}

/* ---------- Admin screen ---------- */

function arr_menu_builder_page() {
	add_management_page(
		__( 'ARR Navigation', 'arr-theme' ),
		__( 'ARR Navigation', 'arr-theme' ),
		'edit_theme_options',
		'arr-navigation',
		'arr_render_menu_builder_page'
	);
}
add_action( 'admin_menu', 'arr_menu_builder_page' );

function arr_render_menu_builder_page() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to change menus.', 'arr-theme' ) );
	}

	$report = null;

	if ( isset( $_POST['arr_build_menu'] ) && check_admin_referer( 'arr_build_menu' ) ) {
		$report = arr_build_primary_menu();
	}

	echo '<div class="wrap"><h1>' . esc_html__( 'ARR Navigation', 'arr-theme' ) . '</h1>';

	echo '<p>' . esc_html__( 'Builds the approved navigation in one step: Home, Analysis, Ideas, ARR Brief, Authors and About, with their dropdowns filled in from your categories and pages.', 'arr-theme' ) . '</p>';

	echo '<p><strong>' . esc_html__( 'This replaces everything currently in the primary menu.', 'arr-theme' ) . '</strong> '
		. esc_html__( 'Any items you have added or reordered by hand will be lost. Afterwards it is an ordinary menu again — edit it in Appearance → Menus, and nothing here will touch it unless you run this a second time.', 'arr-theme' ) . '</p>';

	if ( $report ) {
		echo '<div class="notice notice-' . ( $report['ok'] ? 'success' : 'error' ) . '"><p><strong>'
			. esc_html( $report['ok'] ? __( 'Menu rebuilt.', 'arr-theme' ) : __( 'Could not rebuild the menu.', 'arr-theme' ) )
			. '</strong></p><ul style="margin-left:18px;list-style:disc;">';
		foreach ( $report['lines'] as $line ) {
			echo '<li>' . esc_html( $line ) . '</li>';
		}
		echo '</ul></div>';
	}

	echo '<form method="post">';
	wp_nonce_field( 'arr_build_menu' );
	submit_button( __( 'Build the navigation menu', 'arr-theme' ), 'primary', 'arr_build_menu' );
	echo '</form></div>';
}
