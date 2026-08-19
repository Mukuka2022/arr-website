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
      <h2><?php echo esc_html( arr_field( 'contact_form_heading', 'Send us a message' ) ); ?></h2>
      <!-- TODO: this form does not submit anywhere yet. Install WPForms or
           Fluent Forms and replace this block with the plugin's shortcode. -->
      <form class="contact-form" action="#" method="post">
        <div class="field">
          <label for="contact-name"><?php echo esc_html( arr_field( 'contact_name_label', 'Your name' ) ); ?></label>
          <input type="text" id="contact-name" name="name" required />
        </div>
        <div class="field">
          <label for="contact-email"><?php echo esc_html( arr_field( 'contact_email_label', 'Email address' ) ); ?></label>
          <input type="email" id="contact-email" name="email" required />
        </div>
        <div class="field">
          <label for="contact-subject"><?php echo esc_html( arr_field( 'contact_subject_label', 'Subject' ) ); ?></label>
          <input type="text" id="contact-subject" name="subject" />
        </div>
        <div class="field">
          <label for="contact-message"><?php echo esc_html( arr_field( 'contact_message_label', 'Message' ) ); ?></label>
          <textarea id="contact-message" name="message" required></textarea>
        </div>
        <button class="btn btn-primary" type="submit"><?php echo esc_html( arr_field( 'contact_submit_text', 'Send Message' ) ); ?></button>
      </form>
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

      <?php if ( $contact_note ) : ?>
        <p class="contact-note"><?php echo esc_html( $contact_note ); ?></p>
      <?php endif; ?>
    </aside>
  </div>
</section>

<?php get_footer(); ?>
