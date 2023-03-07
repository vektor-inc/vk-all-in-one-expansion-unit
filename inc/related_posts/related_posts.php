<?php
/**
 * Related Posts
 *
 * @package vektor-inc/vk-all-in-pne-expansion-unit
 */

/*
非推奨タグ / Deprecated Tag
出力先
veu_get_related_posts()
veu_add_related_posts_item_html()
veu_add_related_posts_html()
Customizer
*/

/**********************************************
 * 非推奨タグ / Deprecated Tag
 */

/*
キャメルケースは非推奨なので関数名を変更したが、
プラグイン外で関数が使用されているかもしれないので念の為旧関数でも動作するように
※ いずれ完全に廃止するので、キャメルケースの関数は外部で使用しないでください。
*/
function vkExUnit_add_relatedPosts_item_html( $post ) {
	veu_add_related_posts_item_html( $post );
}
function vkExUnit_add_related_loopend( $query ) {
	veu_add_related_loopend( $query );
}
function vkExUnit_add_relatedPosts_html( $content ) {
	veu_add_related_posts_html( $content );
}
function vkExUnit_get_relatedPosts( $post_type = 'post', $taxonomy = 'post_tag', $max_show_posts = 10 ) {
	veu_get_related_posts( $post_type, $taxonomy, $max_show_posts );
}

/**********************************************
 * 出力先
 */

/*
loop_end でも出力出来るように一時期していたが、
コンテンツエリアのタグより外に出力されるなどで、
レイアウトの不具合が発生するので実質的には content にしかならないようになっている。
 */
if ( veu_content_filter_state() == 'content' ) {
	add_filter( 'the_content', 'veu_add_related_posts_html', 800, 1 );
} else {
	add_action( 'loop_end', 'veu_add_related_loopend', 800, 1 );
}

function veu_add_related_loopend( $query ) {
	if ( ! $query->is_main_query() ) {
		return;
	}
	echo veu_add_related_posts_html( '' );
}

/**
 * 関連記事の投稿データを取得
 * veu_get_related_posts()
 *
 * @param string  $post_type : 投稿タイプ.
 * @param string  $taxonomy : 分類.
 * @param integer $max_show_posts : 表示件数.
 * @return array  $related_posts : 該当の投稿リスト
 */
function veu_get_related_posts( $post_type = 'post', $taxonomy = 'post_tag', $max_show_posts = 10 ) {
	$posts_array = '';
	$post_id     = get_the_id();

	$terms = get_the_terms( $post_id, $taxonomy );

	if ( ! $terms || ! is_array( $terms ) ) {
		return $posts_array; }
	$tags = array();
	foreach ( $terms as $t ) {
		$tags[] = $t->term_id; }

	$args_base = array(
		'posts_per_page'   => $max_show_posts,
		'offset'           => 0,
		'orderby'          => 'date',
		'order'            => 'DESC',
		'post__not_in'     => array( $post_id ),
		'post_type'        => $post_type,
		'post_status'      => 'publish',
		'suppress_filters' => true,
	);

	$args = $args_base;

	$args['tax_query'] = array(
		array(
			'taxonomy'         => $taxonomy,
			'field'            => 'id',
			'terms'            => $tags,
			'include_children' => false,
			'operator'         => 'AND',
		),
	);

	$posts_array = get_posts( $args );

	if ( ! is_array( $posts_array ) ) {
		$posts_array = array(); }

	$post_shortage = $max_show_posts - count( $posts_array );
	if ( $post_shortage > 0 ) {
		$args                   = $args_base;
		$args['posts_per_page'] = $post_shortage;
		foreach ( $posts_array as $post ) {
			$args['post__not_in'][] = $post->ID;
		}
		$args['tax_query'] = array(
			array(
				'taxonomy'         => $taxonomy,
				'field'            => 'id',
				'terms'            => $tags,
				'include_children' => false,
				'operator'         => 'IN',
			),
		);
		$singletags        = get_posts( $args );
		if ( is_array( $singletags ) && count( $singletags ) ) {
			$posts_array = array_merge( $posts_array, $singletags ); }
	}

	$related_posts = $posts_array;
	return $related_posts;
}

/**
 * 関連記事の1件分のHTML
 *
 * @param object $post : post object.
 * @return string : single rerated post item html
 */
function veu_add_related_posts_item_html( $post ) {
	$post_item_html  = '<div class="col-sm-6 relatedPosts_item">';
	$post_item_html .= '<div class="media">';
	if ( has_post_thumbnail( $post->ID ) ) :
		$post_item_html .= '<div class="media-left postList_thumbnail">';
		$post_item_html .= '<a href="' . get_the_permalink( $post->ID ) . '">';
		$post_item_html .= get_the_post_thumbnail( $post->ID, 'thumbnail' );
		$post_item_html .= '</a>';
		$post_item_html .= '</div>';
	endif;
	$post_item_html .= '<div class="media-body">';
	$post_item_html .= '<div class="media-heading"><a href="' . get_the_permalink( $post->ID ) . '">' . $post->post_title . '</a></div>';
	$post_item_html .= '<div class="media-date published"><i class="fa fa-calendar"></i>&nbsp;' . get_the_date( '', $post->ID ) . '</div>';
	$post_item_html .= '</div>';
	$post_item_html .= '</div>';
	$post_item_html .= '</div>' . "\n";
	$post_item_html  = apply_filters( 'veu_related_post_item', $post_item_html );
	return $post_item_html;
}

