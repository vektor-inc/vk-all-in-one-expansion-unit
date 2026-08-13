<?php
/**
 * e2e テスト専用 mu-plugin: 投稿編集画面のエディターキャンバスを非 iframe 化する ( #1452 ).
 *
 * Gutenberg のブロックエディタは、編集中の投稿に存在する「すべてのブロックが
 * apiVersion 3 以上」の場合にのみキャンバスを iframe 化する
 * ( wp-includes/js/dist/edit-post.js の useShouldIframe を参照。ブロックが
 * 1 つも無い新規投稿では Array.prototype.every() が空配列に対して true を返すため、
 * 既定のまま何も挿入しなければ常に iframe 化される )。
 *
 * #1452 で問題になった「キャンバスが iframe 化されていない環境」を e2e で
 * 再現するため、apiVersion 2 のダミーブロックをクライアント側にのみ登録する。
 * このブロックが本文に 1 つあるだけで上記の判定が false になり、キャンバスが
 * 非 iframe 化される（サーバー側の register_block_type は不要。この判定は
 * クライアント側のブロックタイプレジストリのみを見るため）。save() は null
 * （動的ブロック扱い。他の対象ブロックと同じ ServerSideRender 系のパターン）
 * にしているため、投稿本文に自己終了タグ `<!-- wp:veu-e2e/legacy-canvas-block /-->`
 * を直接書き込むだけで済み、ブロックの内容検証（invalid block）の対象にもならない。
 *
 * ただし Gutenberg プラグインが有効な環境では常に iframe 化されるため
 * （`useShouldIframe` の `isGutenbergPlugin` 判定）、この手法は使えない。その場合
 * spec の `iframe[name="editor-canvas"]` の count 0 アサーションで検知される。
 *
 * このスクリプトは e2e テストの `veu_e2e_force_non_iframed_canvas` クエリ
 * パラメータが付いている投稿編集画面でのみ読み込まれる（パラメータが無い通常の
 * アクセス・他の e2e スペックには一切影響しない）。
 *
 * e2e-only mu-plugin: registers a client-side-only dummy block ( apiVersion 2 )
 * so that inserting it forces the editor canvas out of iframe mode ( see
 * useShouldIframe in wp-includes/js/dist/edit-post.js — the canvas is iframed
 * only when every block present has apiVersion >= 3 ). Only loaded when the
 * `veu_e2e_force_non_iframed_canvas` query parameter is present.
 *
 * @package vk-all-in-one-expansion-unit
 */

add_action(
	'enqueue_block_editor_assets',
	function () {
		// クエリパラメータが無ければ何もしない（通常の投稿編集・他の e2e スペックには影響させない）。
		// Do nothing unless the e2e-only query parameter is present.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- e2e テスト専用の画面切替パラメータで、値自体を保存・実行に使わないため nonce 検証は不要。
		if ( ! isset( $_GET['veu_e2e_force_non_iframed_canvas'] ) ) {
			return;
		}

		$handle = 'veu-e2e-legacy-canvas-block';

		// src を false にして「インラインスクリプトのみを乗せるハンドル」として登録する
		// （webpack ビルド不要。wp-blocks / wp-element / wp-block-editor のグローバルのみで完結する）。
		wp_register_script(
			$handle,
			false,
			array( 'wp-blocks', 'wp-element', 'wp-block-editor' ),
			'1.0.0',
			true
		);
		wp_add_inline_script(
			$handle,
			"wp.blocks.registerBlockType( 'veu-e2e/legacy-canvas-block', {
				apiVersion: 2,
				title: 'VEU E2E Legacy Canvas Marker',
				category: 'text',
				edit: function () {
					return wp.element.createElement(
						'p',
						wp.blockEditor.useBlockProps(),
						'VEU E2E legacy canvas marker block'
					);
				},
				// 動的ブロック扱い（save 無し）。本文には自己終了タグ
				// `<!-- wp:veu-e2e/legacy-canvas-block /-->` を直接書き込む。
				save: function () {
					return null;
				},
			} );"
		);
		wp_enqueue_script( $handle );
	}
);
