<?php

/*********************************************
 * Color picker
 */

// color picker js
add_action( 'admin_enqueue_scripts', 'vkExUnit_admin_scripts_color_picker' );
function vkExUnit_admin_scripts_color_picker() {
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker' );
	// カラーピッカー対象class指定 （　外観 > ウィジェット 画面で効かないので一旦コメントアウト ）
	// wp_enqueue_script( 'colorpicker_script', plugins_url( 'js/admin-widget.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
}
// 外観 > ウィジェット 画面で動作させるために必要
add_action( 'admin_footer-widgets.php', 'print_scripts_pr_color' );
function print_scripts_pr_color() {
	?>
<script type="text/javascript">
(function($){
	function initColorPicker(widget) {
		widget.find( '.color_picker' ).wpColorPicker( {
			change: _.throttle( function() {
				$(this).trigger('change');
			}, 3000 )
		});
	}

	function onFormUpdate(event, widget) {
		initColorPicker(widget);
	}
	$(document).on('widget-added widget-updated', onFormUpdate );
	$(document).ready( function() {
		$('#widgets-right .widget:has(.color_picker)').each( function () {
			initColorPicker( $(this) );
		});
	});
}(jQuery));
</script>
	<?php
}

/*********************************************
 * Archives_where
 */
add_filter( 'getarchives_where', 'vkExUnit_info_getarchives_where', 10, 2 );
function vkExUnit_info_getarchives_where( $where, $r ) {
	global $my_archives_post_type;
	if ( isset( $r['post_type'] ) ) {
		$my_archives_post_type = $r['post_type'];
		$where                 = str_replace( '\'post\'', '\'' . $r['post_type'] . '\'', $where );
	} else {
		$my_archives_post_type = '';
	}
	return $where;
}

add_filter( 'get_archives_link', 'vkExUnit_rewrite_archives_link' );
function vkExUnit_rewrite_archives_link( $link_html ) {
	global $my_archives_post_type;
	if ( $my_archives_post_type && $my_archives_post_type != 'post' ) {

		$link_url_before = preg_replace( "/^.+<a.+href=\'(.+)\'.+$/is", '$1', $link_html );
		if ( $link_html == $link_url_before ) {
			return $link_html;
		}

		$olink = parse_url( $link_url_before );
		if ( preg_match( '/\/' . $my_archives_post_type . '\/?/', $olink['path'] ) ) {
			return $link_html;
		}

		if ( ! isset( $olink['query'] ) ) {
			$olink['query'] = '';
		}
		parse_str( $olink['query'], $query );
		if ( isset( $query['post_type'] ) && $query['post_type'] ) {
			return $link_html;
		}

		$query['post_type'] = $my_archives_post_type;
		$new_query          = '?' . http_build_query( $query );
		$new_url            = $olink['scheme'] . '://' . $olink['host'] . $olink['path'] . $new_query;

		$link_html = preg_replace( "/href=\'(.+)\'/", "href='" . $new_url . "'", $link_html );
		return $link_html;
	}
	return $link_html;
}
