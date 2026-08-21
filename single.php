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

    <?php $arr_share = arr_share_links(); ?>
    <?php if ( $arr_share ) : ?>
      <div class="share-row">
        <span class="share-label"><?php esc_html_e( 'Share this article', 'arr-theme' ); ?></span>
        <div class="share-links">
          <?php foreach ( $arr_share as $arr_target ) : ?>
            <a class="share-btn" href="<?php echo esc_url( $arr_target['url'] ); ?>"
               target="_blank" rel="noopener noreferrer"
               aria-label="<?php echo esc_attr( $arr_target['label'] ); ?>"
               title="<?php echo esc_attr( $arr_target['label'] ); ?>">
              <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="<?php echo esc_attr( $arr_target['icon'] ); ?>" /></svg>
            </a>
          <?php endforeach; ?>
          <button type="button" class="share-btn share-copy"
                  data-share-url="<?php echo esc_url( get_permalink() ); ?>"
                  aria-label="<?php esc_attr_e( 'Copy link', 'arr-theme' ); ?>"
                  title="<?php esc_attr_e( 'Copy link', 'arr-theme' ); ?>">
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M10.6 13.4a1 1 0 0 1 0-1.41l3.54-3.54a3 3 0 1 1 4.24 4.24l-1.77 1.77a1 1 0 0 1-1.41-1.41l1.77-1.77a1 1 0 0 0-1.42-1.42l-3.53 3.54a1 1 0 0 1-1.42 0zm2.8-2.8a1 1 0 0 1 1.42 1.41l-3.54 3.54a1 1 0 0 0 1.42 1.42l1.76-1.77a1 1 0 0 1 1.42 1.41l-1.77 1.77a3 3 0 0 1-4.24-4.24z" /></svg>
            <span class="share-copied" aria-hidden="true"><?php esc_html_e( 'Copied', 'arr-theme' ); ?></span>
          </button>
        </div>
      </div>
    <?php endif; ?>

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
