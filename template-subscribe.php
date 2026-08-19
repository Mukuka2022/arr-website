<?php
/**
 * Template Name: Subscribe Page
 *
 * Select this under Page Attributes → Template when creating your Subscribe page.
 */
get_header();

// Subscribe hero — editable via ACF ("Subscribe Page — Hero" field group)
// once this template is selected on a page. Falls back to approved copy.
$sub_eyebrow  = function_exists( 'get_field' ) ? get_field( 'sub_eyebrow' ) : '';
$sub_headline = function_exists( 'get_field' ) ? get_field( 'sub_headline' ) : '';
$sub_dek      = function_exists( 'get_field' ) ? get_field( 'sub_dek' ) : '';

if ( ! $sub_eyebrow )  $sub_eyebrow = 'Weekly Brief';
if ( ! $sub_headline ) $sub_headline = 'A curated briefing of ideas that matter — every Saturday.';
if ( ! $sub_dek )      $sub_dek = "Join policymakers, executives, and scholars who start their weekend with ARR's take on the analysis shaping Africa's next chapter.";
?>

<section class="signup-band" style="padding-bottom:70px;">
  <div class="wrap signup-grid">
    <div>
      <span class="eyebrow"><?php echo esc_html( $sub_eyebrow ); ?></span>
      <h1 style="margin-top:12px;"><?php echo esc_html( $sub_headline ); ?></h1>
      <p><?php echo esc_html( $sub_dek ); ?></p>
      <!-- TODO: point this form at your email service provider -->
      <form class="signup-form" action="#" method="post">
        <input type="email" name="email" placeholder="Your email address" required />
        <button class="btn btn-primary" type="submit">Subscribe Free</button>
      </form>
      <p class="fine-print">No spam. Unsubscribe anytime. One email, every Saturday.</p>
    </div>
    <div class="signup-visual">
      <div class="stat"><span>Editorial Pillars Covered</span><b>7</b></div>
      <div class="stat"><span>Avg. Read Time</span><b>10 min</b></div>
      <div class="stat"><span>Publishing Cadence</span><b>Weekly</b></div>
      <div class="stat"><span>Contributing Voices</span><b><?php echo intval( count_users()['total_users'] ); ?>+</b></div>
    </div>
  </div>
</section>

<section>
  <div class="wrap">
    <div class="section-head" style="justify-content:center;flex-direction:column;text-align:center;align-items:center;">
      <span class="eyebrow">Membership</span>
      <h2 style="margin-top:10px;">Join ARR — free, always</h2>
      <p style="color:var(--muted);max-width:520px;margin-top:12px;">Create a free account to save articles, join the discussion, and get the weekly brief straight to your inbox.</p>
    </div>
    <div class="tier-grid" style="margin-top:40px;">
      <div class="tier-card">
        <h3>Reader</h3>
        <div class="tier-price">Free</div>
        <ul>
          <li>Full access to every article</li>
          <li>Weekly newsletter</li>
          <li>Podcast episodes</li>
        </ul>
        <a href="<?php echo esc_url( wp_registration_url() ); ?>" class="btn btn-outline" style="color:var(--midnight);border-color:var(--hairline);">Get Started</a>
      </div>
      <div class="tier-card featured">
        <span class="tier-badge">Recommended</span>
        <h3>Free Account</h3>
        <div class="tier-price">Free</div>
        <ul>
          <li>Everything in Reader</li>
          <li>Save articles to read later</li>
          <li>Comment &amp; discussion access</li>
          <li>Early notice of special reports</li>
        </ul>
        <a href="<?php echo esc_url( wp_registration_url() ); ?>" class="btn btn-primary">Create Free Account</a>
      </div>
    </div>
    <!-- NOTE: wp_registration_url() only works once Settings → General → "Anyone can register" is turned on. -->
  </div>
</section>

<?php get_footer(); ?>
