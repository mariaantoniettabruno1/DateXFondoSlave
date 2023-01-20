<?php

namespace ZionBuilderPro;

use ZionBuilderPro\Repeater\RepeaterElement;
use ZionBuilderPro\Plugin;
use ZionBuilderPro\Repeater\Providers\ActivePageQuery;
use ZionBuilderPro\Repeater\Providers\RecentPosts;
use ZionBuilderPro\Repeater\Providers\QueryBuilder;

class Repeater {
	public static $repeater_providers = [];
	public static $repeater_consumers = [];

	/**
	 * Holds a reference to the current provider index
	 *
	 * @var integer
	 */
	public static $current_index = 0;


	public static $active_consumer_data = [];

	// New
	public $providers        = [];
	public $active_providers = [];

	public function __construct() {
		add_filter( 'zionbuilder/renderer/custom_renderer', [ $this, 'maybe_change_element_renderer' ], 10, 2 );
		add_action( 'zionbuilder/schema/advanced_options', [ $this, 'add_repeater_options' ] );
		add_filter( 'zionbuilder/api/bulk_actions', [ $this, 'add_repeater_data_to_bulk_actions' ] );
		add_action( 'zionbuilderpro/editor/initial_data', [ $this, 'add_editor_initial_data' ] );

		// CSS extraction
		add_action( 'zionbuilder/element/before_element_extract_assets', [ $this, 'before_css_extraction' ] );
		add_action( 'zionbuilder/assets/after_element_extract_assets', [ $this, 'after_css_extraction' ] );

		// register default providers
		$this->register_providers();
	}

	public function before_css_extraction( $element_instance ) {
		// If this is a repeater provider, set the query
		if ( self::is_repeater_provider( $element_instance ) ) {
			$provider_config = self::get_repeater_provider_config( $element_instance );
			$this->set_active_provider( $provider_config );
		}

		if ( self::is_repeater_consumer( $element_instance ) && $this->get_active_provider() ) {
			$this->get_active_provider()->start_loop();
		}

		// Check if this is a repeater consumer
		if ( self::is_repeater_consumer( $element_instance ) || ( $this->get_active_provider() && $this->get_active_provider()->is_looping() ) ) {
			// Replace the element uid with the css class
			$element_css_id                         = $element_instance->get_element_css_id();
			$element_instance->element_css_selector = '.zb .' . $element_css_id;
		}
	}

	public function after_css_extraction( $element_instance ) {
		if ( self::is_repeater_consumer( $element_instance ) && $this->get_active_provider() ) {
			$this->get_active_provider()->stop_loop();
		}

		// If this is a repeater provider, reset the query
		if ( self::is_repeater_provider( $element_instance ) ) {
			$this->reset_active_provider();
		}

	}

	/**
	 * Will register repeater providers
	 *
	 * @return void
	 */
	public function register_providers() {
		$this->register_provider( new ActivePageQuery() );
		$this->register_provider( new RecentPosts() );
		$this->register_provider( new QueryBuilder() );

		// Allow 3rd party plugins to register their providers
		do_action( 'zionbuilderpro/repeater/register_providers', $this );
	}


	/**
	 * Returns a provider instance
	 *
	 * @param string $provider_id
	 * @return Provider
	 */
	public function get_provider( $provider_id ) {
		if ( isset( $this->providers[$provider_id] ) ) {
			return $this->providers[$provider_id];
		}

		return false;
	}

	/**
	 * Undocumented function
	 *
	 * @param Provider $provider_class
	 *
	 * @return void
	 */
	public function register_provider( $provider_class ) {
		$this->providers[$provider_class->get_id()] = $provider_class;
	}

	/**
	 * Sets an active provider based on element instance
	 *
	 * @param Element $element_instance
	 *
	 * @return RepeaterProvider|boolean
	 */
	public function set_active_provider( $provider_config ) {
		// Setup the repeater provider
		$type           = $provider_config['type'];
		$provider_class = $this->get_provider( $type );

		// Remove the provider type from config
		unset( $provider_config['type'] );

		if ( $provider_class ) {
			// Get repeater provider class
			$provider_class_name      = $provider_class->get_class_name();
			$provider_instance        = new $provider_class_name( $provider_config );
			$this->active_providers[] = $provider_instance;

			return $provider_instance;
		}

		return false;
	}

