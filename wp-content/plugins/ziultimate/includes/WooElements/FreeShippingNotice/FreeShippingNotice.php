<?php
namespace ZiUltimate\WooElements\FreeShippingNotice;

use ZiUltimate\WooHelpers;
use ZiUltimate\UltimateElements;
use ZiUltimate\Utils;
use ZiUltimate\Admin\License;
use ZionBuilder\Options\BaseSchema;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class FreeShippingNotice
 *
 * @package ZiUltimate\WooElements
 */
class FreeShippingNotice extends UltimateElements {

    public function get_type() {
		return 'zu_free_shipping_notice';
	}

	public function get_name() {
		return __( 'Free Shipping Notice', 'ziultimate' );
	}

	public function get_keywords() {
		return [ 'shipping', 'notice', 'free shipping' ];
	}

	/*public function get_label() {
		return [
			'text'  => $this->get_label_text(),
			'color' => $this->get_label_color(),
		];
	}*/

	public function get_category() {
		return $this->zuwoo_elements_category();
	}

	public function options( $options ) {
		$options->add_option(
			'msg_type',
			[
				'type' 		=> 'select',
				'title' 	=> __('Message Type', "oxyultimate-woo"),
				'default' 	=> 'amount',
				'options' 	=> [
					[
						'name' 	=> esc_html__('Minimum Amount', "ziultimate"),
						'id' 	=> 'amount'
					],
					[
						'name' 	=> esc_html__('Cart Quantity', "ziultimate"),
						'id' 	=> 'quantity'
					]
					
				]
			]
		);

		/****************** Minimum Amount *************************/

		$options->add_option(
			'min_amount',
			[
				'type' 			=> 'text',
				'title' 		=> esc_html__('Minimum Order Amount', "ziultimate"),
				'description' 	=> esc_html__('Do not enter the currency. Minimum order amount to encourage users to purchase more.', "ziultimate"),
				'placeholder' 	=> 100,
				'dependency' 	=> [
					[
						'option' 	=> 'msg_type',
						'value' 	=> ['amount']
					]
				]
			]
		);

		$options->add_option(
			'threshold_amount',
			[
				'type' 			=> 'text',
				'title' 		=> esc_html__('Threshold Amount', "ziultimate"),
				'placeholder' 	=> 50,
				'description' 	=> esc_html__( 'Threshold amount after which notice should start appear.', "ziultimate" ),
				'dependency' => [
					[
						'option' 	=> 'msg_type',
						'value' 	=> ['amount']
					]
				]
			]
		);

		$options->add_option(
			'minamt_message',
			[
				'type' 			=> 'textarea',
				'title' 		=> esc_html__('Enter Your Message', "ziultimate"),
				'default' 		=> 'Add {remaining_amount} to your cart in order to receive free shipping!',
				'dependency' => [
					[
						'option' 	=> 'msg_type',
						'value' 	=> ['amount']
					]
				]
			]
		);

		$options->add_option(
			'exclude_coupons',
			[
				'type' 		=> 'checkbox_switch',
				'title' 	=> esc_html__('Exclude Coupons Amount', "ziultimate"),
				'default' 	=> false,
				'layout' 	=> 'inline',
				'dependency' => [
					[
						'option' 	=> 'msg_type',
						'value' 	=> ['amount']
					]
				]
			]
		);

		$options->add_option(
			'update_price',
			[
				'type' 		=> 'checkbox_switch',
				'title' 	=> esc_html__('Will price update automatically?', "ziultimate"),
				'default' 	=> false,
				'layout' 	=> 'inline',
				'dependency' => [
					[
						'option' 	=> 'msg_type',
						'value' 	=> ['amount']
					]
				]
			]
		);

		/****************** Cart Quantity *************************/
		$options->add_option(
			'required_qty',
			[
				'type' 			=> 'text',
				'title' 		=> esc_html__('Required Quantity', "ziultimate"),
				'placeholder' 	=> 3,
				'dependency' 	=> [
					[
						'option' 	=> 'msg_type',
						'value' 	=> ['quantity']
					]
				]
			]
		);

		$options->add_option(
			'threshold_qty',
			[
				'type' 			=> 'text',
				'title' 		=> esc_html__('Threshold Quantity', "ziultimate"),
				'placeholder' 	=> 3,
				'description' 	=> esc_html__( 'Set here the minimum product quantity to show the notice.', "ziultimate" ),
				'dependency' 	=> [
					[
						'option' 	=> 'msg_type',
						'value' 	=> ['quantity']
					]
				]
			]
		);

		$options->add_option(
			'qty_message',
			[
				'type' 			=> 'textarea',
				'title' 		=> esc_html__('Enter Your Message', "ziultimate"),
				'default' 		=> 'Add {remaining_quantity} into your cart in order to receive free shipping!',
				'dependency' 	=> [
					[
						'option' 	=> 'msg_type',
						'value' 	=> ['quantity']
					]
				]
			]
		);

		$options->add_option(
			'update_qty',
			[
				'type' 			=> 'checkbox_switch',
				'title' 		=> esc_html__('Will quantity update automatically?', "ziultimate"),
				'default'		=> false,
				'layout' 		=> 'inline',
				'dependency' 	=> [
					[
						'option' 	=> 'msg_type',
						'value' 	=> ['quantity']
					]
				]
			]
		);

		$options->add_option(
			'after_action',
			[
				'type' 		=> 'select',
				'title' 	=> esc_html__('Action', "oxyultimate-woo"),
				'description' => esc_html__( 'when set amount/quantity is reached.', "ziultimate" ),
				'options' 	=> [
					[
						'name' 	=> esc_html__('Confirmation Message', "ziultimate"),
						'id' 	=> 'custmsg'
					],
					[
						'name' 	=> esc_html__('Hide', "ziultimate"),
						'id' 	=> 'hide'
					]
					
				],
				'default' => 'hide'
			]
		);

		$options->add_option(
			'end_notice',
			[
				'type' 			=> 'textarea',
				'title' 		=> esc_html__('Enter Confirmation Message', "ziultimate"),
				'dependency' 	=> [
					[
						'option' 	=> 'after_action',
						'value' 	=> ['custmsg']
					]
				]
			]
		);

		$options->add_option(
			'outer_wrap_sel',
			[
				'type' 		=> 'text',
				'title' 	=> __('Outer Wrapper Selector', "ziultimate"),
				'description' => esc_html__('Setup when you will put this component into another wrapper.', "ziultimate")
			]
		);

		$options->add_option(
			'cta_sel',
			[
				'type' 		=> 'text',
				'title' 	=> __('Custom Button Selector', "ziultimate"),
				'description' => esc_html__('Setup when you will use the custom button or link.', "ziultimate")
			]
		);

		/**
		 * Progress Bar
		 */
		$pb = $options->add_group(
			'pb_group',
			[
				'type' 		=> 'panel_accordion',
				'title' 	=> esc_html__('Progress Bar', 'ziultimate'),
				'collapsed' =>true,
			]
		);

		$pb->add_option(
			'enable_pb',
			[
				'type' 			=> 'checkbox_switch',
				'title' 		=> esc_html__('Enable Progress Bar?', "ziultimate"),
				'default'		=> false,
				'layout' 		=> 'inline'
			]
		);

		$pb->add_option(
			'pb_min_max',
			[
				'type' 			=> 'checkbox_switch',
				'title' 		=> esc_html__('Display Min/Max Value?', "ziultimate"),
				'description' 	=> esc_html__('Price or quantity value will show at left and right side of the progress bar.', "ziultimate"),
				'default'		=> false,
				'layout' 		=> 'inline'
			]
		);

		$pb->add_option(
			'pb_hide',
			[
				'type' 			=> 'checkbox_switch',
				'title' 		=> esc_html__('Hide it?', "ziultimate"),
				'description' 	=> esc_html__('When set amount/quantity is reached.', "ziultimate"),
				'default'		=> false,
				'layout' 		=> 'inline'
			]
		);

		$pb->add_option(
			'pb_initial_color',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> esc_html__('Background Color'),
				'width' 	=> 50,
				'default' 	=> '#dacece',
				'css_style' => [
					[
						'selector' 	=> "{{ELEMENT}} .fsn-progress-bar",
						'value' 	=> 'background-color: {{VALUE}}'
					]
				]
			]
		);

