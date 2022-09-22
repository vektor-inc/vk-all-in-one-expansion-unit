<?php
/**
 * CTA ブロックを追加
 */


 // フィルターフックを追加
 // フルサイト編集意では本文欄を経由しないため、CTAのコンテンツに対して WordPress が通常の投稿に行っているものと同じ処理をする
 // Add fiter for render post content( Cope with FSE )
add_filter( 'veu_cta_content', 'do_blocks', 9 );
add_filter( 'veu_cta_content', 'wptexturize' );
add_filter( 'veu_cta_content', 'convert_smilies', 20 );
add_filter( 'veu_cta_content', 'shortcode_unautop' );
add_filter( 'veu_cta_content', 'prepend_attachment' );
add_filter( 'veu_cta_content', 'wp_filter_content_tags' );
add_filter( 'veu_cta_content', 'do_shortcode', 11 );
add_filter( 'veu_cta_content', 'capital_P_dangit', 11 );

 /**
  * CTA ブロックを追加
  */
function veu_cta_block_setup() {
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type(
			'vk-blocks/cta',
			array(
				'attributes'      => array_merge(
					array(
						'className' => array(
							'type'    => 'string',
							'default' => '',
						),
						'postId'    => array(
							'type'    => 'string',
							'default' => '',
						),
					),
					veu_common_attributes()
				),
				'editor_script'   => 'veu-block',
				'editor_style'    => 'veu-block-editor',
				'render_callback' => 'veu_cta_block_callback',
				'supports'        => array(),
			)
		);

        // CTA のリストを取得
        $args  = array(
            'post_type'  => 'cta',
            'nopaging'   => true,
            'post_count' => -1,
        );
        $cta_posts   = get_posts( $args );


        // CTA の選択肢の配列を作成
        $cta_options = array();

        foreach ( $cta_posts as $cta_post ) {
            $cta_options[] = array(
                'value' => $cta_post->ID,
                'label' => $cta_post->post_title,
            );
        }

        // ランダムを先頭に追加
        array_unshift(
            $cta_options,
            array(
                'value'   => 'random',
                'label' => __( 'Random', 'vk-all-in-one-expansion-unit' ),
            )
        );

        // なんとなく「選択してください」を戦闘に追加
        array_unshift(
            $cta_options,
            array(
                'value'   => '',
                'label' => __( 'Please Select', 'vk-all-in-one-expansion-unit' ),
            )
        );

        // CTA のリストをブロック側に送信
        wp_localize_script(
            'veu-block',
            'veuBlockOption',
            array(
                'cat_option'  => $cta_options,
            )
        );

        // CTA のカスタムフィールドをブロックエディタで読めるように
        $args = array(
            'public'   => true,
        );
        $post_types =  get_post_types( $args, 'names' );

        foreach ( $post_types  as $key => $post_type ) {
            register_post_meta(
                $post_type,
                'vkexunit_cta_each_option',
                [
                    'show_in_rest' => true,
                    'single'       => true,
                    'type'         => 'string',
                ]
            );
        }
	}
}
add_action( 'init', 'veu_cta_block_setup', 15 );

function veu_cta_block_callback( $attributes, $content ) {
    $attributes = wp_parse_args(
		$attributes,
		array(
            'postId'    => '',
			'className' => '',
		)
	);

    $content = '';
    
    global $post;
    $post_config = get_post_meta( $post->ID, 'vkexunit_cta_each_option', true );
    // 各記事で非表示指定されていなかったら表示する
    if ( 'disable' !== $post_config ) {
        if ( ! empty( $attributes['postId'] ) ) {
            $post_id = 'random' !== $attributes['postId'] ? $attributes['postId'] : Vk_Call_To_Action::cta_id_random();
            $id      = $post_id;
            
			$cta_post = get_post( $post_id );

            if ( ! empty( $cta_post ) ) {

                $content .= '<div class="veu-cta-block ' . $attributes['className'] . '">';

                // 本文に入力がある場合は本文を表示.
                $cta_content = $cta_post->post_content;
                if ( ! empty ( $cta_content ) && 'veu_cta_normal' !== $cta_post->vkExUnit_cta_use_type ) {

                    $content .= apply_filters( 'veu_cta_content', $cta_content );

                } else {

                    $fa = '';

                    if ( class_exists( 'Vk_Font_Awesome_Versions' ) ) {
                        $fa = Vk_Font_Awesome_Versions::print_fa();
                    }

                    $btn_text   = get_post_meta( $id, 'vkExUnit_cta_button_text', true );
                    $btn_before = get_post_meta( $id, 'vkExUnit_cta_button_icon_before', true );

                    if ( $btn_before ) {
                        $btn_before = '<i class="' . $fa . esc_attr( $btn_before ) . ' font_icon"></i> ';
                    }

                    $btn_after = get_post_meta( $id, 'vkExUnit_cta_button_icon_after', true );

                    if ( $btn_after ) {
                        $btn_after = ' <i class="' . $fa . esc_attr( $btn_after ) . ' font_icon"></i>';
                    }

                    $url   = get_post_meta( $id, 'vkExUnit_cta_url', true );
                    $text  = get_post_meta( $id, 'vkExUnit_cta_text', true );
                    $text  = preg_replace( '/\n/', '<br/>', $text );
                    $imgid = get_post_meta( $id, 'vkExUnit_cta_img', true );


                    $image_position = get_post_meta( $id, 'vkExUnit_cta_img_position', true );

                    if ( ! $image_position ) {
                        $image_position = 'right'; 
                    }

                    $content .= '<section class="veu_cta" id="veu_cta-' . $id . '">';
                    $content .= '<h1 class="cta_title">' . $cta_post->post_title . '</h1>';
                    $content .= '<div class="cta_body">';


                    // 別ウィンドウで開くかどうかのカスタムフィールドの値を取得 //////.
                    $target_blank = get_post_meta( $id, 'vkExUnit_cta_url_blank', true );

                    if ( 'window_self' !== $target_blank ) {
                        $target = ' target="_blank"';
                    } else {
                        $target = '';
                    }

                    if ( $imgid ) {
                        $content .= '<div class="cta_body_image cta_body_image_' . $image_position . '">';
                        $content .= ( $url ) ? '<a href="' . $url . '"' . $target . '>' : '';
                        $content .= wp_get_attachment_image( $imgid, 'large' );
                        $content .= ( $url ) ? '</a>' : '';
                        $content .= '</div>';
                    }

                    $content .= '<div class="cta_body_txt ' . ( ( $imgid ) ? 'image_exist' : 'image_no' ) . '">';
                    $content .= wp_kses_post( do_shortcode( $text ) );
                    $content .= '</div>';

                    if ( $url && $btn_text ) {
                        $content .= '<div class="cta_body_link">';
                        $content .= '<a href="' . $url . '" class="btn btn-primary btn-block btn-lg"' . $target . '>';
                        $content .= $btn_before . $btn_text . $btn_after;
                        $content .= '</a>';
                        $content .= '</div>';
                    }

                    $content .= '</div><!-- [ /.vkExUnit_cta_body ] -->';
                    $content .= '</section>';
                }

                $content .= '</div>';
                
                // Display Edit Button.
                $url = get_edit_post_link( $cta_post->ID );
                if ( $url ) {
                    $content .= '<div class="veu_adminEdit"><a href="' . $url . '" class="btn btn-default" target="_blank">' . __( 'Edit CTA', 'vk-all-in-one-expansion-unit' ) . '</a></div>';
                }

            }

		}
    }

    return $content;

}