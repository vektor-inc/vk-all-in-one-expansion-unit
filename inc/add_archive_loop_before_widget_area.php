<?php
function veu_set_archive_loop_before_widget_area(){

    // 公開されている投稿タイプを呼び出し
    $postTypes = get_post_types( array( 'public' => true ) );
    // 固定ページはアーカイブないので削除
    unset( $postTypes['page'] );

    foreach ( $postTypes as $postType ) {

        // Get post type name
        /*-------------------------------------------*/
        $post_type_object = get_post_type_object( $postType );
        if ( $post_type_object ) {

            // Set post type name
            $postType_name = esc_html( $post_type_object->labels->name );

            // Set post type widget area
            register_sidebar(
                array(
                    'name'          => sprintf( __( 'Post type archive before loop (%s)', 'vk-all-in-one-expansion-unit' ), $postType_name ),
                    'id'            => $postType . '-archive-loop-before',
                    'description'   => '',
                    'before_widget' => '<aside class="widget %2$s" id="%1$s">',
                    'after_widget'  => '</aside>',
                    'before_title'  => '<h2 class="widget-title">',
                    'after_title'   => '</h2>',
                )
            );
        } // if( $post_type_object ){

    } // foreach ($postTypes as $postType) {

}
add_action( 'widgets_init', 'veu_set_archive_loop_before_widget_area' );

function veu_display_archive_loop_before_widget_area( $query ){

    $loop_action_point = veu_get_loop_before_widget_action_point();
    if ( veu_get_loop_before_widget_action_point() === 'loop_start' ) {
        // $loop_action_point を loop_start にする場合メインクエリ以外の場所に出ないように終了
        if ( ! $query->is_main_query() ) {
            return;
        }
    }


    if ( ! is_post_type_archive() && ! is_home() && ! is_front_page() ){
        return;
    }

    if ( is_home() ){
        $post_type = 'post';
    }

    global $wp_query;

    if ( ! empty( $wp_query->query_vars['post_type'] ) ){
        $post_type = $wp_query->query_vars['post_type'];
    }

    if ( empty ( $post_type ) ) {
        return;
    }

    if ( ! empty( $wp_query->posts ) ){
        // 2ページ目以外は非表示
        if ( get_query_var( 'paged', 0 ) !== 0 ){
            return;
        }
    }

    // ※ get_post_type() は該当記事がない場合に投稿タイプが取得できないため
    $widget_area = $post_type . '-archive-loop-before';
    if ( is_active_sidebar( $widget_area ) ) {
        dynamic_sidebar( $widget_area );
    }

}

/**
 * ウィジェットエリアを追加するアクションフックポイント
 */
function veu_get_loop_before_widget_action_point(){
    $template = wp_get_theme()->Template;
    $loop_action_point = 'loop_start';
    if ( $template == 'katawara' ) {
        // Katawaraの場合
        $loop_action_point = 'katawara_loop_before';
    } elseif ( $template == 'lightning' || $template == 'lightning-pro' ) {
        // Lightningの場合
        $loop_action_point = 'lightning_loop_before';
    }
    return $loop_action_point;
}

function veu_loop_before_widget_run(){
    $loop_action_point = veu_get_loop_before_widget_action_point();
    add_action( $loop_action_point, 'veu_display_archive_loop_before_widget_area' );
}
add_action( 'after_setup_theme', 'veu_loop_before_widget_run' );