	public function get_active_provider() {
		return end( $this->active_providers );
	}

	public function reset_active_provider() {
		// Call reset on repeater instance
		$active_provider = $this->get_active_provider();

		if ( method_exists( $active_provider, 'reset_query' ) ) {
			$active_provider->reset_query();
		}

		array_pop( $this->active_providers );
	}

	public function get_providers() {
		return $this->providers;
	}

	/**
	 * Check to see if this is a repeater provider or consumer and replace the renderer
	 *
	 * @param [type] $element_instance
	 */
	public function maybe_change_element_renderer( $renderer, $element_instance ) {
		if ( self::is_repeater_provider( $element_instance ) || self::is_repeater_consumer( $element_instance ) ) {
			return new RepeaterElement( $element_instance );
		}

		return $renderer;
	}

	public static function is_repeater_provider( $element ) {
		return $element->options->get_value( '_advanced_options.is_repeater_provider', false );
	}

	public static function is_repeater_consumer( $element ) {
		return $element->options->get_value( '_advanced_options.is_repeater_consumer', false );
	}

	public static function get_repeater_provider_config( $element ) {
		return $element->options->get_value(
			'_advanced_options.repeater_provider_config',
			[
				'type' => 'active_page_query',
			]
		);
	}

	public static function get_repeater_consumer_config( $element ) {
		return $element->options->get_value( '_advanced_options.repeater_consumer_config', false );
	}


	/**
	 * Adds the repeater related options to Element Advanced options
	 *
	 * @param [type] $options
	 * @return void
	 */
	public function add_repeater_options( $options ) {
		$repeater_options = $options->add_group(
			'repeater-provider-options',
			[
				'type'      => 'panel_accordion',
				'title'     => __( 'Repeater Options', 'zionbuilder-pro' ),
				'collapsed' => true,
			]
		);

		$repeater_options->add_option(
			'is_repeater_provider',
			[
				'type'    => 'checkbox_switch',
				'default' => false,
				'layout'  => 'inline',
				'title'   => esc_html__( 'Enable repeater provider?', 'zionbuilder-pro' ),
			]
		);

		$repeater_options->add_option(
			'repeater_provider_config',
			[
				'type'       => 'query_builder',
				'dependency' => [
					[
						'option' => 'is_repeater_provider',
						'value'  => [ true ],
					],
				],
			]
		);

		// Repeater consumer
		$repeater_options->add_option(
			'is_repeater_consumer',
			[
				'type'    => 'checkbox_switch',
				'default' => false,
				'layout'  => 'inline',
				'title'   => esc_html__( 'Enable repeater consumer?', 'zionbuilder-pro' ),
			]
		);

		$consumer_config_group = $repeater_options->add_option(
			'repeater_consumer_config',
			[
				'type'       => 'group',
				'dependency' => [
					[
						'option' => 'is_repeater_consumer',
						'value'  => [ true ],
					],
				],
			]
		);

		$consumer_config_group->add_option(
			'start',
			[
				'type'  => 'number',
				'title' => __( 'Start', 'zionbuilder-pro' ),
			]
		);

		$consumer_config_group->add_option(
			'end',
			[
				'type'  => 'number',
				'title' => __( 'End', 'zionbuilder-pro' ),
			]
		);

	}

	public function add_repeater_data_to_bulk_actions( $actions ) {
		$actions['perform_repeater_query'] = [ $this, 'perform_repeater_query' ];

		return $actions;
	}

	public function perform_repeater_query( $query_config ) {
		$provider = $this->get_active_provider();

		if ( $provider ) {
			return $provider->get_query();
		}

		return false;
	}

	public function add_editor_initial_data( $data ) {
		$data['repeater_data'] = [
			'query_builder_types' => $this->get_repeater_options(),
		];

		return $data;
	}

	public function get_repeater_options() {
		$providers_config = [];

		foreach ( $this->get_providers() as $provider_id => $provider_instance ) {
			$providers_config[] = [
				'name'   => $provider_instance->get_name(),
				'id'     => $provider_id,
				'schema' => $provider_instance->get_schema(),
			];
		}

		return $providers_config;
	}

}
