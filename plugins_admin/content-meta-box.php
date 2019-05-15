<?php
/*
  add page custom field
/*-------------------------------------------*/

require_once( dirname( __FILE__ ) . '/class-veu-metabox.php' );

/**
 * Add Content meta box use for "Child Page List" , "Sitemap" , "Contact section" and more fields
 */
function veu_add_content_meta_box() {
	if ( apply_filters( 'veu_content_meta_box_activation', false ) ) {
		$meta_box_name = veu_get_name();

		$args       = array(
			'public' => true,
		);
		$post_types = get_post_types( $args );

		foreach ( $post_types as $key => $post_type ) {
			add_meta_box( 'veu_content_meta_box', $meta_box_name, 'veu_post_metabox_body', $post_type, 'normal', 'high' );
		}
	}
}
add_action( 'admin_menu', 'veu_add_content_meta_box' );

/**
 * Insert ExUnit Settings.
 */
function veu_post_metabox_body() {
	do_action( 'veu_post_metabox_body' );
}
