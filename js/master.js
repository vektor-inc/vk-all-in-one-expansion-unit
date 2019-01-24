/*-------------------------------------------*/
/*  ページ内するするスクロール
/*-------------------------------------------*/
/*  jquery.flatheights.js
/*-------------------------------------------*/
/*  snsCount
/*-------------------------------------------*/

/*-------------------------------------------*/
/*  ページ内するするスクロール
/*-------------------------------------------*/
(function($) {
// #にダブルクォーテーションが必要
$('a[href^="#"]').click(function() {
	if ( jQuery(this).attr("href") === "#" ){
		return;
	}
	if ( jQuery(this).attr("role") === "tab"  ){
		return;
	}
	if ( jQuery(this).attr("data-toggle") ){
		return;
	}

	// .carousel-control を除外しないとLightningのスライダーの左右ボタンでページトップになってしまう。
	if ( ! $(this).hasClass('carousel-control') ){
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
	}
})
})(jQuery);


/*-------------------------------------------*/
/*	jquery.flatheights.js
/*-------------------------------------------*/
(function($) {
	$(function() {
		$('.prArea > .subSection-title').flatHeights();
		$('.prArea > .summary').flatHeights();
		// $('.childPage_list_title').flatHeights();
	});
	// window.onload は複数使うと一つしか動作しなくなるので使用しない
	window.addEventListener('DOMContentLoaded', function() {
		$('.childPage_list_text').flatHeights();
		// $('.childPage_list_box').flatHeights();
	})
})(jQuery);

/*-------------------------------------------*/
/*	snsCount
/*-------------------------------------------*/
(function($) {
	var socials = $('.veu_socialSet');
	if (typeof socials[0] === "undefined") return;
	var linkurl = encodeURIComponent((typeof vkExOpt !== "undefined" && vkExOpt.sns_linkurl) || location.href);
	var facebook = {
		init: function() {
			var url = 'https://graph.facebook.com/?id=' + linkurl;
			$.ajax({
				url: url,
				dataType: 'jsonp',
				success: function(response) {
					if (!response.share || response.share.share_count === undefined) return;
					socials.find('.veu_count_sns_fb').html(response.share.share_count);
				}
			});
		}
	}

	var hatena = {
		init: function() {
			var url = (location.protocol === 'https:' ? 'https://b.hatena.ne.jp' : 'http://api.b.st-hatena.com') +
				'/entry.count?url=' + linkurl;
			$.ajax({
				url: url,
				dataType: 'jsonp',
				success: function(response) {
					var count = response ? response : 0;
					socials.find('.veu_count_sns_hb').html(count);

					if (typeof(count) == 'undefined') {
						count = 0;
					}
				}
			});
		}
	}
	var pocket = {
		init: function() {
			$.ajax({
				url: vkExOpt.ajax_url,
				type: 'POST',
				data: {
					'action': 'vkex_pocket_tunnel',
					'linkurl': linkurl
				},
				dataType: 'html',
				success: function(response) {
					var count = $(response).find("#cnt").html();
					if (count === undefined) return;
					socials.find('.veu_count_sns_pocket').html(count);
				}
			})
		}
	}
	facebook.init();
	hatena.init();
	pocket.init();
})(jQuery);


/// master.jsのも同じコードがあるので注意
;
(function($, d) {
	var a = false,
		b = '',
		c = '',
		f = function() {
			if (a) {
				a = false;
				c.show();
				b.removeClass('active');
			} else {
				a = true;
				c.hide();
				b.addClass('active');
			}
		};
	$(d).ready(function() {
		b = $('#wp-admin-bar-veu_disable_admin_edit .ab-item').on('click', f);
		c = $('.veu_adminEdit');
	});
})(jQuery, document);
