<footer class="site-footer">
  <div class="wrap footer-top">
    <div class="footer-brand">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="brand">
        <img src="<?php echo esc_url( get_template_directory_uri() . '/images/logo.png' ); ?>" alt="" style="height:38px;" />
        <span class="brand-text"><span class="arr">ARR</span></span>
      </a>
      <p>Shaping Africa's Intellectual Renaissance. Independent. Research-driven. Unapologetically African.</p>
    </div>
    <div class="footer-col">
      <h4>Company</h4>
      <a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About Us</a>
      <a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">Editorial Charter</a>
      <a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">Our Team</a>
      <a href="#">Careers</a>
    </div>
    <div class="footer-col">
      <h4>Resources</h4>
      <a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">Authors</a>
      <a href="<?php echo esc_url( home_url( '/articles/' ) ); ?>">Archive</a>
      <a href="<?php echo esc_url( home_url( '/subscribe/' ) ); ?>">Newsletter</a>
      <a href="#">Podcasts</a>
    </div>
    <div class="footer-col">
      <h4>Support</h4>
      <a href="#">Contact Us</a>
      <a href="<?php echo esc_url( home_url( '/subscribe/' ) ); ?>">Submissions</a>
      <a href="#">FAQs</a>
      <a href="#">Privacy Policy</a>
    </div>
  </div>
  <div class="wrap footer-bottom">
    <span>&copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. All rights reserved.</span>
    <div class="social-row">
      <a href="#" aria-label="X">𝕏</a>
      <a href="#" aria-label="LinkedIn">in</a>
      <a href="#" aria-label="Facebook">f</a>
      <a href="#" aria-label="YouTube">▶</a>
    </div>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
