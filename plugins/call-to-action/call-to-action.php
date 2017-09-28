<?php

/*
このファイルの元ファイルは
https://github.com/vektor-inc/vektor-wp-libraries
にあります。修正の際は上記リポジトリのデータを修正してください。
*/

namespace Vektor\ExUnit\Package\Cta;


/**
 * VkExUnit call_to_action.php
 * Set CTA section to after content of Page.
 *
 * @package  VkExUnit
 * @author   shoji imamura<imamura@vektor-inc.co.jp>
 * @since    3/Aug/2015
 */

require_once vkExUnit_get_directory() . '/plugins/call-to-action/class-vk-call-to-action.php';
require_once vkExUnit_get_directory() . '/plugins/call-to-action/widget-call-to-action.php';

Vk_Call_To_Action::init();

add_action( 'widgets_init', 'Vektor\ExUnit\Package\Cta\widget_init' );
function widget_init() {
    return register_widget("Vektor\ExUnit\Package\Cta\Widget_CTA");
}
