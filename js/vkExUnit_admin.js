
/*-------------------------------------------*/
/* メディアアップローダー
/*-------------------------------------------*/
jQuery(document).ready(function($){
    var custom_uploader;
// var media_id = new Array(2);　//配列の宣言
// media_id[0] = "head_logo";
// media_id[1] = "foot_logo";

//for (i = 0; i < media_id.length; i++) {　//iという変数に0をいれループ一回ごとに加算する

        // var media_btn = '#media_' + media_id[i];
        // var media_target = '#' + media_id[i];
        jQuery('.media_btn').click(function(e) {
            media_target = jQuery(this).attr('id').replace(/media_/g,'#');
            e.preventDefault();
            if (custom_uploader) {
                custom_uploader.open();
                return;
            }
            custom_uploader = wp.media({
                title: 'Choose Image',
                // 以下のコメントアウトを解除すると画像のみに限定される。 → されないみたい
                library: {
                    type: 'image'
                },
                button: {
                    text: 'Choose Image'
                },
                multiple: false, // falseにすると画像を1つしか選択できなくなる
            });
            custom_uploader.on('select', function() {
                var images = custom_uploader.state().get('selection');
                images.each(function(file){
                    //$('#head_logo').append('<img src="'+file.toJSON().url+'" />');
                    jQuery(media_target).attr('value', file.toJSON().url );
                });
            });
            custom_uploader.open();
        });
//}

});

/*-------------------------------------------*/
/* ページ内の表示／非表示切り替えセクションの追加
/*-------------------------------------------*/
jQuery(document).ready(function($){
    jQuery('.showHideSection .showHideBtn').on("click", function() {
            jQuery(this).next().slideToggle();
     });
});