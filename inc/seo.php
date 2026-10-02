<?php
/**
 * Search-engine metadata that the SEO plugin cannot work out on its own.
 *
 * Rank Math handles the ordinary cases well: it writes titles, canonicals,
 * Open Graph and Twitter tags, and it summarises a post's own text into a
 * meta description. Nothing here duplicates that.
 *
 * What it cannot do is summarise a page whose text is not in post_content.
 * Most of this theme's pages — Ideas, The ARR Brief, Analysis, Contribute,
 * Editors Notes, Caricatures, Advertise, 5 Things to Know — are built by PHP
 * templates that query posts and print markup. Their post_content is empty, so
 * Rank Math finds nothing to summarise and omits the description entirely.
 * Seven of the site's twelve pages were shipping with no description at all,
 * which leaves Google to invent one from whatever text it scrapes off the page.
 *
 * So the descriptions live here, keyed by template file rather than by slug or
 * page ID, which means renaming a page or moving it to a new URL cannot break
 * the link between a page and its description.
 *
 * Every filter below yields to a value that already exists. If an editor writes
 * a description in the Rank Math box on a page, that wins — these are defaults
 * for the empty case, not overrides.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/** Longest description Google will render before truncating, in characters. */
const ARR_SEO_DESCRIPTION_MAX = 160;

/**
 * Default descriptions for the template-driven pages.
 *
 * Written to read as a sentence in a search result, not as a keyword list: the
 * description is not a ranking factor, it is the copy that decides whether
 * somebody clicks. Each one names the publication, because a reader seeing ARR
 * for the first time in a result list has no other context.
 *
 * @return array<string,string> template file name => description
 */
function arr_seo_template_descriptions() {
	return array(
		'template-articles.php'      => __( 'Essays, analysis and commentary from African Renaissance Review — on the ideas, institutions and decisions shaping Africa\'s next century.', 'arr-theme' ),
		'template-categories.php'    => __( 'Our analysis, arranged by the questions it asks: governance and power, faith, technology, finance, culture and Africa\'s place in the world.', 'arr-theme' ),
		'template-ideas.php'         => __( 'The eight pillars of the African Renaissance Review — from governance and enterprise to faith, technology and personal development.', 'arr-theme' ),
		'template-brief.php'         => __( 'The ARR Brief: short, structured reading on the week in African affairs — explainers, data notes and five-point primers from our editors.', 'arr-theme' ),
		'template-five-things.php'   => __( 'Five questions, asked the same way every time: what is happening, how we got here, why Africa should care, what it means, and what to watch.', 'arr-theme' ),
		'template-editors-notes.php' => __( 'Notes from the editors of African Renaissance Review — on what we are publishing, why it matters, and how we are thinking about it.', 'arr-theme' ),
		'template-caricatures.php'   => __( 'Editorial cartoons from African Renaissance Review: African politics, power and public life, drawn with a steady hand and a straight face.', 'arr-theme' ),
		'template-authors.php'       => __( 'The writers, scholars and practitioners who contribute to African Renaissance Review, and the subjects each of them covers.', 'arr-theme' ),
		'template-contribute.php'    => __( 'Write for African Renaissance Review. What we publish, who can contribute, how to pitch an essay or analysis, and what happens after you send it.', 'arr-theme' ),
		'template-advertise.php'     => __( 'Reach readers across Africa and its diaspora. Advertising, sponsorship and partnership options with African Renaissance Review.', 'arr-theme' ),
		'template-subscribe.php'     => __( 'Subscribe to The ARR Brief and get African Renaissance Review\'s analysis by email — no noise, no daily deluge, just the reading that matters.', 'arr-theme' ),
		'template-about.php'         => __( 'A Zambian publication with continental range: who we are, what African Renaissance Review exists to do, and the standards we hold ourselves to.', 'arr-theme' ),
		'template-editorial.php'     => __( 'Editorial standards and governance at African Renaissance Review: how we commission, verify, correct and disclose. Our obligations to readers, in writing.', 'arr-theme' ),
		'template-contact.php'       => __( 'Contact the African Renaissance Review newsroom — editorial enquiries, corrections, pitches, partnerships and reader correspondence.', 'arr-theme' ),
	);
}

