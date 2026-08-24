<?php
/**
 * ExUnit-owned template tag implementations.
 * ExUnit が所有するテンプレートタグの実装。
 *
 * Unlike inc/template-tags/package/template-tags.php and package/template-tags-veu.php, this
 * file is NOT a copy synced from vektor-wp-libraries. It is authored and maintained by ExUnit
 * itself and is never overwritten by a library sync, so it is the correct place to add ExUnit's
 * own fixes going forward.
 * inc/template-tags/package/template-tags.php や package/template-tags-veu.php と異なり、この
 * ファイルは vektor-wp-libraries から同期されたコピーではない。ExUnit 自身が作成・保守するファイル
 * であり、ライブラリ同期で上書きされることもない。そのため、今後 ExUnit 独自の修正を加える先として
 * 正しい置き場所である。
 *
 * Why this file exists:
 * package/template-tags.php wraps every "vk_" prefixed function in a function_exists() guard so
 * the same file can safely be bundled by multiple Vektor plugins without a fatal redeclaration
 * error. The side effect is that whichever plugin's bundled copy of the file loads first "wins"
 * the definition for the entire request. When another Vektor plugin (e.g. VK Post Author
 * Display) bundles an older copy of the same file and happens to load before ExUnit, ExUnit's
 * own bug fixes inside these functions never take effect: ExUnit ends up silently running the
 * other plugin's stale implementation instead of its own.
 * このファイルが存在する理由:
 * package/template-tags.php は "vk_" 接頭辞の各関数を function_exists() ガードで囲んでおり、これに
 * より同じファイルが複数の Vektor 製プラグインへ同梱されても関数の再宣言エラーにならない。その副作用
 * として、どのプラグインの同梱コピーが最初に読み込まれるかで、そのリクエスト全体の定義が決まってしまう。
 * 他の Vektor 製プラグイン（VK Post Author Display 等）が同じファイルの古いコピーを同梱していて、
 * ExUnit より先に読み込まれた場合、ExUnit 側で行った修正が効かず、古い実装のまま動作してしまう。
 *
 * How this file solves it:
 * ExUnit's own code (everything under inc/ except inc/template-tags/package/, which is a synced
 * copy left untouched — see above) calls the "veu_" prefixed functions defined in this
 * file instead of the "vk_" names. These are full duplicates of the corresponding "vk_"
 * implementation, not thin wrappers that call the "vk_" version — delegating to "vk_" would
 * still route through whichever plugin's copy happened to load first, which defeats the purpose
 * of this file entirely. For the same reason, when one of these functions internally calls
 * another shared helper (for example veu_get_post_type() using the "page for posts" helper),
 * that internal call also targets the "veu_" version here, never the "vk_" one.
 * このファイルでの解決方法:
 * ExUnit 自身のコード（inc/ 配下のうち、同期コピーである inc/template-tags/package/ を除く全体。
 * 上記参照）は、"vk_" ではなくこのファイルで定義する "veu_" 接頭辞の関数を呼び出す。これらは
 * "vk_" 版のラッパー（内部で "vk_" を呼ぶだけの委譲）ではなく、対応する実装を丸ごと複製したもの
 * である。委譲にしてしまうと、結局どのプラグインのコピーが先に読み込まれたかに処理が流れてしまい、
 * このファイルを用意した意味がなくなるためである。同じ理由で、ある関数が内部で別の共通処理を
 * 呼んでいる場合（例: veu_get_post_type() が「投稿トップページ判定」のヘルパーを使う場合）も、その
 * 内部呼び出しは "vk_" 側ではなく必ずこのファイルの "veu_" 版を向ける。
 *
 * The "vk_" functions in package/template-tags.php are left completely untouched by this change
 * and keep working exactly as before: they remain the external-facing compatibility layer for
 * themes, other plugins, and site owners' functions.php that may already call them directly.
 * package/template-tags.php の "vk_" 関数はこの変更で一切変更していない。従来どおり動作し続け、
 * テーマ・他プラグイン・利用者の functions.php から直接呼ばれる可能性がある外部向けの互換レイヤーと
 * して維持される。
 *
 * These "veu_" functions are intentionally NOT wrapped in function_exists() guards. Guarding
 * them would mean deferring to a same-named function defined elsewhere first if one ever
 * existed, which reproduces the exact load-order problem this file exists to solve. Safety
 * against ExUnit's own double-loading instead comes from this file being loaded via
 * require_once (never plain require/include) — see inc/template-tags/template-tags-config.php,
 * the sole loader of this file.
 * この "veu_" 関数群は意図的に function_exists() ガードで囲んでいない。ガードを付けると、万一どこかに
 * 同名の関数が既に存在していた場合にそちらへ処理を譲ってしまい、このファイルが解決しようとしている
 * 読み込み順序問題をそのまま再現してしまうためである。ExUnit 自身の二重読み込みに対する安全性は、この
 * ファイルの唯一の読み込み元である inc/template-tags/template-tags-config.php が require_once
 * （単純な require / include ではなく）で読み込むことで担保している。
 *
 * IF tests/test-template-tags-parity.php FAILS after you edit a function in this file: that
 * test pins each "veu_" function here against its "vk_" source of truth in
 * package/template-tags.php at the PHP token level. A failure means you either (a) need to
 * port the same fix into package/template-tags.php (the vektor-wp-libraries source repo) so
 * the next sync carries it too, or (b) made a deliberate ExUnit-only change that should stay
 * different from "vk_" — in that case add it to that test's FUNCTION_PAIRS as a documented,
 * reviewed difference (see that file's own docblock for the available options) rather than
 * editing the comparison logic ad hoc.
 * tests/test-template-tags-parity.php がこのファイルの関数を編集した後に失敗した場合: このテストは
 * ここの各 "veu_" 関数を、正本である package/template-tags.php の対応する "vk_" 関数と PHP トークン
 * 単位で突き合わせて固定している。失敗した場合は、(a) 同じ修正を package/template-tags.php（正本の
 * vektor-wp-libraries リポジトリ）側にも移植し、次回同期でも反映されるようにするか、(b) ExUnit だけの
 * 意図的な変更で "vk_" 側とは違えたままにしたい場合は、その場しのぎで比較ロジックを書き換えるのではなく、
 * 同テストの FUNCTION_PAIRS へレビュー済みの差分として明記して追加する（選択肢の詳細は同ファイル自身の
 * docblock を参照）。
 *
 * NOTICE FOR vektor-wp-libraries MAINTAINERS — do not add these 9 function names there:
 * veu_get_page_for_posts(), veu_get_post_type(), veu_get_page_description(),
 * veu_the_post_type_check_list(), veu_the_taxonomy_check_list(),
 * veu_the_post_type_check_list_saved_array_convert(), veu_is_checked(),
 * veu_sanitize_number(), veu_is_excerpt(). This file defines them WITHOUT a function_exists()
 * guard (see above for why). If vektor-wp-libraries (or any other Vektor plugin bundling a copy
 * of it) ever defines one of these same names without a guard too, and that copy happens to load
 * before ExUnit, PHP raises a fatal "Cannot redeclare" error and takes the whole site down. The
 * "veu_" prefix is not reserved for ExUnit: the shared library itself already defines
 * veu_sanitize_boolean() / veu_sanitize_radio() in package/template-tags.php, and VK Post Author
 * Display bundles that same file — so this collision is a real, not theoretical, risk.
 * vektor-wp-libraries の同期担当者へ — 以下の9関数名を vektor-wp-libraries 側で定義しないこと:
 * veu_get_page_for_posts(), veu_get_post_type(), veu_get_page_description(),
 * veu_the_post_type_check_list(), veu_the_taxonomy_check_list(),
 * veu_the_post_type_check_list_saved_array_convert(), veu_is_checked(),
 * veu_sanitize_number(), veu_is_excerpt()。このファイルはこれらを function_exists() ガード無しで
 * 定義している（理由は上記参照）。もし vektor-wp-libraries（またはそれを同梱する他の Vektor 製
 * プラグイン）が将来これらと同名の関数をガード無しで定義し、そのコピーが ExUnit より先に読み込まれると、
 * PHP が "Cannot redeclare" 致命的エラーを起こしサイト全体が落ちる。"veu_" 接頭辞は ExUnit 専用では
 * ない。共有ライブラリ自身が package/template-tags.php で veu_sanitize_boolean() /
 * veu_sanitize_radio() を既に定義しており、VK Post Author Display も同じファイルを同梱しているため、
 * この衝突は理論上の懸念ではなく現実的なリスクである。
 *
 * @package VK All in One Expansion Unit
 * @see https://github.com/vektor-inc/vk-all-in-one-expansion-unit/issues/1478
 * @see https://github.com/vektor-inc/vk-all-in-one-expansion-unit/issues/1476
 */

