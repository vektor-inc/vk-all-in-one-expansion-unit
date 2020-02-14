<?php
require_once veu_get_directory() . '/inc/other-widget/class-veu-widget-control.php';
require_once veu_get_directory() . '/inc/other-widget/common.php';

if (is_admin()) {
	require_once veu_get_directory() . '/inc/other-widget/class-vew-other-widget-admin-control.php';
	new VEU_Other_Widget_Admin_Control();
}

function vew_widget_packages() {
	return [
		[
			'id' => 1,
			'priority' => 10,
			'name' => __( 'Recent Posts', 'vk-all-in-one-expansion-unit' ),
			'description' => __( 'Displays a list of your most recent posts', 'vk-all-in-one-expansion-unit' ),
			'include' => 'widget-new-posts.php'
		],
		[
			'id' => 2,
			'priority' => 10,
			'name' => __( 'Profile', 'vk-all-in-one-expansion-unit' ),
			'description' => __( 'Displays a your profile', 'vk-all-in-one-expansion-unit' ),
			'include' => 'widget-profile.php'
		],
		[
			'id' => 3,
			'priority' => 10,
			'name' => __( '3PR area', 'vk-all-in-one-expansion-unit' ),
			'description' => __( 'Displays a 3PR area', 'vk-all-in-one-expansion-unit' ),
			'include' => 'widget-3pr-area.php'
		],
		[
			'id' => 4,
			'priority' => 10,
			'name' => __( 'page content to widget', 'vk-all-in-one-expansion-unit' ),
			'description' => __( 'Displays a page contents to widget.', 'vk-all-in-one-expansion-unit' ),
			'include' => 'widget-page.php'
		],
		[
			'id' => 5,
			'priority' => 10,
			'name' => __( 'Categories/Custom taxonomies list', 'vk-all-in-one-expansion-unit' ),
			'description' => __( 'Displays a categories and custom taxonomies list.', 'vk-all-in-one-expansion-unit' ),
			'include' => 'widget-taxonomies.php'
		],
		[
			'id' => 6,
			'priority' => 10,
			'name' => __( 'archive list', 'vk-all-in-one-expansion-unit' ),
			'description' => __( 'Displays a list of archives. You can choose the post type and also to display archives by month or by year.', 'vk-all-in-one-expansion-unit' ),
			'include' => 'widget-archives.php'
		],
		[
			'id' => 7,
			'priority' => 10,
			'name' => __( 'PR Blocks', 'vk-all-in-one-expansion-unit' ),
			'description' => __( 'Displays a circle image or icon font for pr blocks', 'vk-all-in-one-expansion-unit' ),
			'include' => 'widget-pr-blocks.php'
		],
		[
			'id' => 8,
			'priority' => 10,
			'name' => __( 'child pages list', 'vk-all-in-one-expansion-unit' ),
			'description' => __( 'Displays list of child page for the current page.', 'vk-all-in-one-expansion-unit' ),
			'include' => 'widget-side-child-page-list.php'
		],
		[
			'id' => 9,
			'priority' => 10,
			'name' => __( 'Button', 'vk-all-in-one-expansion-unit' ),
			'description' => __( 'You can set buttons for arbitrary text.', 'vk-all-in-one-expansion-unit' ),
			'include' => 'widget-button.php'
		],
		[
			'id' => 10,
			'priority' => 10,
			'name' => __( 'Banner', 'vk-all-in-one-expansion-unit' ),
			'description' => sprintf( __( 'You can easily set up a banner simply by registering images and link destinations.', 'vk-all-in-one-expansion-unit' ), vkExUnit_get_little_short_name() ),
			'include' => 'widget-banner.php'
		],
		[
			'id' => 11,
			'priority' => 10,
			'name' => __( 'Child Page List', 'vk-all-in-one-expansion-unit' ),
			'description' => __( 'Display the child pages list from ancestor page.', 'vk-all-in-one-expansion-unit' ),
			'include' => 'widget-child-page-list.php'
		]
	];
}


VEU_Widget_Control::load_widgets();