/**
 * The description this request should carry, or '' when the default applies.
 *
 * Kept separate from the filter so the same logic serves both the Rank Math
 * path and the plugin-absent fallback, and so it can be called from a template
 * without side effects.
 */
function arr_seo_description() {
	if ( is_singular() && ! is_front_page() ) {
		$template = get_page_template_slug( get_queried_object_id() );
		$defaults = arr_seo_template_descriptions();

		if ( $template && isset( $defaults[ $template ] ) ) {
			return $defaults[ $template ];
		}
	}

	/* A contributor's archive is generated by WordPress, so it has no text of
	   its own either. Their biography is the honest description; the generic
	   sentence is only for a contributor who has not written one yet. */
	if ( is_author() ) {
		$author = get_queried_object();

		if ( $author ) {
			$bio = trim( (string) get_the_author_meta( 'description', $author->ID ) );

			if ( $bio ) {
				return arr_seo_trim( $bio );
			}

			return sprintf(
				/* translators: %s: contributor's display name. */
				__( 'Articles, analysis and commentary by %s for African Renaissance Review.', 'arr-theme' ),
				$author->display_name
			);
		}
	}

	return '';
}

/**
 * Shorten to the length a search result will actually show, on a word boundary.
 *
 * Cutting mid-word looks like a bug; Google's own ellipsis at least reads as a
 * deliberate truncation, so we stop at the last complete word before the limit.
 */
function arr_seo_trim( $text, $limit = ARR_SEO_DESCRIPTION_MAX ) {
	$text = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $text ) ) );

	if ( strlen( $text ) <= $limit ) {
		return $text;
	}

	$cut = substr( $text, 0, $limit );
	$gap = strrpos( $cut, ' ' );

	return rtrim( false === $gap ? $cut : substr( $cut, 0, $gap ), " ,;:." ) . '…';
}

/**
 * Supply the description when Rank Math has none.
 *
 * One filter covers three tags: Rank Math's Open Graph and Twitter description
 * both fall back through Paper::get_description(), which is what this filters.
 * Anything an editor has typed arrives here non-empty and is returned as it is.
 */
function arr_seo_filter_description( $description ) {
	if ( trim( (string) $description ) ) {
		return $description;
	}

	$default = arr_seo_description();

	return $default ? $default : $description;
}
add_filter( 'rank_math/frontend/description', 'arr_seo_filter_description' );

/**
 * The same description, for a site running without an SEO plugin.
 *
 * Deactivating Rank Math should cost the site its rich snippets, not every
 * description on it. Guarded so the two never both print a description tag.
 */
function arr_seo_fallback_description_tag() {
	if ( defined( 'RANK_MATH_VERSION' ) || class_exists( 'WPSEO_Options' ) ) {
		return;
	}

	$description = arr_seo_description();

	if ( ! $description && is_front_page() ) {
		$description = get_bloginfo( 'description' );
	}

	if ( ! $description && is_singular() ) {
		$description = get_the_excerpt();
	}

	if ( ! $description ) {
		return;
	}

	printf( '<meta name="description" content="%s" />' . "\n", esc_attr( arr_seo_trim( $description ) ) );
}
add_action( 'wp_head', 'arr_seo_fallback_description_tag', 1 );

/**
 * Structured data: the publication, its articles, and the path to them.
 *
 * Three changes to what Rank Math emits:
 *
 * 1. Articles are marked NewsArticle rather than BlogPosting. ARR is a
 *    publication, not a blog, and NewsArticle is the type Google's news
 *    surfaces and Top Stories look for. BlogPosting is not eligible for them.
 *
 * 2. The publisher gains its social profiles, email and telephone. Without
 *    sameAs links a knowledge panel has nothing to attach the brand to, and
 *    Google has no way to connect this site to the LinkedIn and Facebook pages
 *    that carry the same name.
 *
 * 3. A BreadcrumbList is added. Rank Math only emits one when its own
 *    breadcrumb function is called in the template, which this theme does not
 *    do — so there was none. Breadcrumbs are what replace the bare URL under a
 *    search result with a readable trail.
 */
