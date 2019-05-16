(function($) {
	$(function() {
		$('.veu_metabox_section .veu_metabox_section_title').each(function(){
				jQuery(this).click(function() {
					if ( ! jQuery(this).next().hasClass('veu_metabox_section_body-open') ) {
						jQuery(this).next().addClass('veu_metabox_section_body-open');
					} else {
						jQuery(this).next().removeClass('veu_metabox_section_body-open');
					}
				});
		});
		jQuery('.veu_metabox_section_toggle_open').click(function() {
			jQuery('.veu_metabox_section_body').each(function(){
					jQuery(this).addClass('veu_metabox_section_body-open');
			});
		});
		jQuery('.veu_metabox_section_toggle_close').click(function() {
			jQuery('.veu_metabox_section_body').each(function(){
					jQuery(this).removeClass('veu_metabox_section_body-open');
			});
		});
	}); // $(function() {
})(jQuery);


/// all.jsのも同じコードがあるので注意
;(function($,d){var a=false,b='',c='',f=function(){
if(a){a=false;c.show();b.removeClass('active');}else{a=true;c.hide();b.addClass('active');}
};$(d).ready(function(){b=$('#wp-admin-bar-veu_disable_admin_edit .ab-item').on('click',f);c=$('.veu_adminEdit');});})(jQuery,document);
