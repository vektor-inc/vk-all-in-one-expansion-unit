<?php
/**
 * Adds a "Shared Template-Tag Files (ExUnit)" section to WordPress' built-in Site Health
 * "Info" tab (Tools > Site Health > Info), showing which plugin's bundled copy of each shared
 * template-tag file is currently in effect.
 *
 * WordPress 標準のサイトヘルス「情報」タブ（ツール > サイトヘルス > 情報）に
 * 「Shared Template-Tag Files (ExUnit)」セクションを追加し、共有テンプレートタグファイルごとに
 * 現在どのプラグインの同梱コピーが採用されているかを表示する。
 *
 * This is informational only: no pass/fail judgement, warning, or color/icon is attached to any
 * combination of results.
 *
 * あくまで情報提供のみで、どの組み合わせに対しても良し悪しの判定・警告・色やアイコンは付けない。
 *
 * @package VK All in One Expansion Unit
 * @see https://github.com/vektor-inc/vk-all-in-one-expansion-unit/issues/1479
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'debug_information', 'veu_add_shared_template_tags_site_health_section' );

/**
 * Add the "Shared Template-Tag Files (ExUnit)" section's fields to the Site Health "Info" tab.
 *
 * サイトヘルス「情報」タブに「Shared Template-Tag Files (ExUnit)」セクションのフィールドを追加する。
 *
 * The status collection this builds on (veu_get_shared_template_tags_status()) parses ExUnit's
 * own bundled files with token_get_all() and resolves plugin ownership with ReflectionFunction,
 * both of which are non-trivial work. This filter only ever runs when the Site Health "Info"
 * screen (or its "copy site info" action) actually renders, so that cost is not paid on every
 * request.
 *
 * ここで利用する状態収集（veu_get_shared_template_tags_status()）は ExUnit 同梱ファイルの
 * token_get_all() 解析と ReflectionFunction によるプラグイン特定を行うため、それなりのコストが
 * ある。このフィルターはサイトヘルス「情報」画面（および「サイト情報をコピー」操作）が実際に
 * 描画されるときにしか呼ばれないため、毎リクエストこのコストを払うことはない。
 *
 * @param array $info Existing debug information sections, keyed by section id.
 * @return array
 */
function veu_add_shared_template_tags_site_health_section( $info ) {
	$status = veu_get_shared_template_tags_status();

	$fields = array();
	foreach ( $status as $file_status ) {
		$value = veu_format_shared_template_tags_status_value( $file_status['sources'] );

		$fields[ $file_status['file'] ] = array(
			'label' => $file_status['file'],
			// The Info tab's visual table and its "copy site info to clipboard" output are both
			// built from this data. This section's format never contains markup, so the same
			// plain-text value is safe to reuse for both, keeping what's shown and what's copied
			// identical.
			// 情報タブの表画面と「サイト情報をコピー」の出力はどちらもこのデータから作られる。
			// このセクションの書式に HTML は含まれないため、同じプレーンテキストの値を
			// 両方にそのまま使い回せ、画面表示とコピー結果が一致する.
			'value' => $value,
			'debug' => $value,
		);
	}

	$info['vk-exunit-shared-template-tags'] = array(
		'label'       => __( 'Shared Template-Tag Files (ExUnit)', 'vk-all-in-one-expansion-unit' ),
		// The decided copy is two sentences. Coding rules require one sentence per translation
		// function call, so it is split into two __() calls and joined with a single space; the
		// displayed text is unchanged from the decided copy.
		// 確定仕様の説明文は2文構成。コーディングルールにより翻訳関数は1文ごとに区切る必要があるため
		// __() を2つに分け、半角スペースで連結している。表示される文面は確定仕様のままである.
		'description' => __( 'VK All in One Expansion Unit and some other Vektor plugins bundle the same shared template-tag files, and whichever plugin\'s copy loads first is the one that runs.', 'vk-all-in-one-expansion-unit' ) . ' ' . __( 'This section lists, for each shared file, which plugin\'s copy is currently in effect and its version.', 'vk-all-in-one-expansion-unit' ),
		'fields'      => $fields,
	);

	return $info;
}

