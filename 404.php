<?php get_header(); ?>

<div class="page-banner">
  <div class="wrap">
    <span class="eyebrow"><?php echo esc_html( get_theme_mod( 'arr_404_eyebrow', '404' ) ); ?></span>
    <h1><?php echo esc_html( get_theme_mod( 'arr_404_title', 'Page not found' ) ); ?></h1>
  </div>
</div>

<section>
  <div class="wrap notfound">
    <p><?php echo esc_html( get_theme_mod( 'arr_404_text', 'The page you were looking for has moved or no longer exists.' ) ); ?></p>
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary"><?php echo esc_html( get_theme_mod( 'arr_404_button_text', 'Back to Home' ) ); ?></a>
  </div>
</section>

<?php get_footer(); ?>
