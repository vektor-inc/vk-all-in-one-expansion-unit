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
 * Get the array of public post types after applying the veu_sitemap_exclude_post_types filter only.
 * `veu_sitemap_exclude_post_types` フィルターまで適用した公開投稿タイプの配列を取得する。
 *
 * Deliberately does NOT apply the user-configurable `excludePostTypes` sitemap option.
 * This is the shared base used both by veu_get_sitemap_post_types() (which additionally
 * applies `excludePostTypes`) and by veu_get_sitemap_available_taxonomies(). Taxonomies must
 * NOT be filtered by `excludePostTypes`, otherwise a taxonomy checkbox can disappear from the
 * settings screen merely because an admin also excluded its post type, and the next save (which
 * only writes back the checkboxes that were actually rendered) silently drops the taxonomy
 * exclusion the admin had set earlier.
 * あえてユーザー設定の `excludePostTypes` オプションは適用しない。
 * veu_get_sitemap_post_types()（さらに `excludePostTypes` を適用する）と
 * veu_get_sitemap_available_taxonomies() の両方が使う共通の基準集合。
 * タクソノミーの一覧をここで `excludePostTypes` まで絞り込んでしまうと、投稿タイプを除外しただけで
 * タクソノミーのチェックボックスが設定画面から消え、次回保存時（実際に描画されたチェックボックスの分しか
 * 書き戻されない）にそれまでのタクソノミー除外設定が無言で失われてしまう。
 *
 * @return array Array of post type names, in the same shape returned by get_post_types().
 *               投稿タイプ名を値に持つ配列（get_post_types() の戻り値形式のまま）。
 */
function veu_get_sitemap_public_post_types() {
	$all_post_types = get_post_types( array( 'public' => true ) );

	// Exclude post types via the veu_sitemap_exclude_post_types filter.
	// フィルターによる除外投稿タイプ処理.
	$exclude_post_types = apply_filters( 'veu_sitemap_exclude_post_types', array( 'page', 'attachment', 'vk-managing-patterns' ) );
	foreach ( $exclude_post_types as $exclude_post_type ) {
		unset( $all_post_types[ $exclude_post_type ] );
	}

	return $all_post_types;
}

/**
 * Get the array of post types actually output on the sitemap.
 * サイトマップに実際に出力される投稿タイプの配列を取得する。
 *
 * Takes veu_get_sitemap_public_post_types() and additionally removes the post types
 * excluded by the ExUnit sitemap setting (`excludePostTypes`). Used by the front-end
 * output (`vkExUnit_sitemap()`) to decide which post types to loop over.
 * veu_get_sitemap_public_post_types() から、さらに ExUnit のサイトマップ設定
 * （`excludePostTypes`）による除外を反映したものを返す。フロント出力（vkExUnit_sitemap()）が
 * どの投稿タイプをループ対象にするかを決めるために使う。
 *
 * @param  array|null $public_post_types Optional. Pre-fetched result of veu_get_sitemap_public_post_types(),
 *                                       to avoid calling it (and firing the veu_sitemap_exclude_post_types
 *                                       filter) a second time when the caller already has it (see
 *                                       vkExUnit_sitemap(), which shares one fetch with
 *                                       veu_get_sitemap_available_taxonomies()). Pass null (default) to
 *                                       fetch it internally.
 *                                       任意。veu_get_sitemap_public_post_types() の結果を事前に渡す事で、
 *                                       呼び出し元が既に取得済みの場合に再取得（veu_sitemap_exclude_post_types
 *                                       フィルターの再発火を含む）を避けられる（vkExUnit_sitemap() が
 *                                       veu_get_sitemap_available_taxonomies() と1回の取得を共有するために使う）。
 *                                       省略時（null）は内部で取得する.
 * @return array Array of post type names, in the same shape returned by get_post_types().
 *               投稿タイプ名を値に持つ配列（get_post_types() の戻り値形式のまま）。
 */
