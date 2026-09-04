<?php
/**
 * A single caricature.
 *
 * The drawing is the content, so it gets the width and nothing sits on top of
 * it. The commentary follows underneath, where it reads as a note on the
 * cartoon rather than as an article the cartoon illustrates.
 */
get_header();
?>

<?php while ( have_posts() ) : the_post(); ?>
<?php
$artist    = arr_field( 'caricature_artist', '' );
$image_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
?>

<section class="cartoon-single">
  <div class="wrap">
    <div class="cartoon-single-head">
      <span class="eyebrow"><?php esc_html_e( 'Caricature', 'arr-theme' ); ?></span>
      <h1><?php the_title(); ?></h1>
      <span class="cartoon-meta">
        <?php echo esc_html( get_the_date() ); ?><?php if ( $artist ) : ?> &middot; <?php echo esc_html( $artist ); ?><?php endif; ?>
      </span>
    </div>

    <?php if ( $image_url ) : ?>
      <figure class="cartoon-single-media">
        <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( arr_caricature_alt( get_the_ID() ) ); ?>" />
      </figure>
    <?php endif; ?>

    <?php if ( trim( get_the_content() ) ) : ?>
      <div class="cartoon-single-body">
        <?php the_content(); ?>
      </div>
    <?php endif; ?>

    <?php get_template_part( 'parts/share-row', null, array( 'label' => __( 'Share this cartoon', 'arr-theme' ) ) ); ?>

    <?php
    // Other cartoons, so a reader who arrives on one has somewhere to go.
    $others = get_posts( array(
      'post_type'      => ARR_CARICATURE_TYPE,
      'posts_per_page' => 3,
      'post__not_in'   => array( get_the_ID() ),
      'post_status'    => 'publish',
      'no_found_rows'  => true,
    ) );
    ?>
    <?php if ( $others ) : ?>
      <div class="cartoon-more">
        <div class="section-head">
          <h2><?php esc_html_e( 'More caricatures', 'arr-theme' ); ?></h2>
          <?php $archive = arr_page_url_by_template( 'template-caricatures.php' ); ?>
          <?php if ( $archive ) : ?>
            <a class="view-all" href="<?php echo esc_url( $archive ); ?>"><?php esc_html_e( 'See all', 'arr-theme' ); ?> <span aria-hidden="true">&rarr;</span></a>
          <?php endif; ?>
        </div>
        <div class="cartoon-grid">
          <?php foreach ( $others as $other ) : ?>
            <figure class="cartoon-card">
              <a class="cartoon-media" href="<?php echo esc_url( get_permalink( $other ) ); ?>">
                <?php if ( has_post_thumbnail( $other ) ) : ?>
                  <img src="<?php echo esc_url( get_the_post_thumbnail_url( $other, 'large' ) ); ?>"
                       alt="<?php echo esc_attr( arr_caricature_alt( $other->ID ) ); ?>" loading="lazy" />
                <?php endif; ?>
              </a>
              <figcaption class="cartoon-body">
                <h2><a href="<?php echo esc_url( get_permalink( $other ) ); ?>"><?php echo esc_html( get_the_title( $other ) ); ?></a></h2>
                <span class="cartoon-meta"><?php echo esc_html( get_the_date( '', $other ) ); ?></span>
              </figcaption>
            </figure>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php endwhile; ?>

<?php get_footer(); ?>
