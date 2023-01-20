<?php

namespace ZionBuilderPro\Integrations\WooCommerce\Elements\MiniCart;

use ZionBuilderPro\Integrations\WooCommerce\Elements\WooCommerceElement;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class MiniCart
 *
 * @package ZionBuilderPro\Integrations\Elements
 */
class MiniCart extends WooCommerceElement {
	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'woo-mini-cart';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Mini cart', 'zionbuilder-pro' );
	}

	/**
	 * Get Category
	 *
	 * Will return the element category
	 *
	 * @return string
	 */
	public function get_category() {
		return 'woocommerce';
	}

	/**
	 * Get keywords
	 *
	 * Returns the keywords for this element
	 *
	 * @return array The list of element keywords
	 */
	public function get_keywords() {
		return array( 'woocommerce' );
	}

	/**
	 * Registers the element options
	 *
	 * @param \ZionBuilder\Options\Options $options The Options instance
	 *
	 * @return void
	 */
	public function options( $options ) {
		$options->add_option(
			'icon',
			[
				'type'        => 'icon_library',
				'id'          => 'icon',
				'title'       => esc_html__( 'Icon', 'zionbuilder' ),
				'description' => esc_html__( 'Choose an icon for the cart', 'zionbuilder' ),
				'default'     => [
					'family'  => 'Font Awesome 5 Free Solid',
					'name'    => 'shopping-cart',
					'unicode' => 'uf07a',
				],
			]
		);

		$options->add_option(
			'show_cart_count',
			[
				'type'    => 'checkbox_switch',
				'title'   => esc_html__( 'Show cart count', 'zionbuilder' ),
				'layout'  => 'inline',
				'default' => true,
			]
		);

		$options->add_option(
			'show_cart_subtotal',
			[
				'type'    => 'checkbox_switch',
				'title'   => esc_html__( 'Show cart subtotal', 'zionbuilder' ),
				'layout'  => 'inline',
				'default' => true,
			]
		);

		$options->add_option(
			'cart_subtotal_position',
			[
				'type'       => 'select',
				'columns'    => 2,
				'title'      => esc_html__( 'Cart subtotal position', 'zionbuilder' ),
				'default'    => 'right',
				'layout'     => 'inline',
				'options'    => [
					[
						'id'   => 'left',
						'name' => esc_html__( 'left', 'zionbuilder' ),
					],
					[
						'id'   => 'right',
						'name' => esc_html__( 'right', 'zionbuilder' ),
					],
				],
				'dependency' => [
					[
						'option' => 'show_cart_subtotal',
						'value'  => [ true ],
					],
				],
			]
		);

		// $cart_details = $options->add_group(
		//  'cart_details',
		//  [
		//      'type'      => 'panel_accordion',
		//      'title'     => esc_html__( 'Cart details', 'zionbuilder' ),
		//      'collapsed' => true,
		//  ]
		// );

		$options->add_option(
			'cart_action',
			[
				'type'    => 'select',
				'title'   => esc_html__( 'Cart details action', 'zionbuilder' ),
				'default' => 'link',
				'options' => [
					[
						'name' => esc_html__( 'Link to cart page', 'zionbuilder' ),
						'id'   => 'link',
					],
					[
						'name' => esc_html__( 'Show dropdown', 'zionbuilder' ),
						'id'   => 'dropdown',
					],
					// [
					//  'name' => esc_html__( 'Show off canvas', 'zionbuilder' ),
					//  'id'   => 'show_off_canvas',
					// ],
				],
			]
		);

		// $cart_details->add_option(
		//  'cart_action_trigger',
		//  [
		//      'type'       => 'select',
		//      'title'      => esc_html__( 'Cart details trigger', 'zionbuilder' ),
		//      'default'    => 'click',
		//      'options'    => [
		//          [
		//              'name' => esc_html__( 'Click', 'zionbuilder' ),
		//              'id'   => 'click',
		//          ],
		//          [
		//              'name' => esc_html__( 'Hover', 'zionbuilder' ),
		//              'id'   => 'hover',
		//          ],
		//      ],
		//      'dependency' => [
		//          [
		//              'option' => 'cart_action',
		//              'value'  => [ 'show_dropdown' ],
		//          ],
		//      ],
		//  ]
		// );
	}

	/**
	 * Render
	 *
	 * Will render the element based on options
	 *
	 * @param mixed $options
	 *
	 * @return void
	 */
	public function render( $options ) {
		$show_cart_count        = $options->get_value( 'show_cart_count', true );
		$show_cart_subtotal     = $options->get_value( 'show_cart_subtotal', true );
		$cart_subtotal_position = $options->get_value( 'cart_subtotal_position', 'right' );
		$cart_action            = $options->get_value( 'cart_action', 'link' );
		$cart_url               = wc_get_cart_url();
		$cart_items_count       = is_object( WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
		$subtotal               = is_object( WC()->cart ) ? WC()->cart->get_cart_subtotal() : 0;
		$icon_content           = $show_cart_count ? "<span class='zb-miniCart-count'>{$cart_items_count}</span>" : '';

		$icon_markup = self::get_icon_markup(
			$options->get_value( 'icon' ),
			$icon_content,
			[
				'class' => 'zb-miniCartIcon',
			]
		);

			echo "<a href='{$cart_url}' class='zb-miniCartAction zb-miniCart-subtotal--{$cart_subtotal_position}'>";
				echo $icon_markup; ?>

				<?php if ( $show_cart_subtotal ) : ?>
					<span class="zb-miniCart-subtotal"><?php echo $subtotal; ?></span>
					<?php
				endif;

				echo '</a>'
				?>
			<?php if ( $cart_action === 'dropdown' ) : ?>
				<div class="zb-miniCart-details">
					<div class="widget_shopping_cart_content"></div>
				</div>
			<?php endif; ?>

			<style>
				.zb-el-wooMiniCart {
					position: relative;
					font-size: 18px;
					display: inline-flex;
				}

				.zb-miniCartIcon {
					position: relative;
				}

				.zb-miniCart-count {
					position: absolute;
					right: 0;
					top: 0;
					transform: translate(50%,-50%);
					background: #000;
					color: #fff;
					border-radius: 50%;
					width: 18px;
					height: 18px;
					line-height: 1;
					font-size: 10px;
					display: flex;
					align-items: center;
					justify-content: center;
				}

				.zb-miniCart-subtotal {
					margin-left: 7px;
				}

				.zb-miniCartAction {
					display: inline-flex;
				}

				.zb-miniCart-subtotal--left{
					flex-direction: row-reverse;
				}

				.zb-miniCart-subtotal--left .zb-miniCart-subtotal{
					margin-left: 0;
					margin-right: 7px;
				}


				.zb-miniCart-details {
					visibility: hidden;
					opacity: 0;
					position: absolute;
					transition: visibility .15s, opacity .15s;
					top: 100%;
					right: 0;
					padding-top: 10px;
					z-index: 999;
				}

				.zb-el-wooMiniCart .widget_shopping_cart_content {
					width: 320px;
					box-shadow: #dddbdb 0 0px 10px;
					padding: 20px;
					background: #fff;
				}

				.zb-el-wooMiniCart:hover .zb-miniCart-details {
					visibility: visible;
					opacity: 1;

				}

				.zb-el-wooMiniCart .zb-miniCart-details li{
					margin-bottom: 10px;
					overflow: hidden;
					position: relative;
					margin-right: 0;
				}

				.woocommerce .zb-el-wooMiniCart .zb-miniCart-details img {
					float: left;
					margin-right: 15px;
					width: 60px;
				}


				.zb-el-wooMiniCart .zb-miniCart-details li .remove {
					font-size: 21px;
					font-weight: 400;
					line-height: 1;
					opacity: .5;
					position: absolute;
					right: 0;
					background: none;
				}

				.zb-el-wooMiniCart .zb-miniCart-details li .remove:hover {
					opacity: 0.7;
					color: inherit;
				}

				.zb-el-wooMiniCart .zb-miniCart-details li .quantity {
					border: none;
					box-shadow: none;
					color: var(--bricks-text-light);
					display: block;
					line-height: 1;
					margin-top: 5px;
				}

				.zb-el-wooMiniCart .zb-miniCart-details .woocommerce-mini-cart__buttons {
					display: flex;
					justify-content: space-between;
				}

				.zb-el-wooMiniCart .zb-miniCart-details .woocommerce-mini-cart__buttons a {
					font-size: 14px;
					line-height: 1;
					padding: 10px 15px;
				}
				</style>

		<?php
	}

	public function on_register_styles() {
		$this->register_style_options_element(
			'cart_count',
			array(
				'title'                   => esc_html__( 'Cart count', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .zb-miniCart-count',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			)
		);

		$this->register_style_options_element(
			'cart_icon',
			array(
				'title'                   => esc_html__( 'Cart icon', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .zb-miniCartIcon',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			)
		);

		$this->register_style_options_element(
			'cart_subtotal',
			array(
				'title'                   => esc_html__( 'Cart subtotal', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .zb-miniCart-subtotal',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			)
		);

		$this->register_style_options_element(
			'cart_details',
			array(
				'title'                   => esc_html__( 'Cart details', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .zb-miniCart-details .widget_shopping_cart_content',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			)
		);
	}
}
