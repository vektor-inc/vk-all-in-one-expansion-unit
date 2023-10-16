<?php
add_filter( 'lightning_footerPoweredCustom', 'vkExUnit_lightning_footerPoweredCustom' );
function vkExUnit_lightning_footerPoweredCustom( $lightning_footerPowered ) {
	// Powered
	/*------------------*/
	$lightning_footerPowered = __( '<p>Powered by <a href="https://wordpress.org/">WordPress</a> with <a href="https://wordpress.org/themes/lightning/" target="_blank" title="Free WordPress Theme Lightning">Lightning Theme</a> &amp; <a href="https://wordpress.org/plugins/vk-all-in-one-expansion-unit/" target="_blank">VK All in One Expansion Unit</a></p>', 'vk-all-in-one-expansion-unit' );
	return $lightning_footerPowered;

}
