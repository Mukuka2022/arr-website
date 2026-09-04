<?php
/**
 * Routes everything WordPress sends through an external SMTP relay.
 *
 * Why not an SMTP plugin: the credentials would live in wp_options, which
 * means they travel inside every database export. This site's database has
 * already been carried between hosts once as a .sql file, and a sending key
 * sitting in that file is a key that has been emailed, copied to a laptop and
 * left in a downloads folder. Constants in wp-config.php stay on the server,
 * out of the database and out of backups — and cost no plugin overhead on a
 * site where page weight has been a concern.
 *
 * Defining nothing leaves this dormant: WordPress keeps using the host's own
 * mail, so a missing constant degrades to the previous behaviour rather than
 * to silence.
 *
 * Expected in wp-config.php:
 *
 *     define( 'ARR_SMTP_HOST', 'smtp-relay.brevo.com' );
 *     define( 'ARR_SMTP_PORT', 587 );
 *     define( 'ARR_SMTP_USER', '…@smtp-brevo.com' );
 *     define( 'ARR_SMTP_PASS', '…' );   // SMTP key, not the API key
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Whether SMTP credentials have been supplied.
 */
function arr_smtp_configured() {
	return defined( 'ARR_SMTP_HOST' ) && ARR_SMTP_HOST
		&& defined( 'ARR_SMTP_USER' ) && ARR_SMTP_USER
		&& defined( 'ARR_SMTP_PASS' ) && ARR_SMTP_PASS;
}

/**
 * Hand PHPMailer the relay settings.
 */
function arr_configure_smtp( $phpmailer ) {
	if ( ! arr_smtp_configured() ) {
		return;
	}

	$port = defined( 'ARR_SMTP_PORT' ) ? (int) ARR_SMTP_PORT : 587;

	$phpmailer->isSMTP();
	$phpmailer->Host       = ARR_SMTP_HOST;
	$phpmailer->Port       = $port;
	$phpmailer->SMTPAuth   = true;
	$phpmailer->Username   = ARR_SMTP_USER;
	$phpmailer->Password   = ARR_SMTP_PASS;

	// 465 is the implicit-TLS port and must open encrypted; 587 and 2525 start
	// in the clear and upgrade with STARTTLS. Choosing the wrong one produces a
	// connection that hangs rather than an error that explains itself.
	$phpmailer->SMTPSecure = ( 465 === $port ) ? 'ssl' : 'tls';

	$phpmailer->SMTPAutoTLS = true;
}
add_action( 'phpmailer_init', 'arr_configure_smtp' );

/**
 * Record why a send failed.
 *
 * wp_mail() returns a bare false, so without this a delivery problem shows up
 * as "the form did not work" with nothing to go on. Kept to the last failure
 * only — this is a breadcrumb for the test screen, not a mail log.
 */
function arr_record_mail_failure( $error ) {
	update_option( 'arr_last_mail_error', array(
		'time'    => time(),
		'message' => $error->get_error_message(),
	), false );
}
add_action( 'wp_mail_failed', 'arr_record_mail_failure' );

/**
 * Tools → Email Delivery: shows the active configuration and sends a test.
 *
 * Deliverability problems are found by sending real mail and reading what
 * comes back, and the client cannot do that from a wp-config file. This is
 * the smallest thing that makes the setup verifiable by the person who owns
 * the site.
 */
function arr_register_mail_test_page() {
	add_management_page(
		__( 'Email Delivery', 'arr-theme' ),
		__( 'Email Delivery', 'arr-theme' ),
		'manage_options',
		'arr-email-delivery',
		'arr_render_mail_test_page'
	);
}
add_action( 'admin_menu', 'arr_register_mail_test_page' );

