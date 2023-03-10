<?php
/**
 * This file runs when the plugin in uninstalled (deleted).
 * This will not run when the plugin is deactivated.
 * Ideally you will add all your clean-up scripts here
 * that will clean-up unused meta, options, etc. in the database.
 *
 * @package ZiUltimate/Uninstall
 */

// If plugin is not being uninstalled, exit (do nothing).
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Do something here if plugin is being uninstalled.
$settings = get_option('zu_settings');
if ( ! empty( $settings['delete_data'] ) && 'enabled' === $settings['delete_data'] ) {
	// Remove all matching options from the database.
	foreach ( wp_load_alloptions() as $option => $value ) {
		if ( strpos( $option, 'zu_' ) !== false ) {
			delete_option( $option );
		}

		if ( strpos( $option, 'ziultimate_' ) !== false ) {
			delete_option( $option );
		}
	}

	delete_option( 'zuwl' );
}