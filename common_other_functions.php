<?php
add_filter('lightning_footerPoweredCustom','vkExUnit_lightning_footerPoweredCustom');
function vkExUnit_lightning_footerPoweredCustom($lightning_footerPowered){
	// Powered
	/*------------------*/
	$lightning_footerPowered = __( '<p>Powered by <a href="https://wordpress.org/">WordPress</a> with <a href="//lightning.bizvektor.com" target="_blank" title="Free WordPress Theme Lightning"> Lightning Theme</a> &amp; <a href="http://ex-unit.bizvektor.com/" target="_blank">VK All in One Expansion Unit</a> by <a href="http://www.vektor-inc.co.jp" target="_blank">Vektor,Inc.</a> technology.</p>','vkExUnit');
	return $lightning_footerPowered;

}