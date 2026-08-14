<?php
/**
 * Share button
 *
 * @package vk-all-in-one-expantion-unit
 */

// global なので $options にすると ExUnit 全体の $options の値を汚染するので $sns_options を使用
$sns_options = veu_get_sns_options();
if ( veu_is_sns_btns_auto_insert() ) {
	if ( ! empty( $sns_options['hook_point'] ) ) {
		$hook_points = explode( "\n", $sns_options['hook_point'] );
		foreach ( $hook_points as $hook_point ) {
			add_action( $hook_point, 'veu_the_sns_btns' );
		}
	} elseif ( 'content' === veu_content_filter_state() ) {
		add_filter( 'the_content', 'veu_add_sns_btns', 200, 1 );
	} else {
		add_action( 'loop_end', 'veu_add_sns_btns_loopend' );
	}
}

/**
 * Fix share button hide option
 */
function veu_fix_sns_btns_hide() {
	$options = veu_get_sns_options();
	if ( ! empty( $options['snsBtn_ignorePosts'] ) ) {
		$ignore_post_ids = explode( ',', $options['snsBtn_ignorePosts'] );
		foreach ( $ignore_post_ids as $ignore_post_id ) {
			$ignore_post_id = trim( $ignore_post_id );
			if ( ! empty( get_post( $ignore_post_id ) ) ) {
				update_post_meta( $ignore_post_id, 'sns_share_botton_hide', true );
			}
		}
		unset( $options['snsBtn_ignorePosts'] );
		update_option( 'vkExUnit_sns_options', $options );
	}
}
add_action( 'admin_init', 'veu_fix_sns_btns_hide' );


/**
 * Display share button on hook point
 *
 * @param object $query : main query.
 * @return void
 */
function veu_the_sns_btns( $query ) {
	echo veu_get_sns_btns();
}

/**
 * Display share button on loop end
 *
 * @param object $query : main query.
 * @return void
 */
function veu_add_sns_btns_loopend( $query ) {
	if ( ! $query->is_main_query() ) {
		return;
	}
	if ( is_front_page() || is_home() || is_404() ) {
		return;
	}
	echo veu_add_sns_btns( '' );
}

/**
 * Display share button on content or fook point
 * 基本的にクラッシックテーマ向け機能
 * 本文下やフックにボタンを表示するかどうか
 * ブロックで配置した分には影響しない
 *
 * @param string $content : post content.
 * @return bool $auto_insert : post content.
 */
function veu_is_sns_btns_auto_insert() {
	$auto_insert = false;
	$options     = veu_get_sns_options();
	if ( ! empty( $options['enableSnsBtns'] ) ) {
		$auto_insert = true;
	}
	return $auto_insert;
}

/**
 * Resolve the reason the share button is hidden for the current post.
 * 現在の投稿でシェアボタンが非表示と判定される理由を特定する。
 *
 * This centralizes the hide logic so veu_is_sns_btns_display() (boolean) and the share button
 * block's editor-only notice (which needs to explain *why* it is hidden) share one source of truth.
 * 判定ロジックを一箇所に集約し、veu_is_sns_btns_display()（真偽値）とシェアボタンブロックの
 * 編集画面専用通知（非表示の理由を説明する必要がある）が同じ判定基準を共有できるようにする。
 *
 * Checked in this order, first match wins:
 * この順にチェックし、最初に一致したものが理由になる：
 * 1. Per-post "Hide setting of share button" ( post meta ) — always applies, regardless of $context ( issue #1213 ).
 *    記事ごとの「シェアボタンの非表示設定」（post meta） — $context に関わらず常に適用（issue #1213 の決定）。
 * 2. 404 page — always applies, regardless of $context.
 *    404 ページ — $context に関わらず常に適用。
 * 3. "Exclude Post Types" option — applies unless $context is 'block' and the "Always display the
 *    share button block" option ( snsBtn_block_ignore_exclude ) is enabled.
 *    「シェアボタンを表示しない投稿タイプ」設定 — $context が 'block' かつ
 *    「シェアボタンブロックを常に表示する」設定（snsBtn_block_ignore_exclude）が有効な場合を除き適用される。
 *
 * @param string $context Calling context. 'auto' ( default ) for automatic insertion ( content filter /
 *                         hook points ), 'block' for the manually placed share button block.
 *                         呼び出し元の文脈。'auto'（既定）は本文下などへの自動挿入、
 *                         'block' はシェアボタンブロック（手動配置）.
 * @return string 'post_meta' | '404' | 'post_type' | '' ( not hidden ).
 */
function veu_get_sns_btns_hidden_reason( $context = 'auto' ) {
	$sns_share_button_hide = get_post_meta( get_the_ID(), 'sns_share_botton_hide', true );

	// カスタムフィールドで非表示の場合は表示しない（文脈に関わらず常に最優先）
	// Hidden by the per-post custom field ( always takes priority, regardless of context ).
	if ( ! empty( $sns_share_button_hide ) ) {
		return 'post_meta';
	}

	// 404ページの内容を G3 ProUnit で指定の記事本文に書き換えた場合に表示されないように（文脈に関わらず常に適用）
	// So it stays hidden even when a 404 page's content is replaced with another post's body via
	// G3 ProUnit ( always applies, regardless of context ).
	if ( is_404() ) {
		return '404';
	}

	$options   = veu_get_sns_options();
	$post_type = vk_get_post_type();
	$post_type = $post_type['slug'];

	// シェアボタンブロック限定で「常に表示する」設定が有効な場合、投稿タイプ除外設定を無視する.
	// For the share button block only, ignore the post type exclusion setting when "always display" is enabled.
	$ignore_post_type_exclude = ( 'block' === $context && ! empty( $options['snsBtn_block_ignore_exclude'] ) );

	// シェアボタンを表示しない投稿タイプが配列で指定されている場合（チェックが入ってたら）.
	if ( ! $ignore_post_type_exclude && ! empty( $options['snsBtn_exclude_post_types'][ $post_type ] ) ) {
		return 'post_type';
	}

	// 上記に該当しない場合は表示.
	return '';
}

