// カラーピッカーjs

(function( $ ) {
 
    //カラーピッカーを導入するためのクラスを指定します
    $(function() {
		$('.color_picker').wpColorPicker();

		// ウィジェット画面でもクリアボタン出す		
		if( $('.wp-picker-clear').hasClass('hidden') ){
			$('.wp-picker-clear').removeClass('hidden');
		}
		
	}); 
})( jQuery );
