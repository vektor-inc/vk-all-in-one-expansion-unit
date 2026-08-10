<?php
/**
 * e2e テスト専用 mu-plugin: CTA 画像位置のサニタイズを wp-cli 実行時のみ無効化する.
 *
 * #1438 で CTA 画像位置 ( vkExUnit_cta_img_position ) の保存経路に許可値の検証が入ったため、
 * 通常の保存 API ( REST / クラシックの管理画面フォーム ) では不正な値を DB に保存できなくなった。
 * A-2（出力時エスケープ）の e2e ケースは「不正値が既に DB に保存されている状態」を検証対象と
 * するため、その状態を意図的に作る手段が必要になる。
 *
 * `register_post_meta( 'cta', 'vkExUnit_cta_img_position', ... )` は post_type ( subtype ) を
 * 指定して登録されているため、sanitize_meta() は `sanitize_post_meta_vkExUnit_cta_img_position_for_cta`
 * フィルタが存在すればそちらだけを見て早期 return する。
 * `sanitize_post_meta_vkExUnit_cta_img_position` ( _for_cta の付かない汎用名 ) に掛けても効かない。
 *
 * 発動条件は下記の両方を満たす場合のみとし、Web リクエスト ( フロント表示・REST 保存 ) には
 * 絶対に効かせない。効かせてしまうと #1438 の検証そのものが無意味になる。
 *   1. wp-cli 経由の実行であること ( defined('WP_CLI') && WP_CLI )
 *   2. 専用の環境変数 VEU_E2E_BYPASS_CTA_SANITIZE が指定されていること
 *
 * e2e テスト専用の mu-plugin: disables the CTA image position sanitization only during
 * WP-CLI execution.
 *
 * #1438 added allowlist validation to the CTA image position ( vkExUnit_cta_img_position ) save
 * path, so normal save APIs ( REST / the classic admin form ) can no longer persist an invalid
 * value. The A-2 e2e case ( output-time escaping ) needs to reproduce "an invalid value is
 * already stored in the DB", which requires a deliberate way to create that state.
 *
 * Because `register_post_meta( 'cta', 'vkExUnit_cta_img_position', ... )` registers a
 * post_type ( subtype ), sanitize_meta() only looks at
 * `sanitize_post_meta_vkExUnit_cta_img_position_for_cta` and returns early when it exists;
 * hooking the generic `sanitize_post_meta_vkExUnit_cta_img_position` ( without `_for_cta` )
 * has no effect.
 *
 * This only activates when BOTH of the following are true, and must never activate for a
 * web request ( front-end rendering / REST save ) — doing so would make the #1438 validation
 * meaningless:
 *   1. Running via WP-CLI ( defined('WP_CLI') && WP_CLI )
 *   2. The dedicated environment variable VEU_E2E_BYPASS_CTA_SANITIZE is set
 *
 * @package vk-all-in-one-expansion-unit
 */

// WP-CLI 実行かつ専用の環境変数が指定されている時だけ発動する.
// Web リクエストではこの分岐に入らないため、フロント表示・REST 保存の検証には一切影響しない.
if ( defined( 'WP_CLI' ) && WP_CLI && getenv( 'VEU_E2E_BYPASS_CTA_SANITIZE' ) ) {
	add_action(
		'init',
		function () {
			// veu_register_active_feature_meta() ( inc/block-editor-panels/enqueue.php ) が
			// 'init' の既定優先度 (10) で register_post_meta() を呼びフィルタを追加するため、
			// それより後の優先度 (20) で除去する.
			// veu_register_active_feature_meta() ( inc/block-editor-panels/enqueue.php ) runs on
			// 'init' at the default priority (10) and adds the filter via register_post_meta(),
			// so remove it at a later priority (20) to run after that registration.
			remove_all_filters( 'sanitize_post_meta_vkExUnit_cta_img_position_for_cta' );
		},
		20
	);
}
