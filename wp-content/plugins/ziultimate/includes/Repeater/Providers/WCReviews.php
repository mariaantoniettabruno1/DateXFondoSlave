<?php
namespace ZiUltimate\Repeater\Providers;

use ZionBuilderPro\Repeater\RepeaterProvider;
use ZionBuilder\Options\Options;

class WCReviews extends RepeaterProvider {
	private $temp_comments = null;

	public static function get_id() {
		return 'wc_reviews';
	}

	public static function get_name() {
		return esc_html__( 'WooCommerce Reviews Builder', 'ziultimate' );
	}

	public function the_item( $index = null ) {
		$real_index = null === $index ? $this->get_real_index() : $index;
		$current_item = $this->get_item_by_index( $real_index );

		$this->temp_comments = isset( $GLOBALS['comment'] ) ? $GLOBALS['comment'] : '';
		if ( $current_item ) {
			$GLOBALS['comment'] = $current_item;
		}
	}

	public function perform_query() {
		$config = isset( $this->config['config'] ) ? $this->config['config'] : [];

		$args = array(
			'status' 	=> 'approve',
			'type' 		=> 'review',
			'offset' 	=> 2
		);

		$config['author__in'] = isset( $config['author__in'] ) ? explode( ',', $config['author__in'] ) : '';
		$config['author__not_in'] = isset( $config['author__not_in'] ) ? explode( ',', $config['author__not_in'] ) : '';
		$config['comment__in'] = isset( $config['comment__in'] ) ? explode( ',', $config['comment__in'] ) : '';
		$config['comment__not_in'] = isset( $config['comment__not_in'] ) ? explode( ',', $config['comment__not_in'] ) : '';

		if( isset( $config['is_single_prd'] ) && $config['is_single_prd'] === true ) {
			$config['post_id'] = get_the_ID();
		}

		// The Query
		$comments_query = new \WP_Comment_Query;
		$comments = $comments_query->query( array_merge( $args, $config ) );

		if( $comments ) {
			$this->query = [
				'query' => [],
				'items' => is_array( $comments ) ? $comments : [],
			];
		} else {
			$this->query = [
				'query' => null,
				'items' => [],
			];
		}
	}

	public function reset_item() {
		$GLOBALS['comment'] = $this->temp_comments;
		$this->temp_comments = null;
	}

