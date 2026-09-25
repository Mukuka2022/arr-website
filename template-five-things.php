<?php
/**
 * Template Name: 5 Things to Know
 *
 * Select this under Page Attributes → Template. The homepage block links here
 * once the page exists.
 *
 * Opens with the five questions every edition answers, then lists the
 * editions. Editions are ordinary posts filed under the "5 Things to Know"
 * category — one of the ARR Brief formats — so nothing new has to be learned
 * to publish one.
 */
get_header();

$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
$term  = get_category_by_slug( ARR_FIVE_CATEGORY );

$editions = new WP_Query( array(
	'cat'            => $term ? $term->term_id : 0,
	// Without a category the query would fall through to every post on the
	// site, listing unrelated articles as though they were editions.
	'post__in'       => $term ? array() : array( 0 ),
	'posts_per_page' => 10,
	'paged'          => $paged,
	'post_status'    => 'publish',
) );
?>

<div class="page-banner">
  <div class="wrap">
    <span class="eyebrow"><?php echo esc_html( arr_field( 'five_page_eyebrow', 'A more informed Africa' ) ); ?></span>
    <h1><?php echo esc_html( arr_field( 'five_page_title', '5 Things to Know' ) ); ?></h1>
    <p><?php echo esc_html( arr_field( 'five_page_subtitle', 'Five facts. One subject. A bigger picture. Africa in focus, in the time it takes to drink a coffee.' ) ); ?></p>
  </div>
</div>

<section class="five-band five-band-page">
  <div class="wrap">
    <div class="section-head">
      <h2><?php echo esc_html( arr_field( 'five_page_framework_heading', 'Every edition answers the same five questions' ) ); ?></h2>
    </div>
    <ol class="five-grid">
      <?php foreach ( arr_five_things_framework() as $step ) : ?>
        <li class="five-card">
          <span class="five-number"><?php echo esc_html( $step['number'] ); ?></span>
          <h3><?php echo esc_html( $step['title'] ); ?></h3>
          <p><?php echo esc_html( $step['question'] ); ?></p>
        </li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>

<section style="padding-bottom: 90px;">
  <div class="wrap">
    <div class="section-head">
      <h2><?php echo esc_html( arr_field( 'five_page_editions_heading', 'Recent editions' ) ); ?></h2>
    </div>

    <?php if ( $editions->have_posts() ) : ?>
      <div class="related-grid">
        <?php while ( $editions->have_posts() ) : $editions->the_post(); ?>
          <?php $thumb = get_the_post_thumbnail_url( get_the_ID(), 'arr-card' ); ?>
          <a class="related-card" href="<?php the_permalink(); ?>">
            <?php if ( $thumb ) : ?>
              <div class="related-media"><img src="<?php echo esc_url( $thumb ); ?>" alt="" loading="lazy" /></div>
            <?php endif; ?>
            <div class="related-body">
              <span class="cat"><?php esc_html_e( '5 Things to Know', 'arr-theme' ); ?></span>
              <h3><?php the_title(); ?></h3>
              <span class="meta"><?php echo esc_html( get_the_date() ); ?></span>
            </div>
          </a>
        <?php endwhile; ?>
      </div>

      <div class="load-more">
        <?php
        echo paginate_links( array(
          'total'     => $editions->max_num_pages,
          'current'   => $paged,
          'prev_text' => '← Newer',
          'next_text' => 'Older →',
        ) );
        ?>
      </div>
    <?php else : ?>
      <p style="color:var(--muted);"><?php echo esc_html( arr_field( 'five_page_empty_text', 'No editions published yet. Write a post, tick the “5 Things to Know” category, and fill in the five answers — it will appear here.' ) ); ?></p>
    <?php endif; ?>
    <?php wp_reset_postdata(); ?>
  </div>
</section>

<?php get_footer(); ?>
