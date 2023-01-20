<?php

namespace ZionBuilderPro\Elements\Menu;

use ZionBuilderPro\MegaMenu;
use ZionBuilder\Icons;
use ZionBuilder\Plugin as FreePlugin;

class ZionMenuWalker extends \Walker_Nav_Menu {

	private $zb_mm_submenu_classes = [];
	private $zb_mm_submenu_styles  = '';

	private function get_mega_menu_config( $item ) {
		$mega_menu_data = MegaMenu::get_config_for_item( $item->ID );
		$defaults       = [
			'content_enabled'      => false,
			'submenu_width'        => 'default',
			'submenu_width_custom' => '',
			'submenu_position'     => 'default',
			'icon'                 => false,
			'icon_position'        => 'left',
			'icon_color'           => '',
			'badget_text'          => '',
			'badge_color'          => '',
			'text_color'           => '',
			'show_title'           => true,
		];

		return wp_parse_args( $mega_menu_data, $defaults );
	}

	/**
	 * Starts the element output.
	 *
	 * @param string $output Used to append additional content (passed by reference).
	 * @param \WP_Post $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param \stdClass $args An object of wp_nav_menu() arguments.
	 * @param int $id Current item ID.
	 *
	 * @see Walker::start_el()
	 *
	 * @since 3.0.0
	 * @since 4.4.0 The {@see 'nav_menu_item_args'} filter was added.
	 *
	 */
	function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		global $wp_query, $zb_el_menu_indicator;
		$indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // code indent

		$mega_menu_data     = $this->get_mega_menu_config( $item );
		$mega_menu_template = MegaMenu::get_pagebuilder_template( $item->ID );

		// Depth-dependent classes.
		$depth_classes     = [
			( $depth == 0 ? 'main-menu-item' : 'sub-menu-item' ),
			( $depth >= 2 ? 'sub-sub-menu-item' : '' ),
			( $depth % 2 ? 'menu-item-odd' : 'menu-item-even' ),
			'menu-item-depth-' . $depth,
		];
		$depth_class_names = esc_attr( implode( ' ', $depth_classes ) );

		// Passed classes.
		$classes = empty( $item->classes ) ? [] : (array) $item->classes;

		// Check for item mega menu classes
		if ( ! empty( $mega_menu_data['icon'] ) && ! empty( $mega_menu_data['icon_position'] ) ) {
			$classes[] = sprintf( 'zb-menuIcon--%s', $mega_menu_data['icon_position'] );
		}

		// Add position width
		$classes[] = sprintf( 'zb-menuWidth--%s', $mega_menu_data['submenu_width'] );

		$classes[] = sprintf( 'zb-menuPosition--%s', $mega_menu_data['submenu_position'] );

		// Check for container width
		if ( $mega_menu_data['submenu_width'] === 'container' ) {
			array_push( $this->zb_mm_submenu_classes, 'zb-section__innerWrapper' );
		}

		// Check for custom width
		if ( $mega_menu_data['submenu_width'] === 'custom' && ! empty( $mega_menu_data['submenu_width_custom'] ) ) {
			$this->zb_mm_submenu_styles .= sprintf( 'width: %s;', $mega_menu_data['submenu_width_custom'] );
		}

		// Add the has-children css class in case this is a mega menu
		if ( $mega_menu_data['content_enabled'] && ! empty( $mega_menu_template ) ) {
			$classes[] = 'menu-item-has-children';
		}

