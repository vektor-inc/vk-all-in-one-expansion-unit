<?php
/**
 * Page top button (ページトップへ戻るボタン)
 *
 * フッターに「ページトップへ戻る」ボタンを出力する機能。
 * ユーザーが管理画面 / カスタマイザーから画像をアップロードした場合は
 * `<a>` の style 属性で `--veu_page_top_button_url` を上書きし、
 * 既存ユーザーがテーマで上書きしている `--ver_` には触らない。
 *
 * @package Vk_All_In_One_Expansion_Unit
 */

/*
	footer add pagetop btn
/*-------------------------------------------*/
add_action( 'wp_footer', 'veu_add_pagetop' );

/**
 * Output the page top button HTML in the footer.
 *
 * フッターに `<a>` 要素を出力する。
 * モバイル非表示オプションが有効でモバイル端末の場合は何も出力しない。
 *
 * @return void
 */
function veu_add_pagetop() {
	$options = veu_pagetop_options();

	// Bail when the user has chosen to hide the button on touch screen devices.
	// モバイル非表示が有効かつモバイル判定の場合は表示しない。
	if ( wp_is_mobile() && ! empty( $options['hide_mobile'] ) ) {
		return;
	}

	echo veu_pagetop_render( $options ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- veu_pagetop_render() で適切にエスケープ済み。
}

/**
 * Generate the page top button HTML.
 *
 * `image_url` が設定されている場合は style 属性で
 * `--veu_page_top_button_url` を上書きする。
 * テスト容易性のためマークアップ生成を関数化している。
 *
 * @param mixed $options vkExUnit_pagetop オプション配列。配列以外が渡された場合は空配列として扱う。
 * @return string The page top button HTML.
 */
function veu_pagetop_render( $options = array() ) {
	// Defensive guard: callers may pass non-array values (null, string, etc.)
	// either by mistake or via legacy code paths. Type-hint cannot be added
	// without breaking backward compatibility, so normalize to an empty array
	// here and let wp_parse_args() backfill defaults below.
	// 後方互換のため引数の型宣言は付けず、配列以外が渡された場合は空配列として
	// 扱うガードを入れる。後段の wp_parse_args() でデフォルト値を補完する。
	if ( ! is_array( $options ) ) {
		$options = array();
	}

	// Backfill defaults so callers can pass partial arrays / option output as-is.
	// 不足キーをデフォルト値で補完。
	$options = wp_parse_args( $options, veu_pagetop_default() );

	// Sanitize the URL again on the output side to guard against any
	// tampering of the option value that bypassed the input sanitizer.
	// 出力時にもサニタイズし、入力サニタイザーを通っていない値で
	// 万一汚染されていた場合でも CSS injection を防ぐ。
	$image_url = veu_pagetop_sanitize_image_url( $options['image_url'] );

	$style_attr = '';
	$class      = 'page_top_btn';
	if ( '' !== $image_url ) {
		// Use double quotes inside url() as required by CSS specs. The
		// sanitizer already rejects values containing quotes / parens /
		// whitespace, so this stays a single token.
		// url() 内はダブルクォート固定。サニタイザーで
		// クォート・括弧・空白を含む値は除外済みのため安全。
		$style_value = '--veu_page_top_button_url:url("' . $image_url . '");';

		// Append width / height CSS custom properties when the user provided
		// a positive size. Run through the shared sanitizer again as a
		// defence-in-depth measure: callers that invoke `veu_pagetop_render()`
		// directly with arbitrary arrays would otherwise bypass the 500px
		// clamp applied on save / read via `veu_pagetop_sanitize_image_size()`.
		// `has-image` のときだけサイズ用カスタムプロパティを付与する。
		// `veu_pagetop_options()` 経由なら既にサニタイズ済みだが、
		// `veu_pagetop_render()` を直接呼ぶ経路でも 500px クランプ等の
		// 同一ルールが効くよう共通サニタイザーを通す（多重防御）。
		$image_width  = veu_pagetop_sanitize_image_size( isset( $options['image_width'] ) ? $options['image_width'] : 0 );
		$image_height = veu_pagetop_sanitize_image_size( isset( $options['image_height'] ) ? $options['image_height'] : 0 );
		if ( $image_width > 0 ) {
			$style_value .= '--veu_page_top_button_width:' . $image_width . 'px;';
		}
		if ( $image_height > 0 ) {
			$style_value .= '--veu_page_top_button_height:' . $image_height . 'px;';
		}

		$style_attr = ' style="' . esc_attr( $style_value ) . '"';
		// `has-image` クラスを付けることで SCSS 側で画像サイズを
		// `contain` に切り替えるなどの調整が可能。
		$class .= ' has-image';
	}

	// 可視部分は背景画像アイコン。リンクの読み上げ名は内側の `<span>` で
	// 視覚的に隠して提供し、翻訳関数でラップする。テキストを `<a>` 直下ではなく
	// 内側 span に入れているのは、`<a>` 自体に visually-hidden を当てると
	// `<a>` が 1px に潰れて背景画像アイコンごと消えてしまうため。
	// span にはクラスを付けず、SCSS 側は子セレクタ `#page_top > span` で
	// visually-hidden を当てる（`<a>` の子は span 1 つだけなので一意）。
	// The visible button is an icon (background image). The accessible name is
	// provided by a visually-hidden inner `<span>`, wrapped in a translation
	// function so screen readers announce it. The text is placed in the inner
	// span (not directly on the `<a>`) on purpose: applying visually-hidden to
	// the `<a>` itself collapses the anchor to 1px and hides the background-image
	// icon along with it. No class is added to the span; the SCSS targets it via
	// the class-less child selector `#page_top > span` (the `<a>` has exactly one
	// child span, so the selector is unambiguous).
	return '<a href="#top" id="page_top" class="' . esc_attr( $class ) . '"' . $style_attr . '><span>' . esc_html__( 'Back to top', 'vk-all-in-one-expansion-unit' ) . '</span></a>';
}

/**
 * Sanitize the image URL stored in vkExUnit_pagetop[image_url].
 *
 * `esc_url_raw()` で正規化したうえで、CSS injection を引き起こしうる
 * 値（クォート・括弧・制御文字・空白）を含むものや、画像として
 * 妥当な拡張子を持たない値は空文字を返す。
 *
 * @param mixed $value Raw value from option / customizer / POST.
 * @return string Sanitized URL, or empty string when the input is unsafe.
 */
function veu_pagetop_sanitize_image_url( $value ) {
	// Non-string inputs are rejected outright.
	// 文字列以外は問答無用で空文字。
	if ( ! is_string( $value ) ) {
		return '';
	}

	// Trim leading/trailing whitespace before normalization.
	// 前後の空白を除去してから正規化。
	$value = trim( $value );
	if ( '' === $value ) {
		return '';
	}

	// Reject values that contain characters which would let an attacker
	// break out of the `url("...")` context (quotes, parens, backslash,
	// control chars including C0 / DEL / C1, internal whitespace).
	// The `u` modifier enables UTF-8 mode so that multi-byte C1 controls
	// (U+0080-U+009F) are matched as a single code point rather than
	// individual bytes.
	// `url("...")` のコンテキストから脱出可能な文字（クォート・括弧・
	// バックスラッシュ・制御文字・内部空白）を含む場合は拒否する。
	// `u` 修飾子で UTF-8 モードを有効化し、C1 制御文字 (U+0080-U+009F) も
	// マルチバイト文字として一括りに検出する。
	if ( preg_match( '/[\s"\'\\\\()]|[\x00-\x1F\x7F]|[\x{0080}-\x{009F}]/u', $value ) ) {
		return '';
	}

	// Reject URL-encoded variants of the dangerous characters above. Browsers
	// may decode `%22` / `%27` / `%28` / `%29` / `%5C` inside `url("...")`,
	// allowing an attacker to break out even though the raw value passed the
	// first regex. Detection is case-insensitive so that `%5C` and `%5c`
	// (both valid encodings of the backslash) are caught equally.
	// 上記の危険文字を URL エンコードした表現も拒否する。ブラウザは
	// `url("...")` 内の `%22` などをデコードしうるため、生クォート等を
	// 弾いただけでは脱出を防げない。URL の percent-encoding は大文字小文字
	// が等価（例: `%5C` と `%5c` はどちらもバックスラッシュ）なので、
	// 取りこぼさないよう case-insensitive で判定する。
	if ( preg_match( '/%(22|27|28|29|5C)/i', $value ) ) {
		return '';
	}

	// Run through WordPress core URL sanitization.
	// WordPress 標準の URL サニタイズを通す。
	$value = esc_url_raw( $value );
	if ( '' === $value ) {
		return '';
	}

	// Whitelist common image extensions only (svg は管理画面で
	// アップロード不可だが、ユーザーが既存の svg URL を貼るケース
	// を想定して許可する。SVG 自体のアップロード許可は別 issue).
	$allowed_extensions = array( 'svg', 'png', 'jpg', 'jpeg', 'gif', 'webp' );

	// Strip the query string / fragment when extracting the extension so
	// that `image.png?ver=1` is recognized correctly.
	// 拡張子判定は ?ver=1 等を除いた path 部分から行う。
	$path = wp_parse_url( $value, PHP_URL_PATH );
	if ( ! is_string( $path ) || '' === $path ) {
		return '';
	}

	$ext = strtolower( pathinfo( $path, PATHINFO_EXTENSION ) );
	if ( ! in_array( $ext, $allowed_extensions, true ) ) {
		return '';
	}

	return $value;
}

/**
 * Sanitize an image size (width / height) value for the page top button.
 *
 * Accepts arbitrary input (string, int, float, null, array...) and normalizes
 * it to a non-negative integer pixel value clamped to a sane maximum.
 *
 * - Non-scalar inputs (arrays, null, etc.) are treated as `0`.
 * - Strings such as `'60'` are coerced via `absint()` so negative and
 *   non-numeric values become `0`.
 * - The result is clamped to a maximum of 500px to prevent extremely large
 *   values from breaking the layout (defence-in-depth).
 * - `0` means "unspecified" and is the default state.
 *
 * 画像サイズ用の共通サニタイザー。
 * 非数値・null・配列などは 0 に正規化し、`absint()` で整数化したうえで
 * 上限 500px にクランプする。0 は「未指定」を表し既定状態となる。
 *
 * @param mixed $value Raw value from option / customizer / POST.
 * @return int Sanitized pixel value (0 - 500).
 */
function veu_pagetop_sanitize_image_size( $value ) {
	// Reject arrays / objects / null up-front so `absint()` does not emit
	// notices on unexpected input types.
	// 配列・オブジェクト・null は absint() で warning を出さないよう先に弾く。
	if ( ! is_scalar( $value ) ) {
		return 0;
	}

	// `absint()` converts to non-negative integer; non-numeric strings
	// become 0. Boolean true would become 1, but the upper clamp also
	// keeps the value within range.
	// absint() で非負整数化。非数値文字列は 0 になる。
	$value = absint( $value );

	// Clamp to the documented maximum (defence-in-depth against absurd values).
	// 極端な値での画面崩れを防ぐため 500px でクランプ。
	return min( 500, $value );
}

add_action( 'customize_register', 'veu_customize_register_pagetop' );

/**
 * Register Customizer settings & controls for the page top button.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 * @return void
 */
function veu_customize_register_pagetop( $wp_customize ) {

	/*
		Page Top setting
	/*-------------------------------------------*/
	$wp_customize->add_section(
		'veu_pagetop_setting',
		array(
			'title'    => __( 'Page Top Button', 'vk-all-in-one-expansion-unit' ),
			'priority' => 10000,
			'panel'    => 'veu_setting',
		)
	);

	// Hide on mobile.
	// モバイル端末で非表示にするチェックボックス。
	$wp_customize->add_setting(
		'vkExUnit_pagetop[hide_mobile]',
		array(
			'default'           => false,
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'veu_sanitize_boolean',
		)
	);
	$wp_customize->add_control(
		'vkExUnit_pagetop[hide_mobile]',
		array(
			'label'    => __( 'Do not display on touch screen devices', 'vk-all-in-one-expansion-unit' ),
			'section'  => 'veu_pagetop_setting',
			'settings' => 'vkExUnit_pagetop[hide_mobile]',
			'type'     => 'checkbox',
		)
	);

	// Image URL (file uploader).
	// 画像 URL のアップロード設定。
	$wp_customize->add_setting(
		'vkExUnit_pagetop[image_url]',
		array(
			'default'           => '',
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'veu_pagetop_sanitize_image_url',
			'transport'         => 'refresh',
		)
	);
	// Build the shared "Page top button image" group heading + description block.
	// Issue #1368: the parent group label "Page top button image" was previously
	// shown only as the WP_Customize_Image_Control label (normal-weight text),
	// while the child group "Image size" right below it was rendered as h3
	// (admin-custom-h3) via VK_Custom_Html_Control. The visual hierarchy was
	// inverted ("child looks more prominent than parent"). To restore the
	// hierarchy, render the group heading explicitly as h2 (admin-custom-h2)
	// via VK_Custom_Html_Control just above the image control, and drop the
	// label / description from the image control itself to avoid duplication.
	// (See rules/design-rules.md "見出しレベルと情報階層".)
	//
	// issue #1368 対応。以前は「Page top button image」が WP_Customize_Image_Control
	// の label として通常字で出ているだけで、直下の子グループ「Image size」が
	// h3 (admin-custom-h3) として表示される情報階層と見た目の逆転が発生していた。
	// 解消のため、画像コントロールの直前に VK_Custom_Html_Control で h2
	// (admin-custom-h2) として親見出しを出し、画像コントロール側の label /
	// description は外して重複を避ける（design-rules.md「見出しレベルと情報階層」）。
	//
	// Translation rule (rules/coding-rules.md): one sentence per translation call.
	// Each sentence becomes its own `<p class="description">` so they render as
	// distinct paragraphs (matches the admin page grouping). The whole block is
	// passed to custom_html which is sanitized via wp_kses_post() on output.
	// 翻訳ルールに従い 1 文ずつ翻訳関数で区切り、それぞれを個別の
	// `<p class="description">` として出力する（custom_html 側は wp_kses_post() でサニタイズ）。
	$image_description_html  = '<p class="description">' . esc_html__( 'Upload an image to replace the default page top button icon.', 'vk-all-in-one-expansion-unit' ) . '</p>';
	$image_description_html .= '<p class="description">' . esc_html__( 'Recommended formats: SVG, PNG, JPG, GIF, WebP.', 'vk-all-in-one-expansion-unit' ) . '</p>';
	$image_description_html .= '<p class="description">' . esc_html__( 'A square (1:1) image is recommended.', 'vk-all-in-one-expansion-unit' ) . ' ' . esc_html__( 'Images with a very different aspect ratio may show extra empty space.', 'vk-all-in-one-expansion-unit' ) . '</p>';

	$wp_customize->add_control(
		new VK_Custom_Html_Control(
			$wp_customize,
			'vkExUnit_pagetop_image_heading',
			array(
				'section'     => 'veu_pagetop_setting',
				// 値を持たない説明・見出し専用の control。
				// `settings` に空配列を渡すことで「紐づく setting なし」として扱われ、
				// option 名前空間を汚染しない（PR #1363 review と同じ理由）。
				'settings'    => array(),
				// 親グループ見出しなので h2 (admin-custom-h2) で出力する。
				// VK_Custom_Html_Control のデフォルトも h2 だが、意図を明示するため指定する。
				'label'       => __( 'Page top button image', 'vk-all-in-one-expansion-unit' ),
				'label_tag'   => 'h2',
				'custom_html' => $image_description_html,
			)
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'vkExUnit_pagetop_image_url',
			array(
				// 親見出しと説明文は直前の VK_Custom_Html_Control に集約済みのため、
				// この画像コントロール自体には label / description を持たせず、
				// 親階層との視覚的逆転を解消する（issue #1368）。
				'section'       => 'veu_pagetop_setting',
				'settings'      => 'vkExUnit_pagetop[image_url]',
				// Customize the built-in image control button labels so that
				// "Remove" is interpreted as "clear this setting", not
				// "delete from the media library".
				// ビルトインの button_labels をカスタマイズして、
				// 「削除」がメディアライブラリからの削除ではなく
				// 「設定値を解除」である事をユーザーに明示する。
				'button_labels' => array(
					'select'       => __( 'Select image', 'vk-all-in-one-expansion-unit' ),
					'change'       => __( 'Change image', 'vk-all-in-one-expansion-unit' ),
					'remove'       => __( 'Clear image selection', 'vk-all-in-one-expansion-unit' ),
					'default'      => __( 'Default', 'vk-all-in-one-expansion-unit' ),
					'placeholder'  => __( 'No image selected', 'vk-all-in-one-expansion-unit' ),
					'frame_title'  => __( 'Select image', 'vk-all-in-one-expansion-unit' ),
					'frame_button' => __( 'Choose image', 'vk-all-in-one-expansion-unit' ),
				),
			)
		)
	);

	// Shared "Image size" description block, rendered once above the width / height
	// inputs via VK_Custom_Html_Control (provided by vektor-inc/vk-helpers >= 0.3.0).
	//
	// Previously the same long description was attached to the width control only,
	// which still meant the description visually sat under just one of the two
	// related inputs and was easy to miss. design-rules.md
	// ("共通の説明文は 1 箇所に集約する") prescribes a single description area at
	// the head of the group instead — VK_Custom_Html_Control gives us exactly
	// that: a section-scoped "label-only" control whose custom_html is rendered
	// once, sanitized via wp_kses_post().
	//
	// 設定値そのものではなく説明文を表示するための「説明エリア専用」 control。
	// width / height 共通のサイズ説明を 1 か所だけ表示する目的で配置する。
	// 旧実装では width control の description に長文を持たせていたが、
	// design-rules.md「共通の説明文は 1 箇所に集約する」に従い、
	// 入力欄群の上に共通説明を 1 度だけ表示する形へ変更した。
	//
	// この control は値を持たないため、`add_setting` は呼ばずに
	// `'settings' => array()` を明示する（空配列）。
	// WP_Customize_Control::__construct() は `settings` が array の場合、
	// 各要素を `WP_Customize_Manager::get_setting()` で解決するだけなので、
	// 空配列を渡せば「紐づく setting 0 個」の説明専用 control として動作する。
	// 以前は `__return_empty_string` を sanitize_callback にしたダミー setting を
	// 登録していたが、それでは `vkExUnit_pagetop_image_size_description` という
	// option キーが DB に書き込まれる副作用があり、option 名前空間を汚染するため
	// 廃止した（PR #1363 review）。
	// Build the shared description HTML. Translation rule (rules/coding-rules.md):
	// one sentence per translation call. Each sentence becomes its own
	// `<p class="description">` so the customizer renders them as distinct
	// paragraphs (and matches the admin page's grouping).
	// 翻訳ルールに従い 1 文ずつ翻訳関数で区切り、それぞれを個別の
	// `<p class="description">` として出力する。
	// PR #1363 のレビューで「説明が長すぎる」と指摘を受け、試せば分かる挙動
	// （片方だけ指定／縦横比保持）や単位・上限の重複説明を落とし 3 段落に短縮済み。
	$image_size_description_html  = '<p class="description">' . esc_html__( 'Specify the image size in pixels.', 'vk-all-in-one-expansion-unit' ) . ' ' . esc_html__( 'Default: 40 x 38 px / Max: 500 px.', 'vk-all-in-one-expansion-unit' ) . '</p>';
	$image_size_description_html .= '<p class="description">' . esc_html__( '44 px or larger is recommended for touch screen devices.', 'vk-all-in-one-expansion-unit' ) . '</p>';
	$image_size_description_html .= '<p class="description">' . esc_html__( 'The width / height values are kept when the image is cleared.', 'vk-all-in-one-expansion-unit' ) . '</p>';

	$wp_customize->add_control(
		new VK_Custom_Html_Control(
			$wp_customize,
			'vkExUnit_pagetop_image_size_description',
			array(
				'section'     => 'veu_pagetop_setting',
				// この control は値を持たない（説明文専用）ため `settings` に空配列を渡す。
				// WP_Customize_Control::__construct() の "Process settings" ブロックは
				// settings が array の場合、各要素を `manager->get_setting()` で解決するだけなので、
				// 空配列を渡せば「紐づく setting なし」の状態で安全にインスタンス化できる。
				// これにより `add_setting` 側でダミー option を登録する必要が無くなり、
				// option 名前空間 (`vkExUnit_pagetop_image_size_description`) の汚染を回避できる。
				'settings'    => array(),
				// 'Image size' をグループ見出しとして h3 (admin-custom-h3) で表示する。
				// 親セクション内で別途出力される「Page top button image」(h2 相当) の
				// 子グループに当たるため、design-rules.md「見出しレベルと情報階層」に
				// 従い 1 段下げて h3 にする（label_tag で切り替え、vk-helpers 0.3.0 以降対応）。
				'label'       => __( 'Image size', 'vk-all-in-one-expansion-unit' ),
				'label_tag'   => 'h3',
				// custom_html は wp_kses_post() で出力時にサニタイズされるため、
				// ここで esc_html__() を通したテキストを <p class="description"> で
				// くるんだ HTML を渡して問題ない。
				'custom_html' => $image_size_description_html,
			)
		)
	);

	// Image width (in px).
	// 画像の幅（px）。
	$wp_customize->add_setting(
		'vkExUnit_pagetop[image_width]',
		array(
			'default'           => 0,
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'veu_pagetop_sanitize_image_size',
			'transport'         => 'refresh',
		)
	);
	// width 入力欄は vk-helpers 0.3.0 の VK_Custom_Text_Control を使い、
	// 「Image width [入力] px」のように単位ラベル (px) を input_after で表示する。
	// `(px)` をラベルから外す事で「ラベル列幅 + 入力欄 + 単位」のレイアウトを
	// design-rules.md「レスポンシブ表示での折り返し対策」「ラベル付き入力欄の x 座標揃え」
	// に沿った形へ揃える（カスタマイザー上で width / height のラベル文字数が揃うため
	// 入力欄の左端も揃いやすくなる）。
	$wp_customize->add_control(
		new VK_Custom_Text_Control(
			$wp_customize,
			'vkExUnit_pagetop_image_width',
			array(
				'label'       => __( 'Image width', 'vk-all-in-one-expansion-unit' ),
				'section'     => 'veu_pagetop_setting',
				'settings'    => 'vkExUnit_pagetop[image_width]',
				// description は共通説明エリア（VK_Custom_Html_Control）に集約済みのため
				// width 個別には設定しない（design-rules.md「共通の説明文は 1 箇所に集約する」）。
				'input_type'  => 'number',
				'input_after' => 'px',
				'input_attrs' => array(
					'min'       => 1,
					'max'       => 500,
					'step'      => 1,
					'inputmode' => 'numeric',
				),
			)
		)
	);

	// Image height (in px).
	// 画像の高さ（px）。
	$wp_customize->add_setting(
		'vkExUnit_pagetop[image_height]',
		array(
			'default'           => 0,
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'veu_pagetop_sanitize_image_size',
			'transport'         => 'refresh',
		)
	);
	// height も width と同様、VK_Custom_Text_Control + input_after='px' で
	// 「Image height [入力] px」の表示にする。`(px)` はラベルから外す。
	$wp_customize->add_control(
		new VK_Custom_Text_Control(
			$wp_customize,
			'vkExUnit_pagetop_image_height',
			array(
				'label'       => __( 'Image height', 'vk-all-in-one-expansion-unit' ),
				'section'     => 'veu_pagetop_setting',
				'settings'    => 'vkExUnit_pagetop[image_height]',
				// description は共通説明エリア（VK_Custom_Html_Control）に集約済み。
				'input_type'  => 'number',
				'input_after' => 'px',
				'input_attrs' => array(
					'min'       => 1,
					'max'       => 500,
					'step'      => 1,
					'inputmode' => 'numeric',
				),
			)
		)
	);

	// Selective refresh: re-render the `<a>` element when settings change.
	// 設定変更時に `<a>` を差し替える selective refresh パーシャル。
	$wp_customize->selective_refresh->add_partial(
		'vkExUnit_pagetop[hide_mobile]',
		array(
			'selector'        => '.page_top_btn',
			'render_callback' => '',
			'supports'        => array(),
		)
	);
	$wp_customize->selective_refresh->add_partial(
		'vkExUnit_pagetop[image_url]',
		array(
			'selector'            => '.page_top_btn',
			'container_inclusive' => true,
			'render_callback'     => 'veu_pagetop_partial_render',
		)
	);
	// width / height も image_url と同じく <a> 要素ごと差し替える。
	$wp_customize->selective_refresh->add_partial(
		'vkExUnit_pagetop[image_width]',
		array(
			'selector'            => '.page_top_btn',
			'container_inclusive' => true,
			'render_callback'     => 'veu_pagetop_partial_render',
		)
	);
	$wp_customize->selective_refresh->add_partial(
		'vkExUnit_pagetop[image_height]',
		array(
			'selector'            => '.page_top_btn',
			'container_inclusive' => true,
			'render_callback'     => 'veu_pagetop_partial_render',
		)
	);
}

/**
 * Enqueue the ExUnit admin CSS on the Customizer controls frame.
 *
 * `admin_enqueue_scripts` (登録は admin/admin.php) はカスタマイザーのコントロール
 * フレームでは発火しないため、`#customize-control-vkExUnit_pagetop_image_heading`
 * などコントロール固有のスタイル（issue #1368 の説明文と画像サムネイル間の余白拡大）が
 * 反映されない。`customize_controls_enqueue_scripts` 経由で同じ
 * `vkExUnit_admin.css` を読み込むことで、メイン設定ページとカスタマイザーの両方で
 * 同一スタイルが効くようにする。
 *
 * 副作用について:
 * `vkExUnit_admin.css` には `#pagetopSetting` / `.veu_metabox_*` / `.wp-list-table`
 * などメイン管理画面の DOM 構造に依存した名前空間付きセレクタしか含まれていないため、
 * カスタマイザー側にこれらの ID/クラスは存在せず誤発火しない。
 *
 * @return void
 */
function veu_pagetop_customize_controls_enqueue() {
	wp_enqueue_style( 'veu_admin_css', VEU_DIRECTORY_URI . '/assets/css/vkExUnit_admin.css', array(), VEU_VERSION, 'all' );
}
add_action( 'customize_controls_enqueue_scripts', 'veu_pagetop_customize_controls_enqueue' );

/**
 * Render callback for the selective refresh partial.
 *
 * `veu_add_pagetop()` と同じく、モバイル非表示オプションが有効で
 * モバイル端末の場合は空文字を返してプレビュー表示も非表示にする。
 * これを行わないと、フロント側では非表示なのにカスタマイザのプレビュー
 * だけ表示されてしまい挙動が一致しなくなる。
 *
 * @return string The page top button HTML, or empty string when hidden on mobile.
 */
function veu_pagetop_partial_render() {
	$options = veu_pagetop_options();
	// Match veu_add_pagetop() behavior: return empty on mobile when hide_mobile is enabled.
	// veu_add_pagetop() と挙動を合わせ、モバイル非表示時は空文字を返す。
	if ( wp_is_mobile() && ! empty( $options['hide_mobile'] ) ) {
		return '';
	}
	return veu_pagetop_render( $options );
}


/**
 * Register the page-top settings tab on ExUnit main setting page.
 *
 * @return void
 */
function veu_pagetop_admin_register() {
	$tab_label         = __( 'Page Top Button', 'vk-all-in-one-expansion-unit' );
	$option_name       = 'vkExUnit_pagetop';
	$sanitize_callback = 'veu_pagetop_sanitize';
	$render_page       = 'veu_pagetop_admin';
	vkExUnit_register_setting( $tab_label, $option_name, $sanitize_callback, $render_page );
}
add_action( 'veu_package_init', 'veu_pagetop_admin_register' );

/**
 * Render the page-top admin setting section.
 *
 * @return void
 */
function veu_pagetop_admin() {
	$options       = veu_pagetop_options();
	$image_url     = $options['image_url'];
	$image_width   = isset( $options['image_width'] ) ? (int) $options['image_width'] : 0;
	$image_height  = isset( $options['image_height'] ) ? (int) $options['image_height'] : 0;
	$preview_style = '' !== $image_url ? '' : ' style="display:none;"';
	// 画像未アップロード時はサイズ入力欄も非表示にする。
	// プレビューと同じトグル機構（thumb.src の MutationObserver / clear ボタン）で
	// `.veu_pagetop_image_size` の表示状態を同期させる。
	$size_style     = '' !== $image_url ? '' : ' style="display:none;"';
	$customizer_url = admin_url( 'customize.php?autofocus[section]=veu_pagetop_setting' );
	?>
<div id="pagetopSetting" class="sectionBox">
<h3><?php esc_html_e( 'Page Top Button', 'vk-all-in-one-expansion-unit' ); ?></h3>
<table class="form-table">
<tr>
<th><?php esc_html_e( 'Page Top Button', 'vk-all-in-one-expansion-unit' ); ?> </th>
<td><label>
<input type="checkbox" name="vkExUnit_pagetop[hide_mobile]" value="true"
	<?php
	if ( ! empty( $options['hide_mobile'] ) ) {
		echo ' checked';}
	?>
	/> <?php esc_html_e( 'Do not display on touch screen devices', 'vk-all-in-one-expansion-unit' ); ?> </label>
</td>
</tr>
<tr>
<th><?php esc_html_e( 'Page top button image', 'vk-all-in-one-expansion-unit' ); ?></th>
<td>
	<?php
	// 設定 UI は 3 つの情報グループで構成する（design-rules.md「余白」セクション
	// 「情報グループ内の余白 < 情報グループ間の余白」「UIでも情報グループ間の余白
	// 関係を守る」準拠）。グループ内の小要素間 (`<p>` 同士など) は詰め、
	// グループ間 (A→B→C) は大きく開ける。具体的なマージン値は SCSS 側
	// (`assets/_scss/vkExUnit_admin.scss` の `#pagetopSetting`) に集約する。
	//
	// A: 画像ソース（プレビュー + URL 入力 + Select / Clear ボタン）
	// B: サイズ入力（幅・高さ + サイズに関する説明） — 画像が無いときは display:none
	// C: 画像全体に関する meta（推奨フォーマット / アスペクト比 / Clear 挙動 /
	// テーマ上書きの注意 / カスタマイザーへのリンク）
	?>
	<div class="veu_pagetop_image_source">
		<div class="veu_pagetop_image_preview"<?php echo $preview_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- 固定文字列のみ。 ?>>
			<img id="thumb_pagetop_image_url" src="<?php echo esc_url( $image_url ); ?>" alt="" style="max-width:120px;height:auto;" />
		</div>
		<p class="veu_pagetop_image_source__url">
			<input type="text" name="vkExUnit_pagetop[image_url]" id="pagetop_image_url" value="<?php echo esc_attr( $image_url ); ?>" />
		</p>
		<?php
		// Buttons line up horizontally when there is room and wrap to a second
		// row when the container is narrow (e.g. on a sidebar-style layout).
		// Flex layout / wrap behavior is defined on `.veu_pagetop_image_source__buttons`
		// in the admin SCSS, so the inline `style` attributes that were here
		// previously have been removed. This follows the "横並びボタンの揃え"
		// rule in design-rules.md.
		// 「画像を選択」「画像の選択を解除」ボタンは、広い時は横並び・狭い時は
		// 段落ちさせる。flex / wrap の指定は SCSS 側
		// (`.veu_pagetop_image_source__buttons`) に集約済みのため、ここではクラス
		// のみを付与する（design-rules.md「横並びボタンの揃え」準拠）。
		?>
		<p class="veu_pagetop_image_source__buttons">
			<button type="button" id="media_src_pagetop_image_url" class="media_btn button button-default"><?php esc_html_e( 'Select an image', 'vk-all-in-one-expansion-unit' ); ?></button>
			<button type="button" id="veu_pagetop_image_clear" class="button button-default"><?php esc_html_e( 'Clear image selection', 'vk-all-in-one-expansion-unit' ); ?></button>
		</p>
	</div>
	<div class="veu_pagetop_image_size"<?php echo $size_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- 固定文字列のみ。 ?>>
		<?php
		// Width and height inputs are placed in separate `<p>` blocks so each
		// "label + input + unit" set occupies its own line. This prevents the
		// "(px)" suffix from being orphaned on a wrapped line when the panel
		// becomes narrow, following the "レスポンシブ表示での折り返し対策"
		// rule in design-rules.md (1 セット 1 行に分ける).
		// 「ラベル + 入力欄 + 単位」を 1 セット 1 行ずつ縦に積み、狭い幅で
		// 「(px)」だけが次行に取り残されるのを防ぐ（design-rules.md
		// 「レスポンシブ表示での折り返し対策」準拠）。
		?>
		<div class="veu_pagetop_image_size__inputs">
			<p>
				<label for="pagetop_image_width">
					<?php esc_html_e( 'Image width (px)', 'vk-all-in-one-expansion-unit' ); ?>
					<input type="number" name="vkExUnit_pagetop[image_width]" id="pagetop_image_width" value="<?php echo $image_width > 0 ? esc_attr( $image_width ) : ''; ?>" min="1" max="500" step="1" inputmode="numeric" />
				</label>
			</p>
			<p>
				<label for="pagetop_image_height">
					<?php esc_html_e( 'Image height (px)', 'vk-all-in-one-expansion-unit' ); ?>
					<input type="number" name="vkExUnit_pagetop[image_height]" id="pagetop_image_height" value="<?php echo $image_height > 0 ? esc_attr( $image_height ) : ''; ?>" min="1" max="500" step="1" inputmode="numeric" />
				</label>
			</p>
		</div>
		<?php
		// description は意味のまとまりごとに別の `<p class="description">` に
		// 分けて段落化する。`<br /><br />` で空きを作るのは design-rules.md
		// 「余白」セクションで禁止されているため使わない（グループ内の小さな
		// 段差は SCSS のマージンで作る）。
		// PR #1363 のレビューで「説明が長すぎる」と指摘を受け、試せば分かる挙動
		// （片方だけ指定／縦横比保持）や単位・上限の重複説明を落とし 3 段落に短縮した。
		?>
		<div class="veu_pagetop_image_size__notes">
			<p class="description">
				<?php esc_html_e( 'Specify the image size in pixels.', 'vk-all-in-one-expansion-unit' ); ?>
				<?php esc_html_e( 'Default: 40 x 38 px / Max: 500 px.', 'vk-all-in-one-expansion-unit' ); ?>
			</p>
			<p class="description">
				<?php esc_html_e( '44 px or larger is recommended for touch screen devices.', 'vk-all-in-one-expansion-unit' ); ?>
			</p>
			<p class="description">
				<?php esc_html_e( 'The width / height values are kept when the image is cleared.', 'vk-all-in-one-expansion-unit' ); ?>
			</p>
		</div>
	</div>
	<?php
	// C グループ: 画像全体に関する meta 情報。
	// 以前は `<br />` 連結で 1 段落にまとまっていたが、design-rules.md
	// 「余白」セクション（`<br />` で余白を作らない）と植草レビューを踏まえ、
	// 意味の塊ごとに `<p class="description">` に分けて段落化した。
	?>
	<div class="veu_pagetop_image_meta">
		<p class="description">
			<?php esc_html_e( 'Upload an image to replace the default page top button icon.', 'vk-all-in-one-expansion-unit' ); ?>
		</p>
		<p class="description">
			<?php esc_html_e( 'Recommended formats: SVG, PNG, JPG, GIF, WebP.', 'vk-all-in-one-expansion-unit' ); ?>
		</p>
		<p class="description">
			<?php esc_html_e( 'A square (1:1) image is recommended.', 'vk-all-in-one-expansion-unit' ); ?>
			<?php esc_html_e( 'Images with a very different aspect ratio may show extra empty space.', 'vk-all-in-one-expansion-unit' ); ?>
		</p>
		<p class="description">
			<a href="<?php echo esc_url( $customizer_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Configure with live preview in the Customizer', 'vk-all-in-one-expansion-unit' ); ?> &rarr;</a>
		</p>
	</div>
</td>
</tr>
</table>
	<?php submit_button(); ?>
</div>
<script>
	// Toggle the thumbnail preview in sync with the shared vk_admin.js
	// .media_btn handler, which updates `#thumb_pagetop_image_url` (img src)
	// and `#pagetop_image_url` (text input value) when a user picks an image.
	// We observe the img src via MutationObserver instead of polling, so the
	// preview reflects changes the moment the shared handler writes to it.
	// 共通ヘルパー (vk_admin.js .media_btn) が `#thumb_pagetop_image_url` の
	// src と `#pagetop_image_url` の value を更新するので、MutationObserver
	// で src の変化を検知してプレビューの表示/非表示を切り替える。
	// (以前は setInterval で 250ms × 60 回のポーリングをしていたが、
	// 共通ヘルパーと二重に src を更新する形になっていたため廃止。)
	(function(){
		var urlInput = document.getElementById('pagetop_image_url');
		var thumb    = document.getElementById('thumb_pagetop_image_url');
		var preview  = thumb ? thumb.parentNode : null;
		var clearBtn = document.getElementById('veu_pagetop_image_clear');
		// サイズ入力欄ブロック（幅・高さ）も同じトグル機構に乗せる。
		var sizeBlock = document.querySelector('.veu_pagetop_image_size');

		// Toggle the preview wrapper visibility based on the current img src.
		// 現在の thumb.src を見てプレビュー枠の表示/非表示を切り替える。
		var togglePreview = function() {
			if ( ! thumb || ! preview ) {
				return;
			}
			var src = thumb.getAttribute( 'src' ) || '';
			preview.style.display = ( '' !== src ) ? '' : 'none';
			// サイズ入力欄も画像の有無に追従させる。
			if ( sizeBlock ) {
				sizeBlock.style.display = ( '' !== src ) ? '' : 'none';
			}
		};

		// Sync the preview from the URL text input.
		// When the user pastes / types a URL directly into the text field,
		// the shared media handler does not fire so we have to mirror the
		// value into thumb.src ourselves and re-evaluate the preview state.
		// URL テキスト欄にユーザーが直接 URL を貼り付け / 入力した場合は
		// 共通ヘルパーが発火しないため、ここで thumb.src に反映して
		// プレビューの表示状態を再評価する。
		var syncPreviewFromInput = function() {
			if ( ! urlInput || ! thumb ) {
				return;
			}
			thumb.setAttribute( 'src', urlInput.value );
			togglePreview();
		};

		// Watch for src changes coming from the shared .media_btn handler.
		// MutationObserver なら共通ハンドラが src を書き換えた瞬間に検知できる。
		if ( thumb && 'MutationObserver' in window ) {
			var observer = new MutationObserver( togglePreview );
			observer.observe( thumb, { attributes: true, attributeFilter: [ 'src' ] } );
		}

		// URL input: react to manual edits (paste, typing, etc.) so the
		// thumbnail preview stays in sync without requiring a save.
		// URL 入力欄: 手入力や貼り付けに反応してプレビューを同期させる。
		// `change` は確定時、`blur` はフォーカスアウト時のフォールバック。
		if ( urlInput ) {
			urlInput.addEventListener( 'change', syncPreviewFromInput );
			urlInput.addEventListener( 'blur', syncPreviewFromInput );
		}

		// Clear button: empty the URL input, clear the thumb src and hide the preview.
		// 「画像の指定を解除」ボタン: URL を空にし thumb.src を空に、プレビューを非表示にする。
		if ( clearBtn ) {
			clearBtn.addEventListener('click', function(){
				if ( urlInput ) {
					urlInput.value = '';
				}
				if ( thumb ) {
					// Setting src triggers the observer which hides the preview.
					// src を空にすると observer 経由でプレビューも非表示になる。
					thumb.setAttribute( 'src', '' );
				}
				// Fallback for environments without MutationObserver.
				// MutationObserver 非対応環境向けのフォールバック。
				togglePreview();
			});
		}
	})();
</script>
	<?php
}


/**
 * Get vkExUnit_pagetop options merged with defaults.
 *
 * 取得時点で `image_url` を必ず `veu_pagetop_sanitize_image_url()` に
 * 通すことで、option 直書き等で配列・null・破損文字列が保存されていた場合でも
 * 後段の `esc_url()` / `esc_attr()` で TypeError を起こさず安全に空文字へ正規化する。
 *
 * @return array
 */
function veu_pagetop_options() {
	$options = get_option( 'vkExUnit_pagetop', array() );
	if ( ! is_array( $options ) ) {
		$options = array();
	}
	$options = wp_parse_args( $options, veu_pagetop_default() );

	// Defensive normalization: option storage can hold non-string values
	// (e.g. arrays / null) when written directly by other code. The sanitizer
	// itself rejects non-string inputs and returns an empty string, but we
	// keep the explicit guard here to make the contract obvious to readers.
	// 取得時の防御的サニタイズ。サニタイザー側で非文字列は空文字にされるが、
	// 意図を明示するため `is_string` ガードも残す。
	$image_url            = isset( $options['image_url'] ) ? $options['image_url'] : '';
	$options['image_url'] = is_string( $image_url ) ? veu_pagetop_sanitize_image_url( $image_url ) : '';

	// Defensive normalization for image size values. Sanitize on read so that
	// option storage written by other code (legacy plugin versions / direct
	// `update_option()` calls) cannot poison the output even if the saved
	// value is non-numeric, negative, or out of range.
	// 画像サイズ値も取得時に防御的サニタイズ。直書きや過去版で異常値が保存
	// されていても出力側を汚染しないようにする。
	$options['image_width']  = veu_pagetop_sanitize_image_size( isset( $options['image_width'] ) ? $options['image_width'] : 0 );
	$options['image_height'] = veu_pagetop_sanitize_image_size( isset( $options['image_height'] ) ? $options['image_height'] : 0 );

	return $options;
}

/**
 * Default values for vkExUnit_pagetop option.
 *
 * @return array
 */
function veu_pagetop_default() {
	$default_options = array(
		'hide_mobile'  => false,
		'image_url'    => '',
		// 画像アップロード時のサイズ指定（px）。0 は未指定（既存 SCSS の 40 / 38 が適用）。
		'image_width'  => 0,
		'image_height' => 0,
	);
	return apply_filters( 'veu_pagetop_default', $default_options );
}

/**
 * Sanitize the vkExUnit_pagetop option on save (main setting page).
 *
 * @param array $input Raw POSTed values.
 * @return array
 */
function veu_pagetop_sanitize( $input ) {
	$output = array();
	if ( ! is_array( $input ) ) {
		$input = array();
	}
	if ( isset( $input['hide_mobile'] ) ) {
		// Use the shared boolean sanitizer to match the Customizer setting's
		// sanitize_callback (`veu_sanitize_boolean`) and keep the stored
		// representation consistent across both entry points.
		// カスタマイザ側の sanitize_callback と揃えるため共通の
		// veu_sanitize_boolean() を使用し、保存形式（bool）を統一する。
		$output['hide_mobile'] = veu_sanitize_boolean( $input['hide_mobile'] );
	}
	if ( isset( $input['image_url'] ) ) {
		$output['image_url'] = veu_pagetop_sanitize_image_url( $input['image_url'] );
	} else {
		$output['image_url'] = '';
	}
	// 画像サイズ（幅・高さ）。未指定 (0) も含めて常にサニタイザーを通す。
	$output['image_width']  = isset( $input['image_width'] ) ? veu_pagetop_sanitize_image_size( $input['image_width'] ) : 0;
	$output['image_height'] = isset( $input['image_height'] ) ? veu_pagetop_sanitize_image_size( $input['image_height'] ) : 0;
	return $output;
}
