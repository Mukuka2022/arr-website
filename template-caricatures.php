<?php
/**
 * Template Name: Caricatures
 *
 * Select this under Page Attributes → Template when creating your Caricatures
 * page, and give it the slug "caricatures" — the homepage section links here
 * by that slug.
 *
 * Lists every published cartoon, newest first. Nothing to maintain: adding a
 * caricature puts it here and on the homepage automatically.
 */
get_header();

$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;

$cartoons = new WP_Query( array(
	'post_type'      => ARR_CARICATURE_TYPE,
	'posts_per_page' => 12,
	'paged'          => $paged,
	'post_status'    => 'publish',
) );
?>

<div class="page-banner">
  <div class="wrap">
    <span class="eyebrow"><?php echo esc_html( arr_field( 'caricatures_eyebrow', 'Drawn' ) ); ?></span>
    <h1><?php echo esc_html( arr_field( 'caricatures_title', 'Caricatures' ) ); ?></h1>
    <p><?php echo esc_html( arr_field( 'caricatures_subtitle', 'Editorial cartoons from the African Renaissance Review — the week in a single drawing.' ) ); ?></p>
  </div>
</div>

<section style="padding-bottom: 90px;">
  <div class="wrap">
    <?php if ( $cartoons->have_posts() ) : ?>
      <div class="cartoon-grid">
        <?php while ( $cartoons->have_posts() ) : $cartoons->the_post(); ?>
          <?php $artist = arr_field( 'caricature_artist', '', get_the_ID() ); ?>
          <figure class="cartoon-card">
            <a class="cartoon-media" href="<?php the_permalink(); ?>">
              <?php if ( has_post_thumbnail() ) : ?>
                <img src="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'large' ) ); ?>"
                     alt="<?php echo esc_attr( arr_caricature_alt( get_the_ID() ) ); ?>" loading="lazy" />
              <?php endif; ?>
            </a>
            <figcaption class="cartoon-body">
              <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
              <span class="cartoon-meta">
                <?php echo esc_html( get_the_date() ); ?><?php if ( $artist ) : ?> &middot; <?php echo esc_html( $artist ); ?><?php endif; ?>
              </span>
            </figcaption>
          </figure>
        <?php endwhile; ?>
      </div>

      <div class="load-more">
        <?php
        echo paginate_links( array(
          'total'     => $cartoons->max_num_pages,
          'current'   => $paged,
          'prev_text' => '← Newer',
          'next_text' => 'Older →',
        ) );
        ?>
      </div>
    <?php else : ?>
      <p style="color:var(--muted);"><?php echo esc_html( arr_field( 'caricatures_empty_text', 'No caricatures published yet. Add one under Caricatures → Add New and it will appear here and on the homepage.' ) ); ?></p>
    <?php endif; ?>
    <?php wp_reset_postdata(); ?>
  </div>
</section>

<?php get_footer(); ?>
