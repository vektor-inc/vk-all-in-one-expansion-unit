<?php

/*
	Add setting page
-------------------------------------------*/
/*
	Options Init
-------------------------------------------*/
/*
	insert sitemap page
-------------------------------------------*/
/*
	admin _ meta box
-------------------------------------------*/


/*
	Add setting page
/*-------------------------------------------*/

require_once __DIR__ . '/class-veu-metabox-sitemap.php';
require_once __DIR__ . '/sitemap-page-admin-main-setting.php';
require_once __DIR__ . '/sitemap-page-helpers.php';



/*
	Options Init
/*-------------------------------------------*/
function vkExUnit_sitemap_options_init() {
	if ( false === veu_get_sitemap_options() ) {
		add_option( 'vkExUnit_sitemap_options', veu_get_sitemap_options_default() ); }
}
add_action( 'veu_package_init', 'vkExUnit_sitemap_options_init' );


/*
	insert sitemap page
/*-------------------------------------------*/
if ( veu_content_filter_state() == 'content' ) {
	add_filter( 'the_content', 'veu_show_sitemap', 7, 1 );
} else {
	add_action( 'loop_end', 'veu_sitemap_loopend' );
}

function veu_sitemap_loopend( $query ) {
	if ( ! $query->is_main_query() ) {
		return;
	}
	echo veu_show_sitemap( '' );
}

/**
 * 現在は veu_show_sitemap() に変更になっているのが、旧 show_sitemap で飛び出された時用
 *
 * @param  [type] $content [description]
 * @return [type]          [description]
 */
function show_sitemap( $content ) {
	veu_show_sitemap( $content );
}
/**
 * [veu_show_sitemap description]
 *
 * @since  7.0
 * @param  [type] $content [description]
 * @return [type]          [description]
 */
function veu_show_sitemap( $content ) {
	global $is_pagewidget;

	if ( $is_pagewidget ) {
		return $content; }

	// 404ページの内容を G3 ProUnit で指定の記事本文に書き換えた場合に表示されないように
	if ( is_404() ) {
		return $content; }

	// ↓ のコメントを見る限り child page index などで必要らしいが、見た限りなくても困らないように見える。
	// wp_reset_postdata(); // need under other section / ex:child page index

	// wp_reset_postdata(); があると、VK 投稿リストブロックで get_the_excerpt() でこの関数が走ってしまい、抜粋の次の author 情報がリセットされて、以降の表示要素の $post が狂ってしまうので一旦コメントアウト
	// もし wp_reset_postdata(); がない事により不具合が発生するようなら再検討
	global $post;
	if ( $post ) {
		$enable = get_post_meta( $post->ID, 'vkExUnit_sitemap', true );
		if ( $enable ) {
			return $content . "\n" . do_shortcode( '[vkExUnit_sitemap]' );
		}
	}
	return $content;
}

