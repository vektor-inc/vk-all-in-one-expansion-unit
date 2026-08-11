<?php
/**
 * e2e テスト専用 mu-plugin: CTA 投稿タイプをクエリパラメータ付きの時だけクラシックエディタにする.
 *
 * CTA のメタボックス ( vkExUnit_cta_url ) は __back_compat_meta_box => true で登録されているため、
 * ブロックエディタ画面では描画されない ( class-vk-call-to-action.php参照 )。
 * 画像位置 ( vkExUnit_cta_img_position ) left / center / right の保存を e2e で検証するには、
 * クラシックエディタ ( メタボックス経由 ) の保存フォームをブラウザから叩く必要があるが、
 * 標準の管理画面には CTA をクラシックエディタで開く導線が無い。
 *
 * この mu-plugin は、投稿編集画面の URL にクエリパラメータ `veu_e2e_classic=1` が
 * 付いている時だけ CTA 投稿タイプをクラシックエディタへ切り替える。
 * パラメータが無い通常のアクセス（実際のユーザー操作・他の e2e テスト）には一切影響しない。
 *
 * e2e テスト専用の mu-plugin: switches the CTA post type to the Classic Editor only when the
 * `veu_e2e_classic=1` query parameter is present on the post edit screen.
 *
 * The CTA metabox ( vkExUnit_cta_url ) is registered with __back_compat_meta_box => true
 * ( see class-vk-call-to-action.php ), so it is never rendered in the Block Editor.
 * Verifying the image position ( vkExUnit_cta_img_position ) values left / center / right via e2e
 * requires going through the classic metabox save form in a real browser, but there is no
 * standard way to open CTA in the Classic Editor from the admin UI.
 *
 * Without the query parameter, this has no effect on normal usage or other e2e specs.
 *
 * @package vk-all-in-one-expansion-unit
 */

add_filter(
	'use_block_editor_for_post_type',
	function ( $use_block_editor, $post_type ) {
		// CTA 投稿タイプ以外には一切影響させない.
		// Never affect post types other than CTA.
		if ( 'cta' !== $post_type ) {
			return $use_block_editor;
		}

		// クエリパラメータが無ければ通常通りブロックエディタの判定に従う.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- e2e テスト専用の画面切替パラメータで、値自体を保存・実行に使わないため nonce 検証は不要。
		if ( ! isset( $_GET['veu_e2e_classic'] ) ) {
			return $use_block_editor;
		}

		// クエリパラメータ付きの時だけクラシックエディタを強制する.
		// Force the Classic Editor only when the query parameter is present.
		return false;
	},
	10,
	2
);