	public function get_schema() {
		$options_schema = new Options( 'zionbuilderpro/repeater_provider/wc_reviews' );

		$options = [];
		$posts = get_posts([
			'post_type' => 'product',
			'numberposts' => -1,
		]);

		foreach ($posts as $post) {
			$options[] = array(
				'name' => $post->post_title,
				'id' => $post->ID,
			);
		}

		$options_schema->add_option(
			'preview_note',
			[
				'type' 			=> 'html',
				'title' 		=> esc_html__( 'Notes:', 'ziultimate' ),
				'content' 		=> '<p class="description">' . esc_html__( 'Refresh the builder editor, if preview is not working properly.', 'ziultimate' ) .'</p>'
			]
		);

		$options_schema->add_option(
			'is_single_prd',
			[
				'type' 			=> 'checkbox_switch',
				'title' 		=> esc_html__( 'Get reviews of current product', 'ziultimate' ),
				'description' 	=> esc_html__( 'Eanble it when you are using it inside the query builder repeater or single product page.', 'ziultimate' ),
				'layout' 		=> 'inline',
				'default' 		=> false
			]
		);

		$options_schema->add_option(
			'post_id',
			[
				'type' 			=> 'select',
				'title' 		=> esc_html__( 'Show reviews of selected product', 'ziultimate' ),
				'placeholder' 	=> esc_html__( 'Select product', 'ziultimate' ),
				'options' 		=> $options,
				'filterable' 	=> true,
				'dependency' 	=> [
					[
						'option' 	=> 'is_single_prd',
						'value' 	=> [ false ]
					]
				]
			]
		);

		$options_schema->add_option(
			'post__not_in',
			[
				'type' 			=> 'select',
				'title' 		=> esc_html__( 'Exclude products', 'ziultimate' ),
				'placeholder' 	=> esc_html__( 'Excluding selected products', 'ziultimate' ),
				'options' 		=> $options,
				'filterable' 	=> true,
				'multiple' 		=> true
			]
		);

		$options_schema->add_option(
			'number',
			[
				'type' 			=> 'text',
				'title' 		=> esc_html__( 'Number of comments to show', 'ziultimate' ),
				'description' 	=> esc_html__( 'Maximum number of comments to retrieve. Default empty (no limit).', 'ziultimate' ),
				'placeholder' 	=> esc_html__( 'Leave it blank to show all', 'ziultimate' ),
			]
		);

		/*$options_schema->add_option(
			'offset',
			[
				'type' 			=> 'text',
				'title' 		=> esc_html__('Offset', 'ziultimate' ),
				'description' 	=> esc_html__( 'Number of comments to offset the query. Default 0.', 'ziultimate' )
			]
		);*/

		$options_schema->add_option(
			'incl_excl_reviews',
			[
				'type' 			=> 'html',
				'title' 		=> esc_html__('Include/Exclude reviews:', 'ziultimate' ),
				'content' 		=> '<hr style="border:1px solid #e5e5e5;"/>'
			]
		);

		$options_schema->add_option(
			'comment__in',
			[
				'type' 			=> 'text',
				'title' 		=> esc_html__( 'Include reviews', 'ziultimate' ),
				'placeholder' 	=> esc_html__( 'Enter review IDs with comma separator', 'ziultimate' ),
			]
		);

		$options_schema->add_option(
			'comment__not_in',
			[
				'type' 			=> 'text',
				'title' 		=> esc_html__( 'Exclude reviews', 'ziultimate' ),
				'placeholder' 	=> esc_html__( 'Enter review IDs with comma separator', 'ziultimate' ),
			]
		);

		$options_schema->add_option(
			'incl_excl_auth',
			[
				'type' 			=> 'html',
				'title' 		=> esc_html__( 'Include/Exclude authors:', 'ziultimate' ),
				'content' 		=> '<hr style="border:1px solid #e5e5e5;"/>'
			]
		);

		$options_schema->add_option(
			'author__in',
			[
				'type' 			=> 'text',
				'title' 		=> esc_html__( 'Include authors', 'ziultimate' ),
				'placeholder' 	=> esc_html__( 'Enter author IDs with comma separator.', 'ziultimate' )
			]
		);

		$options_schema->add_option(
			'author__not_in',
			[
				'type' 			=> 'text',
				'title' 		=> esc_html__( 'Exclude authors', 'ziultimate' ),
				'placeholder' 	=> esc_html__( 'Enter author IDs with comma separator.', 'ziultimate' )
			]
		);

		$options_schema->add_option(
			'parent',
			[
				'type' 			=> 'text',
				'title' 		=> esc_html__( 'Parent Review ID', 'ziultimate' ),
				'description' 	=> esc_html__( 'Display child reviews.', 'ziultimate' )
			]
		);

		$options_schema->add_option(
			'orderby',
			[
				'type' 			=> 'select',
				'title' 		=> esc_html__( 'Orderby', 'ziultimate' ),
				'default' 		=> 'comment_date_gmt',
				'options' 		=>[
					[
						'id' 	=> 'comment_author',
						'name'	=> esc_html__( 'Author name', 'ziultimate' ),
					],
					[
						'id' 	=> 'comment_author_email',
						'name'	=> esc_html__( 'Author email', 'ziultimate' ),
					],
					[
						'id' 	=> 'comment_date_gmt',
						'name'	=> esc_html__('Date'),
					],
					[
						'id' 	=> 'comment_content',
						'name'	=> esc_html__( 'Review content', 'ziultimate' ),
					],
					[
						'id' 	=> 'comment_ID',
						'name'	=> esc_html__('ID')
					],
					[
						'id' 	=> 'comment__in',
						'name'	=> esc_html__( 'comment__in', 'ziultimate' )
					],
					[
						'id' 	=> 'meta_value',
						'name'	=> esc_html__( 'Meta value', 'ziultimate' )
					],
					[
						'id' 	=> 'meta_value_num',
						'name'	=> esc_html__( 'Meta value num', 'ziultimate' )
					]
				]
			]
		);

		$options_schema->add_option(
			'order',
			[
				'type' 			=> 'custom_selector',
				'title' 		=> esc_html__( 'Order', 'ziultimate' ),
				'default' 		=> 'desc',
				'options' 		=>[
					[
						'id' 	=> 'asc',
						'name'	=> esc_html__('ASC'),
					],
					[
						'id' 	=> 'desc',
						'name'	=> esc_html__('DESC'),
					]
				]
			]
		);

		return $options_schema->get_schema();
	}
}