<?php

namespace ZionBuilderPro\ThemeBuilder;

use ZionBuilder\Plugin as FreePlugin;
use ZionBuilder\CommonJS;
use ZionBuilderPro\Plugin;
use ZionBuilderPro\Utils;
use ZionBuilderPro\ThemeBuilder\RestController;
use ZionBuilderPro\Conditions\PageRequest;
use ZionBuilderPro\Conditions\Conditions;
use ZionBuilder\WhiteLabel;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

class ThemeBuilder {
	private $registered_templates = [];
	private $admin_page_hook      = null;

	const TEMPLATES_CONFIG_DB_ID    = '_zionbuilder_site_templates';
	const TEMPLATES_AREA_META_FIELD = 'zionbuilder_template_themebuilder_area';

	public function __construct() {
		if ( is_admin() ) {
			// Register theme builder menu
			add_action( 'admin_menu', [ $this, 'add_menu_pages' ] );

			// Enqueue scripts
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		}

		add_action( 'zionbuilder/frontend/init', [ $this, 'on_zion_frontend_init' ] );
		add_action( 'zionbuilder/rest_api/register_controllers', [ $this, 'register_rest_controller' ] );
		add_action( 'zionbuilder/rest/templates/added', [ $this, 'on_added_template' ], 10, 2 );
		add_action( 'zionbuilderpro/conditions/register_saved_data', [ $this, 'register_conditions_for_data' ] );
		add_filter( 'zionbuilder/post/zion_template/data_for_api', [ $this, 'attach_template_area' ] );
		add_action( 'zionbuilder/templates/before_init', [ $this, 'register_template_types' ] );

		// Filter dynamic data source
		add_filter( 'zionbuilderpro/dynamic_data/source_values', [ $this, 'set_dynamic_data_source' ] );

		// Add theme builder link in admin bar
		add_action( 'zionbuilder/admin-bar/register_menu_items', [ $this, 'add_wp_admin_bar_menu_items' ] );

		// Style the admin bar items
		add_action( 'wp_head', [ $this, 'add_admin_bar_styles' ], 999 );
	}

	/**
	 * Will save the theme builder area type
	 *
	 * @param \WP_Post $template
	 * @param \WP_REST_Request $request
	 *
	 * @return void
	 */
	public function on_added_template( $template_instance, $request ) {
		$template_type      = $request->get_param( 'template_type' );
		$template_area_type = $request->get_param( 'theme_area' );

		if ( $template_type === 'theme_builder' ) {
			$template_id = $template_instance->get_post_id();
			update_post_meta( $template_id, self::TEMPLATES_AREA_META_FIELD, $template_area_type );
		}
	}

	public function attach_template_area( $template ) {
		if ( $template['type'] === 'theme_builder' ) {
			$template['theme_area'] = get_post_meta( $template['id'], self::TEMPLATES_AREA_META_FIELD, true );
			$template['category']   = [ get_post_meta( $template['id'], self::TEMPLATES_AREA_META_FIELD, true ) ];
		}

		return $template;
	}

	public function register_rest_controller( $rest_manager ) {
		$rest_manager->register_controller( new RestController() );
	}

	public static function update_site_templates( $templates_config ) {
		return update_option( self::TEMPLATES_CONFIG_DB_ID, $templates_config, true );
	}

	public function get_template_configs() {
		return get_option(
			self::TEMPLATES_CONFIG_DB_ID,
			[
				'default_template' => null,
				'templates'        => [],
			]
		);
	}

	public function register_template_types( $template_manager ) {
		// Register headers
		$template_manager->register_template_type(
			[
				'name'          => __( 'Headers', 'zionbuilder-pro' ),
				'singular_name' => __( 'Header', 'zionbuilder-pro' ),
				'id'            => 'header',
			]
		);

		$template_manager->register_template_type(
			[
				'name'          => __( 'Body', 'zionbuilder-pro' ),
				'singular_name' => __( 'Body', 'zionbuilder-pro' ),
				'id'            => 'body',
			]
		);

		$template_manager->register_template_type(
			[
				'name'          => __( 'Footers', 'zionbuilder-pro' ),
				'singular_name' => __( 'Footer', 'zionbuilder-pro' ),
				'id'            => 'footer',
			]
		);
	}

	public function set_proper_template_category( $item_data ) {
		if ( $item_data['type'] === 'theme_builder' ) {
			$item_data['category'] = [ get_post_meta( $item_data['id'], self::TEMPLATES_AREA_META_FIELD, true ) ];
		}
		return $item_data;
	}

