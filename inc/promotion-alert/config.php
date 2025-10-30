<?php
/**
 * VEU Promotion Notation Setting
 */

require __DIR__ . '/package/class-veu-promotion-alert.php';
VEU_Promotion_Alert::init();

function veu_load_promotion_alert_metabox() {
	require_once __DIR__ . '/package/class-veu-promotion-alert-metabox.php';
	$veu_promotion_alert_metabox = new VEU_Promotion_Alert_Metabox();
}
add_action( 'admin_menu', 'veu_load_promotion_alert_metabox' );
