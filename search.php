<?php get_header(); ?>

<div class="page-banner">
  <div class="wrap">
    <span class="eyebrow"><?php echo esc_html( get_theme_mod( 'arr_search_eyebrow', 'Search' ) ); ?></span>
    <h1><?php printf( esc_html__( 'Results for "%s"', 'arr-theme' ), esc_html( get_search_query() ) ); ?></h1>
    <?php get_search_form(); ?>
  </div>
</div>

<section style="padding-bottom: 90px;">
  <div class="wrap">
    <div class="article-grid">
      <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <article class="article-card">
          <a href="<?php the_permalink(); ?>">
            <?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'arr-card' ); else : ?>
              <img src="https://picsum.photos/seed/<?php echo esc_attr( get_the_ID() ); ?>/500/320" alt="" />
            <?php endif; ?>
          </a>
          <div class="article-body">
            <?php $cats = get_the_category(); if ( $cats ) : ?>
              <span class="cat" style="color:var(--emerald);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;"><?php echo esc_html( $cats[0]->name ); ?></span>
            <?php endif; ?>
            <h3><a href="<?php the_permalink(); ?>" style="color:inherit;"><?php the_title(); ?></a></h3>
            <p class="dek"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
            <div class="meta">
              <?php echo get_avatar( get_the_author_meta( 'ID' ), 48 ); ?>
              <?php the_author(); ?> · <?php echo esc_html( arr_reading_time() ); ?> min read
            </div>
          </div>
        </article>
      <?php endwhile; else : ?>
        <p style="color:var(--muted);"><?php echo esc_html( get_theme_mod( 'arr_search_empty_text', 'No articles matched your search. Try a different term.' ) ); ?></p>
      <?php endif; ?>
    </div>

    <div class="load-more">
      <?php the_posts_pagination( array( 'prev_text' => '← Newer', 'next_text' => 'Older →' ) ); ?>
    </div>
  </div>
</section>

<?php get_footer(); ?>
