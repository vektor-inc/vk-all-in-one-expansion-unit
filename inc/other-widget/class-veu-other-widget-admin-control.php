<?php

class VEU_Other_Widget_Admin_Control {
	function __construct() {
		add_action( 'admin_init', array( $this, 'add_hooks' ) );
	}

	function add_hooks() {
		// Add Widget activation list table to ExUnit Active Setting page.
		add_action( 'vew_admin_setting_block', array( $this, 'admin_setting' ), 10, 1 );
		// veu_get_common_options_default() の中で走る。
		// 普通はアクションフックでええんちゃう？と思ったりするが 
		// $input の値を受け取る手法としてこのフィルターを使っているっぽい
		add_filter( 'vkExUnit_common_options_validate', array( $this, 'admin_config_validate' ), 10, 3 );
	}

	/**
	 * ウィジェット有効化設定
	 */
	public function admin_config_validate( $output, $input, $defaults ) {
		$_v = array();
		// 有効化設定の $_POST['vkExUnit_widget_setting'] がちゃんと落ちてきたら動作するように変更
		if ( ! empty( $_POST['vkExUnit_widget_setting'] ) ) {
			// ウィジェットの有効化情報（$input['enable_widgets']）がちゃんと落ちてきたら update 処理する
			if ( ! empty( $input['enable_widgets'] ) && is_array( $input['enable_widgets'] ) ) {
				foreach ( $input['enable_widgets'] as $v ) {
					array_push( $_v, $v );
				}
			}
			/*
			有効化設定ページ以外だと $input['enable_widgets'] が入ってこないので $_v が空のまま
			option値 vkExUnit_enable_widgets を上書きしてしまい、全てのウィジェットが有効化解除されてしまうので注意
			*/
			VEU_Widget_Control::update_options( $_v );
			return $output;
		}
	}

	public function admin_setting( $options ) {
		include VEU_DIRECTORY_PATH . '/inc/other-widget/template/admin_setting.php';
	}
}
new VEU_Other_Widget_Admin_Control();
