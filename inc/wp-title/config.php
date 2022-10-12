<?php
/**
 * WP Title の書き換え
 *
 * @package WP Title
 */
$package_path = dirname( __FILE__ ) . '/package/';
require $package_path . 'wp-title.php';
require $package_path . 'class-veu-metabox-head-title.php';
$VEU_Metabox_Head_Title = new VEU_Metabox_Head_Title();
