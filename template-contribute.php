<?php
/**
 * Template Name: Contribute
 *
 * Select this under Page Attributes → Template when creating your Contribute
 * page. The "Contribute" button in the header points here.
 *
 * Applications are emailed to the editorial desk; accounts are created by hand
 * afterwards. See inc/contribute-form.php for why registration is not open.
 */
get_header();

$notice  = arr_contribute_notice();
$pillars = get_categories( array( 'hide_empty' => false, 'number' => 12 ) );
?>

<div class="page-banner">
  <div class="wrap">
    <span class="eyebrow"><?php echo esc_html( arr_field( 'contribute_eyebrow', 'Write for ARR' ) ); ?></span>
    <h1><?php echo esc_html( arr_field( 'contribute_title', 'Become a Contributor' ) ); ?></h1>
    <p><?php echo esc_html( arr_field( 'contribute_subtitle', 'ARR publishes rigorous, evidence-based writing on the questions shaping Africa. If that is the work you want to do, tell us about it.' ) ); ?></p>
  </div>
</div>

<section class="contribute-section">
  <div class="wrap">
    <div class="contribute-grid">

      <div class="contribute-card" id="contribute-form">
        <div class="contribute-card-head">
          <h2><?php echo esc_html( arr_field( 'contribute_form_heading', 'Tell us about your work' ) ); ?></h2>
          <p><?php echo esc_html( arr_field( 'contribute_form_intro', 'Every application is read by an editor. There is no form letter and no automated rejection.' ) ); ?></p>
        </div>

        <?php if ( $notice ) : ?>
          <div class="form-notice form-notice-<?php echo esc_attr( $notice['tone'] ); ?>" role="status">
            <?php echo esc_html( $notice['text'] ); ?>
          </div>
        <?php endif; ?>

        <form class="contribute-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
          <input type="hidden" name="action" value="arr_contribute" />
          <?php wp_nonce_field( 'arr_contribute', 'arr_contribute_nonce' ); ?>

          <?php /* Hidden from people, irresistible to form bots. Not type="hidden",
                   which bots skip — an off-screen real field catches far more. */ ?>
          <div class="arr-hp" aria-hidden="true">
            <label for="arr_website"><?php esc_html_e( 'Leave this field empty', 'arr-theme' ); ?></label>
            <input type="text" id="arr_website" name="arr_website" tabindex="-1" autocomplete="off" />
          </div>

          <div class="field-row">
            <p class="field">
              <label for="arr_name"><?php esc_html_e( 'Full name', 'arr-theme' ); ?> <span class="req">*</span></label>
              <input type="text" id="arr_name" name="arr_name" required />
            </p>
            <p class="field">
              <label for="arr_email"><?php esc_html_e( 'Email address', 'arr-theme' ); ?> <span class="req">*</span></label>
              <input type="email" id="arr_email" name="arr_email" required />
            </p>
          </div>

          <div class="field-row">
            <p class="field">
              <label for="arr_phone"><?php esc_html_e( 'Phone (optional)', 'arr-theme' ); ?></label>
              <input type="tel" id="arr_phone" name="arr_phone" />
            </p>
            <p class="field">
              <label for="arr_pillar"><?php esc_html_e( 'Area of interest', 'arr-theme' ); ?></label>
              <select id="arr_pillar" name="arr_pillar">
                <option value=""><?php esc_html_e( 'Choose a pillar…', 'arr-theme' ); ?></option>
                <?php foreach ( $pillars as $pillar ) : ?>
                  <option value="<?php echo esc_attr( $pillar->name ); ?>"><?php echo esc_html( $pillar->name ); ?></option>
                <?php endforeach; ?>
              </select>
            </p>
          </div>

          <p class="field">
            <label for="arr_links"><?php esc_html_e( 'Link to published work (optional)', 'arr-theme' ); ?></label>
            <input type="url" id="arr_links" name="arr_links" placeholder="https://" />
          </p>

          <p class="field">
            <label for="arr_pitch"><?php esc_html_e( 'What would you like to write about?', 'arr-theme' ); ?> <span class="req">*</span></label>
            <textarea id="arr_pitch" name="arr_pitch" rows="6" required></textarea>
          </p>

          <button type="submit" class="btn btn-primary"><?php echo esc_html( arr_field( 'contribute_submit_text', 'Send application' ) ); ?></button>
          <p class="form-fineprint"><?php echo esc_html( arr_field( 'contribute_fineprint', 'We use your details only to reply to this application. They are not stored on the website and never shared.' ) ); ?></p>
        </form>
      </div>

      <aside class="contribute-aside">
        <h4><?php echo esc_html( arr_field( 'contribute_aside_heading', 'How it works' ) ); ?></h4>
        <ol class="contribute-steps">
          <li>
            <strong><?php echo esc_html( arr_field( 'contribute_step_1_title', 'You apply' ) ); ?></strong>
            <span><?php echo esc_html( arr_field( 'contribute_step_1_text', 'Send the form with a short note on the subjects you follow and how you would approach them.' ) ); ?></span>
          </li>
          <li>
            <strong><?php echo esc_html( arr_field( 'contribute_step_2_title', 'An editor reads it' ) ); ?></strong>
            <span><?php echo esc_html( arr_field( 'contribute_step_2_text', 'We look for command of the subject and a clear argument, not a long list of credits.' ) ); ?></span>
          </li>
          <li>
            <strong><?php echo esc_html( arr_field( 'contribute_step_3_title', 'We set up your account' ) ); ?></strong>
            <span><?php echo esc_html( arr_field( 'contribute_step_3_text', 'Accepted contributors get a login and can draft directly on the site. An editor reviews each piece before it publishes.' ) ); ?></span>
          </li>
        </ol>

        <div class="contribute-note">
          <p><?php echo esc_html( arr_field( 'contribute_note', 'Already write for ARR? Sign in to start a new draft.' ) ); ?></p>
          <a class="view-all" href="<?php echo esc_url( wp_login_url( home_url( '/wp-admin/' ) ) ); ?>"><?php esc_html_e( 'Contributor sign-in', 'arr-theme' ); ?> <span aria-hidden="true">&rarr;</span></a>
        </div>
      </aside>

    </div>
  </div>
</section>

<?php get_footer(); ?>
