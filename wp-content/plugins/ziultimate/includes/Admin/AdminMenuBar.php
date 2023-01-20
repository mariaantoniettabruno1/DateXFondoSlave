<?php
namespace ZiUltimate\Admin;

use ZiUltimate\Requirements;
use ZionBuilder\Whitelabel;
use ZionBuilder\Plugin;
use ZionBuilder\Permissions;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class AdminMenuBar
 *
 * @package ZiUltimate\Admin
 */
class AdminMenuBar {
	function __construct() {
		$settings = get_option('zu_settings');

		if( empty( $settings['menu_bar'] ) || $settings['menu_bar'] != 'quicklinks' )
			return;

		add_action( 'admin_bar_menu', [ $this, 'zu_add_link_to_admin_bar' ], 999 );
		if ( is_admin_bar_showing() ) {
			add_action('wp_head', function() {
				echo "<style type='text/css'>
						#wp-admin-bar-zu-general-elements{border-top: 1px solid hsla(0,0%,87%,.5); margin-top: 10px!important; padding-top: 5px!important;}
						#wp-admin-bar-zu-parent-item-default .ab-sub-wrapper {
							overflow-x: hidden;
							overflow-y: auto;
							max-height: 95vh;
						}
					</style>";
			});
		}
	}

	public function zu_add_link_to_admin_bar( $admin_bar ) {
		$menu_name = __( 'ZiUltimate', "ziultimate" );
		$menu_slug = 'ziultimate';

		$zuwl = get_option('zuwl');
		if( $zuwl ) {
			$menu_name = ! empty( $zuwl['menu_name'] ) ? esc_html( $zuwl['menu_name'] ) : $menu_name;
			$menu_slug = ! empty( $zuwl['menu_slug'] ) ? esc_html( $zuwl['menu_slug'] ) : $menu_slug;
		}

		// ziultimate parent menu
		$admin_bar->add_node([
			'id'    => 'zu-parent-item',
			'title' => $menu_name,
			'href' 	=> esc_url( admin_url( 'admin.php?page=' . $menu_slug ) ),
		]);

		$this->cpts_built_with_zion( $admin_bar );

		//* General Elements sub menu item
		$admin_bar->add_node([
			'parent' => 'zu-parent-item',
			'id'     => 'zu-general-elements',
			'title'  => esc_html__( 'General Elements', 'ziultimate' ),
			'href'   => esc_url( admin_url( 'admin.php?page=' . $menu_slug ) ),
		]);

		if( Requirements::passed_pro_plugin_requirements() ) {
			if( class_exists( 'WooCommerce' ) ) {
				//* Woo Elements sub menu
				$admin_bar->add_node([
					'parent' => 'zu-parent-item',
					'id'     => 'zu-woo-elements',
					'title'  => esc_html__( 'Woo Elements', 'ziultimate' ),
					'href'   => esc_url( admin_url( 'admin.php?page=' . $menu_slug . '&tab=woo' ) ),
				]);
			}

			//* Templates sub menu item
			$this->generate_templates_menu( $admin_bar );

			//* Theme Builder sub menu item
			$admin_bar->add_node([
				'parent' => 'zu-parent-item',
				'id'     => Whitelabel::get_id() . '-theme-builder',
				'title'  => esc_html( Whitelabel::get_title() . ' Theme Builder' ),
				'href'   => esc_url( admin_url( 'admin.php?page=' . sprintf( '%s-theme-builder', WhiteLabel::get_id() ) ) ),
			]);
		}
	}

	private function generate_templates_menu( $admin_bar )
	{
		$templates 	= Plugin::instance()->templates->get_templates_by_type('template');
		$blocks 	= Plugin::instance()->templates->get_templates_by_type('block');

		if( ! empty( $templates ) || ! empty( $blocks ) ) 
		{
			$admin_bar->add_node([
				'parent' => 'zu-parent-item',
				'id'     => Whitelabel::get_id() . '-templates',
				'title'  => esc_html( Whitelabel::get_title() . ' Templates' ),
				'href'   => esc_url( admin_url( 'admin.php?page=' . Whitelabel::get_id() . '#/templates/template' ) ),
			]);
		}
		
		if( $templates ) 
		{
			foreach( $templates as $template ) {
				$admin_bar->add_node([
					'parent' => Whitelabel::get_id() . '-templates',
					'id'     => Whitelabel::get_id() . '-tpl-' . $template->ID,
					'title'  => esc_attr( $template->post_title ),
					'href'   => esc_url( admin_url( 'post.php?post_id=' . $template->ID . '&action=zion_builder_active' ) ),
				]);
			}
		}

		if( $blocks ) 
		{
			foreach( $blocks as $block ) {
				$admin_bar->add_node([
					'parent' => Whitelabel::get_id() . '-templates',
					'id'     => Whitelabel::get_id() . '-block-' . $block->ID,
					'title'  => esc_attr( $block->post_title ),
					'href'   => esc_url( admin_url( 'post.php?post_id=' . $block->ID . '&action=zion_builder_active' ) ),
				]);
			}
		}
	}

	private function cpts_built_with_zion( $admin_bar )
	{
		//* CPTs built with zion builder
		$zion_allowed_post_types = Permissions::get_allowed_post_types('allowed_post_types');

		if( $zion_allowed_post_types ) :
			foreach( $zion_allowed_post_types as $type ) :
				
				if( $type == 'zion_template')
					continue;

				$posts = get_posts([
					'post_type' 	=> $type,
					'nopaging' 		=> true,
					'meta_query' 	=> [
						[
							'key' 		=> '_zionbuilder_page_status',
							'value' 	=> 'enabled',
							'compare' 	=> '='
						]
					]
				]);

				if( $posts ) 
				{
					$cpt = get_post_type_object( $type );

					if( ! $cpt )
						return;

					$admin_bar->add_node([
						'parent' => 'zu-parent-item',
						'id'     => 'zu-' . Whitelabel::get_id() . '-' . $type,
						'title'  => wp_kses_post( Whitelabel::get_title() . ' ' . $cpt->labels->name ),
						'href'   => esc_url( admin_url( 'edit.php?post_type=' . $type ) ),
					]);

					foreach( $posts as $post ) 
					{
						$admin_bar->add_node([
							'parent' => 'zu-' . Whitelabel::get_id() . '-' . $type,
							'id'     => 'zu-' . Whitelabel::get_id() . '-' . $type . '-' . $post->ID,
							'title'  => wp_kses_post( $post->post_title ),
							'href'   => esc_url( get_permalink( $post->ID ) ),
							//'href'   => esc_url( admin_url( 'post.php?post_id=' . $post->ID . '&action=zion_builder_active' ) ),
						]);
					} //endforeach
				} //endif
			endforeach;
		endif;
	}
}