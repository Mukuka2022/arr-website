<?php
/**
 * Template Name: Categories
 *
 * Select this under Page Attributes → Template when creating your Categories
 * page. The homepage strip links here once the page exists.
 *
 * WordPress has category archives but no page that lists the categories
 * themselves, so this is that page: every subject the publication covers, what
 * each one is for, and how much has been published in it.
 *
 * Editor's Notes is left out deliberately — it is a publishing mechanism with
 * its own page, not a subject someone would browse for. See
 * arr_pillar_categories().
 */
get_header();

// 0 = no limit. Empty categories are included: a subject with nothing in it yet
// is still part of the publication's stated scope, and the card says "No
// articles yet" rather than pretending the subject does not exist.
$categories = arr_pillar_categories( 0, false );
?>

<div class="page-banner">
  <div class="wrap">
    <span class="eyebrow"><?php echo esc_html( arr_field( 'categories_eyebrow', 'Browse' ) ); ?></span>
    <h1><?php echo esc_html( arr_field( 'categories_title', 'Categories' ) ); ?></h1>
    <p><?php echo esc_html( arr_field( 'categories_subtitle', 'Every subject the African Renaissance Review covers, from governance and technology to faith, history and sport.' ) ); ?></p>
  </div>
</div>

<section style="padding-bottom: 90px;">
  <div class="wrap">
    <?php if ( $categories ) : ?>
      <div class="category-index">
        <?php foreach ( $categories as $category ) : ?>
          <a class="category-tile" href="<?php echo esc_url( get_category_link( $category ) ); ?>">
            <h2><?php echo esc_html( $category->name ); ?></h2>
            <?php if ( $category->description ) : ?>
              <p><?php echo esc_html( $category->description ); ?></p>
            <?php endif; ?>
            <span class="category-count">
              <?php
              echo esc_html( $category->count
                ? sprintf(
                    /* translators: %s: number of articles. */
                    _n( '%s article', '%s articles', $category->count, 'arr-theme' ),
                    number_format_i18n( $category->count )
                  )
                : __( 'No articles yet', 'arr-theme' )
              );
              ?>
            </span>
          </a>
        <?php endforeach; ?>
      </div>
    <?php else : ?>
      <p style="color:var(--muted);"><?php echo esc_html( arr_field( 'categories_empty_text', 'Categories will appear here once they are created under Posts → Categories.' ) ); ?></p>
    <?php endif; ?>
  </div>
</section>

<?php get_footer(); ?>
