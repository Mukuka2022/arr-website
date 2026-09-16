<?php
/**
 * The "Our Analysis" bar: a rotating ticker of recent pieces with the newest
 * one spotlit beside it.
 *
 * Shared by the Articles page and every section landing page, so the markup
 * lives once and the bar cannot drift into several slightly different
 * versions.
 *
 * @param int[] $args['category_ids'] Limit the bar to these categories. Empty
 *                                    means every post on the site.
 *
 * The label is a site-wide Customizer setting rather than a per-page field.
 * It used to be the ACF field articles_breaking_label, and a value typed into
 * that field ("Breaking NEWS") was stored on the Articles page — which beats
 * any default the theme sets, so renaming the label in code changed nothing on
 * screen. A setting under a new name has no stored value to override it, and
 * one setting covers every page the bar appears on.
 */

$category_ids = isset( $args['category_ids'] ) ? array_filter( array_map( 'intval', (array) $args['category_ids'] ) ) : array();
$label        = get_theme_mod( 'arr_analysis_bar_label', __( 'Our Analysis', 'arr-theme' ) );

$query = array(
	'numberposts'         => 4,
	'ignore_sticky_posts' => true,
	'post_status'         => 'publish',
);

// A section with no categories yet would otherwise fall through to "no
// filter" and show the whole site's newest posts under a section heading
// they do not belong to.
if ( isset( $args['category_ids'] ) ) {
	if ( ! $category_ids ) {
		return;
	}
	$query['category__in'] = $category_ids;
}

// Four posts: the newest becomes the spotlight card on the right of the bar,
// the next three rotate through the ticker on the left.
$items = get_posts( $query );
$lead  = $items ? array_shift( $items ) : null;

if ( ! $lead ) {
	return;
}
?>
<div class="breaking-bar">
  <div class="breaking-bar-inner">
    <div class="breaking-live">
      <span class="breaking-badge"><?php echo esc_html( $label ); ?></span>
      <?php if ( $items ) : ?>
        <div class="breaking-ticker" data-slider>
          <div class="breaking-ticker-window">
            <div class="slider-track">
              <?php foreach ( $items as $item ) : ?>
                <div class="slider-slide">
                  <a href="<?php echo esc_url( get_permalink( $item ) ); ?>" class="breaking-item"><?php echo esc_html( get_the_title( $item ) ); ?></a>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="slider-dots"></div>
        </div>
      <?php endif; ?>
    </div>

    <a class="breaking-lead" href="<?php echo esc_url( get_permalink( $lead ) ); ?>">
      <?php if ( has_post_thumbnail( $lead ) ) : ?>
        <?php echo get_the_post_thumbnail( $lead, 'arr-card' ); ?>
      <?php else : ?>
        <img src="https://picsum.photos/seed/<?php echo esc_attr( $lead->ID ); ?>/160/160" alt="" />
      <?php endif; ?>
      <span class="breaking-lead-copy">
        <?php $lead_cats = get_the_category( $lead->ID ); ?>
        <span class="breaking-lead-cat">
          <?php echo esc_html( $lead_cats ? $lead_cats[0]->name : $label ); ?>
        </span>
        <span class="breaking-lead-title"><?php echo esc_html( get_the_title( $lead ) ); ?></span>
        <span class="breaking-lead-time">
          <?php echo esc_html( sprintf(
            /* translators: %s: human-readable time difference, e.g. "2 hours". */
            __( '%s ago', 'arr-theme' ),
            human_time_diff( get_post_time( 'U', true, $lead ), time() )
          ) ); ?>
        </span>
      </span>
    </a>
  </div>
</div>