function arr_seo_json_ld( $data ) {
	if ( ! is_array( $data ) ) {
		return $data;
	}

	foreach ( $data as $key => $entity ) {
		if ( ! is_array( $entity ) || empty( $entity['@type'] ) ) {
			continue;
		}

		$type = is_array( $entity['@type'] ) ? $entity['@type'] : array( $entity['@type'] );

		/* Posts only. Rank Math types the front page as Article too, and the
		   homepage of a publication is not a news story — promoting it to
		   NewsArticle would claim a publication date and a byline for a page
		   that has neither in any meaningful sense. */
		if ( is_singular( 'post' ) && array_intersect( $type, array( 'BlogPosting', 'Article' ) ) ) {
			$data[ $key ] = arr_seo_article_entity( $entity );
		}

		/* Pages were being described as Articles, with a publication date and a
		   byline — which is also where the "Written by Mukuka" label on the
		   homepage's Twitter card came from. Nobody wrote the Contact page as a
		   piece of journalism. The @id is left alone: other entities in the
		   graph point at it. */
		if ( ! is_singular( 'post' ) && is_singular() && array_intersect( $type, array( 'BlogPosting', 'Article' ) ) ) {
			$entity['@type'] = arr_seo_page_entity_type();
			unset( $entity['datePublished'], $entity['dateModified'], $entity['author'] );
			$data[ $key ]    = $entity;
		}

		if ( in_array( 'Organization', $type, true ) ) {
			$data[ $key ] = arr_seo_organization_entity( $entity );
		}
	}

	$crumbs = arr_seo_breadcrumb_entity();

	if ( $crumbs ) {
		$data['arr-breadcrumbs'] = $crumbs;
	}

	return $data;
}
/* Priority 50 is deliberate. Rank Math builds this array from an empty one:
   its own snippets are added on the same filter at 10, and it stitches the
   entities together at 99. A callback at the default 10 registered by a theme
   can run before the entities it means to edit even exist — which is exactly
   what happened here, and why the article type went on saying BlogPosting while
   the breadcrumbs this same function adds appeared correctly. */
add_filter( 'rank_math/json_ld', 'arr_seo_json_ld', 50 );

/**
 * Make NewsArticle the default article type for posts.
 *
 * The JSON-LD filter above corrects the entity after the fact; this sets it at
 * source, so Rank Math's own Open Graph type mapping and its admin preview
 * agree with what the page actually emits. It only supplies the default, so a
 * type chosen in Rank Math's settings still wins.
 */
function arr_seo_default_article_type( $type, $post_type ) {
	return 'post' === $post_type ? 'NewsArticle' : $type;
}
add_filter( 'rank_math/settings/snippet/article_type', 'arr_seo_default_article_type', 10, 2 );

/**
 * Promote an article entity to NewsArticle and fill in what is missing.
 */
function arr_seo_article_entity( $entity ) {
	$entity['@type'] = 'NewsArticle';

	/* Rank Math fills headline from the SEO title, which carries the site name
	   as a suffix — "Non-Alignment 2.0 - African Renaissance Review". A headline
	   is the thing the newsroom published, not the tab title, and the publisher
	   is already named elsewhere in the same graph. Google also truncates
	   headline at 110 characters, so the brand can crowd out the actual words. */
	$title = get_the_title();

	if ( $title ) {
		$entity['headline'] = wp_strip_all_tags( $title );
	}

	/* The section is how a reader would describe where they found the piece,
	   and how Google groups coverage of the same subject. */
	$categories = get_the_category();

	if ( $categories ) {
		$entity['articleSection'] = $categories[0]->name;
	}

	/* Stated explicitly because the alternative — leaving it out — is read as
	   "we don't know", and a paywall is then one of the possibilities. */
	if ( ! isset( $entity['isAccessibleForFree'] ) ) {
		$entity['isAccessibleForFree'] = true;
	}

	if ( ! isset( $entity['inLanguage'] ) ) {
		$entity['inLanguage'] = get_bloginfo( 'language' );
	}

	return $entity;
}

/**
 * The right schema type for the page being viewed.
 *
 * Most of this theme's page templates are listings — they query posts and print
 * them — and schema.org has a type for exactly that. Distinguishing them from
 * the pages that are simply prose (About, Contact, Editorial Standards) costs
 * one array and tells a crawler which pages are worth returning to for new
 * items and which are static.
 */
