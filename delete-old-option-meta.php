<?php
/**
 * Delete Old Options and Metas
 * 
 * @package VK All in One Expansion Unit
 */

/**
 * Disable Old Packages
 */
function veu_disable_old_packages() {
    $options = veu_get_common_options();
    // 古いパッケージのリスト
    $old_packages = array(
        'vk-blocks',
        'tiny_mce_style_tags',
        'bootstrap',
        'metaKeyword',
        'icon'
    );
    // 有効化オプションを削除
    if ( ! empty( $options ) ) {
        foreach( $old_packages as $old_package ) {
            unset( $options[ 'active_' . $old_package ] );
        }
        update_option( 'vkExUnit_common_options', $options );
    }
}
add_action( 'init', 'veu_disable_old_packages' );

/**
 * Delete Old Options
 */
function veu_delete_old_options() {
    // Meta キーワードを削除
    $old_options = array(
        'vkExUnit_icon_settings',
        'vkExUnit_common_keywords',
        'vkExUnit_colors'
    );
    foreach( $old_options as $old_option ) {
        delete_option( $old_option );
    }
}
add_action( 'init', 'veu_delete_old_options' );

/**
 * Delete Old Meta
 */
function veu_delete_old_metas() {
    global $wpdb;
    // Meta キーワードを削除
    $old_metas = array(
        'vkExUnit_metaKeyword'
    );
    foreach(  $old_metas as  $old_meta ) {
        $wpdb->delete(
            $wpdb->prefix . 'postmeta',
            array( 'meta_key' => $old_meta ),
            array( '%s' )
        );
    }
}
add_action( 'init', 'veu_delete_old_metas' );