function veu_get_sitemap_post_types( $public_post_types = null ) {
	$options = veu_get_sitemap_options();

	$all_post_types = ( null === $public_post_types ) ? veu_get_sitemap_public_post_types() : $public_post_types;

	// Exclude post types via the ExUnit sitemap option (excludePostTypes).
	// ExUnit のサイトマップ設定（excludePostTypes）による除外投稿タイプ処理.
	if ( isset( $options['excludePostTypes'] ) && is_array( $options['excludePostTypes'] ) ) {
		foreach ( $options['excludePostTypes'] as $post_type => $value ) {
			if ( $value ) {
				unset( $all_post_types[ $post_type ] );
			}
		}
	}

	return $all_post_types;
}

/**
 * Get the taxonomies used both by the sitemap's "exclude taxonomy" checkbox list and by the front-end output.
 * サイトマップの「除外タクソノミー」設定画面・フロント出力の両方で使う、対象タクソノミー一覧を取得する。
 *
 * Returns only the taxonomies that have `show_in_menu` enabled and are attached to a
 * post type in veu_get_sitemap_public_post_types() (public, minus the filter-based
 * exclusion only — NOT the `excludePostTypes` option; see that function's doc for why).
 * A taxonomy attached only to a post type the admin separately excluded via
 * `excludePostTypes` is harmless to keep listed here: the front-end post type loop
 * already skips that post type, so its taxonomies are never rendered anyway.
 * `show_in_menu` が有効で、かつ veu_get_sitemap_public_post_types()（`excludePostTypes`
 * オプションではなく、フィルター除外分までを反映した公開投稿タイプ集合。理由は同関数の
 * ドキュメントを参照）に紐づいているタクソノミーだけを返す。`excludePostTypes` で個別に除外した
 * 投稿タイプだけに紐づくタクソノミーをここに残しても実害はない。フロント側の投稿タイプループが
 * その投稿タイプ自体をそもそも回さないため、紐づくタクソノミーもどのみち描画されない。
 * Keeping this single condition (show_in_menu) here, and having the front-end output
 * check membership in this function's return value instead of re-implementing the
 * same condition, keeps the settings checkbox list and the front-end exclusion
 * condition from falling out of sync.
 * この条件（show_in_menu）をここ1箇所にまとめ、フロント側もこの関数の戻り値への所属で判定する
 * ことで、設定画面のチェックボックス一覧とフロント側の除外判定の条件が食い違わないようにしている。
 * As a side effect, internal taxonomies not shown in the admin UI (e.g. `post_format`)
 * are automatically excluded because their `show_in_menu` is false.
 * 副次的に、投稿フォーマット（post_format）など管理画面 UI に表示されない内部タクソノミーは
 * `show_in_menu` が false のため、この一覧には含まれない。
 *
 * @param  array|null $post_types Optional. Pre-fetched result of veu_get_sitemap_public_post_types(),
 *                                to avoid calling it (and firing the veu_sitemap_exclude_post_types
 *                                filter) a second time when the caller already has it. Pass null
 *                                (default) to fetch it internally; the settings screen relies on
 *                                this default so it keeps working unchanged.
 *                                任意。veu_get_sitemap_public_post_types() の結果を事前に渡す事で、
 *                                呼び出し元が既に取得済みの場合に再取得（veu_sitemap_exclude_post_types
 *                                フィルターの再発火を含む）を避けられる。省略時（null、既定）は
 *                                内部で取得する。設定画面はこの既定動作のまま変更不要で動く.
 * @return array Array of WP_Taxonomy objects keyed by taxonomy name.
 *               タクソノミー名をキーにした WP_Taxonomy オブジェクトの配列。
 */
function veu_get_sitemap_available_taxonomies( $post_types = null ) {
	if ( null === $post_types ) {
		$post_types = veu_get_sitemap_public_post_types();
	}
	$available_taxonomies = array();

	foreach ( $post_types as $post_type ) {
		$taxonomy_objects = get_object_taxonomies( $post_type, 'objects' );
		foreach ( $taxonomy_objects as $taxonomy_name => $taxonomy_object ) {
			// Skip taxonomies already collected, or not shown in the admin UI.
			// 既に一覧に含まれている、または管理画面 UI に表示しないタクソノミーは対象外.
			if ( isset( $available_taxonomies[ $taxonomy_name ] ) || ! $taxonomy_object->show_in_menu ) {
				continue;
			}
			$available_taxonomies[ $taxonomy_name ] = $taxonomy_object;
		}
	}

	return $available_taxonomies;
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
