<?php
/**
 * Template Name: Legal Page
 *
 * Generic long-form template for Privacy Policy, Terms & Conditions, and
 * similar pages. Content is edited directly in the block editor rather
 * than through ACF fields — legal text needs full paragraph/heading/list
 * flexibility, not a handful of fixed fields.
 */
get_header();
?>

<div class="page-banner">
  <div class="wrap">
    <span class="eyebrow"><?php esc_html_e( 'Legal', 'arr-theme' ); ?></span>
    <h1><?php the_title(); ?></h1>
  </div>
</div>

<section>
  <div class="wrap prose">
    <?php while ( have_posts() ) : the_post(); the_content(); endwhile; ?>
  </div>
</section>

<?php get_footer(); ?>
