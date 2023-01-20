<?php

namespace ZionBuilderPro\Elements\SocialShare;

use ZionBuilder\Elements\Element;
use ZionBuilderPro\Utils;
use ZionBuilderPro\Plugin;

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Class SocialShare
 *
 * @package ZionBuilderPro\Elements
 */
class SocialShare extends Element {
	/**
	 * Get type
	 *
	 * Returns the unique id for the element
	 *
	 * @return string The element id/type
	 */
	public function get_type() {
		return 'social_share';
	}
	/**
	 * Get name
	 *
	 * Returns the name for the element
	 *
	 * @return string The element name
	 */
	public function get_name() {
		return __( 'Social Share Buttons', 'zionbuilder-pro' );
	}

	/**
	 * Get keywords
	 *
	 * Returns the keywords for this element
	 *
	 * @return array The list of element keywords
	 */
	public function get_keywords() {
		return [ 'social', 'share', 'btn' ];
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
	 * Get Element Icon
	 *
	 * Returns the icon used in add elements panel for this element
	 *
	 * @return string The element icon
	 */
	public function get_element_icon() {
		return 'element-social-share';
	}

	public function options( $options ) {
		$share_icon_group = $options->add_option(
			'share_icon_group',
			[
				'type'               => 'repeater',
				'add_button_text'    => __( 'Add new share icon', 'zionbuilder-pro' ),
				'item_title'         => 'share icon',
				'default_item_title' => 'Share icon %s',
				'default'            => [
					[
						'icon_label' => 'Share on facebook',
						'icon'       => [
							'family'  => 'Font Awesome 5 Brands Regular',
							'name'    => 'facebook-f',
							'unicode' => 'uf39e',
						],
					],
					[
						'icon_label' => 'Tweet about it',
						'icon'       => [
							'family'  => 'Font Awesome 5 Brands Regular',
							'name'    => 'twitter',
							'unicode' => 'uf099',
						],
					],
					[

						'icon' => [
							'family'  => 'Font Awesome 5 Brands Regular',
							'name'    => 'linkedin-in',
							'unicode' => 'uf0e1',
						],
					],
					[

						'icon' => [
							'family'  => 'Font Awesome 5 Brands Regular',
							'name'    => 'pinterest-p',
							'unicode' => 'uf231',
						],
					],
				],
			]
		);

		$share_icon_group->add_option(
			'icon',
			[
				'type'              => 'icon_library',
				'title'             => __( 'Choose an icon', 'zionbuilder-pro' ),
				'specialFilterPack' => [
					[
						'built_in' => true,
						'id'       => 'FontAwesome5Brands-Regular',
						'name'     => 'Font Awesome 5 Brands Regular',
						'icons'    => [
							[
								'name'    => 'facebook-f',
								'unicode' => 'uf39e',
							],
							[
								'name'    => 'twitter',
								'unicode' => 'uf099',
							],
							[
								'name'    => 'linkedin-in',
								'unicode' => 'uf0e1',
							],
							[
								'name'    => 'pinterest-p',
								'unicode' => 'uf231',
							],
							[
								'name'    => 'whatsapp',
								'unicode' => 'uf232',
							],
							[
								'name'    => 'reddit',
								'unicode' => 'uf1a1',
							],
							[
								'name'    => 'tumblr',
								'unicode' => 'uf173',
							],
							[
								'name'    => 'digg',
								'unicode' => 'uf1a6',
							],
							[
								'name'    => 'get-pocket',
								'unicode' => 'uf265',
							],
							[
								'name'    => 'vk',
								'unicode' => 'uf189',
							],
							[
								'name'    => 'odnoklassniki',
								'unicode' => 'uf263',
							],
							[
								'name'    => 'stumbleupon',
								'unicode' => 'uf1a4',
							],
							[
								'name'    => 'xing',
								'unicode' => 'uf168',
							],
							[
								'name'    => 'telegram-plane',
								'unicode' => 'uf3fe',
							],
							[
								'name'    => 'skype',
								'unicode' => 'uf17e',
							],

						],

					],
				],
			]
		);
		$share_icon_group->add_option(
			'icon_label',
			[
				'type'        => 'text',
				'title'       => __( 'Icon label', 'zionbuilder-pro' ),
				'description' => esc_html__( 'Type text to inside the share button', 'zionbuilder-pro' ),
			]
		);

		// $options->add_option(
		// 	'show_count',
		// 	[
		// 		'type'             => 'checkbox_switch',
		// 		'default'          => false,
		// 		'layout'           => 'inline',
		// 		'title'            => esc_html__( 'Show counter likes', 'zionbuilder-pro' ),
		// 		'description'      => esc_html__( 'Check to show the counter inside the button', 'zionbuilder-pro' ),

		// 	]
		// );

		$options->add_option(
			'social_type',
			[
				'type'             => 'select',
				'default'          => 'minimal',
				'title'            => esc_html__( 'Layout type', 'zionbuilder-pro' ),
				'description'      => esc_html__( 'Choose how you want the buttons to be displayed', 'zionbuilder-pro' ),
				'options'          => [
					[
						'name' => esc_html__( 'Minimal', 'zionbuilder-pro' ),
						'id'   => 'minimal',
					],
					[
						'name' => esc_html__( 'Colored', 'zionbuilder-pro' ),
						'id'   => 'colored',
					],

				],
				'render_attribute' => [
					[
						'attribute' => 'class',
						'value'     => 'zb-el-socialShare--{{VALUE}}',
					],
				],
			]
		);

		$options->add_option(
			'rounded',
			[
				'type'             => 'checkbox_switch',
				'default'          => false,
				'layout'           => 'inline',
				'title'            => esc_html__( 'Rounded', 'zionbuilder-pro' ),
				'description'      => esc_html__( 'Check to make rounded buttons', 'zionbuilder-pro' ),
				'render_attribute' => [
					[
						'attribute' => 'class',
						'value'     => 'zb-el-socialShare--rounded',
					],
				],
			]
		);

		$options->add_option(
			'stretched',
			[
				'type'             => 'checkbox_switch',
				'default'          => false,
				'layout'           => 'inline',
				'title'            => esc_html__( 'Stretched', 'zionbuilder-pro' ),
				'description'      => esc_html__( 'Check to make buttons stretched', 'zionbuilder-pro' ),
				'render_attribute' => [
					[
						'attribute' => 'class',
						'value'     => 'zb-el-socialShare--stretched',
					],
				],
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
		$this->enqueue_editor_script( Utils::get_file_url( 'dist/elements/SocialShare/editor.js' ) );
		wp_enqueue_script( 'zb-element-social-share', Utils::get_file_url( 'dist/elements/SocialShare/frontend.js' ), [], Plugin::instance()->get_version(), true );
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
		$this->enqueue_element_style( Utils::get_file_url( 'dist/elements/SocialShare/frontend.css' ) );
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
		$icons = $options->get_value( 'share_icon_group', [] );

		foreach ( $icons as $config ) {
			$this->render_single_share_icon( $config, $options );
		}
	}
	public function render_single_share_icon( $config, $options ) {
		$has_count = $options->get_value( 'show_count' );
		// Get current page URL
		$post_url = urlencode( get_permalink() );
		// Get current page title
		$post_title = str_replace( ' ', '%20', get_the_title() );
		// Get current page thumb
		$post_thumb = get_the_post_thumbnail_url();

		$twitter_url     = 'https://twitter.com/intent/tweet?text=' . $post_title . '&amp;url=' . $post_url . '&amp;via=wpvkp';
		$facebook_url    = 'https://www.facebook.com/sharer/sharer.php?u=' . $post_url;
		$linkedin_url    = 'https://www.linkedin.com/shareArticle?mini=true&url=' . $post_url . '&amp;title=' . $post_title;
		$pinterest_url   = 'https://pinterest.com/pin/create/button/?url=' . $post_url . '&amp;media=' . $post_thumb[0] . '&amp;description=' . $post_title;
		$whatsup_url     = 'https://api.whatsapp.com/send?text=*' . $post_url;
		$reddit_url      = 'https://www.reddit.com/submit?url=' . $post_url;
		$tumblr_url      = 'https://www.tumblr.com/share/link?url=' . $post_url;
		$digg_url        = 'https://digg.com/submit?url=' . $post_url;
		$pocket_url      = 'https://getpocket.com/edit?url=' . $post_url;
		$vk_url          = 'https://vkontakte.ru/share.php?url=' . $post_url . '&title=' . $post_title . '&source=' . $post_url;
		$ok_url          = 'https://connect.ok.ru/offer?url=' . $post_url . '&title=' . $post_title . '&imageUrl=' . $post_thumb[0];
		$stumbleupon_url = 'https://www.stumbleupon.com/submit?url=' . $post_url;
		$xing_url        = 'https://www.xing.com/app/user?op=share&url=' . $post_url;
		$telegram_url    = 'https://telegram.me/share/url?url=' . $post_url;
		$skype_url       = 'https://web.skype.com/share?url=' . $post_url;

		$rendered_url = '';

		switch ( $config['icon']['name'] ) {
			case 'facebook-f':
				$rendered_url .= $facebook_url;
				break;

			case 'twitter':
				$rendered_url .= $twitter_url;
				break;

			case 'pinterest-p':
				$rendered_url .= $pinterest_url;
				break;

			case 'linkedin-in':
				$rendered_url .= $linkedin_url;
				break;

			case 'whatsapp':
				$rendered_url .= $whatsup_url;
				break;

			case 'reddit':
				$rendered_url .= $reddit_url;
				break;

			case 'tumblr':
				$rendered_url .= $tumblr_url;
				break;

			case 'digg':
				$rendered_url .= $digg_url;
				break;

			case 'get-pocket':
				$rendered_url .= $pocket_url;
				break;

			case 'vk':
				$rendered_url .= $vk_url;
				break;

			case 'odnoklassniki':
				$rendered_url .= $ok_url;
				break;

			case 'stumbleupon':
				$rendered_url .= $stumbleupon_url;
				break;

			case 'xing':
				$rendered_url .= $xing_url;
				break;

			case 'telegram-plane':
				$rendered_url .= $telegram_url;
				break;

			case 'skype':
				$rendered_url .= $skype_url;
				break;
		}
		if ( ! empty( $config['icon'] ) ) {
			$class = sprintf( 'zb-el-socialShare__item--is-%s', $config['icon']['name'] ); ?>
				<a class="zb-el-socialShare__item <?php echo $class; ?>" href="<?php echo $rendered_url; ?>" title="<?php echo esc_html__( 'Share on', 'zionbuilder-pro' ) . ' ' . $config['icon']['name']; ?>">
			<?php
			$this->attach_icon_attributes( 'icon', $config['icon'] );
			$this->render_tag( 'span', 'icon', '', [ 'class' => 'zb-el-socialShare__icon' ] );
			?>
			<?php if ( ! empty( $config['icon_label'] ) ) { ?>
				<span class="zb-el-socialShare__label"><?php echo esc_html( $config['icon_label'] ); ?></span>

			<?php } ?>
			<?php if ( ! empty( $has_count ) ) { ?>
			<span class="zb-el-socialShare__count">236</span>
				<?php
			}
		}
		?>
			</a>
		<?php
	}

	public function on_register_styles() {
		$this->register_style_options_element(
			'social_block',
			[
				'title'    => esc_html__( 'Social block', 'zionbuilder-pro' ),
				'selector' => '{{ELEMENT}} .zb-el-socialShare__item',
			]
		);
	}
}
