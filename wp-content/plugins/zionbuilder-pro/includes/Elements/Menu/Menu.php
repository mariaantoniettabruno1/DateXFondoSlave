<?php

namespace ZionBuilderPro\Elements\Menu;

use ZionBuilder\Plugin as FreePlugin;
use ZionBuilder\Elements\Element;
use ZionBuilderPro\Utils;
use ZionBuilderPro\MegaMenu;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Menu
 * @package ZionBuilderPro\Elements\Menu
 */
class Menu extends Element {
	/**
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'menu';
	}

	/**
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Menu', 'zionbuilder' );
	}

	/**
	 * Returns the keywords for this element
	 *
	 * @return array<string> The list of element keywords
	 */
	public function get_keywords() {
		return [ 'menu' ];
	}

	/**
	 * Registers the element options
	 *
	 * @param \ZionBuilder\Options\Options $options The Options instance
	 *
	 * @return void
	 */
	public function options( $options ) {
		$menus = $this->get_menu_as_options();

		$options->add_option(
			'menu_id',
			[
				'type'        => 'select',
				'title'       => esc_html__( 'Select menu', 'zionbuilder' ),
				'description' => __( 'You can Create/Edit a menu by going to Appearance > Menus in WP Dashboard.', 'zionbuilder' ),
				'options'     => $menus,
				'default'     => count( $menus ) > 0 ? $menus[0]['id'] : '',
			]
		);
		//orientation
		$options->add_option(
			'orientation',
			[
				'title'   => esc_html__( 'Orientation', 'zionbuilder' ),
				'type'    => 'custom_selector',
				'default' => 'horizontal',
				'columns' => 2,
				'options' => [
					[
						'name' => __( 'Horizontal', 'zionbuilder' ),
						'id'   => 'horizontal',
					],
					[
						'name' => __( 'Vertical', 'zionbuilder' ),
						'id'   => 'vertical',
					],
				],
			]
		);
		$options->add_option(
			'vertical_submenu_style',
			[
				'title'       => esc_html__( 'Submenu style', 'zionbuilder' ),
				'type'        => 'select',
				'default'     => 'dropdown',
				'description' => esc_html__( 'Select how to display the submenu.', 'zionbuilder' ),
				'columns'     => 2,
				'options'     => [
					[
						'name' => __( 'Dropdown', 'zionbuilder' ),
						'id'   => 'dropdown',
					],
					[
						'name' => __( 'Accordion', 'zionbuilder' ),
						'id'   => 'accordion',
					],
				],
				'dependency'  => [
					[
						'option' => 'orientation',
						'value'  => [ 'vertical' ],
					],
				],
			]
		);

		//menu_alignment
		$options->add_option(
			'menu_alignment',
			[
				'type'        => 'custom_selector',
				'title'       => esc_html__( 'Menu alignment', 'zionbuilder' ),
				'default'     => 'start',
				'description' => esc_html__( 'Set the horizontal alignment for the menu.', 'zionbuilder' ),
				'columns'     => 4,
				'options'     => [
					[
						'name' => __( 'start', 'zionbuilder' ),
						'id'   => 'start',
						'icon' => 'align--left',
					],
					[
						'name' => __( 'center', 'zionbuilder' ),
						'id'   => 'center',
						'icon' => 'align--center',
					],
					[
						'name' => __( 'end', 'zionbuilder' ),
						'id'   => 'end',
						'icon' => 'align--right',
					],
					[
						'name' => __( 'stretch', 'zionbuilder' ),
						'id'   => 'stretch',
						'icon' => 'align--justify',
					],
				],
			]
		);

		$submenu_indicator_group = $options->add_group(
			'submenu_indicator_group',
			[
				'type'      => 'panel_accordion',
				'title'     => __( 'Submenu Indicator', 'zionbuilder' ),
				'collapsed' => true,
			]
		);

		//#! Submenu indicator
		$submenu_indicator_group->add_option(
			'submenu_indicator',
			[
				'type'        => 'checkbox_switch',
				'default'     => true,
				'description' => esc_html__( 'This option will display an icon next to the menu item that has a submenu.', 'zionbuilder' ),
				'layout'      => 'inline',
				'title'       => esc_html__( 'Display submenu indicator?', 'zionbuilder' ),
			]
		);
		//submenu_indicator_icon
		$submenu_indicator_group->add_option(
			'submenu_indicator_icon',
			[
				'type'       => 'icon_library',
				'id'         => 'icon',
				'title'      => esc_html__( 'Select Icon', 'zionbuilder' ),
				'default'    => [
					'family'  => 'Font Awesome 5 Free Solid',
					'name'    => 'chevron-down',
					'unicode' => 'uf078',
				],
				'dependency' => [
					[
						'option' => 'submenu_indicator',
						'value'  => [ true ],
					],
				],
			]
		);
		//submenu_indicator_icon_size
		$submenu_indicator_group->add_option(
			'submenu_indicator_icon_size',
			[
				'type'        => 'dynamic_slider',
				'default'     => '12px',
				'description' => esc_html__( 'Set the submenu indicator size. You can also modify it in the Styling tab.', 'zionbuilder' ),
				'title'       => esc_html__( 'Submenu indicator size', 'zionbuilder' ),
				'options'     => [
					[
						'unit' => 'px',
						'min'  => 0,
						'max'  => 999,
						'step' => 1,
					],
					[
						'unit' => '%',
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
					[
						'unit' => 'pt',
						'min'  => 0,
						'max'  => 999,
						'step' => 1,
					],
					[
						'unit' => 'em',
						'min'  => 0,
						'max'  => 999,
						'step' => 1,
					],
					[
						'unit' => 'rem',
						'min'  => 0,
						'max'  => 999,
						'step' => 1,
					],
				],
				'css_style'   => [
					[
						'selector' => '{{ELEMENT}} .zb-submenu-indicator',
						'value'    => 'font-size: {{VALUE}}',
					],

				],
				'dependency'  => [
					[
						'option' => 'submenu_indicator',
						'value'  => [ true ],
					],
				],
			]
		);

		//#! Flip submenu indicator
		$submenu_indicator_group->add_option(
			'flip_submenu_indicator',
			[
				'type'        => 'checkbox_switch',
				'default'     => true,
				'description' => esc_html__( 'Enabling this option will cause the submenu indicator to flip im the opposite direction when the submenu will open', 'zionbuilder' ),
				'title'       => esc_html__( 'Flip the indicator when the submenu opens?', 'zionbuilder' ),
				'dependency'  => [
					[
						'option' => 'submenu_indicator',
						'value'  => [ true ],
					],
				],
			]
		);

		$mobile_menu_options = $options->add_group(
			'mobile_menu_options',
			[
				'type'      => 'panel_accordion',
				'title'     => __( 'Mobile Menu', 'zionbuilder' ),
				'collapsed' => true,
			]
		);

		//collapse_breakpoint
		$mobile_menu_options->add_option(
			'collapse_breakpoint',
			[
				'type'        => 'slider',
				'title'       => esc_html__( 'Collapse to mobile at', 'zionbuilder' ),
				'description' => esc_html__( 'When the mobile menu should appear. Please note that the mobile menu will be displayed before the specified value (in pixels).', 'zionbuilder' ),
				'min'         => 0,
				'max'         => 2560,
				'default'     => 991,
			]
		);
		//mobile_menu_type
		//#! Enable when there will be more than one option
		//#! Until then, the hard-coded "collapsible" will be used
		//#! since it will be pointless to have it as an option
		//      $mobile_menu_options->add_option(
		//          'mobile_menu_type',
		//          [
		//              'type'    => 'select',
		//              'title'   => esc_html__( 'Mobile menu type', 'zionbuilder' ),
		//              'default' => 'collapsible',
		//              'options' => [
		//                  [
		//                      'id'   => 'collapsible',
		//                      'name' => esc_html__( 'Collapsible', 'zionbuilder' ),
		//                  ],
		//              ],
		//          ]
		//      );

		//#! Submenu indicator
		$mobile_menu_options->add_option(
			'mobile_menu_fullwidth',
			[
				'type'        => 'checkbox_switch',
				'default'     => false,
				'description' => esc_html__( 'Choose whether you want the mobile menu to be full-width.', 'zionbuilder' ),
				'layout'      => 'inline',
				'title'       => esc_html__( 'Mobile menu full-width?', 'zionbuilder' ),
			]
		);

		//mobile_menu_alignment
		$mobile_menu_options->add_option(
			'mobile_menu_alignment',
			[
				'type'        => 'custom_selector',
				'title'       => esc_html__( 'Mobile menu alignment', 'zionbuilder' ),
				'default'     => 'center',
				'description' => esc_html__( 'Set the horizontal alignment for the mobile menu.', 'zionbuilder' ),
				'columns'     => 3,
				'options'     => [
					[
						'name' => __( 'left', 'zionbuilder' ),
						'id'   => 'start',
						'icon' => 'align--left',
					],
					[
						'name' => __( 'center', 'zionbuilder' ),
						'id'   => 'center',
						'icon' => 'align--center',
					],
					[
						'name' => __( 'right', 'zionbuilder' ),
						'id'   => 'end',
						'icon' => 'align--right',
					],
				],
			]
		);

		//mobile_menu_trigger_alignment
		$mobile_menu_options->add_option(
			'mobile_menu_trigger_alignment',
			[
				'type'        => 'custom_selector',
				'title'       => esc_html__( 'Mobile menu trigger alignment', 'zionbuilder' ),
				'default'     => 'end',
				'description' => esc_html__( 'Set the horizontal alignment for the mobile menu trigger.', 'zionbuilder' ),
				'columns'     => 3,
				'options'     => [
					[
						'name' => __( 'left', 'zionbuilder' ),
						'id'   => 'start',
						'icon' => 'align--left',
					],
					[
						'name' => __( 'center', 'zionbuilder' ),
						'id'   => 'center',
						'icon' => 'align--center',
					],
					[
						'name' => __( 'right', 'zionbuilder' ),
						'id'   => 'end',
						'icon' => 'align--right',
					],
				],
			]
		);

		//mobile_menu_trigger_title
		$mobile_menu_options->add_option(
			'mobile_menu_trigger_title',
			[
				'type'        => 'text',
				'title'       => esc_html__( 'Mobile menu trigger title', 'zionbuilder' ),
				'description' => esc_html__( 'This title will appear next to the trigger icon.', 'zionbuilder' ),
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
		$this->enqueue_editor_script( Utils::get_file_url( 'dist/elements/Menu/editor.js' ) );
		$this->enqueue_element_script( Utils::get_file_url( 'dist/elements/Menu/frontend.js' ) );
	}

	/**
	 * Enqueue element styles for both frontend and editor
	 *
	 * If you want to use the ZionBuilder cache system you must use
	 * the enqueue_editor_style(), enqueue_element_style() functions
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		// Using helper methods will go through caching policy
		$this->enqueue_element_style( Utils::get_file_url( 'dist/elements/Menu/frontend.css' ) );
	}

	public function on_register_styles() {
		//#! DESKTOP MENU
		$this->register_style_options_element(
			'first_level_menu_items_styles',
			[
				'title'    => esc_html__( 'First level menu items', 'zionbuilder-pro' ),
				'selector' => '{{ELEMENT}} .zb-menu:not(.zb-menu-mobile--active) .zb-menu-list > li > a',
			]
		);
		$this->register_style_options_element(
			'first_level_menu_items_active_state_styles',
			[
				'title'    => esc_html__( 'First level menu items active state', 'zionbuilder-pro' ),
				'selector' => '{{ELEMENT}} .zb-menu:not(.zb-menu-mobile--active) .zb-menu-list > li.current-menu-item > a',
			]
		);

		//#! DESKTOP SUBMENU
		$this->register_style_options_element(
			'submenu_wrapper_styles',
			[
				'title'    => esc_html__( 'Submenu wrapper', 'zionbuilder-pro' ),
				'selector' => '{{ELEMENT}} .zb-menu:not(.zb-menu-mobile--active) .zb-menu-list .sub-menu',
			]
		);
		$this->register_style_options_element(
			'submenu_items_styles',
			[
				'title'    => esc_html__( 'Submenu items', 'zionbuilder-pro' ),
				'selector' => '{{ELEMENT}} .zb-menu:not(.zb-menu-mobile--active) .zb-menu-list .sub-menu a',
			]
		);
		$this->register_style_options_element(
			'submenu_items_active_state_styles',
			[
				'title'    => esc_html__( 'Submenu items active state', 'zionbuilder-pro' ),
				'selector' => '{{ELEMENT}} .zb-menu:not(.zb-menu-mobile--active) .zb-menu-list .sub-menu li.current-menu-item > a',
			]
		);

		//#! MOBILE MENU TRIGGER
		$this->register_style_options_element(
			'mobile_menu_trigger_styles',
			[
				'title'    => esc_html__( 'Mobile menu trigger', 'zionbuilder-pro' ),
				'selector' => '{{ELEMENT}} .zb-menu-trigger',
			]
		);

		//#! MOBILE MENU
		$this->register_style_options_element(
			'mobile_menu_styles',
			[
				'title'    => esc_html__( 'Mobile menu wrapper', 'zionbuilder-pro' ),
				'selector' => '{{ELEMENT}} .zb-menu.zb-menu-mobile--active .zb-menu-list',
			]
		);
		$this->register_style_options_element(
			'mobile_menu_item_styles',
			[
				'title'    => esc_html__( 'Mobile menu items', 'zionbuilder-pro' ),
				'selector' => '{{ELEMENT}} .zb-menu.zb-menu-mobile--active .zb-menu-list a',
			]
		);
		$this->register_style_options_element(
			'mobile_menu_items_active_state_styles',
			[
				'title'    => esc_html__( 'Mobile menu items active state', 'zionbuilder-pro' ),
				'selector' => '{{ELEMENT}} .zb-menu.zb-menu-mobile--active .zb-menu-list li.current-menu-item > a',
			]
		);
		$this->register_style_options_element(
			'mobile_submenu_wrapper_styles',
			[
				'title'    => esc_html__( 'Mobile submenu wrapper', 'zionbuilder-pro' ),
				'selector' => '{{ELEMENT}} .zb-menu.zb-menu-mobile--active .zb-menu-list .sub-menu',
			]
		);
		$this->register_style_options_element(
			'mobile_submenu_items_styles',
			[
				'title'    => esc_html__( 'Mobile submenu items', 'zionbuilder-pro' ),
				'selector' => '{{ELEMENT}} .zb-menu.zb-menu-mobile--active .zb-menu-list .sub-menu a',
			]
		);
		$this->register_style_options_element(
			'mobile_submenu_items_active_state_styles',
			[
				'title'    => esc_html__( 'Mobile submenu items active state', 'zionbuilder-pro' ),
				'selector' => '{{ELEMENT}} .zb-menu.zb-menu-mobile--active .zb-menu-list .sub-menu li.current-menu-item > a',
			]
		);
	}

	/**
	 * Renders the element based on options
	 *
	 * @param \ZionBuilder\Options\Options $options
	 *
	 * @return void
	 */
	public function render( $options ) {
		$menu_id        = $options->get_value( 'menu_id' );
		$orientation    = $options->get_value( 'orientation', 'horizontal' );
		$menu_alignment = $options->get_value( 'menu_alignment', 'start' );

		// If we don't have a menu, use the first one
		if ( empty( $menu_id ) ) {
			$menus = wp_get_nav_menus();
			if ( ! empty( $menus[0] ) ) {
				$menu_id = $menus[0]->term_id;
			}
		}

		if ( empty( $menu_id ) ) {
			$this->render_placeholder_info(
				[
					'title'       => esc_html__( 'No menu selected.', 'zionbuilder-pro' ),
					'description' => esc_html__( 'Please select a menu from the element options panel.', 'zionbuilder-pro' ),
				]
			);
			return;
		}

		$submenu_indicator      = $options->get_value( 'submenu_indicator' );
		$submenu_indicator_icon = $options->get_value( 'submenu_indicator_icon' );

		//#! Mobile menu alignment
		$mobile_menu_alignment = $options->get_value( 'mobile_menu_alignment', 'start' );
		//#! Mobile menu trigger
		$mobile_menu_trigger_alignment = $options->get_value( 'mobile_menu_trigger_alignment', 'start' );
		$mobile_menu_trigger_title     = $options->get_value( 'mobile_menu_trigger_title' );
		$mobile_menu_full_width        = $options->get_value( 'mobile_menu_fullwidth', false );
		$mobile_menu_classes           = $this->get_style_classes_as_string( 'mobile_menu_trigger_styles' );

		$vertical_submenu_style = $options->get_value( 'vertical_submenu_style' );

		//#!
		$menuOrientation  = $options->get_value( 'orientation' );
		$mobile_menu_type = 'collapsible';
		$data             = [
			'orientation'            => $menuOrientation,
			'vertical_submenu_style' => $vertical_submenu_style,
			'mobile_menu_full_width' => $mobile_menu_full_width,
			'breakpoint'             => $options->get_value( 'collapse_breakpoint' ),
		];
		$cssClass         = ( $menuOrientation == 'vertical' && $vertical_submenu_style == 'accordion' ? 'zb-menu--accordion' : '' );
		$isMobileFullSize = $mobile_menu_full_width ? 'zb-menu--mobile--full-width' : ''
		?>
		<div class="zb-menu <?php echo esc_attr( "{$cssClass} {$mobile_menu_type} {$isMobileFullSize}" ); ?>  js-zb-menu" data-zb-menu="<?php echo esc_attr( json_encode( $data ) ); ?>">
			<div class="zb-menu-trigger js-zb-mobile-menu-trigger <?php echo $mobile_menu_classes; ?>" data-align="<?php echo esc_attr( $mobile_menu_trigger_alignment ); ?>">
				<span class="zb-menu-trigger__text"><?php echo esc_html( $mobile_menu_trigger_title ); ?></span>
				<span class="zb-menu-trigger__hamburger">
					<span></span>
					<span></span>
					<span></span>
				</span>
			</div>
			<?php
			$submenu_indicator_html = '';
			if ( $submenu_indicator ) {
				$this->attach_icon_attributes( 'submenu_indicator', $submenu_indicator_icon );
				$this->render_attributes->add( 'submenu_indicator', 'class', 'zb-submenu-indicator' );
				$submenu_indicator_html = $this->get_render_tag( 'span', 'submenu_indicator' );
			}
			$flip_submenu_indicator = $options->get_value( 'flip_submenu_indicator', true ) ? 'zb-submenu-indicator--flip' : '';

			$GLOBALS['zb_el_menu_indicator'] = $submenu_indicator_html;
			wp_nav_menu(
				[
					'menu'            => intval( $menu_id ),
					'menu_class'      => sprintf( 'zb-menu-list zb-align--%s zb-mAlign--%s zb-orientation--%s %s', $menu_alignment, $mobile_menu_alignment, $orientation, $flip_submenu_indicator ),
					'container'       => 'div',
					'container_class' => 'zb-menu-container',
					'walker'          => new ZionMenuWalker(),
				]
			);
			?>
		</div>
		<?php
	}

	/**
	 * Get Children
	 *
	 * Returns an array containing all children of this element.
	 * If the element can have multiple content areas ( for example tabs or accordions ) it will loop trough all areas
	 * and returns all it's children
	 *
	 * @return array<int, mixed>
	 */
	public function get_children() {
		$child_elements = array();
		$options        = $this->options;
		$menu_id        = $options->get_value( 'menu_id' );

		// If we don't have a menu, use the first one
		if ( empty( $menu_id ) ) {
			$menus = wp_get_nav_menus();
			if ( ! empty( $menus[0] ) ) {
				$menu_id = $menus[0]->term_id;
			}
		}

		//$menu = wp_get_nav_menu_object( $locations[ $menu_location ] );
		$menu = wp_get_nav_menu_object( $menu_id );
		if ( ! $menu ) {
			return [];
		}

		$menu_items = wp_get_nav_menu_items( $menu->term_id, array( 'update_post_term_cache' => false ) );

		if ( ! is_array( $menu_items ) ) {
			return [];
		}

		foreach ( $menu_items as $key => $menu_item ) {
			if ( $menu_item->menu_item_parent != '0' ) {
				continue;
			}

			$mega_menu_data     = MegaMenu::get_config_for_item( $menu_item->ID );
			$mega_menu_template = MegaMenu::get_pagebuilder_template( $menu_item->ID );

			if ( isset( $mega_menu_data['content_enabled'] ) && $mega_menu_data['content_enabled'] && $mega_menu_template ) {

				$post_instance = FreePlugin::instance()->post_manager->get_post_instance( $mega_menu_template );

				if ( ! $post_instance || ! $post_instance->is_built_with_zion() ) {
					continue;
				}

				// Register the template area
				$post_template_data = $post_instance->get_template_data();
				FreePlugin::$instance->renderer->register_area( $mega_menu_template, $post_template_data );
				// $child_elements = array_merge($child_elements, $post_template_data);
			}
		}

		return $child_elements;

	}

	/**
	 * Will render the element without the wrappers
	 *
	 * @return void
	 */
	public function server_render( $request ) {
		$this->options->parse_data();
		$this->get_children();
		$this->render( $this->options );

		$element_html_id = $this->get_css_selector();

		echo "<script type='text/javascript'>
			var menuEl = document.querySelector('{$element_html_id} .zb-menu');
			window.zbScripts.menu(menuEl);
		</script>";
	}


	/**
	 * Retrieve all menus from WordPress
	 * @return array
	 */
	private function get_menu_as_options() {
		$out   = [];
		$menus = wp_get_nav_menus();
		if ( ! empty( $menus ) ) {
			foreach ( $menus as $term ) {
				$out[] = [
					'id'   => $term->term_id,
					'name' => $term->name,
				];
			}
		}

		return $out;
	}
}
