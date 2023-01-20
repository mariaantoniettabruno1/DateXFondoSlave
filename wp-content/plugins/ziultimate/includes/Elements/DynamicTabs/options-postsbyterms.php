<?php
	$dyndata->add_option(
		'tax_name',
		[
			'type' 		=> 'select',
			'title' 	=> __('Select Taxonomy', 'ziultimate'),
			'options'	=> $this->getTaxonomies(),
			'default' 	=> 'category',
			'dependency'	=> [
				[
					'option' 	=> 'source',
					'value' 	=> [ 'postsbyterms' ]
				]
			]
		]
	);

	$dyndata->add_option(
		'include_ids',
		[
			'type' 			=> 'text',
			'title' 		=> __('Include Terms', 'ziultimate'),
			'width' 		=> 50,
			'description'	=> __('Enter the term ID. Apply comma separator for multiple IDs', 'ziultimate'),
			'dependency'	=> [
				[
					'option' 	=> 'source',
					'value' 	=> [ 'postsbyterms' ]
				]
			],
			'dynamic' 		=> [
				'enabled' => true
			]
		]
	);

	$dyndata->add_option(
		'exclude_ids',
		[
			'type' 			=> 'text',
			'title' 		=> __('Exclude Terms', 'ziultimate'),
			'width' 		=> 50,
			'description'	=> __('Enter the term ID. Apply comma separator for multiple IDs', 'ziultimate'),
			'dependency'	=> [
				[
					'option' 	=> 'source',
					'value' 	=> [ 'postsbyterms' ]
				]
			],
			'dynamic' 		=> [
				'enabled' => true
			]
		]
	);

	$dyndata->add_option(
		'hide_empty',
		[
			'type' 		=> 'checkbox_switch',
			'title' 	=> __('Hide Empty Category', 'ziultimate'),
			'default' 	=> true,
			'layout' 	=> 'inline',
			'dependency'	=> [
				[
					'option' 	=> 'source',
					'value' 	=> [ 'postsbyterms' ]
				]
			]
		]
	);

	$dyndata->add_option(
		'child_of',
		[
			'type' 			=> 'text',
			'title' 		=> __('Child Of'),
			'width' 		=> 50,
			'dependency'	=> [
				[
					'option' 	=> 'source',
					'value' 	=> [ 'postsbyterms' ]
				]
			],
			'dynamic' 		=> [
				'enabled' => true
			]
		]
	);

	$dyndata->add_option(
		'limit',
		[
			'type' 			=> 'text',
			'title' 		=> __('Number of Terms'),
			'description'	=> __('How many terms will show in tabs. Default is 3.', 'ziultimate'),
			'default' 		=> 3,
			'width' 		=> 50,
			'dependency'	=> [
				[
					'option' 	=> 'source',
					'value' 	=> [ 'postsbyterms' ]
				]
			],
			'dynamic' 		=> [
				'enabled' => true
			]
		]
	);

	$dyndata->add_option(
		'order',
		[
			'type' 			=> 'select',
			'title' 		=> __('Order'),
			'default' 		=> 'ASC',
			'width' 		=> 50,
			'options' 		=> [
				[
					'name' 	=> 'ASC',
					'id' 	=> 'ASC'
				],
				[
					'name' 	=> 'DESC',
					'id' 	=> 'DESC'
				]
			],
			'dependency'	=> [
				[
					'option' 	=> 'source',
					'value' 	=> [ 'postsbyterms' ]
				]
			]
		]
	);

	$dyndata->add_option(
		'orderby',
		[
			'type' 			=> 'select',
			'title' 		=> __('Order by'),
			'default' 		=> 'name',
			'width' 		=> 50,
			'options' 		=> [
				[
					'name' 	=> 'Name',
					'id' 	=> 'name'
				],
				[
					'name' 	=> 'ID',
					'id' 	=> 'id'
				],
				[
					'name' 	=> 'Slug',
					'id' 	=> 'slug'
				],
				[
					'name' 	=> 'Menu Order',
					'id' 	=> 'menu_order'
				],
				[
					'name' 	=> 'Include',
					'id' 	=> 'include'
				],
				[
					'name' 	=> 'Count',
					'id' 	=> 'count'
				]
			],
			'dependency'	=> [
				[
					'option' 	=> 'source',
					'value' 	=> [ 'postsbyterms' ]
				]
			]
		]
	);

	$dyndata->add_option(
		'saved_template_id',
		[
			'type' 			=> 'text',
			'title' 		=> __('Zion Builder TemplateFondo ID', 'ziultimate'),
			'description'	=> __('It is a mandatory option. You should enter the correct template ID.', 'ziultimate'),
			'dependency'	=> [
				[
					'option' 	=> 'source',
					'value' 	=> [ 'postsbyterms' ]
				]
			],
			'dynamic' 		=> [
				'enabled' => true
			]
		]
	);