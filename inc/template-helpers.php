<?php
/**
 * Shared template helpers.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * The publication's real contact details.
 *
 * Held as constants because three separate places need the same value — the
 * contact page, the footer email icon and the Customizer default. Anything the
 * client enters in wp-admin overrides these; they exist so the details are
 * never blank and never disagree with each other.
 *
 * The phone number is stored in international E.164 form for the tel: link and
 * formatted separately for display.
 */
define( 'ARR_CONTACT_EMAIL', 'editor@arreview.africa' );
define( 'ARR_CONTACT_PHONE', '+260 96 584 5592' );

/**
 * Slug of the category that drives the Editor's Notes page. The category's
 * display name is the client's to change; this slug is not.
 */
define( 'ARR_NOTES_CATEGORY', 'editors-notes' );

/**
 * Strip a phone number down to what a tel: href accepts.
 *
 * Keeps a leading "+" and digits, drops the spaces and brackets people type.
 * Without this, "+260 96 584 5592" produces a link some Android dialers refuse.
 */
function arr_tel_href( $number ) {
	$digits = preg_replace( '/[^0-9]/', '', $number );
	return 'tel:' . ( strpos( trim( $number ), '+' ) === 0 ? '+' : '' ) . $digits;
}

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
 * The category that holds Editor's Notes, created on first use.
 *
 * The slug is what identifies it, so the client can rename the category in
 * wp-admin ("Letter from the Editor", say) and the page keeps working. Only the
 * slug must stay as it is.
 *
 * Created lazily rather than on theme activation because the theme was already
 * active on the live site when this feature was added — an activation hook
 * would never have fired there. The option flag keeps this to one term lookup
 * for the life of the install rather than one per request.
 *
 * @return WP_Term|null
 */
function arr_editors_notes_category() {
	$category = get_category_by_slug( ARR_NOTES_CATEGORY );
	if ( $category ) {
		return $category;
	}

	if ( get_option( 'arr_notes_category_created' ) ) {
		return null; // Created once and since deleted on purpose — don't recreate it.
	}

	$created = wp_insert_term( "Editor's Notes", 'category', array(
		'slug'        => ARR_NOTES_CATEGORY,
		'description' => __( 'Short reflections from the editorial desk. Posts in this category appear on the Editor\'s Notes page.', 'arr-theme' ),
	) );

	update_option( 'arr_notes_category_created', 1 );

	if ( is_wp_error( $created ) ) {
		return null;
	}

	return get_term( $created['term_id'], 'category' );
}

/**
 * Posts related to $post_id, best match first.
 *
 * Relatedness is "shares a category", which on this site means shares an
 * editorial pillar — the most meaningful signal available without a tagging
 * discipline the client has not been asked to maintain.
 *
 * If that turns up fewer than $count posts (common early on, when a pillar has
 * only one or two articles), the remainder is topped up with the most recent
 * posts so the section is never half-empty or ragged. Returns an empty array
 * on a site with no other published posts, and the template hides itself.
 *
 * @return WP_Post[]
 */
function arr_related_posts( $post_id, $count = 3 ) {
	$categories = wp_get_post_categories( $post_id );

	$args = array(
		'post__not_in'        => array( $post_id ),
		'posts_per_page'      => $count,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	);

	$related = $categories
		? get_posts( array_merge( $args, array( 'category__in' => $categories ) ) )
		: array();

	if ( count( $related ) < $count ) {
		$exclude = array_merge( array( $post_id ), wp_list_pluck( $related, 'ID' ) );

		$filler = get_posts( array_merge( $args, array(
			'post__not_in'   => $exclude,
			'posts_per_page' => $count - count( $related ),
		) ) );

		$related = array_merge( $related, $filler );
	}

	return $related;
}

/**
 * The homepage advert slots that actually have a banner uploaded.
 *
 * Returned in slot order with the blanks removed, so an empty slot 2 doesn't
 * leave a hole between adverts 1 and 3 — and so the template can hide the whole
 * section by checking one thing.
 */
