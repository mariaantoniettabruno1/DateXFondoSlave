<?php

namespace ZionBuilderPro;

use ZionBuilder\Plugin as FreePlugin;
use ZionBuilderPro\Utils;
use ZionBuilderPro\Plugin;
use ZionBuilderPro\License;
use ZionBuilderPro\DynamicContent\Manager as DynamicData;

class Editor {
	public function __construct() {
		add_action( 'zionbuilder/editor/before_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'zionbuilder/preview/after_load_scripts', [ $this, 'enqueue_preview_scripts' ] );
		add_filter( 'zionbuilder/rest/bulk_actions/post_id', [ $this, 'change_bulk_actions_post_id' ], 10, 2 );
		add_action( 'zionbuilder/rest/bulk_actions/before_action', [ $this, 'set_repeater_data' ] );
		add_action( 'zionbuilder/rest/bulk_actions/after_action', [ $this, 'reset_dynamic_data_query' ] );

		if ( License::has_valid_license() ) {
			add_action( 'zionbuilder/schema/style_options', [ $this, 'add_style_options' ] );
		}
	}

	public function set_repeater_data( $action_config ) {
		if ( isset( $action_config['dynamic_data_source'] ) ) {
			DynamicData::prepare_query( $action_config['dynamic_data_source'] );
		}

		// Check for repeater provider config
		if ( isset( $action_config['repeaterConfigs'] ) ) {
			// Set all active repeaters
			if ( is_array( $action_config['repeaterConfigs'] ) ) {
				$this->setup_repeater_data( $action_config['repeaterConfigs'] );
			}
		}
	}

	public function setup_repeater_data( $config ) {
		if ( isset( $config['repeaterProvider'] ) && $config['repeaterProvider'] ) {
			Plugin::instance()->repeater->set_active_provider( $config['repeaterProvider'] );
		}

		if ( isset( $config['repeaterConsumer'] ) && ! empty( $config['repeaterConsumer'] ) ) {
			$active_provider = Plugin::instance()->repeater->get_active_provider();
			if ( $active_provider ) {
				$active_provider->start_loop( $config['repeaterConsumer'] );
			}
		}

		if ( isset( $config['repeaterItemIndex'] ) ) {
			$active_provider = Plugin::instance()->repeater->get_active_provider();
			if ( $active_provider ) {
				$active_provider->the_item( $config['repeaterItemIndex'] );
			}
		}

		if ( ! empty( $config['children'] ) ) {
			$this->setup_repeater_data( $config['children'] );
		}
	}

	public function reset_dynamic_data_query( $action_config ) {
		if ( isset( $action_config['dynamic_data_source'] ) ) {
			DynamicData::reset_query( $action_config['dynamic_data_source'] );
		}
	}

	public function change_bulk_actions_post_id( $post_id, $request ) {
		$dynamic_data_config = $request->get_param( 'dynamic_data_source' );

		if ( ! empty( $dynamic_data_config['type'] ) && isset( $dynamic_data_config['id'] ) && $dynamic_data_config['type'] === 'single' ) {
			return $dynamic_data_config['id'];
		}

		return $post_id;
	}

	public function add_style_options( $options ) {
		// Display
		$display_accordion = $options->get_option( '_styles.pseudo_selectors.display' );
		if ( $display_accordion ) {
			// remove filter upgrade to PRO
			$display_accordion->remove_option( 'upgrade_message' );

			// Add filter options
			$this->attach_display_options( $display_accordion );
		}
		// Box Shadow
		$box_shadow_group = $options->get_option( '_styles.pseudo_selectors.borders.box-shadow-group' );
		if ( $box_shadow_group ) {
			// remove transition upgrade to PRO
			$box_shadow_group->remove_option( 'upgrade_message' );

			// Remove label
			unset( $box_shadow_group->label );

			// Add transition options
			$this->attach_box_shadow_options( $box_shadow_group );
		}
		// Transitions
		$transitions_accordion = $options->get_option( '_styles.pseudo_selectors.transitions' );
		if ( $transitions_accordion ) {
			// remove transition upgrade to PRO
			$transitions_accordion->remove_option( 'upgrade_message' );

			// Remove label
			unset( $transitions_accordion->label );

			// Add transition options
			$this->attach_transitions_options( $transitions_accordion );
		}

		// Transform
		$transform_accordion = $options->get_option( '_styles.pseudo_selectors.transform' );
		if ( $transform_accordion ) {
			// remove transition upgrade to PRO
			$transform_accordion->remove_option( 'upgrade_message' );

			// Remove label
			unset( $transform_accordion->label );

			// Add transition options
			$this->attach_transform_options( $transform_accordion );
		}

		// Filters
		$filters_accordion = $options->get_option( '_styles.pseudo_selectors.filters' );
		if ( $filters_accordion ) {
			// remove filter upgrade to PRO
			$filters_accordion->remove_option( 'upgrade_message' );

			// Remove label
			unset( $filters_accordion->label );

			// Add filter options
			$this->attach_filters_options( $filters_accordion );
		}
	}

	public static function attach_box_shadow_options( $options ) {
		$options->add_option(
			'box-shadow',
			[
				'type'        => 'shadow',
				'title'       => __( 'Box Shadow', 'zionbuilder-pro' ),
				'description' => __( 'Set the desired box shadow.', 'zionbuilder-pro' ),
				'shadow_type' => 'box-shadow',
			]
		);
	}

	public static function attach_transform_options( $options ) {
		$transform = $options->add_option(
			'transform',
			[
				'type'               => 'repeater',
				'add_button_text'    => __( 'Add new transform property', 'zionbuilder-pro' ),
				'item_title'         => 'property',
				'default_item_title' => 'item %s',
				'reset_group'        => [
					'option' => 'property',
				],
				'title'              => __( 'Transform properties', 'zionbuilder-pro' ),
			]
		);

		$transform->add_option(
			'property',
			[
				'type'    => 'select',
				'title'   => __( 'Property', 'zionbuilder-pro' ),
				'default' => 'translate',
				'options' => [
					[
						'id'   => 'translate',
						'name' => __( 'Translate', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'scale',
						'name' => __( 'Scale', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'rotate',
						'name' => __( 'Rotate', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'skew',
						'name' => __( 'Skew', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'perspective',
						'name' => __( 'Perspective', 'zionbuilder-pro' ),
					],
				],
			]
		);

		$translate = $transform->add_option(
			'translate',
			[
				'type'           => 'group',
				'options-layout' => 'full',
				'dependency'     => [
					[
						'option' => 'property',
						'value'  => [ 'translate' ],
					],
				],
			]
		);

		$translate->add_option(
			'translateX',
			[
				'type'        => 'dynamic_slider',
				'title'       => __( 'Translate X', 'zionbuilder-pro' ),
				'description' => __( 'Set translate property for X dimension.', 'zionbuilder-pro' ),
				'options'     => [
					[
						'unit' => 'px',
						'min'  => -999,
						'max'  => 999,
						'step' => 1,
					],
					[
						'unit' => '%',
						'min'  => -100,
						'max'  => 100,
						'step' => 1,
					],
					[
						'unit' => 'pt',
						'min'  => -999,
						'max'  => 999,
						'step' => 1,
					],
					[
						'unit' => 'em',
						'min'  => -999,
						'max'  => 999,
						'step' => 1,
					],
					[
						'unit' => 'rem',
						'min'  => -999,
						'max'  => 999,
						'step' => 1,
					],
				],
			]
		);

		$translate->add_option(
			'translateY',
			[
				'type'        => 'dynamic_slider',
				'title'       => __( 'Translate Y', 'zionbuilder-pro' ),
				'description' => __( 'Set translate property for Y dimension.', 'zionbuilder-pro' ),
				'options'     => [
					[
						'unit' => 'px',
						'min'  => -999,
						'max'  => 999,
						'step' => 1,
					],
					[
						'unit' => '%',
						'min'  => -100,
						'max'  => 100,
						'step' => 1,
					],
					[
						'unit' => 'pt',
						'min'  => -999,
						'max'  => 999,
						'step' => 1,
					],
					[
						'unit' => 'em',
						'min'  => -999,
						'max'  => 999,
						'step' => 1,
					],
					[
						'unit' => 'rem',
						'min'  => -999,
						'max'  => 999,
						'step' => 1,
					],
				],
			]
		);

		$translate->add_option(
			'translateZ',
			[
				'type'        => 'dynamic_slider',
				'title'       => __( 'Translate Z', 'zionbuilder-pro' ),
				'description' => __( 'Set translate property for Z dimension.', 'zionbuilder-pro' ),
				'options'     => [
					[
						'unit' => 'px',
						'min'  => -999,
						'max'  => 999,
						'step' => 1,
					],
					[
						'unit' => '%',
						'min'  => -100,
						'max'  => 100,
						'step' => 1,
					],
				],
			]
		);

		$scale = $transform->add_option(
			'scale',
			[
				'type'           => 'group',
				'options-layout' => 'full',
				'dependency'     => [
					[
						'option' => 'property',
						'value'  => [ 'scale' ],
					],
				],
			]
		);

		$scale->add_option(
			'scaleX',
			[
				'type'        => 'slider',
				'min'         => -5,
				'max'         => 5,
				'default'     => 1,
				'step'        => 0.05,
				'shift_step'  => 0.1,
				'title'       => __( 'Scale X', 'zionbuilder-pro' ),
				'description' => __( 'Set scale property for X dimension.', 'zionbuilder-pro' ),
			]
		);

		$scale->add_option(
			'scaleY',
			[
				'type'         => 'slider',
				'min'          => -5,
				'max'          => 5,
				'default'      => 1,
				'step'         => 0.05,
				'shift_step'   => 0.1,
				'title'        => __( 'Scale Y', 'zionbuilder-pro' ),
				'default_unit' => 'unitless',
				'description'  => __( 'Set scale property for Y dimension.', 'zionbuilder-pro' ),
			]
		);

		$scale->add_option(
			'scaleZ',
			[
				'type'        => 'slider',
				'min'         => -5,
				'max'         => 5,
				'default'     => 1,
				'step'        => 0.05,
				'shift_step'  => 0.1,
				'title'       => __( 'Scale Z', 'zionbuilder-pro' ),
				'description' => __( 'Set scale property for Z dimension.', 'zionbuilder-pro' ),
			]
		);

		$rotate = $transform->add_option(
			'rotate',
			[
				'type'           => 'group',
				'options-layout' => 'full',
				'dependency'     => [
					[
						'option' => 'property',
						'value'  => [ 'rotate' ],
					],
				],
			]
		);

		$rotate->add_option(
			'rotate',
			[
				'type'        => 'dynamic_slider',
				'title'       => __( 'Rotate', 'zionbuilder-pro' ),
				'description' => __( 'Set rotation property.', 'zionbuilder-pro' ),
				'options'     => [
					[
						'unit' => 'deg',
						'min'  => -360,
						'max'  => 360,
						'step' => 1,
					],
				],
			]
		);

		$rotate->add_option(
			'rotateX',
			[
				'type'        => 'dynamic_slider',
				'title'       => __( 'Rotate X', 'zionbuilder-pro' ),
				'description' => __( 'Set rotation property for X dimension.', 'zionbuilder-pro' ),
				'options'     => [
					[
						'unit' => 'deg',
						'min'  => -360,
						'max'  => 360,
						'step' => 1,
					],
				],
			]
		);

		$rotate->add_option(
			'rotateY',
			[
				'type'        => 'dynamic_slider',
				'title'       => __( 'Rotate Y', 'zionbuilder-pro' ),
				'description' => __( 'Set rotation property for Y dimension.', 'zionbuilder-pro' ),
				'options'     => [
					[
						'unit' => 'deg',
						'min'  => -360,
						'max'  => 360,
						'step' => 1,
					],
				],
			]
		);

		$rotate->add_option(
			'rotateZ',
			[
				'type'         => 'dynamic_slider',
				'default_unit' => 'deg',
				'title'        => __( 'Rotate Z', 'zionbuilder-pro' ),
				'description'  => __( 'Set rotation property for Z dimension.', 'zionbuilder-pro' ),
				'options'      => [
					[
						'unit' => 'deg',
						'min'  => -360,
						'max'  => 360,
						'step' => 1,
					],
				],
			]
		);

		$skew = $transform->add_option(
			'skew',
			[
				'type'           => 'group',
				'options-layout' => 'full',
				'dependency'     => [
					[
						'option' => 'property',
						'value'  => [ 'skew' ],
					],
				],
			]
		);

		$skew->add_option(
			'skewX',
			[
				'type'         => 'dynamic_slider',
				'title'        => __( 'Skew X', 'zionbuilder-pro' ),
				'description'  => __( 'Set skew property for X dimension.', 'zionbuilder-pro' ),
				'default_unit' => 'deg',
				'options'      => [
					[
						'unit' => 'deg',
						'min'  => -180,
						'max'  => 180,
						'step' => 1,
					],
				],
			]
		);

		$skew->add_option(
			'skewY',
			[
				'type'         => 'dynamic_slider',
				'title'        => __( 'Skew Y', 'zionbuilder-pro' ),
				'description'  => __( 'Set skew property for Y dimension.', 'zionbuilder-pro' ),
				'default_unit' => 'deg',
				'options'      => [
					[
						'unit' => 'deg',
						'min'  => -180,
						'max'  => 180,
						'step' => 1,
					],
				],
			]
		);

		$perspective = $transform->add_option(
			'perspective',
			[
				'type'           => 'group',
				'options-layout' => 'full',
				'dependency'     => [
					[
						'option' => 'property',
						'value'  => [ 'perspective' ],
					],
				],
			]
		);

		$perspective->add_option(
			'perspective_value',
			[
				'type'        => 'dynamic_slider',
				'title'       => __( 'Perspective value', 'zionbuilder-pro' ),
				'description' => __( 'Set perspective property.', 'zionbuilder-pro' ),
				'options'     => [
					[
						'unit' => 'px',
						'min'  => 0,
						'max'  => 999,
						'step' => 1,
					],
					[
						'unit' => 'pt',
						'min'  => -999,
						'max'  => 999,
						'step' => 1,
					],
					[
						'unit' => 'em',
						'min'  => -999,
						'max'  => 999,
						'step' => 1,
					],
					[
						'unit' => 'rem',
						'min'  => -999,
						'max'  => 999,
						'step' => 1,
					],
				],
			]
		);

		$perspective->add_option(
			'perspective_origin_x_axis',
			[
				'type'        => 'dynamic_slider',
				'title'       => __( 'Perspective origin x-axis', 'zionbuilder-pro' ),
				'description' => __( 'Set perspective property.', 'zionbuilder-pro' ),
				'options'     => [
					[
						'unit' => '%',
						'min'  => -100,
						'max'  => 100,
						'step' => 10,
					],
					[
						'unit' => 'left',
					],
					[
						'unit' => 'center',
					],
					[
						'unit' => 'right',
					],
				],
				'default'     => '50%',
			]
		);

		$perspective->add_option(
			'perspective_origin_y_axis',
			[
				'type'        => 'dynamic_slider',
				'title'       => __( 'Perspective origin y-axis', 'zionbuilder-pro' ),
				'description' => __( 'Set perspective property.', 'zionbuilder-pro' ),
				'options'     => [
					[
						'unit' => '%',
						'min'  => -100,
						'max'  => 100,
						'step' => 10,
					],
					[
						'unit' => 'left',
					],
					[
						'unit' => 'center',
					],
					[
						'unit' => 'right',
					],
				],
				'default'     => '50%',
			]
		);

		$options->add_option(
			'perspective',
			[
				'type'        => 'dynamic_slider',
				'id'          => 'perspective',
				'title'       => __( 'Perspective', 'zionbuilder-pro' ),
				'description' => __( 'Set perspective property.', 'zionbuilder-pro' ),
				'options'     => [
					[
						'unit' => 'px',
						'min'  => 0,
						'max'  => 5000,
						'step' => 10,
					],
					[
						'unit' => 'initial',
					],
					[
						'unit' => 'inherit',
					],
					[
						'unit' => 'unset',
					],
				],
			]
		);

		$options->add_option(
			'transform_origin_x_axis',
			[
				'type'        => 'dynamic_slider',
				'id'          => 'transform_origin_x_axis',
				'title'       => __( 'Transform origin X axis', 'zionbuilder-pro' ),
				'description' => __( 'Set horizontal position of the transform origin', 'zionbuilder-pro' ),
				'options'     => [
					[
						'unit' => '%',
						'min'  => -100,
						'max'  => 100,
						'step' => 1,
					],
					[
						'unit' => 'left',
					],
					[
						'unit' => 'center',
					],
					[
						'unit' => 'right',
					],
				],
				'default'     => '50%',
			]
		);

		$options->add_option(
			'transform_origin_y_axis',
			[
				'type'        => 'dynamic_slider',
				'id'          => 'transform_origin_y_axis',
				'title'       => __( 'Transform origin Y axis', 'zionbuilder-pro' ),
				'description' => __( 'Set vertical position of the transform origin', 'zionbuilder-pro' ),
				'options'     => [
					[
						'unit' => '%',
						'min'  => -100,
						'max'  => 100,
						'step' => 1,
					],
					[
						'unit' => 'left',
					],
					[
						'unit' => 'center',
					],
					[
						'unit' => 'right',
					],
				],
				'default'     => '50%',
			]
		);

		$options->add_option(
			'transform_origin_z_axis',
			[
				'type'        => 'dynamic_slider',
				'id'          => 'transform_origin_z_axis',
				'title'       => __( 'Transform origin Z axis', 'zionbuilder-pro' ),
				'description' => __( 'Set the Z offset of the transform origin', 'zionbuilder-pro' ),
				'options'     => [
					[
						'unit' => 'px',
						'min'  => -100,
						'max'  => 100,
						'step' => 1,
					],
				],

			]
		);

		$options->add_option(
			'transform_style',
			[
				'type'        => 'select',
				'id'          => 'transform_style',
				'title'       => __( 'Transform style', 'zionbuilder-pro' ),
				'description' => __( 'Specifies that child elements will preserve its 3D position', 'zionbuilder-pro' ),
				'default'     => 'flat',
				'options'     => [
					[
						'id'   => 'flat',
						'name' => __( 'Flat', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'preserve-3d',
						'name' => __( 'Preserve 3d', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'initial',
						'name' => __( 'Initial', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'inherit',
						'name' => __( 'Inherit', 'zionbuilder-pro' ),
					],
				],

			]
		);

		$origin = $transform->add_option(
			'transform-origin',
			[
				'type'           => 'group',
				'options-layout' => 'full',
				'dependency'     => [
					[
						'option' => 'property',
						'value'  => [ 'transform-origin' ],
					],
				],
			]
		);
		$origin->add_option(
			'x_axis',
			[
				'type'        => 'dynamic_slider',
				'id'          => 'x_axis',
				'title'       => __( 'X axis', 'zionbuilder-pro' ),
				'description' => __( 'Set X axis origin property.', 'zionbuilder-pro' ),
				'options'     => [
					[
						'unit' => 'px',
						'min'  => -1000,
						'max'  => 1000,
						'step' => 1,
					],
					[
						'unit' => '%',
						'min'  => -100,
						'max'  => 100,
						'step' => 1,
					],
					[
						'unit' => 'left',
					],
					[
						'unit' => 'center',
					],
					[
						'unit' => 'right',
					],
				],
			]
		);
		$origin->add_option(
			'y_axis',
			[
				'type'        => 'dynamic_slider',
				'id'          => 'y_axis',
				'title'       => __( 'Y axis', 'zionbuilder-pro' ),
				'description' => __( 'Set Y axis origin property.', 'zionbuilder-pro' ),
				'options'     => [
					[
						'unit' => 'px',
						'min'  => -1000,
						'max'  => 1000,
						'step' => 1,
					],
					[
						'unit' => '%',
						'min'  => -100,
						'max'  => 100,
						'step' => 1,
					],
					[
						'unit' => 'top',
					],
					[
						'unit' => 'center',
					],
					[
						'unit' => 'bottom',
					],
				],
			]
		);
		$origin->add_option(
			'z_axis',
			[
				'type'        => 'dynamic_slider',
				'id'          => 'z_axis',
				'title'       => __( 'Z axis', 'zionbuilder-pro' ),
				'description' => __( 'Set Z axis origin property.', 'zionbuilder-pro' ),
				'options'     => [
					[
						'unit' => 'px',
						'min'  => -1000,
						'max'  => 1000,
						'step' => 1,
					],
				],
			]
		);
	}


	public static function attach_display_options( $options ) {
		$display_group = $options->add_group(
			'display-group',
			[
				'type'  => 'panel_accordion',
				'title' => __( 'Display options', 'zionbuilder-pro' ),
			]
		);

		$display_group->add_option(
			'display',
			[
				'type'        => 'custom_selector',
				'title'       => __( 'Display', 'zionbuilder-pro' ),
				'description' => __( 'Display css properties', 'zionbuilder-pro' ),
				'columns'     => 3,
				'search_tags' => [ 'flex', 'block', 'inline', 'none' ],
				'options'     => [
					[
						'name' => __( 'flex', 'zionbuilder-pro' ),
						'id'   => 'flex',
					],
					[
						'name' => __( 'block', 'zionbuilder-pro' ),
						'id'   => 'block',
					],
					[
						'name' => __( 'inline', 'zionbuilder-pro' ),
						'id'   => 'inline',
					],
					[
						'name' => __( 'inline-flex', 'zionbuilder-pro' ),
						'id'   => 'inline-flex',
					],
					[
						'name' => __( 'inline-block', 'zionbuilder-pro' ),
						'id'   => 'inline-block',
					],
					[
						'name' => __( 'none', 'zionbuilder-pro' ),
						'id'   => 'none',
					],
				],
			]
		);

		$display_group->add_option(
			'visibility',
			[
				'type'        => 'custom_selector',
				'title'       => __( 'Visibility', 'zionbuilder-pro' ),
				'description' => __( 'Set visibility option for element', 'zionbuilder-pro' ),
				'columns'     => 2,
				'options'     => [
					[
						'name' => 'visible',
						'id'   => 'visible',
					],
					[
						'name' => 'hidden',
						'id'   => 'hidden',
					],
				],
			]
		);

		$display_group->add_option(
			'overflow',
			[
				'type'        => 'custom_selector',
				'title'       => __( 'Overflow', 'zionbuilder-pro' ),
				'description' => __( 'Set overflow for element.', 'zionbuilder-pro' ),
				'columns'     => 3,
				'options'     => [
					[
						'name' => 'visible',
						'id'   => 'visible',
					],
					[
						'name' => 'hidden',
						'id'   => 'hidden',
					],
					[
						'name' => 'auto',
						'id'   => 'auto',
					],
				],
			]
		);

		$display_group->add_option(
			'backface-visibility',
			[
				'type'        => 'select',
				'title'       => __( 'Backface Visibility', 'zionbuilder-pro' ),
				'description' => __( 'The backface-visibility property defines whether or not the back face of an element should be visible when facing the user. ', 'zionbuilder-pro' ),
				'options'     => [
					[
						'name' => 'visible',
						'id'   => 'visible',
					],
					[
						'name' => 'hidden',
						'id'   => 'hidden',
					],
					[
						'name' => 'initial',
						'id'   => 'initial',
					],
					[
						'name' => 'inherit',
						'id'   => 'inherit',
					],
				],
			]
		);

		$flex_container_group = $options->add_group(
			'flexbox-container-group',
			[
				'type'      => 'panel_accordion',
				'title'     => __( 'Flexbox container options', 'zionbuilder-pro' ),
				// 'dependency'  => [
				//  [
				//      'option' => 'display',
				//      'value'  => [ 'flex', 'inline-flex' ],
				//  ],
				// ],
				'collapsed' => true,
			]
		);

		$flex_container_group->add_option(
			'gap',
			[
				'type'         => 'number_unit',
				'title'        => __( 'Gap', 'zionbuilder-pro' ),
				'description'  => __( 'The gap option allows you to specify the gap between child elements on the main axis. Current browser support is 89.92%. In order to increase browser support, you can use margins.', 'zionbuilder-pro' ),
				'placeholder'  => '0px',
				'default_unit' => 'px',
			]
		);

		$flex_container_group->add_option(
			'flex-direction',
			[
				'type'    => 'custom_selector',
				'width'   => 60,
				'options' => [
					[
						'name' => __( 'vertical', 'zionbuilder-pro' ),
						'id'   => 'column',
					],
					[
						'name' => __( 'horizontal', 'zionbuilder-pro' ),
						'id'   => 'row',
					],
				],
				'title'   => __( 'Flex direction', 'zionbuilder-pro' ),
			]
		);

		$flex_container_group->add_option(
			'flex-reverse',
			[
				'type'    => 'custom_selector',
				'width'   => 40,
				'options' => [
					[
						'name' => __( 'flex-reverse', 'zionbuilder-pro' ),
						'icon' => 'reverse',
						'id'   => true,
					],
				],
				'title'   => __( 'Flex reverse', 'zionbuilder-pro' ),
			]
		);

		$flex_container_group->add_option(
			'align-items',
			[
				'type'        => 'custom_selector',
				'title'       => __( 'Align items', 'zionbuilder-pro' ),
				'description' => __( 'Set align items', 'zionbuilder-pro' ),
				'options'     => [
					[
						'name' => __( 'flex-start', 'zionbuilder-pro' ),
						'id'   => 'flex-start',
						'icon' => 'align-start',
					],
					[
						'name' => __( 'center', 'zionbuilder-pro' ),
						'id'   => 'center',
						'icon' => 'align-center',
					],
					[
						'name' => __( 'flex-end', 'zionbuilder-pro' ),
						'id'   => 'flex-end',
						'icon' => 'align-end',
					],
					[
						'name' => __( 'stretch', 'zionbuilder-pro' ),
						'id'   => 'stretch',
						'icon' => 'align-stretch',
					],
					[
						'name' => __( 'baseline', 'zionbuilder-pro' ),
						'id'   => 'baseline',
						'icon' => 'align-baseline',
					],
				],
			]
		);

		$flex_container_group->add_option(
			'justify-content',
			[
				'type'        => 'custom_selector',
				'title'       => __( 'Justify', 'zionbuilder-pro' ),
				'description' => __( 'Set float option for element', 'zionbuilder-pro' ),
				'columns'     => 5,
				'options'     => [
					[
						'name' => __( 'flex-start', 'zionbuilder-pro' ),
						'id'   => 'flex-start',
						'icon' => 'justify-start',
					],
					[
						'name' => __( 'center', 'zionbuilder-pro' ),
						'id'   => 'center',
						'icon' => 'justify-center',
					],
					[
						'name' => __( 'flex-end', 'zionbuilder-pro' ),
						'id'   => 'flex-end',
						'icon' => 'justify-end',
					],
					[
						'name' => __( 'space-between', 'zionbuilder-pro' ),
						'id'   => 'space-between',
						'icon' => 'justify-sp-btw',
					],
					[
						'name' => __( 'space-around', 'zionbuilder-pro' ),
						'id'   => 'space-around',
						'icon' => 'justify-sp-around',
					],
				],
			]
		);

		$flex_container_group->add_option(
			'flex-wrap',
			[
				'type'        => 'custom_selector',
				'grow'        => '5',
				'title'       => __( 'Wrap', 'zionbuilder-pro' ),
				'description' => __( 'Set wrap for element', 'zionbuilder-pro' ),
				'options'     => [
					[
						'name' => __( 'wrap', 'zionbuilder-pro' ),
						'id'   => 'wrap',
					],
					[
						'name' => __( 'nowrap', 'zionbuilder-pro' ),
						'id'   => 'nowrap',
					],
					[
						'name' => __( 'wrap-reverse', 'zionbuilder-pro' ),
						'id'   => 'wrap-reverse',
						'icon' => 'reverse',
					],
				],
			]
		);

		$flex_container_group->add_option(
			'align-content',
			[
				'type'        => 'custom_selector',
				'title'       => __( 'Align content', 'zionbuilder-pro' ),
				'description' => __( 'Set align content', 'zionbuilder-pro' ),
				'columns'     => 5,
				'options'     => [
					[
						'name' => __( 'flex-start', 'zionbuilder-pro' ),
						'id'   => 'flex-start',
						'icon' => 'content-start',
					],
					[
						'name' => __( 'center', 'zionbuilder-pro' ),
						'id'   => 'center',
						'icon' => 'content-center',
					],
					[
						'name' => __( 'flex-end', 'zionbuilder-pro' ),
						'id'   => 'flex-end',
						'icon' => 'content-end',
					],
					[
						'name' => __( 'space-between', 'zionbuilder-pro' ),
						'id'   => 'space-between',
						'icon' => 'content-space-btw',
					],
					[
						'name' => __( 'space-around', 'zionbuilder-pro' ),
						'id'   => 'space-around',
						'icon' => 'content-space-around',
					],
					[
						'name' => __( 'strech', 'zionbuilder-pro' ),
						'id'   => 'stretch',
						'icon' => 'content-stretch',
					],
				],
			]
		);

		$flex_child_group = $options->add_group(
			'flexbox-child-group',
			[
				'type'      => 'panel_accordion',
				'title'     => __( 'Flexbox child options', 'zionbuilder-pro' ),
				'collapsed' => true,
			]
		);

		$flex_child_group->add_option(
			'flex-grow',
			[
				'type'  => 'number',
				'width' => 33.3,
				'title' => __( 'Flex Grow', 'zionbuilder-pro' ),
			]
		);

		$flex_child_group->add_option(
			'flex-shrink',
			[
				'type'  => 'number',
				'width' => 33.3,
				'title' => __( 'Flex Shrink', 'zionbuilder-pro' ),
			]
		);

		$flex_child_group->add_option(
			'flex-basis',
			[
				'type'         => 'number_unit',
				'width'        => 33.3,
				'title'        => __( 'Flex Basis', 'zionbuilder-pro' ),
				'default_unit' => 'px',
			]
		);

		$flex_child_group->add_option(
			'align-self',
			[
				'type'        => 'custom_selector',
				'title'       => __( 'Align self', 'zionbuilder-pro' ),
				'description' => __( 'Set align self', 'zionbuilder-pro' ),
				'options'     => [
					[
						'name' => __( 'flex-start', 'zionbuilder-pro' ),
						'id'   => 'flex-start',
						'icon' => 'self-start',
					],
					[
						'name' => __( 'center', 'zionbuilder-pro' ),
						'id'   => 'center',
						'icon' => 'self-center',
					],
					[
						'name' => __( 'flex-end', 'zionbuilder-pro' ),
						'id'   => 'flex-end',
						'icon' => 'self-end',
					],
					[
						'name' => __( 'stretch', 'zionbuilder-pro' ),
						'id'   => 'stretch',
						'icon' => 'self-stretch',
					],
					[
						'name' => __( 'baseline', 'zionbuilder-pro' ),
						'id'   => 'baseline',
						'icon' => 'self-baseline',
					],
				],
			]
		);

		$flex_child_group->add_option(
			'custom-order',
			[
				'type'    => 'custom_selector',
				'title'   => __( 'Order', 'zionbuilder-pro' ),
				'width'   => 60,
				'options' => [
					[
						'name' => __( 'first', 'zionbuilder-pro' ),
						'id'   => -1,
					],
					[
						'name' => __( 'last', 'zionbuilder-pro' ),
						'id'   => 99,
					],
				],
			]
		);

		$flex_child_group->add_option(
			'order',
			[
				'type'  => 'number',
				'title' => __( 'Custom Order', 'zionbuilder-pro' ),
				'width' => 40,
			]
		);

		$position_group = $options->add_group(
			'position-group',
			[
				'type'      => 'panel_accordion',
				'title'     => __( 'Position options', 'zionbuilder-pro' ),
				'collapsed' => true,
			]
		);

		$position_group->add_option(
			'position',
			[
				'type'        => 'custom_selector',
				'title'       => __( 'Position', 'zionbuilder-pro' ),
				'description' => __( 'Set element position', 'zionbuilder-pro' ),
				'columns'     => 4,
				'options'     => [
					[
						'name' => __( 'static', 'zionbuilder-pro' ),
						'id'   => 'static',
					],
					[
						'name' => __( 'relative', 'zionbuilder-pro' ),
						'id'   => 'relative',
					],
					[
						'name' => __( 'absolute', 'zionbuilder-pro' ),
						'id'   => 'absolute',
					],
					[
						'name' => __( 'fixed', 'zionbuilder-pro' ),
						'id'   => 'fixed',
					],
					[
						'name' => __( 'sticky', 'zionbuilder-pro' ),
						'id'   => 'sticky',
					],
				],
			]
		);

		$position_group->add_option(
			'top',
			[
				'type'         => 'number_unit',
				'title'        => __( 'Top', 'zionbuilder-pro' ),
				'placeholder'  => '0px',
				'width'        => '25',
				'default_unit' => 'px',
			]
		);

		$position_group->add_option(
			'bottom',
			[
				'type'         => 'number_unit',
				'title'        => __( 'Bottom', 'zionbuilder-pro' ),
				'placeholder'  => '0px',
				'width'        => '25',
				'default_unit' => 'px',
			]
		);

		$position_group->add_option(
			'left',
			[
				'type'         => 'number_unit',
				'title'        => __( 'Left', 'zionbuilder-pro' ),
				'placeholder'  => '0px',
				'width'        => '25',
				'default_unit' => 'px',
			]
		);

		$position_group->add_option(
			'right',
			[
				'type'         => 'number_unit',
				'title'        => __( 'Right', 'zionbuilder-pro' ),
				'placeholder'  => '0px',
				'width'        => '25',
				'default_unit' => 'px',
			]
		);

		$object_fit = $options->add_group(
			'object-fit',
			[
				'type'      => 'panel_accordion',
				'title'     => __( 'Object fit', 'zionbuilder-pro' ),
				'collapsed' => true,
			]
		);

		// Object fit
		$object_fit->add_option(
			'object-fit',
			[
				'type'        => 'select',
				'title'       => __( 'Object fit', 'zionbuilder-pro' ),
				'description' => __( 'Object fit is used to specify how an <img> or <video> should be resized to fit its container.', 'zionbuilder-pro' ),
				'default'     => 'fill',
				'options'     => [
					[
						'id'   => 'fill',
						'name' => __( 'fill', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'contain',
						'name' => __( 'contain', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'cover',
						'name' => __( 'cover', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'none',
						'name' => __( 'none', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'scale-down',
						'name' => __( 'scale-down', 'zionbuilder-pro' ),
					],
				],
			]
		);

		$object_fit->add_option(
			'object-position',
			[
				'type'        => 'text',
				'title'       => __( 'Object fit position', 'zionbuilder-pro' ),
				'description' => __( 'Object fit is used to specify how an <img> or <video> should be resized to fit its container.', 'zionbuilder-pro' ),
			]
		);

		$floating_group = $options->add_group(
			'floating-group',
			[
				'type'      => 'panel_accordion',
				'title'     => __( 'Floating options', 'zionbuilder-pro' ),
				'collapsed' => true,
			]
		);

		$floating_group->add_option(
			'float',
			[
				'type'        => 'custom_selector',
				'title'       => __( 'Float', 'zionbuilder-pro' ),
				'description' => __( 'Set float option for element', 'zionbuilder-pro' ),
				'columns'     => 3,
				'options'     => [
					[
						'name' => __( 'left', 'zionbuilder-pro' ),
						'id'   => 'left',
					],
					[
						'name' => __( 'right', 'zionbuilder-pro' ),
						'id'   => 'right',
					],
					[
						'name' => __( 'none', 'zionbuilder-pro' ),
						'id'   => 'none',
					],
				],
			]
		);

		$floating_group->add_option(
			'clear',
			[
				'type'        => 'custom_selector',
				'title'       => __( 'Clear', 'zionbuilder-pro' ),
				'description' => __( 'Set clear option for element', 'zionbuilder-pro' ),
				'columns'     => 3,
				'options'     => [

					[
						'name' => 'left',
						'id'   => 'left',
					],
					[
						'name' => 'right',
						'id'   => 'right',
					],
					[
						'name' => 'both',
						'id'   => 'both',
					],
				],
			]
		);
	}


	public function attach_filters_options( $options ) {
		$filters_group = $options->add_group(
			'filters-group',
			[
				'type'      => 'panel_accordion',
				'title'     => __( 'Filter options', 'zionbuilder-pro' ),
				'collapsed' => false,
			]
		);

		// Mix blend mode
		$filters_group->add_option(
			'mix-blend-mode',
			[
				'type'        => 'select',
				'title'       => __( 'Mix Blend Mode', 'zionbuilder-pro' ),
				'description' => __( 'Set the desired mix blend mode.', 'zionbuilder-pro' ),
				'default'     => 'normal',
				'options'     => [
					[
						'id'   => 'normal',
						'name' => __( 'normal', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'multiply',
						'name' => __( 'multiply', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'screen',
						'name' => __( 'screen', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'overlay',
						'name' => __( 'overlay', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'darken',
						'name' => __( 'darken', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'lighten',
						'name' => __( 'lighten', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'color-dodge',
						'name' => __( 'color-dodge', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'color-burn',
						'name' => __( 'color-burn', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'hard-light',
						'name' => __( 'hard-light', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'soft-light',
						'name' => __( 'soft-light', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'difference',
						'name' => __( 'difference', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'exclusion',
						'name' => __( 'exclusion', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'hue',
						'name' => __( 'hue', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'saturation',
						'name' => __( 'saturation', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'color',
						'name' => __( 'color', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'luminosity',
						'name' => __( 'luminosity', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'initial',
						'name' => __( 'initial', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'inherit',
						'name' => __( 'inherit', 'zionbuilder-pro' ),
					],
					[
						'id'   => 'unset',
						'name' => __( 'unset', 'zionbuilder-pro' ),
					],
				],
			]
		);

		$filters_group->add_option(
			'grayscale',
			[
				'type'        => 'slider',
				'title'       => __( 'Grayscale', 'zionbuilder-pro' ),
				'description' => __( 'Set the desired grayscale css filter.', 'zionbuilder-pro' ),
				'default'     => 0,
				'min'         => 0,
				'max'         => 100,
				'content'     => '%',
			]
		);

		$filters_group->add_option(
			'sepia',
			[
				'type'        => 'slider',
				'title'       => __( 'Sepia', 'zionbuilder-pro' ),
				'description' => __( 'Set the desired sepia css filter.', 'zionbuilder-pro' ),
				'default'     => 0,
				'min'         => 0,
				'max'         => 100,
				'content'     => '%',
			]
		);

		$filters_group->add_option(
			'blur',
			[
				'type'        => 'slider',
				'title'       => __( 'Blur', 'zionbuilder-pro' ),
				'description' => __( 'Set the desired blur css filter.', 'zionbuilder-pro' ),
				'default'     => 0,
				'min'         => 0,
				'max'         => 200,
				'content'     => 'px',
			]
		);

		$filters_group->add_option(
			'brightness',
			[
				'type'        => 'slider',
				'title'       => __( 'Brightness', 'zionbuilder-pro' ),
				'description' => __( 'Set the desired brightness css filter.', 'zionbuilder-pro' ),
				'default'     => 100,
				'min'         => 0,
				'max'         => 100,
				'content'     => '%',
			]
		);

		$filters_group->add_option(
			'hue-rotate',
			[
				'type'        => 'slider',
				'title'       => __( 'Hue Rotate', 'zionbuilder-pro' ),
				'description' => __( 'Set the desired hue rotate css filter.', 'zionbuilder-pro' ),
				'default'     => 0,
				'min'         => 0,
				'max'         => 360,
				'content'     => 'deg',
			]
		);

		$filters_group->add_option(
			'saturate',
			[
				'type'        => 'slider',
				'title'       => __( 'Saturate', 'zionbuilder-pro' ),
				'description' => __( 'Set the desired saturate css filter.', 'zionbuilder-pro' ),
				'default'     => 100,
				'min'         => 0,
				'max'         => 200,
				'content'     => '%',
			]
		);

		$filters_group->add_option(
			'opacity',
			[
				'type'        => 'slider',
				'title'       => __( 'Opacity', 'zionbuilder-pro' ),
				'description' => __( 'Set the desired opacity for the element.', 'zionbuilder-pro' ),
				'default'     => 100,
				'min'         => 0,
				'max'         => 100,
				'content'     => '%',
			]
		);

		$filters_group->add_option(
			'contrast',
			[
				'type'        => 'slider',
				'title'       => __( 'Contrast', 'zionbuilder-pro' ),
				'description' => __( 'Set the desired contrast for the element.', 'zionbuilder-pro' ),
				'default'     => 100,
				'min'         => 0,
				'max'         => 100,
				'content'     => '%',
			]
		);

		$filters_group->add_option(
			'invert',
			[
				'type'        => 'slider',
				'title'       => __( 'Invert', 'zionbuilder-pro' ),
				'description' => __( 'Set the desired invert filter for the element.', 'zionbuilder-pro' ),
				'default'     => 0,
				'min'         => 0,
				'max'         => 100,
				'content'     => '%',
			]
		);
	}


	public function attach_transitions_options( $options ) {
		// Transition property
		$options->add_option(
			'transition-property',
			[
				'type'        => 'text',
				'title'       => __( 'Transition property', 'zionbuilder-pro' ),
				'description' => __( 'Add desired transition properties separated by comma', 'zionbuilder-pro' ),
				'placeholder' => __( 'all', 'zionbuilder-pro' ),
				'dynamic'     => false,
			]
		);

		// Transition duration
		$options->add_option(
			'transition-duration',
			[
				'type'        => 'slider',
				'title'       => __( 'Transition Duration', 'zionbuilder-pro' ),
				'description' => __( 'Set the desired transition duration.', 'zionbuilder-pro' ),
				'default'     => 0,
				'min'         => 0,
				'max'         => 10000,
				'step'        => 50,
				'content'     => 'ms',
			]
		);

		// Transition delay
		$options->add_option(
			'transition-delay',
			[
				'type'        => 'slider',
				'title'       => __( 'Transition Delay', 'zionbuilder-pro' ),
				'description' => __( 'Set the desired transition delay.', 'zionbuilder-pro' ),
				'default'     => 0,
				'min'         => 0,
				'max'         => 10000,
				'step'        => 50,
				'content'     => 'ms',
			]
		);

		// Transition timing
		$options->add_option(
			'transition-timing-function',
			[
				'type'        => 'select',
				'default'     => 'linear',
				'title'       => __( 'Timing function', 'zionbuilder-pro' ),
				'description' => __( 'Set the desired timing function for the transition. Start typing to add a Custom transition', 'zionbuilder-pro' ),
				'options'     => [
					[
						'name' => __( 'linear', 'zionbuilder-pro' ),
						'id'   => 'linear',
					],
					[
						'name' => __( 'ease', 'zionbuilder-pro' ),
						'id'   => 'ease',
					],
					[
						'name' => __( 'ease-in', 'zionbuilder-pro' ),
						'id'   => 'ease-in',
					],
					[
						'name' => __( 'ease-out', 'zionbuilder-pro' ),
						'id'   => 'ease-out',
					],
					[
						'name' => __( 'ease-in-out', 'zionbuilder-pro' ),
						'id'   => 'ease-in-out',
					],
				],
				'filterable'  => true,
				'addable'     => true,
			]
		);
	}

	public function enqueue_preview_scripts() {
		wp_enqueue_style(
			'zion-pro-editor-style',
			Utils::get_file_url( 'dist/editor.css' ),
			[],
			Plugin::instance()->get_version()
		);
	}

	public function enqueue_scripts() {
		wp_enqueue_style(
			'zion-pro-editor-style',
			Utils::get_file_url( 'dist/editor.css' ),
			[ 'zion-editor-style' ],
			Plugin::instance()->get_version()
		);

		wp_enqueue_script(
			'zb-editor-pro',
			Utils::get_file_url( 'dist/editor.js' ),
			[
				'zb-editor',
			],
			Plugin::instance()->get_version(),
			true
		);

		wp_localize_script(
			'zb-editor-pro',
			'ZionProRestConfig',
			[
				'nonce'     => wp_create_nonce( 'wp_rest' ),
				'rest_root' => esc_url_raw( rest_url() ),
			]
		);

		wp_localize_script( 'zb-editor-pro', 'ZionBuilderProInitialData', $this->get_editor_initial_data() );
	}

	private function get_editor_initial_data() {
		return apply_filters(
			'zionbuilderpro/editor/initial_data',
			[
				'dynamic_fields_data' => Plugin::$instance->dynamic_content_manager->get_fields_data(),
				'dynamic_fields_info' => Plugin::$instance->dynamic_content_manager->get_fields_for_editor(),
				'license_details'     => License::get_license_details(),
				'license_key'         => License::get_license_key(),
			]
		);
	}
}
