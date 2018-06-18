<?php
function vkExUnit_get_packages() {
	$required_packages = array();
	/*
	Example :
	$required_packages[] = array(
	'name'        => 'auto_eyecatch',
	'title'       => __('Automatic Eye Catch insert', 'vkExUnit'),
	'description' => __('Display Eye Catch image at before content.', 'vkExUnit'),
	'attr'        => array(
		array(
			'name'        =>__('Setting','vkExUnit'),
			'url'         => admin_url().'admin.php?page=vkExUnit_css_customize',
			'enable_only' => 1,
		)
	),
	'default'     => false,
	'include'     => '/auto_eyecatch.php',
	);

	*/

	/*-------------------------------------------*/
	/*  bootstrap
	/*-------------------------------------------*/
	/*  fontawesome
	/*-------------------------------------------*/
	/*  icon
	/*-------------------------------------------*/
	/*  wpTitle
	/*-------------------------------------------*/
	/*  metaKeyword
	/*-------------------------------------------*/
	/*  metaDescription
	/*-------------------------------------------*/
	/*  sns
	/*-------------------------------------------*/
	/*  ga
	/*-------------------------------------------*/
	/*  otherWidgets
	/*-------------------------------------------*/
	/*  css_customize
	/*-------------------------------------------*/
	/*  Contact Section
	/*-------------------------------------------*/
	/*  ChildPageIndex
	/*-------------------------------------------*/
	/*  pageList_ancestor
	/*-------------------------------------------*/
	/*  Sitemap_page
	/*-------------------------------------------*/
	/*  Call To Action
	/*-------------------------------------------*/
	/*  insert_ads
	/*-------------------------------------------*/
	/*  relatedPosts
	/*-------------------------------------------*/
	/*  auto_eyecatch
	/*-------------------------------------------*/
	/*  disable_ping-back
	/*-------------------------------------------*/
	/*  TinyMCE Style Tags
	/*-------------------------------------------*/
	/*  Page Top Button
	/*-------------------------------------------*/

	/*-------------------------------------------*/
	/*  bootstrap
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'bootstrap',
		'title'       => __( 'Print Bootstrap css ( grid / button / table )', 'vkExUnit' ),
		'description' => __( 'If your using theme has already including Bootstrap, you deactivate this item.', 'vkExUnit' ),
		'default'     => false,
		'include'     => 'bootstrap.php',
	);

	/*-------------------------------------------*/
	/*  fontawesome
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'fontawesome',
		'title'       => __( 'Print link fontawesome', 'vkExUnit' ),
		'description' => __( 'Print fontawesome link tag to html head.', 'vkExUnit' ),
		'default'     => false,
		'include'     => 'font-awesome-config.php',
	);

	/*-------------------------------------------*/
	/*  icon
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'icon',
		'title'       => __( 'Favicon setting', 'vkExUnit' ),
		'description' => __( 'About favicon.', 'vkExUnit' ) . '<br><br>* * * * * * * * * * * * * * * * * * * * * * * *  <br>' . __( 'This feature will be discontinued shortly.<br>You can set the site icon from "Site Identity" panel of "Themes > Customize".', 'vkExUnit' ) . '<br>* * * * * * * * * * * * * * * * * * * * * * * * ',
		'default'     => true,
		'include'     => 'icons.php',
	);

	/*-------------------------------------------*/
	/*  wpTitle
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'wpTitle',
		'title'       => __( 'Rewrite the title tag', 'vkExUnit' ),
		'description' => __( 'Print is rewritten by its own rules to html head.', 'vkExUnit' ),
		'attr'        => array(
			array(
				'name'        => __( 'Setting', 'vkExUnit' ),
				'url'         => admin_url( 'admin.php?page=vkExUnit_main_setting#vkExUnit_wp_title' ),
				'enable_only' => 1,
			),
		),
		'default'     => true,
		'include'     => 'wp_title.php',
	);

	/*-------------------------------------------*/
	/*  metaKeyword
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'metaKeyword',
		'title'       => __( 'Print meta Keyword', 'vkExUnit' ),
		'description' => __( 'Print meta Keyword to html head.', 'vkExUnit' ),
		'attr'        => array(
			array(
				'name'        => __( 'Setting', 'vkExUnit' ),
				'url'         => admin_url() . 'admin.php?page=vkExUnit_main_setting#vkExUnit_common_keywords',
				'enable_only' => 1,
			),
		),
		'default'     => true,
		'include'     => 'meta_keyword.php',
	);
	/*-------------------------------------------*/
	/*  metaDescription
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'metaDescription',
		'title'       => __( 'Print meta description', 'vkExUnit' ),
		'description' => __( 'Print meta description to html head.', 'vkExUnit' ),
		'default'     => true,
		'include'     => 'meta_description.php',
	);

	/*-------------------------------------------*/
	/*  sns
	/*-------------------------------------------*/
	$deskSns     = array();
	$settingPage = '<a href="' . admin_url() . 'admin.php?page=vkExUnit_main_setting#vkExUnit_sns_options">' . __( 'Main setting page' ) . '</a>';
	$deskSns[]   = '<ul>';
	$deskSns[]   = '<li>' . __( 'Print og tags to html head.', 'vkExUnit' ) . '</li>';
	$deskSns[]   = '<li>' . __( 'Print twitter card tags to html head.', 'vkExUnit' ) . '</li>';
	$deskSns[]   = '<li>' . __( 'Print social bookmark buttons.', 'vkExUnit' ) . '</li>';
	$deskSns[]   = '<li>' . __( 'Facebook Page Plugin widget.', 'vkExUnit' ) . '</li>';
	$deskSns[]   = '<li>' . __( 'Print Follow me box to content bottom.', 'vkExUnit' ) . '</li>';
	$deskSns[]   = '</ul>';
	$deskSns[]   = '<p>' . sprintf( __( '* You can stop the function separately from the %s.', 'vkExUnit' ), $settingPage ) . '</p>';

	$required_packages[] = array(
		'name'        => 'sns',
		'title'       => __( 'Social media cooperation', 'vkExUnit' ),
		'description' => $deskSns,
		'attr'        => array(
			array(
				'name'        => __( 'Setting', 'vkExUnit' ),
				'url'         => admin_url() . 'admin.php?page=vkExUnit_main_setting#vkExUnit_sns_options',
				'enable_only' => 1,
			),
		),
		'default'     => true,
		'include'     => 'sns/sns.php',
	);

	/*-------------------------------------------*/
	/*  ga
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'ga',
		'title'       => 'Google Analytics',
		'description' => __( 'Print Google Analytics tracking code.', 'vkExUnit' ),
		'attr'        => array(
			array(
				'name'        => __( 'Setting', 'vkExUnit' ),
				'url'         => admin_url() . 'admin.php?page=vkExUnit_main_setting#vkExUnit_ga_options',
				'enable_only' => 1,
			),
		),
		'default'     => true,
		'include'     => 'google_analytics/google_analytics.php',
	);

	/*-------------------------------------------*/
	/*  otherWidgets
	/*-------------------------------------------*/
	$desk   = array();
	$desk[] = '<p>' . __( 'You can use various widgets.', 'vkExUnit' ) . '</p>';
	$desk[] = '<ul>';
	$desk[] = '<li>' . __( 'VK_Recent Posts - display the link text and the date of the latest article title.', 'vkExUnit' ) . '</li>';
	$desk[] = '<li>' . __( 'VK_Page content to widget - display the contents of the page to the widgets.', 'vkExUnit' ) . '</li>';
	$desk[] = '<li>' . __( 'VK_Profile - display the profile entered in the widget.', 'vkExUnit' ) . '</li>';
	$desk[] = '<li>' . __( 'VK_FB Page Plugin - display the Facebook Page Plugin.', 'vkExUnit' ) . '</li>';
	$desk[] = '<li>' . __( 'VK_3PR area - display the 3PR area.', 'vkExUnit' ) . '</li>';
	$desk[] = '<li>VK_' . __( 'categories/tags list', 'vkExUnit' ) . __( 'Displays a categories, tags or format list.', 'vkExUnit' ) . '</li>';
	$desk[] = '<li>VK_' . __( 'archive list', 'vkExUnit' ) . __( 'Displays a list of archives. You can choose the post type and also to display archives by month or by year.', 'vkExUnit' ) . '</li>';
	$desk[] = '</ul>';

	$required_packages[] = array(
		'name'        => 'otherWidgets',
		'title'       => __( 'Widgets', 'vkExUnit' ),
		'description' => $desk,
		'attr'        => array(
			array(
				'name'        => __( 'Setting', 'vkExUnit' ),
				'url'         => admin_url() . 'widgets.php',
				'enable_only' => 1,
			),
		),
		'default'     => true,
		'include'     => 'other_widget/other_widget.php',
	);

	/*-------------------------------------------*/
	/*  css_customize
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'css_customize',
		'title'       => __( 'CSS customize', 'vkExUnit' ),
		'description' => __( 'You can set Customize CSS.', 'vkExUnit' ),
		'attr'        => array(
			array(
				'name'        => __( 'Setting', 'vkExUnit' ),
				'url'         => admin_url() . 'admin.php?page=vkExUnit_css_customize',
				'enable_only' => 1,
			),
		),
		'default'     => true,
		'include'     => 'css_customize/css_customize.php',
	);

	/*-------------------------------------------*/
	/*  ChildPageIndex
	/*-------------------------------------------*/

	$required_packages[] = array(
		'name'        => 'childPageIndex',
		'title'       => __( 'Child page index', 'vkExUnit' ),
		'description' => __( 'At the bottom of the specified page, it will display a list of the child page.', 'vkExUnit' ),
		'default'     => true,
		'include'     => 'child_page_index.php',
	);

	/*-------------------------------------------*/
	/*  pageList_ancestor
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'pageList_ancestor',
		'title'       => __( 'Page list from ancestor', 'vkExUnit' ),
		'description' => __( 'Display Page list from ancestor at after content.', 'vkExUnit' ),
		'default'     => true,
		'include'     => 'pageList_ancestor.php',
	);

	/*-------------------------------------------*/
	/*  Contact Section
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'contact_section',
		'title'       => __( 'Contact Section', 'vkExUnit' ),
		'description' => __( 'Display Contact Section at after content.', 'vkExUnit' ),
		'attr'        => array(
			array(
				'name'        => __( 'Setting', 'vkExUnit' ),
				'url'         => admin_url() . 'admin.php?page=vkExUnit_main_setting#vkExUnit_contact',
				'enable_only' => 1,
			),
		),
		'default'     => true,
		'include'     => 'contact-section/contact-section.php',
	);

	/*-------------------------------------------*/
	/*  Sitemap_page
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'sitemap_page',
		'title'       => __( 'Display HTML Site Map', 'vkExUnit' ),
		'description' => __( 'It displays a HTML Site Map to the specified page.', 'vkExUnit' ),
		'default'     => true,
		'include'     => 'sitemap_page/sitemap_page.php',
	);

	/*-------------------------------------------*/
	/*  Call To Action
	/*-------------------------------------------*/
	$cta_description  = __( 'Display the CTA at the end of the post content.', 'vkExUnit' );
	$cta_description .= '<br>';
	$cta_description .= __( 'The CTA stands for "Call to action" and this is the area that prompts the user behavior.', 'vkExUnit' );
	$cta_description .= '<br>';
	$cta_description .= __( 'As an example, text message and a link button for induction to the free sample download page.', 'vkExUnit' );

	$required_packages[] = array(
		'name'        => 'call_to_action',
		'title'       => __( 'Call To Action', 'vkExUnit' ),
		'description' => $cta_description,
		'attr'        => array(
			array(
				'name'        => __( 'Setting', 'vkExUnit' ),
				'url'         => admin_url() . 'admin.php?page=vkExUnit_main_setting#vkExUnit_cta_settings',
				'enable_only' => 1,
			),
			array(
				'name'        => __( 'Contents setting', 'vkExUnit' ),
				'url'         => admin_url() . 'edit.php?post_type=cta',
				'enable_only' => 1,
			),
		),
		'default'     => true,
		'include'     => 'call-to-action-config.php',
	);

	/*-------------------------------------------*/
	/*  insert_ads
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'insert_ads',
		'title'       => __( 'Insert ads', 'vkExUnit' ),
		'description' => __( 'Insert ads to content.', 'vkExUnit' ),
		'attr'        => array(
			array(
				'name'        => __( 'Setting', 'vkExUnit' ),
				'url'         => admin_url() . 'admin.php?page=vkExUnit_main_setting#vkExUnit_Ads',
				'enable_only' => 1,
			),
		),
		'default'     => true,
		'include'     => 'insert_ads.php',
	);
	/*-------------------------------------------*/
	/*  relatedPosts
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'relatedPosts',
		'title'       => __( 'Related posts', 'vkExUnit' ),
		'description' => __( 'Print Related posts lists to post content bottom.', 'vkExUnit' ),
		'default'     => true,
		'include'     => 'related_posts/related_posts.php',
	);

	/*-------------------------------------------*/
	/*  auto_eyecatch
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'auto_eyecatch',
		'title'       => __( 'Automatic Eye Catch insert', 'vkExUnit' ),
		'description' => __( 'Display Eye Catch image at before content.', 'vkExUnit' ),
		'default'     => false,
		'include'     => 'auto_eyecatch.php',
	);

	/*-------------------------------------------*/
	/*  disable_ping-back
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'disable_ping-back',
		'title'       => __( 'Disable ping back', 'vkExUnit' ),
		'description' => __( 'Disable xmlrpc ping back.', 'vkExUnit' ),
		'default'     => false,
		'include'     => 'disable_ping-back.php',
		'hidden'      => true,
	);

	$required_packages[] = array(
		'name'        => 'disable_dashbord',
		'title'       => __( 'Disable dashbord', 'vkExUnit' ),
		'description' => __( 'Disable dashbord', 'vkExUnit' ),
		'default'     => false,
		'include'     => 'disable_dashbord.php',
		'hidden'      => true,
	);

	/*-------------------------------------------*/
	/*  TinyMCE Style Tags
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'tiny_mce_style_tags',
		'title'       => __( 'TinyMCE Style Tags', 'vkExUnit' ),
		'description' => __( 'Add TinyMCE Editor to style tags.', 'vkExUnit' ),
		'default'     => true,
		'include'     => 'tiny_mce_style_tags.php',
	);

	$required_packages[] = array(
		'name'        => 'admin_bar',
		'title'       => __( 'Admin bar manu', 'vkExUnit' ),
		'description' => __( 'Add ExUnit menu to admin bar.', 'vkExUnit' ),
		'default'     => true,
		'include'     => '../plugins_admin/admin_bar.php',
	);

	/*-------------------------------------------*/
	/*  post_type_manager
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'post_type_manager',
		'title'       => __( 'Post Type Manager', 'vkExUnit' ),
		'description' => __( 'Add custom post types and custom taxonomies.', 'vkExUnit' ),
		'attr'        => array(
			array(
				'name'        => __( 'Setting', 'vkExUnit' ),
				'url'         => admin_url() . 'edit.php?post_type=post_type_manage',
				'enable_only' => 1,
			),
		),
		'default'     => false,
		'include'     => 'post-type-manager-config.php',
	);

	/*-------------------------------------------*/
	/*  Page Top Button
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'pagetop_button',
		'title'       => __( 'Page Top Button', 'vkExUnit' ),
		'description' => __( 'The page top button is displayed in the lower right corner of the screen.', 'vkExUnit' ),
		'default'     => false,
		'include'     => 'pagetop-btn/pagetop-btn.php',
	);
	return $required_packages;
} // function vkExUnit_get_packages(){

$required_packages = vkExUnit_get_packages();

foreach ( $required_packages as $package ) {
	vkExUnit_package_register( $package );
}
