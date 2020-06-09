<?php
$common_attributes = array(
    'vkb_hidden'    => array(
        'type'    => 'boolean',
        'default' => false,
    ),
    'vkb_hidden_xl' => array(
        'type'    => 'boolean',
        'default' => false,
    ),
    'vkb_hidden_lg' => array(
        'type'    => 'boolean',
        'default' => false,
    ),
    'vkb_hidden_md' => array(
        'type'    => 'boolean',
        'default' => false,
    ),
    'vkb_hidden_sm' => array(
        'type'    => 'boolean',
        'default' => false,
    ),
    'vkb_hidden_xs' => array(
        'type'    => 'boolean',
        'default' => false,
    ),
);

function vk_add_hidden_class($classes = '', $attributes){

	if ( isset($attributes['vkb_hidden']) && $attributes['vkb_hidden'] ) {
		$classes .= ' ' . 'vk_hidden';
	}
	if ( isset($attributes['vkb_hidden_xl']) && $attributes['vkb_hidden_xl'] ) {
		$classes .= ' ' . 'vk_hidden_xl';
	}
	if ( isset($attributes['vkb_hidden_lg']) ) {
		$classes .= ' ' . 'vk_hidden_lg';
	}
	if ( isset($attributes['vkb_hidden_md']) ) {
		$classes .= ' ' . 'vk_hidden_md';
	}
	if ( isset($attributes['vkb_hidden_sm']) ) {
		$classes .= ' ' . 'vk_hidden_sm';
	}
	if ( isset($attributes['vkb_hidden_xs']) ) {
		$classes .= ' ' . 'vk_hidden_xs';
    }
    
    return $classes;
}