/**
 * Format one shared file's list of sources into the single display string shown in its Site
 * Health row.
 *
 * 1つの共有ファイルの採用元一覧を、サイトヘルスの行に表示する1つの文字列へ整形する。
 *
 * @param array $sources Source descriptors from veu_get_shared_template_tags_status().
 * @return string
 */
function veu_format_shared_template_tags_status_value( array $sources ) {
	if ( empty( $sources ) ) {
		// This handles the same "not loaded" case as veu_get_shared_template_tags_status_rows()
		// (collector.php); update that one too when this one changes. They are two separate
		// literals on purpose (see the note there), not shared code. The text happens to read
		// identically ("Not loaded") today, but that is not the point being guarded here -- only
		// the case is, not the wording.
		// これは veu_get_shared_template_tags_status_rows()（collector.php）と同じ「読み込まれて
		// いない」ケースを扱っている。片方を変えたらもう片方も直すこと。あえて共通化せず別々の
		// リテラルにしている（理由は collector.php 側のコメントを参照）。現状は文言もたまたま
		// 同じ（"Not loaded"）だが、ここで揃えたいのはケースであって文言そのものではない.
		return __( 'Not loaded', 'vk-all-in-one-expansion-unit' );
	}

	// When a single shared file's functions are split across more than one plugin's copy (e.g.
	// one function still comes from an older bundled copy while another already comes from
	// ExUnit's own), all of them are listed side by side. No count limit, "N more" truncation,
	// special label, color, or icon is added for this case -- it is shown exactly like any other
	// row, just with more than one entry.
	// 1つの共有ファイル内で関数ごとに採用元が分かれている場合（例: 一部の関数はまだ古い同梱コピー
	// のまま、別の関数は既に ExUnit 自身のもの、など）、それらをすべて並べて表示する。件数の上限や
	// 「ほかN件」の省略、混在を示す専用ラベル・色・アイコンは付けない。他の行と同じ見せ方で、
	// 単に項目数が複数になるだけである.
	$labels = array_map( 'veu_format_template_tags_source_label', $sources );

	return implode( ' / ', $labels );
}

/**
 * Format a single source descriptor into a display label.
 *
 * 1件の採用元記述を表示用ラベルへ整形する。
 *
 * Plugin name/version/basename are intentionally NOT escaped here, even though they come from a
 * third-party plugin's own header data. WordPress' Site Health "Info" tab escapes 'value' itself
 * when rendering the on-screen table (wp-admin/site-health-info.php calls esc_html() on it), and
 * outputs 'debug' completely raw for the "copy site info to clipboard" action
 * (wp-admin/includes/class-wp-debug-data.php). Escaping here as well would double-escape: a
 * plugin literally named "Search & Filter Pro" would render as "Search &amp;amp; Filter Pro" on
 * screen and in the copied text, which defeats the point of "copy site info" (pasting it
 * somewhere for support). debug_information's contract is to receive plain text and let core do
 * the escaping, so do not add esc_html() back here.
 *
 * プラグイン名・バージョン・ベースネームは、サードパーティプラグイン自身のヘッダ由来のデータでは
 * あるが、ここではあえてエスケープしない。WordPress 本体のサイトヘルス「情報」タブは、画面表示時に
 * 'value' 自体を esc_html() でエスケープしており（wp-admin/site-health-info.php）、「サイト情報を
 * コピー」操作では 'debug' を全くエスケープせずそのまま出力する
 * （wp-admin/includes/class-wp-debug-data.php）。ここでもエスケープすると二重エスケープになり、
 * 例えば "Search & Filter Pro" という名前のプラグインが画面でもコピー結果でも
 * "Search &amp;amp; Filter Pro" になってしまい、「コピーしてサポートに貼る」というこの機能の
 * 目的そのものを壊す。debug_information はプレーンテキストを渡してエスケープは本体に委ねる契約な
 * ので、ここに esc_html() を書き戻さないこと.
 *
 * @param array $source Source descriptor.
 * @return string
 */