/**
 * Get info about whether a static page is used as the "posts" page, and if so its title.
 * 投稿一覧として固定ページ（投稿トップページ）が使われているかどうかと、使われている場合はその
 * タイトルを取得する。
 *
 * Full duplicate of package/template-tags.php's vk_get_page_for_posts() (see the file docblock
 * above for why this is a duplicate, not a wrapper around the "vk_" version).
 * package/template-tags.php の vk_get_page_for_posts() の完全な複製（委譲ではなく複製にしている
 * 理由はファイル冒頭の docblock を参照）。
 *
 * @return array {
 *     @type int|string $post_top_id   The "page_for_posts" option value. 「page_for_posts」オプションの値。
 *     @type bool        $post_top_use Whether a posts page is set. 投稿トップページが設定されているか。
 *     @type string      $post_top_name The posts page title, or '' when not set. 投稿トップページのタイトル（未設定時は ''）。
 * }
 */
function veu_get_page_for_posts() {
	// Get post top page by setting display page.
	// 表示ページの設定から投稿トップページを取得する.
	$page_for_posts['post_top_id'] = get_option( 'page_for_posts' );

	// Set use post top page flag.
	// 投稿トップページを使用しているかどうかのフラグをセットする.
	$page_for_posts['post_top_use'] = ( isset( $page_for_posts['post_top_id'] ) && $page_for_posts['post_top_id'] ) ? true : false;

	// When use post top page that get post top page name.
	// 投稿トップページを使用している場合はそのページ名を取得する.
	$page_for_posts['post_top_name'] = ( $page_for_posts['post_top_use'] ) ? get_the_title( $page_for_posts['post_top_id'] ) : '';

	return $page_for_posts;
}

