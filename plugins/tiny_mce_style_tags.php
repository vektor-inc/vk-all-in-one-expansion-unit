<?php 
/*-------------------------------------------*/
/*	リッチエディタにオリジナルスタイルボタンの追加
/*-------------------------------------------*/
function vkExUnit_mce_before_init_insert_formats( $init_array ) {  
	$style_formats = array(
 		array( 
			'title' => 'a.btn.btn-primary',  
			'block' => 'a',  
			'classes' => 'btn btn-primary',
			'wrapper' => true,
		),
 		array( 
			'title' => 'a.btn-primary.btn-block',  
			'block' => 'a',  
			'classes' => 'btn btn-primary btn-block',
			'wrapper' => true,
		),
 		array( 
			'title' => 'a.btn-primary.btn-block.btn-lg',  
			'block' => 'a',  
			'classes' => 'btn btn-primary btn-block btn-lg',
			'wrapper' => true,
		),
 		array( 
			'title' => 'a.btn.btn-default',  
			'block' => 'a',  
			'classes' => 'btn btn-default',
			'wrapper' => true,
		),
 		array( 
			'title' => 'a.btn-default.btn-block',  
			'block' => 'a',  
			'classes' => 'btn btn-default btn-block',
			'wrapper' => true,
		),
 		array( 
			'title' => 'a.btn-default.btn-block.btn-lg',  
			'block' => 'a',  
			'classes' => 'btn btn-default btn-block btn-lg',
			'wrapper' => true,
		),
	);  
	
	$init_array['style_formats'] = json_encode( $style_formats );  
	
	return $init_array;  
  
} 
add_filter( 'tiny_mce_before_init', 'vkExUnit_mce_before_init_insert_formats' ); 