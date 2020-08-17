<?php
/*
-------------------------------------------*/
/*
  footer add pagetop btn
/*-------------------------------------------*/
add_action( 'wp_footer', 'veu_add_pagetop' );
function veu_add_pagetop() {
	echo '<a href="#top" id="page_top" class="page_top_btn">PAGE TOP</a>';
}

function veu_pagetop_button() {
	$dynamic_css = ':root {
		--ver_page_top_button_url:url(' . veu_get_directory_uri( '/assets/images/to-top-btn-icon.svg' ) . ');
	}';

	// delete before after space
	$dynamic_css = trim( $dynamic_css );
	// convert tab and br to space
	$dynamic_css = preg_replace( '/[\n\r\t]/', '', $dynamic_css );
	// Change multiple spaces to single space
	$dynamic_css = preg_replace( '/\s(?=\s)/', '', $dynamic_css );
	wp_add_inline_style( 'vkExUnit_common_style', $dynamic_css );
}
add_action( 'wp_head', 'veu_pagetop_button', 5 );