/**
 * Check sns btn display
 * シェアボタンを表示するかどうかを判定する
 *
 * @param string $context Calling context passed through to veu_get_sns_btns_hidden_reason().
 *                         'auto' ( default ) or 'block'. Existing no-argument calls keep the previous
 *                         behavior unchanged. See veu_get_sns_btns_hidden_reason() for details.
 *                         veu_get_sns_btns_hidden_reason() へそのまま渡す呼び出し元の文脈。
 *                         'auto'（既定）または 'block'。既存の引数なし呼び出しは従来どおり動作する。
 *                         詳細は veu_get_sns_btns_hidden_reason() を参照.
 * @return bool
 */
function veu_is_sns_btns_display( $context = 'auto' ) {
	return '' === veu_get_sns_btns_hidden_reason( $context );
}

/**
 * Whether the current request is the block editor canvas' server-side render preview.
 * 現在のリクエストがブロックエディタのキャンバスによる ServerSideRender プレビューかどうかを判定する。
 *
 * The block editor canvas ( ServerSideRender ) requests dynamic block markup via the REST API with
 * a `context=edit` query parameter, so a PHP render callback can tell an editor preview apart from
 * an actual front-end request. This check requires all of the following, so the editor-only notice
 * ( which links to the settings screen ) is never returned to an unauthenticated front-end visitor:
 * 1. the request is an actual REST API request,
 * 2. the `context` query var is exactly 'edit', and
 * 3. the current user has permission to edit the post ( or, when there is no post — e.g. the site
 *    editor — permission to edit posts in general, matching how WP_REST_Block_Renderer_Controller
 *    authorizes edit-context block renders ).
 * ブロックエディタのキャンバス（ServerSideRender）は、PHP の描画コールバックが実際の公開画面
 * リクエストと編集画面プレビューを区別できるよう、`context=edit` クエリパラメータ付きで
 * REST API 経由で動的ブロックのマークアップを要求する。設定画面へのリンクを含む編集画面専用の
 * 通知が、権限のない公開画面の閲覧者に返らないよう、以下をすべて満たす場合のみ true を返す。
 * 1. 実際に REST API リクエストであること
 * 2. `context` クエリ変数が厳密に 'edit' であること
 * 3. 現在のユーザーがその投稿を編集する権限を持つこと（投稿が無い場合 — サイトエディター等 —
 *    は投稿を編集する一般的な権限。WP_REST_Block_Renderer_Controller が edit コンテキストの
 *    ブロック描画を許可する考え方に合わせている）
 *
 * Uses wp_is_rest_endpoint() ( WP 6.5+, matching this plugin's minimum supported version ) rather
 * than checking the REST_REQUEST constant directly. This is both more correct and more testable:
 * REST_REQUEST is only defined for a request dispatched directly as a REST request, while
 * wp_is_rest_endpoint() also covers internal dispatches such as rest_preload_api_request() ( it
 * checks $wp_rest_server->is_dispatching() too ), and — since it runs its result through the
 * `wp_is_rest_endpoint` filter — tests can toggle it with a filter instead of defining a PHP
 * constant that can never be undefined again for the rest of the process.
 * REST_REQUEST 定数を直接見るのではなく wp_is_rest_endpoint()（WP 6.5+。このプラグインの
 * 対応最低バージョンと一致）を使う。より正確かつテストしやすい。REST_REQUEST は単独の REST
 * リクエストとしてディスパッチされた場合にしか定義されないが、wp_is_rest_endpoint() は
 * rest_preload_api_request() のような内部ディスパッチも拾う（$wp_rest_server->is_dispatching()
 * も見ているため）。加えて、結果を `wp_is_rest_endpoint` フィルターに通すため、テストは
 * 二度と undefine できない PHP 定数を定義する代わりに、フィルターで切り替えられる。
 *
 * @return bool
 */
