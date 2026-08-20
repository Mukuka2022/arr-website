<?php
/**
 * Template Name: Authors Page
 *
 * Select this under Page Attributes → Template when creating your Authors page.
 * Lists every author who has at least one published post.
 */
get_header();

$authors = get_users( array(
	'capability'          => array( 'edit_posts' ),
	'number'              => -1,
	'has_published_posts' => array( 'post' ),
	'orderby'             => 'display_name',
) );
?>

<div class="page-banner">
  <div class="wrap">
    <span class="eyebrow"><?php echo esc_html( arr_field( 'authors_eyebrow', 'Contributors' ) ); ?></span>
    <h1><?php echo esc_html( arr_field( 'authors_title', 'Meet Our Authors' ) ); ?></h1>
    <p><?php echo esc_html( arr_field( 'authors_subtitle', "The economists, policymakers, and scholars whose analysis shapes ARR's seven editorial pillars." ) ); ?></p>
  </div>
</div>

<section style="padding-bottom: 90px;">
  <div class="wrap">
    <div class="team-grid" style="padding-top: 44px;">
      <?php foreach ( $authors as $author ) : ?>
        <a class="team-card" href="<?php echo esc_url( get_author_posts_url( $author->ID ) ); ?>" style="text-decoration:none;color:inherit;">
          <?php echo get_avatar( $author->ID, 300 ); ?>
          <h5><?php echo esc_html( $author->display_name ); ?></h5>
          <p><?php echo esc_html( get_the_author_meta( 'description', $author->ID ) ?: 'Contributor' ); ?></p>
        </a>
      <?php endforeach; ?>
      <?php if ( empty( $authors ) ) : ?>
        <p style="color:var(--muted);font-size:13px;"><?php echo esc_html( arr_field( 'authors_empty_text', "Authors appear here automatically once they've published at least one article." ) ); ?></p>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php get_footer(); ?>
