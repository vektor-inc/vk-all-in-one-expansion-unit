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

	echo '<meta name="description" content="' . vkExUnit_get_pageDescription() . '" />';
}