/**
 * Get info about the post type of the currently viewed (or, in the admin, currently edited) post.
 * 現在表示中（管理画面では現在編集中）の投稿の投稿タイプ情報を取得する。
 *
 * Full duplicate of package/template-tags.php's vk_get_post_type(), calling
 * veu_get_page_for_posts() internally instead of vk_get_page_for_posts() (see the file docblock
 * above for why this is a duplicate, not a wrapper around the "vk_" version).
 * package/template-tags.php の vk_get_post_type() の完全な複製。内部で vk_get_page_for_posts()
 * ではなく veu_get_page_for_posts() を呼ぶ点のみ異なる（委譲ではなく複製にしている理由はファイル
 * 冒頭の docblock を参照）。
 *
 * @return array {
 *     @type string $slug The post type slug. 投稿タイプの slug。
 *     @type string $name The post type label (only set when the post type is known). 投稿タイプのラベル（投稿タイプが判明している場合のみセット）。
 *     @type string|false $url  The post type archive URL (only set when the post type is known). 投稿タイプアーカイブの URL（投稿タイプが判明している場合のみセット）。
 * }
 */
function veu_get_post_type() {

	$postType = array();

	// WP-CLI / cron 等では REQUEST_URI が未設定になり得るため、strpos() に null が渡らないよう既定値を与える。
	// REQUEST_URI can be unset under WP-CLI / cron, so default it to avoid passing null to strpos().
	$url = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';

	// 管理画面の投稿タイプ
	// ※ phpunitで is_admin()判定が効かない場合のため strpos( $url, 'wp-admin' ) を使用
	// Admin-side post type.
	// Also check strpos( $url, 'wp-admin' ) because is_admin() is not reliable under PHPUnit.
	if ( is_admin() || strpos( $url, 'wp-admin' ) ) {
		global $post;
		$postType['slug'] = get_post_type( $post );
		if ( ! $postType['slug'] ) {
			if ( ! empty( $_GET['post_type'] ) ) {
				$postType['slug'] = $_GET['post_type'];
			} elseif ( ! empty( $_GET['post'] ) ) {
				$admin_post = get_post( $_GET['post'] );
				if ( ! empty( $admin_post->post_type ) ) {
					$postType['slug'] = $admin_post->post_type;
				}
			}
		}
		return $postType;
	}

	/*-------------------------------------------*/
	global $wp_query;
	$page_for_posts   = veu_get_page_for_posts();
	$postType['slug'] = get_post_type();
	if ( ! $postType['slug'] ) {
		if ( isset( $wp_query->query_vars['post_type'] ) && $wp_query->query_vars['post_type'] ) {
			$postType['slug'] = $wp_query->query_vars['post_type'];
			// メインクエリに post_type を配列で指定した場合（例: pre_get_posts で array( 'event', 'page' ) を set）への対策.
			// slug は文字列前提で利用されるため、配列の場合は先頭要素を採用して文字列に正規化する.
			// これをしないと後続の 'post-type-' . $slug 等で "Array to string conversion" Warning が発生する.
			// Countermeasure for when the main query sets post_type as an array (e.g. via
			// pre_get_posts with array( 'event', 'page' )). Callers treat slug as a string, so
			// normalize an array to a string by taking its first element; otherwise later code
			// such as 'post-type-' . $slug triggers an "Array to string conversion" warning.
			if ( is_array( $postType['slug'] ) ) {
				$postType['slug'] = ! empty( $postType['slug'] ) ? reset( $postType['slug'] ) : '';
			}
		} else {
			// Case of no post type query
			if ( ! empty( $wp_query->queried_object->taxonomy ) ) {
				// Case of tax archive and no posts
				$taxonomy         = $wp_query->queried_object->taxonomy;
				$postType['slug'] = get_taxonomy( $taxonomy )->object_type[0];
			} else {
				// Case of no tax query and no post type query and no posts
				$postType['slug'] = 'post';
			} // if ( ! empty( $wp_query->queried_object->taxonomy ) ) {
		}
	}

	// Get post type name
	/*-------------------------------------------*/
	$post_type_object = get_post_type_object( $postType['slug'] );
	if ( $post_type_object ) {
		$allowed_html = array(
			'span' => array( 'class' => array() ),
			'b'    => array(),
		);
		if ( $page_for_posts['post_top_use'] && $postType['slug'] == 'post' ) {
			$postType['name'] = wp_kses( get_the_title( $page_for_posts['post_top_id'] ), $allowed_html );
		} else {
			$postType['name'] = esc_html( $post_type_object->labels->name );
		}
	}

	// Get post type archive url
	/*-------------------------------------------*/
	if ( $page_for_posts['post_top_use'] && $postType['slug'] == 'post' ) {
		$postType['url'] = get_the_permalink( $page_for_posts['post_top_id'] );
	} else {
		$postType['url'] = get_post_type_archive_link( $postType['slug'] );
	}

	$postType = apply_filters( 'vkExUnit_postType_custom', $postType );
	return $postType;
}