function vkExUnit_sitemap( $attr ) {

	$classes = '';
	if ( function_exists( 'veu_add_common_attributes_class' ) ) {
		if ( veu_add_common_attributes_class( $classes, $attr ) ) {
			$classes .= ' ' . veu_add_common_attributes_class( $classes, $attr );
		}
	}

	$attr = shortcode_atts(
		array(
			'exclude'   => '',
			'className' => '',
		),
		$attr
	);
	if ( ! empty( $attr['className'] ) ) {
		$classes .= ' ' . $attr['className'];
	}

	$sitemap_html = '<div class="row veu_sitemap' . esc_attr( $classes ) . '">' . PHP_EOL;

	/*
	Exclude Page ids by ExUnit Main Setting Page
	/*-------------------------------------------*/
	$options  = veu_get_sitemap_options();
	$excludes = '';
	if ( isset( $options['excludeId'] ) ) {
		$excludes = esc_attr( $options['excludeId'] );
		$excludes = str_replace( '，', ',', $excludes );
		$excludes = mb_convert_kana( $excludes, 'rn' );
	}

	/*
	Exclude Page ids by Page Edit meta box
	/*-------------------------------------------*/
	$veu_sitemap_exclude_page_ids = veu_sitemap_exclude_page_ids();
	if ( ! $excludes ) {
		$excludes .= $veu_sitemap_exclude_page_ids;
	} elseif ( $excludes && $veu_sitemap_exclude_page_ids ) {
		$excludes .= ',' . $veu_sitemap_exclude_page_ids;
	}

	/*
	pages
	/*-------------------------------------------*/
	$sitemap_html .= '<div class="col-md-6 sitemap-col">' . PHP_EOL;
	$sitemap_html .= '<ul class="link-list">' . PHP_EOL;
	$args          = array(
		'title_li'     => '',
		'echo'         => 0,
		'exclude_tree' => $excludes,
	);
	$sitemap_html .= wp_list_pages( $args );

	$sitemap_html .= '</ul>' . PHP_EOL; // <!-- [ /.link-list ] -->
	$sitemap_html .= '</div>' . PHP_EOL; // <!-- [ /.sitemap-col ] -->

	/*
	Posts & Custom posts
	/*-------------------------------------------*/
	$sitemap_html .= '<div class="col-md-6 sitemap-col">' . PHP_EOL;

	$page_for_posts = vk_get_page_for_posts();
	$all_post_types = get_post_types( array( 'public' => true ) );

	$p = get_posts(
		array(
			'post_type'   => 'post',
			'post_status' => 'publish',
		)
	);
	if ( empty( $p ) ) {
		unset( $all_post_types['post'] );
	}

	// 除外投稿タイプ処理
	$exclude_post_types = apply_filters( 'veu_sitemap_exclude_post_types', array( 'page', 'attachment', 'vk-managing-patterns' ) );
	foreach ( $exclude_post_types as $exclude_post_type ) {
		unset( $all_post_types[ $exclude_post_type ] );
	}

	// 除外投稿タイプ処理
	if ( isset( $options['excludePostTypes'] ) && is_array( $options['excludePostTypes'] ) ) {
		foreach ( $options['excludePostTypes'] as $key => $value ) {
			if ( $value ) {
				unset( $all_post_types[ $key ] );
			}
		}
	}

	foreach ( $all_post_types as $postType ) {
		$post_type_object = get_post_type_object( $postType );
		if ( $post_type_object ) {
			$sitemap_html .= '<div class="sitemap-' . esc_attr( $postType ) . '">' . PHP_EOL;
			$sitemap_html .= '<div class="sectionBox">' . PHP_EOL;

			/*
			Post type name
			/*-------------------------------------------*/
			if ( $postType == 'post' && $page_for_posts['post_top_use'] ) {
				$postTypeName   = $page_for_posts['post_top_name'];
				$postTypeTopUrl = get_the_permalink( $page_for_posts['post_top_id'] );
			} else {
				$postTypeName   = $post_type_object->labels->name;
				$postTypeTopUrl = get_post_type_archive_link( $postType );
			}
			$sitemap_html .= '<h4 class="sitemap-post-type-title sitemap-post-type-' . $postType . '"><a href="' . $postTypeTopUrl . '">' . esc_html( $postTypeName ) . '</a></h4>' . PHP_EOL;

			/*
			Taxonomy name
			/*-------------------------------------------*/
			// 投稿タイプに紐付いている taxonomy名だけ配列で取得
			$taxonomies = get_object_taxonomies( $postType );

			foreach ( $taxonomies as $taxonomy ) {
				// taxonomyの詳細情報を取得
				$taxonomy_object = get_taxonomy( $taxonomy );

				// 管理画面のUIに表示させているものだけに限定
				if ( $taxonomy_object->show_in_menu ) {
					$sitemap_html .= '<h5 class="sitemap-taxonomy-title sitemap-taxonomy-' . esc_attr( $taxonomy_object->name ) . '">' . wp_kses_post( $taxonomy_object->label ) . '</h5>' . PHP_EOL;

					/*
					Term
					/*-------------------------------------------*/

					$sitemap_html                     .= '<ul class="sitemap-term-list sitemap-taxonomy-' . esc_attr( $taxonomy_object->name ) . ' link-list">' . PHP_EOL;
										$args          = array(
											'taxonomy' => $taxonomy_object->name,
											'title_li' => '',
											'orderby'  => 'order',
											'echo'     => 0,
											'show_option_none' => '',
										);
										$sitemap_html .= wp_list_categories( $args );
										$sitemap_html .= '</ul>' . PHP_EOL;
				} // if ( $taxonomy_object->show_in_menu ) {

			} // foreach ( $taxonomies as $taxonomy ) {

			$sitemap_html .= '</div><!-- [ /.sectionBox ] -->' . PHP_EOL;
			$sitemap_html .= '</div>' . PHP_EOL;

		} // if ( $post_type_object ) {
	} // foreach ( $all_post_types as $postType ) {

	$sitemap_html .= '</div>' . PHP_EOL; // <!-- [ /.sitemap-col ] -->
	$sitemap_html .= '</div>' . PHP_EOL; // <!-- [ /.sitemap ] -->

	wp_reset_postdata();
	wp_reset_query();
	return $sitemap_html;
}
add_shortcode( 'vkExUnit_sitemap', 'vkExUnit_sitemap' );

/*
	admin _ meta box
	こちらは非表示設定ではなく サイトマップ自体を表示するかどうか
/*-------------------------------------------*/
add_action( 'veu_metabox_insert_items', 'vkExUnit_sitemap_meta_box' );
function vkExUnit_sitemap_meta_box() {
	global $post;
	// sitemap display
	$enable = get_post_meta( $post->ID, 'vkExUnit_sitemap', true ); ?>

<div>
<input type="hidden" name="_nonce_vkExUnit__custom_field_sitemap" id="_nonce_vkExUnit__custom_field_sitemap" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
<label for="vkExUnit_sitemap">
	<input type="checkbox" id="vkExUnit_sitemap" name="vkExUnit_sitemap" <?php echo ( $enable ) ? ' checked' : ''; ?> />
	<?php _e( 'Display a HTML sitemap', 'vk-all-in-one-expansion-unit' ); ?>
</label>
</div>

	<?php
}


// save custom field sitemap
add_action( 'save_post', 'vkExUnit_save_custom_field_sitemapData' );
function vkExUnit_save_custom_field_sitemapData( $post_id ) {
	$sitemap = isset( $_POST['_nonce_vkExUnit__custom_field_sitemap'] ) ? htmlspecialchars( $_POST['_nonce_vkExUnit__custom_field_sitemap'] ) : null;

	if ( ! wp_verify_nonce( $sitemap, plugin_basename( __FILE__ ) ) ) {
			return $post_id;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id; }

	$data = isset( $_POST['vkExUnit_sitemap'] ) ? htmlspecialchars( $_POST['vkExUnit_sitemap'] ) : null;

	if ( 'page' == $data ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return $post_id; }
	}

	if ( '' == get_post_meta( $post_id, 'vkExUnit_sitemap' ) ) {
		add_post_meta( $post_id, 'vkExUnit_sitemap', $data, true );
	} elseif ( $data != get_post_meta( $post_id, 'vkExUnit_sitemap' ) ) {
		update_post_meta( $post_id, 'vkExUnit_sitemap', $data );
	} elseif ( '' == $data ) {
		delete_post_meta( $post_id, 'vkExUnit_sitemap' );
	}
}

require_once __DIR__ . '/block/index.php';