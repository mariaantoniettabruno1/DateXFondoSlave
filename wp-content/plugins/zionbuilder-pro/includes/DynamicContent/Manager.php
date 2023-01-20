<?php

namespace ZionBuilderPro\DynamicContent;

// Fields
use ZionBuilderPro\DynamicContent\Fields\AuthorInfo;
use ZionBuilderPro\DynamicContent\Fields\AuthorMeta;
use ZionBuilderPro\DynamicContent\Fields\CommentsNumber;
use ZionBuilderPro\DynamicContent\Fields\CurrentUserInfo;
use ZionBuilderPro\DynamicContent\Fields\CurrentUserMeta;
use ZionBuilderPro\DynamicContent\Fields\FeaturedImage;
use ZionBuilderPro\DynamicContent\Fields\GlobalColor;
use ZionBuilderPro\DynamicContent\Fields\GlobalGradient;
use ZionBuilderPro\DynamicContent\Fields\PostContent;
use ZionBuilderPro\DynamicContent\Fields\PostCustomField;
use ZionBuilderPro\DynamicContent\Fields\PostExcerpt;
use ZionBuilderPro\DynamicContent\Fields\PostId;
use ZionBuilderPro\DynamicContent\Fields\PostTerms;
use ZionBuilderPro\DynamicContent\Fields\PostTitle;
use ZionBuilderPro\DynamicContent\Fields\PostDate;
use ZionBuilderPro\DynamicContent\Fields\Shortcode;
use ZionBuilderPro\DynamicContent\Fields\SiteEmailAddress;
use ZionBuilderPro\DynamicContent\Fields\SiteTagline;
use ZionBuilderPro\DynamicContent\Fields\SiteTimezone;
use ZionBuilderPro\DynamicContent\Fields\SiteTitle;
use ZionBuilderPro\DynamicContent\Fields\TaxonomyTitle;
use ZionBuilderPro\DynamicContent\Fields\TaxonomyDescription;
use ZionBuilderPro\DynamicContent\Fields\FunctionReturnValue;
use ZionBuilderPro\DynamicContent\Fields\RepeaterField;

// Image
use ZionBuilderPro\DynamicContent\Fields\MediaFeaturedImage;
use ZionBuilderPro\DynamicContent\Fields\MediaAuthorProfile;
use ZionBuilderPro\DynamicContent\Fields\MediaSiteLogo;

// Link
use ZionBuilderPro\DynamicContent\Fields\LinkPostLink;
use ZionBuilderPro\DynamicContent\Fields\LinkAuthorPage;
use ZionBuilderPro\DynamicContent\Fields\LinkHomePage;



// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class Manager
 *
 * @package ZionBuilderPro\DynamicContent
 */
class Manager {
	/**
	 * Holds a reference to all registered fields
	 */
	private $registered_fields = null;


	/**
	 * Holds a reference to all field types
	 */
	private $registered_field_groups = null;

	/**
	 * Main class constructor
	 *
	 * @return void
	 */
	public function __construct() {
		// Applies the dynamic data to data models
		add_filter( 'zionbuilder/options/model_parse', [ $this, 'apply_dynamic_content' ], 10 );

		// Register dynamic data API request
		add_filter( 'zionbuilder/api/bulk_actions', [ $this, 'add_dynamic_data_to_bulk_actions' ] );

		// Add dynamic data source option
		add_action( 'zionbuilder/schema/page_options', [ $this, 'attach_dynamic_data_source_options' ] );
		add_filter( 'zionbuilder/post/page_options_data', [ $this, 'add_data_to_page_options' ], 10, 2 );

		add_filter( 'zionbuilder/api/bulk_actions/get_input_select_options/get_post_custom_fields', [ __CLASS__, 'get_post_custom_fields' ], 10, 2 );
	}


	/**
	 * Register fields
	 *
	 * Will register dynamic content fields
	 */
	private function register_fields() {
		// Register default fields
		$this->register_default_fields();
		$this->register_field_groups();

		/*
		 * Allow others to register their own fields
		 */
		do_action( 'zionbuilderpro/dynamic_content_manager/register_fields', $this );
	}

