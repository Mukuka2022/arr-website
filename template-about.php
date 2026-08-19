<?php
/**
 * Template Name: About Page
 *
 * Select this under Page Attributes → Template when creating your About page.
 *
 * Every text block is editable in the "About Page Content" field group and
 * falls back to the exact approved prototype copy when left empty, so the
 * design never breaks.
 */
get_header();

$vision_text = arr_field( 'vision_text', "To become Africa's most trusted independent institution for ideas, scholarship, and public discourse — advancing an intellectual renaissance that shapes ethical leadership, resilient institutions, technological innovation, and sustainable development." );
$vision_note = arr_field( 'vision_note', 'Trust is more valuable than popularity. Influence is more enduring than visibility.' );
$mission_text = arr_field( 'mission_text', "To cultivate rigorous analysis, independent thought, and evidence-based dialogue that strengthens Africa's intellectual life, informs public policy, and equips society to address the defining challenges of the 21st century." );
$mission_note = arr_field( 'mission_note', 'Every article should educate. Every publication should illuminate. Every debate should elevate.' );

// Intro text — editable via this page's normal content editor. Falls back
// to the approved copy if the page content is empty.
ob_start();
if ( have_posts() ) : while ( have_posts() ) : the_post();
	the_content();
endwhile; endif;
$intro_content = trim( ob_get_clean() );

if ( ! $intro_content ) {
	$intro_content = '<span class="eyebrow">Who We Are</span>'
		. '<h2 style="margin-top:10px;">Not a daily news outlet — an institution for Africa\'s intellectual renewal</h2>'
		. '<p>The African Renaissance Review (ARR) is founded on the conviction that ideas remain among humanity\'s most powerful instruments of transformation. We exist to cultivate thoughtful analysis, encourage evidence-based debate, and promote solutions rooted in African realities while engaging confidently with global perspectives.</p>'
		. '<p>ARR does not aspire merely to inform; it seeks to illuminate — fostering conversations that outlast the news cycle and shape the institutions Africa builds next.</p>'
		. '<p class="pull-quote">"Ideas build institutions. Institutions shape nations. Nations transform civilizations."</p>';
}
?>

<div class="page-banner">
  <div class="wrap">
    <span class="eyebrow"><?php echo esc_html( arr_field( 'about_eyebrow', 'About ARR' ) ); ?></span>
    <h1><?php echo esc_html( arr_field( 'about_title', 'An institution built on the conviction that ideas transform civilizations' ) ); ?></h1>
    <p><?php echo esc_html( arr_field( 'about_subtitle', 'Ideas. Evidence. Wisdom. Africa. — the four words that discipline everything we publish.' ) ); ?></p>
  </div>
</div>

<section class="about-intro">
  <div class="wrap intro">
    <div><?php echo $intro_content; ?></div>
    <img src="<?php echo esc_url( arr_field( 'about_intro_image', 'https://picsum.photos/seed/arraboutteam/700/560' ) ); ?>" alt="" />
  </div>
</section>

<section class="vm-band">
  <div class="wrap vm-grid">
    <div>
      <h4><?php echo esc_html( arr_field( 'vision_heading', 'Our Vision' ) ); ?></h4>
      <p class="big"><?php echo esc_html( $vision_text ); ?></p>
      <p class="note"><?php echo esc_html( $vision_note ); ?></p>
    </div>
    <div>
      <h4><?php echo esc_html( arr_field( 'mission_heading', 'Our Mission' ) ); ?></h4>
      <p class="big"><?php echo esc_html( $mission_text ); ?></p>
      <p class="note"><?php echo esc_html( $mission_note ); ?></p>
    </div>
  </div>
</section>

<?php
$story_defaults = array(
  array( 'A Crisis of Ideas', "Africa's public discourse is too often dominated by immediate events rather than enduring principles — news rewards speed over reflection, opinion over evidence." ),
  array( 'A Historic Response', 'ARR was founded in direct response to this challenge: to cultivate thoughtful analysis and evidence-based dialogue rooted in African realities.' ),
  array( 'A Different Kind of Publication', 'Not a daily wire service, but a home for depth — long-form essays, research translation, and ideas built to outlast the headlines that inspired them.' ),
);
$pillar_defaults = array(
  'Governance, Leadership &amp; Public Institutions',
  'Technology, Cybersecurity &amp; Digital Transformation',
  'Economics, Enterprise &amp; Sustainable Development',
  'Faith, Ethics &amp; Society',
  'Science, Education &amp; Knowledge',
  'Africa and the World',
  'History, Culture &amp; Civilisation',
);
$value_defaults = array(
  array( 'Truth', 'The foundation of public trust, pursued with disciplined research and honesty.' ),
  array( 'Independence', 'Editorial judgment free from partisan, commercial, or ideological influence.' ),
  array( 'Excellence', 'Continuous learning and work that meets the highest international standards.' ),
  array( 'Integrity', 'Consistency between principle and practice across every process.' ),
  array( 'Intellectual Humility', 'Openness to dialogue, recognising that reasonable people may disagree.' ),
  array( 'Courage', 'Publishing what is true and necessary, even when it is uncomfortable.' ),
);
?>

