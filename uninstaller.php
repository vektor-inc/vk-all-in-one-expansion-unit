<?php

$vkExUnit_options = veu_get_common_options();
if ( ! $vkExUnit_options['delete_options_at_deactivate'] ) {
	return; }

$delete_options = array(
	'vkExUnit_common_options',
	'vkExUnit_cta_settings',
	'vkExUnit_css_customize',
	'vkExUnit_ga_options',
	'vkExUnit_sitemap_options',
	'vkExUnit_sns_options',
	'vkExUnit_contact',
	'vkExUnit_icon_settings',
	'vkExUnit_Ads',
	'vkExUnit_description_options',
	'vkExUnit_common_keywords',
	'vkExUnit_colors',
	'vkExUnit_enable_widgets',
	'vkExUnit_pagetop',
	'vkExUnit_smooth',
	'vkExUnit_pagespeeding',
);

$delete_customfields = array(
	'vkexunit_cta_each_option',
	'vkExUnit_cta_img',
	'vkExUnit_cta_img_position',
	'vkExUnit_cta_button_text',
	'vkExUnit_cta_url',
	'vkExUnit_cta_text',
	'vkExUnit_childPageIndex',
	'vkExUnit_sitemap',
	'vkExUnit_EyeCatch_disable',
	'vkExUnit_contact_enable',
	'vkExUnit_metaKeyword',
);

$delete_options = apply_filters( 'vkExUnit_uninstall_option', $delete_options );
foreach ( $delete_options as $delete_option_name ) {
	delete_option( $delete_option_name );
}

$delete_customfields = apply_filters( 'vkExUnit_uninstall_postmeta', $delete_customfields );
global $wpdb;
foreach ( $delete_customfields as $delete_customfield ) {
	$wpdb->delete(
		$wpdb->prefix . 'postmeta',
		array( 'meta_key' => $delete_customfield ),
		array( '%s' )
	);
}

$wpdb->delete(
	$wpdb->prefix . 'posts',
	array( 'post_type' => 'cta' ),
	array( '%s' )
);