		$pb->add_option(
			'pb_active_color',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> esc_html__('Active Background Color'),
				'width' 	=> 50,
				'default' 	=> '#f73a3a',
				'css_style' => [
					[
						'selector' 	=> "{{ELEMENT}} .fsn-progress-bar .fsn-progress-bar-res",
						'value' 	=> 'background-color: {{VALUE}}'
					]
				]
			]
		);

		$pb->add_option(
			'pb_width',
			[
				'type' 		=> 'slider',
				'content' 	=> '%',
				'min' 		=> 0,
				'max' 		=> 100,
				'default' 	=> 100,
				'title' 	=> esc_html__('Width'),
				'responsive_options' => true,
				'css_style' => [
					[
						'selector' 	=> "{{ELEMENT}} .fsn-progress-bar-wrap",
						'value' 	=> 'width: {{VALUE}}%'
					]
				]
			]
		);

		$pb->add_option(
			'pb_height',
			[
				'type' 		=> 'slider',
				'content' 	=> 'px',
				'min' 		=> 0,
				'max' 		=> 50,
				'default' 	=> 5,
				'title' 	=> esc_html__('Height'),
				'responsive_options' => true,
				'css_style' => [
					[
						'selector' 	=> "{{ELEMENT}} .fsn-progress-bar",
						'value' 	=> 'height: {{VALUE}}px'
					]
				]
			]
		);

		$pb->add_option(
			'pb_brd',
			[
				'type' 		=> 'html',
				'title' 	=> esc_html__( 'Border' ),
				'content'	=> '<hr style="border: none; border-bottom: 1px solid #dcdcdc;"/>'
			]
		);

		$pb->add_option(
			'pb_brd_clr',
			[
				'type' 		=> 'colorpicker',
				'title' 	=> esc_html__('Color'),
				'width' 	=> 33.33,
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} .fsn-progress-bar',
						'value' 	=> 'border-color: {{VALUE}}'
					]
				]
			]
		);
		
		$pb->add_option(
			'pb_brd_wdth',
			[
				'type' 		=> 'number_unit',
				'units' 	=> BaseSchema::get_units(),
				'title' 	=> esc_html__('Width'),
				'width' 	=> 33.33,
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} .fsn-progress-bar',
						'value' 	=> 'border-width: {{VALUE}}'
					]
				]
			]
		);

		$pb->add_option(
			'pb_brd_style',
			[
				'type' 		=> 'select',
				'title' 	=> esc_html__('Style'),
				'width' 	=> 33.33,
				'options' 	=> [
					[
						'name' 	=> esc_html__('None'),
						'id' 	=> 'none'
					],
					[
						'name' 	=> esc_html__('Solid'),
						'id' 	=> 'solid'
					],
					[
						'name' 	=> esc_html__('Dashed'),
						'id' 	=> 'dashed'
					],
					[
						'name' 	=> esc_html__('Dotted'),
						'id' 	=> 'dotted'
					],
				],
				'css_style' => [
					[
						'selector' 	=> '{{ELEMENT}} .fsn-progress-bar',
						'value' 	=> 'border-style: {{VALUE}}'
					]
				]
			]
		);

		$pb->add_option(
			'pb_brd_rad',
			[
				'type' 		=> 'slider',
				'content' 	=> 'px',
				'min' 		=> 0,
				'max' 		=> 50,
				'default' 	=> 0,
				'step' 		=> 1,
				'title' 	=> esc_html__('Radius'),
				'css_style' => [
					[
						'selector' 	=> "{{ELEMENT}} .fsn-progress-bar",
						'value' 	=> 'border-radius: {{VALUE}}px'
					]
				]
			]
		);

		$pb->add_option(
			'pb_margin',
			[
				'type' 		=> 'html',
				'title' 	=> esc_html__( 'Margin' ),
				'content'	=> '<hr style="border: none; border-bottom: 1px solid #dcdcdc;"/>'
			]
		);

		$this->attach_margin_options( $pb, 'pb_m', '{{ELEMENT}} .fsn-progress-bar-wrap');

		$anim = $options->add_group(
			'animation',
			[
				'type' 		=> 'panel_accordion',
				'title' 	=> esc_html__( 'Animation' ),
				'collapsed'	=> true
			]
		);

		$anim->add_option(
			'enable_animation',
			[
				'type' 			=> 'checkbox_switch',
				'title' 		=> esc_html__('Enable Animation?', "ziultimate"),
				'default'		=> false,
				'layout' 		=> 'inline'
			]
		);

		$anim->add_option(
			'fade_speed',
			[
				'type' 		=> 'slider',
				'content' 	=> 'ms',
				'min' 		=> 0,
				'max' 		=> 10000,
				'default' 	=> 1500,
				'step' 		=> 10,
				'title' 	=> esc_html__('Transition Duration for Fade', 'ziultimate')
			]
		);

		$anim->add_option(
			'anim_td',
			[
				'type' 		=> 'slider',
				'content' 	=> 's',
				'min' 		=> 0,
				'max' 		=> 10,
				'default' 	=> 0.15,
				'step' 		=> 0.01,
				'title' 	=> esc_html__('Transition Duration for Slide', 'ziultimate'),
				'css_style' => [
					[
						'selector' 	=> "{{ELEMENT}}",
						'value' 	=> 'transition-duration: {{VALUE}}s'
					]
				]
			]
		);
	}

	protected function can_render() {
		if( ! License::has_valid_license() )
			return;

		return true;
	}

	public function before_render( $options ) {
		$this->render_attributes->add('wrapper', 'class', $this->uid );
	}

	public function render( $options ) {
		$message_type = $options->get_value( 'msg_type', 'amount' );
		$price_update = $options->get_value('update_price', false );
		$update_qty = $options->get_value( 'update_qty', false );

		if( $message_type == 'amount' ) {
			
			$price = $options->get_value('min_amount', 100 );

			if( empty( $price ) || $price === false )
				return;

			add_filter( 'woocommerce_price_trim_zeros', array( $this, 'fsn_price_trim_zeros' ) );

			$this->minimum_amount_notice( $options );

			remove_filter( 'woocommerce_price_trim_zeros', array( $this, 'fsn_price_trim_zeros' ) );

		} elseif( $message_type == 'quantity' ) {

			$required_qty = $options->get_value('required_qty', 3 );

			if( empty( $required_qty ) || $required_qty === false )
				return;

			$this->quantity_notice( $options );
		}
	}

	public function server_render( $request ) {

		if ( function_exists( 'WC' ) ) {
			\WC()->frontend_includes();
			\WC_Template_Loader::init();
			\wc_load_cart();
		}

		parent::server_render( $request );
	}

	/**
	 * Registering the styles
	 */
	public function on_register_styles() {
		$this->register_style_options_element(
			'cmsg_styles',
			[
				'title'    => esc_html__( 'Confirmation Message', 'ziultimate' ),
				'selector' => '{{ELEMENT}} .fs-aftermsg',
			]
		);

		$this->register_style_options_element(
			'pb_minmax_styles',
			[
				'title'    => esc_html__( 'Progress Bar Min/Max Value', 'ziultimate' ),
				'selector' => '{{ELEMENT}} .fsn-progress-bar-min-qty, {{ELEMENT}} .fsn-progress-qty, {{ELEMENT}} .fsn-progress-bar-min-price .woocommerce-Price-amount, {{ELEMENT}} .fsn-progress-amount .woocommerce-Price-amount',
			]
		);
	}

	public function fsn_price_trim_zeros( $bool ) {
		return true;
	}

	/**
	 * Enqueue Styles
	 *
	 * Loads the scripts necessary for the current element
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		add_filter('body_class', array($this, 'fsn_body_class'), 99 );

		$this->enqueue_element_style( Utils::get_file_url('dist/css/elements/FreeShippingNotice/frontend.css') );
	}

	/**
	 * Enqueue Scripts
	 *
	 * Loads the scripts necessary for the current element
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$this->enqueue_element_script( Utils::get_file_url('dist/js/elements/FreeShippingNotice/frontend.js') );
	}

	public function fsn_body_class( $classes ) {
		if( in_array( 'znpb-editor-preview', $classes ) )
			return $classes;

		$options = $this->options;

		$message_type = $options->get_value( 'msg_type', 'amount' );
		$display_fstext = $options->get_value( 'after_action', 'hide' );
		$outer_wrap_sel = $options->get_value( 'outer_wrap_sel', false );

		if( $message_type == 'amount' ) {
			$order_amt = $options->get_value( 'min_amount', 100 );
			$exclude_coupons = $options->get_value( 'exclude_coupons', false );		
			$threshold_amt = $options->get_value( 'threshold_amount', false );

			$cart_total = WooHelpers::cart_total( $exclude_coupons );

			if( ( ! empty( $threshold_amt ) && $threshold_amt > $cart_total ) || 
				( $cart_total >= $order_amt ) 
			) {
				if( !empty( $outer_wrap_sel ) ) {
					$compsel = str_replace( array( '#', '.'), '' , $outer_wrap_sel );
				} else {
					$compsel = $this->uid;
				}

				$classes[] = $compsel;
			}

			if( $cart_total >= $order_amt && $display_fstext != 'hide' ) {
				$classes[] = $compsel . '-hide-fstxt';
			}
		}

		if( $message_type == 'quantity' ) {
			$required_qty = $options->get_value( 'required_qty', 3 );
			$threshold_qty = $options->get_value( 'threshold_qty', false );

			$cart_qty = ( is_null( WC()->cart ) || WC()->cart->is_empty() ) ? 0 : WC()->cart->get_cart_contents_count();

			if( ( ! empty( $threshold_qty ) && $threshold_qty > $cart_qty ) 
				|| ( $cart_qty >= $required_qty ) 
			) {
				if( !empty( $outer_wrap_sel ) ) {
					$compsel = str_replace( array( '#', '.'), '' , $outer_wrap_sel );
				} else {
					$compsel = $this->uid;
				}

				$classes[] = $compsel;
			}

			if( $cart_qty >= $required_qty && $display_fstext != 'hide' ) {
				$classes[] = $compsel . '-hide-fstxt';
			}
		}

		return $classes;
	}

	private function minimum_amount_notice( $options ) {
		$notice = $toggleclass = $svgIcon = '';
		$order_amt = $options->get_value( 'min_amount', 100 );
		$exclude_coupons = $options->get_value( 'exclude_coupons', false );
		$threshold_amt = $options->get_value( 'threshold_amount', '' );
		$price_update = $options->get_value( 'update_price', 'false' );

		$cart_total = WooHelpers::cart_total( $exclude_coupons );
		$amount = ( $cart_total < $order_amt ) ? wc_price( $order_amt - $cart_total ) : wc_price( $cart_total );
		$enable_animation = $options->get_value( 'enable_animation', 'false' );
		$fade_speed = $options->get_value( 'fade_speed', 1500 );
		$outer_wrap_sel = $options->get_value( 'outer_wrap_sel', false );
		$outer_wrap_sel = ! empty( $outer_wrap_sel ) ? ' data-wrapsel="'. $outer_wrap_sel . '"' : '';

		$pb_hide = $options->get_value( 'pb_hide', false );
		$pb_hide = ! empty( $pb_hide ) ? "true" : "false";

		$msg = $options->get_value( 'minamt_message' );
		$action = $options->get_value( 'after_action', 'hide' );
		$fs_notice = $options->get_value( 'end_notice' );

		$message = isset( $msg ) ? wp_kses_post( $msg ) : 'Add {remaining_amount} to your cart in order to receive free shipping!';
		$message = str_replace("{remaining_amount}", $amount, $message);

		if( $action != 'hide' ) {
			$after_action = ' data-after-action=showmsg';
			$notice = isset($fs_notice) ? '<span class="fs-aftermsg">' . wp_kses_post($fs_notice) . '</span>' : '';

			if( $cart_total >= $order_amt )
				$toggleclass = ' hide-defaultmsg';
		} else {
			$after_action = ' data-after-action=hidemsg';
		}

		echo '<p class="free-shipping-content' . $toggleclass . '" data-fs-msgtype="amount" data-fsamount="' . $order_amt . '" data-zufs-animation="'. $enable_animation .'" data-auto-update="'. $price_update .'" data-pb-hide="' . $pb_hide . '" data-fade-speed="'. $fade_speed .'" data-exclude-coupons="'.$exclude_coupons.'" data-threshold-amt="'. $threshold_amt .'"' . $after_action . $outer_wrap_sel .'><span class="fs-defaultmsg">' . $message . '</span>' . $notice . '</p>';

		$showpb = $options->get_value('enable_pb', false);
		if( ! empty( $showpb ) ) {
			$pbshowprice = $options->get_value('pb_min_max', false);
			$pbres = ($cart_total < $order_amt) ? ceil( ( $cart_total / $order_amt ) * 100 ) : 100;
	?>
		<div class="fsn-progress-bar-wrap">
			<?php if( $pbshowprice ): ?>
				<span class="fsn-progress-bar-min-price"><?php echo wc_price(0) ; ?></span>
			<?php endif; ?>
			<div class="fsn-progress-bar">
				<div class="fsn-progress-bar-res" style="width: <?php echo $pbres; ?>%;"></div>
			</div>
			<?php if( $pbshowprice ): ?>
				<span class="fsn-progress-amount"><?php echo wc_price( $order_amt ); ?></span>
			<?php endif; ?>
		</div>
	<?php
		}
	}

	private function quantity_notice( $options ) {
		$required_qty = absint( $options->get_value( 'required_qty', 3 ) );
		$threshold_qty = wp_kses_post( $options->get_value( 'threshold_qty', false ) );
		$update_qty = $options->get_value( 'update_qty', 'false' );
		$outer_wrap_sel = $options->get_value( 'outer_wrap_sel', false );
		$outer_wrap_sel = ! empty( $outer_wrap_sel ) ? ' data-wrapsel="'. $outer_wrap_sel . '"' : '';
		$action = $options->get_value( 'after_action', 'hide' );
		$fs_notice = $options->get_value( 'end_notice' );
		$enable_animation = $options->get_value( 'enable_animation', 'false' );
		$fade_speed = $options->get_value( 'fade_speed', 1500 );

		$pb_hide = $options->get_value( 'pb_hide', false );
		$pb_hide = ! empty( $pb_hide ) ? "true" : "false";

		$cart_qty = ( is_null( WC()->cart ) || WC()->cart->is_empty() ) ? 0 : WC()->cart->get_cart_contents_count();
		$remaining_qty = ( $cart_qty < $required_qty ) ? ( $required_qty - $cart_qty ) : $cart_qty;

		$notice = $toggleclass = '';

		if( $action != 'hide' ) {
			$after_action = ' data-after-action="showmsg"';
			$notice = isset($fs_notice) ? '<span class="fs-aftermsg">' . wp_kses_post($fs_notice) . '</span>' : '';

			if( $cart_qty >= $required_qty )
				$toggleclass = ' hide-defaultmsg';
		} else {
			$after_action = ' data-after-action="hidemsg"';
		}

		$msg = $options->get_value( 'qty_message' );
		$msg = isset($msg) ? wp_kses_post($msg) : 'Add {remaining_quantity} into your cart in order to receive free shipping!';
		$message = str_replace("{remaining_quantity}", '<span class="remaining-qty">' . $remaining_qty . '</span>', $msg);

		echo '<p class="free-shipping-content' . $toggleclass . '" data-fs-msgtype="quantity" data-fsqty="' . $required_qty . '" data-zufs-animation="'. $enable_animation .'" data-auto-update="'. $update_qty .'" data-pb-hide="' . $pb_hide . '" data-fade-speed="'. $fade_speed .'" data-threshold-qty="'. $threshold_qty .'"' . $after_action . $outer_wrap_sel .'><span class="fs-defaultmsg">' . $message . '</span>' . $notice . '</p>';

		$showpb = $options->get_value('enable_pb', false);
		if( ! empty( $showpb ) ) {
			$pbshowprice = $options->get_value('pb_min_max', false);
			$pbres = ($cart_qty < $required_qty) ? ceil( ( $cart_qty / $required_qty ) * 100 ) : 100;
	?>
		<div class="fsn-progress-bar-wrap">
			<?php if( $pbshowprice ): ?>
				<span class="fsn-progress-bar-min-qty">0</span>
			<?php endif; ?>
			<div class="fsn-progress-bar">
				<div class="fsn-progress-bar-res" style="width: <?php echo $pbres; ?>%;"></div>
			</div>
			<?php if( $pbshowprice ): ?>
				<span class="fsn-progress-qty"><?php echo $required_qty; ?></span>
			<?php endif; ?>
		</div>
	<?php
		}
	}
}