function arr_home_ads( $slots = 3 ) {
	$ads = array();

	for ( $i = 1; $i <= $slots; $i++ ) {
		$image = arr_field( "ad_{$i}_image", '' );
		if ( ! $image ) {
			continue;
		}

		$ads[] = array(
			'image' => $image,
			'link'  => arr_field( "ad_{$i}_link", '' ),
			'alt'   => arr_field( "ad_{$i}_alt", '' ),
		);
	}

	return $ads;
}

/**
 * The social platforms the footer can show, in display order.
 *
 * Single source of truth: the Customizer builds its controls from this list and
 * arr_social_links() reads it back, so adding a platform is a one-line change
 * here rather than two lists that quietly drift apart.
 *
 * The client's real accounts are the defaults, so the icons render correctly on
 * a fresh install with nothing entered. A URL set in the Customizer still wins;
 * clearing a field to blank hides that icon.
 */
function arr_social_platforms() {
	return array(
		'linkedin' => array(
			'label'   => __( 'LinkedIn', 'arr-theme' ),
			'glyph'   => 'in',
			'mod'     => 'arr_social_linkedin',
			'default' => 'https://www.linkedin.com/company/144824906',
		),
		'facebook' => array(
			'label'   => __( 'Facebook', 'arr-theme' ),
			'glyph'   => 'f',
			'mod'     => 'arr_social_facebook',
			'default' => 'https://web.facebook.com/profile.php?id=61593932194249',
		),
		'whatsapp' => array(
			'label'   => __( 'WhatsApp Channel', 'arr-theme' ),
			'glyph'   => '✆',
			'mod'     => 'arr_social_whatsapp',
			'default' => 'https://whatsapp.com/channel/0029VbD6O366xCSYhdbKGk3D',
		),
		'x' => array(
			'label'   => __( 'X (Twitter)', 'arr-theme' ),
			'glyph'   => '𝕏',
			'mod'     => 'arr_social_x',
			'default' => '',
		),
		'youtube' => array(
			'label'   => __( 'YouTube', 'arr-theme' ),
			'glyph'   => '▶',
			'mod'     => 'arr_social_youtube',
			'default' => '',
		),
		'instagram' => array(
			'label'   => __( 'Instagram', 'arr-theme' ),
			'glyph'   => 'ig',
			'mod'     => 'arr_social_instagram',
			'default' => '',
		),
	);
}

/**
 * Social links that the client has actually filled in. Anything left blank
 * is omitted rather than rendered as a dead "#" link.
 */
