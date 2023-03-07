/*-------------------------------------------*/
/*  画像登録処理
/*-------------------------------------------*/

// 画像登録処理（ ボタンに直接 onclick="javascript:vk_title_bg_image_addiditional(this);return false;" を記述している ）
if ( vk_widget_image_add == undefined ){

	// 画像追加処理
	var vk_widget_image_add = function(e){

	// プレビュー画像を表示するdiv
    var thumb_outer=jQuery(e).parent().children("._display");

	// 画像IDを保存するinputタグ
    var thumb_id=jQuery(e).parent().children("._form").children('._id')[0];
    var thumb_url=jQuery(e).parent().children("._form").children('._url')[0];
	var thumb_alt=jQuery(e).parent().children("._form").children('._alt')[0];

    var u=wp.media({library:{type:'image'},multiple:false}).on('select', function(e){
		u.state().get('selection').each(function(file){
			// プレビュー画像の枠の中の要素を一旦削除
			thumb_outer.children().remove();
			// ウィジェットフォームでのプレビュー画像を設定
			// thumb_outer.append(jQuery('<img class="admin-custom-thumb">').attr('src',f.toJSON().url).attr('alt',f.toJSON().url));
			thumb_outer.append('<img class="admin-custom-thumb" src="'+ file.toJSON().url +'" alt="'+ file.toJSON().title +'" />');
			/*
			file.toJSON().id で id
			file.toJSON().title で titleが返せる
			*/
			// hiddeになってるinputタグのvalueも変更
			jQuery(thumb_id).val(file.toJSON().id);
			jQuery(thumb_url).val(file.toJSON().url);
			jQuery(thumb_alt).val(file.toJSON().title).change();
		});
    });
    u.open();
};
}

/*-------------------------------------------*/
/*  画像削除処理
/*-------------------------------------------*/
// 画像削除処理（ ボタンに直接 onclick="javascript:vk_widget_image_del(this);return false;" を記述している ）
if ( vk_widget_image_del == undefined ){
	var vk_widget_image_del = function(e){
		// プレビュー画像を表示するdiv
		var thumb_outer=jQuery(e).parent().children("._display");
		// 画像IDを保存するinputタグ
		var thumb_input=jQuery(e).parent().children("._form").children('._id')[0];
		// 画像URLを保存するinputタグ
		var thumb_input=jQuery(e).parent().children("._form").children('._url')[0];
		// プレビュー画像のimgタグを削除
		thumb_outer.children().remove();
		// w.attr("value","");
		jQuery(e).parent().children("._form").children('._id').attr("value","").change();
		jQuery(e).parent().children("._form").children('._url').attr("value","").change();
		jQuery(e).parent().children("._form").children('._alt').attr("value","");
	};
}
