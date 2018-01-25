/*-------------------------------------------*/
/*  Lightning Charm 1.2.0 での表示崩れ回避用
/*  Lightning Charm 1.4.0 以降になったら削除
/*-------------------------------------------*/
(function($) {
	// veu_postList のサムネイルがない場合にクラスを付与
	$veu_postList = $('.veu_postList');
	if ($veu_postList.length) {
		var $postList_item_veu = $('.veu_postList .postList > .postList_item'),
			cnt = 0;

		$postList_item_veu.each(function() {
			var $post_thumb = $(this).find('.postList_thumbnail');
			if (!($post_thumb.length)) {
				$(this).addClass('no_img');
			} else {
				cnt++;
				if ((cnt % 2) == 0) {
					$(this).addClass('even');
				}
			}
		});
	}
})(jQuery);

/*-------------------------------------------*/
/*  facebookLikeBox
/*-------------------------------------------*/
/*  jquery.flatheights.js
/*-------------------------------------------*/
/*  snsCount
/*-------------------------------------------*/

pagePluginReSize();
jQuery(window).resize(function() {
	pagePluginReSize();
});

/*-------------------------------------------*/
/*	facebookLikeBox
/*-------------------------------------------*/
function pagePluginReSize() {
	// jQuery('.fb_iframe_widget').each(function(){
	// 	var element = jQuery(this).parent().width();
	// 	console.log(element);
	// 	jQuery(this).attr('data-width',element);
	// 	jQuery(this).children('span:first').css({"width":element});
	// 	jQuery(this).children('span iframe.fb_ltr').css({"width":element});
	// });
}

! function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0],
		p = /^http:/.test(d.location) ? 'http' : 'https';
	if (!d.getElementById(id)) {
		js = d.createElement(s);
		js.id = id;
		js.src = p + '://platform.twitter.com/widgets.js';
		fjs.parentNode.insertBefore(js, fjs);
	}
}(document, 'script', 'twitter-wjs');

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

	window.twttr = (function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0],
			t = window.twttr || {};
		if (d.getElementById(id)) return t;
		js = d.createElement(s);
		js.id = id;
		js.src = "https://platform.twitter.com/widgets.js";
		fjs.parentNode.insertBefore(js, fjs);
		t._e = [];
		t.ready = function(f) {
			t._e.push(f);
		};
		return t;
	}(document, "script", "twitter-wjs"));

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