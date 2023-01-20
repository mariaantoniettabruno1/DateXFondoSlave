<?php
	$dyndata->add_option(
		'post_ids',
		[
			'type' 			=> 'text',
			'title' 		=> esc_html__('Enter Post IDs', 'ziultimate'),
			'description'	=> esc_html__('Enter IDs with comma.', 'ziultimate'),
			'dependency'	=> [
				[
					'option' 	=> 'source',
					'value' 	=> [ 'postsbyids' ]
				]
			],
			'dynamic' 		=> [
				'enabled' => true
			]
		]
	);

	$dyndata->add_option(
		'tab_title_source',
		[
			'type' 			=> 'custom_selector',
			'title' 		=> esc_html__('Retrieve Tab Title From', 'ziultimate'),
			'default' 		=> 'post_title',
			'options'		=> [
				[
					'name' 	=> esc_html__('Post Title', 'ziultimate'),
					'id' 	=> 'post_title'
				],
				[
					'name' 	=> esc_html__('Custom Field', 'ziultimate'),
					'id' 	=> 'ttl_cust_field'
				]
			],
			'dependency'	=> [
				[
					'option' 	=> 'source',
					'value' 	=> [ 'postsbyids' ]
				]
			]
		]
	);

	$dyndata->add_option(
		'title_metakey',
		[
			'type' 			=> 'text',
			'title' 		=> esc_html__('Custom Field Name', 'ziultimate'),
			'description'	=> esc_html__('Enter meta key name.', 'ziultimate'),
			'dependency'	=> [
				[
					'option' 	=> 'tab_title_source',
					'value' 	=> [ 'ttl_cust_field' ]
				]
			]
		]
	);

	$dyndata->add_option(
		'tab_content_source',
		[
			'type' 			=> 'select',
			'title' 		=> esc_html__('Retrieve Tab Content From', 'ziultimate'),
			'default' 		=> 'post_excerpt',
			'options'		=> [
				[
					'name' 	=> esc_html__('Post Excerpt', 'ziultimate'),
					'id' 	=> 'post_excerpt'
				],
				[
					'name' 	=> esc_html__('Post Content', 'ziultimate'),
					'id' 	=> 'post_content'
				],
				[
					'name' 	=> esc_html__('Zion TemplateFondo with Repeater', 'ziultimate'),
					'id' 	=> 'zion_template'
				],
				[
					'name' 	=> esc_html__('Custom Field', 'ziultimate'),
					'id' 	=> 'cnt_cust_field'
				]
			],
			'dependency'	=> [
				[
					'option' 	=> 'source',
					'value' 	=> [ 'postsbyids' ]
				]
			]
		]
	);

	$dyndata->add_option(
		'cnt_metakey',
		[
			'type' 			=> 'text',
			'title' 		=> esc_html__('Custom Field Name', 'ziultimate'),
			'description'	=> esc_html__('Enter meta key name.', 'ziultimate'),
			'dependency'	=> [
				[
					'option' 	=> 'tab_content_source',
					'value' 	=> [ 'cnt_cust_field' ]
				]
			]
		]
	);

	$dyndata->add_option(
		'zion_template_id',
		[
			'type' 			=> 'text',
			'title' 		=> __('TemplateFondo ID', 'ziultimate'),
			'description'	=> __('It is a mandatory option. You should enter the correct template ID.', 'ziultimate'),
			'dependency'	=> [
				[
					'option' 	=> 'tab_content_source',
					'value' 	=> [ 'zion_template' ]
				]
			],
			'dynamic' 		=> [
				'enabled' => true
			]
		]
	);