function veu_is_block_editor_preview() {
	// ServerSideRender は REST API 経由でしか来ないため、REST リクエストでなければ即座に false.
	// ServerSideRender only ever arrives via the REST API, so bail out immediately for non-REST requests.
	if ( ! wp_is_rest_endpoint() ) {
		return false;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only check of the render context ( no state is read from or written to ).
	if ( ! isset( $_GET['context'] ) || 'edit' !== sanitize_key( wp_unslash( $_GET['context'] ) ) ) {
		return false;
	}

	// 編集画面向けの通知（設定画面への実リンクを含む）なので、編集権限を持つユーザーにのみ返す.
	// This is an editor-only notice ( it includes a real link to the settings screen ), so only return
	// true for a user who has permission to edit.
	$post_id = get_the_ID();
	return $post_id ? current_user_can( 'edit_post', $post_id ) : current_user_can( 'edit_posts' );
}

/**
 * Build the block editor canvas notice shown when the share button block will not appear on the front end.
 * シェアボタンブロックが公開画面に表示されない場合に、ブロックエディタのキャンバスへ表示する通知を組み立てる。
 *
 * Replaces the previous "context=edit bypass", which rendered live share buttons in the editor even
 * when the front end would show nothing — hiding the editor/front-end mismatch from editors.
 * 従来の「context=edit なら判定を素通りする」実装（編集画面だけ実際のボタンが見えてしまい、
 * 公開画面では何も出ない食い違いが編集者に伝わっていなかった）を置き換える。
 *
 * @param string $reason Value returned by veu_get_sns_btns_hidden_reason(): 'post_meta' | '404' | 'post_type' | ''.
 *                        veu_get_sns_btns_hidden_reason() の返り値.
 * @return string Escaped notice HTML, or '' when there is no dedicated message for the reason ( e.g. '404' or '' ).
 */
function veu_sns_btns_editor_notice( $reason ) {

	// 記事単位の非表示設定による場合 ( post meta ).
	// 主語は「シェアボタン」であり、記事自体が公開画面に出ないわけではない事を明確にする.
	// Hidden by the per-post hide setting ( post meta ). The subject is "the share button", not the
	// post itself — the post body still displays normally, only the share button is hidden.
	if ( 'post_meta' === $reason ) {
		return '<div class="veu_share_button_block-notice"><p>'
			. esc_html__( 'The share button will not appear on the front end because of this post\'s "Hide setting of share button".', 'vk-all-in-one-expansion-unit' )
			. '</p></div>';
	}

	// 投稿タイプ除外設定による場合。設定画面への実リンクを添える.
	// 主語は「シェアボタンブロック」であり、この投稿タイプ全体が公開画面に出ないわけではない事を明確にする.
	// Hidden by the "Exclude Post Types" option. Include a real link to the settings screen.
	// The subject is "the share button block", not this post type as a whole.
	if ( 'post_type' === $reason ) {
		$settings_url = esc_url( admin_url( 'admin.php?page=vkExUnit_main_setting#vkExUnit_sns_options' ) );

		// 未エスケープの原文を保持し、用途（aria-label は esc_attr、リンクテキストは esc_html）ごとに
		// 1回だけエスケープする。esc_html__() の戻り値を esc_attr() へ二重に通すと、アポストロフィや
		// & を含む翻訳文字列で実体参照が二重化しうるため.
		// Keep the raw, unescaped strings and escape once per usage ( esc_attr for the aria-label,
		// esc_html for the link text ). Passing esc_html__()'s already-escaped return value through
		// esc_attr() again could double-escape entities in a translation containing an apostrophe or &.
		$link_label   = __( 'Open the SNS settings', 'vk-all-in-one-expansion-unit' );
		$new_tab_note = __( 'Opens in a new tab.', 'vk-all-in-one-expansion-unit' );

		return '<div class="veu_share_button_block-notice"><p>'
			. esc_html__( 'The share button block will not appear on the front end for this post type because of the "Exclude Post Types" setting.', 'vk-all-in-one-expansion-unit' )
			. '</p><p><a href="' . $settings_url . '" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr( $link_label . ' ' . $new_tab_note ) . '">'
			. esc_html( $link_label )
			. '</a></p></div>';
	}

	// 404 など、専用メッセージを用意していない理由の場合は何も表示しない.
	// For reasons without a dedicated message ( e.g. '404' ), show nothing.
	return '';
}

/**
 * シェアボタンのCSS
 *
 * @param array $options : オプション値.
 * @return string $outer_css : style
 */
function veu_sns_outer_css( $options ) {

	// snsBtn_bg_fill_not が定義されている場合.
	$sns_btn_bg_fill_not = false;
	if ( ! empty( $options['snsBtn_bg_fill_not'] ) ) {
		$sns_btn_bg_fill_not = true;
	}

	// snsBtn_color が定義されている場合.
	if ( isset( $options['snsBtn_color'] ) ) {
		$sns_btn_color = esc_html( $options['snsBtn_color'] );
	} else {
		$sns_btn_color = '';
	}

	// 背景塗り && 色指定がない場合.
	if ( ! $sns_btn_bg_fill_not && ! $sns_btn_color ) {
		// （ ExUnitのCSSファイルに書かれている色が適用されているので個別には出力しなくてよい ）
		$outer_css = '';

		// 背景なし枠線の場合.
	} elseif ( $sns_btn_bg_fill_not ) {
		// 色指定がない場合.
		if ( ! $sns_btn_color ) {
			$sns_btn_color = '#ccc';
		}
		$outer_css = ' style="border:1px solid ' . $sns_btn_color . ';background:none;box-shadow: 0 2px 0 rgba(0,0,0,0.15);"';

		// それ以外（ 背景塗りの時 ）.
	} else {
		$outer_css = ' style="border:1px solid ' . $sns_btn_color . ';background-color:' . $sns_btn_color . ';box-shadow: 0 2px 0 rgba(0,0,0,0.15);"';
	}
	return $outer_css;
}

/**
 * シェアボタンのアイコンと文字部分のCSS
 *
 * @param array $options : オプション値.
 * @return string $style : style
 */
function veu_sns_icon_css( $options ) {
	// snsBtn_bg_fill_not が定義されている場合.
	$sns_btn_bg_fill_not = '';
	if ( ! empty( $options['snsBtn_bg_fill_not'] ) ) {
		$sns_btn_bg_fill_not = true;
	}

	// snsBtn_color が定義されている場合.
	if ( isset( $options['snsBtn_color'] ) ) {
		$style = esc_html( $options['snsBtn_color'] );
	} else {
		$style = '';
	}

	if ( ! $sns_btn_bg_fill_not && ! $style ) {
		$style = '';
	} elseif ( $sns_btn_bg_fill_not ) {
		// 線のとき.
		if ( ! $style ) {
			$style = '#ccc';
		}
		$style = ' style="color:' . $style . ';"';
	} else {
		// 塗りのとき.
		$style = ' style="color:#fff;"';
	}
	return $style;
}

/**
 * Threads シェアボタンのアイコン SVG
 * 自前フォント vk_sns に字形がないため Font Awesome の fa-brands fa-threads を使っていたが、
 * 本プラグインは Font Awesome を読み込んでおらず、テーマ側の読み込みに依存していたため、
 * Font Awesome 未読み込みのテーマ（Twenty Twenty-Five など）でアイコンが表示されない不具合があった。
 * Font Awesome に依存しないよう、インライン SVG に置き換える。
 * 素材の出典: Simple Icons（ https://simpleicons.org/ ）の threads.svg。
 * ライセンス: CC0 1.0 Universal（ https://github.com/simple-icons/simple-icons/blob/develop/LICENSE.md ）。
 * このライセンスは SVG（トレース成果物）の著作権表示に関するもので、Threads ロゴ自体の商標権は
 * Meta に帰属したまま。単色・形状非改変・シェア導線としての用途に限定して使用している。
 * The in-house web font ( vk_sns ) has no glyph for Threads, so the Font Awesome brand icon
 * fa-brands fa-threads was used. This plugin does not load Font Awesome itself and relied on
 * the theme loading it, so the icon did not render on themes that do not load Font Awesome
 * ( e.g. Twenty Twenty-Five ). Replace it with an inline SVG so it no longer depends on Font Awesome.
 * Source: Simple Icons ( https://simpleicons.org/ ) threads.svg.
 * License: CC0 1.0 Universal ( https://github.com/simple-icons/simple-icons/blob/develop/LICENSE.md ).
 * This license covers the SVG ( the traced artwork ) copyright notice only; the Threads logo
 * itself remains a trademark of Meta. Used single-color, unmodified in shape, and only as a
 * share link icon.
 * width / height を明示するのは、CSS が当たらない経路（RSS フィード・CSS ツリーシェイキング等）で
 * 置換要素の既定サイズ規則により正方形が大きく肥大表示されるのを防ぐため。
 * Explicit width / height prevent the default replaced-element sizing rules from rendering an
 * oversized square when CSS does not apply ( e.g. RSS feeds, CSS tree-shaking ).
 *
 * @return string SVG markup.
 */
function veu_sns_icon_svg_threads() {
	return '<svg class="sb_svg_icon" width="1em" height="1em" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"><path d="M18.263 11.097c-.03-3.486-1.92-5.586-5.111-5.586-2.13 0-3.922.963-4.863 2.499l2.062 1.438c.535-.843 1.272-1.543 2.628-1.543 1.528 0 2.318.85 2.544 2.431a15 15 0 0 0-2.236-.173c-4.125 0-6.068 1.867-6.068 4.336s1.943 3.99 4.804 3.99c3.139 0 5.013-2.115 5.781-4.735.798.361 1.348 1.204 1.348 2.47 0 3.387-3.907 5.232-7.22 5.232-4.885 0-8.077-3.207-8.077-8.424 0-6.392 4.223-10.487 9.9-10.487 3.808 0 5.69 1.671 6.97 3.914l2.108-1.475C21.44 2.078 18.331 0 13.663 0 6.227 0 1.168 5.277 1.168 12.934c0 7 4.953 11.066 10.856 11.066 4.878 0 9.809-2.846 9.809-7.716 0-2.545-1.46-4.231-3.569-5.187m-6.33 4.855c-1.077 0-2.026-.512-2.026-1.453 0-1.483 1.822-1.934 3.606-1.934.678 0 1.34.045 1.927.173-.422 1.927-1.671 3.215-3.508 3.214Z"/></svg>';
}

/**
 * Copy（コピー）シェアボタンのアイコン SVG
 * 自前フォント vk_sns に字形がなく、Font Awesome の fas fa-copy に実質依存していたため、
 * Threads と同様に Font Awesome 未読み込みのテーマでアイコンが表示されない不具合があった。
 * Font Awesome に依存しないよう、インライン SVG に置き換える。
 * 素材の出典: Heroicons（ https://heroicons.com/ ）の square-2-stack（ solid ）。
 * ライセンス: MIT License（ https://github.com/tailwindlabs/heroicons/blob/master/LICENSE ）。
 * The in-house web font ( vk_sns ) has no glyph for this either, and it effectively depended on
 * the Font Awesome fas fa-copy icon, so it had the same missing-icon issue as Threads on themes
 * that do not load Font Awesome. Replace it with an inline SVG so it no longer depends on Font Awesome.
 * Source: Heroicons ( https://heroicons.com/ ) square-2-stack ( solid ).
 * License: MIT License ( https://github.com/tailwindlabs/heroicons/blob/master/LICENSE ).
 * width / height を明示するのは、CSS が当たらない経路（RSS フィード・CSS ツリーシェイキング等）で
 * 置換要素の既定サイズ規則により正方形が大きく肥大表示されるのを防ぐため。
 * Explicit width / height prevent the default replaced-element sizing rules from rendering an
 * oversized square when CSS does not apply ( e.g. RSS feeds, CSS tree-shaking ).
 *
 * @return string SVG markup.
 */
function veu_sns_icon_svg_copy() {
	return '<svg class="sb_svg_icon" width="1em" height="1em" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"><path d="M16.5 6a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3v7.5a3 3 0 0 0 3 3v-6A4.5 4.5 0 0 1 10.5 6h6Z"/><path d="M18 7.5a3 3 0 0 1 3 3V18a3 3 0 0 1-3 3h-7.5a3 3 0 0 1-3-3v-7.5a3 3 0 0 1 3-3H18Z"/></svg>';
}

/**
 * Share button html
 * シェアボタンの HTML を組み立てる
 *
 * @param array $attr : class / position and so on. Pass 'context' => 'block' when called from the
 *                       share button block's render callback so the block-only exception and the
 *                       editor notice can be applied. 位置・クラスなどの属性。シェアボタンブロックの
 *                       描画コールバックから呼ぶ場合はブロック限定の例外・編集画面通知を適用できるよう
 *                       'context' => 'block' を渡す.
 * @return string Button DOM, an editor-only notice, or an empty string.
 */
function veu_get_sns_btns( $attr = array() ) {

	$options   = veu_get_sns_options();
	$outer_css = veu_sns_outer_css( $options );
	$icon_css  = veu_sns_icon_css( $options );

	$link_url   = rawurlencode( get_permalink() );
	$page_title = rawurlencode( veu_get_the_sns_title() );

	$classes     = '';
	$social_btns = '';

	// 呼び出し元の文脈（既定は 'auto'。シェアボタンブロックからは 'block' が渡される）.
	// Calling context ( defaults to 'auto'. The share button block passes 'block' ).
	$context = isset( $attr['context'] ) ? $attr['context'] : 'auto';

	$hidden_reason = veu_get_sns_btns_hidden_reason( $context );

	// 個別の記事で ボタンを表示する指定にしてある場合.
	if ( '' === $hidden_reason ) {
		if ( function_exists( 'veu_add_common_attributes_class' ) ) {
			$classes .= veu_add_common_attributes_class( $classes, $attr );
		}

		if ( isset( $attr['position'] ) ) {
			$classes .= ' veu_socialSet-position-' . $attr['position'];
		}
		if ( isset( $attr['className'] ) ) {
			$classes .= ' ' . $attr['className'];
		}

		$auto_class = ( isset( $attr['auto'] ) && $attr['auto'] ) ? ' veu_socialSet-auto' : '';

		$social_btns = '<div class="veu_socialSet' . $auto_class . esc_attr( $classes ) . ' veu_contentAddSection"><script>window.twttr=(function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],t=window.twttr||{};if(d.getElementById(id))return t;js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);t._e=[];t.ready=function(f){t._e.push(f);};return t;}(document,"script","twitter-wjs"));</script><ul>';
		// facebook.
		if ( ! empty( $options['useFacebook'] ) ) {
			$social_btns .= '<li class="sb_facebook sb_icon">';
			$social_btns .= '<a class="sb_icon_inner" href="' . esc_url( '//www.facebook.com/sharer.php?src=bm&u=' . $link_url . '&t=' . $page_title ) . '" target="_blank" ' . $outer_css . 'onclick="window.open(this.href,\'FBwindow\',\'width=650,height=450,menubar=no,toolbar=no,scrollbars=yes\');return false;">';
			// 字形を CSS 疑似要素で描く自前 web フォント（vk_sns）アイコン。隣に可視ラベル（.sns_txt "Facebook"）があるため装飾扱いで読み上げから除外する。
			// In-house web font ( vk_sns ) icon whose glyph is drawn via a CSS pseudo-element. The visible label ( .sns_txt "Facebook" ) sits next to it, so it is decorative and hidden from screen readers.
			$social_btns .= '<span class="vk_icon_w_r_sns_fb icon_sns" aria-hidden="true"' . $icon_css . '></span>';
			$social_btns .= '<span class="sns_txt"' . $icon_css . '>Facebook</span>';
			$social_btns .= '<span class="veu_count_sns_fb"' . $icon_css . '></span>';
			$social_btns .= '</a>';
			$social_btns .= '</li>';
		}

		// X.
		if ( ! empty( $options['useTwitter'] ) ) {
			$social_btns .= '<li class="sb_x_twitter sb_icon">';
			$social_btns .= '<a class="sb_icon_inner" href="' . esc_url( '//twitter.com/intent/tweet?url=' . $link_url . '&text=' . $page_title ) . '" target="_blank" ' . $outer_css . '>';
			// 自前 web フォント（vk_sns）のアイコン。隣に可視ラベル（.sns_txt "X"）があるため装飾扱いで読み上げから除外する。
			// In-house web font ( vk_sns ) icon. The visible label ( .sns_txt "X" ) sits next to it, so it is decorative and hidden from screen readers.
			$social_btns .= '<span class="vk_icon_w_r_sns_x_twitter icon_sns" aria-hidden="true"' . $icon_css . '></span>';
			$social_btns .= '<span class="sns_txt"' . $icon_css . '>X</span>';
			$social_btns .= '</a>';
			$social_btns .= '</li>';
		}

		// bluesky.
		// 「タイトル + 改行(%0A) + URL」の %0A は esc_url() だと除去されてしまうため、
		// rawurlencode() 済みの構成要素を esc_attr() で属性エスケープする（Threads も同様）
		// esc_url() strips the %0A of "title + line break (%0A) + URL", so escape the
		// rawurlencode()-ed parts with esc_attr() for the attribute instead ( same for Threads ).
		if ( ! empty( $options['useBluesky'] ) ) {
			$social_btns .= '<li class="sb_bluesky sb_icon">';
			$social_btns .= '<a class="sb_icon_inner" href="' . esc_attr( 'https://bsky.app/intent/compose?text=' . $page_title . '%0A' . $link_url ) . '" target="_blank" ' . $outer_css . '>';
			// 自前 web フォント（vk_sns）のアイコン。隣に可視ラベル（.sns_txt "Bluesky"）があるため装飾扱いで読み上げから除外する。
			// In-house web font ( vk_sns ) icon. The visible label ( .sns_txt "Bluesky" ) sits next to it, so it is decorative and hidden from screen readers.
			$social_btns .= '<span class="vk_icon_w_r_sns_bluesky icon_sns" aria-hidden="true"' . $icon_css . '></span>';
			$social_btns .= '<span class="sns_txt"' . $icon_css . '>Bluesky</span>';
			$social_btns .= '</a>';
			$social_btns .= '</li>';
		}

		// threads.
		// Threads の共有ボタン。投稿画面を開く intent URL に「タイトル + 改行(%0A) + URL」を渡す。
		// Threads share button. Pass "title + line break (%0A) + URL" to the intent URL that opens the compose screen.
		// アイコンは Font Awesome 非依存のインライン SVG（ veu_sns_icon_svg_threads() ）を使用する。
		// Use a Font Awesome independent inline SVG ( veu_sns_icon_svg_threads() ) for the icon.
		if ( ! empty( $options['useThreads'] ) ) {
			$social_btns .= '<li class="sb_threads sb_icon">';
			$social_btns .= '<a class="sb_icon_inner" href="' . esc_attr( 'https://www.threads.net/intent/post?text=' . $page_title . '%0A' . $link_url ) . '" target="_blank" ' . $outer_css . '>';
			// 隣に可視ラベル（.sns_txt "Threads"）があるためアイコンは装飾。読み上げから除外する。
			// 他5つの SNS アイコンに揃え、aria-hidden は外側の span に統一する。
			// The visible label ( .sns_txt "Threads" ) sits next to it, so the icon is decorative and hidden from screen readers.
			// aria-hidden is placed on the outer span to match the other 5 SNS icons.
			$social_btns .= '<span class="icon_sns" aria-hidden="true"' . $icon_css . '>' . veu_sns_icon_svg_threads() . '</span>';
			$social_btns .= '<span class="sns_txt"' . $icon_css . '>Threads</span>';
			$social_btns .= '</a>';
			$social_btns .= '</li>';
		}

		// hatena.
		if ( ! empty( $options['useHatena'] ) ) {
			$social_btns .= '<li class="sb_hatena sb_icon">';
			$social_btns .= '<a class="sb_icon_inner" href="' . esc_url( '//b.hatena.ne.jp/add?mode=confirm&url=' . $link_url . '&title=' . $page_title ) . '" target="_blank" ' . $outer_css . ' onclick="window.open(this.href,\'Hatenawindow\',\'width=650,height=450,menubar=no,toolbar=no,scrollbars=yes\');return false;">';
			// 自前 web フォント（vk_sns）のアイコン。隣に可視ラベル（.sns_txt "Hatena"）があるため装飾扱いで読み上げから除外する。
			// In-house web font ( vk_sns ) icon. The visible label ( .sns_txt "Hatena" ) sits next to it, so it is decorative and hidden from screen readers.
			$social_btns .= '<span class="vk_icon_w_r_sns_hatena icon_sns" aria-hidden="true"' . $icon_css . '></span>';
			$social_btns .= '<span class="sns_txt"' . $icon_css . '>Hatena</span>';
			$social_btns .= '<span class="veu_count_sns_hb"' . $icon_css . '></span>';
			$social_btns .= '</a>';
			$social_btns .= '</li>';
		}

		// line.
		// line: は esc_url() のデフォルト許可プロトコル外で空文字になるため、許可プロトコルを明示する
		// The line: scheme is not in esc_url()'s default allowed protocols ( the URL would become
		// an empty string ), so pass the allowed protocol explicitly.
		if ( wp_is_mobile() && ! empty( $options['useLine'] ) ) :
			$social_btns .= '<li class="sb_line sb_icon">';
			$social_btns .= '<a class="sb_icon_inner"  href="' . esc_url( 'line://msg/text/' . $page_title . ' ' . $link_url, array( 'line' ) ) . '" ' . $outer_css . '>';
			// 自前 web フォント（vk_sns）のアイコン。隣に可視ラベル（.sns_txt "LINE"）があるため装飾扱いで読み上げから除外する。
			// In-house web font ( vk_sns ) icon. The visible label ( .sns_txt "LINE" ) sits next to it, so it is decorative and hidden from screen readers.
			$social_btns .= '<span class="vk_icon_w_r_sns_line icon_sns" aria-hidden="true"' . $icon_css . '></span>';
			$social_btns .= '<span class="sns_txt"' . $icon_css . '>LINE</span>';
			$social_btns .= '</a>';
			$social_btns .= '</li>';
		endif;
		// copy.
		// アイコンは Font Awesome 非依存のインライン SVG（ veu_sns_icon_svg_copy() ）を使用する。
		// Use a Font Awesome independent inline SVG ( veu_sns_icon_svg_copy() ) for the icon.
		if ( ! empty( $options['useCopy'] ) ) {
			$social_btns .= '<li class="sb_copy sb_icon">';
			$social_btns .= '<button class="copy-button sb_icon_inner"' . $outer_css . 'data-clipboard-text="' . esc_attr( urldecode( $page_title ) ) . ' ' . esc_attr( urldecode( $link_url ) ) . '">';
			// 隣に可視ラベル（.sns_txt "Copy"）があるためアイコンは装飾。読み上げから除外する。
			// 他5つの SNS アイコンに揃え、aria-hidden は外側の span に統一する。
			// The visible label ( .sns_txt "Copy" ) sits next to it, so the icon is decorative and hidden from screen readers.
			// aria-hidden is placed on the outer span to match the other 5 SNS icons.
			$social_btns .= '<span class="icon_sns" aria-hidden="true"' . $icon_css . '>' . veu_sns_icon_svg_copy() . '</span>';
			$social_btns .= '<span class="sns_txt"' . $icon_css . '>Copy</span>';
			$social_btns .= '</button>';
			$social_btns .= '</li>';
		}

		$social_btns .= '</ul></div><!-- [ /.socialSet ] -->';
	} elseif ( 'block' === $context && veu_is_block_editor_preview() ) {
		// ブロックエディタのキャンバスでは、公開画面と一致しない実際のボタンを描画する代わりに、
		// 非表示の理由と対処方法を伝える通知を表示する（旧 "context=edit バイパス" の置き換え）。
		// In the block editor canvas, show a notice explaining why ( and how to fix ) it is hidden,
		// instead of rendering live buttons that would not match the front end ( replaces the old
		// "context=edit bypass" ).
		$social_btns = veu_sns_btns_editor_notice( $hidden_reason );
	}

	return $social_btns;
}

/**
 * Add sns btn to $content
 *
 * @param string $content : post content.
 * @return string $content add sns btns
 */
function veu_add_sns_btns( $content ) {

	// ウィジェットなら表示しない.
	global $is_pagewidget;
	if ( $is_pagewidget ) {
		return $content;
	}

	// 抜粋でも表示しない.
	if ( function_exists( 'vk_is_excerpt' ) ) {
		if ( vk_is_excerpt() ) {
			return $content;
		}
	}

	// アーカイブページでも表示しない.
	if ( is_archive() ) {
		return $content;
	}

	// フォーム内など不適切なループ外で混入するのを防ぐ
	if ( ! apply_filters( 'veu_sns_btns_check_mainloop', in_the_loop() && is_main_query() ) ) {
		return $content;
	}

	// フォーム内の自動挿入SNSボタンを表示しない.
	if ( strpos( $content, '<form' ) !== false ) {
		$form_start = strpos( $content, '<form' );
		if ( preg_match( '/<\/form>/', $content, $matches, PREG_OFFSET_CAPTURE, $form_start ) ) {
			$form_end = $matches[0][1] + strlen( $matches[0][0] );
		} else {
			return $content; // </form> が見つからない場合はそのまま返す
		}

		$form_inner = substr( $content, $form_start, $form_end - $form_start );

		// veu_socialSet-auto クラスを含む要素だけ削除（ブロックは残す）
		$form_inner_cleaned = preg_replace(
			'/<div[^>]+class="[^"]*veu_socialSet-auto[^"]*"[^>]*>.*?<\/div>/s',
			'',
			$form_inner
		);

		$content = substr( $content, 0, $form_start ) . $form_inner_cleaned . substr( $content, $form_end );
	}

	if ( veu_is_sns_btns_display() ) {
		$options = veu_get_sns_options();

		if ( ! empty( $options['snsBtn_position']['before'] ) ) {
			$content = veu_get_sns_btns(
				array(
					'position' => 'before',
					'auto'     => true,
				)
			) . $content;
		}

		if ( ! empty( $options['snsBtn_position']['after'] ) ) {
			$content .= veu_get_sns_btns(
				array(
					'position' => 'after',
					'auto'     => true,
				)
			);
		}
	}

	return $content;
}

add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'vk_ex_unit/v1',
			'/hatena_entry/(?P<linkurl>.+)',
			array(
				'methods'             => 'GET',
				'callback'            => 'vew_sns_hatena_restapi_callback',
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			'vk_ex_unit/v1',
			'/hatena_entry',
			array(
				'methods'             => 'POST',
				'callback'            => 'vew_sns_hatena_restapi_callback',
				'args'                => array(
					'linkurl' => array(
						'description' => 'linkurl',
						'required'    => true,
						'type'        => 'string',
					),
				),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			'vk_ex_unit/v1',
			'/facebook_entry/(?P<linkurl>.+)',
			array(
				'methods'             => 'GET',
				'callback'            => 'vew_sns_facebook_restapi_callback',
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			'vk_ex_unit/v1',
			'/facebook_entry',
			array(
				'methods'             => 'POST',
				'callback'            => 'vew_sns_facebook_restapi_callback',
				'args'                => array(
					'linkurl' => array(
						'description' => 'linkurl',
						'required'    => true,
						'type'        => 'string',
					),
				),
				'permission_callback' => '__return_true',
			)
		);
	}
);

add_filter(
	'vkExUnit_master_js_options',
	function ( $options ) {
		$opt                              = veu_get_sns_options();
		$options['hatena_entry']          = get_rest_url( 0, 'vk_ex_unit/v1/hatena_entry/' );
		$options['facebook_entry']        = get_rest_url( 0, 'vk_ex_unit/v1/facebook_entry/' );
		$options['facebook_count_enable'] = false;
		$options['entry_count']           = (bool) ( 'disable' !== $opt['entry_count'] );
		$options['entry_from_post']       = (bool) ( 'post' === $opt['entry_count'] );

		$opt = veu_get_sns_options();
		if ( ! empty( $opt['fbAccessToken'] ) ) {
			$options['facebook_count_enable'] = true;
		}
		return $options;
	},
	10,
	1
);

/**
 * Hatena count
 *
 * @param string $data : Setting parametor ( url and so on ).
 * @return string api response
 */
function vew_sns_hatena_restapi_callback( $data ) {

	$siteurl = get_site_url();

	// Avoiding Apache config "AllowEncodedSlashes" option issue
	$link_url = str_replace( '-#-', '/', urldecode( $data['linkurl'] ) );

	// ホスト名のみを抽出して厳密に比較する。
	// 部分文字列一致 (strpos) では example.com.attacker.com のような
	// 攻撃者制御下のドメインで自サイトのホスト名を含めることでバリデーションを
	// 迂回されうるため、wp_parse_url() でホスト名のみを取り出し、
	// 大文字小文字を無視して完全一致 (strcasecmp) で判定する。サブドメインは許可しない。
	// Extract only the host name and compare strictly. Substring matching with
	// strpos can be bypassed by attacker-controlled domains such as
	// example.com.attacker.com that contain the site's host name, so we extract
	// the host with wp_parse_url() and compare for an exact, case-insensitive
	// match. Subdomains are not allowed.
	$link_host = wp_parse_url( $link_url, PHP_URL_HOST );
	$site_host = wp_parse_url( $siteurl, PHP_URL_HOST );

	if ( empty( $link_host ) || empty( $site_host ) || 0 !== strcasecmp( $link_host, $site_host ) ) {
		$response = new WP_REST_Response( array() );
		$response->set_status( 403 );
		return $response;
	}

	$link_url = urlencode( $link_url );

	$r = wp_safe_remote_get( 'https://bookmark.hatenaapis.com/count/entry?url=' . $link_url );

	if ( ! is_wp_error( $r ) ) {
		$response = new WP_REST_Response( array( 'count' => $r['body'] ) );
		if ( 'GET' === $data->get_method() ) {
			if ( empty( $r['headers']['cache-control'] ) ) {
				$cache_control = 'Cache-Control: public, max-age=3600, s-maxage=3600';
			} else {
				$cache_control = $r['headers']['cache-control'];
			}
			$response->header( 'Cache-Control', $cache_control );
		} else {
			$response->header( 'Cache-Control', 'no-cache' );
		}
		$response->set_status( 200 );
		return $response;
	}
	$response = new WP_REST_Response( array( 'errors' => array( 'Service Unavailable' ) ) );
	$response->set_status( 503 );

	return $response;
}

/**
 * Facebook count
 *
 * @param string $data : Setting parametor ( url and so on ).
 * @return string api response
 */
function vew_sns_facebook_restapi_callback( $data ) {

	$siteurl = get_site_url();

	// Avoiding Apache config "AllowEncodedSlashes" option issue
	$link_url = str_replace( '-#-', '/', urldecode( $data['linkurl'] ) );

	// ホスト名のみを抽出して厳密に比較する。
	// 部分文字列一致 (strpos) では example.com.attacker.com のような
	// 攻撃者制御下のドメインで自サイトのホスト名を含めることでバリデーションを
	// 迂回されうるため、wp_parse_url() でホスト名のみを取り出し、
	// 大文字小文字を無視して完全一致 (strcasecmp) で判定する。サブドメインは許可しない。
	// Extract only the host name and compare strictly. Substring matching with
	// strpos can be bypassed by attacker-controlled domains such as
	// example.com.attacker.com that contain the site's host name, so we extract
	// the host with wp_parse_url() and compare for an exact, case-insensitive
	// match. Subdomains are not allowed.
	$link_host = wp_parse_url( $link_url, PHP_URL_HOST );
	$site_host = wp_parse_url( $siteurl, PHP_URL_HOST );

	if ( empty( $link_host ) || empty( $site_host ) || 0 !== strcasecmp( $link_host, $site_host ) ) {
		$response = new WP_REST_Response( array() );
		$response->set_status( 403 );
		return $response;
	}

	$link_url = urlencode( $link_url );

	$options = veu_get_sns_options();
	if ( empty( $options['fbAccessToken'] ) ) {
		$response = new WP_REST_Response( array( 'errors' => array( 'Service Unavailable' ) ) );
		$response->set_status( 503 );
		return $response;
	}

	$r = wp_safe_remote_get( 'https://graph.facebook.com/?fields=engagement&access_token=' . $options['fbAccessToken'] . '&id=' . $link_url );

	if ( ! is_wp_error( $r ) ) {
		$j = json_decode( $r['body'] );

		if ( isset( $j->engagement->share_count ) ) {
			$response = new WP_REST_Response( array( 'count' => $j->engagement->share_count ) );
			if ( 'GET' === $data->get_method() ) {
				$response->header( 'Cache-Control', 'Cache-Control: public, max-age=3600, s-maxage=3600' );
			} else {
				$response->header( 'Cache-Control', 'no-cache' );
			}
			$response->set_status( 200 );
			return $response;
		}
	}
	$response = new WP_REST_Response( array( 'errors' => array( 'Service Unavailable' ) ) );
	$response->set_status( 503 );

	return $response;
}
