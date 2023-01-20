<?php

namespace ZionBuilderPro;

use ZionBuilderPro\Plugin;
use ZionBuilder\Plugin as FreePlugin;

class Requirements {
	const MINIMUM_FREE_PLUGIN_VERSION = '3.3.0';

	public static $passed_requirements = false;

	/**
	 * Check to see if the minimum requirements are passed
	 *
	 * @return boolean
	 */
	public static function passed_requirements() {
		// Check to see if the plugin needs to be updates
		if ( ! class_exists( 'ZionBuilder\Plugin' ) ) {
			add_action( 'admin_notices', [ __CLASS__, 'notice_free_plugin_not_active' ] );

			return false;
		}

		// Check minimum zion builder plugin required verion
		if ( ! version_compare( FreePlugin::instance()->get_version(), self::MINIMUM_FREE_PLUGIN_VERSION, '>=' ) ) {
			// Show admin notice to update the base plugin
			add_action( 'admin_notices', [ __CLASS__, 'notice_free_plugin_minimum_version' ] );
			return false;
		}

		// Check to see if the minimum version requires a different minimum version
		if ( defined( 'MINIMUM_ZION_PRO_VERSION' ) ) {
			if ( ! version_compare( Plugin::instance()->get_version(), \MINIMUM_ZION_PRO_VERSION, '>=' ) ) {
				// Show admin notice to update the base plugin
				add_action( 'admin_notices', [ __CLASS__, 'notice_pro_plugin_minimum_version' ] );

				return false;
			}
		}

		return true;
	}

	public static function notice_free_plugin_minimum_version() {
		$zionbuilder_plugin_file = rawurlencode( 'zionbuilder/zionbuilder.php' );
		$update_url              = wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' . $zionbuilder_plugin_file ), 'upgrade-plugin_' . $zionbuilder_plugin_file );
		$message                 = sprintf( __( 'Zion builder Pro was not actived because you are using an outdated version of Zion Builder. Please update Zion Builder plugin to at least %s', 'zionbuilder-pro' ), self::MINIMUM_FREE_PLUGIN_VERSION );
		$button                  = sprintf( '<a href="%s" class="button-primary">%s</a>', $update_url, __( 'Update Zion Builder', 'zionbuilder-pro' ) );
		// Print the message and button
		echo wp_kses_post( sprintf( '<div class="error"><p>%s</p><p>%s</p></div>', $message, $button ) );
	}

	/**
	 * Will show a notice if the free plugin is not active
	 *
	 * @return void
	 */
	public static function notice_free_plugin_not_active() {
		$button = '';

		// Don't show notices if the user is already on the install/update plugins pages
		$screen = get_current_screen();
		if ( isset( $screen->parent_file ) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id ) {
			return;
		}

		$zionbuilder_plugin_file = 'zionbuilder/zionbuilder.php';
		$zionbuilder_plugin_slug = 'zionbuilder';

		// Check if plugin is not installed
		if ( self::is_plugin_installed() ) {
			// Don't show a message if the Zion Builder plugin requirements are not met
			if ( defined( 'ZIONBUILDER_REQUIREMENTS_FAILED' ) && ZIONBUILDER_REQUIREMENTS_FAILED ) {
				return;
			}

			$activate_url = wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=' . rawurlencode( $zionbuilder_plugin_file ) . '&plugin_status=all' ), 'activate-plugin_' . $zionbuilder_plugin_file );
			$message      = __( 'Zion builder Pro is not working because it requires the Zion builder plugin to be installed and active.', 'zionbuilder-pro' );

			if ( current_user_can( 'activate_plugins' ) ) {
				$button = sprintf( '<a href="%s" class="button-primary">%s</a>', $activate_url, __( 'Activate Zion Builder', 'zionbuilder-pro' ) );
			}

			// Print the message and button
			echo wp_kses_post( sprintf( '<div class="error"><p>%s</p><p>%s</p></div>', $message, $button ) );
		} else {
			$button      = '';
			$install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . $zionbuilder_plugin_slug ), 'install-plugin_' . $zionbuilder_plugin_slug );

			// Allow the user to install the base plugin
			if ( current_user_can( 'install_plugins' ) ) {
				$button = sprintf( '<a href="%s" class="button-primary">%s</a>', $install_url, __( 'Install Zion Builder', 'zionbuilder-pro' ) );
			} else {
				$button = __( 'Please contact an administrator to install the ZionBuilder plugin', 'zionbuilder-pro' );
			}

			$message = __( 'Zion builder Pro was not activated because it requires the Zion builder plugin.', 'zionbuilder-pro' );

			// Print the message and button
			echo wp_kses_post( sprintf( '<div class="error"><p>%s</p><p>%s</p></div>', $message, $button ) );
		}
	}


	/**
	 * Check to see if the free plugin is installed
	 *
	 * @return boolean
	 */
	public static function is_plugin_installed() {
		$zionbuilder_plugin_file = 'zionbuilder/zionbuilder.php';
		$installed_plugins       = get_plugins();

		return isset( $installed_plugins[$zionbuilder_plugin_file] );
	}

	public static function notice_pro_plugin_minimum_version() {
		$zionbuilder_plugin_file = rawurlencode( 'zionbuilder-pro/zionbuilder-pro.php' );
		$update_url              = wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' . $zionbuilder_plugin_file ), 'upgrade-plugin_' . $zionbuilder_plugin_file );
		$message                 = sprintf( __( 'Zion Builder plugin requires a higher version of Zion Builder PRO. Please update the plugin to at least vesrion %s.', 'zionbuilder-pro' ), MINIMUM_ZION_PRO_VERSION );
		$button                  = sprintf( '<a href="%s" class="button-primary">%s</a>', $update_url, __( 'Update Zion Builder PRO', 'zionbuilder-pro' ) );

		// Print the message and button
		echo wp_kses_post( sprintf( '<div class="error"><p>%s</p><p>%s</p></div>', $message, $button ) );
	}
}