function arr_seo_page_entity_type() {
	$listings = array(
		'template-articles.php',
		'template-categories.php',
		'template-ideas.php',
		'template-brief.php',
		'template-five-things.php',
		'template-editors-notes.php',
		'template-caricatures.php',
		'template-authors.php',
	);

	if ( is_front_page() ) {
		return 'CollectionPage';
	}

	$template = get_page_template_slug( get_queried_object_id() );

	if ( 'template-contact.php' === $template ) {
		return 'ContactPage';
	}

	if ( 'template-about.php' === $template ) {
		return 'AboutPage';
	}

	return in_array( $template, $listings, true ) ? 'CollectionPage' : 'WebPage';
}

/**
 * Attach the publication's real-world identifiers to the Organization entity.
 */
function arr_seo_organization_entity( $entity ) {
	/* NewsMediaOrganization is a subtype of Organization, so nothing that
	   references this entity breaks — but it states what ARR is rather than
	   leaving a crawler to infer it from the articles underneath. */
	$entity['@type'] = 'NewsMediaOrganization';

	$profiles = array();

	foreach ( arr_social_platforms() as $platform ) {
		$url = get_theme_mod( $platform['mod'], $platform['default'] );

		if ( $url ) {
			$profiles[] = esc_url_raw( $url );
		}
	}

	if ( $profiles ) {
		$existing = isset( $entity['sameAs'] ) ? (array) $entity['sameAs'] : array();
		$entity['sameAs'] = array_values( array_unique( array_merge( $existing, $profiles ) ) );
	}

	$email = get_theme_mod( 'arr_social_email', ARR_CONTACT_EMAIL );

	if ( $email && empty( $entity['email'] ) ) {
		$entity['email'] = $email;
	}

	if ( ARR_CONTACT_PHONE && empty( $entity['telephone'] ) ) {
		$entity['telephone'] = ARR_CONTACT_PHONE;
	}

	return $entity;
}

/**
 * A breadcrumb trail for the current request, as a BreadcrumbList.
 *
 * Built from the queried object rather than from a menu, because the menu is
 * about navigation and this is about position: a reader arriving cold from a
 * search result needs to know what this page is part of.
 *
 * Returns an empty array on the front page, where a one-item trail says
 * nothing, and on the search and 404 pages, which are not indexed anyway.
 *
 * @return array
 */
function arr_seo_breadcrumb_entity() {
	if ( is_front_page() || is_search() || is_404() ) {
		return array();
	}

	$trail = array(
		array( 'name' => __( 'Home', 'arr-theme' ), 'url' => home_url( '/' ) ),
	);

	if ( is_singular( 'post' ) ) {
		$categories = get_the_category();

		if ( $categories ) {
			/* The primary category's own parent first, so a nested format such
			   as ARR Brief → 5 Things to Know reads in full. */
			$parent = $categories[0]->parent ? get_category( $categories[0]->parent ) : null;

			if ( $parent && ! is_wp_error( $parent ) ) {
				$trail[] = array( 'name' => $parent->name, 'url' => get_category_link( $parent ) );
			}

			$trail[] = array(
				'name' => $categories[0]->name,
				'url'  => get_category_link( $categories[0] ),
			);
		}

		$trail[] = array( 'name' => get_the_title(), 'url' => get_permalink() );
	} elseif ( is_singular() ) {
		foreach ( array_reverse( get_post_ancestors( get_queried_object_id() ) ) as $ancestor ) {
			$trail[] = array( 'name' => get_the_title( $ancestor ), 'url' => get_permalink( $ancestor ) );
		}

		$trail[] = array( 'name' => get_the_title(), 'url' => get_permalink() );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$term = get_queried_object();

		if ( ! $term ) {
			return array();
		}

		foreach ( array_reverse( get_ancestors( $term->term_id, $term->taxonomy ) ) as $ancestor_id ) {
			$ancestor = get_term( $ancestor_id, $term->taxonomy );

			if ( $ancestor && ! is_wp_error( $ancestor ) ) {
				$trail[] = array( 'name' => $ancestor->name, 'url' => get_term_link( $ancestor ) );
			}
		}

		$trail[] = array( 'name' => $term->name, 'url' => get_term_link( $term ) );
	} elseif ( is_author() ) {
		$contributors = arr_page_url_by_template( 'template-authors.php' );

		if ( $contributors ) {
			$trail[] = array( 'name' => __( 'Contributors', 'arr-theme' ), 'url' => $contributors );
		}

		$trail[] = array(
			'name' => get_the_author_meta( 'display_name', get_queried_object_id() ),
			'url'  => get_author_posts_url( get_queried_object_id() ),
		);
	} else {
		return array();
	}

	if ( count( $trail ) < 2 ) {
		return array();
	}

	$items = array();

	foreach ( $trail as $position => $crumb ) {
		if ( is_wp_error( $crumb['url'] ) ) {
			continue;
		}

		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $position + 1,
			'name'     => wp_strip_all_tags( $crumb['name'] ),
			'item'     => esc_url_raw( $crumb['url'] ),
		);
	}

	return array(
		'@type'           => 'BreadcrumbList',
		'@id'             => home_url( '/#breadcrumb' ),
		'itemListElement' => $items,
	);
}

