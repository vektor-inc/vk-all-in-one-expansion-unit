<?php
/*-------------------------------------------*/
/*  add page custom field
/*-------------------------------------------*/

/**
 * Add Content meta box use for "Child Page List" , "Sitemap" , "Contact section" and more fields
 */
function veu_add_content_meta_box() {
	if ( apply_filters( 'veu_content_meta_box_activation', false ) ) {
		add_meta_box( 'veu_content_meta_box', __( 'Setting of insert items', 'vkExUnit' ), 'veu_content_meta_box_content', 'page', 'normal', 'high' );
	}
}
add_action( 'admin_menu', 'veu_add_content_meta_box' );

/**
 * Insert ExUnit Settings.
 */
function veu_content_meta_box_content() {
	do_action( 'veu_content_meta_box_content' );
}
