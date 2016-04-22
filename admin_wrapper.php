<?php

function vkExUnit_add_main_setting() {
	$capability_required = add_filter( 'vkExUnit_ga_page_capability', vkExUnit_get_capability_required() );
	$custom_page = add_submenu_page(
		'vkExUnit_setting_page',			// parent
		__( 'Main setting','vkExUnit' ),		// Name of page
		__( 'Main setting','vkExUnit' ),		// Label in menu
		// $capability_required,
		'activate_plugins',					// Capability
		'vkExUnit_main_setting',			// ユニークなこのサブメニューページの識別子
		'vkExUnit_render_main_config'		// メニューページのコンテンツを出力する関数
	);
	if ( ! $custom_page ) { return; }
}
add_action( 'admin_menu', 'vkExUnit_add_main_setting' );


function vkExUnit_render_main_config() {

	vkExUnit_save_main_config();
?>
<div class="wrap vkExUnit_admin_page">

<div class="adminMain">
<form method="post" action="">
<?php
	wp_nonce_field( 'standing_on_the_shoulder_of_giants', '_nonce_vkExUnit' );
	global $vkExUnit_options;
if ( is_array( $vkExUnit_options ) ) : ?>

	<div id="adminContent_sub">

	<?php echo vkExUnit_get_systemlogo();?>

	<div class="optionNav"><ul>
	<?php
	// $i = 0;
	foreach ( $vkExUnit_options as $vkoption ) {
		if ( ! isset( $vkoption['render_page'] ) ) {  continue; }
		// $linkUrl = ($i == 0) ? 'wpwrap':$vkoption['option_name'];
		$linkUrl = $vkoption['option_name'];
		echo '<li id="btn_"'. $vkoption['option_name']. '" class="'.$vkoption['option_name'].'"><a href="#'. $linkUrl .'">';
		echo $vkoption['tab_label'];
		echo '</a></li>';
		// $i++;

	}
	?>
	</ul></div>
	</div><!-- [ /#adminContent_sub ] -->

	<div id="adminContent_main">

	<?php
	foreach ( $vkExUnit_options as $vkoption ) {
		if ( empty( $vkoption['render_page'] ) ) {  continue; }

		echo '<section id="'. $vkoption['option_name'] .'">';

		call_user_func_array( $vkoption['render_page'], array() );

		echo '</section>';
	}
	?>

	</div><!-- [ /#adminContent_main ] -->



<?php else :

	_e( 'Activated Packages is noting. please activate some package.', 'vkExUnit' );

	endif; ?>
</form>
</div><!-- [ /.adminMain ] -->

<div class="adminSub">
<div class="exUnit_infoBox"><?php vkExUnit_news_body(); ?></div>
<div class="exUnit_adminBnr"><?php vkExUnit_admin_banner(); ?></div>
</div><!-- [ /.adminSub ] -->
</div>
<?php
}

function vkExUnit_register_setting( $tab_label = 'tab_label', $option_name, $sanitize_callback, $render_page ) {
	global $vkExUnit_options;
	$vkExUnit_options[] =
		array(
			'option_name' => $option_name,
			'callback' => $sanitize_callback,
			'tab_label' => $tab_label,
			'render_page' => $render_page,
		);
}


function vkExUnit_main_config_sanitaize( $post ) {
	global $vkExUnit_options;

	if ( ! empty( $vkExUnit_options ) ) {
		foreach ( $vkExUnit_options as $opt ) {

			if ( ! empty( $opt['callback'] ) ) {
				$before = ( ! empty( $post[ $opt['option_name'] ] )? $post[ $opt['option_name'] ]: null);
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
