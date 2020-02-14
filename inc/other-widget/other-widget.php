<?php
require_once veu_get_directory() . '/inc/other-widget/class-veu-widget-control.php';
require_once veu_get_directory() . '/inc/other-widget/common.php';

require dirname( __FILE__ ) . '/widget-new-posts.php';
require dirname( __FILE__ ) . '/widget-profile.php';
require dirname( __FILE__ ) . '/widget-3pr-area.php';
require dirname( __FILE__ ) . '/widget-page.php';
require dirname( __FILE__ ) . '/widget-taxonomies.php';
require dirname( __FILE__ ) . '/widget-archives.php';
require dirname( __FILE__ ) . '/widget-pr-blocks.php';
require dirname( __FILE__ ) . '/widget-side-child-page-list.php';
require dirname( __FILE__ ) . '/widget-button.php';
require dirname( __FILE__ ) . '/widget-banner.php';

if (is_admin()) {
	require_once veu_get_directory() . '/inc/other-widget/class-veu-other-widget-admin-control.php';
	new VEU_Other_Widget_Admin_Control();
}

function vew_widget_packages() {
	return array(
		array(
			'id' => 1,
			'class' => 'WP_Widget_vkExUnit_post_list',
		),
		array(
			'id' => 2,
			'class' => 'WP_Widget_vkExUnit_profile',
		),
		array(
			'id' => 3,
			'class' => 'WP_Widget_vkExUnit_3PR_area',
		),
		array(
			'id' => 4,
			'class' => 'WP_Widget_vkExUnit_widget_page',
		),
		array(
			'id' => 5,
			'class' => 'WP_Widget_VK_taxonomy_list',
		),
		array(
			'id' => 6,
			'class' => 'WP_Widget_VK_archive_list',
		),
		array(
			'id' => 7,
			'class' => 'WP_Widget_vkExUnit_PR_Blocks',
		),
		array(
			'id' => 8,
			'class' => 'WP_Widget_vkExUnit_ChildPageList',
		),
		array(
			'id' => 9,
			'class' => 'WP_Widget_Button',
		),
		array(
			'id' => 10,
			'class' => 'WidgetBanner',
		)
	);
	// next id is 11.
}

add_action('widgets_init', array('VEU_Widget_Control', 'widgets_init'));