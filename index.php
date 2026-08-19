<?php get_header(); ?>

<section>
  <div class="wrap">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
      <article style="margin-bottom:40px;padding-bottom:40px;border-bottom:1px solid var(--hairline);">
        <h2><a href="<?php the_permalink(); ?>" style="color:inherit;"><?php the_title(); ?></a></h2>
        <p style="color:var(--muted);margin-top:10px;"><?php the_excerpt(); ?></p>
      </article>
    <?php endwhile; else : ?>
      <p><?php echo esc_html( get_theme_mod( 'arr_archive_empty_text', 'No articles here yet.' ) ); ?></p>
    <?php endif; ?>
  </div>
</section>

<?php get_footer(); ?>
