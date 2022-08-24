<?php
/**
 * CTA ブロックを追加
 */

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
                'cat_option' => $cta_options,
            )
        );
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
                    $content .= $cta_content;
                } else {
                    // 旧 CTA レイアウト.
                    $content .= include dirname( dirname( __FILE__ ) ) . '/view-actionbox.php';
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