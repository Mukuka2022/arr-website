<?php
/**
 * Comments template.
 *
 * Without this file WordPress falls back to wp-includes/theme-compat/comments.php,
 * which ships unstyled browser-default markup and looks nothing like the rest of
 * the site. Everything here is standard core output so plugins (Akismet, comment
 * moderation, threading) keep working — the design lives in pages.css.
 */

// Don't leak comments on a password-protected post the visitor hasn't unlocked.
if ( post_password_required() ) {
	return;
}

$arr_comment_count = get_comments_number();
?>

<section id="comments" class="comments-area">

	<?php if ( have_comments() ) : ?>
		<div class="comments-head">
			<h2 class="comments-title">
				<?php
				printf(
					/* translators: %s: comment count. */
					esc_html( _n( '%s Response', '%s Responses', $arr_comment_count, 'arr-theme' ) ),
					esc_html( number_format_i18n( $arr_comment_count ) )
				);
				?>
			</h2>
		</div>

		<ol class="comment-list">
			<?php
			wp_list_comments( array(
				'style'       => 'ol',
				'short_ping'  => true,
				'avatar_size' => 52,
			) );
			?>
		</ol>

		<?php
		the_comments_pagination( array(
			'prev_text' => esc_html__( '← Newer', 'arr-theme' ),
			'next_text' => esc_html__( 'Older →', 'arr-theme' ),
		) );
		?>

		<?php if ( ! comments_open() ) : ?>
			<p class="comments-closed"><?php esc_html_e( 'Comments are closed on this article.', 'arr-theme' ); ?></p>
		<?php endif; ?>
	<?php endif; ?>

	<?php
	$arr_req   = get_option( 'require_name_email' );
	$arr_aria  = $arr_req ? " required aria-required='true'" : '';
	$arr_star  = $arr_req ? ' <span class="required">*</span>' : '';

	comment_form( array(
		'title_reply'          => esc_html__( 'Join the conversation', 'arr-theme' ),
		/* translators: %s: name of the comment being replied to. */
		'title_reply_to'       => esc_html__( 'Reply to %s', 'arr-theme' ),
		'cancel_reply_link'    => esc_html__( 'Cancel reply', 'arr-theme' ),
		'label_submit'         => esc_html__( 'Post Comment', 'arr-theme' ),
		'class_submit'         => 'btn btn-primary comment-submit',
		'submit_button'        => '<button name="%1$s" type="submit" id="%2$s" class="%3$s">%4$s</button>',
		'title_reply_before'   => '<h3 id="reply-title" class="comment-reply-title">',
		'title_reply_after'    => '</h3>',
		'comment_notes_before' => '<p class="comment-notes">' . esc_html__( 'Your email address is never published. We moderate every comment before it appears.', 'arr-theme' ) . '</p>',
		'comment_notes_after'  => '',
		'comment_field'        => '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'Comment', 'arr-theme' ) . ' <span class="required">*</span></label><textarea id="comment" name="comment" rows="7" required placeholder="' . esc_attr__( 'Share your perspective…', 'arr-theme' ) . '"></textarea></p>',
		'fields'               => array(
			'author' => '<div class="comment-form-row">'
				. '<p class="comment-form-author"><label for="author">' . esc_html__( 'Name', 'arr-theme' ) . $arr_star . '</label>'
				. '<input id="author" name="author" type="text" value="' . esc_attr( wp_get_current_commenter()['comment_author'] ) . '" maxlength="245"' . $arr_aria . ' /></p>',
			'email'  => '<p class="comment-form-email"><label for="email">' . esc_html__( 'Email', 'arr-theme' ) . $arr_star . '</label>'
				. '<input id="email" name="email" type="email" value="' . esc_attr( wp_get_current_commenter()['comment_author_email'] ) . '" maxlength="100"' . $arr_aria . ' /></p>'
				. '</div>',
			'url'    => '<p class="comment-form-url"><label for="url">' . esc_html__( 'Website', 'arr-theme' ) . '</label>'
				. '<input id="url" name="url" type="url" value="' . esc_attr( wp_get_current_commenter()['comment_author_url'] ) . '" maxlength="200" placeholder="' . esc_attr__( 'Optional', 'arr-theme' ) . '" /></p>',
		),
	) );
	?>

	<?php if ( ! comments_open() && ! have_comments() ) : ?>
		<p class="comments-closed"><?php esc_html_e( 'Comments are closed on this article.', 'arr-theme' ); ?></p>
	<?php endif; ?>

</section>
