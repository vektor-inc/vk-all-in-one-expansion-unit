
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

/*-------------------------------------------*/
/* スクロール時の位置固定
/*-------------------------------------------*/
jQuery(document).ready(function(){
    var contentHeight = jQuery('.adminMain').height();
    setNav();
    // スクロールしたら
    jQuery(window).scroll(function () {
        var scroll = jQuery(this).scrollTop();
        if ( scroll < contentHeight ){ // これがないと延々とスクロールする
            setNav();
        }
    });
    function setNav(){
        // スクロールの量を取得
        var scroll = jQuery(this).scrollTop();
        jQuery('#adminContent_sub').css({"padding-top":scroll});
        jQuery('.adminSub').css({"padding-top":scroll});
    }
});
/*-------------------------------------------*/
/* ページ内リンクで頭出しの余白を適切にする
/*-------------------------------------------*/
jQuery(document).ready(function(){
    // 一つ目のセクションの位置を取得
    var default_offset = jQuery('.adminMain section:first-child').offset();

    // 全てのセクションの上に余白を追加（頭出しがきれいになるようにするため）
    jQuery('.adminMain section').each(function(i){
        if (i != 0){ // 読み込んだ時、一つ目は余白要らない
            jQuery(this).css({"padding-top":default_offset["top"]});
        }
    });

    jQuery(window).scroll(function () {
        // スクロール量を取得
        var scroll = jQuery(this).scrollTop();

        if ( scroll < default_offset["top"] ){
            // スクロールが少ない場合は最初のセクションに余白を入れない
            jQuery('.adminMain section:first-child').css({"padding-top":0});
        } else {
            // ある程度スクロールしている状態ならば余白を入れる
            jQuery('.adminMain section:first-child').css({"padding-top":default_offset["top"]});
        }
    });
});
