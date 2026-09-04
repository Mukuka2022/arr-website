<?php
/**
 * Template Name: Editor's Notes
 *
 * Select this under Page Attributes → Template when creating your Editor's
 * Notes page.
 *
 * Shows only posts filed under the "Editor's Notes" category. Editors write a
 * note the same way they write anything else — Posts → Add New — and tick that
 * one category; there is no second editor, no separate menu and nothing new to
 * learn. The category is created for you, so the page works from the moment it
 * is published.
 *
 * Notes are laid out as a dated list rather than a card grid. They are short,
 * frequent and read in sequence, which a grid of thumbnails works against.
 */
get_header();

$paged    = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
$category = arr_editors_notes_category();

$notes = new WP_Query( array(
	'posts_per_page' => 10,
	'paged'          => $paged,
	'cat'            => $category ? $category->term_id : 0,
	// Without this a missing category would fall through to "no filter" and
	// list every post on the site, which is precisely what this page must not do.
	'post__in'       => $category ? array() : array( 0 ),
) );
?>

<div class="page-banner">
  <div class="wrap">
    <span class="eyebrow"><?php echo esc_html( arr_field( 'notes_eyebrow', 'From the Desk' ) ); ?></span>
    <h1><?php echo esc_html( arr_field( 'notes_title', "Editor's Notes" ) ); ?></h1>
    <p><?php echo esc_html( arr_field( 'notes_subtitle', 'Short reflections from the ARR editorial desk — on the stories we chose, the ones we did not, and why.' ) ); ?></p>
  </div>
</div>

<section style="padding-bottom: 90px;">
  <div class="wrap">
    <?php if ( $notes->have_posts() ) : ?>
      <div class="note-list">
        <?php while ( $notes->have_posts() ) : $notes->the_post(); ?>
          <article class="note-item">
            <div class="note-date">
              <span class="note-day"><?php echo esc_html( get_the_date( 'j' ) ); ?></span>
              <span class="note-month"><?php echo esc_html( get_the_date( 'M Y' ) ); ?></span>
            </div>
            <div class="note-body">
              <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
              <p class="dek"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 34 ) ); ?></p>
              <div class="note-meta">
                <?php echo get_avatar( get_the_author_meta( 'ID' ), 40 ); ?>
                <span><?php the_author(); ?> · <?php echo esc_html( arr_reading_time() ); ?> min read</span>
              </div>
            </div>
          </article>
        <?php endwhile; ?>
      </div>

      <div class="load-more">
        <?php
        echo paginate_links( array(
          'total'     => $notes->max_num_pages,
          'current'   => $paged,
          'prev_text' => '← Newer',
          'next_text' => 'Older →',
        ) );
        ?>
      </div>
    <?php else : ?>
      <p class="note-empty"><?php echo esc_html( arr_field( 'notes_empty_text', "No editor's notes yet. Write a post, tick the “Editor's Notes” category, and it will appear here." ) ); ?></p>
    <?php endif; ?>
    <?php wp_reset_postdata(); ?>
  </div>
</section>

<?php get_footer(); ?>
