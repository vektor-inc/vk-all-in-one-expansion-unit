<?php
/*
  Load modules
  Add vkExUnit css
  Add vkExUnit js
/*-------------------------------------------*/


/*
  Load modules
/*-------------------------------------------*/
add_action( 'plugins_loaded', 'veu_loadfile' );
function veu_loadfile() {
	require vkExUnit_get_directory() . '/common_init.php';
	require vkExUnit_get_directory() . '/common_helpers.php';
	require vkExUnit_get_directory() . '/veu-package-manager.php';
	require vkExUnit_get_directory() . '/veu-packages.php';
	require vkExUnit_get_directory() . '/plugins/footer_copyright_change.php';
	vkExUnit_package_include(); // package_manager.php
}

/*
  Add vkExUnit css
/*-------------------------------------------*/
add_action( 'wp_enqueue_scripts', 'veu_print_css' );
function veu_print_css() {
	global $vkExUnit_version;
	$options = veu_get_common_options();
	if ( isset( $options['active_bootstrap'] ) && $options['active_bootstrap'] ) {
		wp_enqueue_style( 'vkExUnit_common_style', plugins_url( '', __FILE__ ) . '/css/vkExUnit_style_in_bs.css', array(), $vkExUnit_version, 'all' );
	} else {
		wp_enqueue_style( 'vkExUnit_common_style', plugins_url( '', __FILE__ ) . '/css/vkExUnit_style.css', array(), $vkExUnit_version, 'all' );
	}
}

function veu_print_editor_css() {
	add_editor_style( plugins_url( '', __FILE__ ) . '/css/vkExUnit_editor_style.css' );
}
add_action( 'after_setup_theme', 'veu_print_editor_css' );


/*
  Add vkExUnit js
/*-------------------------------------------*/
add_action( 'wp_head', 'veu_print_js' );
function veu_print_js() {
	global $vkExUnit_version;
	wp_register_script( 'vkExUnit_master-js', plugins_url( '', __FILE__ ) . '/js/all.min.js', array( 'jquery' ), $vkExUnit_version, true );
	wp_localize_script( 'vkExUnit_master-js', 'vkExOpt', apply_filters( 'vkExUnit_localize_options', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) ) );
	wp_enqueue_script( 'vkExUnit_master-js' );
}

if ( function_exists( 'register_activation_hook' ) ) {
	register_activation_hook( __FILE__, 'veu_install_function' );
}
function veu_install_function() {
	$opt = get_option( 'vkExUnit_common_options' );
	if ( ! $opt ) {
		add_option( 'vkExUnit_common_options', veu_get_common_options_default() );
	}
}
