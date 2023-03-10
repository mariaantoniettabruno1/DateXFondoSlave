<?php

namespace ZionBuilderPro\Integrations\WooCommerce\Elements\CheckoutFormWrapper;

use ZionBuilderPro\Integrations\WooCommerce\Elements\WooCommerceElement;
use ZionBuilderPro\Utils;
use ZionBuilder\Options\Schemas\StyleOptions;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class CheckoutFormWrapper
 *
 * @package ZionBuilderPro\Integrations\Elements
 */
class CheckoutFormWrapper extends WooCommerceElement {
	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'woo-checkout-form-wrapper';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Checkout form wrapper', 'zionbuilder-pro' );
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
	 * Get Element Icon
	 *
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'element-woo-add-to-cart';
	}

	/**
	 * Is wrapper
	 *
	 * Returns true if the element can contain other elements ( f.e. section, column )
	 *
	 * @return boolean The element icon
	 */
	public function is_wrapper() {
		return true;
	}



	/**
	 * Enqueue element scripts for both frontend and editor
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		// Using helper methods will go through caching policy
		$this->enqueue_editor_script( Utils::get_file_url( 'dist/elements/CheckoutFormWrapper/editor.js' ) );
	}

	/**
	 * Enqueue element scripts for both frontend and editor
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		$this->enqueue_element_style( Utils::get_file_url( 'dist/elements/CheckoutFormWrapper/frontend.css' ) );
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
			'display',
			[
				'type'        => 'custom_selector',
				'title'       => __( 'Display', 'zionbuilder' ),
				'description' => __( 'Display css properties', 'zionbuilder' ),
				'columns'     => 3,
				'default'     => 'flex',
				'search_tags' => [ 'flex', 'block', 'inline', 'none' ],
				'sync'        => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.display',
				'options'     => [
					[
						'name' => __( 'flex', 'zionbuilder' ),
						'id'   => 'flex',
					],
					[
						'name' => __( 'block', 'zionbuilder' ),
						'id'   => 'block',
					],
					[
						'name' => __( 'inline', 'zionbuilder' ),
						'id'   => 'inline',
					],
					[
						'name' => __( 'inline-flex', 'zionbuilder' ),
						'id'   => 'inline-flex',
					],
					[
						'name' => __( 'inline-block', 'zionbuilder' ),
						'id'   => 'inline-block',
					],
					[
						'name' => __( 'none', 'zionbuilder' ),
						'id'   => 'none',
					],
				],
			]
		);

		/**
		 * Flex
		 */
		$flex_container_group = $options->add_group(
			'flexbox-container-group',
			[
				'type'       => 'panel_accordion',
				'title'      => __( 'Flexbox container options', 'zionbuilder' ),
				'collapsed'  => true,
				'dependency' => [
					[
						'option' => 'display',
						'value'  => [ 'flex' ],
					],
				],
			]
		);

		$flex_container_group->add_option(
			'gap',
			[
				'type'         => 'number_unit',
				'title'        => __( 'Gap', 'zionbuilder' ),
				'description'  => __( 'The gap option allows you to specify the gap between child elements on the main axis. Current browser support is 89.92%. In order to increase browser support, you can use margins.', 'zionbuilder' ),
				'placeholder'  => '0px',
				'default_unit' => 'px',
				'sync'         => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.gap',
			]
		);

		$flex_container_group->add_option(
			'flex-direction',
			[
				'type'    => 'custom_selector',
				'width'   => 60,
				'options' => [
					[
						'name' => __( 'vertical', 'zionbuilder' ),
						'id'   => 'column',
					],
					[
						'name' => __( 'horizontal', 'zionbuilder' ),
						'id'   => 'row',
					],
				],
				'title'   => __( 'Flex direction', 'zionbuilder' ),
				'sync'    => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.flex-direction',
			]
		);

		$flex_container_group->add_option(
			'flex-reverse',
			[
				'type'    => 'custom_selector',
				'width'   => 40,
				'options' => [
					[
						'name' => __( 'flex-reverse', 'zionbuilder' ),
						'icon' => 'reverse',
						'id'   => true,
					],
				],
				'title'   => __( 'Flex reverse', 'zionbuilder' ),
				'sync'    => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.flex-reverse',
			]
		);

		$flex_container_group->add_option(
			'align-items',
			[
				'type'        => 'custom_selector',
				'title'       => __( 'Align items', 'zionbuilder' ),
				'description' => __( 'Set align items', 'zionbuilder' ),
				'options'     => [
					[
						'name' => __( 'flex-start', 'zionbuilder' ),
						'id'   => 'flex-start',
						'icon' => 'align-start',
					],
					[
						'name' => __( 'center', 'zionbuilder' ),
						'id'   => 'center',
						'icon' => 'align-center',
					],
					[
						'name' => __( 'flex-end', 'zionbuilder' ),
						'id'   => 'flex-end',
						'icon' => 'align-end',
					],
					[
						'name' => __( 'stretch', 'zionbuilder' ),
						'id'   => 'stretch',
						'icon' => 'align-stretch',
					],
					[
						'name' => __( 'baseline', 'zionbuilder' ),
						'id'   => 'baseline',
						'icon' => 'align-baseline',
					],
				],
				'sync'        => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.align-items',
			]
		);

		$flex_container_group->add_option(
			'justify-content',
			[
				'type'        => 'custom_selector',
				'title'       => __( 'Justify', 'zionbuilder' ),
				'description' => __( 'Set float option for element', 'zionbuilder' ),
				'columns'     => 5,
				'options'     => [
					[
						'name' => __( 'flex-start', 'zionbuilder' ),
						'id'   => 'flex-start',
						'icon' => 'justify-start',
					],
					[
						'name' => __( 'center', 'zionbuilder' ),
						'id'   => 'center',
						'icon' => 'justify-center',
					],
					[
						'name' => __( 'flex-end', 'zionbuilder' ),
						'id'   => 'flex-end',
						'icon' => 'justify-end',
					],
					[
						'name' => __( 'space-between', 'zionbuilder' ),
						'id'   => 'space-between',
						'icon' => 'justify-sp-btw',
					],
					[
						'name' => __( 'space-around', 'zionbuilder' ),
						'id'   => 'space-around',
						'icon' => 'justify-sp-around',
					],
				],
				'sync'        => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.justify-content',
			]
		);

		$flex_container_group->add_option(
			'flex-wrap',
			[
				'type'        => 'custom_selector',
				'grow'        => '5',
				'title'       => __( 'Wrap', 'zionbuilder' ),
				'description' => __( 'Set wrap for element', 'zionbuilder' ),
				'options'     => [
					[
						'name' => __( 'wrap', 'zionbuilder' ),
						'id'   => 'wrap',
					],
					[
						'name' => __( 'nowrap', 'zionbuilder' ),
						'id'   => 'nowrap',
					],
					[
						'name' => __( 'wrap-reverse', 'zionbuilder' ),
						'id'   => 'wrap-reverse',
						'icon' => 'reverse',
					],
				],
				'sync'        => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.flex-wrap',
			]
		);

		$flex_container_group->add_option(
			'align-content',
			[
				'type'        => 'custom_selector',
				'title'       => __( 'Align content', 'zionbuilder' ),
				'description' => __( 'Set align content', 'zionbuilder' ),
				'columns'     => 5,
				'options'     => [
					[
						'name' => __( 'flex-start', 'zionbuilder' ),
						'id'   => 'flex-start',
						'icon' => 'content-start',
					],
					[
						'name' => __( 'center', 'zionbuilder' ),
						'id'   => 'center',
						'icon' => 'content-center',
					],
					[
						'name' => __( 'flex-end', 'zionbuilder' ),
						'id'   => 'flex-end',
						'icon' => 'content-end',
					],
					[
						'name' => __( 'space-between', 'zionbuilder' ),
						'id'   => 'space-between',
						'icon' => 'content-space-btw',
					],
					[
						'name' => __( 'space-around', 'zionbuilder' ),
						'id'   => 'space-around',
						'icon' => 'content-space-around',
					],
					[
						'name' => __( 'strech', 'zionbuilder' ),
						'id'   => 'stretch',
						'icon' => 'content-stretch',
					],
				],
				'sync'        => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.align-content',
			]
		);

		$flex_child_group = $options->add_group(
			'flexbox-child-group',
			[
				'type'       => 'panel_accordion',
				'title'      => __( 'Flexbox child options', 'zionbuilder' ),
				'collapsed'  => true,
				'dependency' => [
					[
						'option' => 'display',
						'value'  => [ 'flex' ],
					],
				],
			]
		);

		$flex_child_group->add_option(
			'flex-grow',
			[
				'type'  => 'number',
				'width' => 33.3,
				'title' => __( 'Flex Grow', 'zionbuilder' ),
				'sync'  => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.flex-grow',
			]
		);

		$flex_child_group->add_option(
			'flex-shrink',
			[
				'type'  => 'number',
				'width' => 33.3,
				'title' => __( 'Flex Shrink', 'zionbuilder' ),
				'sync'  => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.flex-shrink',
			]
		);

		$flex_child_group->add_option(
			'flex-basis',
			[
				'type'         => 'number_unit',
				'width'        => 33.3,
				'title'        => __( 'Flex Basis', 'zionbuilder' ),
				'sync'         => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.flex-basis',
				'default_unit' => 'px',
			]
		);

		$flex_child_group->add_option(
			'align-self',
			[
				'type'        => 'custom_selector',
				'title'       => __( 'Align self', 'zionbuilder' ),
				'description' => __( 'Set align self', 'zionbuilder' ),
				'sync'        => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.align-self',
				'options'     => [
					[
						'name' => __( 'flex-start', 'zionbuilder' ),
						'id'   => 'flex-start',
						'icon' => 'self-start',
					],
					[
						'name' => __( 'center', 'zionbuilder' ),
						'id'   => 'center',
						'icon' => 'self-center',
					],
					[
						'name' => __( 'flex-end', 'zionbuilder' ),
						'id'   => 'flex-end',
						'icon' => 'self-end',
					],
					[
						'name' => __( 'stretch', 'zionbuilder' ),
						'id'   => 'stretch',
						'icon' => 'self-stretch',
					],
					[
						'name' => __( 'baseline', 'zionbuilder' ),
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
				'title'   => __( 'Order', 'zionbuilder' ),
				'sync'    => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.custom-order',
				'width'   => 60,
				'options' => [
					[
						'name' => __( 'first', 'zionbuilder' ),
						'id'   => -1,
					],
					[
						'name' => __( 'last', 'zionbuilder' ),
						'id'   => 99,
					],
				],
			]
		);

		$flex_child_group->add_option(
			'order',
			[
				'type'  => 'number',
				'title' => __( 'Custom Order', 'zionbuilder' ),
				'sync'  => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.order',
				'width' => 40,
			]
		);

		$spacing_group = $options->add_group(
			'padding',
			[
				'type'      => 'panel_accordion',
				'title'     => esc_html__( 'Spacing', 'zionbuilder' ),
				'sync'      => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.order',
				'collapsed' => true,
			]
		);

		$spacing_group->add_group(
			'padding',
			[
				'type'        => 'dimensions',
				'title'       => esc_html__( 'Padding', 'zionbuilder' ),
				'description' => esc_html__( 'Choose the desired padding for this element.', 'zionbuilder' ),
				'sync'        => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.padding',
				'min'         => 0,
				'max'         => 99999,
				'dimensions'  => [
					[
						'name' => 'top',
						'icon' => 'padding-top',
						'id'   => 'padding-top',
					],
					[
						'name' => 'right',
						'icon' => 'padding-right',
						'id'   => 'padding-right',
					],
					[
						'name' => 'bottom',
						'icon' => 'padding-bottom',
						'id'   => 'padding-bottom',
					],
					[
						'name' => 'left',
						'icon' => 'padding-left',
						'id'   => 'padding-left',
					],
				],
			]
		);
		$spacing_group->add_group(
			'margin',
			[
				'type'        => 'dimensions',
				'title'       => esc_html__( 'Margin', 'zionbuilder' ),
				'description' => esc_html__( 'Choose the desired margin for this element.', 'zionbuilder' ),
				'sync'        => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.margin',
				'min'         => -99999,
				'max'         => 99999,
				'dimensions'  => [
					[
						'name' => 'top',
						'icon' => 'margin-top',
						'id'   => 'margin-top',
					],
					[
						'name' => 'right',
						'icon' => 'margin-right',
						'id'   => 'margin-right',
					],
					[
						'name' => 'bottom',
						'icon' => 'margin-bottom',
						'id'   => 'margin-bottom',
					],
					[
						'name' => 'left',
						'icon' => 'margin-left',
						'id'   => 'margin-left',
					],
				],
			]
		);

		$sizings_group = $options->add_group(
			'sizing',
			[
				'type'      => 'panel_accordion',
				'title'     => esc_html__( 'Sizing', 'zionbuilder' ),
				'collapsed' => true,
			]
		);

		$sizings_group->add_option(
			'width',
			[
				'type'         => 'number_unit',
				'title'        => esc_html__( 'Width', 'zionbuilder' ),
				'sync'         => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.width',
				'width'        => 33.3,
				'default_unit' => 'px',
				'min'          => 0,
			]
		);

		$sizings_group->add_option(
			'min-width',
			[
				'type'         => 'number_unit',
				'title'        => esc_html__( 'Min Width', 'zionbuilder' ),
				'sync'         => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.min-width',
				'width'        => 33.3,
				'default_unit' => 'px',
				'min'          => 0,
			]
		);

		$sizings_group->add_option(
			'max-width',
			[
				'type'         => 'number_unit',
				'title'        => esc_html__( 'Max Width', 'zionbuilder' ),
				'sync'         => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.max-width',
				'width'        => 33.3,
				'min'          => 0,
				'default_unit' => 'px',
			]
		);

		$sizings_group->add_option(
			'height',
			[
				'type'         => 'number_unit',
				'title'        => esc_html__( 'Height', 'zionbuilder' ),
				'sync'         => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.height',
				'width'        => 33.3,
				'default_unit' => 'px',
				'min'          => 0,
			]
		);

		$sizings_group->add_option(
			'min-height',
			[
				'type'         => 'number_unit',
				'title'        => esc_html__( 'Min Height', 'zionbuilder' ),
				'sync'         => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.min-height',
				'width'        => 33.3,
				'default_unit' => 'px',
				'min'          => 0,
			]
		);

		$sizings_group->add_option(
			'max-height',
			[
				'type'         => 'number_unit',
				'title'        => esc_html__( 'Max Height', 'zionbuilder' ),
				'sync'         => '_styles.wrapper.styles.%%RESPONSIVE_DEVICE%%.default.max-height',
				'width'        => 33.3,
				'default_unit' => 'px',
				'min'          => 0,
			]
		);
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
		remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );
		remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
		// remove_action( 'woocommerce_checkout_order_review', 'woocommerce_order_review', 10 );
		// remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );
		// remove_action( 'woocommerce_checkout_terms_and_conditions', 'wc_checkout_privacy_policy_text', 20 );
		// remove_action( 'woocommerce_checkout_terms_and_conditions', 'wc_terms_and_conditions_page_content', 30 );
		// remove_action( 'woocommerce_checkout_before_customer_details', 'wc_get_pay_buttons', 30 );

		// Show non-cart errors.
		do_action( 'woocommerce_before_checkout_form_cart_notices' );

		// Check cart has contents.
		// TODO: add sample products in cart so you can see what your are styling even if you do not have products in cart
		if ( WC()->cart->is_empty() && ! is_customize_preview() && apply_filters( 'woocommerce_checkout_redirect_empty_cart', true ) ) {
			return;
		}

		// Check cart contents for errors.
		do_action( 'woocommerce_check_cart_items' );

		// Calc totals.
		WC()->cart->calculate_totals();

		// Get checkout object.
		$checkout = WC()->checkout();

		if ( empty( $_POST ) && wc_notice_count( 'error' ) > 0 ) { // WPCS: input var ok, CSRF ok.

			wc_get_template( 'checkout/cart-errors.php', array( 'checkout' => $checkout ) );
			wc_clear_notices();

		} else {

			$non_js_checkout = ! empty( $_POST['woocommerce_checkout_update_totals'] ); // WPCS: input var ok, CSRF ok.

			if ( wc_notice_count( 'error' ) === 0 && $non_js_checkout ) {
				wc_add_notice( __( 'The order totals have been updated. Please confirm your order by pressing the "Place order" button at the bottom of the page.', 'woocommerce' ) );
			}

			// Fire WOO actions for checkout form
			do_action( 'woocommerce_before_checkout_form', $checkout );

			if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
				echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
				return;
			}

			?>
			<form name="checkout" method="post" class="checkout woocommerce-checkout zb-woo-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

			<div class="zb-woo-checkout-container">
				<?php $this->render_children(); ?>
			</div>

			</form>

			<?php
			do_action( 'woocommerce_after_checkout_form', $checkout );

		}
	}
}
