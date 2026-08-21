<?php get_header(); ?>

<?php
// Latest 3 posts for the "Latest Opinions" sidebar
$latest = new WP_Query( array( 'posts_per_page' => 3, 'ignore_sticky_posts' => true ) );

// Every string falls back to the approved prototype copy, so an empty
// field restores the approved design rather than breaking it.
$hero_eyebrow     = arr_field( 'hero_eyebrow', "Editor's Pick" );
$hero_headline    = arr_field( 'hero_headline', "Africa's Future Will Be Built on Knowledge, Not Raw Materials" );
$hero_dek         = arr_field( 'hero_dek', "The continent's next leap will come from innovation, skills, institutions, and ideas that solve our own problems." );
$hero_button_text = arr_field( 'hero_button_text', 'Read the Editorial' );
$hero_button_link = arr_field( 'hero_button_link', home_url( '/articles/' ) );
$hero_image       = arr_field( 'hero_image', '' );

if ( ! $hero_image && has_post_thumbnail() ) $hero_image = get_the_post_thumbnail_url( get_the_ID(), 'large' );
?>

<section class="hero">
  <div class="hero-grid">
    <div class="hero-feature"<?php if ( $hero_image ) : ?> style="background-image: linear-gradient(100deg, rgba(11,31,58,0.95) 20%, rgba(11,31,58,0.55) 55%, rgba(11,31,58,0.15) 100%), url('<?php echo esc_url( $hero_image ); ?>');"<?php endif; ?>>
      <span class="eyebrow"><?php echo esc_html( $hero_eyebrow ); ?></span>
      <h1><?php echo esc_html( $hero_headline ); ?></h1>
      <p class="dek"><?php echo esc_html( $hero_dek ); ?></p>
      <a href="<?php echo esc_url( $hero_button_link ); ?>" class="btn btn-primary"><?php echo esc_html( $hero_button_text ); ?> &rarr;</a>
      <?php if ( ! function_exists( 'get_field' ) && current_user_can( 'edit_pages' ) ) : ?>
        <p style="color:rgba(250,250,247,0.35);font-size:11px;margin-top:16px;">(Install ACF to make this editable — admin-only note.)</p>
      <?php endif; ?>
    </div>
    <div class="hero-side">
      <h4><?php echo esc_html( arr_field( 'hero_side_heading', 'Latest Opinions' ) ); ?></h4>
      <?php if ( $latest->have_posts() ) : while ( $latest->have_posts() ) : $latest->the_post(); ?>
        <a href="<?php the_permalink(); ?>" class="mini-story" style="text-decoration:none;">
          <?php if ( has_post_thumbnail() ) : ?>
            <?php the_post_thumbnail( 'arr-card' ); ?>
          <?php else : ?>
            <img src="https://picsum.photos/seed/<?php echo esc_attr( get_the_ID() ); ?>/200/160" alt="" />
          <?php endif; ?>
          <div>
            <?php $cats = get_the_category(); if ( $cats ) : ?>
              <span class="cat"><?php echo esc_html( $cats[0]->name ); ?></span>
            <?php endif; ?>
            <h5><?php the_title(); ?></h5>
            <span class="by">By <?php the_author(); ?> · <?php echo esc_html( arr_reading_time() ); ?> min read</span>
          </div>
        </a>
      <?php endwhile; wp_reset_postdata(); else : ?>
        <p style="color:rgba(250,250,247,0.5);font-size:13px;"><?php echo esc_html( arr_field( 'hero_empty_text', "Publish your first article and it'll appear here automatically." ) ); ?></p>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php
