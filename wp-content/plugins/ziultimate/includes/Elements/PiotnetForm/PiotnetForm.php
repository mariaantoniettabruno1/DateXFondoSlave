<?php
namespace ZiUltimate\Elements\PiotnetForm;

use ZiUltimate\UltimateElements;
use ZiUltimate\Utils;
use ZiUltimate\Admin\License;
use ZionBuilder\Options\BaseSchema;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class PiotnetForm
 *
 * @package ZiUltimate\Elements
 */
class PiotnetForm extends UltimateElements {
	
	public function get_type() {
		return 'zu_pf';
	}

	public function get_name() {
		return __( 'Piotnet Forms Styler', 'ziultimate' );
	}

	public function get_keywords() {
		return [ 'pf', 'piotnet', 'form', 'piotnetform', 'styler' ];
	}

	public function get_element_icon() {
		return 'element-form';
	}

	/*public function get_label() {
		return [
			'text'  => $this->get_label_text(),
			'color' => $this->get_label_color(),
		];
	}*/

	public function get_category() {
		return $this->zu_elements_category();
	}

	/**
	 * Registering the options form
	 * 
	 * @return void
	 */
	public function options( $options ) {
		if( ! License::has_valid_license() ) {
			$title = $this->get_name();
			$description = 'You can directly customize your piotnet form style on builder editor.';
			$options->add_option(
				'el',
				[
					'type' 		=> 'html',
					'content' 	=> self::getHTMLContent($title, $description)
				]
			);

			return;
		}

		$options->add_option(
			'source_type',
			[
				'type' 		=> 'custom_selector',
				'title' 	=> esc_html__( 'Source Type', 'ziultimate' ),
				'default' 	=> 'static',
				'options' 	=> [
					[
						'name' => 'Static',
						'id'   => 'static',
					],
					[
						'name' => 'Custom Field',
						'id'   => 'dynamic',
					]
				],
			]
		);

		$options->add_option(
			'pf_form',
			[
				'type' 			=> 'select',
				'default' 		=> -1,
				'title' 		=> __( 'Piotnet Form', 'ziultimate' ),
				'description' 	=> __( "Make sure that you build at least one piotnet form.", 'ziultimate' ),
				'options' 		=> self::getPiotnetForms(),
				'dependency'		=> [
					[
						'option' 	=> 'source_type',
						'value' 	=> [ 'static' ]
					]
				]
			]
		);

		$options->add_option(
			'pf_dymc_form',
			[
				'type' 			=> 'text',
				'title' 		=> __( 'Form ID', 'ziultimate' ),
				'description' 	=> __( "Make sure that it returns the form ID.", 'ziultimate' ),
				'dependency'		=> [
					[
						'option' 	=> 'source_type',
						'value' 	=> [ 'dynamic' ]
					]
				],
				'dynamic'     	=> [
					'enabled' => true,
				]
			]
		);

		$options->add_option(
			'pf_textarea',
			[
				'type' 			=> 'number_unit',
				'units' 		=> BaseSchema::get_units(),
				'title' 		=> __( 'Textarea Height', 'ziultimate' ),
				'responsive_options' => true,
				'css_style' 	=> [
					[
						'selector' => '{{ELEMENT}} #piotnetforms .piotnetforms-field-type-textarea textarea.piotnetforms-field',
						'value'    => 'height: {{VALUE}}',
					],
				],
			]
		);

		/**
		 * Dropdown arrow group
		 */
		$selectArrow = $options->add_group(
			'select_arrow',
			[
				'type' 		=> 'panel_accordion',
				'title' 	=> esc_html__( 'Dropdown Arrow', 'ziultimate'),
				'collapsed' => true
			]
		);

		$selectArrow->add_option(
			'arrow_color',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> esc_html__( 'Color' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} #piotnetforms .piotnetforms-select-wrapper:before',
						'value' 	=> 'color: {{VALUE}}'
					]
				]
			]
		);

		$selectArrow->add_option(
			'arrow_size',
			[
				'type' 		=> 'number_unit',
				'units' 	=> BaseSchema::get_units(),
				'default' 	=> '8px',
				'title' 	=> esc_html__( 'Size' ),
				'width' 	=> 50,
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} #piotnetforms .piotnetforms-select-wrapper:before',
						'value' 	=> 'font-size: {{VALUE}}'
					]
				]
			]
		);

		$selectArrow->add_option(
			'arrow_pos_top',
			[
				'type' 			=> 'dynamic_slider',
				'default_step' 	=> 1,
				'responsive_options' => true,
				'title' 		=> esc_html__('Position Top', 'zionbuilder'),
				'default' 		=> [
					'default' => '50%',
				],
				'options'            => [
					[
						'min'        => 0,
						'max'        => 100,
						'step'       => 1,
						'shift_step' => 1,
						'unit'       => '%',
					],
					[
						'min'        => 0,
						'max'        => 100,
						'step'       => 1,
						'shift_step' => 1,
						'unit'       => 'px',
					]
				],
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} #piotnetforms .piotnetforms-select-wrapper:before',
						'value' 	=> 'top: {{VALUE}}'
					]
				]
			]
		);

		$selectArrow->add_option(
			'arrow_pos_right',
			[
				'type' 		=> 'dynamic_slider',
				'title' 	=> esc_html__('Position Right', 'zionbuilder'),
				'responsive_options' => true,
				'default' 	=> [
					'default' => '10px',
				],
				'options'            => [
					[
						'min'        => 0,
						'max'        => 100,
						'step'       => 1,
						'shift_step' => 1,
						'unit'       => '%',
					],
					[
						'min'        => 0,
						'max'        => 100,
						'step'       => 1,
						'shift_step' => 1,
						'unit'       => 'px',
					]
				],
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} #piotnetforms .piotnetforms-select-wrapper:before',
						'value' 	=> 'right: {{VALUE}}'
					]
				]
			]
		);
	}

	private static function getPiotnetForms() {
		$forms = [
			[
				'id' 	=> -1,
				'name' 	=> esc_html__( 'Select a form', 'ziultimate' ),
			]
		];

		if ( class_exists( 'Piotnetforms' ) || class_exists( 'Piotnetforms_pro' ) ) {
			$args = array(
				'posts_per_page' 	=> -1,
				'orderby' 			=> 'date',
				'order' 			=> 'DESC',
				'post_type' 		=> 'piotnetforms',
				'post_status' 		=> 'publish'
			);

			$pforms = new \WP_Query($args);
			if( $pforms->have_posts() ) {
				$i=1;
				foreach( $pforms->posts as $form ) {
					$forms[$i]['id'] = $form->ID;
					$forms[$i]['name'] = wp_kses_post($form->post_title );
					$i++;
				}
			} else {
				$forms[1]['id'] = -1;
				$forms[1]['name'] = esc_html__( 'No forms found!', 'ziultimate' );
			}
		}

		return $forms;
	}

	/**
	 * Loading the styles
	 * 
	 * @return void
	 */
	public function enqueue_styles() {
		if( self::isBuilderEditor() && ( class_exists( 'Piotnetforms' ) || class_exists( 'Piotnetforms_pro' ) ) ) {
			wp_enqueue_style( 'piotnetforms-style' );
			wp_enqueue_style( 'piotnetforms-flatpickr-style' );
			wp_enqueue_style( 'piotnetforms-image-picker-style' );
			wp_enqueue_style( 'piotnetforms-rangeslider-style' );
			wp_enqueue_style( 'piotnetforms-selectize-style' );
			wp_enqueue_style( 'piotnetforms-fontawesome-style' );
			wp_enqueue_style( 'piotnetforms-jquery-ui' );
		}

		$this->enqueue_element_style( Utils::get_file_url( 'dist/css/elements/PiotnetForm/frontend.css' ) );
	}

	/**
	 * Loading the scripts
	 * 
	 * @return void
	 */
	public function enqueue_scripts() {
		if( self::isBuilderEditor() && ( class_exists( 'Piotnetforms' ) || class_exists( 'Piotnetforms_pro' ) ) ) {
			wp_enqueue_script( 'piotnetforms-script' );
			wp_enqueue_script( 'piotnetforms-flatpickr-script' );
			wp_enqueue_script( 'piotnetforms-image-picker-script' );
			wp_enqueue_script( 'piotnetforms-ion-rangeslider-script' );
			wp_enqueue_script( 'piotnetforms-selectize-script' );
			wp_enqueue_script( 'piotnetforms-nice-number-script' );
			wp_enqueue_script( 'piotnetforms-image-upload-script' );
			wp_enqueue_script( 'piotnetforms-advanced-script' );
			wp_enqueue_script( 'piotnetforms-multi-step-script' );
			wp_enqueue_script( 'piotnetforms-date-time-script' );
		}
	}

	/**
	 * Rendering the form
	 * 
	 * @return void
	 */
	public function render( $options ) {
		$source_type = $options->get_value('source_type', 'static');

		if( $source_type == 'dynamic' ) {
			$pf_id = $options->get_value('pf_dymc_form', false);
		} else {
			$pf_id = $options->get_value('pf_form', false);
		}

		if( ! $pf_id || $pf_id <= 0 ) {
			echo '<h5 class="form-missing">' . __("Select a form", 'ziultimate') . '</h5>';
			return;
		}

		if( class_exists( 'Piotnetforms' ) || class_exists( 'Piotnetforms_pro' ) ) {
			echo piotnetforms_shortcode( array( 'id' => $pf_id ), false );
		}
	}

	/**
	 * Registering the styles
	 * 
	 * @return void
	 */
	public function on_register_styles() {
		$this->register_style_options_element(
			'pf_el_label',
			[
				'title'                   => esc_html__( 'Fields Label', 'ziultimate' ),
				'selector'                => '{{ELEMENT}} #piotnetforms .piotnetforms-field-label',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'pf_el_label_icon',
			[
				'title'                   => esc_html__( 'Fields Label Icon', 'ziultimate' ),
				'selector'                => '{{ELEMENT}} #piotnetforms .piotnetforms-field-icon',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'input_field',
			[
				'title'                   => esc_html__( 'Input fields', 'ziultimate' ),
				'selector'                => '{{ELEMENT}} #piotnetforms .piotnetforms-field-group:not(.piotnetforms-field-type-upload) .piotnetforms-field:not(.piotnetforms-select-wrapper)',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'select_field',
			[
				'title'                   => esc_html__( 'Dropdown fields', 'ziultimate' ),
				'selector'                => '{{ELEMENT}} #piotnetforms .piotnetforms-fields-wrapper select',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'pf_el_button',
			[
				'title'                   => esc_html__( 'Button', 'ziultimate' ),
				'selector'                => '{{ELEMENT}} #piotnetforms .piotnetforms-btn a',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'pf_el_submit_button',
			[
				'title'                   => esc_html__( 'Submit Button', 'ziultimate' ),
				'selector'                => '{{ELEMENT}} #piotnetforms .piotnetforms-button',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'icon_field',
			[
				'title'                   => esc_html__( 'Icon Field', 'ziultimate' ),
				'selector'                => '{{ELEMENT}} #piotnetforms .piotnetforms-icon__item i',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'img_field_wrapper',
			[
				'title'                   => esc_html__( 'Image Field Wrapper', 'ziultimate' ),
				'selector'                => '{{ELEMENT}} #piotnetforms .piotnetforms-image',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'img_field',
			[
				'title'                   => esc_html__( 'Image Field', 'ziultimate' ),
				'selector'                => '{{ELEMENT}} #piotnetforms .piotnetforms-image__content',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'img_caption',
			[
				'title'                   => esc_html__( 'Image Caption', 'ziultimate' ),
				'selector'                => '{{ELEMENT}} #piotnetforms .piotnetforms-image__caption',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'wcf_headings',
			[
				'title'                   => esc_html__( 'Headings(Checkout Form)', 'ziultimate' ),
				'selector'                => '{{ELEMENT}} #piotnetforms .woocommerce-checkout h3',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'wcf_labels',
			[
				'title'                   => esc_html__( 'Fields Label(Checkout Form)', 'ziultimate' ),
				'selector'                => '{{ELEMENT}} #piotnetforms .woocommerce-checkout .form-row label',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'wcf_ast',
			[
				'title'                   => esc_html__( 'Asterisk(Checkout Form)', 'ziultimate' ),
				'selector'                => '{{ELEMENT}} #piotnetforms .woocommerce-checkout .form-row .required',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'wcf_input',
			[
				'title'                   => esc_html__( 'Input Fields(Checkout Form)', 'ziultimate' ),
				'selector'                => '{{ELEMENT}} #piotnetforms .woocommerce-checkout .form-row .input-text',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'wcf_select',
			[
				'title'                   => esc_html__( 'Dropdown Fields(Checkout Form)', 'ziultimate' ),
				'selector'                => '{{ELEMENT}} #piotnetforms .woocommerce-checkout .form-row select',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'wcf_place_order_button',
			[
				'title'                   => esc_html__( 'Place Order(Checkout Form)', 'ziultimate' ),
				'selector'                => '{{ELEMENT}} .woocommerce-checkout-payment .form-row .button',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);
	}

	public function server_render( $request ) {
		if ( function_exists( 'WC' ) ) {
			\WC()->frontend_includes();
			\WC_Template_Loader::init();
			\wc_load_cart();
		}

		parent::server_render( $request );
	}
}