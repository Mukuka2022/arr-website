<?php
/**
 * Template Name: Editorial Standards
 *
 * The public summary of ARR's editorial governance: independence, copyright,
 * AI use and complaints. Reached from ABOUT → Editorial.
 *
 * The four policies are held in a PHP structure rather than as ACF fields.
 * Roughly forty separate fields would be needed to make every bullet editable,
 * which makes the page harder to maintain than the document it summarises —
 * and a governance policy is not copy that gets tuned week to week. Anything
 * typed into the page's own editor is rendered above these, so the client can
 * add to or preface the page without touching code; the full policies are
 * issued by the editorial desk on request, as the page itself says.
 */
get_header();

$policies = array(
	array(
		'number'  => '1',
		'title'   => 'Editorial Governance Framework',
		'lead'    => 'The Framework sets out the principles and authority behind every editorial decision ARR makes in relation to independence, accuracy, and accountability, applied consistently across every pillar we publish.',
		'groups'  => array(
			array(
				'heading' => 'What it commits us to',
				'items'   => array(
					'Editorial judgement exercised independently of governments, political parties, corporations, advertisers, sponsors and donors.',
					'Factual claims that are accurate, verifiable, and appropriately supported, with fact from opinion clearly distinguished.',
					'Recognition of African agency, institutions and knowledge systems, without shielding them from legitimate criticism.',
					'Diversity of thought, without treating all claims as equally supported by evidence.',
					'Correction of significant errors once identified, and transparency about material circumstances readers should know.',
				),
			),
			array(
				'heading' => 'Who decides, and how',
				'items'   => array(
					'Editorial authority rests with ARR\'s designated editorial leadership, who may accept, reject, commission, edit or decline to publish any submission.',
					'No contributor, advertiser or sponsor has an automatic right to publication, and commercial relationships never determine editorial conclusions.',
					'Conflicts of interest — financial, institutional or personal — must be disclosed by editors and contributors alike.',
				),
			),
		),
		'short'   => 'ARR answers to evidence and to its readers, not to funders, advertisers or political interests. Publishing a piece is never an automatic endorsement of every view within it.',
		'note'    => 'The detailed complaints and AI-related provisions of this Framework are set out in full in their own dedicated policies below.',
	),
	array(
		'number'  => '2',
		'title'   => 'Copyright and Intellectual Property Policy',
		'lead'    => 'Contributors keep what they write. ARR asks only for the rights it needs to publish, distribute and archive the work responsibly.',
		'groups'  => array(
			array(
				'heading' => 'Ownership',
				'items'   => array(
					'Unless otherwise agreed in writing, contributors retain copyright in their original work.',
					'Submitting an article grants ARR a non-exclusive licence to publish, reproduce, distribute, archive and promote it; not to own it.',
					'Contributors may generally republish their work elsewhere afterward, with appropriate acknowledgement of ARR as original publisher.',
				),
			),
			array(
				'heading' => 'Standards we hold contributors to',
				'items'   => array(
					'Work must be substantially original, with sources appropriately acknowledged and quotations never fabricated.',
					'Permission must be obtained for third-party photographs, illustrations, data, audio, video or other copyrighted material.',
					'Plagiarism is not accepted; ARR may correct, amend or withdraw material where it is identified, even after publication.',
				),
			),
		),
		'short'   => 'Your words stay yours. We ask for a licence to publish them well; not for ownership of them.',
		'note'    => 'Authorship should reflect genuine intellectual contribution; a name is not added to a byline for seniority, funding or institutional position alone.',
	),
	array(
		'number'  => '3',
		'title'   => 'Artificial Intelligence Use Policy',
		'lead'    => 'AI is a legitimate research and writing aid in the contemporary digital world, so it is at ARR, but it is never a source of authority, and never a substitute for a human being\'s responsibility for what gets published.',
		'groups'  => array(
			array(
				'heading' => "What's permitted",
				'items'   => array(
					'Brainstorming, outlining, language assistance, translation, transcription, research organisation and similar preparatory tasks.',
					'Contributors remain fully responsible for verifying anything AI helps produce — citations, statistics, quotations, names and dates above all.',
					'ARR may require disclosure where AI materially contributed to the substantive text, research, data analysis or published images.',
				),
			),
			array(
				'heading' => "What's never permitted",
				'items'   => array(
					'Using AI to fabricate sources, quotations, interviews, research findings or statistics.',
					'Using AI-generated articles and presenting them as authentic.',
					'Presenting AI-generated or AI-manipulated images, audio or video as authentic documentary evidence.',
					"Letting AI-generated summaries or recommendations determine ARR's editorial position. That judgement stays human.",
				),
			),
		),
		'short'   => 'AI can help a contributor think and draft faster. It cannot be an author, a source, or a decision-maker at ARR.',
		'note'    => 'An AI-generated citation is never assumed genuine; it is verified, every time, before publication.',
	),
	array(
		'number'  => '4',
		'title'   => 'Complaints and Appeals Procedure',
		'lead'    => 'Readers, contributors and anyone discussed in our pages have a real, fair route to raise concerns about what we publish; and to appeal the outcome.',
		'groups'  => array(
			array(
				'heading' => 'How it works',
				'items'   => array(
					'Complaints can be sent to ' . ARR_CONTACT_EMAIL . ', ideally identifying the article, the issue, and the evidence behind it.',
					'ARR assesses each complaint, investigates where warranted, and may give the contributor concerned an opportunity to respond.',
					'Outcomes range from no action, to correction, clarification, an editorial note, revision, or in serious cases result in the removal of the material.',
				),
			),
			array(
				'heading' => 'Appeals and fairness',
				'items'   => array(
					'A decision may be appealed where there is significant new evidence, a material error, a procedural failure, or a conflict of interest.',
					'Wherever practicable, appeals are reviewed by someone not solely responsible for the original decision.',
					'ARR does not retaliate against good-faith complaints, and does not treat mere disagreement with a conclusion as grounds for correction.',
				),
			),
		),
		'short'   => "Disagreeing with an argument isn't a complaint. Evidence of a factual error, plagiarism, fabrication or a breach of our standards is, and we take it seriously.",
		'note'    => "Credibility isn't claiming to be infallible. It's being willing to listen, investigate, and correct genuine mistakes.",
	),
);
?>

