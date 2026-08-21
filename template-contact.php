<?php
/**
 * Template Name: Contact Page
 *
 * Select this under Page Attributes → Template when creating your Contact page.
 */
get_header();

$contact_email    = arr_field( 'contact_email', '' );
$contact_phone    = arr_field( 'contact_phone', '' );
$contact_location = arr_field( 'contact_location', '' );
$contact_hours    = arr_field( 'contact_hours', '' );
$contact_note     = arr_field( 'contact_note', 'We read every message. Expect a reply within two working days.' );
?>

<div class="page-banner">
  <div class="wrap">
    <span class="eyebrow"><?php echo esc_html( arr_field( 'contact_eyebrow', 'Get in Touch' ) ); ?></span>
    <h1><?php echo esc_html( arr_field( 'contact_title', "We'd love to hear from you" ) ); ?></h1>
    <p><?php echo esc_html( arr_field( 'contact_subtitle', 'Story ideas, partnership inquiries, corrections, or submissions — reach the ARR editorial team directly.' ) ); ?></p>
  </div>
</div>

<section class="contact-section">
  <div class="wrap contact-grid">
    <div class="contact-form-col">
      <div class="contact-card">
        <h2><?php echo esc_html( arr_field( 'contact_form_heading', 'Send us a message' ) ); ?></h2>
        <div class="contact-form">
          <?php echo do_shortcode( '[wpforms id="55"]' ); ?>
        </div>
      </div>
    </div>

    <aside class="contact-details">
      <h4><?php echo esc_html( arr_field( 'contact_details_heading', 'Contact Details' ) ); ?></h4>

      <?php if ( $contact_email ) : ?>
        <div class="contact-detail">
          <span class="lbl"><?php esc_html_e( 'Email', 'arr-theme' ); ?></span>
          <a href="mailto:<?php echo esc_attr( $contact_email ); ?>"><?php echo esc_html( $contact_email ); ?></a>
        </div>
      <?php endif; ?>

      <?php if ( $contact_phone ) : ?>
        <div class="contact-detail">
          <span class="lbl"><?php esc_html_e( 'Phone', 'arr-theme' ); ?></span>
          <a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $contact_phone ) ); ?>"><?php echo esc_html( $contact_phone ); ?></a>
        </div>
      <?php endif; ?>

      <?php if ( $contact_location ) : ?>
        <div class="contact-detail">
          <span class="lbl"><?php esc_html_e( 'Location', 'arr-theme' ); ?></span>
          <span class="val"><?php echo esc_html( $contact_location ); ?></span>
        </div>
      <?php endif; ?>

      <?php if ( $contact_hours ) : ?>
        <div class="contact-detail">
          <span class="lbl"><?php esc_html_e( 'Office Hours', 'arr-theme' ); ?></span>
          <span class="val"><?php echo esc_html( $contact_hours ); ?></span>
        </div>
      <?php endif; ?>

      <?php $contact_social = function_exists( 'arr_social_links' ) ? arr_social_links() : array(); ?>
      <?php if ( $contact_social ) : ?>
        <div class="contact-detail contact-social">
          <span class="lbl"><?php esc_html_e( 'Follow', 'arr-theme' ); ?></span>
          <div class="social-row">
            <?php foreach ( $contact_social as $link ) : ?>
              <a href="<?php echo esc_url( $link['url'] ); ?>" aria-label="<?php echo esc_attr( $link['label'] ); ?>"<?php echo $link['external'] ? ' target="_blank" rel="noopener"' : ''; ?>><?php echo esc_html( $link['glyph'] ); ?></a>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <?php if ( $contact_note ) : ?>
        <p class="contact-note"><?php echo esc_html( $contact_note ); ?></p>
      <?php endif; ?>
    </aside>
  </div>
</section>

<?php get_footer(); ?>
