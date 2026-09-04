<?php
/**
 * Template Name: Advertise
 *
 * Select this under Page Attributes → Template when creating your "Advertise
 * with us" page.
 *
 * Every figure and price on this page is an editable field with a placeholder
 * default, because rates are the client's commercial decision — the defaults
 * are marked as examples rather than presented as real prices, so nothing here
 * can be quoted at a real advertiser by accident.
 */
get_header();

$notice = arr_advertise_notice();

// Placements: only the ones with a name are shown, so the client can offer two
// rather than three without leaving an empty column.
//
// The defaults deliberately quote no figure. A price is the client's commercial
// decision, and a plausible-looking number sitting here is one that gets quoted
// at a real advertiser before anyone notices the theme invented it — so the
// fallback is "Rate on request", which is true whatever they decide to charge.
$placement_defaults = array(
	1 => array( 'Homepage banner',     'Rate on request', 'Your banner in the advertising strip on the homepage, seen by every visitor.' ),
	2 => array( 'Newsletter placement', 'Rate on request', 'A placement in the ARR newsletter, delivered directly to subscribers.' ),
	3 => array( 'Partnership',          "Let's talk",      'Longer-term support for a section or a series, on terms that keep our editorial independence intact.' ),
);

$placements = array();
foreach ( $placement_defaults as $i => $default ) {
	$name = arr_field( "ad_rate_{$i}_name", $default[0] );
	if ( ! $name ) {
		continue;
	}
	$placements[] = array(
		'name'  => $name,
		'price' => arr_field( "ad_rate_{$i}_price", $default[1] ),
		'desc'  => arr_field( "ad_rate_{$i}_desc", $default[2] ),
	);
}
?>

<div class="page-banner">
  <div class="wrap">
    <span class="eyebrow"><?php echo esc_html( arr_field( 'advertise_eyebrow', 'Partner with ARR' ) ); ?></span>
    <h1><?php echo esc_html( arr_field( 'advertise_title', 'Advertise with us' ) ); ?></h1>
    <p><?php echo esc_html( arr_field( 'advertise_subtitle', 'Reach readers who come to ARR for serious analysis of the questions shaping Africa — in government, business, academia and civil society.' ) ); ?></p>
  </div>
</div>

<?php if ( $placements ) : ?>
<section class="rates-section">
  <div class="wrap">
    <div class="section-head">
      <h2><?php echo esc_html( arr_field( 'advertise_rates_heading', 'Placements' ) ); ?></h2>
    </div>
    <div class="rate-grid" data-count="<?php echo esc_attr( count( $placements ) ); ?>">
      <?php foreach ( $placements as $placement ) : ?>
        <div class="rate-card">
          <h3><?php echo esc_html( $placement['name'] ); ?></h3>
          <?php if ( $placement['price'] ) : ?>
            <span class="rate-price"><?php echo esc_html( $placement['price'] ); ?></span>
          <?php endif; ?>
          <?php if ( $placement['desc'] ) : ?>
            <p><?php echo esc_html( $placement['desc'] ); ?></p>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
    <?php $rates_note = arr_field( 'advertise_rates_note', 'Rates are a guide. Tell us what you have in mind and we will put together something that fits.' ); ?>
    <?php if ( $rates_note ) : ?>
      <p class="rate-note"><?php echo esc_html( $rates_note ); ?></p>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>

