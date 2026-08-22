<?php
/**
 * Admin appearance.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * The admin colour scheme everyone gets.
 *
 * Set to '' to hand the choice back to each user — the picker on their profile
 * reappears and their own saved preference applies again.
 */
function arr_forced_admin_color() {
	return apply_filters( 'arr_forced_admin_color', 'ocean' );
}

/**
 * Applied when the scheme is read rather than by rewriting each user's saved
 * preference, so nobody's choice is destroyed — clearing the value above
 * restores whatever they had picked before.
 */
function arr_force_admin_color( $color ) {
	$forced = arr_forced_admin_color();
	return $forced ? $forced : $color;
}
add_filter( 'get_user_option_admin_color', 'arr_force_admin_color' );

/**
 * New users start on the same scheme, so the profile screen agrees with what
 * they are actually looking at.
 */
function arr_default_admin_color( $color ) {
	$forced = arr_forced_admin_color();
	return $forced ? $forced : $color;
}
add_filter( 'default_option_admin_color', 'arr_default_admin_color' );

/**
 * Hide the picker while a scheme is forced.
 *
 * Leaving it visible would be worse than removing it: the control still saves,
 * but the filter above overrides it on the next page load, so it looks broken.
 */
function arr_hide_admin_color_picker() {
	if ( ! arr_forced_admin_color() ) return;
	remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );
}
add_action( 'admin_init', 'arr_hide_admin_color_picker' );
