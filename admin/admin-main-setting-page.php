<?php
/*
 Main Setting Page  _ ExUnit > メイン設定 メニューを追加
 Main Setting Page  _ ページのフレーム（ メニューとメインエリア両方 ）
 Main Setting Page  _ メインエリアの中身



/*
 Main Setting Page  _ ExUnit > メイン設定 メニューを追加
/*-------------------------------------------*/
use VektorInc\VK_Admin\VkAdmin;

function veu_add_main_setting() {
	// $capability_required = veu_get_capability_required();
	$custom_page = add_submenu_page(
		'vkExUnit_setting_page',            // parent
		__( 'Main setting', 'vk-all-in-one-expansion-unit' ),   // Name of page
		__( 'Main setting', 'vk-all-in-one-expansion-unit' ),   // Label in menu
		'activate_plugins',                         // veu_get_capability_required()でないのは edit_theme_options権限を付与したユーザーにもアクセスさせないためにactivate_pluginsにしている。
		// $capability_required,		// Capability
		'vkExUnit_main_setting',        // ユニークなこのサブメニューページの識別子
		'veu_render_main_frame'         // メニューページのコンテンツを出力する関数
	);
	if ( ! $custom_page ) {
		return; }
}
add_action( 'admin_menu', 'veu_add_main_setting', 15 );


/*
 Main Setting Page  _ ページのフレーム（ メニューとメインエリア両方 ）
/*-------------------------------------------*/
function veu_render_main_frame() {

	// nonce
	if ( isset( $_POST['_nonce_vkExUnit'] ) && wp_verify_nonce( $_POST['_nonce_vkExUnit'], 'standing_on_the_shoulder_of_giants' ) ) {

		// sanitize & update
		veu_main_sanitaize_and_update( $_POST );
	}

	// Left menu area top Title
	$get_page_title = veu_get_little_short_name() . ' Main setting';

	// Left menu area top logo
	$get_logo_html = veu_get_systemlogo_html();

	// $menu
	/*--------------------------------------------------*/
	global $vkExUnit_options;
	if ( ! isset( $vkExUnit_options ) ) {
		$vkExUnit_options = array();
	}
	$get_menu_html = '';

	foreach ( $vkExUnit_options as $vkoption ) {
		if ( ! isset( $vkoption['render_page'] ) ) {
			continue; }
		// $linkUrl = ($i == 0) ? 'wpwrap':$vkoption['option_name'];
		$linkUrl        = $vkoption['option_name'];
		$get_menu_html .= '<li id="btn_"' . $vkoption['option_name'] . '" class="' . $vkoption['option_name'] . '"><a href="#' . $linkUrl . '">';
		$get_menu_html .= $vkoption['tab_label'];
		$get_menu_html .= '</a></li>';
	}

	VkAdmin::admin_page_frame( $get_page_title, 'vkExUnit_the_main_setting_body', $get_logo_html, $get_menu_html );

}

/*
 Main Setting Page  _ メインエリアの中身
/*-------------------------------------------*/
function vkExUnit_the_main_setting_body() {
	global $vkExUnit_options;?>
	<form method="post" action="">
	<?php
	wp_nonce_field( 'standing_on_the_shoulder_of_giants', '_nonce_vkExUnit' );
	if ( is_array( $vkExUnit_options ) ) {
		echo '<div>'; // jsでfirst-child取得用
		foreach ( $vkExUnit_options as $vkoption ) {

			if ( empty( $vkoption['render_page'] ) ) {
				continue; }

			echo '<section id="' . $vkoption['option_name'] . '">';

			call_user_func_array( $vkoption['render_page'], array() );

			echo '</section>';
		}
		echo '</div>';

	} else {

		echo  __( 'Activated Packages is noting. please activate some package.', 'vk-all-in-one-expansion-unit' );

	}
	echo  '</form>';
}

/*
 Main Setting Page  _ 値をアップデート
 Main Setting Page で複数のoption値が送信される。
 それらをループしながらサニタイズしながらアップデートする
/*-------------------------------------------*/
function veu_main_sanitaize_and_update( $_post ) {

	// ExUnitで保存しているoption項目の配列
	global $vkExUnit_options;

	if ( ! empty( $vkExUnit_options ) ) {

		// ExUnitで利用しているoption項目をループしながらサニタイズ＆アップデートする
		foreach ( $vkExUnit_options as $veu_option ) {

			// サニタイズ Call back が登録されている場合にサニタイズ実行
			// ※サニタイズ call back がないものは保存されない
			if ( ! empty( $veu_option['callback'] ) ) {

				// コールバック関数にわたす入力値を指定
				$option_name = $veu_option['option_name'];

				if ( ! empty( $_post[ $option_name ] ) ) {
					$before = $_post[ $option_name ];
				} else {
					$before = null;
				} // if ( ! empty( $_post[ $option_name ] ){

				// サニタイズコールバックを実行
				$option = call_user_func_array( $veu_option['callback'], array( $before ) );
			} // if ( ! empty( $veu_option['callback'] ) ) {

			update_option( $veu_option['option_name'], $option );
		}
	}
}

/*
global $vkExUnit_options に各種値を登録するための関数
 */
/**
 * [vkExUnit_register_setting description]
 * @param  string $tab_label         管理画面に表示される機能の名前
 * @param  string $option_name       option保存値
 * @param  string $sanitize_callback 保存時のサニタイズ関数
 * @param  string $render_page       メイン設定画面を出力する関数
 * @return [type]                    [description]
 */
function vkExUnit_register_setting( $tab_label, $option_name, $sanitize_callback, $render_page ) {

	$tab_label = ! empty( $tab_label ) ? $tab_label : 'tab_label';
	global $vkExUnit_options;
	if ( ! isset( $vkExUnit_options ) ) {
		$vkExUnit_options = array();
	}
	$vkExUnit_options[] =
		array(
			'option_name' => $option_name,
			'callback'    => $sanitize_callback,
			'tab_label'   => $tab_label,
			'render_page' => $render_page,
		);
}
