<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'xmlrpc_methods', 'vkExUnit_disable_ping' );
function vkExUnit_disable_ping( $methods ) {
	unset( $methods['pingback.ping'] );
	return $methods;
}