function arr_social_links() {
	$links = array();

	foreach ( arr_social_platforms() as $platform ) {
		$url = get_theme_mod( $platform['mod'], $platform['default'] );
		if ( $url ) {
			$links[] = array(
				'url'      => $url,
				'label'    => $platform['label'],
				'glyph'    => $platform['glyph'],
				'external' => true,
			);
		}
	}

	$email = get_theme_mod( 'arr_social_email', ARR_CONTACT_EMAIL );
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

/**
 * Echo a footer link to a page, looked up by slug.
 *
 * Resolving the page rather than hardcoding home_url('/about/') means the link
 * survives the client renaming a slug, and — more importantly — nothing is
 * printed at all when the page does not exist. A footer full of href="#"
 * placeholders is worse than a shorter footer: readers click them, and search
 * engines index them.
 */
function arr_footer_link( $slug, $label ) {
	$page = get_page_by_path( $slug );

	if ( ! $page || 'publish' !== $page->post_status ) {
		return;
	}

	echo '<a href="' . esc_url( get_permalink( $page ) ) . '">' . esc_html( $label ) . '</a>';
}

/**
 * Share targets for a single article.
 *
 * These are plain links to each network's own share endpoint — deliberately
 * not an official share widget or SDK. Those load third-party JavaScript on
 * every article view, which tracks readers who never click a thing and adds a
 * remote script to every page. A link costs nothing and only reaches the
 * network when the reader chooses to share.
 */
function arr_share_links( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$url     = get_permalink( $post_id );
	$title   = wp_strip_all_tags( get_the_title( $post_id ) );

	if ( ! $url ) {
		return array();
	}

	$e_url   = rawurlencode( $url );
	$e_title = rawurlencode( $title );

	return array(
		array(
			'label' => __( 'Share on X', 'arr-theme' ),
			'name'  => 'X',
			'url'   => 'https://twitter.com/intent/tweet?text=' . $e_title . '&url=' . $e_url,
			'icon'  => 'M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z',
		),
		array(
			'label' => __( 'Share on Facebook', 'arr-theme' ),
			'name'  => 'Facebook',
			'url'   => 'https://www.facebook.com/sharer/sharer.php?u=' . $e_url,
			'icon'  => 'M22 12.06C22 6.5 17.52 2 12 2S2 6.5 2 12.06c0 5 3.66 9.15 8.44 9.94v-7.03H7.9v-2.9h2.54V9.85c0-2.51 1.49-3.9 3.77-3.9 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.78-1.63 1.57v1.88h2.78l-.45 2.9h-2.33V22c4.78-.79 8.44-4.94 8.44-9.94z',
		),
		array(
			'label' => __( 'Share on LinkedIn', 'arr-theme' ),
			'name'  => 'LinkedIn',
			'url'   => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $e_url,
			'icon'  => 'M4.98 3.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5zM3 9h4v12H3zM9 9h3.8v1.71h.05c.53-1 1.83-2.06 3.76-2.06 4.02 0 4.76 2.65 4.76 6.1V21h-4v-5.44c0-1.3-.02-2.97-1.81-2.97-1.81 0-2.09 1.41-2.09 2.87V21H9z',
		),
		array(
			'label' => __( 'Share on WhatsApp', 'arr-theme' ),
			'name'  => 'WhatsApp',
			'url'   => 'https://api.whatsapp.com/send?text=' . $e_title . '%20' . $e_url,
			'icon'  => 'M12.04 2c-5.46 0-9.9 4.44-9.9 9.9 0 1.75.46 3.45 1.32 4.95L2 22l5.3-1.38a9.86 9.86 0 0 0 4.74 1.21h.01c5.46 0 9.9-4.44 9.9-9.9 0-2.64-1.03-5.13-2.9-7A9.82 9.82 0 0 0 12.04 2zm0 18.02h-.01a8.2 8.2 0 0 1-4.18-1.15l-.3-.18-3.1.81.83-3.03-.2-.31a8.17 8.17 0 0 1-1.26-4.36c0-4.53 3.69-8.22 8.23-8.22 2.2 0 4.26.86 5.81 2.41a8.16 8.16 0 0 1 2.41 5.82c0 4.53-3.69 8.21-8.23 8.21zm4.52-6.16c-.25-.12-1.46-.72-1.69-.8-.22-.09-.39-.13-.55.12-.16.25-.63.8-.78.97-.14.16-.29.18-.53.06-.25-.12-1.05-.39-1.99-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.01-.38.11-.5.11-.11.25-.29.37-.44.13-.15.17-.25.25-.41.08-.17.04-.31-.02-.43-.06-.12-.55-1.34-.76-1.83-.2-.48-.4-.42-.55-.42h-.47a.9.9 0 0 0-.65.3c-.22.25-.86.84-.86 2.05s.88 2.38 1 2.54c.12.17 1.73 2.64 4.19 3.7.59.26 1.04.41 1.4.52.59.19 1.12.16 1.55.1.47-.07 1.46-.6 1.66-1.18.21-.58.21-1.07.15-1.18-.06-.1-.22-.16-.47-.29z',
		),
		array(
			'label' => __( 'Share by email', 'arr-theme' ),
			'name'  => 'Email',
			'url'   => 'mailto:?subject=' . $e_title . '&body=' . $e_url,
			'icon'  => 'M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 4-8 5-8-5V6l8 5 8-5z',
		),
	);
}
