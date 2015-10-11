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

add_filter( 'get_archives_link', 'vkExUnit_info_get_archives_link' );
function vkExUnit_info_get_archives_link( $link_html ) {
	global $my_archives_post_type;
	if ( $my_archives_post_type != '' ) {
		$add_link = '?post_type=' . $my_archives_post_type;
		$link_html = preg_replace( "/href=\'(.+)\'/", "href='$1" . $add_link. "'", $link_html );
	}
	return $link_html;
}