function veu_format_template_tags_source_label( array $source ) {
	switch ( $source['type'] ) {
		case 'plugin':
			return sprintf(
				/* translators: 1: plugin name (e.g. "VK All in One Expansion Unit"), 2: plugin version (e.g. "9.122.0"), 3: plugin basename -- the path used to identify a plugin, relative to the plugins directory (e.g. "vk-all-in-one-expansion-unit/vkExUnit.php"), as already shown in Site Health's own "Active Plugins" section. */
				__( '%1$s %2$s (%3$s)', 'vk-all-in-one-expansion-unit' ),
				$source['name'],
				$source['version'],
				$source['basename']
			);

		case 'unidentified_file':
			// This handles the same "defining file known, plugin not identified" case as the
			// 'unidentified_file' branch of veu_get_shared_template_tags_status_rows()
			// (collector.php) -- update that one too when this one changes. See the note there
			// for why they are not shared code. UNLIKE the "Not loaded" pair above, the text
			// here is INTENTIONALLY NOT the same: this string embeds the path inline as
			// "...(defined in %s)", while the CLI row keeps 'product' as the fixed "Could not
			// identify the plugin" and puts the path in its own 'path' column instead (see
			// collector.php's docblock on that function). Do not "fix" this by embedding the
			// path here differently to match some other wording, and do not ask for the CLI
			// side to fold its 'path' column back into 'product' to match this string -- that
			// column split was requested in the Issue #1479 UX review specifically so scripts
			// don't have to parse the path back out of a sentence.
			// これは collector.php の veu_get_shared_template_tags_status_rows() の
			// 'unidentified_file' 分岐と同じ「定義元ファイルは分かるがプラグインは特定できない」
			// ケースを扱っている。片方を変えたらもう片方も直すこと。共通化しない理由は
			// collector.php 側のコメントを参照。ただし上の "Not loaded" のペアとは違い、ここは
			// 文言をあえて揃えていない。この文字列は "...(defined in %s)" のようにパスを文中に
			// 埋め込むが、CLI 側の行は 'product' を固定文言 "Could not identify the plugin" の
			// ままにし、パスは独立した 'path' 列に入れる（collector.php 側の同関数の docblock
			// 参照）。ここでの埋め込み方を変えて別の文言に合わせようとしたり、逆に CLI 側の
			// 'path' 列をこの文言に合わせて 'product' へ戻すよう求めたりしないこと。その列構成は
			// Issue #1479 の UX レビューで、スクリプト側が文中からパスを分解し直さずに済むよう
			// 指示されて分離したものである.
			return sprintf(
				/* translators: %s: file path where the function was found, relative to the WordPress root (never a server absolute path). */
				__( 'Could not identify the plugin (defined in %s)', 'vk-all-in-one-expansion-unit' ),
				$source['relative_path']
			);

		case 'unidentified':
		default:
			// This handles the same "nothing identifiable at all" case as the default branch of
			// veu_get_shared_template_tags_status_rows() (collector.php) -- update that one too
			// when this one changes. See the note there for why they are not shared code. The
			// text happens to read identically ("Could not identify the plugin") today, same as
			// the "Not loaded" case above -- but, as with that case, what must stay in sync is
			// the case being handled, not necessarily the wording.
			// これは collector.php の veu_get_shared_template_tags_status_rows() の default
			// 分岐と同じ「何も手がかりがない」ケースを扱っている。片方を変えたらもう片方も
			// 直すこと。共通化しない理由は collector.php 側のコメントを参照。現状は文言も
			// たまたま同じ（"Could not identify the plugin"）だが、上の "Not loaded" のケースと
			// 同様、揃えるべきなのは扱うケースであって、必ずしも文言そのものではない.
			return __( 'Could not identify the plugin', 'vk-all-in-one-expansion-unit' );
	}
}
