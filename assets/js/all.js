/*
======================================================================
	jquery.flatheights.js
	Version: 2010-09-15
	http://www.akatsukinishisu.net/itazuragaki/js/i20070801.html
======================================================================
/*-------------------------------------------*/
/*	$.changeLetterSize.addHandler(func)
/*	文字の大きさが変化した時に実行する処理を追加
/*-------------------------------------------*/
jQuery.changeLetterSize = {
	handlers : [],
	interval : 1000,
	currentSize: 0
};

(function($) {

	var self = $.changeLetterSize;

	/* 文字の大きさを確認するためのins要素 */
	var ins = $('<ins>M</ins>').css({
		display: 'block',
		visibility: 'hidden',
		position: 'absolute',
		padding: '0',
		top: '0'
	});

	/* 文字の大きさが変わったか */
	var isChanged = function() {
		ins.appendTo('body');
		var size = ins[0].offsetHeight;
		ins.remove();
		if (self.currentSize == size) return false;
		self.currentSize = size;
		return true;
	};

	/* 文書を読み込んだ時点で
	   文字の大きさを確認しておく */
	$(isChanged);

	/* 文字の大きさが変わっていたら、
	   handlers中の関数を順に実行 */
	var observer = function() {
		if (!isChanged()) return;
		$.each(self.handlers, function(i, handler) {
			handler();
		});
	};

	/* ハンドラを登録し、
	   最初の登録であれば、定期処理を開始 */
	self.addHandler = function(func) {
		self.handlers.push(func);
		if (self.handlers.length == 1) {
			// 文字サイズが変わった時に定期的に実行されてしまうが、高さなんてそう細かく直さなくていいから停止
			// setInterval(observer, self.interval);
		}
	};

})(jQuery);


/*-------------------------------------------*/
/*	$(expr).flatHeights()
/*	$(expr)で選択した複数の要素について、それぞれ高さを
/*	一番高いものに揃える
/*-------------------------------------------*/

(function($) {

	/* 対象となる要素群の集合 */
	var sets = [];

	/* 高さ揃えの処理本体 */
	var flatHeights = function(set) {
		var maxHeight = 0;
		set.each(function(){
			var height = this.offsetHeight;
			if (height > maxHeight) maxHeight = height;
		});
		set.css('height', maxHeight + 'px');
	};

	/* 要素群の高さを揃え、setsに追加 */
	jQuery.fn.flatHeights = function() {
		if (this.length > 1) {
			flatHeights(this);
			sets.push(this);
		}
		return this;
	};

	/* 高さ揃えを再実行する処理 */
	var reflatting = function() {
		$.each(sets, function() {
			this.height('auto');
			flatHeights(this);
		});
	};

	/* 文字の大きさが変わった時に高さ揃えを再実行 */
	$.changeLetterSize.addHandler(reflatting);

	/* ウィンドウの大きさが変わった時に高さ揃えを再実行 */
	$(window).resize(reflatting);

})(jQuery);

jQuery(document).ready(function($){
    // .topPrTitには高さのpaddingを入れる事もあるので a に対して指定
    jQuery('.topPrTit a').flatHeights();
    jQuery('.topPrDescription').flatHeights();
    // jQuery('.child_page_block').flatHeights();
    // jQuery('.child_page_block p').flatHeights();
	jQuery('.child_page_block h4 a').flatHeights();
});
/*-------------------------------------------*/
/*  jquery.flatheights.js
/*-------------------------------------------*/
/*  snsCount
/*-------------------------------------------*/

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
	// var pocket = {
	// 	init: function() {
	// 		$.ajax({
	// 			url: vkExOpt.ajax_url,
	// 			type: 'POST',
	// 			data: {
	// 				'action': 'vkex_pocket_tunnel',
	// 				'linkurl': linkurl
	// 			},
	// 			dataType: 'html',
	// 			success: function(response) {
	// 				var count = $(response).find("#cnt").html();
	// 				if (count === undefined) return;
	// 				socials.find('.veu_count_sns_pocket').html(count);
	// 			}
	// 		})
	// 	}
	// }
	facebook.init();
	hatena.init();
	// pocket.init();
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

/*----------------------------------------------------------*/
/*	scroll
/*----------------------------------------------------------*/
// Scroll function
(function($) {
$(window).scroll(function() {
	var scroll = $(this).scrollTop();
	if ($(this).scrollTop() > 1) {
		$('body').addClass('scrolled');
	} else {
		$('body').removeClass('scrolled');
	}
});
})(jQuery);
