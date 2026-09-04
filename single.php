<?php get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

<div class="page-banner">
  <div class="wrap">
    <?php $cats = get_the_category(); if ( $cats ) : ?>
      <span class="eyebrow"><?php echo esc_html( $cats[0]->name ); ?></span>
    <?php endif; ?>
    <h1><?php the_title(); ?></h1>
    <p>By <?php the_author(); ?> · <?php echo get_the_date(); ?> · <?php echo esc_html( arr_reading_time() ); ?> min read</p>
  </div>
</div>

<section>
  <div class="wrap" style="max-width:760px;">
    <?php if ( has_post_thumbnail() ) : ?>
      <div style="margin-bottom:36px;"><?php the_post_thumbnail( 'large' ); ?></div>
    <?php endif; ?>
    <div class="single-article-body" style="font-size:17px;line-height:1.75;color:var(--charcoal);">
      <?php the_content(); ?>
    </div>

    <?php get_template_part( 'parts/share-row', null, array( 'label' => __( 'Share this article', 'arr-theme' ) ) ); ?>

    <div style="margin-top:50px;padding-top:26px;border-top:1px solid var(--hairline);display:flex;align-items:center;gap:16px;">
      <?php echo get_avatar( get_the_author_meta( 'ID' ), 60 ); ?>
      <div>
        <strong style="font-family:var(--serif);font-size:16px;color:var(--midnight);"><?php the_author(); ?></strong>
        <p style="font-size:13.5px;color:var(--muted);margin-top:4px;"><?php echo esc_html( get_the_author_meta( 'description' ) ?: get_theme_mod( 'arr_single_author_bio', 'Contributor, African Renaissance Review' ) ); ?></p>
      </div>
    </div>

  </div>
</section>

<?php
/* Related articles sit between the piece and the comments: the reader has
   finished reading and is deciding what to do next, and "read another" is the
   thing we want to make easy. Full width rather than inside the 760px reading
   column, so three cards get room to breathe. */
$related = arr_related_posts( get_the_ID(), 3 );
?>
<?php if ( $related ) : ?>
<section class="related-band">
  <div class="wrap">
    <div class="section-head">
      <h2><?php echo esc_html( get_theme_mod( 'arr_related_heading', __( 'Related Articles', 'arr-theme' ) ) ); ?></h2>
      <a class="view-all" href="<?php echo esc_url( home_url( '/articles/' ) ); ?>"><?php esc_html_e( 'All articles', 'arr-theme' ); ?> <span aria-hidden="true">&rarr;</span></a>
    </div>
    <div class="related-grid">
      <?php foreach ( $related as $related_post ) : ?>
        <?php
        $related_cats  = get_the_category( $related_post->ID );
        $related_thumb = get_the_post_thumbnail_url( $related_post->ID, 'arr-card' );
        ?>
        <a class="related-card" href="<?php echo esc_url( get_permalink( $related_post ) ); ?>">
          <?php if ( $related_thumb ) : ?>
            <div class="related-media"><img src="<?php echo esc_url( $related_thumb ); ?>" alt="" loading="lazy" /></div>
          <?php endif; ?>
          <div class="related-body">
            <?php if ( $related_cats ) : ?>
              <span class="cat"><?php echo esc_html( $related_cats[0]->name ); ?></span>
            <?php endif; ?>
            <h3><?php echo esc_html( get_the_title( $related_post ) ); ?></h3>
            <span class="meta"><?php echo esc_html( get_the_date( '', $related_post ) ); ?></span>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ( comments_open() || get_comments_number() ) : ?>
<section class="comments-section">
  <div class="wrap" style="max-width:760px;">
    <?php comments_template(); ?>
  </div>
</section>
<?php endif; ?>

<?php endwhile; ?>

<?php get_footer(); ?>