<div class="page-banner">
  <div class="wrap">
    <span class="eyebrow"><?php echo esc_html( arr_field( 'editorial_eyebrow', 'How we work' ) ); ?></span>
    <h1><?php echo esc_html( arr_field( 'editorial_title', 'Editorial Standards and Governance' ) ); ?></h1>
    <p><?php echo esc_html( arr_field( 'editorial_subtitle', 'A summary of the policies that govern how The African Renaissance Review edits, publishes, protects and corrects the work it puts into the world.' ) ); ?></p>
  </div>
</div>

<section class="editorial-section">
  <div class="wrap">
    <div class="editorial-intro">
      <p><?php echo esc_html( arr_field( 'editorial_intro', "The African Renaissance Review is built on the belief that Africa's renaissance depends on rigorous, independent, well-governed thought. The summaries below set out, in plain terms, how we handle editorial independence, copyright, artificial intelligence, and complaints. They are a guide to our practice, not a substitute for the full policy documents, which are available from the editorial team on request." ) ); ?></p>
      <p class="editorial-contact">
        <?php esc_html_e( 'Questions about any of these policies can be sent to', 'arr-theme' ); ?>
        <a href="mailto:<?php echo esc_attr( ARR_CONTACT_EMAIL ); ?>"><?php echo esc_html( ARR_CONTACT_EMAIL ); ?></a>
      </p>
    </div>

    <?php while ( have_posts() ) : the_post(); ?>
      <?php if ( trim( get_the_content() ) ) : ?>
        <div class="editorial-custom prose" style="max-width:none;padding:0;"><?php the_content(); ?></div>
      <?php endif; ?>
    <?php endwhile; ?>

    <?php foreach ( $policies as $policy ) : ?>
      <article class="policy">
        <header class="policy-head">
          <span class="policy-number"><?php echo esc_html( $policy['number'] ); ?></span>
          <h2><?php echo esc_html( $policy['title'] ); ?></h2>
        </header>
        <p class="policy-lead"><?php echo esc_html( $policy['lead'] ); ?></p>

        <div class="policy-groups">
          <?php foreach ( $policy['groups'] as $group ) : ?>
            <div class="policy-group">
              <h3><?php echo esc_html( $group['heading'] ); ?></h3>
              <ul>
                <?php foreach ( $group['items'] as $item ) : ?>
                  <li><?php echo esc_html( $item ); ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="policy-short">
          <span class="policy-short-label"><?php esc_html_e( 'In short', 'arr-theme' ); ?></span>
          <p><?php echo esc_html( $policy['short'] ); ?></p>
        </div>

        <?php if ( $policy['note'] ) : ?>
          <p class="policy-note"><?php echo esc_html( $policy['note'] ); ?></p>
        <?php endif; ?>

        <p class="policy-full">
          <?php
          printf(
            /* translators: 1: policy name, 2: email address link. */
            esc_html__( 'The full %1$s is available from %2$s.', 'arr-theme' ),
            esc_html( $policy['title'] ),
            '<a href="mailto:' . esc_attr( ARR_CONTACT_EMAIL ) . '">' . esc_html( ARR_CONTACT_EMAIL ) . '</a>'
          );
          ?>
        </p>
      </article>
    <?php endforeach; ?>

    <div class="editorial-sign-off">
      <strong><?php esc_html_e( 'The African Renaissance Review', 'arr-theme' ); ?></strong>
      <span><?php esc_html_e( 'Ideas. Evidence. Wisdom. Africa.', 'arr-theme' ); ?></span>
      <span><?php esc_html_e( "Shaping Africa's Intellectual Renaissance.", 'arr-theme' ); ?></span>
    </div>
  </div>
</section>

<?php get_footer(); ?>
