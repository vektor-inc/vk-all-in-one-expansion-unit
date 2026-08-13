<?php
/**
 * e2e テスト専用 mu-plugin: お問い合わせセクション機能を強制的に有効化する ( #1452 ).
 *
 * お問い合わせセクション ( vk-blocks/contact-section ) は、ブロックテーマ環境では
 * 既定オフになっている ( veu-packages.php の
 * `'default' => $is_block_theme ? false : true` )。wp-env のテスト環境は既定で
 * ブロックテーマが有効なため、何もしないとこの機能（およびブロック）が登録されず、
 * #1452 の回帰テスト ( cross-origin-iframe-block-error.spec.ts ) が検証する 6
 * ブロックのうちこれだけ確認できなくなってしまう。
 *
 * `vkExUnit_common_options` フィルター ( template-tags-veu.php 参照 ) で
 * `active_contact_section` を true にし、テーマ・既定値に関わらずブロックを
 * 登録させる。ただしこのフィルターは設定画面の表示にも影響する ( 有効な
 * チェックボックスに checked が出力される ) ため、無条件で有効化すると、設定
 * 画面のフォームを送信する他の e2e スペックが `active_contact_section => true`
 * を DB へ実際に書き込んでしまう副作用がある。そのため
 * `veu_e2e_force_contact_section_active` オプションが立っている間だけ有効化する
 * ようゲートする ( ブロック登録判定は REST の block-renderer でも走り、そちらに
 * は画面の URL クエリパラメータが乗らないため、クエリパラメータではゲートでき
 * ない )。このオプションは #1452 の spec が beforeAll で立て、afterAll で消す。
 *
 * e2e-only mu-plugin: forces the Contact Section feature active ( #1452 ).
 *
 * The Contact Section block ( vk-blocks/contact-section ) defaults to inactive
 * on block-theme environments ( see the `'default' => $is_block_theme ? false :
 * true` in veu-packages.php ). Since wp-env's test environment defaults to a
 * block theme, doing nothing would leave this block unregistered, making it the
 * one block the #1452 regression test ( cross-origin-iframe-block-error.spec.ts )
 * cannot verify among the 6 it checks.
 *
 * The `vkExUnit_common_options` filter ( see template-tags-veu.php ) forces
 * `active_contact_section` to true so the block registers regardless of theme
 * or default. This filter also affects the settings screen's display ( active
 * items render their checkbox as checked ), so enabling it unconditionally
 * would let other e2e specs that submit that settings form persist
 * `active_contact_section => true` to the DB for real. It is therefore gated
 * behind the `veu_e2e_force_contact_section_active` option, set by the #1452
 * spec's beforeAll and removed in its afterAll ( a query parameter can't gate
 * this, since the block-registration check also runs via the REST
 * block-renderer request, which carries no URL query string ).
 *
 * @package vk-all-in-one-expansion-unit
 */

add_filter(
	'vkExUnit_common_options',
	function ( $options ) {
		// #1452 の spec が beforeAll で立て、afterAll で消すマーカー。
		// これが無い間は他の e2e スペック・通常アクセスに一切影響しない。
		// Marker set by the #1452 spec's beforeAll and removed in its afterAll;
		// no effect on other e2e specs or normal access while it is unset.
		if ( ! get_option( 'veu_e2e_force_contact_section_active' ) ) {
			return $options;
		}
		$options['active_contact_section'] = true;
		return $options;
	}
);
