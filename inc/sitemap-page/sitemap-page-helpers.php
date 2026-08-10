<?php
function veu_get_sitemap_options() {
	$default_options = array(
		'excludePostTypes'  => array(),
		'excludeTaxonomies' => array(),
	);
	$options         = get_option( 'vkExUnit_sitemap_options', $default_options );
	$options         = wp_parse_args( $options, $default_options );
	return apply_filters( 'vkExUnit_sitemap_options', $options );
}

function veu_get_sitemap_options_default() {
	$default_options['excludeId'] = '';
	return apply_filters( 'vkExUnit_sitemap_options_default', $default_options );
}

/**
 * Get the array of post types actually output on the sitemap.
 * サイトマップに実際に出力される投稿タイプの配列を取得する。
 *
 * Takes `get_post_types( array( 'public' => true ) )` and removes the post types
 * excluded by the `veu_sitemap_exclude_post_types` filter and by the ExUnit
 * sitemap setting (`excludePostTypes`).
 * `get_post_types( array( 'public' => true ) )` から、`veu_sitemap_exclude_post_types`
 * フィルターによる除外と、ExUnit のサイトマップ設定（`excludePostTypes`）による除外を反映したものを返す。
 * This single function is called from both the settings screen (to filter the
 * taxonomy checkbox list) and the front-end output (`vkExUnit_sitemap()`), so
 * the two conditions never fall out of sync.
 * 設定画面（除外タクソノミー一覧の絞り込み）とフロント出力（vkExUnit_sitemap()）の
 * 両方から同じ関数を呼ぶことで、一覧に載る条件と実際に除外される条件を一致させる。
 *
 * @return array Array of post type names, in the same shape returned by get_post_types().
 *               投稿タイプ名を値に持つ配列（get_post_types() の戻り値形式のまま）。
 */
if ( ! function_exists( 'veu_get_sitemap_post_types' ) ) {
	function veu_get_sitemap_post_types() {
		$options = veu_get_sitemap_options();

		$all_post_types = get_post_types( array( 'public' => true ) );

		// Exclude post types via the veu_sitemap_exclude_post_types filter.
		// フィルターによる除外投稿タイプ処理
		$exclude_post_types = apply_filters( 'veu_sitemap_exclude_post_types', array( 'page', 'attachment', 'vk-managing-patterns' ) );
		foreach ( $exclude_post_types as $exclude_post_type ) {
			unset( $all_post_types[ $exclude_post_type ] );
		}

		// Exclude post types via the ExUnit sitemap option (excludePostTypes).
		// ExUnit のサイトマップ設定（excludePostTypes）による除外投稿タイプ処理
		if ( isset( $options['excludePostTypes'] ) && is_array( $options['excludePostTypes'] ) ) {
			foreach ( $options['excludePostTypes'] as $post_type => $value ) {
				if ( $value ) {
					unset( $all_post_types[ $post_type ] );
				}
			}
		}

		return $all_post_types;
	}
}

/**
 * Get the taxonomies used both by the sitemap's "exclude taxonomy" checkbox list and by the front-end output.
 * サイトマップの「除外タクソノミー」設定画面・フロント出力の両方で使う、対象タクソノミー一覧を取得する。
 *
 * Returns only the taxonomies that have `show_in_menu` enabled and are attached to a
 * post type actually output on the sitemap (see veu_get_sitemap_post_types()).
 * `show_in_menu` が有効で、かつ「サイトマップに実際に出力される投稿タイプ」
 * （veu_get_sitemap_post_types()）に紐づいているタクソノミーだけを返す。
 * Keeping this condition in a single function keeps the settings checkbox list and
 * the front-end exclusion condition from falling out of sync.
 * この条件を1つの関数にまとめることで、設定画面のチェックボックス一覧とフロント側の
 * 除外判定の条件が食い違わないようにしている。
 * As a side effect, internal taxonomies not shown in the admin UI (e.g. `post_format`)
 * are automatically excluded because their `show_in_menu` is false.
 * 副次的に、投稿フォーマット（post_format）など管理画面 UI に表示されない内部タクソノミーは
 * `show_in_menu` が false のため、この一覧には含まれない。
 *
 * @return array Array of WP_Taxonomy objects keyed by taxonomy name.
 *               タクソノミー名をキーにした WP_Taxonomy オブジェクトの配列。
 */
if ( ! function_exists( 'veu_get_sitemap_available_taxonomies' ) ) {
	function veu_get_sitemap_available_taxonomies() {
		$post_types           = veu_get_sitemap_post_types();
		$available_taxonomies = array();

		foreach ( $post_types as $post_type ) {
			$taxonomy_objects = get_object_taxonomies( $post_type, 'objects' );
			foreach ( $taxonomy_objects as $taxonomy_name => $taxonomy_object ) {
				// Skip taxonomies already collected, or not shown in the admin UI.
				// 既に一覧に含まれている、または管理画面 UI に表示しないタクソノミーは対象外
				if ( isset( $available_taxonomies[ $taxonomy_name ] ) || ! $taxonomy_object->show_in_menu ) {
					continue;
				}
				$available_taxonomies[ $taxonomy_name ] = $taxonomy_object;
			}
		}

		return $available_taxonomies;
	}
}

/*
	サイトマップで非表示にする
/*-------------------------------------------*/

function veu_sitemap_exclude_page_ids() {
	// meta_key が　sitemap_hide が true で post_type が page の投稿を取得する
	$args                     = array(
		'posts_per_page' => -1, // 取得する数
		'post_type'      => 'page', // 投稿タイプ名
		'meta_query'     => array(
			array(
				'key'   => 'sitemap_hide',
				'value' => 'true',
			),
		),
	);
	$sitemap_hide_customPosts = get_posts( $args );

	// 取得した投稿データをループして、id名を $excludes に追加していく
	// 「sitemap_hide」フィールドの値が格納されていたら「$excludes」に ID を追加する処理を開始
	if ( $sitemap_hide_customPosts ) {
		$excludes = '';
		foreach ( $sitemap_hide_customPosts as $key => $value ) {
			// print_r($value);

			if ( ! $excludes ) {
				$excludes .= $value->ID;
			} else {
				$excludes .= ',' . $value->ID;
			}

			$excludes = esc_attr( $excludes );
		}

		return $excludes;
	} // if( $sitemap_hide_customPosts ) {
} // function veu_sitemap_exclude_page_ids() {
