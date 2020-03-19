<?php

add_filter( 'vkExUnit_master_js_options', function( $options ){
	$options['enable_smooth_scroll'] = true;
	return $options;
}, 10, 1 );
