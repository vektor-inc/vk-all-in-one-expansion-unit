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
	  Contactform7AssetOptimize
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
		'include'     => 'wp-title/config.php',
	);

	/*
	  addReusableBlockMenu
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'addReusableBlockMenu',
		'title'       => __( 'Add Reusable block menu', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'Add Manage all reusable blocks menu to admin menu.', 'vk-all-in-one-expansion-unit' ),
		'default'     => true,
		'include'     => 'add_menu_to_block_reuse.php',
	);

	/*
	  Add Plugin Link to admin bar
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'add_plugin_link_to_admin_menu',
		'title'       => __( 'Add Plugin link to admin bar', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'Add Plugin setting page link to admin bar.', 'vk-all-in-one-expansion-unit' ),
		'default'     => true,
		'include'     => 'add_plugin_link_to_adminbar.php',
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
		'name'          => 'sns',
		'title'         => __( 'Social media cooperation', 'vk-all-in-one-expansion-unit' ),
		'description'   => $deskSns,
		'attr'          => array(
			array(
				'name'        => __( 'Setting', 'vk-all-in-one-expansion-unit' ),
				'url'         => admin_url() . 'admin.php?page=vkExUnit_main_setting#vkExUnit_sns_options',
				'enable_only' => 1,
			),
		),
		'default'       => true,
		'use_ex_blocks' => true,
		'include'       => 'sns/sns.php',
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
	  vk-google-tag-manager
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'google-tag-manager',
		'title'       => __( 'Google Tag Manager', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'Setting of Google Tag Manager', 'vk-all-in-one-expansion-unit' ),
		'default'     => false,
		'include'     => 'vk-google-tag-manager/config.php',
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
	  noindex
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'noindex',
		'title'       => __( 'Noindex additional function', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'Outputs the noindex tag to the html head of the specified page.', 'vk-all-in-one-expansion-unit' ).'<br>'.__( 'If you want to add the noindex tag to specific page that, move to that post edit screen and set from VK All in One Expansion Unit metabox in lower part of content editing field.', 'vk-all-in-one-expansion-unit' ).'<br>'.__( 'If you want add to the other page such as archive page that, you can set to ExUnit Main Setting Page.', 'vk-all-in-one-expansion-unit' ),
		'attr'        => array(
			array(
				'name'        => __( 'Setting', 'vk-all-in-one-expansion-unit' ),
				'url'         => admin_url() . 'admin.php?page=vkExUnit_main_setting#vkExUnit_noindex',
				'enable_only' => 1,
			),
		),
		'default'     => true,
		'include'     => 'noindex/noindex.php',
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
				'name'        => __( 'Enable Setting', 'vk-all-in-one-expansion-unit' ),
				'url'         => admin_url() . 'admin.php?page=vkExUnit_setting_page#widget_enablation',
				'enable_only' => 1,
			),
			array(
				'name'        => __( 'Widget Setting', 'vk-all-in-one-expansion-unit' ),
				'url'         => admin_url() . 'widgets.php',
				'enable_only' => 1,
			),
		),
		'default'     => true,
		'include'     => 'other-widget/other-widget.php',
    );

	/*
	  Before loop widget area
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'archive_loop_before_widget_area',
		'title'       => __( 'Before loop widget area', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'Add widget area before loop at published post type archive page', 'vk-all-in-one-expansion-unit' ),
		'attr'        => array(
			// array(
			// 	'name'        => __( 'Setting', 'vk-all-in-one-expansion-unit' ),
			// 	'url'         => admin_url() . 'edit.php?post_type=post_type_manage',
			// 	'enable_only' => 1,
			// ),
		),
		'default'     => false,
		'include'     => 'add_archive_loop_before_widget_area.php',
	);

    /**
     * Defualt Thumbnail
     */
    $required_packages[] = array(
		'name'        => 'default_thumbnail',
		'title'       => __( 'Default Thumbnail', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'You can set Default Thumbnail.', 'vk-all-in-one-expansion-unit' ),
		'attr'        => array(
			array(
				'name'        => __( 'Setting', 'vk-all-in-one-expansion-unit' ),
				'url'         => admin_url() . 'admin.php?page=vkExUnit_main_setting#vkExUnit_default_thumbnail',
				'enable_only' => 1,
			),
		),
		'default'     => true,
		'include'     => 'default-thumbnail/default-thumbnail.php',
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

	$insert_item_description = '<br />'.__( 'You can insert by metabox of bottom of content edit area at post edit screen, or from by the block or widget and so on.', 'vk-all-in-one-expansion-unit' );

	/*
	  ChildPageIndex
	/*-------------------------------------------*/

	$required_packages[] = array(
		'name'          => 'childPageIndex',
		'title'         => __( 'Child page index', 'vk-all-in-one-expansion-unit' ),
		'description'   => __( 'It displays a list of the child page.', 'vk-all-in-one-expansion-unit' ).$insert_item_description,
		'default'       => true,
		'include'       => 'child-page-index/child-page-index.php',
		'use_ex_blocks' => true,
	);

	/*
	  pageList_ancestor
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'pageList_ancestor',
		'title'       => __( 'Page list from ancestor', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'It displays a page list from ancestor.', 'vk-all-in-one-expansion-unit' ).$insert_item_description,
		'default'     => true,
		'include'     => 'page-list-ancestor/page-list-ancestor.php',
		'use_ex_blocks' => true,
	);

	/*
	  Contact Section
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'          => 'contact_section',
		'title'         => __( 'Contact Section', 'vk-all-in-one-expansion-unit' ),
		'description'   => __( 'It displays a contact information.', 'vk-all-in-one-expansion-unit' ).$insert_item_description,
		'attr'          => array(
			array(
				'name'        => __( 'Setting', 'vk-all-in-one-expansion-unit' ),
				'url'         => admin_url() . 'admin.php?page=vkExUnit_main_setting#vkExUnit_contact',
				'enable_only' => 1,
			),
		),
		'default'       => true,
		'include'       => 'contact-section/contact-section.php',
		'use_ex_blocks' => true,
	);

	/*
	  Sitemap_page
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'sitemap_page',
		'title'       => __( 'Display HTML Site Map', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'It displays a HTML Site Map.', 'vk-all-in-one-expansion-unit' ).$insert_item_description,
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
		'description' => __( 'Print Related posts lists to post content bottom.', 'vk-all-in-one-expansion-unit' ).'<br>'.__( 'Related posts are displayed based on tags, so please set tags for posts.', 'vk-all-in-one-expansion-unit' ),
		'default'     => true,
		'include'     => 'related_posts/related_posts.php',
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

	/**
	 * IE Alart.
	 */
    $required_packages[] = array(
		'name'        => 'display_ie_alert',
		'title'       => __( 'Display IE Alert', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'Display a warning if the user who is viewing this site is using IE.', 'vk-all-in-one-expansion-unit' ).'<br>'.__( 'IE is a very old browser and its creator Microsoft does not recommend its use. Encouraging IE users to switch to the next-generation browser will greatly contribute to the evolution of the website.', 'vk-all-in-one-expansion-unit' ),
		'default'     => true,
		'include'     => 'display-ie-alert.php',
	);

	/**
	 * Disable Core XML Sitemap.
	 */
    $required_packages[] = array(
		'name'        => 'disable_xml_sitemap',
		'title'       => __( 'Disable XML Sitemap', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'Stop the XML Sitemap feature added from WordPress 5.5.', 'vk-all-in-one-expansion-unit' ).'<br>'.__( 'If you already creating XML Sitemap by another Plugin that you can stop  native WordPress Sitemap function by this function.', 'vk-all-in-one-expansion-unit' ),
		'default'     => false,
		'include'     => 'disable-xml-sitemap.php',
	);

	/**
     * Disable Emoji.
     */
    $required_packages[] = array(
		'name'        => 'disable_emoji',
		'title'       => __( 'Disable Emojis', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'You can disable emojis.', 'vk-all-in-one-expansion-unit' ).'<br>'.__( 'If you do not using Emojis that I recommend to enable this function.', 'vk-all-in-one-expansion-unit' ).__( 'If disable emoji that you can stop print emoji codes on html head and it bring to small effect of speeding up.', 'vk-all-in-one-expansion-unit' ),
		'default'     => false,
		'include'     => 'disable-emojis.php',
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
		'default'     => true,
		'include'     => 'post-type-manager/post-type-manager-config.php',
	);

	/*
	  Page Top Button
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'pagetop_button',
		'title'       => __( 'Page Top Button', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'The page top button is displayed in the lower right corner of the screen.', 'vk-all-in-one-expansion-unit' ),
		'default'     => true,
		'include'     => 'pagetop-btn/pagetop-btn.php',
		'attr'        => array(
			array(
				'name'        => __( 'Setting', 'vk-all-in-one-expansion-unit' ),
				'url'         => admin_url() . 'admin.php?page=vkExUnit_main_setting#vkExUnit_pagetop',
				'enable_only' => true,
			),
		),
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
		'attr'        => array(
			array(
				'name'        => __( 'Setting', 'vk-all-in-one-expansion-unit' ),
				'url'         => admin_url() . 'admin.php?page=vkExUnit_main_setting#vkExUnit_smooth',
				'enable_only' => true,
			),
		),
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
	  CSS Optimize
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'css_optimize',
		'title'       => __( 'CSS Optimize', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'Optimize the CSS to speeding display.', 'vk-all-in-one-expansion-unit' ),
		'default'     => false,
		'include'     => 'vk-css-optimize/vk-css-optimize-config.php',
		'attr'        => array(
			array(
				'name'        => __( 'Setting', 'vk-all-in-one-expansion-unit' ),
				'url'         => admin_url() . 'customize.php',
				'enable_only' => true,
			),
		),
	);

	/*
	  Contactform7AssetOptimize
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'Contactform7AssetOptimize',
		'title'       => __( 'Contact Form 7 Asset Optimize', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'Unqueue Contact Form 7 assets at page of unuse form.', 'vk-all-in-one-expansion-unit' ) . '<br/>* ' . __( 'Do not activate if you using css/js optimize plugin like "Autoptimize".', 'vk-all-in-one-expansion-unit' ),
		'attr'        => array(),
		'default'     => true,
		'include'     => '/contactform7-asset-optimize.php',
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

	$not_recommend_description = '<br><br>* * * * * * * * * * * * * * * * * * * * * * * *  <br>' . __( 'This feature will be discontinued shortly.', 'vk-all-in-one-expansion-unit' ) . '<br>* * * * * * * * * * * * * * * * * * * * * * * * ';

	/*
	  VK Blocks
	/*-------------------------------------------*/
	$install_link = admin_url() . 'plugin-install.php?s=vk+blocks&tab=search&type=term';
	$required_packages[] = array(
		'name'        => 'vk-blocks',
		'title'       => __( 'VK Blocks', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'Extends Gutenberg\'s blocks.', 'vk-all-in-one-expansion-unit' ) . $not_recommend_description . '<br><a href="' . $install_link . '">' . __( 'Please install the plugin version of VK Blocks.', 'vk-all-in-one-expansion-unit' ) . '</a>',
		'default'     => false,
		'include'     => 'vk-blocks/vk-blocks-config.php',
	);

	/*
	  TinyMCE Style Tags
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'tiny_mce_style_tags',
		'title'       => __( 'TinyMCE Style Tags', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'Add TinyMCE Editor to style tags.', 'vk-all-in-one-expansion-unit' ). $not_recommend_description,
		'default'     => false,
		'include'     => 'tiny-mce-styletags.php',
	);

	/*
	  bootstrap
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'bootstrap',
		'title'       => __( 'Print Bootstrap css ( grid / button / table )', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'If your using theme has already including Bootstrap, you deactivate this item.', 'vk-all-in-one-expansion-unit' ). $not_recommend_description,
		'default'     => false,
		'include'     => 'bootstrap.php',
	);

	/*
	  metaKeyword
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'metaKeyword',
		'title'       => __( 'Print meta Keyword', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'Print meta Keyword to html head.', 'vk-all-in-one-expansion-unit' ) . $not_recommend_description,
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
		'description' => __( 'About favicon.', 'vk-all-in-one-expansion-unit' ) . $not_recommend_description . '<br>' . __( 'You can set the site icon from "Site Identity" panel of "Themes > Customize".', 'vk-all-in-one-expansion-unit' ),
		'default'     => false,
		'include'     => 'icons.php',
	);

	/*
	  Contactform7AssetOptimize
	/*-------------------------------------------*/
	$required_packages[] = array(
		'name'        => 'Contactform7AssetOptimize',
		'title'       => __( 'Contactform7 Asset Optimize', 'vk-all-in-one-expansion-unit' ),
		'description' => __( 'unqueue Contact Form 7 assets at page of unuse form.', 'vk-all-in-one-expansion-unit' ) . '<br/>* ' . __( 'Do not activate if you using css/js optimize plugin like "Autoptimize".', 'vk-all-in-one-expansion-unit' ),
		'attr'        => array(),
		'default'     => false,
		'include'     => '/contactform7-asset-optimize.php',
	);

	return $required_packages;
} // function veu_get_packages(){


$required_packages = veu_get_packages();
function vkExUnit_get_packages() {
	return veu_get_packages();
}