/**
 * Build the meta description text for the currently viewed page.
 * 現在表示中のページ用のメタディスクリプション文字列を組み立てる。
 *
 * Full duplicate of package/template-tags.php's vk_get_page_description(), calling
 * veu_get_page_for_posts() internally instead of vk_get_page_for_posts() (see the file docblock
 * above for why this is a duplicate, not a wrapper around the "vk_" version).
 * package/template-tags.php の vk_get_page_description() の完全な複製。内部で
 * vk_get_page_for_posts() ではなく veu_get_page_for_posts() を呼ぶ点のみ異なる（委譲ではなく複製に
 * している理由はファイル冒頭の docblock を参照）。
 *
 * @return string The page description, stripped of tags/shortcodes and line breaks. タグ・ショートコード・改行を除去したページの説明文。
 */
function veu_get_page_description() {
	global $wp_query;
	$page_description = '';
	$post             = $wp_query->get_queried_object();
	if ( is_search() || is_404() ) {
		$page_description = '';
	} elseif ( is_front_page() ) {
		if ( isset( $post->post_excerpt ) && $post->post_excerpt && ! post_password_required( $post->ID ) ) {
			$page_description = get_the_excerpt( $post->ID );
		} else {
			$page_description = get_bloginfo( 'description' );
		}
	} elseif ( is_home() ) {
		$page_for_posts = veu_get_page_for_posts();
		if ( $page_for_posts['post_top_use'] ) {
			$page = get_post( $page_for_posts['post_top_id'] );
			if ( ! empty( $page->post_excerpt ) && ! post_password_required( $page->ID ) ) {
				$page_description = get_the_excerpt( $page->ID );
			} else {
				$page_description  = sprintf( _x( 'Article of %s.', 'Archive description', 'vk-all-in-one-expansion-unit' ), esc_html( $page_for_posts['post_top_name'] ) );
				$page_description .= ' ' . get_bloginfo( 'name' ) . ' ' . get_bloginfo( 'description' );
			}
		} else {
			$page_description = get_bloginfo( 'description' );
		}
	} elseif ( is_category() || is_tax() ) {
		if ( empty( $post->description ) ) {
			$page_description = sprintf( __( 'About %s', 'vk-all-in-one-expansion-unit' ), single_cat_title( '', false ) ) . ' ' . get_bloginfo( 'name' ) . ' ' . get_bloginfo( 'description' );
		} else {
			$page_description = $post->description;
		}
	} elseif ( is_tag() ) {
		$page_description = strip_tags( tag_description() );
		$page_description = str_replace( array( "\r\n", "\r", "\n" ), '', $page_description );  // delete br
		if ( ! $page_description ) {
			$page_description = sprintf( __( 'About %s', 'vk-all-in-one-expansion-unit' ), single_tag_title( '', false ) ) . ' ' . get_bloginfo( 'name' ) . ' ' . get_bloginfo( 'description' );
		}
	} elseif ( is_archive() ) {
		if ( is_year() ) {
			$description_date  = get_the_date( _x( 'Y', 'yearly archives date format', 'vk-all-in-one-expansion-unit' ) );
			$page_description  = sprintf( _x( 'Article of %s.', 'Yearly archive description', 'vk-all-in-one-expansion-unit' ), $description_date );
			$page_description .= ' ' . get_bloginfo( 'name' ) . ' ' . get_bloginfo( 'description' );
		} elseif ( is_month() ) {
			$description_date  = get_the_date( _x( 'F Y', 'monthly archives date format', 'vk-all-in-one-expansion-unit' ) );
			$page_description  = sprintf( _x( 'Article of %s.', 'Archive description', 'vk-all-in-one-expansion-unit' ), $description_date );
			$page_description .= ' ' . get_bloginfo( 'name' ) . ' ' . get_bloginfo( 'description' );
		} elseif ( is_author() ) {
			$userObj           = get_queried_object();
			$page_description  = sprintf( _x( 'Article of %s.', 'Archive description', 'vk-all-in-one-expansion-unit' ), esc_html( $userObj->display_name ) );
			$page_description .= ' ' . get_bloginfo( 'name' ) . ' ' . get_bloginfo( 'description' );
		} else {
			$postType = get_post_type();
			if ( $postType ) {
				$page_description  = sprintf( _x( 'Article of %s.', 'Archive description', 'vk-all-in-one-expansion-unit' ), esc_html( get_post_type_object( $postType )->label ) );
				$page_description .= ' ' . get_bloginfo( 'name' ) . ' ' . get_bloginfo( 'description' );
			} else {
				$page_description = get_bloginfo( 'description' );
			}
		}
	} elseif ( is_page() || is_single() ) {
		if ( post_password_required( $post->ID ) ) {
			$page_description = __( 'This article is protected by a password.', 'vk-all-in-one-expansion-unit' );
		} elseif ( ! empty( $post->post_excerpt ) ) {
			$page_description = get_the_excerpt( $post->ID );
		} else {
			$page_description = get_the_content( null, false, $post->ID );
		}
	} else {
		$page_description = get_bloginfo( 'description' );
	}
	global $paged;
	if ( $paged != '0' ) {
		$page_description = '[' . sprintf( __( 'Page of %s', 'vk-all-in-one-expansion-unit' ), $paged ) . '] ' . $page_description;
	}
	// This filter (vkExUnit_pageDescriptionCustom) is deprecated.
	$page_description = apply_filters( 'vkExUnit_pageDescriptionCustom', $page_description );

	// この関数は get_the_ ではないので関数内では esc_attr() は行わない

	// 余計なスタイルタグ・スクリプトタグを除去
	$page_description = preg_replace( '/<(style|script).*?>(.|\r|\n)*?<\/(style|script)>/', '', $page_description );

	// HTML タグを除去
	$page_description = strip_tags( $page_description );

	// ショートコードを削除
	$page_description = strip_shortcodes( $page_description );

	if ( is_singular() ) {
		$page_description = mb_substr( $page_description, 0, 240 ); // kill tags and trim 240 chara
	}

	// Delete Line break
	$page_description = str_replace( array( "\r\n", "\r", "\n", "\t" ), '', $page_description );

	return apply_filters( 'vk_get_page_description', $page_description );
}

