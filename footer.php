<footer class="site-footer">
  <div class="wrap footer-top">
    <div class="footer-brand">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="brand">
        <?php
        if ( has_custom_logo() ) {
          echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'full', false, array( 'alt' => '' ) );
        } else {
          ?>
          <img src="<?php echo esc_url( get_template_directory_uri() . '/images/logo.png' ); ?>" alt="" />
          <?php
        }
        ?>
        <span class="brand-text"><span class="arr"><?php echo esc_html( get_theme_mod( 'arr_brand_name', 'ARR' ) ); ?></span></span>
      </a>
      <p><?php echo wp_kses_post( get_theme_mod( 'arr_footer_tagline', "Shaping Africa's Intellectual Renaissance. Independent. Research-driven. Unapologetically African." ) ); ?></p>
    </div>
    <?php foreach ( array( 1 => 'Company', 2 => 'Resources', 3 => 'Support' ) as $col => $default_heading ) : ?>
      <div class="footer-col">
        <h4><?php echo esc_html( get_theme_mod( 'arr_footer_col' . $col . '_heading', $default_heading ) ); ?></h4>
        <?php arr_footer_menu_column( $col ); ?>
      </div>
    <?php endforeach; ?>
  </div>
  <div class="wrap footer-bottom">
    <div class="footer-legal">
      <span><?php
        echo wp_kses_post( str_replace(
          array( '{year}', '{site}' ),
          array( date_i18n( 'Y' ), get_bloginfo( 'name' ) ),
          get_theme_mod( 'arr_footer_copyright', '&copy; {year} {site}. All rights reserved.' )
        ) );
      ?></span>

      <?php
      // wp_login_url()/wp_logout_url() rather than a hardcoded /wp-login.php:
      // if the login page is ever renamed for security, these follow it.
      if ( is_user_logged_in() ) :
        ?>
        <a class="staff-link" href="<?php echo esc_url( admin_url() ); ?>"><?php esc_html_e( 'Dashboard', 'arr-theme' ); ?></a>
        <a class="staff-link" href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>"><?php esc_html_e( 'Log out', 'arr-theme' ); ?></a>
      <?php else : ?>
        <a class="staff-link" href="<?php echo esc_url( wp_login_url( home_url( '/' ) ) ); ?>"><?php esc_html_e( 'Contributor Login', 'arr-theme' ); ?></a>
      <?php endif; ?>
    </div>
    <?php $arr_social = arr_social_links(); ?>
    <?php if ( $arr_social ) : ?>
      <div class="social-row">
        <?php foreach ( $arr_social as $link ) : ?>
          <a href="<?php echo esc_url( $link['url'] ); ?>" aria-label="<?php echo esc_attr( $link['label'] ); ?>"<?php echo $link['external'] ? ' target="_blank" rel="noopener"' : ''; ?>><?php echo esc_html( $link['glyph'] ); ?></a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