	public function add_dynamic_data_to_bulk_actions( $actions ) {
		$actions['get_dynamic_data'] = [ $this, 'get_field_content' ];

		return $actions;
	}

	/**
	 * Returns the content for a dynamic field
	 *
	 * @param array{type: string, options: array<string, string>}> $field_config
	 *
	 * @return string
	 */
	public function get_field_content( $field_config ) {
		$field_instance  = $this->get_field_by_id( $field_config['type'] );
		$field_options   = isset( $field_config['options'] ) ? $field_config['options'] : [];
		$value_to_return = $field_instance ? $field_instance->get_content( $field_options ) : '';

		return $value_to_return;
	}

	public static function prepare_query( $dynamic_data_config ) {
		$type = 'post';
		$id   = null;

		// Check for repeater
		if ( ! empty( $dynamic_data_config['type'] ) ) {
			if ( $dynamic_data_config['type'] === 'single' ) {
				$type = 'post';
				$id   = isset( $dynamic_data_config['id'] ) ? $dynamic_data_config['id'] : null;

			} elseif ( $dynamic_data_config['type'] === 'archive' ) {
				$type = 'archive';
			} elseif ( $dynamic_data_config['type'] === 'taxonomy_archive' ) {
				$type = 'taxonomy_archive';
			}
		}

		if ( $type === 'post' && ! empty( $id ) ) {
			global $post;

			$post = get_post( $id, OBJECT );
			setup_postdata( $post );
		} elseif ( $type === 'archive' ) {
			query_posts(
				[
					'post_type' => isset( $dynamic_data_config['subtype'] ) ? $dynamic_data_config['subtype'] : null,
				]
			);
		} elseif ( $type === 'taxonomy_archive' ) {
			// Check for taxonomy
			if ( isset( $dynamic_data_config['subtype'] ) ) {
				$taxonomy = $dynamic_data_config['subtype'];
			} else {
				$taxonomies = get_taxonomies( [ 'public' => true ], 'objects' );

				if ( isset( $taxonomies[0] ) ) {
					$taxonomy = $taxonomies[0]->label;
				} else {
					return;
				}
			}

			$id = isset( $dynamic_data_config['id'] ) ? $dynamic_data_config['id'] : null;

			query_posts(
				[
					'tax_query' => [
						[
							'taxonomy' => $taxonomy,
							'field'    => 'term_id',
							'terms'    => [ $id ],
						],
					],
				]
			);
		}
	}

	public static function reset_query( $dynamic_data_config ) {
		if ( ! empty( $dynamic_data_config ) ) {
			wp_reset_query();
			wp_reset_postdata();
		}

	}

	/**
	 * Register field groups
	 *
	 * @hooked zionbuilderpro/dynamic_content_manager/register_field_groups
	 */
	private function register_field_groups() {
		$this->register_default_field_groups();

		/*
		 * Allow others to register their own fields groups
		 */
		do_action( 'zionbuilderpro/dynamic_content_manager/register_field_groups', $this );
	}

	/**
	 * Will register the built in groups
	 */
	private function register_default_field_groups() {
		$this->register_field_group(
			[
				'id'   => 'post',
				'name' => esc_html__( 'Post', 'zionbuilder-pro' ),
			]
		);

		$this->register_field_group(
			[
				'id'   => 'taxonomy',
				'name' => esc_html__( 'Archive', 'zionbuilder-pro' ),
			]
		);

		$this->register_field_group(
			[
				'id'   => 'site',
				'name' => esc_html__( 'Site', 'zionbuilder-pro' ),
			]
		);

		$this->register_field_group(
			[
				'id'   => 'author',
				'name' => esc_html__( 'Author', 'zionbuilder-pro' ),
			]
		);

		$this->register_field_group(
			[
				'id'   => 'user',
				'name' => esc_html__( 'User', 'zionbuilder-pro' ),
			]
		);

		$this->register_field_group(
			[
				'id'   => 'others',
				'name' => esc_html__( 'Others', 'zionbuilder-pro' ),
			]
		);
	}

	/**
	 * Register a field group
	 *
	 * @param array $field_group_config
	 *
	 * @return $this
	 */
	public function register_field_group( $field_group_config ) {
		$this->registered_field_groups[$field_group_config['id']] = $field_group_config;

		// Allow chaining
		return $this;
	}