/**
 * Echo a checkbox list of post types, for use on ExUnit's own settings screens.
 * 投稿タイプのチェックボックス一覧を出力する（ExUnit 自身の設定画面用）。
 *
 * Full duplicate of package/template-tags.php's vk_the_post_type_check_list() (see the file
 * docblock above for why this is a duplicate, not a wrapper around the "vk_" version), with one
 * reviewed addition: $key is wrapped in esc_attr() where the "vk_" version echoes it bare. See
 * the "NOTICE FOR vektor-wp-libraries MAINTAINERS" block above and
 * tests/test-template-tags-parity.php for how this one known difference is tracked.
 * package/template-tags.php の vk_the_post_type_check_list() の完全な複製（委譲ではなく複製に
 * している理由はファイル冒頭の docblock を参照）だが、1点だけレビュー済みの追加がある: "vk_" 版が
 * $key を素のまま echo している箇所を esc_attr( $key ) でラップしている。この既知の差分の扱いは、
 * 上記の「vektor-wp-libraries の同期担当者へ」の注記と tests/test-template-tags-parity.php を参照。
 *
 * @param array $args {
 *     Settings for the checkbox display. チェックボックス表示用の設定。
 *
 *     @type array  $post_types_args    Arguments passed to get_post_types(). get_post_types() へ渡す引数。
 *     @type string $name               Base for the checkbox name attribute. チェックボックスの name 属性のベース。
 *     @type array  $checked            Saved values holding the checked state (post type => true, etc.). チェック状態を持つ保存済みの値（投稿タイプ名 => true 等）。
 *     @type array  $id                 Optional per-post-type id attribute values. 投稿タイプごとの id 属性を指定する場合の配列。
 *     @type array  $exclude_post_types Post type names to leave out of the list. 一覧から除外する投稿タイプ名の配列。
 * }
 * @return void Outputs markup directly via echo; nothing is returned. 直接 echo で出力するため返り値はなし。
 */
