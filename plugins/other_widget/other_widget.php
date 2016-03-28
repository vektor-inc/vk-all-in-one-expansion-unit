<?php
require vkExUnit_get_directory() . '/plugins/other_widget/widget-new-posts.php';
require vkExUnit_get_directory() . '/plugins/other_widget/widget-profile.php';
require vkExUnit_get_directory() . '/plugins/other_widget/widget-3pr-area.php';
require vkExUnit_get_directory() . '/plugins/other_widget/widget-page.php';
require vkExUnit_get_directory() . '/plugins/other_widget/widget-taxonomies.php';
require vkExUnit_get_directory() . '/plugins/other_widget/widget-archives.php';
require vkExUnit_get_directory() . '/plugins/other_widget/widget-pr-blocks.php';
// require vkExUnit_get_directory() . '/plugins/other_widget/widget-child-page-list.php';

add_filter( 'getarchives_where', 'vkExUnit_info_getarchives_where', 10, 2 );
function vkExUnit_info_getarchives_where( $where, $r ) {
	global $my_archives_post_type;
	if ( isset( $r['post_type'] ) ) {
		$my_archives_post_type = $r['post_type'];
		$where = str_replace( '\'post\'', '\'' . $r['post_type'] . '\'', $where );
	} else {
		$my_archives_post_type = '';
	}
	return $where;
}

add_filter( 'get_archives_link', 'vkExUnit_rewrite_archives_link' );
function vkExUnit_rewrite_archives_link( $link_html ) {
	global $my_archives_post_type;
	if ( $my_archives_post_type && $my_archives_post_type != 'post' ) {

		$link_url_before = preg_replace("/^.+<a.+href=\'(.+)\'.+$/is", "$1", $link_html );
		if( $link_html == $link_url_before ) return $link_html;

		$olink = parse_url($link_url_before);
		if( preg_match("/\/".$my_archives_post_type."\/?/", $olink['path'] ) ) return $link_html;

		if( ! isset( $olink['query'] ) ) $olink['query'] = '';
		parse_str( $olink['query'], $query );
		if( isset( $query['post_type'] ) && $query['post_type'] ) return $link_html;

		$query['post_type'] = $my_archives_post_type;
		$new_query = '?' . http_build_query($query);
		$new_url = $olink['scheme'] . '://' . $olink['host'] . $olink['path'] . $new_query;

		$link_html = preg_replace( "/href=\'(.+)\'/", "href='" . $new_url. "'", $link_html );
		return $link_html;
	}
	return $link_html;
}