/**
 * Drop the byline and reading time from a page's social card.
 *
 * Rank Math adds "Written by" and "Time to read" labels to the card that Slack
 * and X render. On an article they are worth having. On the homepage they said
 * "Written by Mukuka" and "Time to read: less than a minute" — a byline for a
 * page nobody wrote, and a reading time for a template whose post_content is
 * empty, which is why every one of them came out as under a minute.
 */
function arr_seo_social_card_data( $data ) {
	return is_singular( 'post' ) ? $data : array();
}
add_filter( 'rank_math/opengraph/slack_enhanced_data', 'arr_seo_social_card_data' );

/**
 * Keep the default "Uncategorized" category out of the index.
 *
 * It exists because WordPress requires a fallback category, not because ARR
 * publishes anything under it. Left indexable it becomes a thin archive
 * competing with the real ones, and — worse for a publication — an archive
 * whose name tells a reader the newsroom has not decided what a piece is.
 */
function arr_seo_noindex_default_category( $robots ) {
	if ( ! is_category() ) {
		return $robots;
	}

	$term = get_queried_object();

	if ( ! $term || (int) get_option( 'default_category' ) !== (int) $term->term_id ) {
		return $robots;
	}

	unset( $robots['index'] );
	$robots['noindex'] = 'noindex';

	return $robots;
}
add_filter( 'rank_math/frontend/robots', 'arr_seo_noindex_default_category' );

/**
 * Drop head tags that no current client reads.
 *
 * Really Simple Discovery and the Windows Live Writer manifest are for desktop
 * blogging clients that no longer exist; the shortlink is a second, competing
 * URL for every page. None of them help a reader or a crawler, and each is a
 * line in every response. wp_generator stays removed by the security layer.
 */
function arr_seo_tidy_head() {
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	remove_action( 'template_redirect', 'wp_shortlink_header', 11 );
}
add_action( 'init', 'arr_seo_tidy_head' );

/**
 * Stop MailPoet loading its font catalogue on every page.
 *
 * MailPoet registers three stylesheets that between them import more than a
 * hundred Google font families — the list its form editor offers, not the one
 * font a form actually uses. They load on every page the newsletter form
 * appears on, which here means the footer, and therefore the whole site.
 *
 * That is three blocking requests to a third-party origin before the page can
 * paint, for fonts nothing renders in. Turning them off leaves the form in the
 * theme's own typeface, which is what it should have been using regardless.
 *
 * Done through MailPoet's own filter rather than by dequeuing the handles: the
 * same switch also governs the copy of those links that MailPoet writes
 * directly into form markup, which a dequeue cannot reach. Only the front end
 * is affected — the form editor in wp-admin still offers the full list.
 */
function arr_seo_drop_mailpoet_font_catalogue( $display ) {
	return is_admin() ? $display : false;
}
add_filter( 'mailpoet_display_custom_fonts', 'arr_seo_drop_mailpoet_font_catalogue' );
