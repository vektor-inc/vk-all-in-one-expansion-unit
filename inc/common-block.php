<?php
/**
 * Common Block Option
 *
 * @package VK All in One Expansion Unit
 */

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