// Manual cards win when the client fills them in; otherwise the strip keeps
// generating itself from the site's live categories.
$manual_cards = array();
for ( $i = 1; $i <= 6; $i++ ) {
  $title = arr_field( "category_{$i}_title", '' );
  if ( ! $title ) {
    continue;
  }
  $manual_cards[] = array(
    'title' => $title,
    'icon'  => arr_field( "category_{$i}_icon", '' ),
    'desc'  => arr_field( "category_{$i}_description", '' ),
    'link'  => arr_field( "category_{$i}_link", '' ),
  );
}
$cat_icon_svg = '<svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="7" y="7" width="10" height="10" rx="1"/><line x1="12" y1="1" x2="12" y2="4"/><line x1="12" y1="20" x2="12" y2="23"/><line x1="1" y1="12" x2="4" y2="12"/><line x1="20" y1="12" x2="23" y2="12"/></svg>';
?>
<section class="cat-strip">
  <?php if ( $manual_cards ) : ?>
  <div class="wrap cat-grid" style="--cat-cols:<?php echo count( $manual_cards ); ?>;">
    <?php foreach ( $manual_cards as $card ) : ?>
      <div class="cat-item">
        <div class="cat-head">
          <?php if ( $card['icon'] ) : ?>
            <img class="ic" src="<?php echo esc_url( $card['icon'] ); ?>" alt="" />
          <?php else : echo $cat_icon_svg; endif; ?>
          <h4>
            <?php if ( $card['link'] ) : ?>
              <a href="<?php echo esc_url( $card['link'] ); ?>" style="color:inherit;"><?php echo esc_html( $card['title'] ); ?></a>
            <?php else : ?>
              <?php echo esc_html( $card['title'] ); ?>
            <?php endif; ?>
          </h4>
        </div>
        <p><?php echo esc_html( $card['desc'] ? $card['desc'] : 'Analysis and commentary in this category.' ); ?></p>
      </div>
    <?php endforeach; ?>
  </div>
  <?php else : ?>
  <div class="wrap cat-grid">
    <?php
    $cats = get_categories( array( 'number' => 6, 'orderby' => 'name' ) );
    foreach ( $cats as $cat ) :
    ?>
      <div class="cat-item">
        <div class="cat-head">
          <?php echo $cat_icon_svg; ?>
          <h4><a href="<?php echo esc_url( get_category_link( $cat ) ); ?>" style="color:inherit;"><?php echo esc_html( $cat->name ); ?></a></h4>
        </div>
        <p><?php echo esc_html( $cat->description ? $cat->description : 'Analysis and commentary in this category.' ); ?></p>
        <span class="count"><?php echo intval( $cat->count ); ?> Articles</span>
      </div>
    <?php endforeach; ?>
    <?php if ( empty( $cats ) ) : ?>
      <p style="color:var(--muted);"><?php echo esc_html( arr_field( 'cat_strip_empty_text', 'Categories will appear here once created under Posts → Categories. The theme pre-creates the 7 editorial pillars on activation.' ) ); ?></p>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</section>

<?php
$trend_eye_svg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/><circle cx="12" cy="12" r="3"/></svg>';
?>
<section>
  <div class="trio">
    <div>
      <div class="section-head">
        <h2><?php echo esc_html( arr_field( 'authors_heading', 'Featured Authors' ) ); ?></h2>
        <a href="<?php echo esc_url( arr_field( 'authors_view_all_link', home_url( '/authors/' ) ) ); ?>" class="view-all"><?php echo esc_html( arr_field( 'authors_view_all_text', 'View All' ) ); ?> &rarr;</a>
      </div>
      <?php
      $authors = get_users( array( 'capability' => array( 'edit_posts' ), 'has_published_posts' => array( 'post' ) ) );
      if ( $authors ) :
        $author_slides = array_chunk( $authors, 4 );
      ?>
        <div class="author-slider" data-slider>
          <div class="slider-track">
            <?php foreach ( $author_slides as $slide ) : ?>
              <div class="slider-slide author-grid">
                <?php foreach ( $slide as $author ) : ?>
                  <div class="author">
                    <img src="https://picsum.photos/seed/author<?php echo esc_attr( $author->ID ); ?>/300/300" alt="" />
                    <h5><?php echo esc_html( $author->display_name ); ?></h5>
                    <p><?php echo esc_html( get_the_author_meta( 'description', $author->ID ) ?: 'Contributor' ); ?></p>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endforeach; ?>
          </div>
          <div class="slider-dots"></div>
        </div>
      <?php else : ?>
        <p style="color:var(--muted);font-size:13px;"><?php echo esc_html( arr_field( 'authors_empty_text', "Author cards appear here once posts are published — set each author's bio under Users → Edit Profile." ) ); ?></p>
      <?php endif; ?>
    </div>

    <?php
    $longread = new WP_Query( array( 'posts_per_page' => 5, 'offset' => 3, 'ignore_sticky_posts' => true ) );
    ?>
    <div class="longread">
      <div class="section-head">
        <h2><?php echo esc_html( arr_field( 'longread_heading', 'Long Reads' ) ); ?></h2>
        <a href="<?php echo esc_url( arr_field( 'longread_view_all_link', home_url( '/articles/' ) ) ); ?>" class="view-all"><?php echo esc_html( arr_field( 'longread_view_all_text', 'View All' ) ); ?> &rarr;</a>
      </div>
      <?php if ( $longread->have_posts() ) : ?>
        <div class="longread-slider" data-slider>
          <div class="slider-track">
            <?php while ( $longread->have_posts() ) : $longread->the_post(); ?>
              <div class="slider-slide longread-slide">
                <a href="<?php the_permalink(); ?>" class="longread-media">
                  <?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'arr-card' ); else : ?>
                    <img src="https://picsum.photos/seed/<?php echo esc_attr( get_the_ID() ); ?>/500/400" alt="" />
                  <?php endif; ?>
                </a>
                <div class="longread-body">
                  <a href="<?php the_permalink(); ?>"><h3><?php the_title(); ?></h3></a>
                  <p style="font-size:14px;color:var(--muted);">By <?php the_author(); ?></p>
                  <p class="meta"><?php echo esc_html( arr_reading_time() ); ?> min read</p>
                </div>
              </div>
            <?php endwhile; wp_reset_postdata(); ?>
          </div>
          <div class="slider-dots"></div>
        </div>
      <?php else : ?>
        <p style="color:var(--muted);font-size:13px;"><?php echo esc_html( arr_field( 'longread_empty_text', 'Your published articles will surface here as the Long Read once there are enough of them.' ) ); ?></p>
      <?php endif; ?>
    </div>

    <?php
    $trending = new WP_Query( array( 'posts_per_page' => 3, 'orderby' => 'comment_count', 'order' => 'DESC', 'ignore_sticky_posts' => true ) );
    ?>
    <div>
      <div class="section-head"><h2><?php echo esc_html( arr_field( 'trending_heading', 'Trending Ideas' ) ); ?></h2></div>
      <div class="trend-list">
        <?php if ( $trending->have_posts() ) :
          $n = 1;
          while ( $trending->have_posts() ) : $trending->the_post();
        ?>
          <a href="<?php the_permalink(); ?>" class="trend-item">
            <span class="trend-num"><?php echo str_pad( $n, 2, '0', STR_PAD_LEFT ); ?></span>
            <div class="trend-text"><h5><?php the_title(); ?></h5><span class="by">By <?php the_author(); ?></span></div>
            <span class="trend-views"><?php echo $trend_eye_svg; ?><?php echo esc_html( arr_placeholder_view_count( get_the_ID() ) ); ?></span>
          </a>
        <?php $n++; endwhile; wp_reset_postdata(); ?>
        <a href="<?php echo esc_url( arr_field( 'trending_view_all_link', home_url( '/articles/' ) ) ); ?>" class="view-all trend-view-all"><?php echo esc_html( arr_field( 'trending_view_all_text', 'View All' ) ); ?> &rarr;</a>
        <?php else : ?>
          <p style="color:var(--muted);font-size:13px;"><?php echo esc_html( arr_field( 'trending_empty_text', 'Ranked automatically by reader comments once articles are live.' ) ); ?></p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php
