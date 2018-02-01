<?php
/*-------------------------------------------*/
/*  著者情報非表示用のメタボックスを追加
/*-------------------------------------------*/
add_action( 'admin_menu', 'pad_add_custom_field_user_view_group' );

// add meta_box
function pad_add_custom_field_user_view_group() {

    $args = array(
       'public'   => true,
    );

    $output = 'names'; // names or objects, note names is the default
    $operator = 'and'; // 'and' or 'or'

    $post_types = pad_display_post_types();

    foreach ( $post_types  as $post_type ) {
       add_meta_box(
        'post_author_display',
        __( 'Post Author Display', 'post-author-display' ),
        'pad_content_meta_fields',
        $post_type,
        'side'
        );
    }
}

/*-------------------------------------------*/
/*  入力フィールドの生成
/*-------------------------------------------*/

function pad_content_meta_fields(){

    //CSRF対策の設定（フォームにhiddenフィールドとして追加するためのnonceを「'noncename__pad_hide」として設定）
    wp_nonce_field( wp_create_nonce(__FILE__), 'noncename__pad_hide' );

    global $post;
    $checked = ( get_post_meta( $post->ID,'pad_hide_post_author',true ) ) ? ' checked':'';
    $label = __('Don\'t display post author', 'post-author-display' );

    echo '<ul>';
    echo '<li><label>'.'<input type="checkbox" id="pad_hide_post_author" name="pad_hide_post_author" value="true"'.$checked.'> '.$label.'</label></li>';
    echo '</ul>';

}

/*-------------------------------------------*/
/*  入力された値の保存
/*-------------------------------------------*/
add_action('save_post', 'pad_save_hide_items');

function pad_save_hide_items($post_id){
    global $post;

    //設定したnonce を取得（CSRF対策）
    $noncename__pad_hide = isset($_POST['noncename__pad_hide']) ? $_POST['noncename__pad_hide'] : null;

    //nonce を確認し、値が書き換えられていれば、何もしない（CSRF対策）
    if(!wp_verify_nonce($noncename__pad_hide, wp_create_nonce(__FILE__))) {
        return $post_id;
    }

    //自動保存ルーチンかどうかチェック。そうだった場合は何もしない（記事の自動保存処理として呼び出された場合の対策）
    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return $post_id; }

    $field = 'pad_hide_post_author';
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

}

/*-------------------------------------------*/
/*  非表示を実行
/*-------------------------------------------*/