/**
 * 関連記事のHTMLが追加された $content を取得
 *
 * @param string $content : post contents.
 * @return string $content added related post html
 */
function veu_add_related_posts_html( $content ) {

	if ( ! is_single() ) {
		return $content;
	}

	global $is_pagewidget;
	if ( $is_pagewidget ) {
		return $content;
	}

	$related_post_types = apply_filters( 'veu_related_post_types', array( 'post' ) );
	if ( ! in_array( get_post_type(), $related_post_types ) ) {
		return $content;
	}

	$content .= veu_get_related_posts_html();

	return $content;
}

/**
 * Get Related Posts HTML
 *
 * @since 9.37.0.0
 * @return string : $related_posts_html
 */
function veu_get_related_posts_html() {

	$output = get_option( 'vkExUnit_related_options' );

	if ( ! empty( $output['related_display_count'] ) ) {
		$count = vk_sanitize_number( $output['related_display_count'] );
	} else {
		$count = 10;
	}

	$related_post_args = apply_filters(
		'veu_related_post_args',
		array(
			'post_type'      => 'post',
			'taxonomy'       => 'post_tag',
			'max_show_posts' => $count,
		)
	);
	$related_posts     = veu_get_related_posts( $related_post_args['post_type'], $related_post_args['taxonomy'], $related_post_args['max_show_posts'] );

	if ( ! $related_posts ) {
		return;
	}

	// $posts_count = mb_convert_kana($relatedPostCount, "a", "UTF-8");
	if ( $related_posts ) {
		$related_posts_html  = '<!-- [ .relatedPosts ] -->';
		$related_posts_html .= '<aside class="veu_relatedPosts veu_contentAddSection">';

		$output = get_option( 'vkExUnit_related_options' );

		// テキストフィールドに値が入っていたら、表示させる.
		if ( ! empty( $output['related_title'] ) ) {
			$related_post_title = $output['related_title'];
		} else {
			// 何も入っていなかったら既存のタイトルを表示させる.
			$related_post_title = __( 'Related posts', 'vk-all-in-one-expansion-unit' );
		}
		// 書き換え用フィルターフック（カスタマイザーで変更出来るが、既存ユーザーで使用しているかもしれないため削除不可）.
		$related_post_title  = apply_filters( 'veu_related_post_title', $related_post_title );
		$related_posts_html .= '<h1 class="mainSection-title relatedPosts_title">' . $related_post_title . '</h1>';

		$i                   = 1;
		$related_posts_html .= '<div class="row">';
		foreach ( $related_posts as $key => $post ) {
			$related_posts_html .= veu_add_related_posts_item_html( $post );
			$i++;
		} // foreach
		$related_posts_html .= '</div>';
		$related_posts_html .= '</aside><!-- [ /.relatedPosts ] -->';

	}

	wp_reset_postdata();
	wp_reset_query();

	return $related_posts_html;
}

/**********************************************
 * Customizer
 */
if ( apply_filters( 'veu_customize_panel_activation', false ) ) {
	add_action( 'customize_register', 'veu_customize_register_related' );
}

function veu_customize_register_related( $wp_customize ) {
	// セクション追加.
	$wp_customize->add_section(
		'veu_related_setting',
		array(
			'title'    => __( 'Related Settings', 'vk-all-in-one-expansion-unit' ),
			'priority' => 1000,
			'panel'    => 'veu_setting',
		)
	);
	// セッティング.
	$wp_customize->add_setting(
		'vkExUnit_related_options[related_title]',
		array(
			'default'           => '',
			'type'              => 'option', // 保存先 option or theme_mod.
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	// コントロール.
	$wp_customize->add_control(
		'related_title',
		array(
			'label'    => __( 'Title:', 'vk-all-in-one-expansion-unit' ),
			'section'  => 'veu_related_setting',
			'settings' => 'vkExUnit_related_options[related_title]',
			'type'     => 'text',
			'priority' => 1,
		)
	);

	// セッティング _ 表示件数.
	$wp_customize->add_setting(
		'vkExUnit_related_options[related_display_count]',
		array(
			'default'           => '',
			'type'              => 'option', // 保存先 option or theme_mod.
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'vk_sanitize_number',
		)
	);
	// コントロール _ 表示件数.
	$wp_customize->add_control(
		'related_display_count',
		array(
			'label'    => __( 'Display count', 'vk-all-in-one-expansion-unit' ),
			'section'  => 'veu_related_setting',
			'settings' => 'vkExUnit_related_options[related_display_count]',
			'type'     => 'text',
			'priority' => 1,
		)
	);

	/*
	  Add Edit Customize Link Btn
	/*-------------------------------------------*/
	$wp_customize->selective_refresh->add_partial(
		'vkExUnit_related_options[related_title]',
		array(
			'selector'        => '.veu_relatedPosts',
			'render_callback' => '',
			'supports'        => array(),
		)
	);
}

/******************************
hook sample

add_filter('veu_related_post_types', 'veu_related_post_types_custom');
function veu_related_post_types_custom( $related_post_types ){
	$related_post_types[] = 'item';
	return $related_post_types;
}

add_filter('veu_related_post_args', 'veu_related_post_args_custom');
function veu_related_post_args_custom( $related_post_args ){
	if ( get_post_type() == 'item' ) {
		$related_post_args = array(
			'post_type' => 'item',
			'taxonomy' => 'item-category',
			'max_show_posts' => 10
		);
	}
	return $related_post_args;
}
*/
