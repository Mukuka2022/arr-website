<?php
/**
 * Template Name: ARR Brief
 *
 * The newsletter and short-form intelligence product: what it is, how to
 * subscribe, the recurring formats, and the most recent editions.
 *
 * Signing up is the point of this page, so the form comes before the archive —
 * a reader who has decided within the first screen should not have to scroll
 * past back issues to act on it.
 */
get_header();

$formats = arr_child_categories( ARR_BRIEF_CATEGORY );
$brief   = get_category_by_slug( ARR_BRIEF_CATEGORY );

$brief_ids = wp_list_pluck( $formats, 'term_id' );
if ( $brief ) {
	$brief_ids[] = $brief->term_id;
}

$editions = $brief_ids ? new WP_Query( array(
	'category__in'        => $brief_ids,
	'posts_per_page'      => 6,
	'ignore_sticky_posts' => true,
	'no_found_rows'       => true,
) ) : null;

$signup_form = arr_field( 'brief_form_shortcode', '[mailpoet_form id="1"]' );
?>

<?php get_template_part( 'parts/analysis-bar', null, array( 'category_ids' => $brief_ids ) ); ?>

<div class="page-banner">
  <div class="wrap">
    <span class="eyebrow"><?php echo esc_html( arr_field( 'brief_eyebrow', 'Short-form intelligence' ) ); ?></span>
    <h1><?php echo esc_html( arr_field( 'brief_title', 'The ARR Brief' ) ); ?></h1>
    <p><?php echo esc_html( arr_field( 'brief_subtitle', "Africa's ideas, developments and strategic questions — every two weeks." ) ); ?></p>
  </div>
</div>

<section class="brief-signup-band">
  <div class="wrap">
    <div class="brief-signup">
      <div class="brief-signup-copy">
        <h2><?php echo esc_html( arr_field( 'brief_signup_heading', 'Get the Brief in your inbox' ) ); ?></h2>
        <p><?php echo esc_html( arr_field( 'brief_signup_text', 'Short, curated and free. No more than twice a month, and nothing you did not ask for.' ) ); ?></p>
      </div>
      <div class="brief-signup-form">
        <?php echo do_shortcode( $signup_form ); ?>
        <p class="brief-fineprint"><?php echo esc_html( arr_field( 'brief_fineprint', 'Unsubscribe in one click. We never share your address.' ) ); ?></p>
      </div>
    </div>
  </div>
</section>

<?php if ( $formats ) : ?>
<section>
  <div class="wrap">
    <div class="section-head">
      <h2><?php echo esc_html( arr_field( 'brief_formats_heading', "What's in it" ) ); ?></h2>
    </div>
    <div class="category-index">
      <?php foreach ( $formats as $format ) : ?>
        <a class="category-tile" href="<?php echo esc_url( get_category_link( $format ) ); ?>">
          <h2><?php echo esc_html( $format->name ); ?></h2>
          <?php if ( $format->description ) : ?>
            <p><?php echo esc_html( $format->description ); ?></p>
          <?php endif; ?>
          <span class="category-count">
            <?php
            echo esc_html( $format->count
              ? sprintf(
                  /* translators: %s: number of editions. */
                  _n( '%s edition', '%s editions', $format->count, 'arr-theme' ),
                  number_format_i18n( $format->count )
                )
              : __( 'Coming soon', 'arr-theme' )
            );
            ?>
          </span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ( $editions && $editions->have_posts() ) : ?>
<section style="padding-top:0;padding-bottom:90px;">
  <div class="wrap">
    <div class="section-head">
      <h2><?php echo esc_html( arr_field( 'brief_latest_heading', 'Recent editions' ) ); ?></h2>
    </div>
    <div class="related-grid">
      <?php while ( $editions->have_posts() ) : $editions->the_post(); ?>
        <?php $thumb = get_the_post_thumbnail_url( get_the_ID(), 'arr-card' ); ?>
        <a class="related-card" href="<?php the_permalink(); ?>">
          <?php if ( $thumb ) : ?>
            <div class="related-media"><img src="<?php echo esc_url( $thumb ); ?>" alt="" loading="lazy" /></div>
          <?php endif; ?>
          <div class="related-body">
            <?php $cats = get_the_category(); if ( $cats ) : ?>
              <span class="cat"><?php echo esc_html( $cats[0]->name ); ?></span>
            <?php endif; ?>
            <h3><?php the_title(); ?></h3>
            <span class="meta"><?php echo esc_html( get_the_date() ); ?></span>
          </div>
        </a>
      <?php endwhile; wp_reset_postdata(); ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php get_footer(); ?>
