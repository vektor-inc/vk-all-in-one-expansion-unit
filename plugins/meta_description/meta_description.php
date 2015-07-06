<?php
add_post_type_support( 'page', 'excerpt' );


function vkExUnit_description_options_init() {
	vkExUnit_register_setting(
		__('Meta Description', 'vkExUnit'), 	 // tab label.
		'vkExUnit_description_options',			 // name attr
		false,                                   // sanitaise function name
		'vkExUnit_add_description_options_page'  // setting_page function name
	);
}
add_action( 'admin_init', 'vkExUnit_description_options_init' );



function vkExUnit_add_description_options_page(){
?>
<h3><?php _e('Meta Description', 'vkExUnit'); ?></h3>
<div id="meta_description" class="sectionBox">
<table class="form-table">
<tr><th>ディスクリプション</th>
<td>
各ページの編集画面の「抜粋」欄に記入した内容がmetaタグのディスクリプションに反映されます。<br />
metaタグのディスクリプションはGoogleなどの検索サイトの検索結果画面で、サイトタイトルの下などに表示されます。<br />
抜粋欄が未記入の場合は、本文文頭より240文字がディスクリプションとして適用される仕様となっています。<br />
トップページのメタディスクリプションにはサイトのキャッチフレーズが適用されます。しかし、トップページに設定した固定ページに抜粋が記入されている場合はその内容が反映されます。<br />
* 抜粋欄が表示されていない場合は、編集画面の右上に「表示」というタブがありますので、そこをクリックすると「抜粋」欄を表示するチェックボックスが出てきますので、チェックして下さい。<br />
</td></tr>
</table>
</div>
<?php
}


/*-------------------------------------------*/
/*	head_description
/*-------------------------------------------*/
add_filter( 'wp_head', 'vkExUnit_render_HeadDescription', 5 );
function vkExUnit_render_HeadDescription() {
	global $wp_query;
	$post = $wp_query->get_queried_object();
	if (is_home() || is_front_page() ) {
		if ( isset($post->post_excerpt) && $post->post_excerpt ) {
			$metadescription = get_the_excerpt();
		} else {
			$metadescription = get_bloginfo( 'description' );
		}
	} else if (is_category() || is_tax()) {
		if ( ! $post->description ) {
			$metadescription = sprintf(__('About %s', 'biz-vektor'),single_cat_title()).get_bloginfo('name').' '.get_bloginfo('description');
		} else {
			$metadescription = esc_html( $post->description );
		}
	} else if (is_tag()) {
		$metadescription = strip_tags(tag_description());
		$metadescription = str_replace(array("\r\n","\r","\n"), '', $metadescription);  // delete br
		if ( ! $metadescription ) {
			$metadescription = sprintf(__('About %s', 'biz-vektor'),single_tag_title()).get_bloginfo('name').' '.get_bloginfo('description');
		}
	} else if (is_archive()) {
		if (is_year()){
			$description_date = get_the_date( _x( 'Y', 'yearly archives date format', 'biz-vektor' ) );
			$metadescription = sprintf(_x('Article of %s.','Yearly archive description', 'biz-vektor'), $description_date );
			$metadescription .= ' '.get_bloginfo('name').' '.get_bloginfo('description');
		} else if (is_month()){
			$description_date = get_the_date( _x( 'F Y', 'monthly archives date format', 'biz-vektor' ) );
			$metadescription = sprintf(_x('Article of %s.','Archive description', 'biz-vektor'),$description_date );
			$metadescription .= ' '.get_bloginfo('name').' '.get_bloginfo('description');
		} else if (is_author()) {
			$userObj = get_queried_object();
			$metadescription = sprintf(_x('Article of %s.','Archive description', 'biz-vektor'),esc_html($userObj->display_name) );
			$metadescription .= ' '.get_bloginfo('name').' '.get_bloginfo('description');
		} else {
			$postType = get_post_type();
			$metadescription = sprintf(_x('Article of %s.','Archive description', 'biz-vektor'),esc_html(get_post_type_object($postType)->labels->name) );
			$metadescription .= ' '.get_bloginfo('name').' '.get_bloginfo('description');
		}
	} else if (is_page() || is_single()) {
		$metaExcerpt = $post->post_excerpt;
		if ($metaExcerpt) {
			// $metadescription = strip_tags($post->post_excerpt);
			$metadescription = strip_tags($post->post_excerpt);
		} else {
			$metadescription = mb_substr( strip_tags($post->post_content), 0, 240 ); // kill tags and trim 240 chara
			$metadescription = str_replace(array("\r\n","\r","\n"), ' ', $metadescription);  // delete br
		}
	} else {
		$metadescription = get_bloginfo('description');
	}
	global $paged;
	if ( $paged != '0'){
		$metadescription = '['.sprintf(__('Page of %s', 'biz-vektor' ),$paged).'] '.$metadescription;
	}
	$metadescription = apply_filters( 'metadescriptionCustom', $metadescription );
	

	echo '<meta name="description" content="' . $metadescription . '" />';
}
