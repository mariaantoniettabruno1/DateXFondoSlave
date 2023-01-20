<?php

namespace ZionBuilderPro\Elements\Search;

use ZionBuilder\Elements\Element;
use ZionBuilderPro\Utils;
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Search
 *
 * @package ZionBuilderPro\Elements
 */
class Search extends Element {
	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'search';
	}

	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Search', 'zionbuilder-pro' );
	}

	/**
	 * Get Category
	 *
	 * Will return the element category
	 *
	 * @return string
	 */
	public function get_category() {
		return 'pro';
	}

	/**
	 * Get keywords
	 *
	 * Returns the keywords for this element
	 *
	 * @return array The list of element keywords
	 */
	public function get_keywords() {
		return [ 'search', 'find', 'src' ];
	}


	/**
	 * Get Element Icon
	 *
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'element-search-form';
	}

	public function options( $options ) {
		$options->add_option(
			'show_button',
			[
				'type'        => 'checkbox_switch',
				'default'     => true,
				'layout'      => 'inline',
				'title'       => esc_html__( 'Show search button', 'zionbuilder-pro' ),
				'description' => esc_html__( 'If no button appears, search will be triggered on enter key', 'zionbuilder-pro' ),

			]
		);

		$options->add_option(
			'search_text',
			[
				'type'        => 'text',
				'default'     => 'Search',
				'title'       => esc_html__( 'Button search text', 'zionbuilder-pro' ),
				'description' => esc_html__( 'Type text to appear on button', 'zionbuilder-pro' ),
				'dependency'  => [
					[
						'option' => 'show_button',
						'value'  => [ true ],
					],
				],
			]
		);

		$options->add_option(
			'placeholder_text',
			[
				'type'        => 'text',
				'title'       => esc_html__( 'Placeholder text', 'zionbuilder-pro' ),
				'description' => esc_html__( 'Set the desired text to appear inside', 'zionbuilder-pro' ),
				'placeholder' => esc_html__( 'Placeholder', 'zionbuilder-pro' ),
			]
		);

		$options->add_option(
			'woocommerce',
			[
				'type'        => 'checkbox_switch',
				'default'     => false,
				'title'       => esc_html__( 'Check within WooCommerce products', 'zionbuilder-pro' ),
				'description' => esc_html__( 'If you have wooCommerce installed check this box', 'zionbuilder-pro' ),

			]
		);
	}

	/**
	 * Enqueue element scripts for both frontend and editor
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		// Using helper methods will go through caching policy
		$this->enqueue_editor_script( Utils::get_file_url( 'dist/elements/Search/editor.js' ) );
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
		$search_query = get_search_query();
		$placeholder  = $options->get_value( 'placeholder_text' );
		$woocommerce  = $options->get_value( 'woocommerce' );
		$show_button  = $options->get_value( 'show_button' );
		$button_text  = $options->get_value( 'search_text' );

		$compiled_placeholder = ! empty( $placeholder ) ? $placeholder : 'search'; ?>
		<form class="zb-el-search__form" action="<?php echo home_url(); ?>" method="get" role="search">

			<input name="s" maxlength="30" class="zb-el-search__input" type="text" size="20" value="<?php echo esc_attr( $search_query ); ?>" placeholder="<?php echo esc_attr( $compiled_placeholder ); ?>" />
			<?php
			if ( $show_button ) {
				?>
				<button type="submit" class="zb-el-search__submit"><?php echo esc_html( $button_text ); ?></button>
			<?php } ?>



			<?php
			if ( $woocommerce && in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				?>
				<input type="hidden" name="post_type" value="product"/>
			<?php } ?>

		</form>
		<?php
	}

	public function on_register_styles() {
		$this->register_style_options_element(
			'form_styles',
			[
				'title'                   => esc_html__( 'Form', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} form',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'input_styles',
			[
				'title'                   => esc_html__( 'Input', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .zb-el-search__input',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);

		$this->register_style_options_element(
			'button_styles',
			[
				'title'                   => esc_html__( 'Button', 'zionbuilder-pro' ),
				'selector'                => '{{ELEMENT}} .zb-el-search__submit',
				'allow_custom_attributes' => false,
				'allow_class_assignments' => false,
			]
		);
	}
}
