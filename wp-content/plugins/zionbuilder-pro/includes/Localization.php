<?php

namespace ZionBuilderPro;

use ZionBuilder\Whitelabel as FreeBuilderWhiteLabel;
class Localization {

	public function __construct() {
		add_filter( 'zionbuilder/localization/strings', [ $this, 'add_localization_strings' ] );
	}

	public function add_localization_strings( $strings ) {
		$pro_strings = [
			// Pro License input
			'pro_info_desc'                 => sprintf( _x( 'Add the license key you received when purchasing %s PRO', 'zionbuilder-pro' ), FreeBuilderWhiteLabel::get_title() ),
			'pro_info'                      => esc_html__( 'Add PRO license', 'zionbuilder-pro' ),
			'pro_license_key'               => esc_html__( 'PRO license key', 'zionbuilder-pro' ),
			'key_example'                   => esc_html__( '11x1x111111x1x111111x11x11xx1xx1', 'zionbuilder-pro' ),
			'key'                           => esc_html__( 'Key', 'zionbuilder-pro' ),
			'valid_until'                   => esc_html__( 'Valid until', 'zionbuilder-pro' ),
			'delete_key'                    => esc_html__( 'Delete key', 'zionbuilder-pro' ),
			'no_license_input'              => esc_html__( 'You did not add any license key', 'zionbuilder-pro' ),
			'add_license'                   => esc_html__( 'Add license key', 'zionbuilder-pro' ),
			'white-label'                   => esc_html__( 'White label', 'zionbuilder-pro' ),
			'white_label_info'              => __( 'Welcome to white label hidden options! Input added will replace the content everywhere it is used in the pagebuilder', 'zionbuilder-pro' ),
			// Typekit
			'typekit_api_key'               => esc_html__( 'API token', 'zionbuilder-pro' ),
			'paste_typekit_token'           => __( 'Paste the Typekit Token in this field', 'zionbuilder-pro' ),
			'no_typekit_fonts'              => esc_html__( 'No web projects added', 'zionbuilder-pro' ),
			'setup_typekit_fonts'           => __( 'Here you can setup the Typekit fonts that you want to use in your site.', 'zionbuilder-pro' ),
			'refresh_lists'                 => esc_html__( 'Refresh lists', 'zionbuilder-pro' ),
			// TypekitTab
			'active_typekit_deactivate'     => __( 'This is your active Typekit font. Uncheck to deactivate it', 'zionbuilder-pro' ),

			// CustomFont
			'click_to_delete_font'          => __( 'Click to delete this font', 'zionbuilder-pro' ),
			'click_to_edit_font'            => __( 'Click to edit this font', 'zionbuilder-pro' ),
			'no_custom_fonts'               => esc_html__( 'No Custom fonts added', 'zionbuilder-pro' ),
			'upload_custom_fonts'           => __( 'Upload your own Custom fonts (.woff, .ttf, .svg, .eot ) to use in the page builder', 'zionbuilder-pro' ),

			// Icon manager
			'add_icons'                     => esc_html__( 'Add icons', 'zionbuilder-pro' ),
			'icons_info'                    => esc_html__( 'Upload your own icon pack of icons - the uploaded zip file should contain font files( .woff, .eot, .ttf, .svg extensions)', 'zionbuilder-pro' ),
			'click_me_to_add_icons'         => esc_html__( 'Click to upload a new icon pack', 'zionbuilder-pro' ),
			'click_to_preview_icon'         => esc_html__( 'Preview icon package', 'zionbuilder-pro' ),
			'click_to_download_icon'        => esc_html__( 'Download icon package', 'zionbuilder-pro' ),
			'click_to_delete_icon'          => esc_html__( 'Delete icon package', 'zionbuilder-pro' ),
			'icon_delete_confirm'           => esc_html__( 'Delete Pack', 'zionbuilder-pro' ),
			'icon_delete_cancel'            => esc_html__( 'Cancel', 'zionbuilder-pro' ),
			'are_you_sure_icons_delete'     => esc_html__( 'Are you sure you want to delete this icon set?', 'zionbuilder-pro' ),

			// Preset container
			'type_preset'                   => __( 'Type a preset', 'zionbuilder-pro' ),

			// Theme builder
			'disabled'                      => __( 'Disabled', 'zionbuilder-pro' ),
			'disable'                       => __( 'Disable', 'zionbuilder-pro' ),
			'enable'                        => __( 'Enable', 'zionbuilder-pro' ),
			'remove'                        => __( 'Remove', 'zionbuilder-pro' ),
			'edit'                          => __( 'Edit', 'zionbuilder-pro' ),
			'copy_component'                => __( 'Copy component', 'zionbuilder-pro' ),
			'rename'                        => __( 'Rename', 'zionbuilder-pro' ),
			'template_assignments'          => __( 'TemplateFondo assignments', 'zionbuilder-pro' ),
			'use_on'                        => __( 'Use on', 'zionbuilder-pro' ),
			'exclude_from'                  => __( 'Exclude from', 'zionbuilder-pro' ),
			'posts'                         => __( 'Posts', 'zionbuilder-pro' ),
			'archive_pages'                 => __( 'Archive Pages', 'zionbuilder-pro' ),
			'add_new_component'             => esc_html__( 'Add new component', 'zionbuilder-pro' ),
			'add_new_template'              => esc_html__( 'Add new template', 'zionbuilder-pro' ),
			'template_not_found'            => esc_html__( 'Area not found', 'zionbuilder-pro' ),
			'paste_component'               => esc_html__( 'Paste component', 'zionbuilder-pro' ),
			'paste_as_new_component'        => esc_html__( 'Paste as new', 'zionbuilder-pro' ),
			'theme_builder'                 => esc_html__( 'Theme Builder', 'zionbuilder-pro' ),
			'save'                          => esc_html__( 'Save', 'zionbuilder-pro' ),
			'templates'                     => esc_html__( 'Templates', 'zionbuilder-pro' ),
			'components'                    => esc_html__( 'Components', 'zionbuilder-pro' ),
			'duplicate'                     => esc_html__( 'Duplicate', 'zionbuilder-pro' ),
			'edit'                          => esc_html__( 'Edit', 'zionbuilder-pro' ),
			'header'                        => esc_html__( 'Header', 'zionbuilder-pro' ),
			'body'                          => esc_html__( 'Body', 'zionbuilder-pro' ),
			'footer'                        => esc_html__( 'Footer', 'zionbuilder-pro' ),
			'copy'                          => esc_html__( 'copy', 'zionbuilder-pro' ),
			'delete'                        => esc_html__( 'Delete', 'zionbuilder-pro' ),
			'default'                       => esc_html__( 'default', 'zionbuilder-pro' ),
			'template'                      => esc_html__( 'TemplateFondo', 'zionbuilder-pro' ),
			'set_as_default'                => esc_html__( 'Set as default', 'zionbuilder-pro' ),
			'no_components_found'           => esc_html__( 'No components found', 'zionbuilder-pro' ),
			'preview_component'             => esc_html__( 'Preview component', 'zionbuilder-pro' ),
			'are_you_sure_delete_component' => esc_html__( 'Are you sure you want to delete this component? This is will delete the component and it will be remove from all assignments. This cannot be undone', 'zionbuilder-pro' ),
			'no_items_found'                => esc_html__( 'No items found', 'zionbuilder-pro' ),
			'no_more_items'                 => esc_html__( 'No more items', 'zionbuilder-pro' ),
			'modal_title_description'       => esc_html__( 'Add a title for the template', 'zionbuilder-pro' ),
			'modal_title_placeholder'       => esc_html__( 'Add a template title', 'zionbuilder-pro' ),
			// Option: WPPageSelector
			'search'                        => esc_html__( 'Search', 'zionbuilder-pro' ),
			'component_delete_confirm'      => esc_html__( 'Yes, delete component', 'zionbuilder-pro' ),
			'component_delete_cancel'       => esc_html__( 'No, keep component', 'zionbuilder-pro' ),
			'editing_component'             => esc_html__( 'Editing component', 'zionbuilder-pro' ),
			'close_component'               => esc_html__( 'Close component', 'zionbuilder-pro' ),

			// attributes option
			'attribute_name'                => esc_html__( 'Attribute name', 'zionbuilder-pro' ),
			'attribute_value'               => esc_html__( 'Attribute value', 'zionbuilder-pro' ),
			'attribute_add_new'             => esc_html__( 'Add new attribute', 'zionbuilder-pro' ),
			'attributes'                    => esc_html__( 'Attributes', 'zionbuilder-pro' ),
			'custom_attributes'             => esc_html__( 'custom attributes', 'zionbuilder-pro' ),

			// dynamic content
			'before'                        => esc_html__( 'Before', 'zionbuilder-pro' ),
			'after'                         => esc_html__( 'After', 'zionbuilder-pro' ),
			'fallback'                      => esc_html__( 'Fallback', 'zionbuilder-pro' ),
			'enable_raw'                    => esc_html__( 'Enable Raw HTML', 'zionbuilder-pro' ),
			'current_page_query'            => esc_html__( 'Current page query', 'zionbuilder-pro' ),
			'source_type'                   => esc_html__( 'Source type', 'zionbuilder-pro' ),
			'select_post_type'              => esc_html__( 'Select post type', 'zionbuilder-pro' ),
			'select_taxonomy'               => esc_html__( 'Select taxonomy', 'zionbuilder-pro' ),
			'select_post'                   => esc_html__( 'Select post', 'zionbuilder-pro' ),
			'use_dynamic_data'              => esc_html__( 'Use dynamic data', 'zionbuilder-pro' ),
			'delete_dynamic_field'          => esc_html__( 'Delete dynamic field', 'zionbuilder-pro' ),
			'edit_field_options'            => esc_html__( 'Edit field options', 'zionbuilder-pro' ),
			'field_not_available'           => esc_html__( 'Field not available', 'zionbuilder-pro' ),

			// Mega menu
			'edit_with_zion_builder'        => esc_html__( 'Edit with Zion Builder', 'zionbuilder-pro' ),
			'mega_menu_options'             => esc_html__( 'Mega menu options', 'zionbuilder-pro' ),
			'save_changes'                  => esc_html__( 'Save changes', 'zionbuilder-pro' ),
			'editing_menu_item'             => esc_html__( 'Editing menu item', 'zionbuilder-pro' ),
			'close_menu_item'               => esc_html__( 'Close menu item', 'zionbuilder-pro' ),
			'description'                   => esc_html__( 'Description', 'zionbuilder-pro' ),

			// ACF
			'repeated_field'                => esc_html__( 'ACF repeater Field', 'zionbuilder-pro' ),

			// Element conditions
			'set_advanced_conditions'       => esc_html__( 'Set advanced conditions', 'zionbuilder-pro' ),
			'advanced_display_conditions'   => esc_html__( 'Advanced display conditions', 'zionbuilder-pro' ),
			'save_conditions'               => esc_html__( 'Save conditions', 'zionbuilder-pro' ),
			'close'                         => esc_html__( 'Close', 'zionbuilder-pro' ),
			'add_conditions_group'          => esc_html__( 'Add conditions group', 'zionbuilder-pro' ),
			'add_condition'                 => esc_html__( 'Add condition', 'zionbuilder-pro' ),
			'delete_group'                  => esc_html__( 'Delete group', 'zionbuilder-pro' ),
			'select'                        => esc_html__( 'Select', 'zionbuilder-pro' ),
			'or'                            => esc_html__( 'Or', 'zionbuilder-pro' ),

		];

		return array_merge( $strings, $pro_strings );
	}
}
