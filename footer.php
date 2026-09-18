<footer class="site-footer">

  <?php
  /* Newsletter sign-up across the top of the footer. The newsletter left the
     main menu for the footer, and a working form here does more than a link
     to it would: this band is on every page, and a reader who has reached the
     bottom of an article is the reader most likely to want the next one.
     Falls back to a button when MailPoet is not active, rather than printing
     the raw shortcode as text. */
  if ( get_theme_mod( 'arr_footer_signup_show', true ) ) :
    $arr_signup_form = '[mailpoet_form id="1"]';
    $arr_newsletter  = arr_page_url_by_template( 'template-subscribe.php' );
    ?>
    <div class="footer-signup">
      <div class="wrap footer-signup-inner">
        <div class="footer-signup-copy">
          <span class="eyebrow"><?php echo esc_html( get_theme_mod( 'arr_footer_signup_eyebrow', __( 'The ARR Brief', 'arr-theme' ) ) ); ?></span>
          <h3><?php echo esc_html( get_theme_mod( 'arr_footer_signup_heading', __( 'Ideas worth your inbox', 'arr-theme' ) ) ); ?></h3>
          <p><?php echo esc_html( get_theme_mod( 'arr_footer_signup_text', __( "Africa's ideas, developments and strategic questions — every two weeks. Free, and nothing you didn't ask for.", 'arr-theme' ) ) ); ?></p>
        </div>
        <div class="footer-signup-form">
          <?php if ( shortcode_exists( 'mailpoet_form' ) ) : ?>
            <?php echo do_shortcode( $arr_signup_form ); ?>
          <?php elseif ( $arr_newsletter ) : ?>
            <a class="btn btn-primary" href="<?php echo esc_url( $arr_newsletter ); ?>"><?php esc_html_e( 'Subscribe to the Brief', 'arr-theme' ); ?></a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php endif; ?>

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

      <?php
      /* Social links live with the brand rather than down in the bottom bar,
         where they sat beside the copyright line and read as fine print. */
      $arr_social = arr_social_links();
      ?>
      <?php if ( $arr_social ) : ?>
        <div class="social-row">
          <?php foreach ( $arr_social as $link ) : ?>
            <a href="<?php echo esc_url( $link['url'] ); ?>" aria-label="<?php echo esc_attr( $link['label'] ); ?>" title="<?php echo esc_attr( $link['label'] ); ?>"<?php echo $link['external'] ? ' target="_blank" rel="noopener"' : ''; ?>><?php echo esc_html( $link['glyph'] ); ?></a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <?php
    // New setting names rather than the old col1/col2/col3 ones: the columns
    // changed meaning (Company → Read, and so on), and a heading saved under
    // an old name would otherwise keep showing its old word over new links.
    $arr_footer_headings = array(
      1 => get_theme_mod( 'arr_footer_read_heading', __( 'Read', 'arr-theme' ) ),
      2 => get_theme_mod( 'arr_footer_about_heading', __( 'About ARR', 'arr-theme' ) ),
      3 => get_theme_mod( 'arr_footer_work_heading', __( 'Work With Us', 'arr-theme' ) ),
    );
    ?>
    <?php foreach ( $arr_footer_headings as $col => $heading ) : ?>
      <nav class="footer-col" aria-label="<?php echo esc_attr( $heading ); ?>">
        <h4><?php echo esc_html( $heading ); ?></h4>
        <?php arr_footer_menu_column( $col ); ?>
      </nav>
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

      <?php foreach ( array( 'privacy-policy' => __( 'Privacy Policy', 'arr-theme' ), 'terms-conditions' => __( 'Terms & Conditions', 'arr-theme' ) ) as $arr_slug => $arr_label ) : ?>
        <?php $arr_legal_url = arr_page_url( $arr_slug ); ?>
        <?php if ( $arr_legal_url ) : ?>
          <a class="staff-link" href="<?php echo esc_url( $arr_legal_url ); ?>"><?php echo esc_html( $arr_label ); ?></a>
        <?php endif; ?>
      <?php endforeach; ?>

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

    <a class="back-to-top" href="#top"><?php esc_html_e( 'Back to top', 'arr-theme' ); ?> <span aria-hidden="true">&uarr;</span></a>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