	/**
	 * Unregister field group
	 *
	 * @param string $field_group_id The field group id that you want to unregister
	 *
	 * @return bool|\WP_Error
	 */
	public function unregister_field_group( $field_group_id ) {
		if ( ! isset( $this->registered_field_groups[$field_group_id] ) ) {
			/* translators: %s: The field group id */
			return new \WP_Error( 'field_not_found', sprintf( esc_html__( 'The %s field requested was not found.', 'zionbuilder-pro' ), $field_group_id ) );
		}

		unset( $this->registered_field_groups[$field_group_id] );
		return true;
	}


	/**
	 * Get groups
	 *
	 * Returns the list of registered groups
	 */
	public function get_groups() {
		return $this->registered_field_groups;
	}


	/**
	 * Register default fields
	 *
	 * Will register our default strings
	 */
	private function register_default_fields() {
		// Hidden
		$this->register_field( new GlobalColor() );
		$this->register_field( new GlobalGradient() );

		// Post
		$this->register_field( new PostTitle() );
		$this->register_field( new PostDate() );
		$this->register_field( new PostContent() );
		$this->register_field( new PostExcerpt() );
		$this->register_field( new PostId() );
		$this->register_field( new PostCustomField() );
		$this->register_field( new PostTerms() );
		$this->register_field( new CommentsNumber() );

		// Author
		$this->register_field( new AuthorInfo() );
		$this->register_field( new AuthorMeta() );

		// Site
		$this->register_field( new SiteTitle() );
		$this->register_field( new SiteTagline() );
		$this->register_field( new SiteEmailAddress() );
		$this->register_field( new SiteTimezone() );

		// Current user
		$this->register_field( new CurrentUserMeta() );
		$this->register_field( new CurrentUserInfo() );

		// Taxonomy
		$this->register_field( new TaxonomyTitle() );
		$this->register_field( new TaxonomyDescription() );

		// Misc
		$this->register_field( new Shortcode() );

		// Media
		$this->register_field( new FeaturedImage() );

		// Media
		$this->register_field( new MediaFeaturedImage() );
		$this->register_field( new MediaAuthorProfile() );
		$this->register_field( new MediaSiteLogo() );

		// Link
		$this->register_field( new LinkPostLink() );
		$this->register_field( new LinkAuthorPage() );
		$this->register_field( new LinkHomePage() );
		$this->register_field( new FunctionReturnValue() );

		// Repeater field
		$this->register_field( new RepeaterField() );
	}

	/**
	 * Register field
	 *
	 * Will register a dynamic content field
	 *
	 * @param BaseField $field_class
	 *
	 * @throws \Exception
	 *
	 * @return $this
	 */
	public function register_field( BaseField $field_class ) {
		if ( $field_class->can_load() ) {
			$this->registered_fields[$field_class->get_id()] = $field_class;
		}

		// Allow chaining
		return $this;
	}

	/**
	 * Unregister field
	 *
	 * Will unregister a dynamic field
	 *
	 * @param string $field_id
	 *
	 * @return bool
	 */
	public function unregister_field( $field_id ) {
		if ( isset( $this->registered_fields[$field_id] ) ) {
			unset( $this->registered_fields[$field_id] );
			return true;
		}
		return false;
	}


	/**
	 * Get registered fields
	 *
	 * Returns the list of registered fields
	 * If the method was not called before, it will also register the fields
	 *
	 * @return array
	 */
	public function get_registered_fields() {
		if ( null === $this->registered_fields ) {
			$this->register_fields();
		}

		return $this->registered_fields;
	}

	/**
	 * Get fields for editor
	 *
	 * Will return a list of all fields to be used in editor mode
	 *
	 * @return array
	 */
	public function get_fields_for_editor() {
		// This needs to be first so we can register categories directly from field registration
		$fields      = $this->get_registered_fields();
		$groups      = $this->get_groups();
		$fields_data = [];

		foreach ( $fields as $field_class ) {
			$fields_data[] = [
				'category' => $field_class->get_category(),
				'name'     => $field_class->get_name(),
				'group'    => $field_class->get_group(),
				'id'       => $field_class->get_id(),
				'options'  => $field_class->get_options(),
			];
		}

		return [
			'fields'       => $fields_data,
			'field_groups' => $groups,
		];
	}

