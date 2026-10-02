<?php
/**
 * Contributor archive — /contributor/<slug>/
 *
 * Without this file, author archives fell through to archive.php, which is
 * written for category archives. That gave every contributor a page headed
 * "CATEGORY", a row of category filter pills that navigated away from them,
 * no portrait, and an article grid that repeated their own name and face
 * under every card on their own page.
 *
 * This is the same furniture as the rest of the site — page-banner,
 * article-grid, article-card — arranged for a person rather than a section.
 */
get_header();

$arr_author = get_queried_object();
$arr_bio    = get_the_author_meta( 'description', $arr_author->ID );
$arr_count  = (int) $GLOBALS['wp_query']->found_posts;
?>

<div class="page-banner">
  <div class="wrap">
    <div class="contributor-head">
      <?php echo get_avatar( $arr_author->ID, 240 ); ?>
      <div class="contributor-head-text">
        <span class="eyebrow"><?php echo esc_html( arr_field( 'contributor_eyebrow', 'Contributor' ) ); ?></span>
        <h1><?php echo esc_html( $arr_author->display_name ); ?></h1>
        <?php if ( $arr_bio ) : ?>
          <p><?php echo esc_html( $arr_bio ); ?></p>
        <?php endif; ?>
        <p class="contributor-count">
          <?php
          printf(
            /* translators: %s: number of articles. */
            esc_html( _n( '%s article', '%s articles', $arr_count, 'arr-theme' ) ),
            esc_html( number_format_i18n( $arr_count ) )
          );
          ?>
        </p>
      </div>
    </div>
  </div>
</div>

<section style="padding-bottom: 90px;">
  <div class="wrap">
    <div class="article-grid" style="padding-top: 44px;">
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
            <?php
            /* The byline is dropped here: on a contributor's own page every
               card would otherwise carry the same name and face. Date and
               reading time are what actually distinguish one card from the
               next. */
            ?>
            <div class="meta">
              <?php echo esc_html( get_the_date() ); ?> &middot; <?php echo esc_html( arr_reading_time() ); ?> min read
            </div>
          </div>
        </article>
      <?php endwhile; else : ?>
        <p style="color:var(--muted);"><?php echo esc_html( arr_field( 'contributor_empty_text', 'No articles from this contributor yet.' ) ); ?></p>
      <?php endif; ?>
    </div>

    <div class="load-more">
      <?php the_posts_pagination( array( 'prev_text' => '&larr; Newer', 'next_text' => 'Older &rarr;' ) ); ?>
    </div>
  </div>
</section>

<?php get_footer(); ?>
