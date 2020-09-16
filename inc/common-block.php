<?php
/**
 * Common Block Option
 *
 * @package VK All in One Expansion Unit
 */

/**
 * VK Block Deprecated Alart
 */
function veu_block_deprecated_alart() {
	global $pagenow;

	if ( 'index.php' === $pagenow && veu_package_is_enable( 'vk-blocks' ) ) {

		// 既に有効化されている場合は表示しない
		//  → 本来ははプラグイン側が有効化されてたらExUnitの方は停止されるのでここを追加しない
		//  → と、言いたい所だがExUnitとプラグインの VK Blocks が同時に有効化されているケースが未だにある
		//  → けれど、プラグイン側が有効化されていようが ExUnit の　VK Blocks がアクティブな場合は問答無用で表示させるべき
		//  → return しない
		// if ( is_plugin_active('vk-blocks') || is_plugin_active('vk-blocks-pro' ) ){
		// 	return;
		// }

		// プラグイン有効化権限がない人にも表示しない
		//  → 権限がある人に連絡してもらわないといけないから表示まま
		// if ( ! current_user_can( 'activate_plugins' ) ) {
		// 	return;
		// }

		$text  = '<div class="notice notice-info"><p>';	
		$text .= '<strong>ExUnit : </strong> ';
		$text .= __( 'VK Blocks in ExUnit will be deleted soon.', 'vk-all-in-one-expansion-unit' ).'</p>';
		$text .= '<ol>';
		$text .= '<li>';
		$text .= __( 'Deactive VK Blocks at ExUnit', 'vk-all-in-one-expansion-unit' ) . ' ';
		$text .= '[ <a href="' . admin_url('?page=vkExUnit_setting_page') . '" target="_blank">' . __( 'Active Setting', 'vk-all-in-one-expansion-unit' ) . '</a> ]';
		$text .= '</li>';
		$text .= '<li>';
		$text .= __( 'If you need VK Blocks, You can install VK Blocks Plugin.', 'vk-all-in-one-expansion-unit' ) . ' ';
		$text .= '[ <a href="' . admin_url('plugin-install.php?s=VK+Blocks&tab=search&type=term') . '">' . __( 'Install VK Blocks', 'vk-all-in-one-expansion-unit' ) . '</a> ]';
		$text .= '</li>';
		$text .= '</ol>';
		$text .= '</div>';
		echo $text;
	}
}
add_action( 'admin_notices', 'veu_block_deprecated_alart' );

global $common_attributes;
$common_attributes = array(
	'vkb_hidden'       => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'vkb_hidden_xxl'   => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'vkb_hidden_xl_v2' => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'vkb_hidden_xl'    => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'vkb_hidden_lg'    => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'vkb_hidden_md'    => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'vkb_hidden_sm'    => array(
		'type'    => 'boolean',
		'default' => false,
	),
	'vkb_hidden_xs'    => array(
		'type'    => 'boolean',
		'default' => false,
	),
);

if ( ! function_exists( 'vk_add_hidden_class' ) ) {
	/**
	 * Hidden Class Options
	 *
	 * @param string $classes added classes.
	 * @param string $attributes attributes.
	 */
	function vk_add_hidden_class( $classes = '', $attributes ) {

		if ( isset( $attributes['vkb_hidden'] ) && $attributes['vkb_hidden'] ) {
			$classes .= ' vk_hidden';
		}
		if ( isset( $attributes['vkb_hidden_xxl'] ) && $attributes['vkb_hidden_xxl'] ) {
			$classes .= ' vk_hidden-xxl';
		}
		if ( isset( $attributes['vkb_hidden_xl_v2'] ) && $attributes['vkb_hidden_xl_v2'] ) {
			$classes .= ' vk_hidden-xl';
		}
		if ( isset( $attributes['vkb_hidden_lg'] ) && $attributes['vkb_hidden_lg'] ) {
			$classes .= ' vk_hidden-lg';
		}
		if ( isset( $attributes['vkb_hidden_md'] ) && $attributes['vkb_hidden_md'] ) {
			$classes .= ' vk_hidden-md';
		}
		if ( isset( $attributes['vkb_hidden_sm'] ) && $attributes['vkb_hidden_sm'] ) {
			$classes .= ' vk_hidden-sm';
		}
		if ( isset( $attributes['vkb_hidden_xs'] ) && $attributes['vkb_hidden_xs'] ) {
			$classes .= ' vk_hidden-xs';
		}

		return $classes;
	}
}
