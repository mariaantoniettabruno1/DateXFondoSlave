<?php
	$dyndata->add_option(
		'acfrep_repfld',
		[
			'type' 			=> 'text',
			'title' 		=> __('Repeater Field Name', 'ziultimate'),
			'dependency' 	=> [
				[
					'option' 	=> 'source',
					'value' 	=> ['acfrep', 'acfopt']
				]
			]
		]
	);

	$dyndata->add_option(
		'mbsettings_pg',
		[
			'type' 			=> 'text',
			'title' 		=> __('Option Name', 'ziultimate'),
			'dependency' 	=> [
				[
					'option' 	=> 'source',
					'value' 	=> ['mbsettings']
				]
			]
		]
	);

	$dyndata->add_option(
		'mbgroup_id',
		[
			'type' 			=> 'text',
			'title' 		=> __('Cloneable Group ID', 'ziultimate'),
			'dependency' 	=> [
				[
					'option' 	=> 'source',
					'value' 	=> ['mbgroup', 'mbsettings']
				]
			]
		]
	);

	$dyndata->add_option(
		'acfrep_title',
		[
			'type' 			=> 'text',
			'title' 		=> __('Title Field Name', 'ziultimate'),
			'dependency' 	=> [
				[
					'option' 	=> 'source',
					'value' 	=> ['acfrep', 'acfopt']
				]
			]
		]
	);

	/*$dyndata->add_option(
		'acfrep_subtitle',
		[
			'type' 			=> 'text',
			'title' 		=> __('Sub Title Field Name', 'ziultimate'),
			'dependency' 	=> [
				[
					'option' 	=> 'source',
					'value' 	=> ['acfrep', 'acfopt']
				]
			]
		]
	);*/

	$dyndata->add_option(
		'mbgroup_title',
		[
			'type' 			=> 'text',
			'title' 		=> __('Tab Title Field ID', 'ziultimate'),
			'dependency' 	=> [
				[
					'option' 	=> 'source',
					'value' 	=> ['mbgroup', 'mbsettings']
				]
			]
		]
	);

	/*$dyndata->add_option(
		'mbgroup_subtitle',
		[
			'type' 			=> 'text',
			'title' 		=> __('Sub Title Field ID', 'ziultimate'),
			'dependency' 	=> [
				[
					'option' 	=> 'source',
					'value' 	=> ['mbgroup', 'mbsettings']
				]
			]
		]
	);*/

	$dyndata->add_option(
		'acfrep_content',
		[
			'type' 			=> 'text',
			'title' 		=> __('Tab Content Field Name', 'ziultimate'),
			'dependency' 	=> [
				[
					'option' 	=> 'source',
					'value' 	=> ['acfrep', 'acfopt']
				]
			]
		]
	);

	$dyndata->add_option(
		'mbgroup_content',
		[
			'type' 			=> 'text',
			'title' 		=> __('Content Field ID', 'ziultimate'),
			'dependency' 	=> [
				[
					'option' 	=> 'source',
					'value' 	=> ['mbgroup', 'mbsettings']
				]
			]
		]
	);

	$dyndata->add_option(
		'enable_autop',
		[
			'type' 			=> 'checkbox_switch',
			'title' 		=> __('Enable wpautop?', 'ziultimate'),
			'default' 		=> false,
			'layout' 		=> 'inline',
			'dependency' 	=> [
				[
					'option' 	=> 'source',
					'value' 	=> ['mbgroup', 'mbsettings']
				]
			]
		]
	);