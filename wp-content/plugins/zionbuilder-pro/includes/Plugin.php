<?php

namespace ZionBuilderPro;

use ZionBuilderPro\Requirements;
use ZionBuilderPro\Integrations;
use ZionBuilderPro\Elements;
use ZionBuilderPro\Permissions;
use ZionBuilderPro\Editor;
use ZionBuilderPro\Fonts\Fonts;
use ZionBuilderPro\Admin;
use ZionBuilderPro\Icons;
use ZionBuilderPro\DynamicContent\Manager as DynamicContentManager;
use ZionBuilderPro\Api\RestApi;
use ZionBuilderPro\ThemeBuilder\ThemeBuilder;
use ZionBuilderPro\Features\AdditionalPageOptions;
use ZionBuilderPro\Features\CustomCSS;
use ZionBuilderPro\Features\Connector\Connector;
use ZionBuilderPro\Frontend;
use ZionBuilderPro\ProMasks;
use ZionBuilderPro\Localization;
use ZionBuilderPro\License;
use ZionBuilderPro\WhiteLabel;
use ZionBuilderPro\Conditions\Conditions;
use ZionBuilderPro\MegaMenu;
use ZionBuilderPro\Assets;
use ZionBuilderPro\ElementConditions\ElementConditions;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

class Plugin {
	/**
	 * ZionBuilderPro instance.
	 *
	 * Holds the plugin instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @static
	 *
	 * @var Plugin
	 */
	public static $instance = null;

	/**
	 * Plugin data from header comments
	 *
	 * @var string
	 */
	private $version = null;

	/**
	 * Project root path
	 *
	 * @var string
	 */
	private $project_root_path = null;

	/**
	 * Project root url
	 *
	 * @var string
	 */
	private $project_root_url = null;

	public $plugin_data = [];

	public $plugin_file = null;

	public $white_label = null;

	public $theme_builder = null;

	/**
	 * Holds the reference to the instance of the \ZionBuilder\Elements\Manager class
	 *
	 * @var ElementsManager
	 *
	 * @see Plugin::init()
	 */
	public $elements_manager = null;

	public function __construct( $path ) {
		$this->plugin_file       = $path;
		$this->project_root_path = trailingslashit( dirname( $path ) );
		$this->project_root_url  = plugin_dir_url( $path );
		$this->plugin_data       = $this->set_plugin_data( $path );
		$this->version           = $this->plugin_data['Version'];

		self::$instance = $this;

		add_action( 'init', [ $this, 'on_wp_init' ] );
		add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded' ] );
	}


	/**
	 * Will instantiate all plugin dependencies
	 *
	 * @return void
	 */
	public function init_plugin() {
		$this->permissions             = new Permissions();
		$this->editor                  = new Editor();
		$this->dynamic_content_manager = new DynamicContentManager();
		$this->api                     = new RestApi();
		$this->icons                   = new Icons();
		$this->theme_builder           = new ThemeBuilder();
		$this->frontend                = new Frontend();
		$this->masks                   = new ProMasks();
		$this->conditions              = new Conditions();
		$this->repeater                = new Repeater();

		new Assets();
		new Localization();
		new Admin();
		new Fonts();
		new Elements();
		new MegaMenu();

		// PRO FEATURES
		new AdditionalPageOptions();
		new CustomCSS();
		new Connector();
		new ElementConditions();
	}


	public function on_plugins_loaded() {
		// Check for requirements
		if ( Requirements::passed_requirements() ) {
			add_action( 'zionbuilder/before_init', [ $this, 'init_integrations' ] );
			add_action( 'zionbuilder/loaded', [ $this, 'init_plugin' ] );
		}
	}

	/**
	 * Will load plugin text domain
	 *
	 * @return void
	 */
	public function on_wp_init() {
		// Init license
		new License();
		load_plugin_textdomain( 'zionbuilder-pro', false, $this->project_root_path . '/languages' );
	}

	/**
	 * Will load all PRO integrations
	 *
	 * @return void
	 */
	public function init_integrations() {
		new WhiteLabel();
		new Integrations();
	}


	/**
	 * Instance.
	 *
	 * Always load a single instance of the Plugin class
	 *
	 * @since  1.0.0
	 * @access public
	 * @static
	 *
	 * @return Plugin an instance of the class
	 */
	public static function instance() {
		return self::$instance;
	}


	/**
	 * Retrieve the project root path
	 *
	 * @return string
	 */
	public function get_root_path() {
		return $this->project_root_path;
	}

	/**
	 * Retrieve the project root path
	 *
	 * @return string
	 */
	public function get_plugin_file() {
		return $this->plugin_file;
	}


	/**
	 * Retrieve the project root url
	 *
	 * @return string
	 */
	public function get_root_url() {
		return $this->project_root_url;
	}

	/**
	 * Retrieve the project version
	 *
	 * @return string
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Retrieve the project data
	 *
	 * @param mixed $type
	 *
	 * @return string
	 */
	public function get_plugin_data( $type ) {
		if ( isset( $this->plugin_data[$type] ) ) {
			return $this->plugin_data[$type];
		}

		return null;
	}


	/**
	 * Will set the plugin data
	 *
	 * @since 2.0.0
	 * @param string $path
	 *
	 * @return array
	 */
	public function set_plugin_data( $path ) {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return get_plugin_data( $path );
	}
}