$podcast_image = arr_field( 'podcast_image', '' );
$reports_image = arr_field( 'reports_image', '' );
?>
<section class="cta-band">
  <div class="cta-grid">
    <div class="cta-item<?php echo $podcast_image ? ' has-art' : ''; ?>">
      <div class="cta-copy">
        <h4><?php echo esc_html( arr_field( 'podcast_title', 'Listen to Our Podcast' ) ); ?></h4>
        <p><?php echo esc_html( arr_field( 'podcast_text', "Conversations with Africa's thinkers, leaders, and builders." ) ); ?></p>
        <a href="<?php echo esc_url( arr_field( 'podcast_link', '#' ) ); ?>" class="cta-link"><?php echo esc_html( arr_field( 'podcast_button_text', 'Listen Now' ) ); ?> <span aria-hidden="true">&rarr;</span></a>
      </div>
      <?php if ( $podcast_image ) : ?>
        <img class="cta-art" src="<?php echo esc_url( $podcast_image ); ?>" alt="" />
      <?php endif; ?>
    </div>

    <div class="cta-item">
      <div class="cta-copy">
        <h4><?php echo esc_html( arr_field( 'brief_title', 'Weekly Brief' ) ); ?></h4>
        <p><?php echo esc_html( arr_field( 'brief_text', 'A curated briefing of ideas that matter. Every Saturday.' ) ); ?></p>
        <div class="sub-form">
          <?php echo do_shortcode( '[mailpoet_form id="1"]' ); ?>
        </div>
      </div>
    </div>

    <div class="cta-item<?php echo $reports_image ? ' has-art' : ''; ?>">
      <div class="cta-copy">
        <h4><?php echo esc_html( arr_field( 'reports_title', 'Special Reports' ) ); ?></h4>
        <p><?php echo esc_html( arr_field( 'reports_text', "In-depth reports on Africa's most critical issues." ) ); ?></p>
        <a href="<?php echo esc_url( arr_field( 'reports_link', '#' ) ); ?>" class="cta-link"><?php echo esc_html( arr_field( 'reports_button_text', 'Explore Reports' ) ); ?> <span aria-hidden="true">&rarr;</span></a>
      </div>
      <?php if ( $reports_image ) : ?>
        <img class="cta-art" src="<?php echo esc_url( $reports_image ); ?>" alt="" />
      <?php endif; ?>
    </div>
  </div>
</section>

<?php get_footer(); ?>
