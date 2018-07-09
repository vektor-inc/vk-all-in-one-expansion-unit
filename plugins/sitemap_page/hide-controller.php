<?php

/*-------------------------------------------*/
/*  サイトマップ非表示用のメタボックスを追加
/*-------------------------------------------*/

/** アクションフックを使用して作成した関数を登録 */
add_action( 'admin_menu', 'vue_sitemap_hide_meta_box' );

/** メニュー作成用のコードを含む関数を作成 */
function vue_sitemap_hide_meta_box() {

  // 現在存在する固定ページを取得
  /*-------------------------------------------*/
     add_meta_box(
      'sitemap-meta-box', // metaboxのID
      veu_get_little_short_name().' '. __( 'Site Map Hide', 'vkExUnit' ), // metaboxの表示名
      'vue_sitemap_hide_controller_setting', // このメタボックスに表示する中身の関数名
      'page', // このメタボックスをどの投稿タイプで表示するのか？
      'side' // 表示する位置
      );
}






/*-------------------------------------------*/
/*  入力フィールドの生成
/*-------------------------------------------*/


function vue_sitemap_hide_controller_setting() {

  //CSRF対策の設定（フォームにhiddenフィールドとして追加するためのnonceを「'noncename__sitemap_hide」として設定）
  wp_nonce_field( wp_create_nonce(__FILE__), 'noncename__sitemap_hide' );

  global $post;
  // カスタムフィールド 'sitemap_hide' の値を取得
  $sitemap_hide = array();
  $sitemap_hide = get_post_meta( $post->ID,'sitemap_hide',true );

  // チェックが入っている場合（ 表示しない ）
  if ( $sitemap_hide ) {
    $checked = ' checked';
  } else {
    $checked = '';
  }

  $label = __('Don\'t display on Sitemap.', 'vkExUnit' );
  echo '<ul>';
  echo '<li><label>'.'<input type="checkbox" id="sitemap_hide" name="sitemap_hide" value="true"'.$checked.'> '.$label.'</label></li>';
  echo '</ul>';


}


/*-------------------------------------------*/
/*  入力された値の保存
/*-------------------------------------------*/
add_action('save_post', 'vue_sitemap_hide_controller_save');

function vue_sitemap_hide_controller_save($post_id){
  global $post;
  //設定したnonce を取得（CSRF対策）
  $noncename__sitemap_hide = isset($_POST['noncename__sitemap_hide']) ? $_POST['noncename__sitemap_hide'] : null;
  //nonce を確認し、値が書き換えられていれば、何もしない（CSRF対策）
  if(!wp_verify_nonce($noncename__sitemap_hide, wp_create_nonce(__FILE__))) {
      return $post_id;
  }

  //自動保存ルーチンかどうかチェック。そうだった場合は何もしない（記事の自動保存処理として呼び出された場合の対策）
  if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return $post_id; }

  $field = 'sitemap_hide';
  $field_value = ( isset( $_POST[$field] ) ) ? $_POST[$field] : '';
  // データが空だったら入れる
  if( get_post_meta($post_id, $field ) == ""){
      add_post_meta($post_id, $field , $field_value, true);
  // 今入ってる値と違ってたらアップデートする
  } elseif( $field_value != get_post_meta( $post_id, $field , true)){
      update_post_meta($post_id, $field , $field_value);
  // 入力がなかったら消す
  } elseif( $field_value == "" ){
      delete_post_meta($post_id, $field , get_post_meta( $post_id, $field , true ));
  }



} // function vue_sitemap_hide_controller_save(){
