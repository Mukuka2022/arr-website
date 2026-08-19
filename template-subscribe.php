<?php
/**
 * Template Name: Subscribe Page
 *
 * Select this under Page Attributes → Template when creating your Subscribe page.
 */
get_header();

$sub_eyebrow  = arr_field( 'sub_eyebrow', 'Weekly Brief' );
$sub_headline = arr_field( 'sub_headline', 'A curated briefing of ideas that matter — every Saturday.' );
$sub_dek      = arr_field( 'sub_dek', "Join policymakers, executives, and scholars who start their weekend with ARR's take on the analysis shaping Africa's next chapter." );

$stat_defaults = array(
  array( 'Editorial Pillars Covered', '7' ),
  array( 'Avg. Read Time', '10 min' ),
  array( 'Publishing Cadence', 'Weekly' ),
  // Value left blank on purpose: falls back to the live registered-user count.
  array( 'Contributing Voices', '' ),
);

$tier_defaults = array(
  array(
    'badge'       => '',
    'name'        => 'Reader',
    'price'       => 'Free',
    'features'    => "Full access to every article\nWeekly newsletter\nPodcast episodes",
    'button_text' => 'Get Started',
    'featured'    => false,
  ),
  array(
    'badge'       => 'Recommended',
    'name'        => 'Free Account',
    'price'       => 'Free',
    'features'    => "Everything in Reader\nSave articles to read later\nComment & discussion access\nEarly notice of special reports",
    'button_text' => 'Create Free Account',
    'featured'    => true,
  ),
);
?>

<section class="signup-band" style="padding-bottom:70px;">
  <div class="wrap signup-grid">
    <div>
      <span class="eyebrow"><?php echo esc_html( $sub_eyebrow ); ?></span>
      <h1 style="margin-top:12px;"><?php echo esc_html( $sub_headline ); ?></h1>
      <p><?php echo esc_html( $sub_dek ); ?></p>
      <!-- TODO: point this form at your email service provider -->
      <form class="signup-form" action="#" method="post">
        <input type="email" name="email" placeholder="<?php echo esc_attr( arr_field( 'sub_placeholder', 'Your email address' ) ); ?>" required />
        <button class="btn btn-primary" type="submit"><?php echo esc_html( arr_field( 'sub_button_text', 'Subscribe Free' ) ); ?></button>
      </form>
      <p class="fine-print"><?php echo esc_html( arr_field( 'sub_fine_print', 'No spam. Unsubscribe anytime. One email, every Saturday.' ) ); ?></p>
    </div>
    <div class="signup-visual">
      <?php foreach ( $stat_defaults as $i => $default ) :
        $n     = $i + 1;
        $label = arr_field( "sub_stat_{$n}_label", $default[0] );
        $value = arr_field( "sub_stat_{$n}_value", $default[1] );
        if ( ! $value ) {
          $value = intval( count_users()['total_users'] ) . '+';
        }
        if ( ! $label ) continue;
      ?>
        <div class="stat"><span><?php echo esc_html( $label ); ?></span><b><?php echo esc_html( $value ); ?></b></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="membership-band">
  <div class="wrap">
    <div class="section-head" style="justify-content:center;flex-direction:column;text-align:center;align-items:center;">
      <span class="eyebrow"><?php echo esc_html( arr_field( 'mem_eyebrow', 'Membership' ) ); ?></span>
      <h2 style="margin-top:10px;"><?php echo esc_html( arr_field( 'mem_heading', 'Join ARR — free, always' ) ); ?></h2>
      <p style="color:var(--muted);max-width:520px;margin-top:12px;"><?php echo esc_html( arr_field( 'mem_sub', 'Create a free account to save articles, join the discussion, and get the weekly brief straight to your inbox.' ) ); ?></p>
    </div>
    <div class="tier-grid" style="margin-top:40px;">
      <?php foreach ( $tier_defaults as $i => $default ) :
        $n     = $i + 1;
        $name  = arr_field( "tier_{$n}_name", $default['name'] );
        if ( ! $name ) continue;
        $badge    = arr_field( "tier_{$n}_badge", $default['badge'] );
        $price    = arr_field( "tier_{$n}_price", $default['price'] );
        $features = arr_lines_to_list( arr_field( "tier_{$n}_features", $default['features'] ) );
        $btn_text = arr_field( "tier_{$n}_button_text", $default['button_text'] );
        $btn_link = arr_field( "tier_{$n}_button_link", wp_registration_url() );
        $featured = function_exists( 'get_field' ) && null !== get_field( "tier_{$n}_featured" )
          ? (bool) get_field( "tier_{$n}_featured" )
          : $default['featured'];
      ?>
        <div class="tier-card<?php echo $featured ? ' featured' : ''; ?>">
          <?php if ( $badge ) : ?><span class="tier-badge"><?php echo esc_html( $badge ); ?></span><?php endif; ?>
          <h3><?php echo esc_html( $name ); ?></h3>
          <div class="tier-price"><?php echo esc_html( $price ); ?></div>
          <ul>
            <?php foreach ( $features as $feature ) : ?>
              <li><?php echo esc_html( $feature ); ?></li>
            <?php endforeach; ?>
          </ul>
          <a href="<?php echo esc_url( $btn_link ); ?>" class="btn <?php echo $featured ? 'btn-primary' : 'btn-outline'; ?>"<?php echo $featured ? '' : ' style="color:var(--midnight);border-color:var(--hairline);"'; ?>><?php echo esc_html( $btn_text ); ?></a>
        </div>
      <?php endforeach; ?>
    </div>
    <!-- NOTE: wp_registration_url() only works once Settings → General → "Anyone can register" is turned on. -->
  </div>
