<?php

class VEU_Other_Widget_Admin_Control {
	function __construct() {
		add_action( 'admin_init', array( $this, 'add_hooks' ) );
	}

	function add_hooks() {
		add_action( 'vew_admin_setting_block', array( $this, 'admin_setting' ), 10, 1 );
		add_filter( 'vkExUnit_common_options_validate', array( $this, 'admin_config_validate' ), 10, 3 );
	}

	public function admin_config_validate( $output, $input, $defaults ) {
		$_v = array();
		if ( ! empty( $input['enable_widgets'] ) && is_array( $input['enable_widgets'] ) ) {
			foreach ( $input['enable_widgets'] as $v ) {
				array_push( $_v, $v );
			}
		}

		VEU_Widget_Control::update_options( $_v );
		return $output;
	}

	public function admin_setting( $options ) {
		include veu_get_directory() . '/inc/other-widget/template/admin_setting.php';
	}
}
