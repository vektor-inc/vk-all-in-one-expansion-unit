
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
 		var position = target.offset().top;
 	}
	$('body,html').animate({scrollTop:position}, speed, 'swing');
	return false;
})
})(jQuery);
