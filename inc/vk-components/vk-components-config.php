<?php
/*-------------------------------------------*/
/*  Load modules
/*-------------------------------------------*/
if ( ! class_exists( 'VK_Component_Button' ) ) {
	require_once dirname( __FILE__ ) . '/package/class-vk-component-button.php';
}
if ( ! class_exists( 'VK_Component_Mini_Contents' ) ) {
	require_once dirname( __FILE__ ) . '/package/class-vk-component-mini-contents.php';
}
if ( ! class_exists( 'VK_Component_Posts' ) ) {
	require_once dirname( __FILE__ ) .  '/package/class-vk-component-posts.php';
}

global $vk_components_textdomain;
$vk_components_textdomain = 'lightning';
