<?php
/**
 * Registers the field groups defined in acf-field-groups.json.
 *
 * Registering them in code rather than importing them into the database
 * means the JSON file is the single source of truth: editing it updates
 * wp-admin immediately, there is no "Import" step to forget, and no
 * duplicate groups can accumulate.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function arr_register_acf_field_groups() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$file = get_template_directory() . '/acf-field-groups.json';
	if ( ! file_exists( $file ) ) {
		return;
	}

	$groups = json_decode( file_get_contents( $file ), true );
	if ( ! is_array( $groups ) ) {
		return;
	}

	foreach ( $groups as $group ) {
		acf_add_local_field_group( $group );
	}
}
add_action( 'acf/init', 'arr_register_acf_field_groups' );

/**
 * ACF is what makes this site editable, so say so plainly if it is missing.
 */
function arr_acf_missing_notice() {
	if ( function_exists( 'get_field' ) || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	echo '<div class="notice notice-warning"><p><strong>ARR theme:</strong> Advanced Custom Fields is not active. The site still displays the approved design, but page content cannot be edited until the plugin is activated.</p></div>';
}
add_action( 'admin_notices', 'arr_acf_missing_notice' );
