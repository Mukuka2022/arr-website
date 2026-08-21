<?php
/**
 * Search form.
 *
 * WordPress's default markup nests the search input inside a <label>, which
 * makes the label — not the input — the flex child of .search-form. The
 * `flex: 1` meant for the input then does nothing and the field collapses to
 * its intrinsic width instead of filling the bar. This template keeps the
 * label as a sibling so the input can actually grow.
 *
 * Each form gets a unique id so multiple forms on one page (header panel,
 * 404, search results) don't produce duplicate ids.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

static $arr_search_form_index = 0;
$arr_search_form_index++;
$field_id = 'arr-search-field-' . $arr_search_form_index;
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
  <label class="screen-reader-text" for="<?php echo esc_attr( $field_id ); ?>"><?php esc_html_e( 'Search for:', 'arr-theme' ); ?></label>
  <input
    type="search"
    id="<?php echo esc_attr( $field_id ); ?>"
    class="search-field"
    name="s"
    value="<?php echo esc_attr( get_search_query() ); ?>"
    placeholder="<?php esc_attr_e( 'Search articles, authors, topics…', 'arr-theme' ); ?>"
  />
  <button type="submit" class="search-submit"><?php esc_html_e( 'Search', 'arr-theme' ); ?></button>
</form>
