<?php
/*
 * Remove Core Sitemaps actions.
 * @since 5.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

remove_action( 'init', 'wp_sitemaps_get_server' );
