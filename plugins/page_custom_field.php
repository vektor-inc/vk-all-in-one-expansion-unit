<?php

/*-------------------------------------------*/
/*  add page custom field
/*-------------------------------------------*/
add_action( 'admin_menu', 'vkExUnit_add_custom_field_pageOption' );

// add meta_box
function vkExUnit_add_custom_field_pageOption() {
	if ( apply_filters( 'vkExUnit_customField_Page_activation', false ) ) {
		add_meta_box( 'pageOption', __( 'Setting of insert items', 'vkExUnit' ), 'vkExUnit_pageOption_box', 'page', 'normal', 'high' );
	}
}

// display a meta_box
function vkExUnit_pageOption_box() {
	do_action( 'vkExUnit_customField_Page_box' );
}