</section>

<?php
// "Why Support ARR" — the stylesheet has always carried .why-grid/.why-item;
// this is the first template to use it, so the copy below is new and should
// be confirmed with the client before launch.
$why_defaults = array(
  array( 'Independent Journalism', 'No proprietor, party, or advertiser sets our editorial line. Our judgment is our own.' ),
  array( 'African Perspectives', 'Analysis rooted in African realities, written by people who live them.' ),
  array( 'Rigorous Research', 'Every claim is sourced. Every argument is built to withstand scrutiny.' ),
  array( 'Free & Open Access', 'No paywall, ever. Ideas that matter should not be rationed by ability to pay.' ),
);
$why_icon_svg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" width="40" height="40"><circle cx="12" cy="12" r="9"/><path d="M9 12l2 2 4-4"/></svg>';
?>
<section class="why-band">
  <div class="wrap">
    <div class="section-head" style="justify-content:center;flex-direction:column;text-align:center;align-items:center;">
      <h2><?php echo esc_html( arr_field( 'why_heading', 'Why Support ARR' ) ); ?></h2>
      <?php $why_sub = arr_field( 'why_sub', '' ); ?>
      <?php if ( $why_sub ) : ?>
        <p style="color:var(--muted);max-width:520px;margin-top:12px;"><?php echo esc_html( $why_sub ); ?></p>
      <?php endif; ?>
    </div>
    <div class="why-grid">
      <?php foreach ( $why_defaults as $i => $default ) :
        $n     = $i + 1;
        $title = arr_field( "why_{$n}_title", $default[0] );
        $text  = arr_field( "why_{$n}_text", $default[1] );
        if ( ! $title && ! $text ) continue;
        $icon = arr_field( "why_{$n}_icon", '' );
      ?>
        <div class="why-item">
          <div class="ic">
            <?php if ( $icon ) : ?>
              <img src="<?php echo esc_url( $icon ); ?>" alt="" />
            <?php else : echo $why_icon_svg; endif; ?>
          </div>
          <h4><?php echo esc_html( $title ); ?></h4>
          <p><?php echo esc_html( $text ); ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php get_footer(); ?>
