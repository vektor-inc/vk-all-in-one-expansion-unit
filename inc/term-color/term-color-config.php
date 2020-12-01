<?php

/**********************************************/
// Load modules
/**********************************************/

/*
色選択機能をつける対象のタームの指定
*/

// ★★★★★★ 関数のprefixは固有のものに変更する事 ★★★★★★
// add_filter( 'term_color_taxonomies_custom', 'fort_term_color_taxonomies_custom', 10.2 );
// function fort_term_color_taxonomies_custom( $taxonomies ) {
// $taxonomies[] = 'category';
// $taxonomies[] = 'post_tags';
// return $taxonomies;
// }
if ( ! class_exists( 'Vk_term_color' ) ) {

	/*
	読み込みタイミングを init にしておかないと
	フィールドがカスタム分類の画面で表示されない
	★★★★★★ 関数のprefixは固有のものに変更する事 ★★★★★★
	*/
	add_action( 'init', 'veu_load_term_color' );
	function veu_load_term_color() {
		require_once 'package/class.term-color.php';
	}
}