<section class="contribute-section">
  <div class="wrap">
    <div class="contribute-grid">

      <div class="contribute-card" id="advertise-form">
        <div class="contribute-card-head">
          <h2><?php echo esc_html( arr_field( 'advertise_form_heading', 'Make an enquiry' ) ); ?></h2>
          <p><?php echo esc_html( arr_field( 'advertise_form_intro', 'Tell us a little about what you would like to promote and we will come back with options and availability.' ) ); ?></p>
        </div>

        <?php if ( $notice ) : ?>
          <div class="form-notice form-notice-<?php echo esc_attr( $notice['tone'] ); ?>" role="status">
            <?php echo esc_html( $notice['text'] ); ?>
          </div>
        <?php endif; ?>

        <form class="contribute-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
          <input type="hidden" name="action" value="arr_advertise" />
          <?php wp_nonce_field( 'arr_advertise', 'arr_advertise_nonce' ); ?>

          <div class="arr-hp" aria-hidden="true">
            <label for="arr_website_hp"><?php esc_html_e( 'Leave this field empty', 'arr-theme' ); ?></label>
            <input type="text" id="arr_website_hp" name="arr_website" tabindex="-1" autocomplete="off" />
          </div>

          <div class="field-row">
            <p class="field">
              <label for="arr_name"><?php esc_html_e( 'Your name', 'arr-theme' ); ?> <span class="req">*</span></label>
              <input type="text" id="arr_name" name="arr_name" required />
            </p>
            <p class="field">
              <label for="arr_company"><?php esc_html_e( 'Organisation', 'arr-theme' ); ?></label>
              <input type="text" id="arr_company" name="arr_company" />
            </p>
          </div>

          <div class="field-row">
            <p class="field">
              <label for="arr_email"><?php esc_html_e( 'Email address', 'arr-theme' ); ?> <span class="req">*</span></label>
              <input type="email" id="arr_email" name="arr_email" required />
            </p>
            <p class="field">
              <label for="arr_phone"><?php esc_html_e( 'Phone (optional)', 'arr-theme' ); ?></label>
              <input type="tel" id="arr_phone" name="arr_phone" />
            </p>
          </div>

          <div class="field-row">
            <p class="field">
              <label for="arr_url"><?php esc_html_e( 'Your website (optional)', 'arr-theme' ); ?></label>
              <input type="url" id="arr_url" name="arr_url" placeholder="https://" />
            </p>
            <p class="field">
              <label for="arr_interest"><?php esc_html_e( 'Interested in', 'arr-theme' ); ?></label>
              <select id="arr_interest" name="arr_interest">
                <option value=""><?php esc_html_e( 'Choose…', 'arr-theme' ); ?></option>
                <?php foreach ( $placements as $placement ) : ?>
                  <option value="<?php echo esc_attr( $placement['name'] ); ?>"><?php echo esc_html( $placement['name'] ); ?></option>
                <?php endforeach; ?>
                <option value="Something else"><?php esc_html_e( 'Something else', 'arr-theme' ); ?></option>
              </select>
            </p>
          </div>

          <p class="field">
            <label for="arr_message"><?php esc_html_e( 'What would you like to promote?', 'arr-theme' ); ?> <span class="req">*</span></label>
            <textarea id="arr_message" name="arr_message" rows="6" required></textarea>
          </p>

          <button type="submit" class="btn btn-primary"><?php echo esc_html( arr_field( 'advertise_submit_text', 'Send enquiry' ) ); ?></button>
          <p class="form-fineprint"><?php echo esc_html( arr_field( 'advertise_fineprint', 'We use your details only to reply to this enquiry. They are not stored on the website and never shared.' ) ); ?></p>
        </form>
      </div>

      <aside class="contribute-aside">
        <h4><?php echo esc_html( arr_field( 'advertise_aside_heading', 'What we ask' ) ); ?></h4>
        <ul class="advertise-points">
          <li><?php echo esc_html( arr_field( 'advertise_point_1', 'Advertising is always labelled as advertising. It never appears as editorial.' ) ); ?></li>
          <li><?php echo esc_html( arr_field( 'advertise_point_2', 'Advertisers have no say over what we publish, and no advance sight of it.' ) ); ?></li>
          <li><?php echo esc_html( arr_field( 'advertise_point_3', 'We decline advertising that misleads readers or conflicts with our editorial independence.' ) ); ?></li>
        </ul>

        <div class="contribute-note">
          <p><?php echo esc_html( arr_field( 'advertise_note', 'Prefer email? Write to us directly.' ) ); ?></p>
          <a class="view-all" href="mailto:<?php echo esc_attr( ARR_CONTACT_EMAIL ); ?>"><?php echo esc_html( ARR_CONTACT_EMAIL ); ?> <span aria-hidden="true">&rarr;</span></a>
        </div>
      </aside>

    </div>
  </div>
</section>

<?php get_footer(); ?>
