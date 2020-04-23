<?php
/**
 * VkExUnit disable_guide.php
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
			'title' => __( 'Edit Guide', 'vk-all-in-one-expansion-unit' ).' : <span class="_show">SHOW</span><span class="_hide">HIDE</span>',
			'meta'  => array( 'class' => 'veu_admin_bar_disable_button' ),
		);
		$wp_admin_bar->add_node( $args );
	}
}

add_action( 'wp_head','vkExUnit_adminbar_edit_header' );
function vkExUnit_adminbar_edit_header() {
	if ( is_user_logged_in() && ! is_admin() && current_user_can( 'administrator' ) || current_user_can( 'editor' ) ) {  ?>
<style>
#wp-admin-bar-veu_disable_admin_edit .ab-item { background-color: #0085C8; cursor: pointer; }
#wp-admin-bar-veu_disable_admin_edit .ab-item ._hide { display: none; }
#wp-admin-bar-veu_disable_admin_edit.active .ab-item { background-color: #17A686; color: #555; }
#wp-admin-bar-veu_disable_admin_edit.active .ab-item ._show { display: none; }
#wp-admin-bar-veu_disable_admin_edit.active .ab-item ._hide { display: inline; }
#wp-admin-bar-veu_disable_admin_edit .ab-item:hover { background-color: #17A686; color: #555; }
#wp-admin-bar-veu_disable_admin_edit.active .ab-item:hover { background-color: #0085C8; color: #fff; }
body.vew_hide_edit_guide .veu_adminEdit { display: none; }
</style>
<script type="text/javascript">
(function(w,d,i,c,f){
	w.addEventListener('load', function(){
		var e=document.getElementById(i);
		if(e){e.addEventListener('click', function(){
			if(e.classList.contains(c)){
				e.classList.remove(c);d.body.classList.remove(f);
			}else{
				e.classList.add(c);d.body.classList.add(f);
			}
	})}})
})(window,document,'wp-admin-bar-veu_disable_admin_edit','active','vew_hide_edit_guide')
</script>
	<?php }
}