function veu_the_post_type_check_list( $args ) {
	$default    = array(
		'post_types_args'    => array(
			'public' => true,
		),
		'name'               => '',
		'checked'            => array( 'post' => true ),
		'id'                 => '',
		'exclude_post_types' => array( 'attachment' ),
	);
	$args       = wp_parse_args( $args, $default );
	$post_types = get_post_types( $args['post_types_args'], 'object' );

	echo '<ul class="no-style">';
	foreach ( $post_types as $key => $value ) {
		if ( ! in_array( $key, $args['exclude_post_types'] ) ) {
			$checked = ( isset( $args['checked'][ $key ] ) && ( $args['checked'][ $key ] ) ) ? ' checked' : '';

			if ( ! empty( $args['id'][ $key ] ) ) {
				$id = ' id="' . esc_attr( $args['id'][ $key ] ) . '"';
			} elseif ( ! empty( $args['name'][ $key ] ) ) {
				$id = ' id="' . esc_attr( $args['name'][ $key ] ) . '"';
			} else {
				$id = '';
			}

			echo '<li><label>';
			echo '<input type="checkbox" name="' . esc_attr( $args['name'] ) . '[' . esc_attr( $key ) . ']"' . $id . ' value="true"' . $checked . ' />' . esc_html( $value->label );
			echo '</label></li>';
		}
	}
	echo '</ul>';
}

