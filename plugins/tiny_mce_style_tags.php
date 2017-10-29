<?php 
/*-------------------------------------------*/
/*	リッチエディタにオリジナルスタイルボタンの追加
/*-------------------------------------------*/
function vkExUnit_mce_before_init_insert_formats( $init_array ) {  
	$style_formats = array(
 		array( 
			'title' => 'Lead Text',  
			'inline' => 'span',  
			'classes' => 'veu_leadTxt',
			// 'wrapper' => false,
		),
 		array( 
			'title' => 'Dummy Image',  
			'block' => 'div',  
			'classes' => 'veu_dummyImage',
			// 'wrapper' => false,
		),
 	// 	array( 
		// 	'title' => 'Table Width Auto',  
		// 	'block' => 'table',  
		// 	'classes' => 'mce-item-table veu_table_width_inherit',
		// 	// 'wrapper' => true,
		// ),
 		array( 
			'title' => 'a.btn-blank',  
			'inline' => 'a',  
			'classes' => 'btn btn-primary btn-lg',
			// 'wrapper' => false,
		),
 		array( 
			'title' => 'a.btn.btn-primary',  
			'inline' => 'a',  
			'classes' => 'btn btn-primary',
			// 'wrapper' => false,
		),
 		array( 
			'title' => 'a.btn-primary.btn-lg',  
			'inline' => 'a',  
			'classes' => 'btn btn-primary btn-lg',
			// 'wrapper' => false,
		),
 		array( 
			'title' => 'a.btn-primary.btn-lg.btn-block',  
			'inline' => 'a',  
			'classes' => 'btn btn-primary btn-lg btn-block',
			// 'wrapper' => false,
		),
 		array( 
			'title' => 'a.btn-primary.btn-lg.btn-block.btn-blank',  
			'inline' => 'a',  
			'classes' => 'btn btn-primary btn-lg btn-block btn-blank',
			// 'wrapper' => false,
		),
 		array( 
			'title' => 'a.btn.btn-default',  
			'inline' => 'a',  
			'classes' => 'btn btn-default',
			// 'wrapper' => false,
		),
 		array( 
			'title' => 'a.btn-default.btn-block',  
			'inline' => 'a',  
			'classes' => 'btn btn-default btn-block',
			// 'wrapper' => false,
		),
 		array( 
			'title' => 'a.btn-default.btn-block.btn-lg',  
			'inline' => 'a',  
			'classes' => 'btn btn-default btn-block btn-lg',
			// 'wrapper' => false,
		),
	);  
	
	$init_array['style_formats'] = json_encode( $style_formats );  
	
	return $init_array;  
  
} 
add_filter( 'tiny_mce_before_init', 'vkExUnit_mce_before_init_insert_formats' ); 