function arr_render_mail_test_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to view this page.', 'arr-theme' ) );
	}

	$result = null;

	if ( isset( $_POST['arr_mail_test_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['arr_mail_test_nonce'] ), 'arr_mail_test' ) ) {
		$to = sanitize_email( wp_unslash( $_POST['arr_test_to'] ?? '' ) );

		if ( ! is_email( $to ) ) {
			$result = array( 'error', __( 'That does not look like an email address.', 'arr-theme' ) );
		} else {
			delete_option( 'arr_last_mail_error' );

			$sent = wp_mail(
				$to,
				/* translators: %s: site name. */
				sprintf( __( 'Test email from %s', 'arr-theme' ), get_bloginfo( 'name' ) ),
				__( "If you are reading this, the site can send email.\n\nSent from the Email Delivery screen in WordPress.", 'arr-theme' )
			);

			if ( $sent ) {
				$result = array( 'success', sprintf(
					/* translators: %s: recipient address. */
					__( 'Sent to %s. Check the inbox — and the spam folder, which is itself useful to know.', 'arr-theme' ),
					$to
				) );
			} else {
				$failure = get_option( 'arr_last_mail_error' );
				$result  = array( 'error', __( 'The send failed.', 'arr-theme' ) . ( $failure ? ' ' . $failure['message'] : '' ) );
			}
		}
	}

	$configured = arr_smtp_configured();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Email Delivery', 'arr-theme' ); ?></h1>

		<?php if ( $result ) : ?>
			<div class="notice notice-<?php echo esc_attr( $result[0] ); ?>"><p><?php echo esc_html( $result[1] ); ?></p></div>
		<?php endif; ?>

		<h2><?php esc_html_e( 'Current settings', 'arr-theme' ); ?></h2>
		<table class="widefat striped" style="max-width:640px">
			<tbody>
				<tr>
					<th style="width:180px"><?php esc_html_e( 'Sending method', 'arr-theme' ); ?></th>
					<td>
						<?php if ( $configured ) : ?>
							<strong><?php esc_html_e( 'SMTP relay', 'arr-theme' ); ?></strong> —
							<?php echo esc_html( ARR_SMTP_HOST . ':' . ( defined( 'ARR_SMTP_PORT' ) ? ARR_SMTP_PORT : 587 ) ); ?>
						<?php else : ?>
							<?php esc_html_e( "The web host's own mail (no SMTP relay configured)", 'arr-theme' ); ?>
						<?php endif; ?>
					</td>
				</tr>
				<?php if ( $configured ) : ?>
				<tr>
					<th><?php esc_html_e( 'SMTP username', 'arr-theme' ); ?></th>
					<td><code><?php echo esc_html( ARR_SMTP_USER ); ?></code></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'SMTP key', 'arr-theme' ); ?></th>
					<td><?php esc_html_e( 'Set (hidden)', 'arr-theme' ); ?></td>
				</tr>
				<?php endif; ?>
				<tr>
					<th><?php esc_html_e( 'Sent from', 'arr-theme' ); ?></th>
					<td>
						<code><?php echo esc_html( arr_mail_from() ); ?></code>
						<?php
						$from_domain = substr( strrchr( arr_mail_from(), '@' ), 1 );
						$site_domain = wp_parse_url( home_url(), PHP_URL_HOST );
						// A From address on a domain you don't control is the single
						// most common cause of mail being rejected outright.
						if ( in_array( strtolower( $from_domain ), array( 'gmail.com', 'outlook.com', 'hotmail.com', 'yahoo.com', 'icloud.com' ), true ) ) :
						?>
							<p class="description" style="color:#b32d2e">
								<?php esc_html_e( 'This is a free mailbox provider. Mail claiming to come from these domains is rejected by receiving servers. Change it to an address on your own domain under Appearance → Customize → ARR Theme Settings → Header.', 'arr-theme' ); ?>
							</p>
						<?php endif; ?>
						<p class="description">
							<?php
							printf(
								/* translators: 1: From address domain, 2: website domain. */
								esc_html__( 'Sending domain: %1$s. Site domain: %2$s. The sending domain is the one that needs the SPF, DKIM and DMARC records.', 'arr-theme' ),
								'<code>' . esc_html( $from_domain ) . '</code>',
								'<code>' . esc_html( $site_domain ) . '</code>'
							);
							?>
						</p>
					</td>
				</tr>
			</tbody>
		</table>

		<h2><?php esc_html_e( 'Send a test', 'arr-theme' ); ?></h2>
		<form method="post">
			<?php wp_nonce_field( 'arr_mail_test', 'arr_mail_test_nonce' ); ?>
			<p>
				<label for="arr_test_to"><?php esc_html_e( 'Send to', 'arr-theme' ); ?></label><br />
				<input type="email" id="arr_test_to" name="arr_test_to" class="regular-text"
				       value="<?php echo esc_attr( wp_get_current_user()->user_email ); ?>" required />
			</p>
			<p>
				<button type="submit" class="button button-primary"><?php esc_html_e( 'Send test email', 'arr-theme' ); ?></button>
			</p>
		</form>
	</div>
	<?php
}
