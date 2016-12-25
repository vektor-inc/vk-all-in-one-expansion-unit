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
            'title' => __( 'Edit Guide', 'vkExUnit' ).' : <span class="_show">SHOW</span><span class="_hide">HIDE</span>',
            'meta'  => array( 'class' => 'veu_admin_bar_disable_button' , 'onClick' => 'javascript:void(0);' ),
        );
        $wp_admin_bar->add_node( $args );
    }
}



add_action( 'wp_head','vkExUnit_adminbar_edit_header' );
function vkExUnit_adminbar_edit_header() {
    if ( is_user_logged_in() && ! is_admin() && current_user_can( 'administrator' ) || current_user_can( 'editor' ) ) {  ?>
<style>#wpadminbar #wp-admin-bar-veu_disable_admin_edit .ab-item { background-color: #0085C8; cursor: pointer; }
#wpadminbar #wp-admin-bar-veu_disable_admin_edit .ab-item ._hide { display: none; }
#wpadminbar #wp-admin-bar-veu_disable_admin_edit .ab-item.active { background-color: #17A686; color: #555; }
#wpadminbar #wp-admin-bar-veu_disable_admin_edit .ab-item.active ._show { display: none; }
#wpadminbar #wp-admin-bar-veu_disable_admin_edit .ab-item.active ._hide { display: inline; }
#wpadminbar #wp-admin-bar-veu_disable_admin_edit .ab-item:hover { background-color: #17A686; color: #555; }
#wpadminbar #wp-admin-bar-veu_disable_admin_edit .ab-item.active:hover { background-color: #0085C8; color: #fff; }</style>
    <?php }
}