	public function enqueue_scripts( $hook ) {
		if ( $this->admin_page_hook === $hook ) {
			CommonJS::register_scripts();

			wp_enqueue_style(
				'zion-pro-theme-builder-styles',
				Utils::get_file_url( 'dist/theme-builder.css' ),
				[],
				Plugin::instance()->get_version()
			);

			wp_enqueue_style( 'RobotoFont', 'https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;1,400;1,500&display=swap', [], Plugin::instance()->get_version() );

			// Enqueue media script
			wp_enqueue_media();
			if ( is_rtl() ) {
				wp_enqueue_style(
					'zion-pro-theme-builder-rtl-styles',
					Utils::get_file_url( 'dist/rtl-pro.css' ),
					[],
					Plugin::instance()->get_version()
				);
			}

			wp_enqueue_script(
				'zb-pro-theme-builder',
				Utils::get_file_url( 'dist/theme-builder.js' ),
				[ 'zb-vue' ],
				Plugin::instance()->get_version(),
				true
			);

			CommonJS::localize_common_js_data( 'zb-pro-theme-builder' );

			wp_localize_script(
				'zb-pro-theme-builder',
				'ZnPb_ConditionsData',
				[
					'conditions'            => Plugin::$instance->conditions->get_conditions_for_admin(),
					'categories'            => Plugin::$instance->conditions->get_categories(),
					'conditions_saved_data' => Plugin::$instance->conditions->get_conditions_saved_data(),
				]
			);

			wp_localize_script(
				'zb-pro-theme-builder',
				'ZionProRestConfig',
				[
					'nonce'     => wp_create_nonce( 'wp_rest' ),
					'rest_root' => esc_url_raw( rest_url() ),
				]
			);

			wp_localize_script(
				'zb-pro-theme-builder',
				'ZnPbSiteBuilderData',
				[
					'site_templates' => $this->get_template_configs(),
					'urls'           => [
						'logo_url' => Whitelabel::get_logo_url(),
					],
				]
			);
		}
	}

	public function add_menu_pages() {
		$hook                  = sprintf( '%s-theme-builder', WhiteLabel::get_id() );
		$this->admin_page_hook = add_submenu_page(
			WhiteLabel::get_id(),
			__( 'Theme builder', 'zionbuilder-pro' ),
			__( 'Theme builder', 'zionbuilder-pro' ),
			'manage_options',
			$hook,
			[ $this, 'render_submenu_page' ]
		);

	}

	public function render_submenu_page() {
		echo '<div id="znpbpro-theme-builder-app"></div>';
	}

	/**
	 * Check if injection needed
	 *
	 * Will check if any of the saved templates are set for the
	 * current request
	 *
	 * @param mixed $frontend
	 */
	public function on_zion_frontend_init( $frontend ) {
		$this->get_assigned_template();

		if ( $this->get_registered_template_ids() ) {
			foreach ( $this->get_registered_template_ids() as $template_area => $template_id ) {
				// validate the content
				$post_instance = FreePlugin::instance()->post_manager->get_post_instance( $template_id );

				if ( ! $post_instance || ! $post_instance->is_built_with_zion() ) {
					return false;
				}

				$post_template_data = $post_instance->get_template_data();
				FreePlugin::instance()->renderer->register_area( $template_id, $post_template_data );
			}

			// Check if we need to inject a template
			add_action( 'template_include', [ $this, 'on_template_include' ], 11 );
		}
	}

	public function get_template_override( $template_area ) {
		return isset( $this->registered_templates[$template_area] ) ? $this->registered_templates[$template_area] : false;
	}

	public function get_registered_template_ids() {
		return $this->registered_templates;
	}

