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
 * @package vk-all-in-one-expansion-unit
 */

add_filter(
	'vkExUnit_common_options',
	function ( $options ) {
		$options['active_contact_section'] = true;
		return $options;
	}
);
