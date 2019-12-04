<?php
function veu_get_packages() {
	$required_packages = array();
	/*
	Example :
	$required_packages[] = array(
	'name'        => 'auto_eyecatch',
	'title'       => __('Automatic Eye Catch insert', 'vk-all-in-one-expansion-unit' ),
	'description' => __('Display Eye Catch image at before content.', 'vk-all-in-one-expansion-unit' ),
	'attr'        => array(
	array(
		'name'        =>__('Setting', 'vk-all-in-one-expansion-unit' ),
		'url'         => admin_url().'admin.php?page=vkExUnit_css_customize',
		'enable_only' => 1,
	)
	),
	'default'     => false,
	'include'     => '/auto_eyecatch.php',
	);

	*/

	/*
	  fontawesome
	  wpTitle
	  metaKeyword
	  metaDescription
	  sns
	  ga
	  otherWidgets
	  css_customize
	  Contact Section
	  ChildPageIndex
	  pageList_ancestor
	  Sitemap_page
	  Call To Action
	  insert_ads
	  relatedPosts
	  disable_ping-back
	  Page Top Button
	  Smooth Scroll
	  Add Body Class
	  Nav Menu Class Custom
	  auto_eyecatch
	  TinyMCE Style Tags
	  bootstrap
	  icon
	/*-------------------------------------------*/

	/*
	  fontawesome
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'fontawesome',
		'title'       => __( 'Print link fontawesome', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'Print fontawesome link tag to html head.', 'vk-all-in-one-expansion-unit' ),
		'default'     => false,
		'include'     => 'font-awesome/font-awesome-config.php',
	);

	/*
	  VK Blocks
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'vk-blocks',
		'title'       => __( 'VK Blocks', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'Extends Gutenberg\'s blocks.', 'vk-all-in-one-expansion-unit' ),
		'default'     => true,
		'include'     => 'vk-blocks/vk-blocks-config.php',
	);

	/*
	  wpTitle
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'wpTitle',
		'title'       => __( 'Rewrite the title tag', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'Print is rewritten by its own rules to html head.', 'vk-all-in-one-expansion-unit' ),
		'attr'        => array(
			array(
				'name'        => __( 'Setting', 'vk-all-in-one-expansion-unit' ),
				'url'         => admin_url( 'admin.php?page=vkExUnit_main_setting#vkExUnit_wp_title' ),
				'enable_only' => 1,
			),
		),
		'default'     => true,
		'include'     => 'wp-title.php',
	);

	/*
	  metaDescription
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'metaDescription',
		'title'       => __( 'Print meta description', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'Print meta description to html head.', 'vk-all-in-one-expansion-unit' ),
		'default'     => true,
		'include'     => 'meta-description.php',
	);

	/*
	  sns
	/*-------------------------------------------*/
	$deskSns     = array();
	$settingPage = '<a href="' . admin_url() . 'admin.php?page=vkExUnit_main_setting#vkExUnit_sns_options">' . __( 'Main setting page' ) . '</a>';
	$deskSns[]   = '<ul>';
	$deskSns[]   = '<li>' . __( 'Print og tags to html head.', 'vk-all-in-one-expansion-unit' ) . '</li>';
	$deskSns[]   = '<li>' . __( 'Print twitter card tags to html head.', 'vk-all-in-one-expansion-unit' ) . '</li>';
	$deskSns[]   = '<li>' . __( 'Print social bookmark buttons.', 'vk-all-in-one-expansion-unit' ) . '</li>';
	$deskSns[]   = '<li>' . __( 'Facebook Page Plugin widget.', 'vk-all-in-one-expansion-unit' ) . '</li>';
	$deskSns[]   = '<li>' . __( 'Print Follow me box to content bottom.', 'vk-all-in-one-expansion-unit' ) . '</li>';
	$deskSns[]   = '</ul>';
	$deskSns[]   = '<p>' . sprintf( __( '* You can stop the function separately from the %s.', 'vk-all-in-one-expansion-unit' ), $settingPage ) . '</p>';

	$required_packages[] = array(
		'name'        => 'sns',
		'title'       => __( 'Social media cooperation', 'vk-all-in-one-expansion-unit' ),
		'description' => $deskSns,
		'attr'        => array(
			array(
				'name'        => __( 'Setting', 'vk-all-in-one-expansion-unit' ),
				'url'         => admin_url() . 'admin.php?page=vkExUnit_main_setting#vkExUnit_sns_options',
				'enable_only' => 1,
			),
		),
		'default'     => true,
		'include'     => 'sns/sns.php',
	);

	/*
	  ga
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'ga',
		'title'       => 'Google Analytics',
		'description' => __( 'Print Google Analytics tracking code.', 'vk-all-in-one-expansion-unit' ),
		'attr'        => array(
			array(
				'name'        => __( 'Setting', 'vk-all-in-one-expansion-unit' ),
				'url'         => admin_url() . 'admin.php?page=vkExUnit_main_setting#vkExUnit_ga_options',
				'enable_only' => 1,
			),
		),
		'default'     => true,
		'include'     => 'google_analytics/google_analytics.php',
	);

	/*
	  otherWidgets
	/*-------------------------------------------*/
	$desk   = array();
	$desk[] = '<p>' . __( 'You can use various widgets.', 'vk-all-in-one-expansion-unit' ) . '</p>';
	$desk[] = '<ul>';
	$desk[] = '<li>' . __( 'VK_Recent Posts - display the link text and the date of the latest article title.', 'vk-all-in-one-expansion-unit' ) . '</li>';
	$desk[] = '<li>' . __( 'VK_Page content to widget - display the contents of the page to the widgets.', 'vk-all-in-one-expansion-unit' ) . '</li>';
	$desk[] = '<li>' . __( 'VK_Profile - display the profile entered in the widget.', 'vk-all-in-one-expansion-unit' ) . '</li>';
	$desk[] = '<li>' . __( 'VK_FB Page Plugin - display the Facebook Page Plugin.', 'vk-all-in-one-expansion-unit' ) . '</li>';
	$desk[] = '<li>' . __( 'VK_3PR area - display the 3PR area.', 'vk-all-in-one-expansion-unit' ) . '</li>';
	$desk[] = '<li>VK_' . __( 'categories/tags list', 'vk-all-in-one-expansion-unit' ) . __( 'Displays a categories, tags or format list.', 'vk-all-in-one-expansion-unit' ) . '</li>';
	$desk[] = '<li>VK_' . __( 'archive list', 'vk-all-in-one-expansion-unit' ) . __( 'Displays a list of archives. You can choose the post type and also to display archives by month or by year.', 'vk-all-in-one-expansion-unit' ) . '</li>';
	$desk[] = '</ul>';

	$required_packages[] = array(
		'name'        => 'otherWidgets',
		'title'       => __( 'Widgets', 'vk-all-in-one-expansion-unit' ),
		'description' => $desk,
		'attr'        => array(
			array(
				'name'        => __( 'Setting', 'vk-all-in-one-expansion-unit' ),
				'url'         => admin_url() . 'widgets.php',
				'enable_only' => 1,
			),
		),
		'default'     => true,
		'include'     => 'other-widget/other-widget.php',
	);

	/*
	  css_customize
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'css_customize',
		'title'       => __( 'CSS customize', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'You can set Customize CSS.', 'vk-all-in-one-expansion-unit' ),
		'attr'        => array(
			array(
				'name'        => __( 'Setting', 'vk-all-in-one-expansion-unit' ),
				'url'         => admin_url() . 'admin.php?page=vkExUnit_css_customize',
				'enable_only' => 1,
			),
		),
		'default'     => true,
		'include'     => 'css-customize/css-customize.php',
	);

	/*
	  ChildPageIndex
	/*-------------------------------------------*/

	$required_packages[] = array(
		'name'        => 'childPageIndex',
		'title'       => __( 'Child page index', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'At the bottom of the specified page, it will display a list of the child page.', 'vk-all-in-one-expansion-unit' ),
		'default'     => true,
		'include'     => 'child-page-index.php',
	);

	/*
	  pageList_ancestor
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'pageList_ancestor',
		'title'       => __( 'Page list from ancestor', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'Display Page list from ancestor at after content.', 'vk-all-in-one-expansion-unit' ),
		'default'     => true,
		'include'     => 'page-list-ancestor.php',
	);

	/*
	  Contact Section
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'contact_section',
		'title'       => __( 'Contact Section', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'Display Contact Section at after content.', 'vk-all-in-one-expansion-unit' ),
		'attr'        => array(
			array(
				'name'        => __( 'Setting', 'vk-all-in-one-expansion-unit' ),
				'url'         => admin_url() . 'admin.php?page=vkExUnit_main_setting#vkExUnit_contact',
				'enable_only' => 1,
			),
		),
		'default'     => true,
		'include'     => 'contact-section/contact-section.php',
	);

	/*
	  Sitemap_page
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'sitemap_page',
		'title'       => __( 'Display HTML Site Map', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'It displays a HTML Site Map to the specified page.', 'vk-all-in-one-expansion-unit' ),
		'default'     => true,
		'include'     => 'sitemap-page/sitemap-page.php',
	);

	/*
	  Call To Action
	/*-------------------------------------------*/
	$cta_description  = __( 'Display the CTA at the end of the post content.', 'vk-all-in-one-expansion-unit' );
	$cta_description .= '<br>';
	$cta_description .= __( 'The CTA stands for "Call to action" and this is the area that prompts the user behavior.', 'vk-all-in-one-expansion-unit' );
	$cta_description .= '<br>';
	$cta_description .= __( 'As an example, text message and a link button for induction to the free sample download page.', 'vk-all-in-one-expansion-unit' );

	$required_packages[] = array(
		'name'        => 'call_to_action',
		'title'       => __( 'Call To Action', 'vk-all-in-one-expansion-unit' ),
		'description' => $cta_description,
		'attr'        => array(
			array(
				'name'        => __( 'Setting', 'vk-all-in-one-expansion-unit' ),
				'url'         => admin_url() . 'admin.php?page=vkExUnit_main_setting#vkExUnit_cta_settings',
				'enable_only' => 1,
			),
			array(
				'name'        => __( 'Contents setting', 'vk-all-in-one-expansion-unit' ),
				'url'         => admin_url() . 'edit.php?post_type=cta',
				'enable_only' => 1,
			),
		),
		'default'     => true,
		'include'     => 'call-to-action/call-to-action-config.php',
	);

	/*
	  insert_ads
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'insert_ads',
		'title'       => __( 'Insert ads', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'Insert ads to content.', 'vk-all-in-one-expansion-unit' ),
		'attr'        => array(
			array(
				'name'        => __( 'Setting', 'vk-all-in-one-expansion-unit' ),
				'url'         => admin_url() . 'admin.php?page=vkExUnit_main_setting#vkExUnit_Ads',
				'enable_only' => 1,
			),
		),
		'default'     => true,
		'include'     => 'insert-ads.php',
	);
	/*
	  relatedPosts
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'relatedPosts',
		'title'       => __( 'Related posts', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'Print Related posts lists to post content bottom.', 'vk-all-in-one-expansion-unit' ),
		'default'     => true,
		'include'     => 'related_posts/related_posts.php',
	);

	/*
	  noindex
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'noindex',
		'title'       => __( 'Noindex additional function', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'Print noindex tag to html head.', 'vk-all-in-one-expansion-unit' ),
		'default'     => true,
		'include'     => 'noindex/noindex.php',
	);

	/*
	  disable_ping-back
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'disable_ping-back',
		'title'       => __( 'Disable ping back', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'Disable xmlrpc ping back.', 'vk-all-in-one-expansion-unit' ),
		'default'     => false,
		'include'     => 'disable_ping-back.php',
		'hidden'      => true,
	);

	$required_packages[] = array(
		'name'        => 'disable_dashbord',
		'title'       => __( 'Disable dashbord', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'Disable dashbord', 'vk-all-in-one-expansion-unit' ),
		'default'     => false,
		'include'     => 'disable-dashbord.php',
		'hidden'      => true,
	);

	$required_packages[] = array(
		'name'        => 'admin_bar',
		'title'       => __( 'Admin bar manu', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'Add ExUnit menu to admin bar.', 'vk-all-in-one-expansion-unit' ),
		'default'     => true,
		'include'     => '../admin/admin_bar.php',
	);

	/*
	  post_type_manager
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'post_type_manager',
		'title'       => __( 'Post Type Manager', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'Add custom post types and custom taxonomies.', 'vk-all-in-one-expansion-unit' ),
		'attr'        => array(
			array(
				'name'        => __( 'Setting', 'vk-all-in-one-expansion-unit' ),
				'url'         => admin_url() . 'edit.php?post_type=post_type_manage',
				'enable_only' => 1,
			),
		),
		'default'     => false,
		'include'     => 'post-type-manager/post-type-manager-config.php',
	);

	/*
	  Page Top Button
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'pagetop_button',
		'title'       => __( 'Page Top Button', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'The page top button is displayed in the lower right corner of the screen.', 'vk-all-in-one-expansion-unit' ),
		'default'     => false,
		'include'     => 'pagetop-btn/pagetop-btn.php',
	);

	/*
	  Smooth Scroll
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'smooth_scroll',
		'title'       => __( 'Smooth scroll', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'Add smooth scroll at anchor link in same page.', 'vk-all-in-one-expansion-unit' ),
		'default'     => true,
		'include'     => 'smooth-scroll/smooth-scroll.php',
	);

	/*
	  Add Body Class
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'add_body_class',
		'title'       => __( 'Add body class', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'Add "Post type", "Page slug" etc class name to the body class.', 'vk-all-in-one-expansion-unit' ),
		'default'     => true,
		'include'     => 'add-body-class.php',
	);

	/*
	  Nav Menu Class Custom
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'nav_menu_class_custom',
		'title'       => __( 'Navi menu class custom', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'Current class tuning of navi menu.', 'vk-all-in-one-expansion-unit' ),
		'default'     => true,
		'include'     => 'nav-menu-class-custom.php',
	);

	/*
	  auto_eyecatch
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'auto_eyecatch',
		'title'       => __( 'Automatic Eye Catch insert', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'Display Eye Catch image at before content.', 'vk-all-in-one-expansion-unit' ),
		'default'     => false,
		'include'     => 'auto-eyecatch/auto-eyecatch.php',
	);

	/*
	  TinyMCE Style Tags
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'tiny_mce_style_tags',
		'title'       => __( 'TinyMCE Style Tags', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'Add TinyMCE Editor to style tags.', 'vk-all-in-one-expansion-unit' ),
		'default'     => true,
		'include'     => 'tiny-mce-styletags.php',
	);

	/*
	  bootstrap
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'bootstrap',
		'title'       => __( 'Print Bootstrap css ( grid / button / table )', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'If your using theme has already including Bootstrap, you deactivate this item.', 'vk-all-in-one-expansion-unit' ),
		'default'     => false,
		'include'     => 'bootstrap.php',
	);

	/*
	  metaKeyword
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'metaKeyword',
		'title'       => __( 'Print meta Keyword', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'Print meta Keyword to html head.', 'vk-all-in-one-expansion-unit' ) . '<br><br>* * * * * * * * * * * * * * * * * * * * * * * *  <br>' . __( 'This feature will be discontinued shortly.', 'vk-all-in-one-expansion-unit' ) . '<br>* * * * * * * * * * * * * * * * * * * * * * * * ',
		'attr'        => array(
			array(
				'name'        => __( 'Setting', 'vk-all-in-one-expansion-unit' ),
				'url'         => admin_url() . 'admin.php?page=vkExUnit_main_setting#vkExUnit_common_keywords',
				'enable_only' => 1,
			),
		),
		'default'     => false,
		'include'     => 'meta-keyword/meta-keyword.php',
	);

	/*
	  icon
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'icon',
		'title'       => __( 'Favicon setting', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'About favicon.', 'vk-all-in-one-expansion-unit' ) . '<br><br>* * * * * * * * * * * * * * * * * * * * * * * *  <br>' . __( 'This feature will be discontinued shortly.<br>You can set the site icon from "Site Identity" panel of "Themes > Customize".', 'vk-all-in-one-expansion-unit' ) . '<br>* * * * * * * * * * * * * * * * * * * * * * * * ',
		'default'     => false,
		'include'     => 'icons.php',
	);

	return $required_packages;
} // function veu_get_packages(){


$required_packages = veu_get_packages();
function vkExUnit_get_packages() {
	return veu_get_packages();
}