/**
 * Echo a checkbox list of taxonomies, for use on ExUnit's own settings screens.
 * タクソノミーのチェックボックス一覧を出力する（ExUnit 自身の設定画面用）。
 *
 * Full duplicate of package/template-tags.php's vk_the_taxonomy_check_list() (see the file
 * docblock above for why this is a duplicate, not a wrapper around the "vk_" version).
 * package/template-tags.php の vk_the_taxonomy_check_list() の完全な複製（委譲ではなく複製に
 * している理由はファイル冒頭の docblock を参照）。
 *
 * @param array $args {
 *     Settings for the checkbox display. チェックボックス表示用の設定。
 *
 *     @type WP_Taxonomy[] $taxonomies         Taxonomy objects to display, keyed by taxonomy name. 表示対象のタクソノミーオブジェクトの配列（キーはタクソノミー名）。
 *     @type string        $name               Base for the checkbox name attribute. チェックボックスの name 属性のベース。
 *     @type array         $checked            Saved values holding the checked state. チェック状態を持つ保存済みの値。
 *     @type array         $id                 Optional per-taxonomy id attribute values. タクソノミー名ごとの id 属性を指定する場合の配列。
 *     @type string[]      $exclude_taxonomies Taxonomy names to leave out of the list. 一覧から除外するタクソノミー名の配列。
 *     @type string        $empty_message      Message shown instead of the list when 'taxonomies' is empty. 'taxonomies' が空の場合に一覧の代わりに表示する文言。
 * }
 * @return void Outputs markup directly via echo; nothing is returned. 直接 echo で出力するため返り値はなし。
 */
function veu_the_taxonomy_check_list( $args ) {
	$default = array(
		'taxonomies'         => array(),
		'name'               => '',
		'checked'            => array(),
		'id'                 => array(),
		'exclude_taxonomies' => array(),
		'empty_message'      => __( 'No taxonomies are available to exclude.', 'vk-all-in-one-expansion-unit' ),
	);
	$args    = wp_parse_args( $args, $default );

	if ( empty( $args['taxonomies'] ) ) {
		echo '<p class="description">' . esc_html( $args['empty_message'] ) . '</p>';
		return;
	}

	echo '<ul class="no-style">';
	foreach ( $args['taxonomies'] as $key => $value ) {
		if ( ! in_array( $key, $args['exclude_taxonomies'], true ) ) {
			$checked = ( isset( $args['checked'][ $key ] ) && ( $args['checked'][ $key ] ) ) ? ' checked' : '';
			$id      = ! empty( $args['id'][ $key ] ) ? ' id="' . esc_attr( $args['id'][ $key ] ) . '"' : '';

			echo '<li><label>';
			echo '<input type="checkbox" name="' . esc_attr( $args['name'] ) . '[' . esc_attr( $key ) . ']"' . $id . ' value="true"' . $checked . ' />' . esc_html( $value->label );
			echo '</label></li>';
		}
	}
	echo '</ul>';
}

