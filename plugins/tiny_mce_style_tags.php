<?php 
/*-------------------------------------------*/
/*	リッチエディタにオリジナルスタイルボタンの追加
/*-------------------------------------------*/
function vkExUnit_mce_before_init_insert_formats( $init_array ) {  
	$style_formats = array(
 		array( 
			'title' => 'a.btn-blank',  
			'block' => 'a',  
			'classes' => 'btn btn-primary btn-lg',
			'wrapper' => false,
		),
 		array( 
			'title' => 'a.btn.btn-primary',  
			'block' => 'a',  
			'classes' => 'btn btn-primary',
			'wrapper' => false,
		),
 		array( 
			'title' => 'a.btn-primary.btn-lg',  
			'block' => 'a',  
			'classes' => 'btn btn-primary btn-lg',
			'wrapper' => false,
		),
 		array( 
			'title' => 'a.btn-primary.btn-lg.btn-block',  
			'block' => 'a',  
			'classes' => 'btn btn-primary btn-lg btn-block',
			'wrapper' => false,
		),
 		array( 
			'title' => 'a.btn-primary.btn-lg.btn-block.btn-blank',  
			'block' => 'a',  
			'classes' => 'btn btn-primary btn-lg btn-block btn-blank',
			'wrapper' => false,
		),
 		array( 
			'title' => 'a.btn.btn-default',  
			'block' => 'a',  
			'classes' => 'btn btn-default',
			'wrapper' => false,
		),
 		array( 
			'title' => 'a.btn-default.btn-block',  
			'block' => 'a',  
			'classes' => 'btn btn-default btn-block',
			'wrapper' => false,
		),
 		array( 
			'title' => 'a.btn-default.btn-block.btn-lg',  
			'block' => 'a',  
			'classes' => 'btn btn-default btn-block btn-lg',
			'wrapper' => false,
		),
	);  
	
	$init_array['style_formats'] = json_encode( $style_formats );  
	
	return $init_array;  
  
} 
add_filter( 'tiny_mce_before_init', 'vkExUnit_mce_before_init_insert_formats' ); 