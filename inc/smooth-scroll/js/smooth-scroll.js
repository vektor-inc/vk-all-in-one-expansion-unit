
/*-------------------------------------------*/
/*  ページ内するするスクロール
/*-------------------------------------------*/
(function($) {
// #にダブルクォーテーションが必要
$('a[href^="#"]').click(function() {
	// ページ内リンク指定でない（アンカー指定がない）場合
	if ( jQuery(this).attr("href") === "#" ){
		return;
	}
	// bootstrapのタブコンポーネント
	if ( jQuery(this).attr("role") === "tab"  ){
		return;
	}
	if ( jQuery(this).attr("role") === "button"  ){
		return;
	}
	// bootstrapのアコーディオンコンポーネント
	if ( jQuery(this).attr("data-toggle") ){
		return;
	}
	// Lightningのスライダーの左右ボタン
	if ( jQuery(this).hasClass('carousel-control') ){
			return;
	}

	if ( id )
	 var speed = 400;
	 var href= $(this).attr("href");
	 var target = $(href == "#" || href == "" ? 'html' : href);
	 var id = jQuery(this).attr('id');
 	if ( id == 'page_top' ){
 		var position = 0;
 	} else {
		// ヘッダ-固定の時用オフセット
		var header_height = 0;
		if ( jQuery('body').hasClass('headfix') ){
			header_height = jQuery('body > header').outerHeight();
			// console.log(header_height);
		}
		// 管理バー分オフセット
		var admin_bar_height = 0;
		if ( jQuery('body').hasClass('admin-bar') ){
			admin_bar_height = jQuery('#wpadminbar').outerHeight();
		}

 		var position = target.offset().top - header_height - admin_bar_height;
 	}
	$('body,html').animate({scrollTop:position}, speed, 'swing');
	return;
})
})(jQuery);
