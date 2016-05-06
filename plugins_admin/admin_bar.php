<?php
/**
 * VkExUnit disable_admin_edit.php
 * hide admin button.
 *
 * @package  VkExUnit
 * @author   shoji imamura<imamura@vektor-inc.co.jp>
 * @since    28/Aug/2015
 */


add_action( 'admin_bar_menu', 'vkExUnit_adminbar_disable', 999 );
function vkExUnit_adminbar_disable( $wp_admin_bar ) {
	if ( is_user_logged_in() && ! is_admin() && current_user_can( 'administrator' ) || current_user_can( 'editor' ) ) {
		$args = array(
			'id'    => 'veu_disable_admin_edit',
			'title' => __( 'Edit Guide', 'vkExUnit' ).' : SHOW',
			'meta'  => array( 'class' => 'veu_admin_bar_disable_button' , 'onClick' => 'javascript:void(0);' ),
		);
		$wp_admin_bar->add_node( $args );
	}
}



add_action( 'wp_head','vkExUnit_adminbar_edit_header' );
function vkExUnit_adminbar_edit_header() {
	if ( is_user_logged_in() && ! is_admin() && current_user_can( 'administrator' ) || current_user_can( 'editor' ) ) {  ?>
<style>#wpadminbar #wp-admin-bar-veu_disable_admin_edit .ab-item { background-color: #0085C8; cursor: pointer; }
#wpadminbar #wp-admin-bar-veu_disable_admin_edit .ab-item.active { background-color: #17A686; color: #555; }
#wpadminbar #wp-admin-bar-veu_disable_admin_edit .ab-item:hover { background-color: #17A686; color: #555; }
#wpadminbar #wp-admin-bar-veu_disable_admin_edit .ab-item.active:hover { background-color: #0085C8; color: #fff; }
</style><script type="text/javascript">;(function($,d){var a=false,b='',c='',f=function(){
if(a){a=false;c.show();b.removeClass('active').text('<?php echo __( 'Edit Guide', 'vkExUnit' ).' : SHOW'; ?>');}else{a=true;c.hide();b.addClass('active').text('<?php echo __( 'Edit Guide', 'vkExUnit' ).' :  HIDE'; ?>');}
};$(d).ready(function(){b=$('#wp-admin-bar-veu_disable_admin_edit .ab-item').on('click',f);c=$('.veu_adminEdit');});})(jQuery,document);</script>
	<?php }
}



add_action( 'admin_bar_menu', 'vkExUnit_adminbar_link', 40 );
function vkExUnit_adminbar_link( $wp_admin_bar ) {

	if ( ! current_user_can( 'activate_plugins' ) ) { return; }

	$args = array(
		'id'    => 'veu_adminlink',
		'title' => vkExUnit_get_little_short_name(),
		'href'  => admin_url() . 'admin.php?page=vkExUnit_main_setting',
		'meta'  => array(),
	);
	$wp_admin_bar->add_node( $args );
	$wp_admin_bar->add_node(
		array(
			'parent' => 'veu_adminlink',
			'id' => 'veu_adminlink_active',
			'title' => __( 'Active Setting','vkExUnit' ),
			'href' => admin_url() . 'admin.php?page=vkExUnit_setting_page',
		)
	);
	$wp_admin_bar->add_node(
		array(
			'parent' => 'veu_adminlink',
			'id'     => 'veu_adminlink_main',
			'title'  => __( 'Main Setting', 'vkExUnit' ),
			'href'   => admin_url() . 'admin.php?page=vkExUnit_main_setting',
		)
	);

	do_action( 'vkExUnit_action_adminbar', $wp_admin_bar );
}
