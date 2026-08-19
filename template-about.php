<?php
/**
 * Template Name: About Page
 *
 * Select this under Page Attributes → Template when creating your About page.
 *
 * Text on this page comes from WordPress's built-in Custom Fields (no
 * plugin) when set, and falls back to the exact approved prototype copy
 * otherwise — so the design never breaks even if a field is left empty.
 * To add/edit a field: open this page in wp-admin, enable Screen Options
 * → Custom Fields, and add a Name/Value pair using the meta keys below.
 */
get_header();

$post_id = get_the_ID();

$vision_text = function_exists( 'get_field' ) ? get_field( 'vision_text' ) : '';
$vision_note = function_exists( 'get_field' ) ? get_field( 'vision_note' ) : '';
$mission_text = function_exists( 'get_field' ) ? get_field( 'mission_text' ) : '';
$mission_note = function_exists( 'get_field' ) ? get_field( 'mission_note' ) : '';

if ( ! $vision_text ) $vision_text = "To become Africa's most trusted independent institution for ideas, scholarship, and public discourse — advancing an intellectual renaissance that shapes ethical leadership, resilient institutions, technological innovation, and sustainable development.";
if ( ! $vision_note ) $vision_note = 'Trust is more valuable than popularity. Influence is more enduring than visibility.';
if ( ! $mission_text ) $mission_text = "To cultivate rigorous analysis, independent thought, and evidence-based dialogue that strengthens Africa's intellectual life, informs public policy, and equips society to address the defining challenges of the 21st century.";
if ( ! $mission_note ) $mission_note = 'Every article should educate. Every publication should illuminate. Every debate should elevate.';

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
    <span class="eyebrow">About ARR</span>
    <h1>An institution built on the conviction that ideas transform civilizations</h1>
    <p>Ideas. Evidence. Wisdom. Africa. — the four words that discipline everything we publish.</p>
  </div>
</div>

<section>
  <div class="wrap intro">
    <div><?php echo $intro_content; ?></div>
    <img src="https://picsum.photos/seed/arraboutteam/700/560" alt="" />
  </div>
</section>

<section class="vm-band">
  <div class="wrap vm-grid">
    <div>
      <h4>Our Vision</h4>
      <p class="big"><?php echo esc_html( $vision_text ); ?></p>
      <p class="note"><?php echo esc_html( $vision_note ); ?></p>
    </div>
    <div>
      <h4>Our Mission</h4>
      <p class="big"><?php echo esc_html( $mission_text ); ?></p>
      <p class="note"><?php echo esc_html( $mission_note ); ?></p>
    </div>
  </div>
</section>

<section>
  <div class="wrap">
    <div class="section-head"><h2>The ARR Story</h2></div>
    <div class="story-steps">
      <div class="story-step">
        <span class="n">01</span>
        <div><h4>A Crisis of Ideas</h4><p>Africa's public discourse is too often dominated by immediate events rather than enduring principles — news rewards speed over reflection, opinion over evidence.</p></div>
      </div>
      <div class="story-step">
        <span class="n">02</span>
        <div><h4>A Historic Response</h4><p>ARR was founded in direct response to this challenge: to cultivate thoughtful analysis and evidence-based dialogue rooted in African realities.</p></div>
      </div>
      <div class="story-step">
        <span class="n">03</span>
        <div><h4>A Different Kind of Publication</h4><p>Not a daily wire service, but a home for depth — long-form essays, research translation, and ideas built to outlast the headlines that inspired them.</p></div>
      </div>
    </div>
  </div>
</section>

<section style="background:#fff; border-top:1px solid var(--hairline); border-bottom:1px solid var(--hairline);">
  <div class="wrap">
    <div class="section-head"><h2>Editorial Pillars</h2><span style="font-size:14px;color:var(--muted);">Seven pillars define the intellectual identity of ARR</span></div>
    <div class="pillars-grid">
      <div class="pillar-card"><span class="num">01</span><h4>Governance, Leadership &amp; Public Institutions</h4></div>
      <div class="pillar-card"><span class="num">02</span><h4>Technology, Cybersecurity &amp; Digital Transformation</h4></div>
      <div class="pillar-card"><span class="num">03</span><h4>Economics, Enterprise &amp; Sustainable Development</h4></div>
      <div class="pillar-card"><span class="num">04</span><h4>Faith, Ethics &amp; Society</h4></div>
      <div class="pillar-card"><span class="num">05</span><h4>Science, Education &amp; Knowledge</h4></div>
      <div class="pillar-card"><span class="num">06</span><h4>Africa and the World</h4></div>
      <div class="pillar-card"><span class="num">07</span><h4>History, Culture &amp; Civilisation</h4></div>
    </div>
  </div>
</section>

<section>
  <div class="wrap">
    <div class="section-head"><h2>What We Stand For</h2><span style="font-size:14px;color:var(--muted);">Our Core Values</span></div>
    <div class="values-grid">
      <div class="value-item"><h4>Truth</h4><p>The foundation of public trust, pursued with disciplined research and honesty.</p></div>
      <div class="value-item"><h4>Independence</h4><p>Editorial judgment free from partisan, commercial, or ideological influence.</p></div>
      <div class="value-item"><h4>Excellence</h4><p>Continuous learning and work that meets the highest international standards.</p></div>
      <div class="value-item"><h4>Integrity</h4><p>Consistency between principle and practice across every process.</p></div>
      <div class="value-item"><h4>Intellectual Humility</h4><p>Openness to dialogue, recognising that reasonable people may disagree.</p></div>
      <div class="value-item"><h4>Courage</h4><p>Publishing what is true and necessary, even when it is uncomfortable.</p></div>
    </div>
  </div>
</section>

<section style="background:#fff; border-top:1px solid var(--hairline);">
  <div class="wrap">
    <div class="section-head"><h2>Featured Authors</h2><a href="<?php echo esc_url( home_url( '/articles/' ) ); ?>" class="view-all">View All Articles &rarr;</a></div>
    <div class="team-grid">
      <?php
      $authors = get_users( array( 'who' => 'authors', 'number' => 8, 'has_published_posts' => array( 'post' ) ) );
      foreach ( $authors as $author ) :
      ?>
        <div class="team-card">
          <?php echo get_avatar( $author->ID, 300 ); ?>
          <h5><?php echo esc_html( $author->display_name ); ?></h5>
          <p><?php echo esc_html( get_the_author_meta( 'description', $author->ID ) ?: 'Contributor' ); ?></p>
        </div>
      <?php endforeach; ?>
      <?php if ( empty( $authors ) ) : ?>
        <p style="color:var(--muted);font-size:13px;">Authors appear here automatically once they've published at least one article.</p>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php get_footer(); ?>
