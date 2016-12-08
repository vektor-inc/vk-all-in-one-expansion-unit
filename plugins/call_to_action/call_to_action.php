<?php
namespace Vektor\ExUnit\Package\Cta;


/**
 * VkExUnit call_to_action.php
 * Set CTA section to after content of Page.
 *
 * @package  VkExUnit
 * @author   shoji imamura<imamura@vektor-inc.co.jp>
 * @since    3/Aug/2015
 */

require_once vkExUnit_get_directory() . '/plugins/call_to_action/class.call_to_action.php';
require_once vkExUnit_get_directory() . '/plugins/call_to_action/widget.call_to_action.php';

CTA::init();

add_action( 'widgets_init', 'Vektor\ExUnit\Package\Cta\widget_init' );
function widget_init() {
    return register_widget("Vektor\ExUnit\Package\Cta\Widget_CTA");
}