/**
 * Convert a saved post type checkbox array (post type => truthy value) into a plain list of
 * the checked post type slugs.
 * 保存済みの投稿タイプチェックボックス配列（投稿タイプ名 => 真偽値相当）を、チェック済み投稿タイプ
 * slug だけの単純な配列へ変換する。
 *
 * Full duplicate of package/template-tags.php's vk_the_post_type_check_list_saved_array_convert()
 * (see the file docblock above for why this is a duplicate, not a wrapper around the "vk_"
 * version).
 * package/template-tags.php の vk_the_post_type_check_list_saved_array_convert() の完全な複製
 * （委譲ではなく複製にしている理由はファイル冒頭の docblock を参照）。
 *
 * @param array $post_types Saved checkbox array, e.g. array( 'post' => 'true', 'info' => '' ). 保存済みのチェックボックス配列。例: array( 'post' => 'true', 'info' => '' )。
 * @return array List of checked post type slugs, e.g. array( 'post' ). チェック済み投稿タイプ slug の配列。例: array( 'post' )。
 */
function veu_the_post_type_check_list_saved_array_convert( $post_types ) {
	$return = array();
	if ( is_array( $post_types ) ) {
		foreach ( $post_types as $post_type => $value ) {
			if ( $value ) {
				$return[] = $post_type;
			}
		}
	}
	return $return;
}

/**
 * Echo the "checked" HTML attribute when the two given values match.
 * 2つの値が一致する場合に "checked" という HTML 属性を echo する。
 *
 * Full duplicate of package/template-tags.php's vk_is_checked() (see the file docblock above
 * for why this is a duplicate, not a wrapper around the "vk_" version).
 * package/template-tags.php の vk_is_checked() の完全な複製（委譲ではなく複製にしている理由は
 * ファイル冒頭の docblock を参照）。
 *
 * @param string $checked_value The value that means "checked". チェック状態とみなす値。
 * @param string $value         The saved value to compare against. 比較対象の保存値。
 * @return void Outputs ' checked' directly via echo when matched, otherwise outputs nothing. 一致した場合は ' checked' を直接 echo で出力し、一致しない場合は何も出力しない。
 */
function veu_is_checked( $checked_value = '', $value = '' ) {
	$checked = '';
	if ( $checked_value == $value ) {
		$checked = ' checked';
	}
	echo $checked;
}

/**
 * Sanitize a value expected to be a number, including full-width digit conversion.
 * 数値として扱う想定の値をサニタイズする（全角数字の半角変換を含む）。
 *
 * Full duplicate of package/template-tags.php's vk_sanitize_number() (see the file docblock
 * above for why this is a duplicate, not a wrapper around the "vk_" version).
 * package/template-tags.php の vk_sanitize_number() の完全な複製（委譲ではなく複製にしている理由は
 * ファイル冒頭の docblock を参照）。
 *
 * @param mixed $input Raw input value (e.g. a posted form field). 未加工の入力値（フォームの送信値など）。
 * @return string The sanitized integer value as a string. サニタイズ後の整数値（文字列）。
 */
function veu_sanitize_number( $input ) {
	$return = intval( mb_convert_kana( $input, 'n' ) );
	return esc_attr( $return );
}

/**
 * Whether the current output context is inside get_the_excerpt().
 * 現在の出力コンテキストが get_the_excerpt() の中かどうかを判定する。
 *
 * Full duplicate of package/template-tags.php's vk_is_excerpt() (see the file docblock above
 * for why this is a duplicate, not a wrapper around the "vk_" version).
 * package/template-tags.php の vk_is_excerpt() の完全な複製（委譲ではなく複製にしている理由は
 * ファイル冒頭の docblock を参照）。
 *
 * @return bool True when called while the 'get_the_excerpt' filter is running. 'get_the_excerpt' フィルター実行中に呼ばれた場合は true。
 */
function veu_is_excerpt() {
	global $wp_current_filter;
	if ( in_array( 'get_the_excerpt', (array) $wp_current_filter ) ) {
		return true;
	}
	return false;
}
