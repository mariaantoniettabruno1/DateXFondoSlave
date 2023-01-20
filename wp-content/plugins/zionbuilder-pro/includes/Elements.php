<?php

namespace ZionBuilderPro;

class Elements {
	public function __construct() {
		add_filter( 'zionbuilder/elements/categories', [ $this, 'add_elements_categories' ] );
		add_action( 'zionbuilder/elements_manager/register_elements', [ $this, 'register_elements' ] );
		add_action( 'zionbuilder/editor/before_scripts', [ $this, 'enqueue_scripts' ] );
	}


	/**
	 * Get Elements Categories
	 *
	 * Returns all elements categories
	 *
	 * @param mixed $categories
	 *
	 * @return array
	 */
	public function add_elements_categories( $categories ) {
		$pro_categories = [
			[
				'id'   => 'pro',
				'name' => __( 'Pro Elements', 'zionbuilder-pro' ),
			],
		];

		return array_merge( $categories, $pro_categories );
	}


	/**
	 * Register default elements
	 *
	 * @param mixed $elements_manager
	 */
	public function register_elements( $elements_manager ) {
		/**
		 * The list of all the plugin's default elements
		 */
		$default_elements = [
			'Accordions\AccordionItem',
			'Countdown\Countdown',
			'CustomCode\CustomCode',
			'Search\Search',
			'SocialShare\SocialShare',
			'Tabs\Tabs',
			'Tabs\TabsItem',
			'Pagination\Pagination',
			'SliderBuilder\SliderBuilder',
			'SliderBuilder\SliderBuilderSlide',
			'Modal\Modal',
			'InnerContent\InnerContent',
			'Menu\Menu',
			'PostComments\PostComments',
		];

		foreach ( $default_elements as $element_name ) {
			// Normalize class name
			$class_name = str_replace( '-', '_', $element_name );
			$class_name = __NAMESPACE__ . '\\Elements\\' . $class_name;
			$elements_manager->register_element( new $class_name() );
		}
	}


	public function enqueue_scripts() {
		wp_localize_script(
			'zionbuilder-pro-elements-script',
			'ZionProRestConfig',
			[
				'nonce'     => wp_create_nonce( 'wp_rest' ),
				'rest_root' => esc_url_raw( rest_url() ),
			]
		);
	}
}
