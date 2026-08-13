<?php
/**
 * e2e テスト専用 mu-plugin: お問い合わせセクション機能を強制的に有効化する ( #1452 ).
 *
 * お問い合わせセクション（vk-blocks/contact-section）は、ブロックテーマ環境では
 * 既定オフになっている ( veu-packages.php の
 * `'default' => $is_block_theme ? false : true` )。wp-env のテスト環境は既定で
 * ブロックテーマが有効なため、何も対策しないとこの機能（およびブロック）が
 * 登録されない。
 *
 * その結果、#1452 の回帰テスト
 * ( tests/e2e/cross-origin-iframe-block-error.spec.ts ) が検証する 6 ブロックの
 * うち、お問い合わせセクションだけを確認できなくなってしまう
 * （同じ不具合を抱える他の 5 ブロックは既定オンのため影響しない）。
 *
 * `vkExUnit_common_options` フィルター（template-tags-veu.php 参照）で
 * `active_contact_section` を強制的に true にし、環境のテーマ・既定値に関わらず
 * ブロックを登録させる。この mu-plugin は tests サイトにのみマッピングされる
 * ( .wp-env.json 参照 ) ため、開発・本番環境には一切影響しない。
 *
 * このフィルターは `veu_get_common_options()` 経由で設定画面の表示にも影響する
 * （有効なチェックボックスに checked が出力される）。無条件で有効化すると、
 * 設定画面のフォームを送信する他の e2e スペック（例: active-setting.spec.ts）が
 * `active_contact_section => true` を tests サイトの DB へ実際に書き込んでしまい、
 * 「単体では通るのに suite 実行では落ちる」という追いにくい副作用になる。
 * そのため `veu_e2e_force_contact_section_active` オプションが立っている間だけ
 * 有効化するようゲートする。ブロック登録の判定は ServerSideRender の REST
 * リクエスト（/wp/v2/block-renderer/vk-blocks/contact-section）でも走り、
 * そちらには画面の URL クエリパラメータが乗らないため、クエリパラメータでは
 * ゲートできない（オプションをマーカーにする理由）。
 * このオプションは #1452 の spec ( cross-origin-iframe-block-error.spec.ts ) が
 * beforeAll で立て、afterAll で消す。それ以外の間は他の e2e スペック・通常
 * アクセスに一切影響しない。
 *
 * @package vk-all-in-one-expansion-unit
 */

add_filter(
	'vkExUnit_common_options',
	function ( $options ) {
		// #1452 の spec が beforeAll で立て、afterAll で消すマーカー。
		// これが無い間は他の e2e スペック・通常アクセスに一切影響しない。
		if ( ! get_option( 'veu_e2e_force_contact_section_active' ) ) {
			return $options;
		}
		$options['active_contact_section'] = true;
		return $options;
	}
);
