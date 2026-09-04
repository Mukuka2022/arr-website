<?php
/**
 * Share buttons for the current post.
 *
 * Shared by articles and caricatures. Extracted rather than copied, so the
 * copy-link button's markup and the JS hook it depends on cannot drift apart
 * between the two.
 *
 * @param string $args['label'] Heading above the buttons.
 */
$arr_share = arr_share_links();
$arr_label = isset( $args['label'] ) ? $args['label'] : __( 'Share this', 'arr-theme' );
?>
<?php if ( $arr_share ) : ?>
  <div class="share-row">
    <span class="share-label"><?php echo esc_html( $arr_label ); ?></span>
    <div class="share-links">
      <?php foreach ( $arr_share as $arr_target ) : ?>
        <a class="share-btn" href="<?php echo esc_url( $arr_target['url'] ); ?>"
           target="_blank" rel="noopener noreferrer"
           aria-label="<?php echo esc_attr( $arr_target['label'] ); ?>"
           title="<?php echo esc_attr( $arr_target['label'] ); ?>">
          <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="<?php echo esc_attr( $arr_target['icon'] ); ?>" /></svg>
        </a>
      <?php endforeach; ?>
      <button type="button" class="share-btn share-copy"
              data-share-url="<?php echo esc_url( get_permalink() ); ?>"
              aria-label="<?php esc_attr_e( 'Copy link', 'arr-theme' ); ?>"
              title="<?php esc_attr_e( 'Copy link', 'arr-theme' ); ?>">
        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M10.6 13.4a1 1 0 0 1 0-1.41l3.54-3.54a3 3 0 1 1 4.24 4.24l-1.77 1.77a1 1 0 0 1-1.41-1.41l1.77-1.77a1 1 0 0 0-1.42-1.42l-3.53 3.54a1 1 0 0 1-1.42 0zm2.8-2.8a1 1 0 0 1 1.42 1.41l-3.54 3.54a1 1 0 0 0 1.42 1.42l1.76-1.77a1 1 0 0 1 1.42 1.41l-1.77 1.77a3 3 0 0 1-4.24-4.24z" /></svg>
        <span class="share-copied" aria-hidden="true"><?php esc_html_e( 'Copied', 'arr-theme' ); ?></span>
      </button>
    </div>
  </div>
<?php endif; ?>
