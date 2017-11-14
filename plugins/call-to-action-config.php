<?php

/*-------------------------------------------*/
/*  Load modules
/*-------------------------------------------*/
if ( ! class_exists( 'Vk_Call_To_Action' ) )
{
	require_once( 'call-to-action/class-vk-call-to-action.php' );

	global $vk_call_to_action_textdomain;
	$vk_call_to_action_textdomain = 'vkExUnit';
}
