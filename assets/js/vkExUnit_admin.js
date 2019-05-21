(function($) {
	$(function() {
		//	サブセクションタイトルがクリックされたらセクションに open class をつける
		$('.veu_metabox_section .veu_metabox_section_title').each(function(){
				jQuery(this).click(function() {
					if ( ! jQuery(this).parent().hasClass('open') ) {
						jQuery(this).parent().addClass('open');
					} else {
						jQuery(this).parent().removeClass('open');
					}
				});
		});

		// 全展開ボタン
		jQuery('.veu_metabox_all_section_toggle_btn_open').click(function() {
			// 開閉ボタンの親クラス処理
			jQuery(this).parent().removeClass('close');
			jQuery(this).parent().addClass('open');
			// 各セクションのouter
			jQuery('.veu_metabox_section').each(function(){
					jQuery(this).addClass('open');
			});
		});
		jQuery('.veu_metabox_all_section_toggle_btn_close').click(function() {
			// 開閉ボタンの親クラス処理
			jQuery(this).parent().removeClass('open');
			jQuery(this).parent().addClass('close');
			// 各セクションのouter
			jQuery('.veu_metabox_section').each(function(){
					jQuery(this).removeClass('open');
			});
		});
	}); // $(function() {
})(jQuery);


/// all.jsのも同じコードがあるので注意
;(function($,d){var a=false,b='',c='',f=function(){
if(a){a=false;c.show();b.removeClass('active');}else{a=true;c.hide();b.addClass('active');}
};$(d).ready(function(){b=$('#wp-admin-bar-veu_disable_admin_edit .ab-item').on('click',f);c=$('.veu_adminEdit');});})(jQuery,document);
