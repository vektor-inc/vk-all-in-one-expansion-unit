<?php

/*-------------------------------------------*/
/* Main Setting Page  _ メニューに追加
/*-------------------------------------------*/
function vkExUnit_add_main_setting() {
	$capability_required = add_filter( 'vkExUnit_ga_page_capability', vkExUnit_get_capability_required() );
	$custom_page = add_submenu_page(
		'vkExUnit_setting_page',			// parent
		__( 'Main setting','vkExUnit' ),		// Name of page
		__( 'Main setting','vkExUnit' ),		// Label in menu
		// $capability_required,
		'activate_plugins',					// Capability
		'vkExUnit_main_setting',			// ユニークなこのサブメニューページの識別子
		'vkExUnit_render_main_frame'		// メニューページのコンテンツを出力する関数
	);
	if ( ! $custom_page ) { return; }
}
add_action( 'admin_menu', 'vkExUnit_add_main_setting' );


/*-------------------------------------------*/
/* Main Setting Page  _ ページのフレーム
/*-------------------------------------------*/
function vkExUnit_render_main_frame() {

	vkExUnit_save_main_config();

	// Left menu area top Title
	$get_page_title = 'ExUnit Main setting';

	// Left menu area top logo
	$get_logo_html = vkExUnit_get_systemlogo();

	// $menu
	/*--------------------------------------------------*/
	global $vkExUnit_options;
	if (!isset($vkExUnit_options)) $vkExUnit_options = array();
	$get_menu_html = '';
	foreach ( $vkExUnit_options as $vkoption ) {
		if ( ! isset( $vkoption['render_page'] ) ) {  continue; }
		// $linkUrl = ($i == 0) ? 'wpwrap':$vkoption['option_name'];
		$linkUrl = $vkoption['option_name'];
		$get_menu_html .= '<li id="btn_"'. $vkoption['option_name']. '" class="'.$vkoption['option_name'].'"><a href="#'. $linkUrl .'">';
		$get_menu_html .= $vkoption['tab_label'];
		$get_menu_html .= '</a></li>';
	}

	Vk_Admin::admin_page_frame( $get_page_title, 'vkExUnit_the_main_setting_body', $get_logo_html, $get_menu_html );

}

/*-------------------------------------------*/
/* Main Setting Page  _ ページのメインエリアの中身
/*-------------------------------------------*/
function vkExUnit_the_main_setting_body(){
	global $vkExUnit_options;?>
	<form method="post" action="">
	<?php wp_nonce_field( 'standing_on_the_shoulder_of_giants', '_nonce_vkExUnit' );
	if ( is_array( $vkExUnit_options ) ) {
		echo '<div>'; // jsでfirst-child取得用
		foreach ( $vkExUnit_options as $vkoption ) {

			if ( empty( $vkoption['render_page'] ) ) { continue; }

			echo '<section id="'. $vkoption['option_name'] .'">';

			call_user_func_array( $vkoption['render_page'], array() );

			echo '</section>';
		}
		echo '</div>';

	} else {

		echo  __( 'Activated Packages is noting. please activate some package.', 'vkExUnit' );

	}
	echo  '</form>';
}

function vkExUnit_register_setting( $tab_label = 'tab_label', $option_name, $sanitize_callback, $render_page ) {
	global $vkExUnit_options;
	if (!isset($vkExUnit_options)) $vkExUnit_options = array();
	$vkExUnit_options[] =
		array(
			'option_name' => $option_name,
			'callback' => $sanitize_callback,
			'tab_label' => $tab_label,
			'render_page' => $render_page,
		);
}

/*-------------------------------------------*/
/* Main Setting Page  _ 値をアップデート
/*-------------------------------------------*/
function vkExUnit_main_config_sanitaize( $post ) {
	global $vkExUnit_options;

	if ( ! empty( $vkExUnit_options ) ) {
		foreach ( $vkExUnit_options as $opt ) {

			if ( ! empty( $opt['callback'] ) ) {
				$before = ( ! empty( $post[ $opt['option_name'] ] ) ? $post[ $opt['option_name'] ]: null);
				$option = call_user_func_array( $opt['callback'], array( $before ) );
			}

			update_option( $opt['option_name'], $option );
		}
	}
}

function vkExUnit_save_main_config() {

	// nonce
	if ( ! isset( $_POST['_nonce_vkExUnit'] ) ) {
		return ;
	}
	if ( ! wp_verify_nonce( $_POST['_nonce_vkExUnit'], 'standing_on_the_shoulder_of_giants' ) ) {
		return ;
	}

	vkExUnit_main_config_sanitaize( $_POST );
}
