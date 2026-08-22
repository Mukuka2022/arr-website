<?php
/**
 * Brands wp-login.php so contributors following the footer link land on a page
 * that looks like ARR rather than a stock WordPress screen.
 *
 * Styling only — no change to how authentication works, and nothing here
 * weakens the login lockdown rules the security plugin applies.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * The logo above the form links home, not to wordpress.org.
 */
function arr_login_header_url() {
	return home_url( '/' );
}
add_filter( 'login_headerurl', 'arr_login_header_url' );

function arr_login_header_text() {
	return get_bloginfo( 'name' );
}
add_filter( 'login_headertext', 'arr_login_header_text' );

function arr_login_branding() {
	wp_enqueue_style(
		'arr-login-fonts',
		'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600;700&display=swap',
		array(),
		null
	);

	$logo     = get_theme_mod( 'custom_logo' );
	$logo_url = $logo ? wp_get_attachment_image_url( $logo, 'full' ) : get_template_directory_uri() . '/images/logo.png';

	$css = '
	body.login {
		background: #0B1F3A;
		font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
	}
	body.login #login { width: 340px; padding-top: 6vh; }

	/* Replaces the WordPress mark above the form with the site logo. */
	body.login h1 a {
		background-image: url(' . esc_url( $logo_url ) . ');
		background-size: contain;
		background-position: center center;
		width: 100%;
		height: 74px;
		margin-bottom: 22px;
	}

	body.login form {
		background: #FAFAF7;
		border: 1px solid rgba(200,155,60,0.35);
		border-top: 3px solid #C89B3C;
		border-radius: 3px;
		box-shadow: 0 8px 30px rgba(0,0,0,0.28);
		padding: 26px 24px 24px;
	}
	body.login form label { font-size: 12px; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase; color: #6b6459; }
	body.login form .input,
	body.login input[type="text"],
	body.login input[type="password"] {
		background: #fff;
		border: 1px solid #e4ddd0;
		border-radius: 3px;
		color: #2C2C2C;
		font-size: 15px;
		padding: 10px 12px;
	}
	body.login form .input:focus,
	body.login input[type="text"]:focus,
	body.login input[type="password"]:focus {
		border-color: #C89B3C;
		box-shadow: 0 0 0 3px rgba(200,155,60,0.18);
		outline: none;
	}
	body.login .wp-pwd button.wp-hide-pw .dashicons { color: #6b6459; }

	body.login .button-primary {
		background: #C89B3C !important;
		border-color: #C89B3C !important;
		color: #0B1F3A !important;
		font-weight: 700;
		letter-spacing: 0.03em;
		text-shadow: none !important;
		box-shadow: none !important;
		border-radius: 3px;
		height: 42px;
		padding: 0 20px;
	}
	body.login .button-primary:hover { background: #ddb768 !important; border-color: #ddb768 !important; }

	/* "Lost your password?" and "Back to site" beneath the card. */
	body.login #nav, body.login #backtoblog { padding-left: 2px; }
	body.login #nav a, body.login #backtoblog a { color: rgba(250,250,247,0.6) !important; font-size: 13px; }
	body.login #nav a:hover, body.login #backtoblog a:hover { color: #C89B3C !important; }

	body.login .message, body.login .success { border-left-color: #1C6B52; }
	body.login #login_error { border-left-color: #b3261e; }
	body.login .privacy-policy-page-link { display: none; }
	';

	wp_add_inline_style( 'login', $css );
}
add_action( 'login_enqueue_scripts', 'arr_login_branding' );
