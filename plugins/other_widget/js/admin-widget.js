// 背景画像登録処理
if ( vk_widget_image_add == undefined ){
	var vk_widget_image_add = function(e){
		// プレビュー画像を表示するdiv
    var thumb_outer=jQuery(e).parent().children("._display");
		// 画像IDを保存するinputタグ
    var thumb_input=jQuery(e).parent().children("._form").children('.__id')[0];
    var u=wp.media({library:{type:'image'},multiple:false}).on('select', function(e){
				u.state().get('selection').each(function(f){
					// プレビュー画像の枠の中の要素を一旦削除
					thumb_outer.children().remove();
					// ウィジェットフォームでのプレビュー画像を設定
					thumb_outer.append(jQuery('<img style="width:100%;mheight:auto">').attr('src',f.toJSON().url));
					// hiddeになってるinputタグのvalueも変更
					jQuery(thumb_input).val(f.toJSON().url).change();
				});
    });
    u.open();
};
}

// 背景画像削除処理
if ( vk_widget_image_del == undefined ){
	var vk_widget_image_del = function(e){
		// プレビュー画像を表示するdiv
		var thumb_outer=jQuery(e).parent().children("._display");
		// 画像IDを保存するinputタグ
		var thumb_input=jQuery(e).parent().children("._form").children('.__id')[0];
		// プレビュー画像のimgタグを削除
		thumb_outer.children().remove();
		// w.attr("value","");
		jQuery(e).parent().children("._form").children('.__id').attr("value","").change();
	};
}