	/**
	 * Get fields data
	 *
	 * Will return the data for all fields that don't require a server call to get the value
	 *
	 * @return array
	 */
	public function get_fields_data() {
		$fields = $this->get_registered_fields();
		$data   = [];

		foreach ( $fields as $field_id => $field_class ) {
			$field_data = $field_class->get_data();
			if ( $field_data ) {
				$data[$field_id] = $field_data;
			}
		}

		return $data;
	}


	/**
	 * Get field by id
	 *
	 * Will return the field class by specifying the field id
	 *
	 * @param string $field_id
	 *
	 * @return BaseField
	 */
	public function get_field_by_id( $field_id ) {
		$fields = $this->get_registered_fields();
		return $fields[$field_id];
	}


	public static function get_post_custom_fields( $items, $config ) {
		$custom_field_keys = get_post_custom_keys();
		$options           = [];

		if ( ! empty( $custom_field_keys ) ) {
			foreach ( $custom_field_keys as $custom_field_key ) {
				// Skip private fields
				if ( '_' === substr( $custom_field_key, 0, 1 ) ) {
					continue;
				}

				$options[] = [
					// Nicely format the name
					'name' => $custom_field_key,
					'id'   => $custom_field_key,
				];
			}
		}

		return $options;
	}

	public function attach_dynamic_data_source_options( $options_manager ) {
		$group = $options_manager->add_group(
			'dynamic_data_source',
			[
				'title' => esc_html__( 'Dynamic data preview source', 'zionbuilder-pro' ),
				'type'  => 'accordion_menu',
			]
		);

		$group->add_option(
			'dynamic_data_source',
			[
				'type' => 'wp_page_selector',
			]
		);
	}


	/**
	 * Will attach the dynamic data source option to the builder json data
	 *
	 * @param array $page_options_data The builder page content
	 * @param int $post_id The current post id
	 *
	 * @return void
	 */
	public function add_data_to_page_options( $page_options_data, $post_id ) {
		$meta_value = apply_filters( 'zionbuilderpro/dynamic_data/source_values', null );
		if ( ! empty( $meta_value ) ) {
			$page_options_data['dynamic_data_source'] = $meta_value;
		}

		return $page_options_data;
	}


	/**
	 * Parses a data model and checks if we need to apply dynamic content
	 *
	 * @param array $model
	 *
	 * @return array
	 */
	public function apply_dynamic_content( $model ) {
		if ( ! empty( $model ) && is_array( $model ) ) {
			foreach ( $model as $key => &$value ) {
				if ( $key === '__dynamic_content__' && is_array( $value ) ) {
					foreach ( $value as $option_id => $dynamic_content_data ) {
						if ( empty( $dynamic_content_data['type'] ) ) {
							continue;
						}
						$field_instance = $this->get_field_by_id( $dynamic_content_data['type'] );
						$content        = '';

						$field_options = isset( $dynamic_content_data['options'] ) ? $dynamic_content_data['options'] : [];

						$fallback = isset( $field_options['_fallback'] ) ? $field_options['_fallback'] : '';
						$before   = isset( $field_options['_before'] ) ? $field_options['_before'] : '';
						$after    = isset( $field_options['_after'] ) ? $field_options['_after'] : '';

						// If the field is not registered anymore, show an empty text or fallback
						if ( $field_instance ) {
							$content = $field_instance->get_content( $field_options );
						}

						$content = ! empty( $content ) ? $content : $fallback;

						if ( is_array( $content ) ) {
							$content = $this->apply_dynamic_content( $content );
						}

						if ( is_array( $content ) ) {
							$model[$option_id] = $content;
						} else {
							$content_for_render = sprintf( '%s%s%s', $before, $content, $after );
							$model[$option_id]  = $content_for_render;
						}
					}
				} else {
					$model[$key] = $this->apply_dynamic_content( $value );
				}
			}
		}

		return $model;
	}
}
