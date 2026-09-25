<?php
/**
 * "5 Things to Know" — the five-question framework.
 *
 * Each edition takes one subject and answers the same five questions, in the
 * same order. That fixedness is the whole point of the format: a reader who
 * has read one knows exactly where to look in the next.
 *
 * So the five headings and their questions live here in code rather than being
 * retyped per edition. An editor fills in five answers; the scaffolding around
 * them cannot drift, go out of order, or lose a step.
 *
 * The category already exists as one of the ARR Brief formats.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/** Slug of the category that marks a post as an edition. */
const ARR_FIVE_CATEGORY = '5-things-to-know';

/**
 * The five questions, in order.
 *
 * @return array[] list of [ number, title, question, field ]
 */
function arr_five_things_framework() {
	return array(
		array(
			'number'   => '01',
			'title'    => __( 'The Essential Fact', 'arr-theme' ),
			'question' => __( 'What is happening?', 'arr-theme' ),
			'field'    => 'five_1_answer',
		),
		array(
			'number'   => '02',
			'title'    => __( 'The Context', 'arr-theme' ),
			'question' => __( 'How did we get here?', 'arr-theme' ),
			'field'    => 'five_2_answer',
		),
		array(
			'number'   => '03',
			'title'    => __( 'Why It Matters', 'arr-theme' ),
			'question' => __( 'Why should Africa care?', 'arr-theme' ),
			'field'    => 'five_3_answer',
		),
		array(
			'number'   => '04',
			'title'    => __( 'The Bigger Picture', 'arr-theme' ),
			'question' => __( 'What does it mean for Africa and the world?', 'arr-theme' ),
			'field'    => 'five_4_answer',
		),
		array(
			'number'   => '05',
			'title'    => __( 'What to Watch', 'arr-theme' ),
			'question' => __( 'What happens next?', 'arr-theme' ),
			'field'    => 'five_5_answer',
		),
	);
}

/**
 * Whether a post is an edition of the format.
 */
function arr_is_five_things( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$term    = get_category_by_slug( ARR_FIVE_CATEGORY );

	return $term && $post_id && has_category( $term->term_id, $post_id );
}

/**
 * The five answers for one edition, in framework order.
 *
 * An edition with no answers filled in returns an empty array, so the template
 * falls back to the ordinary article layout rather than printing five empty
 * cards under five headings.
 *
 * @return array[] list of [ number, title, question, answer ]
 */
function arr_five_things_points( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$points  = array();

	foreach ( arr_five_things_framework() as $step ) {
		$answer = arr_field( $step['field'], '', $post_id );
		if ( ! $answer ) {
			continue;
		}
		$step['answer'] = $answer;
		$points[]       = $step;
	}

	return $points;
}

/**
 * The most recent published editions.
 *
 * @return WP_Post[]
 */
function arr_five_things_editions( $count = 6 ) {
	$term = get_category_by_slug( ARR_FIVE_CATEGORY );

	if ( ! $term ) {
		return array();
	}

	return get_posts( array(
		'cat'            => $term->term_id,
		'posts_per_page' => $count,
		'post_status'    => 'publish',
		'no_found_rows'  => true,
	) );
}
