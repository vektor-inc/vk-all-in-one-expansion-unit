<?php
require_once VEU_DIRECTORY_PATH . '/inc/other-widget/class-veu-widget-control.php';
require_once VEU_DIRECTORY_PATH . '/inc/other-widget/common.php';

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

if ( is_admin() ) {
	// ウィジェットの個別有効化機能管理画面読み込み
	require_once VEU_DIRECTORY_PATH . '/inc/other-widget/class-veu-other-widget-admin-control.php';
}

function veu_widget_packages() {
	return array(
		array(
			'id'    => 'post_list',
			'class' => 'WP_Widget_vkExUnit_post_list',
		),
		array(
			'id'    => 'archive_list',
			'class' => 'WP_Widget_VK_archive_list',
		),
		array(
			'id'    => 'taxonomy_list',
			'class' => 'WP_Widget_VK_taxonomy_list',
		),
		array(
			'id'    => 'child_page_list',
			'class' => 'WP_Widget_vkExUnit_ChildPageList',
		),
		array(
			'id'    => 'profile',
			'class' => 'WP_Widget_vkExUnit_profile',
		),
		array(
			'id'    => 'widget_page',
			'class' => 'WP_Widget_vkExUnit_widget_page',
		),
		array(
			'id'    => '3pr_area',
			'class' => 'WP_Widget_vkExUnit_3PR_area',
		),
		array(
			'id'    => 'pr_blocks',
			'class' => 'WP_Widget_vkExUnit_PR_Blocks',
		),
		array(
			'id'    => 'button',
			'class' => 'WP_Widget_Button',
		),
		array(
			'id'    => 'banner',
			'class' => 'WidgetBanner',
		),
	);
	// next id is 11.
}

add_action( 'widgets_init', array( 'VEU_Widget_Control', 'widgets_init' ) );