	public function get_assigned_template() {
		// Get the template needed to render
		$config              = $this->get_template_configs();
		$default_template_id = $config['default_template'];
		$templates           = $config['templates'];

		// Set the default template first
		usort(
			$templates,
			function( $a, $b ) use ( $default_template_id ) {
				return $a['id'] === $default_template_id ? -1 : 1;
			}
		);

		$default_template = array_shift( $templates );
		$page_request     = new PageRequest();

		// Check for default template
		if ( $default_template && empty( $default_template['disabled'] ) ) {
			if ( isset( $default_template['template_config'] ) && is_array( $default_template['template_config'] ) ) {
				foreach ( $default_template['template_config'] as $area_id => $area_config ) {
					$this->extract_area( $area_id, $area_config );
				}
			}
		}

		// Check for overrides
		foreach ( $templates as $template_config ) {
			if ( isset( $template_config['disabled'] ) && $template_config['disabled'] === true ) {
				continue;
			}

			if ( ! isset( $template_config['conditions'] ) ) {
				continue;
			}

			$should_apply = Plugin::instance()->conditions->validate( $page_request, $template_config['conditions'] );

			if ( $should_apply ) {
				foreach ( $template_config['template_config'] as $area_id => $area_config ) {
					$this->extract_area( $area_id, $area_config );
				}
				return;
			}
		}
	}

	public function extract_area( $area_id, $area_config ) {
		if ( isset( $area_config['content'] ) && isset( $area_config['active'] ) && $area_config['active'] ) {
			$post_id                              = $area_config['content'];
			$this->registered_templates[$area_id] = apply_filters( 'zionbuilderpro/theme/template_post_id', $post_id, $area_id );
		}
	}

	public function on_template_include( $template ) {
		// Check to see if header is overwritten
		$header_override = $this->get_template_override( 'header' );
		$footer_override = $this->get_template_override( 'footer' );
		$body_override   = $this->get_template_override( 'body' );

		if ( $header_override || $footer_override ) {
			add_action( 'get_header', [ $this, 'override_header' ] );
			add_action( 'get_footer', [ $this, 'override_footer' ], 999 );
		}

		// Check to see if the body is overwritten. It will be disabled in preview mode
		if ( $body_override ) {
			return dirname( __FILE__ ) . '/views/body.php';
		}

		return $template;
	}

	public function override_template( $type, $name ) {
		// Add our override
		require dirname( __FILE__ ) . "/views/{$type}.php";

		/**
		 * Remove old template
		 *
		 * Internally, WordPress uses locate_template and then load_template
		 * to load the template partial. By default, this is set to require_once
		 * the template file. Using the code bellow, we import the old template inside
		 * ob_start so that only our template remains.
		 *
		 * @see https://developer.wordpress.org/reference/functions/get_header/
		 */
		$templates = [];
		$name      = (string) $name;
		if ( '' !== $name ) {
			$templates[] = "{$type}-{$name}.php";
		}

		// Fix compatibility that hook into wp_head action
		// This works because we already loaded our template
		if ( $type === 'header' ) {
			remove_all_actions( 'wp_head' );
		} elseif ( $type === 'footer' ) {
			remove_all_actions( 'wp_footer' );
		}

		$templates[] = "{$type}.php";

		ob_start();
		locate_template( $templates, true, true );
		ob_end_clean();
	}

	public function override_header( $template_name ) {
		$this->override_template( 'header', $template_name );
	}

	public function override_footer( $template_name ) {
		$this->override_template( 'footer', $template_name );
	}

	public function render_template( $type ) {
		$area_content_id = $this->get_template_override( $type );
		FreePlugin::instance()->renderer->render_area( $area_content_id );
	}

	public function register_conditions_for_data( $conditions_manager ) {
		$templates_config = $this->get_template_configs();
		$templates        = ! empty( $templates_config['templates'] ) ? $templates_config['templates'] : [];

		foreach ( $templates as $template ) {
			if ( isset( $template['conditions'] ) ) {
				$conditions_manager->register_conditions_data( $template['conditions'] );
			}
		}

	}

	public function set_dynamic_data_source( $dynamic_source_config ) {
		if ( isset( $_POST['template_conditions'] ) ) {
			$source = json_decode( stripslashes( $_POST['template_conditions'] ), true );

			if ( isset( $source['show_on'] ) && is_array( $source['show_on'] ) ) {
				foreach ( $source['show_on'] as $condition_id => $value ) {
					$condition_id_data = Conditions::get_condition_id_data( $condition_id );

					if ( $condition_id_data[0] === 'singular' ) {
						if ( is_array( $value ) && isset( $value[0] ) ) {
							$id = $value[0];
						} else {
							$id = $this->get_latest_post( $condition_id_data[1] );
						}

						// Proceed if we do not have an id
						if ( ! $id ) {
							continue;
						}

						return [
							'type'    => 'single',
							'subtype' => $condition_id_data[1],
							'id'      => $id,
						];

						break;
					} elseif ( $condition_id_data[0] === 'archive' ) {
						if ( isset( $condition_id_data[1] ) && post_type_exists( $condition_id_data[1] ) ) {
							$type = 'archive';
						} elseif ( isset( $condition_id_data[1] ) && taxonomy_exists( $condition_id_data[1] ) ) {
							$type = 'taxonomy_archive';

							if ( is_array( $value ) && isset( $value[0] ) ) {
								$id = $value[0];
							} else {
								$id = $this->get_latest_taxonomy_term( $condition_id_data[1] );
							}

							// Proceed if we do not have an id
							if ( empty( $id ) ) {
								continue;
							}
						}

						return [
							'type'    => $type,
							'subtype' => $condition_id_data[1],
							'id'      => ! empty( $id ) ? $id : 0,
						];

						break;
					}
				}
			}
		}

		return $dynamic_source_config;
	}

