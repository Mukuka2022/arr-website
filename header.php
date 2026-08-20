<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
  <div class="wrap">
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="brand">
      <?php
      // the_custom_logo() emits its own anchor, which would nest inside .brand.
      if ( has_custom_logo() ) {
        echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'full', false, array( 'alt' => get_bloginfo( 'name' ) ) );
      } else {
        ?>
        <img src="<?php echo esc_url( get_template_directory_uri() . '/images/logo.png' ); ?>" alt="<?php bloginfo( 'name' ); ?>" />
        <?php
      }
      ?>
      <span class="brand-text">
        <span class="arr">ARR</span>
        <span class="tag"><?php echo esc_html( get_theme_mod( 'arr_brand_tagline' ) ?: get_bloginfo( 'name' ) ); ?></span>
      </span>
    </a>

    <nav class="primary-nav">
      <?php
      if ( has_nav_menu( 'primary' ) ) {
        wp_nav_menu( array(
          'theme_location' => 'primary',
          'container'      => false,
          'items_wrap'     => '%3$s',
        ) );
      } else {
        arr_fallback_menu();
      }
      ?>
    </nav>

    <div class="header-actions">
      <?php if ( get_theme_mod( 'arr_header_cta_show', true ) ) : ?>
        <a href="<?php echo esc_url( get_theme_mod( 'arr_header_cta_link' ) ?: home_url( '/subscribe/' ) ); ?>" class="btn btn-primary"><?php echo esc_html( get_theme_mod( 'arr_header_cta_text', 'Contribute' ) ); ?></a>
      <?php endif; ?>
      <button class="icon-btn search-toggle" type="button" aria-label="Search" aria-expanded="false" aria-controls="header-search">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      </button>
      <button class="icon-btn menu-toggle" type="button" aria-label="Open menu" aria-expanded="false">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
      </button>
    </div>
  </div>
  <div class="header-search" id="header-search">
    <div class="wrap">
      <?php get_search_form(); ?>
    </div>
  </div>
</header>
