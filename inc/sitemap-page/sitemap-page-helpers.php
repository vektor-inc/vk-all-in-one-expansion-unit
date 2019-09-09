<?php
function veu_get_sitemap_options() {
	$default_options = array(
		'excludePostTypes' => array(),
	);
	$options         = get_option( 'vkExUnit_sitemap_options', $default_options );
	$options         = wp_parse_args( $options, $default_options );
	return apply_filters( 'vkExUnit_sitemap_options', $options );
}

function veu_get_sitemap_options_default() {
	$default_options['excludeId'] = '';
	return apply_filters( 'vkExUnit_sitemap_options_default', $default_options );
}

/*-------------------------------------------*/
/*  サイトマップで非表示にする
/*-------------------------------------------*/

function veu_sitemap_exclude_page_ids() {
	// meta_key が　sitemap_hide が true で post_type が page の投稿を取得する
	$args                     = array(
		'posts_per_page' => -1, // 取得する数
		'post_type'      => 'page', // 投稿タイプ名
		'meta_query'     => array(
			array(
				'key'   => 'sitemap_hide',
				'value' => 'true',
			),
		),
	);
	$sitemap_hide_customPosts = get_posts( $args );

	// 取得した投稿データをループして、id名を $excludes に追加していく
	// 「sitemap_hide」フィールドの値が格納されていたら「$excludes」に ID を追加する処理を開始
	if ( $sitemap_hide_customPosts ) {
		$excludes = '';
		foreach ( $sitemap_hide_customPosts as $key => $value ) {
			// print_r($value);

			if ( ! $excludes ) {
				 $excludes .= $value->ID;
			} else {
				$excludes .= ',' . $value->ID;
			}

			$excludes = esc_attr( $excludes );
		}

		return $excludes;
	} // if( $sitemap_hide_customPosts ) {

} // function veu_sitemap_exclude_page_ids() {
