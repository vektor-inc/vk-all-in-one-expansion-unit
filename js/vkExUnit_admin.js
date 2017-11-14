/// all.jsのも同じコードがあるので注意
;(function($,d){var a=false,b='',c='',f=function(){
if(a){a=false;c.show();b.removeClass('active');}else{a=true;c.hide();b.addClass('active');}
};$(d).ready(function(){b=$('#wp-admin-bar-veu_disable_admin_edit .ab-item').on('click',f);c=$('.veu_adminEdit');});})(jQuery,document);