	public function get_latest_post( $post_type ) {
		$args = array(
			'post_type'      => $post_type,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'posts_per_page' => 1,
		);

		$query = new \WP_Query();
		$posts = $query->query( $args );

		if ( isset( $posts[0] ) ) {
			return $posts[0]->ID;
		}

		return false;
	}

	public function get_latest_taxonomy_term( $taxonomy ) {
		$terms = get_terms(
			[
				'post_status' => 'any',
				'taxonomy'    => $taxonomy,
			]
		);

		if ( isset( $terms[0] ) ) {
			return $terms[0]->ID;
		}

		return false;
	}

	/**
	 * Adds theme builder link to the admin bar Zion builder submenu
	 *
	 * @param \WP_Admin_Bar $admin_bar
	 *
	 * @return void
	 */
	public function add_wp_admin_bar_menu_items( $admin_bar ) {

		$translations = [
			'header' => esc_html__( 'Header', 'zionbuilder-pro' ),
			'body'   => esc_html__( 'Body', 'zionbuilder-pro' ),
			'footer' => esc_html__( 'Footer', 'zionbuilder-pro' ),
		];

		if ( ! empty( $this->registered_templates ) ) {
			foreach ( $this->registered_templates as $template_area => $post_id ) {
				$post_instance = FreePlugin::instance()->post_manager->get_post_instance( $post_id );

				if ( ! $post_instance ) {
					continue;
				}

				$title = get_the_title( $post_id );
				$admin_bar->add_menu(
					[
						'parent' => 'edit-with-zion',
						'id'     => "zion-theme-builder-area-{$post_id}",
						'title'  => "<div class='znpbpro-TBAdminBarItem'>{$title}<span class='znpbpro-TBAdminBarLabel'>{$translations[$template_area]}</span></div>",
						'href'   => $post_instance->get_edit_url(),
						'meta'   => [
							'class' => 'zion-theme-builder-area',
						],

					]
				);
			}
		}

		if ( count( $this->registered_templates ) > 0 ) {
			$admin_bar->add_menu(
				[
					'parent' => 'edit-with-zion',
					'id'     => 'zbpro-theme-area-separator',
					'title'  => '',
					'href'   => '#',
					'meta'   => [
						'class' => 'zion-theme-builderAreaSeparator',
					],

				]
			);
		}

		$admin_bar->add_menu(
			[
				'parent' => 'edit-with-zion',
				'id'     => 'zion-theme-builder',
				'title'  => esc_html__( 'Theme builder', 'zionbuilder-pro' ),
				'href'   => admin_url( sprintf( 'admin.php?page=%s-theme-builder', WhiteLabel::get_id() ) ),

			]
		);
	}

	public function add_admin_bar_styles() {

		if ( is_admin_bar_showing() ) { ?>


			<style type="text/css">
				#wpadminbar .znpbpro-TBAdminBarItem {
					display: flex;
					justify-content: space-between;
					line-height: 1;
					align-items: center;
					flex-grow: 1;
				}
				#wpadminbar .znpbpro-TBAdminBarLabel {
					padding: 3px 5px;
					background-color: red;
					color: #fff;
					margin-left: 5px;
					padding: 3px 5px;
					background-color: #7e7d7d;
					color: #fff;
					margin-left: 5px;
					font-size: 11px;
					line-height: 1;
					text-transform: uppercase;
				}

				#wpadminbar .zion-theme-builder-area .ab-item {
					display: flex;
				}

				#wpadminbar .zion-theme-builderAreaSeparator a {
					border-bottom: 1px solid #7e7d7d;
					height: auto !important;
					margin: 6px 0;
				}


			</style>

			<?php
		}
	}

}
