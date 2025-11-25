<?php
add_filter( 'lightning_footerPoweredCustom', 'vkExUnit_lightning_footerPoweredCustom' );
function vkExUnit_lightning_footerPoweredCustom( $lightning_footerPowered ) {
		// Powered
		/*
		------------------*/

		$footer_text = sprintf(
		// translators: 1: link to WordPress, 2: link to Lightning theme, 3: link to VK All in One Expansion Unit plugin.
			__( 'Powered by %1$s with %2$s &amp; %3$s', 'vk-all-in-one-expansion-unit' ),
			'<a href="https://wordpress.org/">WordPress</a>',
			'<a href="https://wordpress.org/themes/lightning/" target="_blank" title="Free WordPress Theme Lightning">Lightning Theme</a>',
			'<a href="https://wordpress.org/plugins/vk-all-in-one-expansion-unit/" target="_blank">VK All in One Expansion Unit</a>'
		);
	$lightning_footerPowered = '<p>' . $footer_text . '</p>';
	return $lightning_footerPowered;
}
