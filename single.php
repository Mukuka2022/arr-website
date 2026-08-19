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

    <div style="margin-top:50px;padding-top:26px;border-top:1px solid var(--hairline);display:flex;align-items:center;gap:16px;">
      <?php echo get_avatar( get_the_author_meta( 'ID' ), 60 ); ?>
      <div>
        <strong style="font-family:var(--serif);font-size:16px;color:var(--midnight);"><?php the_author(); ?></strong>
        <p style="font-size:13.5px;color:var(--muted);margin-top:4px;"><?php echo esc_html( get_the_author_meta( 'description' ) ?: get_theme_mod( 'arr_single_author_bio', 'Contributor, African Renaissance Review' ) ); ?></p>
      </div>
    </div>

    <?php if ( comments_open() || get_comments_number() ) : comments_template(); endif; ?>
  </div>
</section>

<?php endwhile; ?>

<?php get_footer(); ?>
