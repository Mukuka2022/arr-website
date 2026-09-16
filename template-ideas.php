<?php
/**
 * Template Name: Ideas
 *
 * Landing page for the Ideas section. Lists the strands of thought ARR covers
 * — the children of the "ideas" category — and the most recent writing from
 * across all of them.
 *
 * The strands come from the category tree rather than a hand-made list, so the
 * page, the ANALYSIS-style dropdown and the archives can never disagree about
 * what the section contains.
 */
get_header();

$strands = arr_child_categories( ARR_IDEAS_CATEGORY );
$ideas   = get_category_by_slug( ARR_IDEAS_CATEGORY );

// Posts filed under any strand, plus any filed on the parent itself.
$strand_ids = wp_list_pluck( $strands, 'term_id' );
if ( $ideas ) {
	$strand_ids[] = $ideas->term_id;
}

$latest = $strand_ids ? new WP_Query( array(
	'category__in'        => $strand_ids,
	'posts_per_page'      => 6,
	'ignore_sticky_posts' => true,
	'no_found_rows'       => true,
) ) : null;
?>

<?php get_template_part( 'parts/analysis-bar', null, array( 'category_ids' => $strand_ids ) ); ?>

<div class="page-banner">
  <div class="wrap">
    <span class="eyebrow"><?php echo esc_html( arr_field( 'ideas_eyebrow', 'The Life of the Mind' ) ); ?></span>
    <h1><?php echo esc_html( arr_field( 'ideas_title', 'Ideas' ) ); ?></h1>
    <p><?php echo esc_html( arr_field( 'ideas_subtitle', "African thought, philosophy and intellectual history — the ideas that shaped the continent, and the ones that will shape what comes next." ) ); ?></p>
  </div>
</div>

<section>
  <div class="wrap">
    <?php if ( $strands ) : ?>
      <div class="section-head">
        <h2><?php echo esc_html( arr_field( 'ideas_strands_heading', 'Strands of thought' ) ); ?></h2>
      </div>
      <div class="category-index">
        <?php foreach ( $strands as $strand ) : ?>
          <a class="category-tile" href="<?php echo esc_url( get_category_link( $strand ) ); ?>">
            <h2><?php echo esc_html( $strand->name ); ?></h2>
            <?php if ( $strand->description ) : ?>
              <p><?php echo esc_html( $strand->description ); ?></p>
            <?php endif; ?>
            <span class="category-count">
              <?php
              echo esc_html( $strand->count
                ? sprintf(
                    /* translators: %s: number of articles. */
                    _n( '%s article', '%s articles', $strand->count, 'arr-theme' ),
                    number_format_i18n( $strand->count )
                  )
                : __( 'No articles yet', 'arr-theme' )
              );
              ?>
            </span>
          </a>
        <?php endforeach; ?>
      </div>
    <?php else : ?>
      <p style="color:var(--muted);"><?php echo esc_html( arr_field( 'ideas_empty_text', 'The strands of this section will appear here once they are created under Posts → Categories.' ) ); ?></p>
    <?php endif; ?>
  </div>
</section>

<?php if ( $latest && $latest->have_posts() ) : ?>
<section style="padding-top:0;padding-bottom:90px;">
  <div class="wrap">
    <div class="section-head">
      <h2><?php echo esc_html( arr_field( 'ideas_latest_heading', 'Latest in Ideas' ) ); ?></h2>
    </div>
    <div class="related-grid">
      <?php while ( $latest->have_posts() ) : $latest->the_post(); ?>
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