		$class_names = esc_attr( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) ) );

		// Build HTML.
		$output .= $indent . '<li id="nav-menu-item-' . $item->ID . '" class="' . $depth_class_names . ' ' . $class_names . '">';

		// Link attributes.
		$attributes  = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) . '"' : '';
		$attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) . '"' : '';
		$attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) . '"' : '';
		$attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) . '"' : '';
		$attributes .= ' class="menu-link ' . ( $depth > 0 ? 'sub-menu-link' : 'main-menu-link' ) . '"';

		$submenu_indicator = '';
		if ( $zb_el_menu_indicator && in_array( 'menu-item-has-children', $classes ) ) {
			$submenu_indicator = $zb_el_menu_indicator;
		}

		$item_output  = '';
		$item_output  = $args->before;
		$item_output .= '<a' . $attributes . '>';

		if ( $mega_menu_data['icon'] ) {
			$icon_attributes          = $mega_menu_data['icon'] ? Icons::get_icon_attributes( $mega_menu_data['icon'] ) : [];
			$icon_attributes['class'] = [ 'zb-menuIcon' ];
			$item_output             .= sprintf( '<span %s></span>', $this->implode_attributes( $icon_attributes ) );
		}

		$item_output .= $args->link_after;

		if ( $mega_menu_data['show_title'] ) {
			$item_output .= '<span class="zb-menuTitle">' . apply_filters( 'the_title', $item->title, $item->ID ) . '</span>';
		}

		$item_output .= $args->link_after;

		// Badge
		if ( $mega_menu_data['badget_text'] ) {
			$badge_attributes          = [];
			$badge_attributes['class'] = [ 'zb-menuBadge' ];
			$badge_attributes['style'] = '';

			if ( ! empty( $mega_menu_data['badge_color'] ) ) {
				$badge_attributes['style'] .= sprintf( 'background-color:%s;', $mega_menu_data['badge_color'] );
			}

			if ( ! empty( $mega_menu_data['text_color'] ) ) {
				$badge_attributes['style'] .= sprintf( 'color:%s;', $mega_menu_data['text_color'] );
			}

			$item_output .= sprintf( '<span %s>%s</span>', $this->implode_attributes( $badge_attributes ), $mega_menu_data['badget_text'] );
		}

		$item_output .= $submenu_indicator . '</a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	function implode_attributes( $attributes ) {
		return implode(
			' ',
			array_map(
				function ( $k, $v ) {
					$value = is_array( $v ) ? implode( ' ', $v ) : $v;
					if ( ! empty( $value ) ) {
						return sprintf( '%s="%s"', esc_attr( $k ), esc_attr( $value ) );
					}
				},
				array_keys( $attributes ),
				$attributes
			)
		);
	}

	/**
	 * Starts the list before the elements are added.
	 *
	 * @since 3.0.0
	 *
	 * @see Walker::start_lvl()
	 *
	 * @param string   $output Used to append additional content (passed by reference).
	 * @param int      $depth  Depth of menu item. Used for padding.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent = str_repeat( $t, $depth );

		// Default class.
		$classes = $this->zb_mm_submenu_classes;
		array_push( $classes, 'sub-menu' );

		$styles = '';
		if ( ! empty( $this->zb_mm_submenu_styles ) ) {
			$styles = sprintf( 'style="%s"', $this->zb_mm_submenu_styles );

			$this->zb_mm_submenu_styles = '';
		}

		/**
		 * Filters the CSS class(es) applied to a menu list element.
		 *
		 * @since 4.8.0
		 *
		 * @param string[] $classes Array of the CSS classes that are applied to the menu `<ul>` element.
		 * @param stdClass $args    An object of `wp_nav_menu()` arguments.
		 * @param int      $depth   Depth of menu item. Used for padding.
		 */
		$class_names = implode( ' ', apply_filters( 'nav_menu_submenu_css_class', $classes, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		// Reset menu classes
		$this->zb_mm_submenu_classes = [];

		$output .= "{$n}{$indent}<ul$class_names $styles>{$n}";
	}

	/**
	 * Ends the element output, if needed.
	 *
	 * @see Walker::end_el()
	 *
	 * @param string   $output Used to append additional content (passed by reference).
	 * @param WP_Post  $item   Page data object. Not used.
	 * @param int      $depth  Depth of page. Not Used.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 */
	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}

		// Check to see if we need to inject the mega menu
		$mega_menu_data     = $this->get_mega_menu_config( $item );
		$mega_menu_template = MegaMenu::get_pagebuilder_template( $item->ID );

		if ( $mega_menu_data['content_enabled'] && ! empty( $mega_menu_template ) ) {
			$post_instance = FreePlugin::instance()->post_manager->get_post_instance( $mega_menu_template );

			if ( ! $post_instance || ! $post_instance->is_built_with_zion() ) {
				return false;
			}

			$post_template_data = $post_instance->get_template_data();

			$classes = $this->zb_mm_submenu_classes;
			array_push( $classes, 'sub-menu' );

			/**
			 * Filters the CSS class(es) applied to a menu list element.
			 *
			 * @since 4.8.0
			 *
			 * @param string[] $classes Array of the CSS classes that are applied to the menu `<ul>` element.
			 * @param stdClass $args    An object of `wp_nav_menu()` arguments.
			 * @param int      $depth   Depth of menu item. Used for padding.
			 */
			$class_names = implode( ' ', apply_filters( 'nav_menu_submenu_css_class', $classes, $args, $depth ) );

			// Reset menu classes
			$this->zb_mm_submenu_classes = [];

			$styles = '';
			if ( ! empty( $this->zb_mm_submenu_styles ) ) {
				$styles                     = sprintf( 'style="%s"', $this->zb_mm_submenu_styles );
				$this->zb_mm_submenu_styles = '';
			}

			$output .= '<ul class="sub-menu zb-mmContent ' . esc_attr( $class_names ) . '" ' . $styles . '>';
			$output .= FreePlugin::instance()->renderer->get_content( $mega_menu_template, $post_template_data );
			$output .= '</ul>';
		}

		$output .= "</li>{$n}";
	}
}