<section class="about-story">
  <div class="wrap">
    <div class="section-head"><h2><?php echo esc_html( arr_field( 'story_heading', 'The ARR Story' ) ); ?></h2></div>
    <div class="story-steps">
      <?php foreach ( $story_defaults as $i => $default ) :
        $n     = $i + 1;
        $title = arr_field( "story_{$n}_title", $default[0] );
        $text  = arr_field( "story_{$n}_text", $default[1] );
        if ( ! $title && ! $text ) continue;
      ?>
        <div class="story-step">
          <span class="n"><?php echo esc_html( arr_field( "story_{$n}_number", str_pad( $n, 2, '0', STR_PAD_LEFT ) ) ); ?></span>
          <div><h4><?php echo esc_html( $title ); ?></h4><p><?php echo esc_html( $text ); ?></p></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="about-pillars" style="border-bottom:1px solid var(--hairline);">
  <div class="wrap">
    <div class="section-head"><h2><?php echo esc_html( arr_field( 'pillars_heading', 'Editorial Pillars' ) ); ?></h2><span style="font-size:14px;color:var(--muted);"><?php echo esc_html( arr_field( 'pillars_sub', 'Seven pillars define the intellectual identity of ARR' ) ); ?></span></div>
    <div class="pillars-grid">
      <?php foreach ( $pillar_defaults as $i => $default ) :
        $n     = $i + 1;
        $title = arr_field( "pillar_{$n}_title", html_entity_decode( $default ) );
        if ( ! $title ) continue;
      ?>
        <div class="pillar-card"><span class="num"><?php echo esc_html( arr_field( "pillar_{$n}_number", str_pad( $n, 2, '0', STR_PAD_LEFT ) ) ); ?></span><h4><?php echo esc_html( $title ); ?></h4></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="about-values">
  <div class="wrap">
    <div class="section-head"><h2><?php echo esc_html( arr_field( 'values_heading', 'What We Stand For' ) ); ?></h2><span style="font-size:14px;color:var(--muted);"><?php echo esc_html( arr_field( 'values_sub', 'Our Core Values' ) ); ?></span></div>
    <div class="values-grid">
      <?php foreach ( $value_defaults as $i => $default ) :
        $n     = $i + 1;
        $title = arr_field( "value_{$n}_title", $default[0] );
        $text  = arr_field( "value_{$n}_text", $default[1] );
        if ( ! $title && ! $text ) continue;
      ?>
        <div class="value-item"><h4><?php echo esc_html( $title ); ?></h4><p><?php echo esc_html( $text ); ?></p></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="about-team">
  <div class="wrap">
    <div class="section-head"><h2><?php echo esc_html( arr_field( 'team_heading', 'Featured Authors' ) ); ?></h2><a href="<?php echo esc_url( arr_field( 'team_view_all_link', home_url( '/articles/' ) ) ); ?>" class="view-all"><?php echo esc_html( arr_field( 'team_view_all_text', 'View All Articles' ) ); ?> &rarr;</a></div>
    <div class="team-grid">
      <?php
      $authors = get_users( array( 'capability' => array( 'edit_posts' ), 'number' => 8, 'has_published_posts' => array( 'post' ) ) );
      foreach ( $authors as $author ) :
      ?>
        <div class="team-card">
          <?php echo get_avatar( $author->ID, 300 ); ?>
          <h5><?php echo esc_html( $author->display_name ); ?></h5>
          <p><?php echo esc_html( get_the_author_meta( 'description', $author->ID ) ?: 'Contributor' ); ?></p>
        </div>
      <?php endforeach; ?>
      <?php if ( empty( $authors ) ) : ?>
        <p style="color:var(--muted);font-size:13px;"><?php echo esc_html( arr_field( 'team_empty_text', "Authors appear here automatically once they've published at least one article." ) ); ?></p>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php get_footer(); ?>
