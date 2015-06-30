<?php 

function vkExUnit_add_main_setting() {
	$capability_required = add_filter( 'vkExUnit_ga_page_capability', vkExUnit_get_capability_required() );
	$custom_page = add_submenu_page(
		'vkExUnit_setting_page',			// parent
		'Main setting',						// Name of page
		'Main setting',						// Label in menu
		// $capability_required,
		'activate_plugins',					// Capability
		'vkExUnit_main_setting',			// ユニークなこのサブメニューページの識別子
		'vkExUnit_render_main_config'		// メニューページのコンテンツを出力する関数
	);
	if ( ! $custom_page ) return;
}
add_action( 'admin_menu', 'vkExUnit_add_main_setting' );



function vkExUnit_render_main_config(){

	vkExUnit_save_main_config();
?>
<div class="warp">
<form method="post" action="">

<?php 
	wp_nonce_field( 'standing_on_the_shoulder_of_giants', '_nonce_vkExUnit' );
	do_action('vkExUnit_main_config');
?>

<?php submit_button(); ?>
</form>
</div>
<?php
}



function vkExUnit_register_setting( $option_group=false, $option_name, $sanitize_callback ){
	global $vkExUnit_options;
	$vkExUnit_options[] = array('option_name'=>$option_name, 'callback'=>$sanitize_callback);
}


function vkExUnit_main_config_sanitaize($post){
	global $vkExUnit_options;

	if(!empty($vkExUnit_options)){
		foreach($vkExUnit_options as $opt){
			if(!function_exists($opt['callback'])){ continue; }

			$before = (isset($post[$opt['option_name']])? $post[$opt['option_name']]: null);
			$option = $opt['callback']($before);
			update_option($opt['option_name'], $option);
		}
	}
}


function vkExUnit_save_main_config(){

    // nonce
    if(!isset($_POST['_nonce_vkExUnit'])){
        return ;
    }
    if(!wp_verify_nonce($_POST['_nonce_vkExUnit'], 'standing_on_the_shoulder_of_giants')){
        return ;
    }

    vkExUnit_main_config_sanitaize($_POST);
}