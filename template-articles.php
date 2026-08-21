<?php
/**
 * Template Name: Articles Page
 *
 * Select this under Page Attributes → Template when creating your Articles page.
 * Lists every published post, newest first, with category filter pills.
 */
get_header();

$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
$articles = new WP_Query( array(
	'posts_per_page' => 9,
	'paged'          => $paged,
) );

// A true_false field must be read directly — arr_field() treats an
// explicit "off" (false) the same as "not set" and would fall back to on.
$breaking_show = function_exists( 'get_field' ) ? get_field( 'articles_breaking_show' ) : null;
if ( null === $breaking_show ) $breaking_show = true;

// Four posts: the newest becomes the spotlight card on the right of the bar,
// the next three rotate through the ticker on the left.
$breaking = $breaking_show ? get_posts( array( 'numberposts' => 4, 'ignore_sticky_posts' => true ) ) : array();
$breaking_lead = $breaking ? array_shift( $breaking ) : null;
?>

<?php if ( $breaking_lead ) : ?>
<div class="breaking-bar">
  <div class="breaking-bar-inner">
    <div class="breaking-live">
      <span class="breaking-badge"><?php echo esc_html( arr_field( 'articles_breaking_label', 'Breaking' ) ); ?></span>
      <?php if ( $breaking ) : ?>
        <div class="breaking-ticker" data-slider>
          <div class="breaking-ticker-window">
            <div class="slider-track">
              <?php foreach ( $breaking as $arr_item ) : ?>
                <div class="slider-slide">
                  <a href="<?php echo esc_url( get_permalink( $arr_item ) ); ?>" class="breaking-item"><?php echo esc_html( get_the_title( $arr_item ) ); ?></a>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="slider-dots"></div>
        </div>
      <?php endif; ?>
    </div>

    <a class="breaking-lead" href="<?php echo esc_url( get_permalink( $breaking_lead ) ); ?>">
      <?php if ( has_post_thumbnail( $breaking_lead ) ) : ?>
        <?php echo get_the_post_thumbnail( $breaking_lead, 'arr-card' ); ?>
      <?php else : ?>
        <img src="https://picsum.photos/seed/<?php echo esc_attr( $breaking_lead->ID ); ?>/160/160" alt="" />
      <?php endif; ?>
      <span class="breaking-lead-copy">
        <?php $arr_lead_cats = get_the_category( $breaking_lead->ID ); ?>
        <span class="breaking-lead-cat">
          <?php echo esc_html( $arr_lead_cats ? $arr_lead_cats[0]->name : arr_field( 'articles_breaking_label', 'Breaking' ) ); ?>
        </span>
        <span class="breaking-lead-title"><?php echo esc_html( get_the_title( $breaking_lead ) ); ?></span>
        <span class="breaking-lead-time">
          <?php echo esc_html( sprintf(
            /* translators: %s: human-readable time difference, e.g. "2 hours". */
            __( '%s ago', 'arr' ),
            human_time_diff( get_post_time( 'U', true, $breaking_lead ), time() )
          ) ); ?>
        </span>
      </span>
    </a>
  </div>
</div>
<?php endif; ?>

<div class="page-banner">
  <div class="wrap">
    <span class="eyebrow"><?php echo esc_html( arr_field( 'articles_eyebrow', 'Archive' ) ); ?></span>
    <h1><?php echo esc_html( arr_field( 'articles_title', 'Articles & Analysis' ) ); ?></h1>
    <p><?php echo esc_html( arr_field( 'articles_subtitle', "Long-form essays and evidence-based commentary across ARR's seven editorial pillars." ) ); ?></p>
  </div>
</div>

<section style="padding-bottom: 90px;">
  <div class="wrap">
    <div class="filter-row" id="filters">
      <a href="<?php echo esc_url( home_url( '/articles/' ) ); ?>" class="filter-pill active"><?php echo esc_html( arr_field( 'articles_all_pill_label', 'All' ) ); ?></a>
      <?php foreach ( get_categories( array( 'number' => 8 ) ) as $cat ) : ?>
        <a href="<?php echo esc_url( get_category_link( $cat ) ); ?>" class="filter-pill"><?php echo esc_html( $cat->name ); ?></a>
      <?php endforeach; ?>
    </div>

    <div class="article-grid">
      <?php if ( $articles->have_posts() ) : while ( $articles->have_posts() ) : $articles->the_post(); ?>
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
        <p style="color:var(--muted);"><?php echo esc_html( arr_field( 'articles_empty_text', 'No articles published yet — your first post will appear here automatically.' ) ); ?></p>
      <?php endif; ?>
    </div>

    <div class="load-more">
      <?php
      echo paginate_links( array(
        'total'   => $articles->max_num_pages,
        'current' => $paged,
        'prev_text' => '← Newer',
        'next_text' => 'Older →',
      ) );
      wp_reset_postdata();
      ?>
    </div>
  </div>
</section>

<?php get_footer(); ?>
